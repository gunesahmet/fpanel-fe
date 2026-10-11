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
 */namespace Dashboard\Controller;

use FPanel\Environments\QueryBuilder;
use FPanel\Globals\Globals;
use FPanel\Menu\Dashboard;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\Models\Mails;
use FPanel\Models\SystemSettings;
use FPanel\Models\Users;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class DashboardController
 * @package Dashboard\Controller
 */
class DashboardController extends ControllerBase
{
    protected $user_data = array();
    protected $client_id;
    protected $client_type;
    protected $client_data;
    protected $user_id;
    protected $user_type;
    protected $system_info;
    protected $server_misc_data;
    protected $nested = false;

    /**
     * @var Dashboard
     */
    private $dashboard;

    /**
     * @return ResponseInterface|void
     */
    public function initialize()
    {
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        parent::initialize();
        if(!$this->session_status) {
            return;
        }

        $params = $this->dispatcher->getParams();
        $this->nested = false;
        if(!empty($params)) {
            if(($params['type'] == "client") || ($params['type'] == 'domain')) {
                $this->nested = true;
            }
        }

        $this->view->setVar('nested', $this->nested);
        $this->user_data = $this->acl->run('dashboard', $this->nested);

        $this->system_info = $this->user_data['system_info'];
        if(!isset($this->system_info['licence'])) {
            if($this->request->isPost() && $this->request->isAjax()) {
                $message = array(
                    'message' => 'licence',
                    'content' => __('Your licence is not allowed for this process')
                );
                return $this->response->setJsonContent($message)->send();
            }
            else {
                if($this->user_data['user']['info']['user_type'] == ROOT_USER || $this->user_data['user']['info']['user_type'] == SUPPORT_USER || $this->user_data['user']['info']['user_type'] == ADMIN_USER) {
                    header("Location: ".$this->uri->url_encrypt("system/licence"));
                }
//                else {
//                    //@TODO check for normal user
//                }
            }
        }
    }

    public function indexAction()
    {
        if($this->auth->getUserType() != EMAIL_USER) {
            $this->systemUsers();
        }
        else {
            $this->emailUsers();
        }
    }

    public function emptyAction()
    {
        $this->view->setVar('user_data', $this->user_data);
        $this->view->setVar("system_items", $this->user_data);
    }

    /**
     * @return void
     */
    private function systemUsers(): void
    {
        $this->tag->appendTitle(__('Dashboard'));
        $this->view->pick("dashboard/index");

        $this->view->setVar('user_type', (int)$this->user_data['user']['info']['user_type']);

        $this->view->setVar('user_data', $this->user_data);
        $this->view->setVar("system_items", $this->user_data);

//        $this->debug->debug($this->user_data, false);
        $this->dashboard = new Dashboard();
        $params = $this->dispatcher->getParams();
        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "domain":
                    $this->view->setVar('domain_dashboard',true);
                    $this->domain($params['id'], true);
                    break;
                case "client":
                    $this->view->setVar('client_dashboard',true);
                    $this->client($params['id']);
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return;
            }
        }
        else {
            if(isset($this->user_data['user']['info']['user_type'])) {
                $user_type = $this->user_data['user']['info']['user_type'];
                if(($user_type == ROOT_USER) || ($user_type == ADMIN_USER) || ($user_type == SUPPORT_USER)) {
                    $this->admin();
                }
                else if($user_type == DOMAIN_USER) {
                    $this->domain($this->user_data['user']['domain_info']['domain_id']);
                }
                else if($user_type == CLIENT_USER || $user_type == RESELLER_USER) {
                    $this->client($this->user_data['user']['info']['user_id']);
                }
            }
            $this->user_data['dashboard'] = $this->dashboard->create($this->user_data);
        }

        $this->view->setVar('username', $this->user_data['user']['info']['username']);
        $this->view->setVar("system_items", $this->user_data);

        $general_items = $this->user_data['user']['info'];
