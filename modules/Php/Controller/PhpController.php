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
namespace Php\Controller;

use FPanel\Domain\PhpSettings;
use FPanel\Domain\Services;
use FPanel\Forms\PhpSettingsForm;
use FPanel\Globals\Globals;
use FPanel\Models\ClientLimitsPermissions;
use FPanel\Models\Domains;
use FPanel\Process\Php;
use Main\Controller\ControllerBase;

/**
 * Class PhpController
 * @package Php\Controller
 */
class PhpController extends ControllerBase
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
        $this->view->setVar('system_items', $this->user_data);
        $this->user_id = $this->user_data['user']['info']['user_id'];
        $this->user_type = $this->user_data['user']['info']['user_type'];
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
                    $this->response->setStatusCode(404, __('Url not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        else {
            $domain_id = $this->user_data['user']['domain_info']['domain_id'];

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
        $this->tag->appendTitle( __('PHP Settings'));

        if(isset($this->domain_id)) {
            $this->breadcrumbs->add(__('PHP Settings'),'php/domain/'.$this->domain_id,'');
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $post_url = $this->uri->url_encrypt('php/domain/'.$this->domain_id);
            $validate_url = $this->uri->url_encrypt('php/domain/'.$this->domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('PHP Settings'),'php','');
            $post_url = $this->uri->url_encrypt('php');
            $validate_url = $this->uri->url_encrypt('php');
        }

        $this->view->setVar('post_url', $post_url);
        $this->view->setVar('validate_url', $validate_url);
        $this->view->setVar('domain_name', $domain_name);
        $hosting_settings = ClientLimitsPermissions::findFirst("domain_id = '$domain_id'");
        $phpmng = new Php();
        $php_settings = $phpmng->show([
            "domain" => $domain_name
        ]);
        $ini_content = $this->env->parseIniString($php_settings);
        $available_modules = $phpmng->getModules([
            "phpver" => str_replace("php", "", $hosting_settings->php_version),
        ]);
        $options = array(
            "data" => $ini_content,
            "available_modules" => $available_modules
        );

        $this->view->form = new PhpSettingsForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $php_domain_settings = new PhpSettings();
            $php_domain_settings->setPost("installed_extensions", $available_modules);
            $php_domain_settings->setPost("phpver", $hosting_settings->php_version);
            $php_domain_settings->setPost("domain_name", $domain_name);

            $status = $php_domain_settings->saveSettings($domain_id);

            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach($php_domain_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Domain settings updated'));
                }
            }
        }
    }
}