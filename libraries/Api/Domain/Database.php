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
 */namespace FPanel\Api\Domain;

use FPanel\Domain\DomainDatabase;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseServer as DatabaseServers;
use FPanel\Models\Domains;
use FPanel\Models\Servers;
use Phalcon\Mvc\User\Component;

class Database extends Component
{
    private $db_server_list;
    private $selected_db_server;
    private $error_messages = [];

    public function database($process, $params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $domain_name = $params['domain_name'];
        if(!$this->env->checkDomainOwner($domain_name, $client_id)) {
            return $this->error_messages['domain_name'] = "You dont have access for this domain";
        }
        switch($process) {
            case "list":
                return $this->databaseList($params);
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            default:
                return false;
        }
    }

    public function databaseList($params)
    {
        $qbuilder = new QueryBuilder();

        $error_messages = array();

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages['domain_name'] = "Domain not found";
        }

        $qbuilder->select([
            "CONCAT(domains.prefix,\"_\",`databases`.name) as name",
            "`databases`.type",
            "CONCAT(database_server.host, \":\", database_server.port) as host",
            "servers.name as server_name",
        ])
            ->from('`databases`')
            ->innerJoin('database_server','database_server.id = `databases`.database_server_id')
            ->innerJoin('servers','database_server.server_id = `servers`.id')
            ->innerJoin('domains','domains.id = `databases`.domain_id')
            ->where([
                'conditions' => "databases.domain_id = ?1",
                'bind' => [1 => $domains->id]
            ])
            ->orderAsc("`databases`.name");

        $database_list = $qbuilder->find();
        foreach ($database_list as $k => $v) {
            $database_list[$k]["type"] = $this->env->getDbTypes($v["type"]);
        }
        $database_list['status'] = true;

        return $database_list;
    }

    public function create($params)
    {
        $error_messages = array();

        $requeired_keys = [
            "domain_name",
            "database_name",
            "database_type",
            "database_server",
            "database_username",
            "database_password",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages['domain_name'] = "Domain not found";
        }
        if(!$this->getDbServerList($params['database_server'], $params['database_type'])) {
            return $error_messages['database_server'] = "Please choose valid database server";
        }

        $post_data = array(
            'domain_id' => $domains->id,
            'domain_name' => $domain_name,
            'database_server_type' => $params['database_type'],
            'database_server' => $this->selected_db_server['id'],
            'database_name' => $params['database_name'],
            'database_username' => $params['database_username'],
            'password' => $params['database_password'],
            'database_server_list' => $this->db_server_list,
        );

        $database = new DomainDatabase();
        $database->setPosts($post_data);
        $status = $database->createDatabase();
        if ($status) {
            return true;
        }
        else {
            foreach ($database->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function update($params)
    {
        $error_messages = array();
        $requeired_keys = [
            "domain_name",
            "database_name",
            "database_username",
            "database_password",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages['domain_name'] = "Domain not found";
        }
        $db_name = $params['database_name'];
        $db = Databases::findFirst("name = '$db_name'");
        if(!$db) {
            return $error_messages['database_name'] = "Database not found";
        }
        $post_data = array(
            'action' => "update",
            'db_id'    => $db->id,
            'user_id'    => $db->database_user_id,
            'domain_id' => $domains->id,
            'domain_name' => $domain_name,
            'database_server' => $db->database_server_id,
            'database_server_type' => $this->env->getDbTypes($db->type, true),
            'database_name' => $params['database_name'],
            'database_username' => $params['database_username'],
            'password' => $params['database_password'],
            'old_username' => isset($params['old_username']) ? $params['old_username'] : null,
        );

        $database = new DomainDatabase();
        $database->setPosts($post_data);

        $status = $database->updateDatabase();
        if ($status) {
            return true;
        }
        else {
            foreach ($database->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function delete($params)
    {
        $error_messages = array();
        $requeired_keys = [
            "domain_name",
            "database_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages['domain_name'] = "Domain not found";
        }
        $db_name = $params['database_name'];
        $db = Databases::findFirst("name = '$db_name'");
        if(!$db) {
            return $error_messages['database_name'] = "Database not found";
        }
        $post_data = array(
            'domain_id' => $domains->id,
            'domain_name' => $domain_name,
            'database_name' => $params['database_name'],
        );

        $database = new DomainDatabase();
        $database->setPosts($post_data);
        $database->deleteDatabase($db->id, $domains->id);
        return true;
    }

    public function getDbServerList($server_name, $type): bool
    {
        if(!empty($server_name)) {
            $servers = Servers::findFirst("name = '$server_name'");
            if(!empty($servers)) {
                $db_server = DatabaseServer::findFirst("server_id = '$servers->id' and type = '$type'");
                if($db_server) {
                    $this->db_server_list = DatabaseServers::find()->toArray();
                    $this->selected_db_server = $db_server->toArray();
                    return true;
                }
            }
        }
        return false;
    }
}