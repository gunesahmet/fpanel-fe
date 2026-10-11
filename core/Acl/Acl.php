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
 */namespace FPanel\Acl;

use FPanel\Environments\Parser;
use FPanel\Environments\QueryBuilder;
use FPanel\Menu\Sidebar;
use FPanel\Models\Domains;
use FPanel\Models\Messages;
use FPanel\Models\Support;
use FPanel\Models\Users;
use FPanel\Process\Admin;
use FPanel\System\SysCore;
use Phalcon\Mvc\User\Component;

/**
 * Class Acl
 * @package FPanel\Acl
 */
class Acl extends Component
{
    protected $user_id;
    protected $user_type;
    protected $user_data;
    protected $nested = false;

    public function run($page, bool $nested = false)
    {
        $identity = $this->auth->getIdentity();
        if($this->dispatcher->getControllerName() == "main")
            exit;

        $this->nested = $nested;
        $this->view->setVar('nested', $nested);

        // TimeOut
        $this->sessionTimeOut();

        $user_type = $identity['type'];
        $this->user_type = $user_type;

        $system = new SysCore();
        $server_data = $system->getSystemData();

        $system_info = $this->getSystemInfo($server_data);

        $builder = new Builder();

        $user_data = $builder->user($identity, $system_info)
            ->execute()
            ->build();

        if(!$user_data) {
            $this->forbidden(__("Sorry, it's not allowed to go beyond this point!"));
        }

        $this->changedDataShow($user_data['user']['info']);

        $this->user_data = $user_data;
        $this->isAllowedSystem();
        $this->isAllowed($page);
        $this->checkDiskLimits($user_data);
        $this->checkLimits($page);

        $this->loggedSupportUser();


        $user_data['system_info'] = $system_info;


        $data = $user_data;
        $data['system_data'] = $server_data;

        if($data['user']['info']['must_change_password'] == ACTIVE) {
            if($this->dispatcher->getControllerName() != "account") {
                return $this->response->redirect($this->uri->url_encrypt('account'),true);
            }
        }

        $sidebar = new Sidebar();
        $data['sidebar'] = $sidebar->sidebar($user_data, $user_type, $server_data);
        $this->view->setVar('panel_system', $server_data['system_type']);

        $current_timestamp = $this->date->dateTime();
        $message = Messages::find("status ='1' AND expire_date > '$current_timestamp'");
        if(!empty($message)) {
            $sys_message = $message->toArray();
        }
        else {
            $sys_message = array();
        }

        $this->view->setVar('sys_messages', $sys_message);
        return $data;
    }

    private function forbidden($message)
    {
        $this->response->setStatusCode(403, "Forbidden");
        $this->view->setVar('error_content', $message);
        $this->view->pick("errors/403");
        exit(403);
    }

    public function isAllowed($request)
    {
        if(($this->user_type > 0) && ($this->user_type < 6)) {
            $request_list = array(
                // Example
                // 'page'   => 'permission for user',
                'users' => 'man_client',
                'domains' => 'man_domain',
                'subdomain' => 'man_subdomain',
                'domain_alias' => 'man_domain_alias',
                'email_alias' => 'man_email_alias',
                'hosting_settings' => 'man_hosting_settings',
                'task' => 'man_task',
                'ssl' => 'man_ssl',
                'dns' => 'man_dns',
                'mailing_lists' => 'man_mailing_lists',
                'spam_filters' => 'man_spam_filters',
                'backup' => 'man_backup',
                'database' => 'man_database',
                'email' => 'man_email',
                'protect_dir' => 'man_protect_dir',
                'templates' => 'man_template',
            );
            foreach ($request_list as $page => $permission ) {
                if ($request == $page) {
                    if ($this->user_data['user']['permissions'][$permission] != ALLOWED) {
                        $this->forbidden(__("Sorry, it's not allowed to go beyond this point!"));
//                        return $this->response->redirect($this->uri->url_encrypt('dashboard'));
                    }
                }
            }
        }
    }

