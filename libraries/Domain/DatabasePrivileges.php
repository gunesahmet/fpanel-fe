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
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseUsers;
use FPanel\Models\Domains;
use PDO;
use PDOException;

trait DatabasePrivileges
{
    public function lockAllMySQLPrivileges($domain_id): bool
    {
        $domain = Domains::findFirstById($domain_id);
        if(!$domain) {
            $this->error_messages["domain"] = __("Domain not found");
            return false;
        }
        $databases = Databases::find("domain_id = '$domain_id' and type = 'mysql'");

        foreach ($databases as $database) {
            $database_server = new DatabaseServer();
            $database_server_query = $database_server->findFirstById($database->database_server_id);
            $database_server = $database_server_query->toArray();

            try {
                if($database_server['host'] == "localhost")
                    $host = "127.0.0.1";
                else
                    $host = $database_server['host'];

                $dbh = new PDO("mysql:host=$host:$database_server[port]", $database_server['username'], trim($this->crypto->bdecode($database_server['password'])));
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->exec("use mysql;");
                $dbh->exec("UPDATE db SET Insert_priv='N', Create_priv='N', Update_priv='N' WHERE Db='$database->name';");
                $dbh->exec("FLUSH PRIVILEGES;");
                $errors = $dbh->errorInfo();
                if($errors[2] != null) {
                    $this->error_messages[] = $errors[2];
                    return false;
                }
            }
            catch (PDOException $e) {
                $this->log->error([
                    "Module" => "DomainDatabase->lockAllMySQLPrivileges",
                    "data" => $this->getPost(),
                    "errors" => $e->getMessage(),
                ]);
                $this->error_messages[] = $e->getMessage();
                return false;
            }
        }
        return true;
    }

    public function unlockAllMySQLPrivileges($domain_id): bool
    {
        $domain = Domains::findFirstById($domain_id);
        if(!$domain) {
            $this->error_messages["domain"] = __("Domain not found");
            return false;
        }
        $databases = Databases::find("domain_id = '$domain_id' and type = 'mysql'");

        foreach ($databases as $database) {
            $database_server = new DatabaseServer();
            $database_server_query = $database_server->findFirstById($database->database_server_id);
            $database_server = $database_server_query->toArray();

            try {
                if($database_server['host'] == "localhost")
                    $host = "127.0.0.1";
                else
                    $host = $database_server['host'];

                $dbh = new PDO("mysql:host=$host:$database_server[port]", $database_server['username'], trim($this->crypto->bdecode($database_server['password'])));
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->exec("use mysql;");
                $dbh->exec("UPDATE db SET Insert_priv='Y', Create_priv='Y', Update_priv='Y' WHERE Db='$database->name';");
                $dbh->exec("FLUSH PRIVILEGES;");
                $errors = $dbh->errorInfo();
                if($errors[2] != null) {
                    $this->error_messages[] = $errors[2];
                    return false;
                }
            }
            catch (PDOException $e) {
                $this->log->error([
                    "Module" => "DomainDatabase->unlockAllMySQLPrivileges",
                    "data" => $this->getPost(),
                    "errors" => $e->getMessage(),
                ]);
                $this->error_messages[] = $e->getMessage();
                return false;
            }
        }
        return true;
    }