//        if(($general_items['user_type'] > ADMIN_USER) && ($general_items['user_type'] < SUPPORT_USER)) {
//            $limit_items = $this->user_data['user']['limits'];
//            $perm_items = $this->user_data['user']['permission'];
//        }
//        $statistics_items = $this->user_data['user']['usage'];
        $last_login_date = "-";
        if (!empty($general_items['last_login']) ) {
            $last_login_date = $general_items['last_login'];
        }
        $this->view->setVar("last_login_date", $last_login_date);
        $this->view->setVar("general_items", $general_items);
    }

    /**
     *
     */
    private function emailUsers()
    {
        $this->tag->appendTitle(__('Email Management'));
        $this->view->setMainView("email/index");
        $email_id = $this->auth->getUserId();
        $qbuilder = new QueryBuilder();
        $mail_data = $qbuilder->select([
            "mails.id",
            "domains.`name` as domain_name",
            "mails.mail_name",
            "mails.`password`",
            "mails.domain_id",
            "mails.forward",
            "mails.forward_addr",
            "mails.mail_group",
            "mails.autoresponder",
            "mails.mailbox_quota",
            "mails.`status`",
            "GROUP_CONCAT(mail_alias.alias_name) as mail_aliases",
        ])
            ->from('mails')
            ->leftJoin("mail_alias",'mails.id = mail_alias.mail_id')
            ->innerJoin('domains','mails.domain_id = domains.id')
            ->where([
                'conditions' => "mails.id = ?1",
                'bind' => [1 => $email_id]
            ])
            ->findFirst();
        $this->view->setVar('mail_data', $mail_data);
        $dataRules = array('message' => 'error', 'content' => __('Email wasn\'t update! Please check your fields.'));

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $old_password = $this->filter->sanitize($this->request->getPost('old_password'),'string');
            $password = $this->filter->sanitize($this->request->getPost('password'),'string');
            $confirm_password = $this->filter->sanitize($this->request->getPost('confirm-password'),'string');

            $this->validation->Required(array(
                array(
                    'item' => $old_password,
                    'field' => 'old_password',
                    'message' => __('Old Password is required')
                ),
                array(
                    'item' => $password,
                    'field' => 'password',
                    'message' => __('Password is required')
                ),
                array(
                    'item' => $confirm_password,
                    'field' => 'confirm-password',
                    'message' => __('Please Confirm Password')
                ),
            ))
                ->Confirm($password,'password', $confirm_password,__('Please confirm password'));
            $messages = Globals::messages();

            if($this->validation->Validate() == false) {
                foreach($this->validation->getMessage() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            else {
                $email = Mails::findFirstById($this->auth->getUserId());
                if(!$email) {
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode([__('Email address is not found')]));
                    if($this->request->isAjax())
                        $this->response->setJsonContent($dataRules)->send();
                    else
                        header('Location: /');
                }
                else {
                    if(trim($this->crypto->bdecode($email->password)) != $old_password) {
                        if(!$this->request->isAjax())
                            $this->flashSession->error(json_encode([__('Your password is not match with old password')]));

                        if($this->request->isAjax())
                            $this->response->setJsonContent($dataRules)->send();
                    }
                    else {
                        $email->password = $this->crypto->bencode($password);
                        if(!$email->update()) {
                            foreach($email->getMessages() as $title => $message) {
                                $messages['errors'][$title] = $message;
                            }
                            if(!$this->request->isAjax())
                                $this->flashSession->error(json_encode($messages['errors']));
                            if($this->request->isAjax())
                                $this->response->setJsonContent($dataRules)->send();
                        }
                        else {
                            $dataRules = array('message' => 'success', 'content' => __('Email updated successful'));
                            if($this->request->isAjax()) {
                                $this->response->setJsonContent($dataRules)->send();
                            }
                            else {
                                $this->flashSession->success(__('Email updated successful'));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * searchAction
     */
    public function searchAction()
    {
        $qbuilder = new QueryBuilder();
        $this->view->disable();
        $search_string = $this->request->getPost('query');

        if(strlen($search_string) > "3") {
            $qbuilder->select([
                "domains.name",
                "users.username",
                "users.type",
                "users.id as user_id",
                "domains.id as domain_id",
            ])
                ->from("users")
                ->leftJoin("domains","users.username = domains.name");
            if(($this->user_type != ROOT_USER) && ($this->user_type != ADMIN_USER)) {
                $qbuilder->where([
                    'conditions' => "users.parent = ?1",
                    'bind' => [1 => $this->user_id]
                ])
                    ->orWhere([
                        'conditions' => "domains.client_id = ?1",
                        'bind' => [1 => $this->user_id]
                    ])
                    ->orWhere([
                        'conditions' => "users.username LIKE %?1%",
                        'bind' => [1 => $search_string]
                    ])
                    ->orWhere([
                        'conditions' => "domains.name LIKE %?1%",
                        'bind' => [1 => $search_string]
                    ]);
            }
            else {
                //"%'.$search_string.'%"
                $qbuilder->where([
                        'conditions' => "users.username LIKE %?1%",
                        'bind' => [1 => $search_string]
                    ])
                    ->orWhere([
                        'conditions' => "domains.name LIKE %?1%",
                        'bind' => [1 => $search_string]
                    ]);
            }

            $result_array = $qbuilder
                ->andWhere([
                    'conditions' => "users.type != ?1",
                    'bind' => [1 => SUPPORT_USER]
                ])
                ->find();

            if(!empty($result_array)) {
                echo '<ul class="" style="display: block;">';
                foreach ($result_array as $result) {
                    $username=$result['username'];
                    switch($result['type']) {
                        case DOMAIN_USER:
                            $link = $this->uri->url_encrypt("dashboard/domain/".$result['domain_id']);
                            $type = "globe";
                            break;
                        case CLIENT_USER:
                            $link = $this->uri->url_encrypt("dashboard/client/".$result['user_id']);
                            $type = "user";
                            break;
                        case RESELLER_USER:
                            $link = $this->uri->url_encrypt("dashboard/client/".$result['user_id']);
                            $type = "group";
                            break;
                        default:
                            $link = "";
                            $type = "user";
                    }

                    echo'<li><a href="'. $link .'" class="'. $type .'"><i></i><span class="strong">'. $username .'</span></a></li>';
                }
                echo "</ul>";

            }
            else {
				echo '<ul class="" style="display: block;"><li>';
				echo '<a href="#">' . __('There is no result') . '</a>';
				echo '</li></ul>';
            }
        }
        else {
			echo '<ul class="" style="display: block;"><li>';
			echo '<a href="#">' . __('There is no result') . '</a>';
			echo '</li></ul>';
        }
    }

    /**
     * @param $domain_id
     */
    private function checkFtpEnabled($domain_id)
    {
        $this->view->setVar('enabled_user',false);
        $domain = Domains::findFirstById($domain_id);
        if(isset($domain->params))
        if($domain->params != null) {
            $params = json_decode($domain->params,true);
            if(!empty($params['ftp_status'])) {
                $this->view->setVar('open_user',false);
                $this->view->setVar('enabled_user',true);
            }
        }
    }

    /**
     * @param $domain_id
     * @param false $nested
     */
    private function domain($domain_id, bool $nested = false)
    {
        $domain = Domains::findFirst("id = '$domain_id'");
        if(!$domain) {
            $this->view->setVar('error_content',__('Domain not found'));
            $this->response->setStatusCode(404, __('Domain not found'));
            $this->view->pick("errors/404");
        }
        else {
            if($nested && ($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER)) {
                $this->acl->checkParent($domain_id,DOMAIN_USER);
            }
            $this->checkFtpEnabled($domain_id);
            if($this->user_data["system_data"]["system_type"] == CLOUD) {
                $this->user_data["server_os"] = $domain->getWebServers()->os;
            }
            else {
                $this->user_data["server_os"] = PANEL_OS_TYPE;
            }
            if($nested) {
                $domain_client = Users::findFirst("username = '$domain->name'");
                $domain_data = $this->acl->getClientData([
                    'id' => $domain_client->id,
                    'type' => DOMAIN_USER,
                    'username' => $domain->name
                ]);
                $this->dashboard->domain_data = $domain_data;

                $this->user_data['dashboard'] = $this->dashboard->create($this->user_data, DOMAIN_USER, $domain_id, $nested);

                $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                $this->breadcrumbs->add($domain->name,'','');
                $client_menu = $this->sidebar->clientMenu(DOMAIN_USER, $domain_id);
                $this->view->setVar("client_menu", $client_menu);
                $this->view->setVar('domain_id', $domain_id);
            }
            else {
                $domain_data = $this->user_data;
            }
            $this->dashboard->domain_data = $domain_data;
            $this->view->setVar('client_data', $domain_data);
            $this->view->setVar('client_type', DOMAIN_USER);
        }
    }

    /**
     * @param $client_id
     */
    private function client($client_id)
    {
        $client = Users::findFirstById($client_id);
        if(!$client) {
            $this->view->setVar('error_content',__('User not found'));
            $this->response->setStatusCode(404, __('User not found'));
            $this->view->pick("errors/404");
        }
        else {
            $clientdata = $client->toArray();
            if(($this->auth->getUserType() != ROOT_USER) || ($this->auth->getUserType() != ADMIN_USER))
                $this->acl->checkParent($client_id, $clientdata['type']);

            $this->client_id = $client_id;
            $this->client_type = $clientdata['type'];

            $client_data = $this->acl->getClientData(array(
                'id' => $client_id,
                'type' => $this->client_type
            ));

            if(!$client_data) {
                return;
            }

            $this->dashboard->client_data = $client_data;

            $this->view->setVar('client_data', $client_data);
            $this->view->setVar('client_type', $this->client_type);
            $this->user_data['dashboard'] = $this->dashboard->create($this->user_data, $this->client_type, $this->client_id,true);

            $this->breadcrumbs->add(__('User List'),'users',' ');
            $this->breadcrumbs->add($clientdata['username'],'','');
            $client_menu = $this->sidebar->clientMenu($clientdata['type'], $this->client_id);
            $this->view->setVar("client_menu", $client_menu);

            $user_data = $this->user_data;
            if(isset($client_id)) {
                $user_data = $client_data;
            }

            $user_info = $user_data['user']['info'];
            $limits = $user_data['user']['limits'];
            $permissions = $user_data['user']['permissions'];

            $ipaddresses = "";
            foreach($user_data['user']['ip_address'] as $ip) {
                if($ipaddresses == null)
                    $ipaddresses = $ip;
                else
                    $ipaddresses = $ipaddresses . ', ' . $ip;
            }

            $expire_date = $this->env->convertDate($user_info['expire_date'],__('Never'));
            $create_date = $this->env->convertDate($user_info['created_date'],__('Never'));

            $this->view->setVar("user_info", $user_info);
            $this->view->setVar("create_date", $create_date);
            $this->view->setVar("expire_date", $expire_date);
            $this->view->setVar("ipaddresses", $ipaddresses);
            $this->view->setVar("user_data", $user_data);
            $this->view->setVar("limits", $limits);
            $this->view->setVar("permissions", $permissions);
        }
    }

    private function admin()
    {
        if(isset($this->user_data['user']['info']['user_type'])) {
            $user_type = $this->user_data['user']['info']['user_type'];
            if(($user_type == ROOT_USER) || ($user_type == ADMIN_USER) || ($user_type == SUPPORT_USER)) {

                $this->server_misc_data = SystemSettings::find()->toArray();
                foreach ($this->server_misc_data as $value) {
                    switch($value['key']) {
                        case "default_ip_address";
                            $this->view->setVar('default_ip_address', $value['value']);
                            break;
                        case "hostname":
                            $host_data = json_decode($value['value'],true);
                            $this->view->setVar('hostname', $host_data['hostname']);
                            break;
                        case "system_data":
                            $system_data = json_decode($value['value'],true);
                            $this->view->setVar('system_type', trim($system_data['system_type']));
                            $this->view->setVar('system_version', $system_data['panel_version']);
                            break;
                    }
                }


                $statistics_traffic = DomainStatistics::sum(array('column' => 'traffic'));
                if($statistics_traffic == null)
                    $statistics_traffic = "0 MB";
                else
                    $statistics_traffic = $this->env->convert($statistics_traffic);

                $total_traffic = $statistics_traffic;
                $server_uptime = "";
                $operating_system = "";
                $cpu_type = "";
                $load_average = "";
                $cpu_used = "";
                $memory_percent = "";
                $disk_percent = "";
                $total_disk = "";
//                    $total_traffic = "";

                if(isset($this->system_info["uptime"])) {
                    $uptime = $this->system_info['uptime'];
                    $server_uptime = $this->env->secondsToTime2($uptime);
                    $operating_system = $this->system_info['system']['os'];
                    $cpu_type = $this->system_info['cpu_info'];
                    $load_average = $this->system_info['loadavg']['min1'].' '.$this->system_info['loadavg']['min5'].' '.$this->system_info['loadavg']['min15'];
                    $cpu_used = $this->system_info['cpu']['used'];

                    if (substr($this->system_info['cpu']['used'], 0, 1 ) === ".") {
                        $cpu_used = "0".$this->system_info['cpu']['used'];
                    }

                    $memory_percent = round((($this->system_info['memory']['used'] / $this->system_info['memory']['total']) * 100));
                    $disk_percent = round((($this->system_info['disk']['used'] / $this->system_info['disk']['total']) * 100));
                    $total_disk = $this->env->convert($this->system_info['disk']['used']);
//                        $total_traffic = $this->env->convert($this->system_info['network']['traffic']);
                }

                $this->view->setVar('server_uptime', $server_uptime);
                $this->view->setVar('operating_system', $operating_system);
                $this->view->setVar('cpu_type', $cpu_type);
                $this->view->setVar('load_average', $load_average);
                $this->view->setVar('cpu_used', $cpu_used);

                $this->view->setVar('memory_used', $memory_percent);
                $this->view->setVar('disk_used', $disk_percent);
                $this->view->setVar('total_disk', $total_disk);
                $this->view->setVar('total_traffic', $total_traffic);

                $qbuilder = new QueryBuilder();
                $logged_total_users = $qbuilder->select([
                    "COUNT(id) as count"
                ])
                    ->from("success_logins")
                    ->where([
                        "conditions" => "session is not NULL",
                        "bind" => []
                    ])
                    ->findFirst();

                if($this->config->support->status != true)
                    $support_status = __('Closed');
                else
                    $support_status = __('Opened');
                if($this->config->api->status == true)
                    $api_status = __('Active');
                else
                    $api_status = __('Passive');

                if($this->config->telegram->status == true)
                    $telegram_status = __('Active');
                else
                    $telegram_status = __('Passive');

                $this->view->setVar('telegram_status', $telegram_status);
                $this->view->setVar('api_status', $api_status);
                $this->view->setVar('support_status', $support_status);
                $this->view->setVar('logged_total_users', $logged_total_users["count"]);
            }
        }
    }

    public function buttonsAction()
    {
        if($this->request->isAjax()) {
            $this->view->disable();
            $this->response->setJsonContent($this->desktop->desktopButtons(['client_type' => $this->user_type,'client_data' => $this->user_data]))->send();
        }
    }
}