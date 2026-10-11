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
 */namespace FPanel\DatabaseServer;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\DatabaseServer as DatabaseServers;
use FPanel\Models\DatabaseServerSettings;
use FPanel\Models\Servers;
use FPanel\Models\SystemPackages;
use FPanel\Process\SqlManager;

class DatabaseServer extends Library
{
    use Connection;

    public function addDatabaseServer(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("host"),
                'field' => 'db_host',
                'message' => __('Server IP Address is required'),
            ),
            array(
                'item' => $this->getPost("port"),
                'field' => 'db_port',
                'message' => __('Port is required'),
            ),
            array(
                'item' => $this->getPost("username"),
                'field' => 'username',
                'message' => __('Username is required'),
            ),
            array(
                'item' => $this->getPost("password"),
                'field' => 'password',
                'message' => __('Password is required'),
            ),
            array(
                'item' => $this->getPost("type"),
                'field' => 'db_type',
                'message' => __('Database Server type is required'),
            ),
        );
        $validation = $this->validation->Required($required)
            ->IpOrDomain($this->getPost("host"),'db_host')
            ->InclusionIn($this->getPost("type"),'db_type',array('mysql','mssql','pgsql'),__('Please select valid server type'))
            ->Username($this->getPost("username"),'username',null,"2")
            ->Password($this->getPost("password"),'password')
            ->Numeric($this->getPost("port"),'db_port',__('Please enter valid port number'))
            ->UniqueDbServer($this->getPost("host"), $this->getPost("port"), $this->getPost("type"),'db_host')
            ->Validate();

        if($validation == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        $database_server = new DatabaseServers();
        $test_connection = $this->connection($this->getPost("type"), $this->getPost("username"), $this->getPost("password"), $this->getPost("port"), $this->getPost("host"));
        if($test_connection == false) {
            return false;
        }

        $database_server->assign(array(
            'db_server_name'    => $this->getPost("db_server_name"),
            'host'              => $this->getPost("host"),
            'port'              => $this->getPost("port"),
            'type'              => $this->getPost("type"),
            'username'          => $this->getPost("username"),
            'password'          => $this->crypto->bencode($this->getPost("password")),
            'last_error'        => 'no_error',
            'server_version'    => null,
            'status'            => $this->getPost("status"),
            'server_id'         => $this->getPost("server_id"),
        ));
        if (!$database_server->create()) {
            foreach ($database_server->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->addDatabaseServer",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function updateDatabaseServer(): bool
    {
        $id = $this->getPost("db_server_id");
        $database_server = DatabaseServers::findFirst("id = '$id'");

        $required = array(
            array(
                'item' => $this->getPost("host"),
                'field' => 'db_host',
                'message' => __('Server IP Address is required'),
            ),
            array(
                'item' => $this->getPost("port"),
                'field' => 'db_port',
                'message' => __('Port is required'),
            ),
            array(
                'item' => $this->getPost("username"),
                'field' => 'username',
                'message' => __('Username is required'),
            ),
            array(
                'item' => $this->getPost("password"),
                'field' => 'password',
                'message' => __('Password is required'),
            ),
            array(
                'item' => $this->getPost("type"),
                'field' => 'db_type',
                'message' => __('Database Server type is required'),
            ),
        );
        $validation = $this->validation->Required($required)
            ->IpOrDomain($this->getPost("host"),'db_host')
            ->InclusionIn($this->getPost("type"),'db_type',array('mysql','mssql'),__('Please select valid server type'))
            ->Username($this->getPost("username"),'username',null,"2")
            ->Password($this->getPost("password"),'password')
            ->Numeric($this->getPost("port"),'db_port',__('Please enter valid port number'))
            ->UniqueDbServer($this->getPost("host"), $this->getPost("port"), $this->getPost("type"),'db_host',null,true, $database_server->host)
            ->Validate();

        if($validation == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }


        $test_connection = $this->connection($this->getPost("type"), $this->getPost("username"), $this->getPost("password"), $this->getPost("port"), $this->getPost("host"));
        if($test_connection == false) {
            return false;
        }
        $assign = [
            'db_server_name'    => $this->getPost("db_server_name"),
            'host'              => $this->getPost("host"),
            'port'              => $this->getPost("port"),
            'type'              => $this->getPost("type"),
            'username'          => $this->getPost("username"),
            'password'          => $this->crypto->bencode($this->getPost("password")),
        ];
        $database_server->assign($assign);
        if (!$database_server->update()) {
            foreach ($database_server->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->updateDatabaseServer",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function deleteDatabaseServer(): bool
    {
        $status = false;
        $database_server = DatabaseServers::findFirstById($this->getPost("id"));
        $database_server_data = $database_server->toArray();
        if(array_key_exists('id', $database_server_data)) {
            if(!$database_server->delete()) {
                foreach($database_server->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DatabaseServer->deleteDatabaseServer",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                $status = false;
            }
            else {
                $status = true;
            }
        }
        return $status;
    }

    public function getDatabaseServerList(): array
    {
        return DatabaseServers::find()->toArray();
    }

    public function databaseServerList($server_id): array
    {
        $datatable = new Datatable();

        $datatable->select(array('database_server.id as db_server_id',
            'database_server.host',
            'database_server.port',
            'database_server.type',
            '(SELECT COUNT(*) FROM `databases` WHERE `databases`.database_server_id = database_server.id) as db_count',
            'database_server.username',
            'database_server.status',
            'database_server.id'))
            ->from("database_server")
            ->leftJoin('servers','database_server.server_id = servers.id');
        if($server_id != "1") {
            $datatable->where("database_server.server_id = '$server_id'");
        }
        $datatable->searchColumn(array('database_server.host','database_server.username','database_server.status','database_server.server_version'));

        if($server_id != "1") {
            $datatable->editColumn('database_server.host','<a href="'. "dbserver/edit/$1/server/$server_id\">$2</a>", 'database_server.id, database_server.host');
        }
        else {
            $datatable->editColumn('database_server.host','<a href="'. 'dbserver/edit/$1">$2</a>', 'database_server.id, database_server.host');
        }

        $data = $datatable->unsetColumns('db_server_id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            if ($value[6] == '1'){
                $data['aaData'][$key][5] = '<span class="label label-primary"> '. __('Active') .' </span>';
            }
            else{
                $data['aaData'][$key][5] = '<span class="label label-danger"> '. __('Passive') .' </span>';
            }
            switch($value[3]) {
                case "mysql":
                    $data['aaData'][$key][3] = '<span class="label label-primary"> MySQL </span>';
                    break;
                case "mssql":
                    $data['aaData'][$key][3] = '<span class="label label-success"> Microsoft SQL </span>';
                    break;
                case "pgsql":
                    $data['aaData'][$key][3] = '<span class="label label-default"> PostgreSQL </span>';
                    break;
            }

            if($server_id != "1") {
                $settings_button = '<a data-id="nested" class="text-success" href="'. $this->uri->url_encrypt("dbserver/settings/".$value[7].'/server/'.$server_id) .'"><i class="fa fa-cogs"></i> '. __('Settings') .'</a> ';
                $edit_button = '<a data-id="nested" class="text-success" href="'. $this->uri->url_encrypt("dbserver/edit/".$value[7].'/server/'.$server_id) .'"><i class="fa fa-edit"></i> '. __('Edit') .'</a>';
                $delete_button = ' <a class="text-danger" data-id="confirm" data-url="'. $this->uri->url_encrypt("dbserver/delete/".$value[7].'/server/'.$server_id) .'"><i class="fa fa-times"></i> '. __('Delete') . '</a>';
            }
            else {
                $settings_button = '<a data-id="nested" class="text-success" href="'. $this->uri->url_encrypt("dbserver/settings/".$value[7]) .'"><i class="fa fa-cogs"></i> '. __('Settings') .'</a> ';
                $edit_button = '<a data-id="nested" class="text-success" href="'. $this->uri->url_encrypt("dbserver/edit/".$value[7]) .'"><i class="fa fa-edit"></i> '. __('Edit') .'</a>';
                $delete_button = ' <a class="text-danger" data-id="confirm" data-url="'. $this->uri->url_encrypt("dbserver/delete/".$value[7]) .'"><i class="fa fa-times"></i> '. __('Delete') . '</a>';
            }

            if($value[4] > 0) {
                if($value[3] != "mssql")
                    $data['aaData'][$key][6] = $settings_button.$edit_button;
                else
                    $data['aaData'][$key][6] = $edit_button;
            }
            else {
                if($value[3] != "mssql")
                    $data['aaData'][$key][6] = $settings_button.$edit_button.$delete_button;
                else
                    $data['aaData'][$key][6] = $edit_button.$delete_button;
            }
            unset($data['aaData'][$key][0]);
            unset($data['aaData'][$key][7]);
            $data['aaData'][$key] = array_values($data['aaData'][$key]);


        }

        return $data;
    }

    public function databaseList($server_id): array
    {
        $datatable = new Datatable();
        $dbadmin_button = $datatable->buttons("database/dbAdmin/$1",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('DB Admin'));

        $datatable->select([
            '`databases`.id',
            '`databases`.name',
            '`databases`.type',
            'database_server.host',
            'database_server.port',
            'domains.name as domain_name'
        ])
            ->from("`databases`")
            ->innerJoin('database_users','`databases`.database_user_id = database_users.id')
            ->innerJoin('database_server','`databases`.database_server_id = database_server.id')
            ->innerJoin('domains','`databases`.domain_id = domains.id')
            ->where("`databases`.server_id = '$server_id'")
            ->searchColumn(array('`databases`.name','`databases`.type','database_server.host','domains.name'))
            ->addColumn('actions', $dbadmin_button,'`databases`.id')
            ->editColumn('database_server.host','$1:'.'$2', 'database_server.host, database_server.port')
            ->editColumn('domain_name','$1', 'domain_name')
            ->unsetColumns('database_server.port');

        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = $value[1];
            switch($value[2]) {
                case "mysql":
                    $data['aaData'][$key][1] = '<span class="label label-primary"> MySQL </span>';
                    break;
                case "mssql":
                    $data['aaData'][$key][1] = '<span class="label label-success"> Microsoft SQL </span>';
                    break;
                case "pgsql":
                    $data['aaData'][$key][1] = '<span class="label label-default"> PostgreSQL </span>';
                    break;
            }
            $data['aaData'][$key][3] = $value[5];
            $data['aaData'][$key][4] = $value[6];
            unset($data['aaData'][$key][5]);
            unset($data['aaData'][$key][6]);
        }
        return $data;
    }

    public function saveMySQLDefaultDatabaseServer($username, $password, $port, $host, string $server_id): bool
    {
        $mysql = new MySQL();
        if(!$mysql->create($username, $password, $port, $host)) {
            return false;
        }
        $database_server = new DatabaseServers();
        $database_server->assign(array(
            "id"                => $this->env->createUuid(),
            'db_server_name'    => $this->getPost("db_server_name"),
            'host'              => $host,
            'port'              => $port,
            'type'              => 'mysql',
            'username'          => $username,
            'password'          => $this->crypto->bencode($password),
            'status'            => ACTIVE,
            'server_id'         => $server_id
        ));
        if(!$database_server->create()) {
            foreach($database_server->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->saveMySQLDefaultDatabaseServer",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return $this->saveMySQLSettings($database_server->id, $server_id);
    }

    public function saveMSSQLDefaultDatabaseServer($username, $password, $port, $host, string $server_id): bool
    {
        $mssql = new MicrosoftSQL();
        $fpanel_admin_status = $mssql->create($username, $password, $host);
        if($fpanel_admin_status) {
            $user = USER_SQL_ADMIN;
            $password = PANEL_SQL_PASS;
        }
        else {
            $user = $username;
        }
        $database_server = new DatabaseServers();
        $database_server->assign(array(
            "id"                => $this->env->createUuid(),
            'db_server_name'    => $this->getPost("db_server_name"),
            'host'              => $host,
            'port'              => $port,
            'type'              => 'mssql',
            'username'          => $user,
            'password'          => $this->crypto->bencode($password),
            'status'            => ACTIVE,
            'server_id'         => $server_id
        ));
        if(!$database_server->create()) {
            foreach($database_server->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->saveMSSQLDefaultDatabaseServer DatabaseServers",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        $database_server_settings = new DatabaseServerSettings();
        $database_server_settings->assign(array(
            'params'        => json_encode([]),
            'database_server_id' => $database_server->id
        ));
        if(!$database_server_settings->create()) {
            foreach($database_server_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->saveMSSQLDefaultDatabaseServer DatabaseServerSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function savePostgreSQLDefaultDatabaseServer($username, $password, $port, $host, string $server_id): bool
    {
        $pgsql = new PostgreSQL();
        if(!$pgsql->create($username, $password, $port, $host)) {
            return false;
        }
        $database_server = new DatabaseServers();
        $database_server->assign(array(
            "id"                => $this->env->createUuid(),
            'db_server_name'    => $this->getPost("db_server_name"),
            'host'              => $host,
            'port'              => $port,
            'type'              => 'pgsql',
            'username'          => $username,
            'password'          => $this->crypto->bencode($password),
            'status'            => ACTIVE,
            'server_id'         => $server_id
        ));
        if(!$database_server->create()) {
            foreach($database_server->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->savePostgreSQLDefaultDatabaseServer DatabaseServers",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        $database_server_settings = new DatabaseServerSettings();
        $database_server_settings->assign(array(
            'params' => json_encode([]),
            'database_server_id' => $database_server->id
        ));
        if(!$database_server_settings->create()) {
            foreach($database_server_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->savePostgreSQLDefaultDatabaseServer DatabaseServerSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function saveMySQLSettings($db_server_id, $server_id): bool
    {
        $server = Servers::findFirstById($server_id);
        $os_type = (strpos(strtolower($server->os), WINDOWS) !== false ? WINDOWS : LINUX);

        if($os_type == WINDOWS) {
            $error_logs = "c:/fpanel/mysql/var/error.log";
        }
        else {
            $error_logs = "/var/log/mysql/error.log";
        }

        $mysql_settings = new DatabaseServerSettings();

        $params = array(
            'allow_remote_connection' => 'off',
            'buffer_pool_size' => 'default',
            'log_file_size' => 'default',
            'file_per_table' => 'default',
            'flush_method' => 'default',
            'log_error' => $error_logs,
            'slow_query_log' => 'default',
            'slow_query_log_file' => '',
            'tmp_table_size' => 'default',
            'max_heap_table_size' => 'default',
            'query_cache_type' => 'default',
            'query_cache_size' => '16',
            'max_connections' => 'default',
            'thread_cache_size' => 'default',
            'table_open_cache' => 'default',
            'open_files_limit' => 'default',
        );

        $mysql_settings->assign(array(
            'params'        => json_encode($params),
            'database_server_id' => $db_server_id
        ));
        if(!$mysql_settings->create()) {
            foreach($mysql_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DatabaseServer->saveMySQLSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function settings($id): bool
    {
        $database_server = DatabaseServers::findFirstById($id);
        if(!$database_server) {
            $this->error_messages['content'] = __('Database Server not found');
            return false;
        }

        $server = Servers::findFirstById($database_server->server_id);
        $os_type = (strpos(strtolower($server->os), WINDOWS) !== false ? WINDOWS : LINUX);


        if($this->validateSettings($os_type) == false)
            return false;

        $database_server_settings = DatabaseServerSettings::findFirst("database_server_id = '$id'");
        $buffer_pool_size = "default";
        if($this->getPost("buffer_pool_size") != null)
            $buffer_pool_size = $this->getPost("buffer_pool_size");
        $log_file_size = "default";
        if($this->getPost("log_file_size") != null)
            $log_file_size = $this->getPost("log_file_size");

        if($os_type != LINUX) {
            $error_logs = "c:/databases/logs/error.log";
        }
        else {
            $error_logs = "/var/log/mysql/error.log";
        }

        if($this->getPost("log_error") != null)
            $error_logs = $this->getPost("log_error");

        $slow_query_log_file = "default";
        if($this->has("slow_query_log_file") && ($this->getPost("slow_query_log_file")))
            $slow_query_log_file = $this->getPost("slow_query_log_file");

        $tmp_table_size = "default";
        if($this->getPost("tmp_table_size") != null)
            $tmp_table_size = $this->getPost("tmp_table_size");

        $max_heap_table_size = "default";
        if($this->getPost("max_heap_table_size") != null)
            $max_heap_table_size = $this->getPost("max_heap_table_size");

        $query_cache_size = "default";
        if($this->getPost("query_cache_size") != null)
            $query_cache_size = $this->getPost("query_cache_size");

        $max_connections = "default";
        if($this->getPost("max_connections") != null)
            $max_connections = $this->getPost("max_connections");

        $thread_cache_size = "default";
        if($this->getPost("thread_cache_size") != null)
            $thread_cache_size = $this->getPost("thread_cache_size");

        $table_open_cache = "default";
        if($this->getPost("table_open_cache") != null)
            $table_open_cache = $this->getPost("table_open_cache");

        $open_files_limit = "default";
        if($this->getPost("open_files_limit") != null)
            $open_files_limit = $this->getPost("open_files_limit");

        $params = array(
            'allow_remote_connection' => $this->getPost("allow_remote_connection"),
            'buffer_pool_size' => $buffer_pool_size,
            'log_file_size' => $log_file_size,
            'file_per_table' => $this->getPost("file_per_table"),
            'flush_method' => $this->getPost("flush_method"),
            'log_error' => $error_logs,
            'slow_query_log' => $this->getPost("slow_query_log"),
            'slow_query_log_file' => $slow_query_log_file,
            'tmp_table_size' => $tmp_table_size,
            'max_heap_table_size' => $max_heap_table_size,
            'query_cache_type' => $this->getPost("query_cache_type"),
            'query_cache_size' => $query_cache_size,
            'max_connections' => $max_connections,
            'thread_cache_size' => $thread_cache_size,
            'table_open_cache' => $table_open_cache,
            'open_files_limit' => $open_files_limit,
        );

        if($database_server_settings) {
            $database_server_settings->assign(array(
                'params' => json_encode($params),
                'database_server_id' => $id,
            ));
            if(!$database_server_settings->update()) {
                foreach($database_server_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "DatabaseServer->settings DatabaseServerSettings update",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $database_server_settings = new DatabaseServerSettings();
            $database_server_settings->assign(array(
                'params' => json_encode($params),
                'database_server_id' => $id,
            ));
            if(!$database_server_settings->create()) {
                foreach($database_server_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "DatabaseServer->settings DatabaseServerSettings create",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        $this->settingsCurl($database_server->server_id, $params);
        return true;
    }

    private function settingsCurl($server_id, $data)
    {
        $params = array(
            'remote-connection' => $data['allow_remote_connection'],
            'buffer-pool-size' => $data['buffer_pool_size'],
            'log-file-size' => $data['log_file_size'],
            'file-per-table' => $data['file_per_table'],
            'flush-method' => $data['flush_method'],
            'log-error' => $data['log_error'],
            'slow-query-log' => $data['slow_query_log'],
            'slow-query-log-file' => $data['slow_query_log_file'],
            'tmp-table-size' => $data['tmp_table_size'],
            'max-heap-table-size' => $data['max_heap_table_size'],
            'query-cache-type' => $data['query_cache_type'],
            'query-cache-size' => $data['query_cache_size'],
            'max-connection' => $data['max_connections'],
            'thread-cache-size' => $data['thread_cache_size'],
            'table-open-cache' => $data['table_open_cache'],
            'open-file-limit' => $data['open_files_limit'],
        );
        foreach ($params as $key => $value) {
            if($value == null)
                unset($params[$key]);
        }
        $databasemng = new SqlManager();
        if($server_id != "1") {
            $databasemng->setServerId($server_id);
        }
        $databasemng->settings($params);
    }

    private function validateSettings($os_type): bool
    {
        $mysql_8 = false;
        if($os_type == LINUX) {
            if(strpos($this->system_packages["DATABASE_SERVERS"], "mysql") !== false) {
                $package = SystemPackages::findFirst("package_name = 'mysql-community-server'");
                if($package) {
                    if(strpos($package->version, "8.") !== false) {
                        $mysql_8 = true;
                    }
                }
                else {
                    $this->error_messages["mysql"] = __("Undefined mysql version");
                    return false;
                }
            }
        }
        else {
            if(strpos($this->system_packages["DATABASE_SERVERS"], "mysql") !== false) {
                $package = SystemPackages::findFirst("package_name LIKE 'MySQL Server%'");
                if($package) {
                    if(strpos($package->version, "8.") !== false) {
                        $mysql_8 = true;
                    }
                }
                else {
                    $this->error_messages["mysql"] = __("Undefined mysql version");
                    return false;
                }
            }
        }

        $required = array(
            array(
                'item' => $this->getPost("allow_remote_connection"),
                'field' => 'allow_remote_connection',
                'message' => __('Allow Remote Connection is required')
            ),
            array(
                'item' => $this->getPost("file_per_table"),
                'field' => 'file_per_table',
                'message' => __('File per table is required')
            ),
            array(
                'item' => $this->getPost("flush_method"),
                'field' => 'flush_method',
                'message' => __('Flush method is required')
            ),
            array(
                'item' => $this->getPost("slow_query_log"),
                'field' => 'slow_query_log',
                'message' => __('Slow query log is required')
            ),
        );
        if(!$mysql_8) {
            $required[] = array(
                'item' => $this->getPost("query_cache_type"),
                'field' => 'query_cache_type',
                'message' => __('Query Cache Type is required')
            );
        }
        if($this->has("slow_query_log") && ($this->getPost("slow_query_log") == "on")) {
            $required[] = array(
                'item' => $this->getPost("slow_query_log_file"),
                'field' => 'slow_query_log_file',
                'message' => __('Log file path is required for slow query')
            );
        }
        $this->validation->Required($required);
        if($os_type == LINUX) {
            $flush_methods = array(
                'default',
                'fsync',
                'O_DSYNC',
                'O_DIRECT',
                'littlesync',
                'nosync',
            );
        }
        else {
            $flush_methods = array(
                'default',
                'async_unbuffered',
                'normal',
                'unbuffered',
            );
        }

        $this->validation->InclusionIn($this->getPost("allow_remote_connection"),'allow_remote_connection',array('on','off'),__('Please select valid option for allow remote connection.'))
            ->InclusionIn($this->getPost("file_per_table"),'file_per_table',array('default','on','off'),__('Please select valid option for File per table.'))
            ->InclusionIn($this->getPost("slow_query_log"),'slow_query_log',array('default','on','off'),__('Please select valid option for Slow query log.'))
//            ->InclusionIn($this->getPost("slow_query_log"),'slow_query_log',array('default','on','off','demand'),__('Please select valid option for Slow query log.'))
            ->InclusionIn($this->getPost("flush_method"),'flush_method', $flush_methods,__('Please select valid option for flush method.'));


        if($this->getPost("buffer_pool_size") != null)
            $this->validation->Numeric($this->getPost("buffer_pool_size"),'buffer_pool_size',__('Please enter valid value for buffer pool size'));

        if($this->getPost("log_file_size") != null)
            $this->validation->Numeric($this->getPost("log_file_size"),'log_file_size',__('Please enter valid value for log file size'));

        if($this->getPost("tmp_table_size") != null)
            $this->validation->Numeric($this->getPost("tmp_table_size"),'tmp_table_size',__('Please enter valid value for tmp table size'));

        if($this->getPost("max_heap_table_size") != null)
            $this->validation->Numeric($this->getPost("max_heap_table_size"),'max_heap_table_size',__('Please enter valid value for heap table size'));

        if(!$mysql_8) {
            if($this->getPost("query_cache_size") != null)
                $this->validation->Numeric($this->getPost("query_cache_size"),'query_cache_size',__('Please enter valid value for query cache size'));
        }

        if($this->getPost("max_connections") != null)
            $this->validation->Numeric($this->getPost("max_connections"),'max_connections',__('Please enter valid value for max connections'));

        if($this->getPost("thread_cache_size") != null)
            $this->validation->Numeric($this->getPost("thread_cache_size"),'thread_cache_size',__('Please enter valid value for thread cache size'));

        if($this->getPost("table_open_cache") != null)
            $this->validation->Numeric($this->getPost("table_open_cache"),'table_open_cache',__('Please enter valid value for table open cache'));

        if($this->getPost("open_files_limit") != null)
            $this->validation->Numeric($this->getPost("open_files_limit"),'open_files_limit',__('Please enter valid value for open file limit'));

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

}