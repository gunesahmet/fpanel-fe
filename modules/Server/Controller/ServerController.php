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
namespace Server\Controller;

use FPanel\Cloud\Servers as CloudServers;
use FPanel\Forms\ServerForm;
use FPanel\Forms\SystemLogForm;
use FPanel\Forms\SystemTimeForm;
use FPanel\Globals\Globals;
use FPanel\Menu\System;
use FPanel\Models\ServerLogSettings;
use FPanel\Models\Servers;
use FPanel\Process\Admin;
use FPanel\Process\Log as Logmng;
use FPanel\Server\ServerSettings;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class ServerController
 * @package Server\Controller
 */
class ServerController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $server_os_type;
    protected $user_type;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('system');
        $this->user_data = $user_data;
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];

        $this->system_data = $user_data['system_data'];

        $this->view->setVar('system_items', $user_data);

        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');

        if($this->system_data["system_type"] == CLOUD) {
            $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
        }
        else {
            $this->breadcrumbs->add(__('System Dashboard'),'system',' ');
        }
        $this->server_os_type = PANEL_OS_TYPE;
        $params = $this->dispatcher->getParams();
        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "server":
                    $server_id = $params['server_id'];
                    $server = Servers::findFirstById($server_id);
                    if(!$server) {
                        $this->view->setVar('error_content',__('Server not found'));
                        $this->response->setStatusCode(404, __('Server not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $server_data = $server->toArray();
                        $this->server_id = $server_id;
                        $this->view->setVar('server_id', $server_id);
                        if($server_id != "1")
                            $this->breadcrumbs->add($server_data['name'],'server/dashboard/server/'.$server_id,' ');
                        $this->server_data = $server_data;
                        $this->server_os_type = $this->env->getOs($server->os);
                    }
                    $this->server_id = $server_id;
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        return true;
    }

    public function createAction()
    {
        $this->view->setVar("server_pkgs", [
            "Web Server" => [
                'apache' => "Apache",
                'nginx' => "Nginx",
                'proftpd' => "Proftp",
                'iis' => "IIS (ASP & ASP.NET & ASP.NET Core)",
                'perl' => "Perl",
                'python' => "Python",
                'php56' => "PHP 5.6",
                'php72' => "PHP 7.2",
                'php74' => "PHP 7.4",
                'roundcube' => "Roundcube Webmail",
                'phpmyadmin' => "phpMyAdmin",
                'mylittleadmin' => "MyLittleAdmin",
                'awstats' => "Awstats",
                'webalizer' => "Webalizer",
            ],
            "DNS Server" => [
                'bind' => "Bind DNS Server",
//        'msdns' => "Microsoft DNS Server",
            ],
            "Database Servers" => [
                'mssql12' => "Microsoft SQL Server 2012",
                'mssql16' => "Microsoft SQL Server 2016",
                'mysql' => "MySQL",
                'mysql57' => "MySQL 5.7",
                'mysql80' => "MySQL 8.0",
                'mariadb' => "MariaDB",
                'postgresql' => "PostgreSQL Server",
            ],
            "Mail Server" => [
                'postfix' => "Postfix",
                'dovecot' => "Dovecot",
                'hmail' => "hMail Server",
                'mailenable' => "MailEnable",
                'spamassassin' => "Spamassassin",
            ],
        ]);
        $this->tag->appendTitle(__('Create New Server'));
        $this->breadcrumbs->add(__('Create Server'),'','');

        $options = array(
            'page' => 'create',
            'action'    => 'create'
        );
        $this->view->form = new ServerForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();
            $post_data = array(
                'agent_status'          => $this->request->getPost('agent_status') != null ? $this->request->getPost('agent_status') : "off",
                'server_os'             => $this->request->getPost('server_os'),
                'ip_address'            => $this->request->getPost('ip_address'),
                'username'              => $this->request->getPost('server_os') == WINDOWS ? "administrator" : "root",
                'password'              => $this->request->getPost('password'),
                'hostname'              => $this->request->getPost('hostname'),
            );
            $cloud = new CloudServers();
//            $cloud->setTest();//@TODO Remove it
            $cloud->setPosts($post_data);
            $status = $cloud->connectionServer();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Connection successful. After reviewing the information, you can move on to the next step.');
            $messages['errors'] = [];

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Error');
                $messages['messages'] = __('Connection failed');
                foreach ($cloud->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            else {
                $this->session->set("agent_status", $this->request->getPost('agent_status'));
                $this->session->set("server_os", $this->request->getPost('server_os'));
                $this->session->set("ip_address", $this->request->getPost('ip_address'));
                $this->session->set("password", $this->request->getPost('password'));
                $this->session->set("username", $this->request->getPost('server_os') == WINDOWS ? "administrator" : "root");
                $messages['sinfo'] = $cloud->getServerInfo();
                $messages['is_core'] = (strpos(strtolower($messages["sinfo"]["result"]["os"]), "core") !== false);
                $this->session->set("hostname", $this->request->getPost('hostname'));
                $this->session->set("installed_os", $messages["sinfo"]["result"]["os"]);
                $this->session->set("ips", $messages["sinfo"]["result"]["ips"]);
                $this->session->set("sinfo", $messages['sinfo']);
            }
            if($this->request->isAjax()) {
                $this->response->setJsonContent($messages)->send();
            }
            else {
                $this->flashSession->success(__('Server connection successful'));
                return $this->response->redirect($this->uri->url_encrypt("server/installation"),true);
            }
        }
        return true;
    }

    /**
     * @return ResponseInterface|void
     */
    public function installationAction()
    {
        $this->tag->appendTitle(__('Create New Server'));
        $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
        $this->breadcrumbs->add(__('Create Server'),'server/create','');
        $this->breadcrumbs->add(__('Install Packages'),'','');

        if($this->request->isAjax())
            $this->view->disable();

        $options = array(
            'page' => 'create',
            'action'    => 'packages'
        );
        $this->view->form = new ServerForm(null, $options);
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $cloud = new CloudServers();
//            $cloud->setTest();//@TODO Remove it
            $cloud->setPost("hostname", $this->session->get('hostname'));
            $status = $cloud->installationServer();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Installation successful. After you finish, your server will be ready to serve.');
            $messages['errors'] = [];

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Error');
                $messages['messages'] = __('Connection failed');
                foreach ($cloud->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            else {
                $status = $cloud->getServerConfiguration();
                if($status) {
                    $cloud->setMainServer();
                    sleep(1);
                    $cloud->rebootServer();
                    $server_data = $cloud->initServer($cloud->getServerId());
                    if(!$server_data) {
                        $messages['type'] = 'error';
                        $messages['title'] = __('Failed');
                        $messages['messages'] = __('Installation failed. Please check error contents.');
                        $messages["errors"] = $cloud->getMessages();
                    }
                    else {
                        $messages['sql_pass'] = $cloud->getSqlPass();
                        $messages['server_data'] = $server_data;
                        $messages['server_id'] = $cloud->getServerId();
                    }
                }
                else {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Error');
                    $messages['messages'] = __('Connection failed');
                    foreach ($cloud->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
            }

            if($this->request->isAjax()) {
                $this->response->setJsonContent($messages)->send();
            }
            else {
                $this->flashSession->success(__('Server connection successful'));
                return $this->response->redirect($this->uri->url_encrypt("server/save"),true);
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function saveAction()
    {
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $cloud = new CloudServers();

            $status = $cloud->saveServer();
            $server_id = $cloud->getServerId();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Server saved. Now you can manage saved server from cloud dashboard.');
            $messages['errors'] = [];

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Error');
                $messages['messages'] = __('Connection failed');
                foreach ($cloud->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            else {
                $messages['redirect_url'] = $this->uri->url_encrypt("system/licence/server/$server_id");
            }

            if($this->request->isAjax()) {
                $this->response->setJsonContent($messages)->send();
            }
            else {
                $this->flashSession->success(__('Server connection successful'));
                return $this->response->redirect($this->uri->url_encrypt("system/licence/server/$server_id"),true);
            }
        }

        $this->tag->appendTitle(__('Save Server'));
        $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
        $this->breadcrumbs->add(__('Create Server'),'server/create','');
        $this->breadcrumbs->add(__('Install Packages'),'server/installation','');
        $this->breadcrumbs->add(__('Save Server'),'','');

        if($this->request->isAjax())
            $this->view->disable();

        $options = array(
            'page'      => 'create',
            'action'    => 'packages'
        );
        $this->view->form = new ServerForm(null, $options);

    }

    /**
     * @param $server_id
     * @return bool|ResponseInterface|void
     */
    public function editAction($server_id)
    {
        $this->tag->appendTitle(__('Edit Server'));
        $this->breadcrumbs->add(__('Edit Server'),'','');

        $server = Servers::findFirstById($server_id);
        $this->view->setVar("server_id", $server_id);

        if(!$server) {
            $this->view->setVar('error_content',__('Server not found'));
            $this->response->setStatusCode(404, __('Server not found'));
            $this->view->pick("errors/404");
            return true;
        }
        else {
            if(($this->request->isPost()) && ($this->request->isAjax())) {
                $this->view->disable();

                $messages = Globals::messages();
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Update successful');

                $cloud = new CloudServers();
                $server_update = $cloud->updateServer($server_id);
                if( !$server_update) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Update failed');
                    $messages['errors'] = $cloud->getMessages();

                    foreach ($server->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                }

                return $this->response->setJsonContent($messages)->send();
            }
            else {
                $this->tag->appendTitle(__('Edit Server'));
                $options = array(
                    'action'    => 'edit',
                    'server_type'  => $server->type,
                    'server_os'  => $server->os,
                    'server_name'  => $server->name,
                    'server_status'  => $server->os,
                    'description'  => $server->description,
                    'ip_address'  => $server->ip_address,
                    'domain_limit'  => $server->domain_limit,
                    'database_limit'  => $server->database_limit,
                    'mail_limit'  => $server->mail_limit,
                    'data'  => json_decode($this->crypto->bdecode($server->data), true),
                );
                $this->view->form = new ServerForm(null, $options);
            }
        }
    }

    /**
     * @param $server_id
     */
    public function deleteAction($server_id)
    {
        $this->view->disable();

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Successful');
        $messages['messages'] = __('Server deleted...');

        if (($this->request->isAjax()) && ($this->request->isPost())) {
            $server = new CloudServers();
            $server->setPosts(array('server_id' => $server_id));
            $server_delete_status = $server->deleteServer();
            if (!$server_delete_status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Delete Failed');
                $messages['messages'] = __('The server cannot be deleted');
                $messages['errors'] = [];
                foreach($server->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
            }
            $this->response->setJsonContent($messages)->send();
        }

    }

    /**
     * @param $server_id
     * @return bool|void
     */
    public function dashboardAction($server_id)
    {
        $this->view->pick('server/dashboard');
        $this->tag->appendTitle(__('Server Dashboard'));
        $server = Servers::findFirstById($server_id);
        if(!$server) {
            $this->view->setVar('error_content',__('Server not found'));
            $this->response->setStatusCode(404, __('Server not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $this->breadcrumbs->add($server->name,"",'');
        $menu = new System();
        $system_type = $this->system_data['system_type'];
        $this->view->setVar("server_type", $this->server_data);
        $dashboard_buttons = $menu->getButtons($system_type, true, $server_id);
        $this->view->setVar('dashboard_buttons', $dashboard_buttons);
    }

    /**
     * @return ResponseInterface|void
     */
    public function logSettingsAction()
    {
        $this->tag->appendTitle(__('Server Log Settings'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $redirect_url = $this->uri->url_encrypt("server/logsettings/server/$server_id");
            $post_url = $this->uri->url_encrypt("server/logsettings/server/$server_id");
        }
        else {
            $redirect_url = $this->uri->url_encrypt("server/logsettings");
            $post_url = $this->uri->url_encrypt("server/logsettings");
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Server Log Settings'),'','');



        $server_log = ServerLogSettings::findFirst("server_id = '$server_id'");

        if($server_log) {
            $server_log_settings = $server_log->toArray();
        }
        else {
            $server_log_settings = array(
                'periodic_delete' => "0",
                'store_day'       => "30",
                'compressing'     => "1",
                'send_mail'       => "0",
                'email'           => "",
            );
        }

        $options = array(
            'periodic_delete'          => $server_log_settings['periodic_delete'],
            'store_time'            => $server_log_settings['store_day'],
            'log_compress'          => $server_log_settings['compressing'],
            'send_information'      => $server_log_settings['send_mail'],
            'email_address'         => $server_log_settings['email'],
        );
        $this->view->form = new SystemLogForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $post_data = array(
                'periodic_del'          => $this->request->getPost('periodic_del'),
                'store_time'            => $this->request->getPost('store_time'),
                'log_compress'          => $this->request->getPost('log_compress'),
                'send_information'      => $this->request->getPost('send_information'),
                'email_address'         => $this->request->getPost('email_address'),
                'server_id'             => $server_id,
            );
            $server = new ServerSettings();
            $server->setPosts($post_data);
            $status = $server->serverLogSettings();
            if(!$status) {

                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($server->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Server logs settings saved'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }

    }

    /**
     * @return ResponseInterface|void
     */
    public function timeSettingsAction()
    {
        $this->tag->appendTitle(__('System Time Settings'));
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            if(isset($this->server_id)) {
                $redirect_url = $this->uri->url_encrypt("server/timesettings");
                $post_url = $this->uri->url_encrypt("server/timesettings");
            }
            else {
                $redirect_url = $this->uri->url_encrypt("server/timesettings/server/$server_id");
                $post_url = $this->uri->url_encrypt("server/timesettings/server/$server_id");
            }
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("server/timesettings");
            $post_url = $this->uri->url_encrypt("server/timesettings");
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('System Time Settings'),'','');
        $server = new ServerSettings();
        $date = $server->getDateFromServer($server_id);
        $options = array(
            'timezone'      => $date['timezone'],
            'sync_time'     => $date["sync"],
            'ntp_address'   => $date['ntp_address'],
            'system_date'   => $date['year'].'/'.$date['month'].'/'.$date['day'],
            'system_time'   => $date['hour'].':'.$date['minute'].':'.$date['seconds'],
            "server_os"     => $this->server_os_type
        );
        $this->view->form = new SystemTimeForm(null, $options);
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $post_data = array(
                'system_date'          => $this->request->getPost('system_date'),
                'system_time'            => $this->request->getPost('system_time'),
                'timezone'          => $this->request->getPost('time_zone'),
                'update_time'      => $this->request->getPost('update_time',null,OFF),
                'sync_time'         => $this->request->getPost('sync_time',null,OFF),
                'ntp_address'         => $this->request->getPost('ntp_address'),
                'server_id'             => $server_id,
            );
            $server->setPosts($post_data);
            $status = $server->serverTimeSettings($server_id);
            if( !$status) {

                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($server->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('System Time updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function optimizeAction()
    {
        $this->view->disable();
        if(($this->request->isPost()) && ($this->request->isAjax())) {
            if($this->system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }
            $adminmng = new Admin();
            $adminmng->setServerId($server_id);
            $adminmng->sysRelax();

            $messages = Globals::messages();
            $messages['type'] = 'info';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'));
        }
    }

    /**
     * @return ResponseInterface|void
     * @throws \Exception
     */
    public function rebootAction()
    {
        $this->view->disable();
        if(($this->request->isPost()) && ($this->request->isAjax())) {
            $messages = Globals::messages();
            $messages['type'] = 'info';
            $messages['title'] = __('Success');
            $messages['messages'] = __('System restarting');

            if(demo) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode');
                return $this->response->setJsonContent($messages)->send();
            }
            if($this->system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }
            $adminmng = new Admin();
            $adminmng->setServerId($server_id);
            $adminmng->reboot();


            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'));
        }
    }

    /**
     * @throws \Exception
     */
    public function showlogsAction()
    {
        $this->view->disable();
        if($this->request->isAjax()) {
            $log = new Logmng();
            if($this->system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
                $log->setServerId($server_id);
            }

            $result = $log->showPanel();
            $result_array = explode("\n", $result);

            $logs = [];
            foreach($result_array as $line) {
                if(trim($line) != null) {
                    $res = preg_split("/[(?<=\[).*?(?=\])]/", $line);
                    if(isset($res[1])) {
                        $message = preg_replace("/\[".$res[1]."\].*"."\[".$res[3]."\].*"."\[".$res[5]."\]/","", $line);
                        $logs[] = array(
                            'date' => $res[1],
                            'type' => $res[3],
                            'code' => $res[5],
                            'message' => $message
                        );
                    }
                }
            }
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action success');
            $messages['logs'] = $logs;
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect('/',true);
        }
    }

    public function resourceAction()
    {
        $this->tag->appendTitle(__('Server Resource View'));
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $server = new ServerSettings();
        $resource_info = $server->getResourceServer($server_id);

        $this->queryBuilder->select([
            "domain_statistics.*"
        ])
            ->from("domain_statistics")
            ->innerJoin("domains", "domains.id = domain_statistics.domain_id")
            ->innerJoin("servers", "domains.web_server_id = servers.id");
            if($server_id != "1") {
                $this->queryBuilder->where([
                    'conditions' => "servers.id = ?1",
                    'bind' => [1 => $server_id]
                ]);
            }
        $statistics = $this->queryBuilder->find();
//        $statistics = DomainStatistics::find("server_id = '$server_id'")->toArray();
        $this->view->setVar('statistics', $statistics);
        $this->view->setVar('resources', $resource_info);

        $this->breadcrumbs->add(__('Server Resource View'),'','');

    }

}