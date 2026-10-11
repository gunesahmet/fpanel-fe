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
use FPanel\Models\DatabaseRemoteHosts;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseUsers;
use FPanel\Models\Servers;
use FPanel\Models\SystemPackages;
use PDO;
use PDOException;

trait Mysql
{
    protected function createMySQLDatabase(): bool
    {
        if(strpos($this->system_packages["DATABASE_SERVERS"], "mysql") !== false) {
            if(PANEL_OS_TYPE == LINUX) {
                $package = SystemPackages::findFirst("package_name = 'mysql-community-server'");
            }
            else {
                $package = SystemPackages::findFirst("package_name LIKE '%MySQL Server 8%'");
            }
            if($package) {
                if(strpos($package->version, "8.") !== false) {
                    $mysql_8 = true;
                }
            }
        }

        return $this->createMySQLDb();
    }

    protected function createMySQLDb(): bool
    {
        $db_server_id = $this->getPost("database_server");
        $db_name = $this->getPost("prefix")."_".$this->getPost("database_name");
        $db_user_name = $this->getPost("database_username");
        $db_user_password = $this->getPost("password");
        $database_server = DatabaseServer::findFirstById($db_server_id);

        if(!$database_server) {
            $this->error_messages["content"] = __("Database server not found");
            return false;
        }
        if($database_server->host == "localhost")
            $host = "127.0.0.1";
        else
            $host = $database_server->host;

        $dbh = new PDO("mysql:host=$host:$database_server->port", $database_server->username, trim($this->crypto->bdecode($database_server->password)));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        try {
            $dbh->exec("SET GLOBAL validate_password.length = 6;");
            $dbh->exec("SET GLOBAL validate_password.policy=LOW;");
        }
        catch (Exception $e) {

        }

        try {
            if($this->createDb($dbh, $db_name)) {
                return $this->createDbUser($dbh, $db_name, $db_user_name, $db_user_password, $host, true);
            }
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createMySQLDb",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
        }
        return false;
    }

    private function createDb(PDO $dbh, $db_name): bool
    {
        try {
            $dbh->exec("CREATE DATABASE $db_name;");
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createDb",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
            return false;
        }
        return true;
    }

    private function createDbUser(PDO $dbh, $db_name, $db_user_name, $db_user_password, $host, $try_again = false): bool
    {
        try {
            if (PANEL_TYPE == CLOUD) {
                $dbh->exec("CREATE USER '$db_user_name'@'$host' IDENTIFIED WITH mysql_native_password BY '$db_user_password';");
                $dbh->exec("GRANT ALL ON $db_name.* TO '$db_user_name'@'$host';");
                $dbh->exec("alter user '$db_user_name'@'$host' identified with mysql_native_password by '$db_user_password';");
            }
            else {
                $dbh->exec("CREATE USER '$db_user_name'@'localhost' IDENTIFIED WITH mysql_native_password BY '$db_user_password';");
                $dbh->exec("GRANT ALL ON $db_name.* TO '$db_user_name'@'localhost';");
                $dbh->exec("alter user '$db_user_name'@'localhost' identified with mysql_native_password by '$db_user_password';");
            }

            $dbh->exec("FLUSH PRIVILEGES;");

            return $this->getErrorInfo($dbh, $db_name);
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createDbUser",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
            if($try_again) {
                $this->dropMySQLUserAlone($dbh, $db_user_name, $host);
                return $this->createDbUser($dbh, $db_name, $db_user_name, $db_user_password, $host, false);
            }
            return false;
        }
    }

    private function getErrorInfo(PDO $dbh, $db_name): bool
    {
        $errors = $dbh->errorInfo();
        if(!empty($errors)) {
            if(is_array($errors)) {
                foreach ($errors as $error) {
                    if($error != 0) {
                        $this->error_messages["content"] = $errors;
                        $this->error_messages["test"] = [
                            ($error != false),
                            ($error != null),
                            ($error != 0),
                        ];
                        $dbh->exec("DROP DATABASE $db_name; FLUSH PRIVILEGES;");
                        return false;
                    }
                }
            }
            else {
                $this->error_messages["content"] = $errors;
                $dbh->exec("DROP DATABASE $db_name; FLUSH PRIVILEGES;");
                return false;
            }
        }
        return true;
    }

    private function dropMySQLUser($username, $host)
    {
        $databases = new DatabaseServer();
        $database_server = $databases->findFirstById($this->getPost("database_server"));
        if($database_server->host == "127.0.0.1") {
            $db_host = "localhost";
        }
        else {
            $db_host = $database_server->host;
        }

        try {
            $dbh = new PDO("mysql:host=$db_host:$database_server->port", $database_server->username, trim($this->crypto->bdecode($database_server->password)));
            $this->dropMySQLUserAlone($dbh, $username, $host);
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->dropMySQLUser",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
        }
    }

    private function dropMySQLUserAlone(PDO $dbh, $username, $host)
    {
        try {
            $dbh->exec("DROP USER '$username'@'$host'; FLUSH PRIVILEGES;");
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->dropMySQLUserAlone",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
        }
    }

