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
 */namespace DomainAlias\Controller;

use FPanel\Domain\DomainAlias;
use FPanel\Domain\Services;
use FPanel\Forms\DomainaliasForm;
use FPanel\Globals\Globals;
use FPanel\Models\DomainAlias as DomainAliasModel;
use FPanel\Models\Domains;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class DomainAliasController
 * @package DomainAlias\Controller
 */
class DomainAliasController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $js_code;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('domain_alias');
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
                            $this->acl->checkParent($domain_id, DOMAIN_USER);
                        $domain_data = $domain->toArray();
                        $this->domain_id = $domain_id;
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$domain_id,' ');
                        $this->domain_data = $domain_data;
                        $this->view->setVar('client_type', DOMAIN_USER);
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
        if($this->user_data['user']['remaining']['domainalias'] < "1")
            $active_class = "disabled";
        else
            $active_class = "";

        $this->view->setVar('active_class', $active_class);


        $this->tag->appendTitle(__('Domain Alias List'));
        $data = $this->user_data;
        $this->breadcrumbs->add(__('Domain Alias List'),'','');

        $this->tag->appendTitle(__('Domain Alias'));

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $this->view->disable();
            $domainalias = new DomainAlias();
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $this->response->setJsonContent($domainalias->listAlias($domain_id,true))->send();
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $this->response->setJsonContent($domainalias->listAlias($domain_id))->send();
            }
        }

    }

    /**
     * @return ResponseInterface|void
     */
    public function createAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Create Domain Alias'));
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Domain Alias List'),'domainalias/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("domainalias/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("domainalias/create/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Domain Alias List'),'domainalias',' ');
            $redirect_url = $this->uri->url_encrypt("domainalias");
            $post_url = $this->uri->url_encrypt("domainalias/create");
        }

        $this->breadcrumbs->add(__('Create Domain Alias'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('post_url', $post_url);

        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
               $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'update'            => false,
                'domain_name'       => $domain_name,
                'domain_id'         => $domain_id,
                'alias_name'        => $this->request->getPost('alias_name'),
                'syn_pri_dom'       => "1",
                'dns'               => "1",
                'status'            => "1",
                'mail'          => $this->request->getPost('activate_mail_service',null,OFF),
                'web'           => $this->request->getPost('activate_web_service',null,OFF),
                'old_alias'         => "",
            );
            $domain_alias = new DomainAlias();
            $domain_alias->setPosts($post_data);
            $domain_alias_status = $domain_alias->createAlias();
            if($domain_alias_status == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($domain_alias->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($domain_alias_status == true) {
                    $this->flashSession->success(__('Domain Alias is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }

        $this->view->form = new DomainaliasForm(null);
    }

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function editAction($id)
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Edit Domain Alias'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Domain Alias List'),'domainalias/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("domainalias/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("domainalias/edit/$id/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Domain Alias List'),'domainalias',' ');
            $redirect_url = $this->uri->url_encrypt("domainalias");
            $post_url = $this->uri->url_encrypt("domainalias/edit/$id");
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Edit Domain Alias'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $this->view->setVar("id", $id);
        $options = array();
        $options['action'] = "edit";
        $options['id'] = $id;
        $get_domain_alias = DomainAliasModel::findFirst("id = '$id' AND domain_id = '$domain_id'");
        if(!$get_domain_alias) {
            $this->view->setVar('error_content',__('Domain Alias not found'));
            $this->response->setStatusCode(404, __('Domain Alias not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $dom_alias_data = $get_domain_alias->toArray();
        $options['dom_alias_data'] = $dom_alias_data;
        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'update'        => true,
                'domain_id'     => $domain_id,
                'id'            => $id,
                'domain_name'   => $domain_name,
                'alias_name'    => $this->request->getPost('alias_name'),
                'syn_pri_dom'   => "1",
                'dns'           => "1",
                'status'        => "1",
                'mail'          => $this->request->getPost('activate_mail_service',null,OFF),
                'web'           => $this->request->getPost('activate_web_service',null,OFF),
                'old_alias'     => $dom_alias_data['alias_name'],
            );
            $domain_alias = new DomainAlias();
            $domain_alias->setPosts($post_data);
            $domain_alias_status = $domain_alias->updateAlias($id, $domain_id);
            if ($domain_alias_status == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($domain_alias->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($domain_alias_status == true) {
                    $this->flashSession->success(__('Domain Alias is updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }

        $this->view->form = new DomainaliasForm(null, $options);

    }

    /**
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $messages = $this->delete($id);
            $this->response->setJsonContent($messages)->send();
        }
    }

    private function delete($id): array
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
        $domain_alias = new DomainAlias();
        $domain_alias_status = $domain_alias->deleteAlias($id, $domain_name, $domain_id);
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        if ($domain_alias_status == false) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Action failed');
        }

        return $messages;
    }

    public function multipleAction()
    {
//        $data = $this->user_data;
//        if(isset($this->domain_id)) {
//            $domain_id = $this->domain_id;
//            $url = $this->uri->url_encrypt('domainalias/domain/'.$domain_id);
//        }
//        else {
//            $domain_id = $data['user']['domain_info']['domain_id'];
//            $url = $this->uri->url_encrypt('domainalias');
//        }

        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');
            $messages = Globals::messages();
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            if($this->request->getPost('id') == null) {
                $messages['messages'] = __('Please select an alias');
            }
            else if($this->request->getPost("action") == null) {
                $messages['messages'] = __('Please select an action');

            }
            else {
                switch($action) {
                    case "delete":
                        $messages = $this->delete($id);
                        break;
                    default:
                        $messages['messages'] = __('Adding not found');
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