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
 */namespace WebHosting\Controller;

use FPanel\Domain\Services;
use FPanel\Domain\Webhosting;
use FPanel\Forms\WebHostingSettingsForm;
use FPanel\Globals\Globals;
use FPanel\Models\Certificates;
use FPanel\Models\Domains;
use FPanel\Models\Servers;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class WebhostingController
 * @package WebHosting\Controller
 */
class WebhostingController extends ControllerBase
{
    use Parents,
        Domain;

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
    protected $web_server_os;

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
        $this->view->setVar("user_type", $this->user_type);
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
                        $web_server_id = $domain->web_server_id;
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
            $web_server_id = $user_data['user']['domain_info']['web_server_id'];
        }

        if(isset($this->domain_id)) {
            $redirect_url = $this->uri->url_encrypt("dashboard/domain/".$this->domain_id);
        }
        else {
            $redirect_url = $this->uri->url_encrypt("dashboard");
        }
        $servers = Servers::findFirstById($web_server_id);
        $this->web_server_os = $this->env->getOs($servers->os);
        $domain_services = new Services();
        $domain_services->checkDomainService($domain_id,"web", $redirect_url);
        $this->view->setVar("web_server_os", $this->web_server_os);
    }

    /**
     * @return ResponseInterface|void
     */
    public function indexAction()
    {
        $this->view->pick('domains/webhosting');
        $data = $this->user_data;
        $this->tag->appendTitle( __('Web Hosting Settings'));

        if(isset($this->domain_id)) {
            $this->breadcrumbs->add(__('Web Hosting Settings'),'webhosting/domain/'.$this->domain_id,'');
            $domain_id = $this->domain_id;
            $domain_data = $this->getdomain($this->domain_id);
            $ip_list =  $this->getparentip($this->parent_id, $domain_data['ipaddr_id'], $domain_data["web_server_id"]);
            $url = $this->uri->url_encrypt('webhosting/domain/'.$this->domain_id);

        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $this->breadcrumbs->add(__('Web Hosting Settings'),'webhosting','');
            $domain_data = $this->getdomaindata($this->user_domain_id);
            $ip_list = $this->getdomainip($this->user_domain_id, $domain_data["web_server_id"]);
            $url = $this->uri->url_encrypt('webhosting');
        }
        $this->view->setVar('post_url', $url);


        $ssl_status = false;
        if($domain_data['ssl'] == ACTIVE)
            $ssl_status = true;

        $this->view->setVar("ssl_status", $ssl_status);

        $ssl = false;
        if($this->checkssl($domain_id) === true) {
            $ssl = true;
        }
        $this->view->setVar("ssl", $ssl);

        $this->domain_data = $domain_data;
        $ssl_certificates = Certificates::find("domain_id = '$domain_id'");
        $ssl_list = array();
        foreach($ssl_certificates as $ssl) {
            $ssl_list[$ssl->id] = $ssl->name . ($ssl->common_name != null ? " ($ssl->common_name)" : "");
        }

        $options = array(
            'user_id'       => $data['user']['info']['user_id'],
            'user_type'     => $data['user']['info']['user_type'],
            'ip_list'       => $ip_list,
            'domain_data'   => $domain_data,
            'ssl_list'      => $ssl_list,
            'domain_id'     => $domain_id
        );

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');


            $status = $this->update($ip_list, $ssl_list);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach($this->error_messages as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Domain settings updated'));
                    return $this->response->redirect($url,true);
                }
            }
        }
        $this->view->form = new WebHostingSettingsForm(null, $options);
    }

    /**
     * @param $ip_list
     * @param $ssl_list
     * @return bool
     */
    private function update($ip_list, $ssl_list): bool
    {
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $ftp_id = $this->domain_data['ftp_user_id'];
            $post_data = array(
                'ipaddr_id'         => $this->request->getPost('ipaddress_list'),
                'ssl'               => $this->request->getPost('ssl_support',null,OFF),
                'php'               => $this->request->getPost('php_support',null,OFF),
                'php_version'       => $this->request->getPost('php_version'),
                'asp'               => $this->request->getPost('asp_support',null,OFF),
                'aspnet'            => $this->request->getPost('asp_net_support',null,OFF),
                'aspnet_core'       => $this->request->getPost('asp_net_core_support',null,OFF),
                'aspnet_version'    => $this->request->getPost('aspnet_version'),
                'cgi'               => $this->request->getPost('cgi_support',null,OFF),
                'perl'              => $this->request->getPost('perl_support',null,OFF),
                'python'            => $this->request->getPost('python_support',null,OFF),
                'fast_cgi'          => OFF,

                'statistic_support' => $this->request->getPost('web_statistics_support',null,OFF),
                'statistic_type'    => $this->request->getPost('web_statistics_type',null),
                'dedicated_pool'    => $this->request->getPost('dedicated_pool',null,OFF),
                'max_cpu'           => $this->request->getPost('max_cpu_usage',null,'0'),
                'application_pool_mode'    => $this->request->getPost('application_pool_mode',null),
                'ssl_list'          => $this->request->getPost('ssl_list'),

                // Ftp Settings
                'ftp_username'      => $this->request->getPost('ftp_username'),
                'password'          => $this->request->getPost('password'),
                'web_server_id'     => $this->domain_data['web_server_id'],
            );
            $nested = true;
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $ftp_id = $data['user']['domain_info']['ftp_user_id'];
            $post_data = array(
                'ssl'    => $this->request->getPost('ssl_support',null,OFF),
                'ssl_list'    => $this->request->getPost('ssl_list'),
                // Ftp Settings
                'ftp_username'    => $this->request->getPost('ftp_username'),
                'password'    => $this->request->getPost('password'),
                'web_server_id' => $this->user_data['user']['domain_settings']['web_server_id'],
            );
            $nested = false;
        }

        $post_data['ftp_id'] = $ftp_id;
        $post_data['domain_id'] = $domain_id;

        $webhosting_settings = new Webhosting();
        $webhosting_settings->nested = $nested; // Domain içine yönetmek için girilmişse (Nested durumu)
        $webhosting_settings->setPosts($post_data);
        $webhosting_settings->ip_list = $ip_list;
        $webhosting_settings->ssl_list = $ssl_list;

        if(!$webhosting_settings->validation(true)) {
            foreach ($webhosting_settings->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        else {
            $ftp_user_update = $webhosting_settings->updateFTPUser();
            if (!$ftp_user_update) {
                foreach ($webhosting_settings->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }
            else {
                if(isset($this->domain_id)) {
                    if (!$webhosting_settings->updateDomainIPAddr()) {
                        foreach ($webhosting_settings->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        return false;
                    }
                    $domain_settings_update = $webhosting_settings->updateDomainSettings(true);
                }
                else {
                    $domain_settings_update = $webhosting_settings->updateDomainSettings();
                }

                if ($domain_settings_update) {
                    return true;
                }
                else {
                    foreach ($webhosting_settings->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    return false;
                }
            }
        }
    }
}