    private function createMySQLUser($username, $database_name, $password, $host, $mysql_8)
    {
        $databases = new DatabaseServer();
        $database_server_query = $databases->findFirstById($this->getPost("database_server"));
        $database_server = $database_server_query->toArray();

        try {
            if($database_server['host'] == "127.0.0.1") {
                $db_host = "localhost";
            }
            else {
                $db_host = $database_server['host'];
            }
            $dbh = new PDO("mysql:host=$db_host:$database_server[port]", $database_server['username'], trim($this->crypto->bdecode($database_server['password'])));

            if($mysql_8) {
                $dbh->exec("CREATE USER '$username'@'$host' IDENTIFIED WITH mysql_native_password BY '$password';
                    GRANT ALL ON $database_name.* TO '$username'@'$host'; FLUSH PRIVILEGES;");
            }
            else {
                $dbh->exec("CREATE USER '$username'@'$host' IDENTIFIED BY '$password';
                    GRANT ALL ON $database_name.* TO '$username'@'$host'; FLUSH PRIVILEGES;");
            }
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->createMySQLUser",
                "data" => [
                    "username" => $username,
                    "database_name" => $database_name,
                    "password" => $password,
                    "host" => $host,
                    "mysql_8" => $mysql_8
                ],
                "errors" => $e->getMessage(),
            ]);
        }

    }

    public function updateMySQLDatabaseUserSystem(Servers $server): bool
    {
        $mysql_8 = false;
        if($this->config->application->type == STANDALONE) {
            if(strpos($this->system_packages["DATABASE_SERVERS"], "mysql") !== false) {
                if(PANEL_OS_TYPE == LINUX) {
                    $package = SystemPackages::findFirst("package_name = 'mysql-community-server'");
                }
                else {
                    $package = SystemPackages::findFirst("package_name LIKE '%MySQL Server 8%'");
                }
                if($package) {
                    if(strpos($package->version, "8.") !== false) {
                        $mysql_8 = true;
                    }
                }
            }
        }
        else {
            if($server->os == LINUX) {
                $package = SystemPackages::findFirst("package_name = 'mysql-community-server' and server_id = '$server->id'");
            }
            else {
                $package = SystemPackages::findFirst("package_name LIKE '%MySQL Server 8%' and server_id = '$server->id'");
            }
            if($package) {
                if(strpos($package->version, "8.") !== false) {
                    $mysql_8 = true;
                }
            }
        }

        $databases = new DatabaseServer();
        $database_server_query = $databases->findFirstById($this->getPost("database_server"));
        $database_server = $database_server_query->toArray();

        $password = $this->getPost("password");
        $username = $this->getPost("database_username");
        $hosts = array();
        if($this->getPost("remote_connection") == "on") {
            $remote_hosts = $this->getPost("remote_hosts");
            if(!empty($remote_hosts)) {
                $hosts = array_unique($remote_hosts);
            }
        }
        $old_hosts = $this->getPost("old_hosts");

        try {

            $dbh = new PDO("mysql:host=$database_server[host]:$database_server[port]", $database_server['username'], trim($this->crypto->bdecode($database_server['password'])));

            $rename_sql = "";
            if($this->change_username) {
                $rename_sql = "RENAME USER '$this->old_username'@'localhost' TO '$username'@'localhost';";
            }

            if($mysql_8) {
                $change_pass_sql = "ALTER USER '$username'@'localhost' IDENTIFIED WITH mysql_native_password BY '$password';";
            }
            else {
                $change_pass_sql = "SET PASSWORD FOR '$username'@'localhost' = PASSWORD('$password');";
            }

            $sql = $rename_sql . $change_pass_sql;

            if($sql != null) {
                $dbh->exec("$sql FLUSH PRIVILEGES;");
            }

            $database_user = DatabaseUsers::findFirst("login = '$username'");
            $database = Databases::findFirst("database_user_id = '$database_user->id'");

            if(!empty($old_hosts)) {
                foreach($old_hosts as $old_host) {
                    $this->dropMySQLUser($username, $old_host);
                }
            }

            if(!empty($hosts)) {
                $user_password = trim($this->crypto->bdecode($database_user->password));
                foreach($hosts as $host) {
                    $this->createMySQLUser($username, $this->getPost("prefix")."_".$database->name, $user_password, $host, $mysql_8);
                }
            }
            return $this->remoteHostAllowed($database_user->id, $hosts);
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->updateMySQLDatabaseUserSystem",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages[] = $e->getMessage();
            return false;
        }
    }

    private function dropMySQLDatabaseAndUser($db_user_id): bool
    {
        $db_name = $this->getPost("prefix")."_".$this->getPost("database_name");
        $db_user_name = $this->getPost("database_username");

        $databases = new DatabaseServer();
        $database_server = $databases->findFirstById($this->getPost("database_server"));

        try {
            $dbh = new PDO("mysql:host=$database_server->host:$database_server->port", $database_server->username, trim($this->crypto->bdecode($database_server->password)));
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->exec("DROP DATABASE $db_name;
                DROP USER '$db_user_name'@'localhost';
                FLUSH PRIVILEGES;");

            if($db_user_id != null) {
                $database_hosts = DatabaseRemoteHosts::findFirst("db_user_id = '$db_user_id'");
                $old_hosts = array();
                if($database_hosts) {
                    if($database_hosts->hosts != null) {
                        $old_hosts = json_decode($database_hosts->hosts,true);
                    }
                }
            }

            if(!empty($old_hosts)) {
                foreach($old_hosts as $old_host) {
                    $this->dropMySQLUser($db_user_name, $old_host);
                }
            }
        }
        catch (PDOException $e) {
            $this->log->error([
                "Module" => "DomainDatabase->dropMySQLDatabaseandUser",
                "data" => $this->getPost(),
                "db_user_id" => $db_user_id,
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages["content"] = $e->getMessage();
            return false;
        }
        return true;
    }
}