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
 */namespace Rewrite\Controller;

use FPanel\Domain\Services;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use Main\Controller\ControllerBase;

/**
 * Class RewriteController
 * @package Rewrite\Controller
 */
class RewriteController extends ControllerBase
{

    protected $user_data = array();
    protected $domain_data = array();
    protected $user_type;
    protected $user_id;
    protected $client_id;
    protected $parent_id;
    protected $client_type;
    protected $domain_id;
    protected $domain_name;
    protected $user_domain_id;
    protected $error_messages = array();

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $this->user_data = $this->acl->run('webhosting');
        $user_data = $this->user_data;

        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];

        if(isset($user_data['user']['domain_info']))
            $this->user_domain_id = $user_data['user']['domain_info']['domain_id'];

        $this->view->setVar("system_items", $user_data);
        $this->view->setVar("user_type", $this->user_type);

        $system_type = $user_data['system_data']['system_type'];
        $this->view->setVar('system_type', $system_type);
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
                        $this->parent_id = $domain_data['client_id'];
                        $this->domain_name = $domain_data['name'];
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$this->domain_id,' ');
                        $this->view->setVar('domain_id', $domain_id);
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
            $redirect_url = $this->uri->url_encrypt("dashboard/domain/".$this->domain_id);
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
        $this->view->pick('domains/rewrite');
//        $data = $this->user_data;
        $this->tag->appendTitle( __('Rewrite Settings'));

        if(isset($this->domain_id)) {
            $this->breadcrumbs->add(__('Rewrite Settings'),'rewrite/domain/'.$this->domain_id,'');
//            $domain_id = $this->domain_id;
            $url = $this->uri->url_encrypt('rewrite/domain/'.$this->domain_id);
        }
        else {
//            $domain_id = $data['user']['domain_info']['domain_id'];
            $this->breadcrumbs->add(__('Rewrite Settings'),'rewrite','');
            $url = $this->uri->url_encrypt('rewrite');
        }
        $this->view->setVar('post_url', $url);

//        $options = array(
//            'user_id'   => $data['user']['info']['user_id'],
//            'user_type'   => $data['user']['info']['user_type'],
//            'domain_data'   => $domain_data,
//            'domain_id' => $domain_id
//        );
//        $this->view->form = new WebHostingSettingsForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

//            $status = $this->update($ip_list, $ssl_list);
//            if( !$status) {
//                $messages['type'] = 'error';
//                $messages['title'] = __('Failed');
//                $messages['messages'] = __('Action failed');
//
//                foreach($this->error_messages as $title => $message) {
//                    $messages['errors'][$title] = $message;
//                    if(!$this->request->isAjax())
//                        $this->flashSession->error((string) $message);
//                }
//            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
//            else {
//                if ($status == true) {
//                    $this->flashSession->success(__('Domain settings updated'));
//                    return $this->response->redirect($url,true);
//                }
//            }
        }

    }

}