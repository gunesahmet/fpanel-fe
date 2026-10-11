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
 */namespace FPanel\Api\System;

use FPanel\Environments\QueryBuilder;
use FPanel\Models\DatabaseServer as SystemDatabaseServer;
use Phalcon\Mvc\User\Component;

class DatabaseServer extends Component
{
    public function init($process, $params)
    {
        switch($process[2]) {
            case "list":
                return $this->serverList();
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    private function serverList(): array
    {
        $qbuilder = new QueryBuilder();
        $qbuilder->select([
            "database_server.id",
            "database_server.host",
            "database_server.port",
            "database_server.type",
            "(SELECT COUNT(*) FROM `databases` WHERE `databases`.database_server_id = database_server.id) as db_count",
            "database_server.username",
            "database_server.status",
        ])
            ->from("database_server")
            ->leftJoin('servers','database_server.server_id = servers.id');

        $database_list = $qbuilder->find();

        $list = array();
        foreach($database_list as $server) {
            $list[] = array(
                'host' => $server['host'],
                'port' => $server['port'],
                'type' => $server['type'],
                'db_count' => $server['db_count'],
                'username' => $server['username'],
            );
        }

        $list['status'] = "1";
        return $list;
    }

    private function create($params)
    {
        $error_messages = array();
        $post_data = array(
            'db_server_name'        => $params['server_name'],
            'host'                  => $params['host'],
            'password'              => $params['password'],
            'port'                  => $params['port'],
            'type'                  => $params['type'],
            'username'              => $params['username'],
            'server_id'             => "1",
            'status'                => '1',
        );
        $server = new \FPanel\DatabaseServer\DatabaseServer();
        $server->setPosts($post_data);
        $server_create = $server->addDatabaseServer();
        if( $server_create == true) {
            return "Success";
        }
        else {
            foreach ($server->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    private function update($params)
    {
        $error_messages = array();
        $database_servers = SystemDatabaseServer::findFirst("db_server_name = '$params[server_name]'");
        if(!$database_servers) {
            return $error_messages['server_name'] = "This server doesnt exist";
        }

        $post_data = array(
            'db_server_id'          => $database_servers->id,
            'db_server_name'        => $params['server_name'],
            'host'                  => $params['host'],
            'password'              => $params['password'],
            'port'                  => $params['port'],
            'type'                  => $params['type'],
            'username'              => $params['username'],
            'server_id'             => "1",
            'status'                => '1',
        );
        $server = new \FPanel\DatabaseServer\DatabaseServer();
        $server->setPosts($post_data);
        $server_update = $server->updateDatabaseServer();

        if( $server_update == true) {
            return "Success";
        }
        else {
            foreach ($server->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    private function delete($params): string
    {
        $error_messages = array();
        $database_servers = SystemDatabaseServer::findFirst("db_server_name = '$params[server_name]'");
        if(!$database_servers) {
            return $error_messages['server_name'] = "This server doesnt exist";
        }

        $server = new \FPanel\DatabaseServer\DatabaseServer();
        $server->setPosts(array(
            'id'    => $database_servers->id,
        ));
        $status = $server->deleteDatabaseServer();

        if( $status == true) {
            return "Success";
        }
        else {
            return $error_messages['server_name'] = "This server doesnt exist";
        }
    }
}