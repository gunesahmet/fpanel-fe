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
 */
namespace AspNet\Controller;

use FPanel\Domain\AspNet;
use FPanel\Forms\AspNetSettingsForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use Main\Controller\ControllerBase;

/**
 * Class AspnetController
 * @package AspNet\Controller
 */
class AspnetController extends ControllerBase
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
                        $this->response->setStatusCode(404, __('Domain not found'));
                        $this->view->setVar('error_content',__('Domain has an error'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER)) {
                            $this->acl->checkParent($domain_id,DOMAIN_USER);
                        }
                        $domain_data = $domain->toArray();
                        $this->domain_id = $domain_id;
                        $this->domain_name = $domain_data['name'];
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'), 'domains', ' ');
                        $this->breadcrumbs->add($domain_data['name'], 'dashboard/domain/' . $domain_id, ' ');
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
    }

    /**
     * index
     */
    public function indexAction()
    {
        $is_admin = false;
        $this->tag->appendTitle(__('ASP.NET Settings'));
        if(($this->auth->getUserType() == ROOT_USER) || ($this->auth->getUserType() == ADMIN_USER)) {
            $is_admin = true;
        }

        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $redirect_url = $this->uri->url_encrypt('aspnet/domain/'.$domain_id);
            $conn_list_url = $this->uri->url_encrypt('aspnet/conn/domain/'.$domain_id);
            $err_list_url = $this->uri->url_encrypt('aspnet/err/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $redirect_url = $this->uri->url_encrypt('aspnet');
            $conn_list_url = $this->uri->url_encrypt('aspnet/conn');
            $err_list_url = $this->uri->url_encrypt('aspnet/err');
        }
        $this->view->setVar('conn_list_url', $conn_list_url);
        $this->view->setVar('err_list_url', $err_list_url);
        $this->view->setVar('post_url', $redirect_url);

        $this->breadcrumbs->add(__('ASP.NET Settings'), '', '');

        $aspnet = new AspNet();
        $aspnet_settings = $aspnet->getAspNetSettingsDomain($domain_name, $domain_id);
        $conn_list = $aspnet->getConnStringList($aspnet_settings, $domain_id);
        $err_list = $aspnet->getCustomErrorList($aspnet_settings, $domain_id);
        $this->view->setVar('err_list', $err_list);
        $this->view->setVar('conn_list', $conn_list);
        $options = array(
            'is_admin' => $is_admin,
            'aspnet_settings' => $aspnet_settings,
        );

//        $this->view->setVar('table_id_conn','connList');

//        $this->view->setVar('table_id_err','errList');
        $this->view->setVar('connectionStrings', $options['aspnet_settings']['connectionStrings']);
        if(!empty($options['aspnet_settings']['customErrors'])) {
            $custom_errors = $options['aspnet_settings']['customErrors'];
        }
        else {
            $custom_errors = null;
        }
        $this->view->setVar('customErrors', $custom_errors);

        $this->view->form = new AspNetSettingsForm(null, $options);

        if ($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('ASP.NET Settings updated');

            $aspnet = new AspNet();
            $aspnet->setPost("is_admin", $is_admin);
            $aspnet->setPost("domain_name", $domain_name);
            $status= $aspnet->saveSettings($domain_id);

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($aspnet->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Settings updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $connection_string
     */
    public function deleteConnAction($connection_string)
    {
        $this->view->disable();
        if($this->request->isAjax() && $this->request->isPost()) {

            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $domain_name = $this->domain_name;
//                $redirect_url = $this->uri->url_encrypt('aspnet/domain/'.$domain_id);
            }
            else {
                $data = $this->user_data;
                $domain_name = $data['user']['domain_info']['domain_name'];
                $domain_id = $data['user']['domain_info']['domain_id'];
//                $redirect_url = $this->uri->url_encrypt('aspnet');
            }

            $string = trim(base64_decode($connection_string));

            $aspnet = new AspNet();
            $aspnet->deleteConnectionString($string, $domain_name, $domain_id);

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Connection String removed');

            $this->response->setJsonContent($messages)->send();

        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    /**
     * @param $status_code
     */
    public function deleterrAction($status_code)
    {
        $this->view->disable();
        if($this->request->isAjax() && $this->request->isPost()) {
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $domain_name = $this->domain_name;
//                $redirect_url = $this->uri->url_encrypt('aspnet/domain/'.$domain_id);
            }
            else {
                $data = $this->user_data;
                $domain_name = $data['user']['domain_info']['domain_name'];
                $domain_id = $data['user']['domain_info']['domain_id'];
//                $redirect_url = $this->uri->url_encrypt('aspnet');
            }
            $status_code = trim(base64_decode($status_code));
            $aspnet = new AspNet();
            $aspnet->deleteCustomError($status_code, $domain_name, $domain_id);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Custom Error removed');
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }
}