    public function lockAllPostgreSQLPrivileges($domain_id): bool
    {
        $domain = Domains::findFirstById($domain_id);
        if(!$domain) {
            $this->error_messages["domain"] = __("Domain not found");
            return false;
        }
        $databases = Databases::find("domain_id = '$domain_id' and type = 'pgsql'");

        foreach ($databases as $database) {
            $database_server = new DatabaseServer();
            $database_server_query = $database_server->findFirstById($database->database_server_id);
            $database_server = $database_server_query->toArray();
            $database_user = DatabaseUsers::findFirstById($database->database_user_id);
            try {
                $dbh = new PDO('pgsql:dbname=postgres;host='. $database_server['host'] .';port='. $database_server["port"] .';user='. $database_server['username'] .';password='. trim($this->crypto->bdecode($database_server['password'])) .'');

                $stmt = $dbh->query("use $database->name; SELECT
                    (table_schema || '.' || table_name) as tnames
                FROM
                    information_schema.tables
                WHERE
                    table_schema NOT IN ('pg_catalog', 'information_schema');");
                if(!$stmt) {
                    return true;
                }
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $tables = "";
                foreach ($result["tnames"] as $tname) {
                    if($tables == null)
                        $tables = $tname;
                    else
                        $tables = ", $tname";
                }
                $dbh->exec("use $database->name; 
                REVOKE INSERT ON $tables FROM $database_user->login; 
                REVOKE UPDATE ON $tables FROM $database_user->login; 
                GRANT DELETE ON $tables TO  $database_user->login;");

                $errors = $dbh->errorInfo();
                if($errors[2] != null) {
                    $this->error_messages['content'] = $errors[2];
                    return false;
                }
            }
            catch (PDOException $e) {
                $this->log->error([
                    "Module" => "DomainDatabase->lockAllPostgreSQLPrivileges",
                    "data" => $this->getPost(),
                    "errors" => $e->getMessage(),
                ]);
                $this->error_messages[] = $e->getMessage();
                return false;
            }
        }
        return true;
    }

    public function unlockAllPostgreSQLPrivileges($domain_id): bool
    {
        $domain = Domains::findFirstById($domain_id);
        if(!$domain) {
            $this->error_messages["domain"] = __("Domain not found");
            return false;
        }
        $databases = Databases::find("domain_id = '$domain_id' and type = 'pgsql'");

        foreach ($databases as $database) {
            $database_server = new DatabaseServer();
            $database_server_query = $database_server->findFirstById($database->database_server_id);
            $database_server = $database_server_query->toArray();
            $database_user = DatabaseUsers::findFirstById($database->database_user_id);
            try {
                $dbh = new PDO('pgsql:dbname=postgres;host='. $database_server['host'] .';port='. $database_server["port"] .';user='. $database_server['username'] .';password='. trim($this->crypto->bdecode($database_server['password'])) .'');

                $stmt = $dbh->query("use $database->name; SELECT
                    (table_schema || '.' || table_name) as tnames
                FROM
                    information_schema.tables
                WHERE
                    table_schema NOT IN ('pg_catalog', 'information_schema');");
                if(!$stmt) {
                    return true;
                }
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $tables = "";
                foreach ($result["tnames"] as $tname) {
                    if($tables == null)
                        $tables = $tname;
                    else
                        $tables = ", $tname";
                }
                $dbh->exec("use $database->name; 
                GRANT INSERT ON $tables TO $database_user->login; 
                GRANT UPDATE ON $tables TO $database_user->login; 
                GRANT DELETE ON $tables TO  $database_user->login;");

                $errors = $dbh->errorInfo();
                if($errors[2] != null) {
                    $this->error_messages['content'] = $errors[2];
                    return false;
                }
            }
            catch (PDOException $e) {
                $this->log->error([
                    "Module" => "DomainDatabase->unlockAllPostgreSQLPrivileges",
                    "data" => $this->getPost(),
                    "errors" => $e->getMessage(),
                ]);
                $this->error_messages[] = $e->getMessage();
                return false;
            }
        }
        return true;
    }

    public function lockAllMSSQLPrivileges($domain_id): bool
    {
        $mssql_databases = Databases::find("domain_id = '$domain_id' and type='mssql'");
        foreach ($mssql_databases as $mssql_database) {
            $mssql_db_servers = DatabaseServer::findFirstById($mssql_database->database_server_id);
            if($mssql_db_servers) {
                try {
                    $serverName = "$mssql_db_servers->host, $mssql_db_servers->port";
                    $connectionInfo = array( "UID"=>$mssql_db_servers->username, "PWD"=>trim($this->crypto->bdecode($mssql_db_servers->password)) );
                    $conn = sqlsrv_connect( $serverName, $connectionInfo);
                    if(!$conn) {
                        return true;
                    }
                    $response = sqlsrv_query($conn, "SELECT 
      database_name = DB_NAME(database_id)
    , log_size = CAST(SUM(CASE WHEN type_desc = 'LOG' THEN size END) * 8 AS DECIMAL(8,2))
    , row_size = CAST(SUM(CASE WHEN type_desc = 'ROWS' THEN size END) * 8 AS DECIMAL(8,2))
    , total_size = CAST(SUM(size) * 8 AS DECIMAL(8,2))
FROM sys.master_files WITH(NOWAIT)
WHERE database_id = DB_ID('$mssql_database->name')
GROUP BY database_id;");
                    $result = sqlsrv_fetch_array($response, SQLSRV_FETCH_ASSOC);
                    if(!empty($result)) {
                        $total_ms_size = $result["total_size"];
                        sqlsrv_query($conn, "USE master; ALTER DATABASE [$mssql_database->name] MODIFY FILE ( NAME = N'$mssql_database->name', MAXSIZE = ". $total_ms_size."KB );");
                    }
                    sqlsrv_close($conn);
                }
                catch (Exception $e) {
                    $this->log->error([
                        "Module" => "DomainDatabase->lockAllMSSQLPrivileges",
                        "data" => $this->getPost(),
                        "errors" => $e->getMessage(),
                    ]);
                    return false;
                }
            }
        }
        return true;
    }

    public function unlockAllMSSQLPrivileges($domain_id, $max_size = null): bool
    {
        $mssql_databases = Databases::find("domain_id = '$domain_id' and type='mssql'");
        foreach ($mssql_databases as $mssql_database) {
            $mssql_db_servers = DatabaseServer::findFirstById($mssql_database->database_server_id);
            if($mssql_db_servers) {
                try {
                    $serverName = "$mssql_db_servers->host, $mssql_db_servers->port";
                    $connectionInfo = array( "UID"=>$mssql_db_servers->username, "PWD"=>trim($this->crypto->bdecode($mssql_db_servers->password)) );
                    $conn = sqlsrv_connect( $serverName, $connectionInfo);
                    if(!$conn) {
                        return true;
                    }
                    sqlsrv_query($conn, "USE master; ALTER DATABASE [$mssql_database->name] MODIFY FILE ( NAME = N'$mssql_database->name', MAXSIZE = ". $max_size."KB );");
                    sqlsrv_close($conn);
                }
                catch (Exception $e) {
                    $this->log->error([
                        "Module" => "DomainDatabase->unlockAllMSSQLPrivileges",
                        "data" => $this->getPost(),
                        "errors" => $e->getMessage(),
                    ]);
                    return false;
                }
            }
        }
        return true;
    }
}