    public function isAllowedSystem()
    {
        if(($this->user_type > 0) && ($this->user_type < 6)) {
            if ($this->user_data['user']['permissions']['access_panel'] != ALLOWED) {
                $this->forbidden(__("Sorry, it's not allowed to go beyond this point!"));
//                $this->auth->remove();
//                $this->response->redirect("/",true);
            }
        }
    }

    public function getSystemInfo($server_data)
    {
        $system = new Admin();
        $system->setServerId($server_data["server_id"]);

        return $system->sysinfo();
    }

    private function loggedSupportUser()
    {
        $ini = new Parser(ROOTPATH.'/config/config.ini');

        if($this->user_type == SUPPORT_USER) {
            $user_data = $this->user_data;

            $username = $user_data['user']['info']['username'];
            $support_user = Support::findFirst("user = '$username'");

            if($support_user) {
                if($support_user->connecting_status != "0") {

                    $ini->data['support']['status'] = "\"true\"";

                    if($support_user->disable_clients_logins == "1") {
                        $ini->data['support']['logins'] = "\"true\"";
                    }

                    $support_user->assign(array(
                        'connecting_status' => '1'
                    ));

                    if(!$support_user->update()) {
                        $error_messages = [];
                        foreach($support_user->getMessages() as $message) {
                            $error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "File" => "Update support use connection",
                            "Error" => "Update support user connection status failed",
                            "Data" => $error_messages
                        ]);
                    }
                    $ini->write();
                }
            }
        }
        $this->view->setVar('support_status', $ini->data['support']['status']);

    }

    public function getClientData($identity = array())
    {
        $builder = new Builder();
        return $builder->user($identity)
            ->execute()
            ->build();
    }

    private function sessionTimeOut()
    {
        $idletime = $this->config->session->timeout;
        if($idletime != "0") {
            if($this->session->get('session_time') == null) {
                $this->response->redirect("/",true);
            }
            if (abs(time()-$this->session->get('session_time')) > $idletime) {
                $this->session->destroy();
                $this->response->redirect("/",true);
            }
            else {
                $this->session->set('session_time',time());
            }
        }
    }

    private function checkLimits($request)
    {
        if(($this->user_type > 0) && ($this->user_type < 6)) {
            $limits_list = array(
                // 'page'   => 'permission for user',
                'users' => array(
                    'clients'
                ),
                'domains' => array(
                    'domains'
                ),
                'subdomain' => array(
                    'subdomains'
                ),
                'domain_alias' => array(
                    'domainalias'
                ),
                'mailing_lists' => array(
                    'mailing_lists'
                ),
                'database' => array(
                    'database',
//                    'disk',
//                    'database_size'
                ),
                'email' => array(
                    'domainemail',
//                    'email_size',
//                    'disk',
                ),
                'templates' => array(
                    'serviceplans'
                ),
            );


            foreach ($limits_list as $page => $limits ) {
                if ($request == $page) {
                    if($this->dispatcher->getActionName() == "create") {
                        foreach($limits as $limit) {
                            if ($this->user_data['user']['remaining'][$limit] <= "0") {
                                if($this->request->isAjax()) {
                                    $response = array('message' => __("Limits are not allowed for this action"));
                                    $this->response->setJsonContent($response)->send();
                                    exit(1);
                                }
                                else {
                                    $this->forbidden(__("Sorry, it's not allowed to go beyond this point!"));
//                                    $this->flashSession->error(json_encode([__("Limits are not allowed for this action")]));
//                                    return $this->response->redirect($this->uri->url_encrypt('dashboard'));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function changedDataShow($data)
    {
        $html = "";
        if(!empty($data['changed_data'])) {
            foreach($data['changed_data'] as $key => $value) {
                switch($key) {
                    case "network":
                        $html = $html.'<div class="alert alert-info" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i> <strong>'. __('Warning').' </strong> '. __('You have not yet updated your network configuration.'). ' <a class="label label-success" data-id="confirm" data-url="' . $this->uri->url_encrypt('network/init/apply') . '">' .__('Apply now!'). '</a> or '. ' <a class="label label-danger" href="' . $this->uri->url_encrypt('network/init/cancel') . '">' .__('Cancel!'). '</a></div>';
                    break;
                    case "dns":
                        $html = $html.'<div class="alert alert-info" style="margin-top:5px"> <button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i> <strong>'. __('Warning').' </strong> '. __('You have not yet made any changes.'). ' <a class="label label-danger" data-id="confirm" data-url="' . $this->uri->url_encrypt('dns/init') . '">' .__('Apply DNS settings now!'). '</a></div>';
                        break;
                    default:
                        break;
                }
            }
        }
        $this->view->setVar('changed_data', $html);
    }

    private function checkDiskLimits($data)
    {
        $this->view->setVar('open_user',false);
        if(($data['user']['info']['user_type'] != ROOT_USER) && ($data['user']['info']['user_type'] != ADMIN_USER)) {
            if($data['user']['info']['status'] == "-1") {
                if(($data['user']['statistics']['remain_total_traffic'] != UNLIMITED) && ($data['user']['statistics']['remain_total_traffic'] <= "0")) {
                    $this->flashSession->error(json_encode([__("Your traffic limit is overuse")]));
                }

                if($data['user']['limits']['disk_limit'] != UNLIMITED) {
                    if(($data['user']['statistics']['remain_total_disk'] != UNLIMITED) && ($data['user']['statistics']['remain_total_disk'] <= "0")) {
                        $this->flashSession->error(json_encode([__('Your disk limit is overuse')]));
                    }
                }
                $this->blockCreatePages();
            }
            else if($data['user']['info']['status'] == "0") {
                $this->session->destroy();
                if($this->request->isAjax()) {
                    $response = array('message' => __("Limits are not allowed for this action"));
                    $this->response->setJsonContent($response)->send();
                    exit;
                }
                else {
                    $this->flashSession->error(json_encode([__("Limits are not allowed for this action")]));
                    $this->response->redirect("/",true);
                }
            }
        }

    }

    private function blockCreatePages()
    {
        $allowed_controllers = [
            "dashboard",
            "resource",
            "dns",
            "subdomain",
            "database",
            "domainbackup",
            "filemanager",
            "mail",
            "named",
            "domainalias",
            "resources",
            "webstatistics",
            "cron",
        ];
        if(!in_array($this->dispatcher->getControllerName(), $allowed_controllers)) {
            $this->flashSession->error(json_encode([__('You dont have access for this action. Please contact your service provider.')]));
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    public function checkParent($client_id, $client_type)
    {
        if(($client_type == CLIENT_USER) || ($client_type == RESELLER_USER)) {
            $this->checkClientParent($client_id);
        }
        else if($client_type == DOMAIN_USER) {
            $this->checkDomainParent($client_id);
        }
    }

    private function checkDomainParent($domain_id)
    {
        $parent_id = $this->auth->getParentId();
        $user_id = $this->auth->getUserId();
        $user_domains = $this->queryBuilder->select([
            "domains.id"
        ])
            ->from("domains")
            ->innerJoin("users as clients","clients.id = domains.client_id")
            ->innerJoin("users","users.id = domains.client_id")
            ->where([
                'conditions' => "clients.parent = ?1",
                'bind' => [1 => $parent_id]
            ])
            ->andwhere([
                'conditions' => "domains.client_id = ?1",
                'bind' => [1 => $user_id]
            ])
            ->find();

        $status = false;
        foreach($user_domains as $domain) {
            if($domain_id == $domain['id'])
                $status = true;
        }

        if(!$status) {
            $this->forbidden(__("Sorry, it's not allowed to go beyond this point!"));
        }
    }

    private function checkClientParent($client_id)
    {
        if($this->auth->getUserType() > ADMIN_USER) {
            $parent_id = $this->auth->getParentId();
            $user_clients = $this->queryBuilder->select([
                "users.id"
            ])
                ->from("users")
                ->where([
                    'conditions' => "users.parent = ?1",
                    'bind' => [1 => $parent_id]
                ])
                ->find();

            $status = false;
            foreach($user_clients as $client) {
                if($client_id == $client['id'])
                    $status = true;
            }
            if(!$status) {
                $this->forbidden(__("Sorry, it's not allowed to go beyond this point!"));
            }
        }
    }
}
