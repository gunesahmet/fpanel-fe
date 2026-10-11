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

use FPanel\Models\DatabaseServer;
use FPanel\Models\Servers;
use FPanel\Models\SystemPackages;
use PDO;
use PDOException;

trait PostgreSQL
{
    protected function createPostgreSQLDatabase(): bool
    {
        $db_server_id = $this->getPost("database_server");
        $db_user_name = strtolower($this->getPost("database_username"));
        $db_user_password = $this->getPost("password");
        $db_name = $this->getPost("prefix")."_".$this->getPost("database_name");
        $database_server = DatabaseServer::findFirstById($db_server_id);

        if(!$database_server) {
            $this->error_messages["content"] = __("Database server not found");
            return false;
        }

        try {
            $dbh = new PDO('pgsql:dbname=postgres;host='. $database_server->host .';port='. $database_server->port .';user='. $database_server->username .';password='. trim($this->crypto->bdecode($database_server->password)));
            return $this->createPgDb($dbh, $db_name, $db_user_name, $db_user_password);
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createPostgreSQLDatabase",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
            return false;
        }
    }

    protected function createPgDb(PDO $dbh, $db_name, $db_user_name, $db_user_password): bool
    {
        try {
            if($this->createPostDb($dbh, $db_name)) {
                return $this->createPgDbUser($dbh, $db_name, $db_user_name, $db_user_password);
            }
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createPgDb",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
        }
        return true;
    }

    private function createPostDb(PDO $dbh, $db_name): bool
    {
        try {
            $dbh->exec("CREATE ROLE $db_name NOSUPERUSER NOCREATEDB NOCREATEROLE NOINHERIT;");
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createPostDb",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
            return false;
        }
        return true;
    }

    private function createPgDbUser(PDO $dbh, $db_name, $db_user_name, $db_user_password): bool
    {
        try {
            $dbh->exec("CREATE ROLE $db_user_name NOSUPERUSER NOCREATEDB NOCREATEROLE NOINHERIT LOGIN ENCRYPTED PASSWORD '$db_user_password';");
            $dbh->exec("GRANT ALL PRIVILEGES ON DATABASE $db_name TO $db_user_name;");
            $dbh->exec("CREATE DATABASE $db_name WITH OWNER = $db_user_name;");
            $dbh->exec("REVOKE ALL ON DATABASE $db_name FROM public;");
            $dbh->exec("GRANT ALL ON SCHEMA public TO $db_name WITH GRANT OPTION;");

            $errors = $dbh->errorInfo();
            if($errors[2] != null) {
                $this->error_messages['content'] = $errors[2];
                return false;
            }
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createPgDbUser",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages['content'] = $e->getMessage();
            return false;
        }
        return true;
    }

    private function updatePostgreSQLDatabaseUserSystem(Servers $server): bool
    {
        $databases = new DatabaseServer();
        $database_server_query = $databases->findFirstById($this->getPost("database_server"));
        $database_server = $database_server_query->toArray();

        $password = $this->getPost("password");
        $username = $this->getPost("database_username");

        try {
            $dbh = new PDO('pgsql:dbname=postgres;host='. $database_server['host'] .';port='. $database_server["port"] .';user='. $database_server['username'] .';password='. trim($this->crypto->bdecode($database_server['password'])) .'');
            if($this->change_username == true) {
                $dbh->exec("ALTER USER $this->old_username RENAME TO $username;");

            }
            if($this->change_password == true) {
                $dbh->exec("ALTER USER $username WITH PASSWORD '$password';");
            }

        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->updatePostgreSQLDatabaseUserSystem",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages[] = $e->getMessage();
            return false;
        }

        return true;
    }

    private function dropPostgreSQLDatabaseAndUser(): bool
    {
        $db_name = $this->getPost("prefix")."_".$this->getPost("database_name");
        $db_user_name = strtolower($this->getPost("database_username"));

        $databases = new DatabaseServer();
        $database_server = $databases->findFirstById($this->getPost("database_server"));

        try {
            $dbh = new PDO('pgsql:dbname=postgres;host='. $database_server->host .';port='. $database_server->port .';user='. $database_server->username .';password='. trim($this->crypto->bdecode($database_server->password)));
            $dbh->exec("SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '$db_name' AND pid <> pg_backend_pid();");
            $dbh->exec("DROP DATABASE $db_name");
            $dbh->exec("DROP ROLE $db_user_name;");
            $dbh->exec("DROP ROLE $db_name");
            return true;
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->dropPostgreSQLDatabaseandUser",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            return false;
        }
    }
}