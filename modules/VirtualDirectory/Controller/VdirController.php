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
 */namespace VirtualDirectory\Controller;

use FPanel\Domain\Services;
use FPanel\Domain\Vdir;
use FPanel\Forms\VdirForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use FPanel\Models\VirtualDirectory;
use FPanel\Process\Vhost;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class VdirController
 * @package VirtualDirectory\Controller
 */
class VdirController extends ControllerBase
{
    protected $server_data = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $domain_name;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('webhosting');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'), 'dashboard', ' ');
        $params = $this->dispatcher->getParams();

        if (array_key_exists('type', $params)) {
            switch ($params['type']) {
                case "domain":
                    $domain_id = $params['domain_id'];
                    $domain = Domains::findFirstById($domain_id);
                    if (!$domain) {
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
                        $this->domain_name = $domain_data['name'];
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'), 'domains', ' ');
                        $this->breadcrumbs->add($domain_data['name'], 'dashboard/domain/' . $domain_id, ' ');
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

    /**
     * index
     */
    public function indexAction()
    {
        $this->tag->appendTitle(__('Virtual Directories'));

        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
//            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $nested = true;
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
//            $domain_name = $data['user']['domain_info']['domain_name'];
            $nested = false;
        }

        $this->breadcrumbs->add(__('Virtual Directories'), '', '');


        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $vdir = new Vdir();
            $this->response->setJsonContent($vdir->vdirList($domain_id, $nested))->send();
        }
    }

    /**
     * generalSettings
     */
    public function generalSettingsAction()
    {
        $this->view->pick('/vdir/general');
        $this->tag->appendTitle(__('Virtual Directory General Settings'));

        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Virtual Directory  List'),'vdir/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("vdir/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("vdir/general/domain/".$domain_id);
        }
        else {
//            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Virtual Directory List'),'vdir',' ');
            $redirect_url = $this->uri->url_encrypt("vdir");
            $post_url = $this->uri->url_encrypt("vdir/general");
        }

        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Virtual Directory General Settings'), '', '');


        $domains = Domains::findFirst("name = '$domain_name'");
        if($this->request->isPost() != true) {
            $vhostmng = new Vhost();
            $vdir_settings = $vhostmng->vDirSettings(array(
                'domain' => $domain_name,
                'prefix' => $domains->prefix,
                'setting-type' => "vdir"
            ));
            $options = array(
                "page" => "general",
                'vdir_settings' => json_decode(json_encode((array) simplexml_load_string($vdir_settings)), 1)
            );
            $this->view->form = new VdirForm(null, $options);
        }

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $vdir = new Vdir();
            $post_data = array(
                'vdir_name' => "/",
                'domain_name' => $domain_name,
                'prefix' => $domains->prefix,
                'accessPolicy' => $this->request->getPost('accessPolicy'),
                'directory_browse' => $this->request->getPost('directory_browse',null,OFF),
                'logging' => $this->request->getPost('logging',null,OFF),
                'execute_in_mta' => $this->request->getPost('execute_in_mta',null,OFF),
                'use_default_document' => $this->request->getPost('use_default_document',null,OFF),
                'default_document_order' => $this->request->getPost('default_document_order'),
                'allow_anonymous' => $this->request->getPost('allow_anonymous',null,OFF),
                'parent_path' => $this->request->getPost('parent_path',null,OFF),
                'server_debugging' => $this->request->getPost('server_debugging',null,OFF),
                'client_debugging' => $this->request->getPost('client_debugging',null,OFF),
                'buffer_limit' => $this->request->getPost('buffer_limit'),
                'entity_size' => $this->request->getPost('entity_size'),
            );
            $vdir->setPosts($post_data);
            $status = $vdir->saveVdirSettings();

            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($vdir->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Settings updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * create
     */
    public function createAction()
    {
        $this->tag->appendTitle(__('Create Virtual Directory'));

        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Virtual Directory  List'),'vdir/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("vdir/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("vdir/create/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Virtual Directory List'),'vdir',' ');
            $redirect_url = $this->uri->url_encrypt("vdir");
            $post_url = $this->uri->url_encrypt("vdir/create");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Create Virtual Directory'), '', '');


        $options = array(
            'page' => "create",
            'action' => 'create'
        );
        $this->view->form = new VdirForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $vdir = new Vdir();
            $post_data = array(
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'dir_name' => $this->request->getPost('dir_name'),
                'physical_path' => $this->request->getPost('physical_path'),
                'accessPolicy' => $this->request->getPost('accessPolicy'),
                'create_application' => $this->request->getPost('create_application',null,OFF),
                'server_debugging' => $this->request->getPost('server_debugging',null,OFF),
                'client_debugging' => $this->request->getPost('client_debugging',null,OFF),
                'buffer_limit' => $this->request->getPost('buffer_limit'),
                'entity_size' => $this->request->getPost('entity_size'),
            );

            $vdir->setPosts($post_data);
            $status = $vdir->createVirtualDirectory();

            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($vdir->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Virtual Directory Created'));
                    return $this->response->redirect($redirect_url,true);

                }
            }
        }
        return true;
    }

    /**
     * @param $id
     * @return bool|ResponseInterface
     */
    public function editAction($id)
    {
        $this->tag->appendTitle(__('Edit Virtual Directory'));

        $this->view->setVar('id', $id);

        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Virtual Directory  List'),'vdir/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("vdir/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("vdir/edit/$id/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Virtual Directory List'),'vdir',' ');
            $redirect_url = $this->uri->url_encrypt("vdir");
            $post_url = $this->uri->url_encrypt("vdir/edit/$id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Virtual Directory'), '', '');


        $vdir_data = VirtualDirectory::findFirstById($id);

        if($this->request->isPost() != true) {
            $domains = Domains::findFirst("name = '$domain_name'");
            $vhostmng = new Vhost();
            $vdir_settings = $vhostmng->vDirSettings(array(
                'domain' => $domain_name,
                'prefix' => $domains->prefix,
                'setting-type' => "vdir",
                'virtual-dir-name' => $vdir_data->name,
            ));

            $options = array(
                'page' => "create",
                'action' => 'edit',
                'vdir_settings' => json_decode($vdir_settings,true),
                'name' => $vdir_data->name,
                'path' => $vdir_data->path,
                'app' => $vdir_data->app,
            );


            $this->view->form = new VdirForm(null, $options);

        }


        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $vdir = new Vdir();
            $post_data = array(
                'id' => $id,
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'dir_name' => $vdir_data->name,
                'physical_path' => $vdir_data->path,
                'accessPolicy' => $this->request->getPost('accessPolicy'),
                'server_debugging' => $this->request->getPost('server_debugging',null,OFF),
                'client_debugging' => $this->request->getPost('client_debugging',null,OFF),
                'buffer_limit' => $this->request->getPost('buffer_limit'),
                'entity_size' => $this->request->getPost('entity_size'),
            );

            $vdir->setPosts($post_data);
            $status = $vdir->updateVdirCurl();

            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($vdir->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Virtual Directory updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $messages = $this->delete($id);
            $this->response->setJsonContent($messages)->send();
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function multipleAction()
    {
//        $data = $this->user_data;
//        if(isset($this->domain_id)) {
//            $domain_id = $this->domain_id;
//            $url = $this->uri->url_encrypt('vdir/domain/'.$domain_id);
//        }
//        else {
//            $domain_id = $data['user']['domain_info']['domain_id'];
//            $url = $this->uri->url_encrypt('vdir');
//        }

        $this->view->disable();

        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');
            $messages = Globals::messages();

            if($this->request->getPost('id') == null) {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Action successful');

            }
            else if($this->request->getPost("action") == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                $messages['errors']['message_summary'] = __('Please select an action');

            }
            else {
                switch($action) {
                    case "delete":
                        $messages = $this->delete($id);
                        break;
                    default:
                        $messages['type'] = 'error';
                        $messages['title'] = __('Failed');
                        $messages['messages'] = __('Action failed');
                        $messages['errors'] = [];
                        $messages['errors']['message_summary'] = __('Action is not found');

                        break;
                }
            }

            return $this->response->setJsonContent($messages)->send();
        }
    }

    private function delete($id): array
    {
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
//            $url = $this->uri->url_encrypt('vdir/domain/'.$domain_id);
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $this->user_data['user']['domain_info']['domain_id'];
        }

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        $vdir = new Vdir();
        $vdir->setPosts(array(
            'id'        => $id,
            'domain_name' => $domain_name,
            'domain_id' => $domain_id
        ));
        $status = $vdir->deleteVdir();
        if (!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Action failed');
        }
        return $messages;
    }
}