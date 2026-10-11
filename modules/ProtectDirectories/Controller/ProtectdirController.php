<?php
/*
 * fpanel — a web-based hosting control panel.
 * Copyright (C) 2026  Ahmet Refik Gunes
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */namespace ProtectDirectories\Controller;

use FPanel\Domain\Protectdir;
use FPanel\Domain\Services;
use FPanel\Forms\ProtectdirForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use FPanel\Models\ProtectDirectory;
use FPanel\Models\ProtectDirectoryUser;
use FPanel\Models\Subdomains;
use Main\Controller\ControllerBase;

/**
 * Class ServiceplansController
 * @package Serviceplans\Controller
 */
class ProtectdirController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $js_code;
    protected $user_js_code;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('protect_dir');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        $params = $this->dispatcher->getParams();

        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "domain":
                    $domain_id = $params['domain_id'];
                    $domain = Domains::findFirstById($domain_id);
                    if(!$domain) {
                        $this->view->setVar('error_content',__('Domain not found'));
                        $this->response->setStatusCode(404, __('Domain not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($domain_id,DOMAIN_USER);
                        $domain_data = $domain->toArray();
                        $this->domain_id = $domain_id;

                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$domain_id,' ');
                        $this->domain_data = $domain_data;
                        $this->view->setVar('client_type',DOMAIN_USER);
                        $client_menu = $this->sidebar->clientMenu(DOMAIN_USER, $domain_id);
                        $this->view->setVar("client_menu", $client_menu);
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        else {
            $domain_id = $user_data['user']['domain_info']['domain_id'];
        }

        if(isset($this->domain_id)) {
            $redirect_url = $this->uri->url_encrypt("dashboard/domain/".$domain_id);
        }
        else {
            $redirect_url = $this->uri->url_encrypt("dashboard");
        }
        $domain_services = new Services();
        $domain_services->checkDomainService($domain_id,"web", $redirect_url);
    }

    public function indexAction()
    {
        $data = $this->user_data;
        $this->breadcrumbs->add(__('Protect Directory List'),'','');



        $this->tag->appendTitle(__('Protect Directory List'));
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $this->view->disable();
            $subdomains = new Protectdir();
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $this->response->setJsonContent($subdomains->listProtectDir($domain_id,true))->send();
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $this->response->setJsonContent($subdomains->listProtectDir($domain_id))->send();
            }
        }

    }

    /**
     * @return false|void
     */
    public function createAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Create Protect Directory'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/domain/'.$domain_id,' ');
            $post_url = $this->uri->url_encrypt('protectdir/create/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir',' ');
            $post_url = $this->uri->url_encrypt('protectdir/create');
        }

        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Create Protect Directory'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $subdomains_list = array();
        $subdomains = Subdomains::find("domain_id = '$domain_id'");
        if($subdomains)
            $subdomains_list = $subdomains->toArray();
        $options = array(
            'domain_id' => $domain_id,
            'domain_name' => $domain_name,
            'page' => "protectdir",
            'action' => "create",
            'domain_list' => $subdomains_list
        );
        $this->view->form = new ProtectdirForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $post_data = array(
                'domain_id'    => $domain_id,
                'path'    => '/'.$this->request->getPost('directory_path'),
                'ssl'    => OFF,
                'cgi_bin'    => OFF,
                'header'    => $this->request->getPost('title_of_header'),
                'subdomain_id'  => $this->request->getPost('domain_name'),
                'subdomain_list' => $subdomains_list,
            );
            $protectdir = new Protectdir();
            $protectdir->domain_name = $domain_name;
            $protectdir->setPosts($post_data);

            $status = $protectdir->saveProtectdir();

            if($status === false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($protectdir->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
                if(!$this->request->isAjax())
                    return false;
            }
            else {
                $protect_id = $status;
                if(isset($this->domain_id)) {
                    $redirect_url =  $this->uri->url_encrypt('protectdir/createuser/'.$protect_id.'/domain/'.$domain_id);
                }
                else {
                    $redirect_url =  $this->uri->url_encrypt('protectdir/createuser/'.$protect_id);
                }
                $messages['redirect_url'] = $redirect_url;

                if(!$this->request->isAjax()) {
                    $this->flashSession->success(__('Protect Directory is created'));
                    $this->flashSession->warning(__('Please create an user for this directory'));
                }
            }

            if($this->request->isAjax() == true)
                $this->response->setJsonContent($messages)->send();
            else {
                if($status !== false) {
                    $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    /**
     * @param $dir_id
     * @return bool|void
     */
    public function editAction($dir_id)
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Edit Protect Directory'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/domain/'.$domain_id,' ');
            $redirect_url =  $this->uri->url_encrypt('protectdir/domain/'.$domain_id);
            $post_url =  $this->uri->url_encrypt("protectdir/edit/$dir_id/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir',' ');
            $redirect_url =  $this->uri->url_encrypt('protectdir');
            $post_url =  $this->uri->url_encrypt("protectdir/edit/$dir_id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Protect Directory'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar("dir_id", $dir_id);

        $protect_dir = ProtectDirectory::findFirstById($dir_id);
        if(!$protect_dir) {
            $this->view->setVar('error_content',__('Protect Directory not found'));
            $this->response->setStatusCode(404, __('Protect Directory not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $protect_dir_data = $protect_dir->toArray();

        $subdomains_list = array();
        $subdomains = Subdomains::find("domain_id = '$domain_id'");
        if($subdomains)
            $subdomains_list = $subdomains->toArray();

        $options = array(
            'domain_id' => $domain_id,
            'domain_name' => $domain_name,
            'action' => "edit",
            'page' => "protectdir",
            'path' => ltrim($protect_dir_data['path'],"/"),
            'dir_id' => $dir_id,
            'header' => $protect_dir_data['header'],
            'domain_list' => $subdomains_list,
            'selected_domain' => $protect_dir_data['subdomain_id']
        );
        $this->view->form = new ProtectdirForm(null, $options);

        if($this->request->isPost() == true){
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $post_data = array(
                'old_path'      => $protect_dir_data['path'],
                'domain_id'     => $domain_id,
                'domain_name'   => $domain_name,
                'subdomain_id'  => $protect_dir_data['subdomain_id'],
                'dir_id'        => $dir_id,
                'path'          => '/'.$this->request->getPost('directory_path'),
                'header'        => $this->request->getPost('title_of_header'),
            );
            $protectdir = new Protectdir();
            $protectdir->setPosts($post_data);

            $protectdir_status = $protectdir->editProtectDir();


            if($protectdir_status == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($protectdir->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($protectdir_status != false) {
                    $this->flashSession->success(__('Protect Directory is updated'));
                    $this->response->redirect($redirect_url,true);
                }
            }

        }
    }

    public function deleteAction($dir_id)
    {
        $this->view->disable();

        if (($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $messages = $this->delete($dir_id);
            $this->response->setJsonContent($messages)->send();
        }
    }

    private function delete($dir_id): array
    {
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
        }

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Delete successful');

        $protectdir = new Protectdir();
        $protectdir->setPosts(array(
            'dir_id' => $dir_id,
            'domain_id' => $domain_id,
        ));
        $protectdir->domain_name = $domain_name;
        $protectdir_status = $protectdir->deleteProtectDir();
        if($protectdir_status == false) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Delete failed');

        }
        return $messages;
    }

    public function protectuserAction($dir_id = null)
    {
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/domain/'.$domain_id,' ');
            $post_url =  $this->uri->url_encrypt("protectdir/edit/$dir_id/domain/".$domain_id);
            $nested = true;
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir',' ');
            $post_url =  $this->uri->url_encrypt("protectdir/edit/$dir_id");
            $nested = false;
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Protect Directory User List'),'','');


        $this->tag->appendTitle(__('Protect Directory User List'));

        $this->view->setVar('user_js', $this->user_js_code);
        $this->view->setVar('dir_id', $dir_id);

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $this->view->disable();
            $subdomains = new Protectdir();
            $this->response->setJsonContent($subdomains->listDirUsers($dir_id, $domain_id, $nested))->send();
        }

    }

    public function createuserAction($dir_id)
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Create Protect Directory User'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/domain/'.$domain_id,' ');
            $this->breadcrumbs->add(__('Protect Directory User List'),'protectdir/protectuser/'.$dir_id.'/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('protectdir/protectuser/'.$dir_id.'/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt('protectdir/createuser/'.$dir_id.'/domain/'.$domain_id);

        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/protectdir',' ');
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/protectuser/'.$dir_id,' ');
            $redirect_url = $this->uri->url_encrypt('protectdir/protectuser/'.$dir_id);
            $post_url = $this->uri->url_encrypt('protectdir/createuser/'.$dir_id);

        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Create Protect Directory User'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $this->view->setVar("dir_id", $dir_id);

        $protect_dir = ProtectDirectory::findFirstById($dir_id);
        $protect_dir_data = $protect_dir->toArray();
        $options = array(
            'domain_id' => $domain_id,
            'domain_name' => $domain_name,
            'page' => "user",
            'path' => $protect_dir_data['path'],
            'action' => "create"
        );
        $this->view->form = new ProtectdirForm(null, $options);

        if($this->request->isPost() == true){
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'domain_id'         => $domain_id,
                'protect_dir_id'    => $dir_id,
                'path'              => $protect_dir_data['path'],
                'username'          => $this->request->getPost('username'),
                'password'          => $this->request->getPost('password'),
            );
            $protectdir_user = new Protectdir();
            $protectdir_user->setPosts($post_data);
            $protectdir_user->domain_name = $domain_name;
            $protectdir_user_save_status = $protectdir_user->saveProtectDirUser();

            if($protectdir_user_save_status == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($protectdir_user->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($protectdir_user_save_status == true) {
                    $this->flashSession->success(__('Protect Directory User is created'));
                    $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    /**
     * @param $user_id
     * @return bool|void
     */
    public function edituserAction($user_id)
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Edit Protect Directory User'));

        $protect_dir_user = ProtectDirectoryUser::findFirstById($user_id);
        if(!$protect_dir_user) {
            $this->view->setVar('error_content',__('Protect Directory User not found'));
            $this->response->setStatusCode(404, __('Protect Directory User not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $protect_dir_user_data = $protect_dir_user->toArray();
        $dir_id = $protect_dir_user_data['protect_dir_id'];
        $this->view->setVar("user_id", $user_id);
        $this->view->setVar("dir_id", $dir_id);

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/domain/'.$domain_id,' ');
            $this->breadcrumbs->add(__('Protect Directory User List'),'protectdir/protectuser/'.$dir_id.'/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('protectdir/protectuser/'.$dir_id.'/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt('protectdir/edituser/'.$user_id.'/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/protectdir',' ');
            $this->breadcrumbs->add(__('Protect Directory List'),'protectdir/protectuser/'.$dir_id,' ');
            $redirect_url = $this->uri->url_encrypt('protectdir/protectuser/'.$dir_id);
            $post_url = $this->uri->url_encrypt('protectdir/edituser/'.$user_id);
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Protect Directory User'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $protect_dir = ProtectDirectory::findFirstById($protect_dir_user_data['protect_dir_id']);

        $protect_dir_data = $protect_dir->toArray();

        $password = trim($this->crypto->bdecode($protect_dir_user_data['password']));
        $options = array(
            'domain_id' => $domain_id,
            'action' => "edit",
            'page' => "useredit",
            'path' => $protect_dir_data['path'],
            'username' => $protect_dir_user_data['username'],
            'user_id' => $user_id,
            'password' => $password,
        );
        $this->view->form = new ProtectdirForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $post_data = array(
                'domain_id'         => $domain_id,
                'protect_dir_id'    => $dir_id,
                'pro_dir_data'      => $protect_dir_data,
                'user_id'           => $user_id,
                'old_username'      => $protect_dir_user_data['username'],
                'username'          => $this->request->getPost('username'),
                'password'          => $this->request->getPost('password'),
            );
            $protectdir_user = new Protectdir();
            $protectdir_user->setPosts($post_data);
            $protectdir_user->domain_name = $domain_name;
            $protectdir_user_update_status = $protectdir_user->editUser();
            if($protectdir_user_update_status == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($protectdir_user->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($protectdir_user_update_status == true) {
                    $this->flashSession->success(__('Protect Directory User is updated'));
                    $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    public function deleteuserAction($user_id, $dir_id)
    {
        $this->view->disable();

        if (($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $messages = $this->deleteUser($user_id, $dir_id);
            $this->response->setJsonContent($messages)->send();
        }
    }

    private function deleteUser($user_id, $dir_id): array
    {
        $data = $this->user_data;
        $domain_id = $this->domain_id ?? $data['user']['domain_info']['domain_id'];

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Delete successful');


        $protectdir_user = new Protectdir();
        $protectdir_user->setPosts(array(
            'user_id' => $user_id,
            'dir_id' => $dir_id,
            'domain_id' => $domain_id,
        ));
        $pro_user_status = $protectdir_user->deleteUser();
        if($pro_user_status == false) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Delete failed');
        }
        return $messages;
    }

    public function multipleAction($type)
    {
        $data = $this->user_data;
        $domain_id = $this->domain_id ?? $data['user']['domain_info']['domain_id'];

        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if($this->request->getPost('id') == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select at least one');
            }
            else if($this->request->getPost("action") == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select an action');
            }
            else {
                switch($action) {
                    case "delete":
                        if($type == "user") {
                            $messages['type'] = 'error';
                            $messages['title'] = __('Failed');
                            $messages['messages'] = __('User not found');
                            $pro_users = ProtectDirectoryUser::findFirstById($id);

                            if($pro_users) {
                                $pro_dir = ProtectDirectory::findFirst("id = '$pro_users->protect_dir_id' AND domain_id = '$domain_id'");
                                if($pro_dir)
                                    $messages = $this->deleteUser($id, $pro_users->protect_dir_id);
                            }
                        }
                        else {
                            $messages = $this->delete($id);
                        }
                        break;
                    default:
                        $messages['type'] = 'error';
                        $messages['title'] = __('Failed');
                        $messages['messages'] = __('Action not found');
                        break;
                }
            }
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
    }
}