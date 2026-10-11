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
 */namespace Database\Controller;

use FPanel\Domain\DomainDatabase;
use FPanel\Forms\DatabaseForm;
use FPanel\Globals\Globals;
use FPanel\Models\DatabaseRemoteHosts;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseServerSettings;
use FPanel\Models\DatabaseUsers;
use FPanel\Models\Domains;
use FPanel\Models\IpAddress;
use FPanel\DatabaseServer\DatabaseServer as DbServer;
use Main\Controller\ControllerBase;

/**
 * Class DatabaseController
 * @package Database\Controller
 */
class DatabaseController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $default_database_servers = array();

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('database');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        $params = $this->dispatcher->getParams();

        $this->default_database_servers = DatabaseServer::find();

        if(count($this->default_database_servers) < 1){
            $this->view->setVar('error_content',__('Database Server not found'));
            $this->response->setStatusCode(404, __('Database Server not found'));
            $this->view->pick("errors/404");
            return true;
        }


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

    public function indexAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Database List'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $this->view->setVar('domain_id', $domain_id);
            $nested = true;
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $nested = false;
        }
        $this->breadcrumbs->add(__('Database List'),'','');


        if($this->user_data['user']['remaining']['database'] < "1")
            $active_class = "disabled";
        else
            $active_class = "";

        $this->view->setVar('active_class', $active_class);


        if($this->request->isPost() && $this->request->isAjax()) {
            $this->view->disable();
            $databases = new DomainDatabase();
            $this->response->setJsonContent($databases->databaseList($domain_id, $nested))->send();
        }
        else {
            $database = new DomainDatabase();
            $db_server_status = true;
            $db_server_name = "";
            foreach($this->default_database_servers as $default_database_server) {
                if($database->connection($default_database_server->id) == false) {
                    $db_server_status = false;
                    $db_server_name = $default_database_server->db_server_name;
                }
            }

            $this->view->setVar('database_server_status', $db_server_status);
            if($db_server_status == false) {
                $this->view->setVar('database_server_message',__('Database server %database_server% connection is failed. Please contact with system administrator.',['database_server' => $db_server_name]));
            }
        }
    }

    public function createAction()
    {
        $database = new DomainDatabase();

        $data = $this->user_data;
        $this->tag->appendTitle(__('Create Database'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Database List'),'database/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('database/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt('database/create/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Database List'),'database',' ');
            $redirect_url = $this->uri->url_encrypt('database');
            $post_url = $this->uri->url_encrypt('database/create');
        }

        $this->breadcrumbs->add(__('Create Database'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('post_url', $post_url);

        $domain_data = $this->queryBuilder->select([
            "domains.id",
            "domains.prefix",
            "domains.name",
            "perms.database_limit",
        ])
            ->from("domains")
            ->innerJoin("client_limits_permissions as perms","perms.domain_id = domains.id")
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();

        $this->view->setVar('prefix', $domain_data['prefix']);
        $database_server = new DbServer();
        $database_server_list = $database_server->getDatabaseServerList();
        $options = array(
            'action'        => "create",
            'domain_data'   => $domain_data,
            'domain_name'   => $domain_name,
            'database_server_list'   => $database_server_list,
        );

        $this->view->form = new DatabaseForm(null, $options);

        if ($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();
            $database->setPost("prefix", $domain_data['prefix']);
            $database->setPost("domain_name", $domain_name);
            $database->setPost("domain_id", $domain_id);
            $database->setPost("database_server_list", $database_server_list);
            $database->setPost("action", "create");
            $status = $database->createDatabase();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;
            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($database->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status) {
                    $this->flashSession->success(__('Database is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        else {
            $database = new DomainDatabase();
            $db_server_status = true;
            $db_server_name = "";
            foreach($this->default_database_servers as $default_database_server) {
                if($database->connection($default_database_server->id) == false) {
                    $db_server_status = false;
                    $db_server_name = $default_database_server->db_server_name;
                }
            }

            $this->view->setVar('database_server_status', $db_server_status);
            if($db_server_status == false) {
                $this->view->setVar('database_server_message',__('Database server %database_server% connection is failed. Please contact with system administrator.',['database_server' => $db_server_name]));
            }
        }

        return true;
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
                $messages = $this->delete($id);
                $this->response->setJsonContent($messages)->send();
            }
        }
        else {
            $database = new DomainDatabase();
            $db_server_status = true;
            $db_server_name = "";
            foreach($this->default_database_servers as $default_database_server) {
                if($database->connection($default_database_server->id) == false) {
                    $db_server_status = false;
                    $db_server_name = $default_database_server->db_server_name;
                }
            }

            $this->view->setVar('database_server_status', $db_server_status);
            if($db_server_status == false) {
                $this->view->setVar('database_server_message',__('Database server %database_server% connection is failed. Please contact with system administrator.',['database_server' => $db_server_name]));
            }
        }
    }

    public function editAction($id)
    {
        $this->tag->appendTitle(__('Edit Database'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->breadcrumbs->add(__('Database List'),'database/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('database/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt("database/edit/$id/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Database List'),'database',' ');
            $redirect_url = $this->uri->url_encrypt('database');
            $post_url = $this->uri->url_encrypt("database/edit/$id");
        }
        $this->breadcrumbs->add(__('Edit Database'),'','');
        $this->view->setVar('post_url', $post_url);


        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('prefix', $this->domain_data["prefix"]);
        $this->view->setVar("id", $id);
        $this->view->setVar('domain_id', $domain_id);

        $options = array();
        $options['action'] = "edit";
        $options['id'] = $id;
        $options['domain_name'] = $domain_name;

        $domain_db_data = $this->queryBuilder->select([
            "`databases`.id",
            "`databases`.name",
            "`databases`.database_server_id",
            "`databases`.database_user_id",
            "`databases`.type",
            "database_server.host",
            "database_server.port",
            "database_users.login",
            "database_users.password",
        ])
            ->from("`databases`")
            ->innerJoin("database_users", "database_users.id = `databases`.database_user_id")
            ->innerJoin("database_server", "database_server.id = `databases`.database_server_id")
            ->where([
                'conditions' => "`databases`.id = ?1",
                'bind' => [1 => $id]
            ])
            ->andWhere([
                'conditions' => "`databases`.domain_id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();


        if(!$domain_db_data) {
            $this->view->setVar('error_content',__('Database not found'));
            $this->response->setStatusCode(404, __('Database not found'));
            $this->view->pick("errors/404");
            return true;
        }


        $options['db_data'] = $domain_db_data;
        $database_server = new DbServer();
        $database_server_list = $database_server->getDatabaseServerList();
        $options['database_server_list'] = $database_server_list;

        $database_server_settings = DatabaseServerSettings::findFirst("database_server_id = '$domain_db_data[database_server_id]'");
        $settings_param = array(
            "allow_remote_connection" => "off"
        );

        if($domain_db_data['type'] == "mysql") {
            if($database_server_settings) {
                $settings_param = json_decode($database_server_settings->params,true);
                $options['remote_connection'] = $settings_param['allow_remote_connection'];
                $this->view->setVar('remote_connection_status', $settings_param['allow_remote_connection']);
            }
            else {
                $this->view->setVar('error_content',__('Database Server not found'));
                $this->response->setStatusCode(404, __('Database Server not found'));
                $this->view->pick("errors/404");
                return true;
            }
        }

        $options['remote_hosts'] = array();
        $database_hosts = DatabaseRemoteHosts::findFirst("db_user_id = '$domain_db_data[database_user_id]'");
        if($database_hosts) {
            $old_hosts = array();
            if($database_hosts->hosts != null) {
                $old_hosts = json_decode($database_hosts->hosts,true);
            }
            $options['remote_hosts'] = $old_hosts;
        }


        $this->view->form = new DatabaseForm(null, $options);

        if (($this->request->isPost() == true)) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $database = new DomainDatabase();

            $post_data = array(
                'action'    => "update",
                'db_id'    => $id,
                'user_id'    => $domain_db_data['database_user_id'],
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'database_server' => $domain_db_data['database_server_id'],
                'database_server_type' => DB_TYPES_ARR[$domain_db_data['type']],
                'database_name' => $domain_db_data['name'],
                'database_username' => $this->request->getPost('database_username'),
                'password' => $this->request->getPost('password'),
                'old_username' => $domain_db_data['login'],
                'remote_hosts' => $this->request->getPost('remote_hosts'),
                'allow_remote_connection' => $settings_param['allow_remote_connection'],
            );

            $database->setPosts($post_data);
            $status = $database->updateDatabase();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($database->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax()) {
                $this->response->setJsonContent($messages)->send();
            }
            else {
                if($status) {
                    $this->flashSession->success(__('Database is updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        else {
            $database = new DomainDatabase();
            $db_server_status = true;
            $db_server_name = "";
            foreach($this->default_database_servers as $default_database_server) {
                if($database->connection($default_database_server->id) == false) {
                    $db_server_status = false;
                    $db_server_name = $default_database_server->db_server_name;
                }
            }

            $this->view->setVar('database_server_status', $db_server_status);
            if($db_server_status == false) {
                $this->view->setVar('database_server_message',__('Database server %database_server% connection is failed. Please contact with system administrator.',['database_server' => $db_server_name]));
            }
        }
        return true;
    }

    /**
     * @param $db_id
     * @return false|void
     */
    public function dbAdminAction($db_id)
    {
        $this->view->disable();
        $database = Databases::findFirstById($db_id);

        if(!$database) {
            return false;
        }
        $db_user = DatabaseUsers::findFirstById($database->database_user_id);
        $db_server = DatabaseServer::findFirstById($database->database_server_id);
        $sys_ip_address = IpAddress::findFirst("is_default = '1'");

        if($database->type == MYSQL) {
            echo '  <form style="display:none" class="login" target="_top" name="login_form" action="http://'.$sys_ip_address->ip_address.'/dbmyadmin/sso.php" id="login_form" method="post">
                <input type="hidden" class="textfield" value="'.$db_user->login.'" id="input_username" name="user">
                <input type="hidden" class="textfield" value="'.trim($this->crypto->bdecode($db_user->password)).'" id="input_password" name="password">
                <input type="hidden" class="textfield" value="'.$db_server->host.'" id="input_host" name="host">
                <input type="hidden" class="textfield" value="'.$db_server->port.'" id="input_port" name="port">
                <input type="hidden" class="textfield" value="' . USER_SQL_ADMIN . '" id="input_password" name="' .USER_SQL_ADMIN . '">
                <input type="submit" id="input_go" value="Git">
                </form>
                <script>document.getElementById("login_form").submit();</script>';
        }
        else if($database->type = MSSQL) {
            echo '<form style="display:none" class="login" name="mainForm" action="http://'.$sys_ip_address->ip_address.'/mylittleadmin/silentlogon.aspx" id="mainForm" method="post">
                <input type="hidden" value="'.$db_user->login.'" id="Login" name="Login">
                <input type="hidden" value="'.trim($this->crypto->bdecode($db_user->password)).'" id="Password" name="Password">
                <input type="hidden" value="127.0.0.1" id="ServerName" name="ServerName">
                <input type="hidden" value="sql" id="Authentication" name="Authentication">
                <input type="hidden" value="'.$database->name.'" id="Database" name="Database">
                <input type="hidden" value="'.$database->name.'" id="InitialCatalog" name="InitialCatalog">
                <input type="submit" id="input_go">
                </form>
                <script>document.getElementById("mainForm").submit();</script>';
        }
    }

    private function delete($id): array
    {
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
//            $url = $this->uri->url_encrypt('database/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
//            $url = $this->uri->url_encrypt('database');
        }

        $messages = Globals::messages();
        $messages['type'] = 'error';
        $messages['title'] = __('Failed');
        $messages['messages'] = __('Delete failed');


        $delete_database = new DomainDatabase();
        $status = $delete_database->deleteDatabase($id, $domain_id);
        if ( $status == true) {
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Delete successful');
        }
        return $messages;
    }

    public function multipleAction()
    {
//        $data = $this->user_data;
//        if(isset($this->domain_id)) {
//            $domain_id = $this->domain_id;
//            $url = $this->uri->url_encrypt('database/domain/'.$domain_id);
//        }
//        else {
//            $domain_id = $data['user']['domain_info']['domain_id'];
//            $url = $this->uri->url_encrypt('database');
//        }

        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');
            $messages = Globals::messages();
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');

            if($this->request->getPost('id') == null) {
                $messages['messages'] = __('Database not found');
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