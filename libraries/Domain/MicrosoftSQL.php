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

use Exception;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DomainStatistics;
use FPanel\Models\Servers;

trait MicrosoftSQL
{
    private function createMSSQLDatabase(): bool
    {
        $db_server_id = $this->getPost("database_server");
        $db_user_name = $this->getPost("database_username");
        $db_user_password = $this->getPost("password");
        $database_server = DatabaseServer::findFirstById($db_server_id);
        if(!$database_server) {
            $this->error_messages["database_server"] = __("Database server not found");
            return false;
        }
        $db_name = $this->getPost("prefix")."_".$this->getPost("database_name");
        $domain_id = $this->getPost("domain_id");
        $database_server_host = $database_server->host;
        $database_server_port = $database_server->port;
        $database_server_username = $database_server->username;
        $database_server_password = $this->crypto->bdecode($database_server->password);
        $serverName = "$database_server_host, $database_server_port"; //serverName\instanceName, portNumber (default is 1433)
        $statistics = DomainStatistics::findFirst("domain_id = '$domain_id'");
        if(!$statistics) {
            $this->error_messages["content"] = __("Domain statistics not found");
            return false;
        }
        $max_db_kb = UNLIMITED;
        if($statistics->db_max != UNLIMITED)
            $max_db_kb = $statistics->db_max * 1024;

        try {
            $connectionInfo = array( "UID"=>$database_server_username, "PWD"=>$database_server_password );
            $conn = sqlsrv_connect( $serverName, $connectionInfo);

            if (!$conn) {
                $this->error_messages['content'] = __('MicrosoftSQL Connection has failed');
                return false;
            }
            else {
                $status = $this->createMsDb($conn, $db_name, $db_user_name, $db_user_password, $max_db_kb);
                sqlsrv_close($conn);
                return $status;
            }
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createMSSQLDatabase",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function createMsDb($conn, $db_name, $db_user_name, $db_user_password, $max_db_kb): bool
    {
        try {
            if($this->createMssqlDb($conn, $db_name)) {
                return $this->createMsDbUser($conn, $db_name, $db_user_name, $db_user_password, $max_db_kb);
            }
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createMsDb",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
        }
        return true;
    }

    private function createMssqlDb($conn, $db_name): bool
    {
        try {
            $create_database = "USE [master]; CREATE DATABASE [$db_name];";
            sqlsrv_query($conn, $create_database);
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createMssqlDb",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
            return false;
        }
        return true;
    }

    private function createMsDbUser($conn, $db_name, $db_user_name, $db_user_password, $max_db_kb): bool
    {
        try {
            // Creating user
            sqlsrv_query($conn, "CREATE LOGIN $db_user_name WITH PASSWORD = '$db_user_password', CHECK_POLICY = OFF;");
            sqlsrv_query($conn, "USE [$db_name]; CREATE USER [$db_user_name] FOR LOGIN [$db_user_name];");

            // Authoriting owned db
            sqlsrv_query($conn, "USE [$db_name]; EXEC sp_addrolemember N'db_owner', N'$db_user_name';");

            // Disallowed viewing other databases
            sqlsrv_query($conn, "USE master; DENY VIEW ANY DATABASE TO [$db_user_name];");
            sqlsrv_query($conn, "USE [$db_name]; DROP USER [$db_user_name];");
            sqlsrv_query($conn, "USE master; ALTER AUTHORIZATION ON DATABASE::$db_name TO $db_user_name;");
            if($max_db_kb != UNLIMITED)
                sqlsrv_query($conn, "USE master; ALTER DATABASE [$db_name] MODIFY FILE ( NAME = N'$db_name', MAXSIZE = ". $max_db_kb."KB );");
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createMsDbUser",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages['content'] = $e->getMessage();
            return false;
        }
        return true;
    }

    public function updateMSSQLDatabaseUserSystem($old_user, Servers $server): bool
    {
        $db_server_id = $this->getPost("database_server");
        $db_user_name = $this->getPost("database_username");
        $db_user_password = $this->getPost("password");

        $database_servers = DatabaseServer::findFirstById($db_server_id);
        $database_server = $database_servers->toArray();

        $database_server_host = $database_server['host'];
        $database_server_port = $database_server['port'];
        $database_server_username = $database_server['username'];
        $database_server_password = $this->crypto->bdecode($database_server['password']);
        $serverName = "$database_server_host, $database_server_port"; //serverName\instanceName, portNumber (default is 1433)

        try {
            $connectionInfo = array( "UID"=>$database_server_username, "PWD"=>$database_server_password );
            $conn = sqlsrv_connect( $serverName, $connectionInfo);

            if (!$conn) {
                $this->error_messages['content'] = __('Connection Error');
                return false;
            }
            else {
                if($old_user != $db_user_name) {
                    $change_username = "ALTER LOGIN [$old_user] WITH NAME = [$db_user_name]";
                    sqlsrv_query($conn, $change_username);
                }
                $change_pass_database = "USE [master] ALTER LOGIN [$db_user_name] WITH PASSWORD=N'$db_user_password';";
                sqlsrv_query($conn, $change_pass_database);

                sqlsrv_close($conn);
                return true;
            }
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "DomainDatabase->updatePostgreSQLDatabaseUserSystem",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function dropMSSQLDatabaseAndUser(): bool
    {
        $db_name = $this->getPost("prefix")."_".$this->getPost("database_name");
        $db_user_name = $this->getPost("database_username");

        $database_server = DatabaseServer::findFirstById($this->getPost("database_server"));

        $database_server_password = $this->crypto->bdecode($database_server->password);
        $server_name = "$database_server->host, $database_server->port";

        try {
            $connection_info = array( "UID" => $database_server->username, "PWD" => $database_server_password );
            $conn = sqlsrv_connect( $server_name, $connection_info);

            if (!$conn) {
                return false;
            }
            else {
                // First getting user session id
                $response = sqlsrv_query($conn, "SELECT conn.session_id
FROM sys.dm_exec_sessions AS sess
JOIN sys.dm_exec_connections AS conn
   ON sess.session_id = conn.session_id
   WHERE login_name='$db_user_name';");
                $result = sqlsrv_fetch_array($response, SQLSRV_FETCH_ASSOC);
                if(!empty($result)) {
                    $session_id = $result["session_id"];
                    $this->debug->firebug("Session ID: " . $session_id, "debug");
                    sqlsrv_query($conn,"KILL $session_id");
                }
                sqlsrv_query($conn, "DROP USER $db_user_name;");
                sqlsrv_query($conn, "DROP LOGIN $db_user_name;");
                sqlsrv_query($conn, "DROP DATABASE $db_name;");
                sqlsrv_close($conn);
            }
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "DomainDatabase->dropMSSQLDatabaseandUser",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
        }
        return true;
    }
}