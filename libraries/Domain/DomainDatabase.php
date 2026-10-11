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
 */namespace FPanel\Domain;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\DatabaseRemoteHosts;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseUsers;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\Models\Servers;
use FPanel\Models\SystemPackages;
use PDO;
use PDOException;

/**
 * Class DomainDatabase
 * @package FPanel\Database
 */
class DomainDatabase extends Library
{
    use Mysql, PostgreSQL, MicrosoftSQL, DatabaseConnection, DatabasePrivileges;
    /**
     * @array
     */
    protected $change_username = false;
    protected $change_password = false;
    protected $old_username;
    protected $old_password;
    protected $deleted_database_type;

    public function createDatabase($restore = false): bool
    {
        $this->db->begin();
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }

            if(!$restore) {
                if(!$this->validate()) {
                    $this->db->rollback();
                    return false;
                }
            }

            if(!$this->connection($this->getPost("database_server"))) {
                $this->db->rollback();
                return false;
            }
            $status = false;
            $db_type = $this->getPost("database_server_type");
            switch($db_type) {
                case "mysql":
                case "maria":
                    $status = $this->createMySQLDatabase();
                    break;
                case "pgsql":
                    $status = $this->createPostgreSQLDatabase();
                    break;
                case "mssql":
                    $status = $this->createMSSQLDatabase();
                    break;
            }
            if($status) {
                $db_user_id = $this->saveDatabaseUser($db_type);
                if($db_user_id) {
                    $status = $this->saveDatabase($db_user_id);
                    if($status) {
                        $this->db->commit();
                        return true;
                    }
                }
                else {
                    $this->db->rollback();
                    $this->deleteSysDatabaseUser($db_user_id);
                    switch($db_type) {
                        case "mysql":
                        case "maria":
                            $this->dropMySQLDatabaseAndUser($db_user_id);
                            break;
                        case "pgsql":
                            $this->dropPostgreSQLDatabaseAndUser();
                            break;
                        case "mssql":
                            $this->dropMSSQLDatabaseAndUser();
                            break;
                    }
                }
            }
            return false;
        }
        catch (\Exception $e) {
            $this->db->rollback();
            $this->log->error([
                "Module" => "Webhosting->createDatabase:exception",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getTrace();
//            $this->error_messages["line"] = [
//                "file" => __FILE__,
//                "class" => __CLASS__,
//                "function" => __FUNCTION__,
//                "method" => __METHOD__,
//                "line" => __LINE__,
//            ];
            return false;
        }
    }

    private function saveDatabase($db_user_id): bool
    {
        $db_server_id = $this->getPost("database_server");
        $domain_id = $this->getPost("domain_id");
        $db_name = $this->getPost("database_name");
        $db_type = $this->getPost("database_server_type");

        $database_save = new Databases();
        $database_server = DatabaseServer::findFirstById($db_server_id);
        $database_save->assign([
            "id"                    => $this->env->createUuid(),
            'name'                  => $db_name,
            'type'                  => DB_TYPES[$db_type],
            'domain_id'             => $domain_id,
            'database_server_id'    => $db_server_id,
            'database_user_id'      => $db_user_id,
            'server_id'             => $database_server->server_id,
        ]);

        if (!$database_save->create()) {
            foreach ($database_save->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DomainDatabase->saveDatabase",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function saveDatabaseUser($db_type)
    {
        $login = $this->getPost("database_username");
        if($db_type == "pgsql")
            $login = strtolower($login);

        $password = $this->getPost("password");
        $database_user_save = new DatabaseUsers();
        $database_user_save->assign(array(
            "id"        => $this->env->createUuid(),
            'login'     => $login,
            'password'  => $this->crypto->bencode($password),
        ));

        if ($database_user_save->create()) {
            return $database_user_save->id;
        }
        else {
            foreach ($database_user_save->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DomainDatabase->saveDatabaseUser",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
        }
        return false;
    }

    public function deleteDatabase($id, $domain_id): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            $database = Databases::findFirst("id = '$id' AND domain_id = '$domain_id'");
            if(!$database) {
                $this->error_messages["content"] = __("Database not found");
                return false;
            }
            $db_users = DatabaseUsers::findFirstById($database->database_user_id);
            if(!$db_users) {
                $this->error_messages["content"] = __("Database user not found");
                return false;
            }

            $status = false;
            $this->setPost("database_username", $db_users->login);
            $this->setPost("database_name", $database->name);
            $this->setPost("database_server", $database->database_server_id);
            $this->setPost("prefix", $database->getDomains()->prefix);
            switch($database->type) {
                case MYSQL:
                case MARIADB:
                    $status = $this->dropMySQLDatabaseAndUser($database->database_user_id);
                    break;
                case PGSQL:
                    $status = $this->dropPostgreSQLDatabaseAndUser();
                    break;
                case MSSQL:
                    $status = $this->dropMSSQLDatabaseAndUser();
                    break;
            }
            $this->deleteSysDatabase($id);
            $this->deleteSysDatabaseUser($database->database_user_id);
            if(!$status) {
                return false;
            }
            return true;
        }
        catch (\Exception $e) {
            $this->db->rollback();
            $this->log->error([
                "Module" => "Webhosting->deleteDatabase:exception",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getTrace();
            return false;
        }
    }

    private function deleteSysDatabase($id)
    {
        $database = Databases::findFirstById($id);
        if($database) {
            $database_type = $database->type;
            $this->deleted_database_type = $database_type;
            if(!$database->delete()) {
                foreach($database->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainDatabase->deleteSysDatabase",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
    }

    private function deleteSysDatabaseUser($db_user_id): bool
    {
        $db_user = DatabaseUsers::findFirstById($db_user_id);
        if(!$db_user) {
            $this->error_messages["content"] = __("User not found");
            return false;
        }
        $db_user_remote_host = DatabaseRemoteHosts::findFirst("db_user_id = '$db_user_id'");
        if($db_user_remote_host) {
            if(!$db_user_remote_host->delete()) {
                foreach($db_user_remote_host->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainDatabase->deleteSysDatabaseUser DatabaseRemoteHosts",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }

        if(!$db_user->delete()) {
            foreach($db_user->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            $this->log->error([
                "Module" => "DomainDatabase->deleteSysDatabaseUser DatabaseUsers",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
        }
        return true;
    }

    public function updateDatabase(): bool
    {
        $this->db->begin();
        try {
            if(!$this->validate(true)) {
                $this->db->rollback();
                return false;
            }
            if(!$this->connection($this->getPost("database_server"))) {
                $this->db->rollback();
                return false;
            }
            $database = Databases::findFirstById($this->getPost("db_id"));
            if($database) {
                $db_uname_id = $database->database_user_id;
                $database_users = DatabaseUsers::findFirstById($db_uname_id);
                $old_user = $database_users->login;
            }
            else {
                $this->error_messages['content'] =  __('Database not found');
                $this->db->rollback();
                return false;
            }

            $db_type = $this->getPost("database_server_type");
            $this->setPost("prefix", $database->getDomains()->prefix);

            if(!$this->updateDatabaseUser()) {
                $this->db->rollback();
                return false;
            }
            switch($db_type) {
                case "mysql":
                case "maria":
                    $status = $this->updateMySQLDatabaseUserSystem($database->getServers());
                    break;
                case "pgsql":
                    $status = $this->updatePostgreSQLDatabaseUserSystem($database->getServers());
                    break;
                case "mssql":
                    $status = $this->updateMSSQLDatabaseUserSystem($old_user, $database->getServers());
                    break;
                default:
                    $status = false;
                    $this->error_messages["database_server_type"] == __("Please select correct database type");
            }
            if(!$status) {
                $this->db->rollback();
                return false;
            }
            $this->db->commit();
            return true;
        }
        catch (\Exception $e) {
            $this->db->rollback();
            $this->log->error([
                "Module" => "Webhosting->updateDatabase:exception",
                "data" => $this->getPost(),
                "exception" => $e->getTrace(),
            ]);
            $this->error_messages["exception"] = $e->getTrace();
            return false;
        }
    }

    public function updateDatabaseUser(): bool
    {
        $database_user = DatabaseUsers::findFirstById($this->getPost("user_id"));
        if($database_user) {
            $database_user_data = $database_user->toArray();
            $old_username = $database_user_data['login'];
            $old_password = trim($this->crypto->bdecode($database_user_data['password']));
            $this->old_username = $old_username;
            $this->old_password = $old_password;
            if (!empty($this->getPost("database_username"))) {
                if($old_username != $this->getPost("database_username")) {
                    $this->change_username = true;
                    $database_user->login = $this->getPost("database_username");
                    if (!$database_user->update()) {
                        foreach ($database_user->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "DomainDatabase->updateDatabaseUser DatabaseUsers",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                }
            }
            if (!empty($this->getPost("password"))) {
                if($old_password != $this->getPost("password")) {
                    $this->change_password = true;
                    $database_user->password = $this->crypto->bencode($this->getPost("password"));
                    if (!$database_user->update()) {
                        foreach ($database_user->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "DomainDatabase->updateDatabaseUser DatabaseUsers2",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function databaseList($domain_id = null, $nested = false): array
    {
        $datatable = new Datatable();
        if($nested) {
            $delete_button = $datatable->buttons("database/delete/$1/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("database/edit/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
            $dbadmin_button = $datatable->buttons("database/dbAdmin/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('DB Admin'),' target="_blank"','data-id="new"');
        }
        else {
            $delete_button = $datatable->buttons('database/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('database/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
            $dbadmin_button = $datatable->buttons("database/dbAdmin/$1",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('DB Admin'),' target="_blank"','data-id="new"');
        }

        $datatable->select(array(
            '`databases`.id',
            'CONCAT(domains.prefix, "_", `databases`.name) as name',
            '`databases`.type',
            'database_server.host',
            'database_server.port',
            'database_users.login',
        ))
            ->from("`databases`")
            ->innerJoin('database_users','`databases`.database_user_id = database_users.id')
            ->innerJoin('database_server','`databases`.database_server_id = database_server.id')
            ->innerJoin('domains','`databases`.domain_id = domains.id')
            ->where("`databases`.domain_id = '$domain_id'")
            ->searchColumn(array('`databases`.name','`databases`.type','database_server.host','database_users.login'))
            ->addColumn('actions', $edit_button.$delete_button.$dbadmin_button,'`databases`.id')
            ->editColumn('database_server.host','$1:'.'$2', 'database_server.host, database_server.port')
            ->unsetColumns('database_server.port');

        if($nested) {
            $datatable->editColumn('name','<a href="'. "database/edit/$1/domain/$domain_id\">$2</a>", '`databases`.id, name');
        }
        else {
            $datatable->editColumn('name','<a href="'. 'database/edit/$1">$2</a>', '`databases`.id, name');
        }

        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = '<input type="checkbox" class="checkbox-blue" value="'. $value[0] .'" name="databases[]">';
            switch($value[2]) {
                case MYSQL:
                    $data['aaData'][$key][2] = '<span class="label label-primary"> MySQL </span>';
                    break;
                case MSSQL:
                    $data['aaData'][$key][2] = '<span class="label label-success"> Microsoft SQL </span>';
                    break;
                case PGSQL:
                    $data['aaData'][$key][2] = '<span class="label label-default"> PostgreSQL </span>';
                    break;
            }
            $data['aaData'][$key][4] = $this->env->mask($value[5]);
            $data['aaData'][$key][5] = $value[6];
            unset($data['aaData'][$key][6]);
        }
        return $data;
    }

    public function validate($update = false, $restore = false): bool
    {
        $required = array(
            array(
                'field' => 'database_username',
                'item' => $this->getPost("database_username"),
                'message' => __('Database username is required')
            ),
            array(
                'field' => 'password',
                'item' => $this->getPost("password"),
                'message' => __('Database user password is required')
            )
        );
        if(!$update) {
            $db_server_list = array();
            if(!$restore) {
                foreach($this->getPost("database_server_list") as $db_server) {
                    $db_server_list[] = $db_server['id'];
                }
            }

            $db_name = str_replace($this->getPost("prefix")."_", "", $this->getPost("database_name"));

            $required[] = array(
                'field' => 'database_server_type',
                'item' => $this->getPost("database_server_type"),
                'message' => __('Database Type is required.')
            );
            $required[] = array(
                'field' => 'database_server',
                'item' => $this->getPost("database_server"),
                'message' => __('Database Server is required.')
            );
            $required[] = array(
                'field' => 'database_name',
                'item' => $this->getPost("database_name"),
                'message' => __('Database Name is required')
            );

            $this->validation->Required($required);

            if(!$restore) {
                $this->validation->InclusionIn($this->getPost("database_server"),'database_server', $db_server_list);
            }

            $this->validation->MySQLDatabaseName($db_name,'database_name',__('Please enter valid database name'));

            $this->validation->DatabaseUser($this->getPost("database_username"),'database_username')
                ->CheckBlackList($this->getPost("database_username"),'database_username','db_username',false,__('This username can\'t be use.'));

            $this->validation->Password($this->getPost("password"),'password')
                ->UniqueDatabaseName($db_name,'database_name', DB_TYPES[$this->getPost("database_server_type")], $this->getPost("domain_id"))
                ->UniqueDatabaseUsername($this->getPost("database_username"),'database_username')
                ->CheckDatabaseServers($this->getPost("database_server"),'database_server');
        }
        else {
            $this->validation->Required($required)
                ->DatabaseUser($this->getPost("database_username"),'database_username')
                ->Password($this->getPost("password"),'password')
                ->UniqueDatabaseUsername($this->getPost("database_username"),'database_username',null,true, $this->getPost("old_username"))
                ->CheckBlackList($this->getPost("database_username"),'database_username','db_username',false,__('This username can\'t be use.'));
        }

        $status = $this->validation->Validate();
        if(!$status) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;

    }

    private function remoteHostAllowed($database_user_id, $hosts): bool
    {
        $db_remote_hosts = DatabaseRemoteHosts::findFirst("db_user_id = '$database_user_id'");
        if($db_remote_hosts) {
            $assign = array();
            foreach($hosts as $host) {
                $assign[] = $host;
            }
            $db_remote_hosts->assign(array(
                'hosts' => json_encode($assign)
            ));
            if (!$db_remote_hosts->update()) {
                foreach ($db_remote_hosts->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "DomainDatabase->remoteHostAllowed DatabaseRemoteHosts update",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $db_remote_hosts = new DatabaseRemoteHosts();
            $hosts = array();
            $db_remote_hosts->assign(array(
                'db_user_id' => $database_user_id,
                'hosts' => json_encode($hosts)
            ));
            if (!$db_remote_hosts->create()) {
                foreach ($db_remote_hosts->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "DomainDatabase->remoteHostAllowed DatabaseRemoteHosts create",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

}