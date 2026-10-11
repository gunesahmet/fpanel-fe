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

use Exception;
use PDO;
use PDOException;

trait Connection
{
    public function connection($type, $username, $password, $port, $host, $wait = 0): bool
    {
        $status = false;
        switch($type) {
            case "mysql":
                $status = $this->testConnectionMySQL($username, $password, $port, $host, $wait);
                break;
            case "mssql":
                $status = $this->testConnectionMSSQL($username, $password, $port, $host, $wait);
                break;
            case "pgsql":
                $status = $this->testConnectionPGSQL($username, $password, $port, $host, $wait);
                break;
        }

        return $status;
    }

    private function testConnectionMySQL($username, $password, $port, $host, $wait = 0): bool
    {
        if($wait > 0) {
            sleep($wait);
        }
        $started_time = time();
        $connected = false;
        $last_errors = [];
        while (true) {
            $current_time = time();
            if(($current_time - $started_time) > 15) {
                break;
            }

            try {
                set_error_handler(function () {});
                if($host == "localhost")
                    $host = "127.0.0.1";
                $fp = @fsockopen($host, $port, $err_code, $err_msg, 1);
                if ($fp) {
                    $connected = true;
                    fclose($fp);
                    break;
                }
                else {
                    $last_errors = [
                        $err_code => $err_msg,
                    ];
                }
            }
            finally {
                restore_error_handler();
            }
            sleep(1);
        }
        if(!$connected) {
            $this->error_messages[] =  __('MySQL Server, port or database type is wrong. Error Message: %err_msg%', ["err_msg" => array_values($last_errors)[0]]);
            $this->log->error([
                "File" => "DatabaseServer => connection",
                "Error" => $this->error_messages[0],
            ]);
            return false;
        }

        try {
            $dbh = new PDO("mysql:host=$host:$port", $username, $password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_TIMEOUT, 3);
            $dbh = null;
            return true;
        }
        catch(PDOException $e) {
            $this->log->error([
                "Module" => "DatabaseServer->testConnectionMySQL",
                "data" => [
                    "username" => $username,
                    "password" => $password,
                    "port" => $port,
                    "host" => $host,
                    "wait" => $wait,
                ],
                "errors" => $e->getMessage(),
                "message" => $last_errors,
            ]);
            $this->error_messages[] =  $e->getMessage();
            return false;
        }
    }

    private function testConnectionPGSQL($username, $password, $port, $host, $wait = 0): bool
    {
        if($wait > 0) {
            sleep($wait);
        }
        $started_time = time();
        $connected = false;
        $last_errors = [];
        while (true) {
            $current_time = time();
            if(($current_time - $started_time) > 15) {
                break;
            }
            try {
                set_error_handler(function () {});
                if($host == "localhost")
                    $host = "127.0.0.1";
                $fp = @fsockopen($host, $port, $err_code, $err_msg,3);
                if ($fp) {
                    $connected = true;
                    fclose($fp);
                    break;
                }
                else {
                    $last_errors = [
                        $err_code => $err_msg,
                    ];
                }
            }
            finally {
                restore_error_handler();
            }
            sleep(1);
        }
        if(!$connected) {
            $this->error_messages[] =  __('PostgreSQL Server, port or database type is wrong. Error Message: %err_msg%', ["err_msg" => array_values($last_errors)[0]]);
            $this->log->error([
                "File" => "DatabaseServer => connection",
                "Error" => $this->error_messages[0],
            ]);
            return false;
        }

        try {
            $dbh = new PDO('pgsql:dbname=postgres;host='. $host .';port='. $port .';user='. $username .';password='. $password .'');
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh = null;
            return true;
        }
        catch(PDOException $e) {
            $this->log->error([
                "Module" => "DatabaseServer->testConnectionMySQL",
                "data" => [
                    "username" => $username,
                    "password" => $password,
                    "port" => $port,
                    "host" => $host,
                    "wait" => $wait,
                ],
                "errors" => $e->getMessage(),
                "message" => $last_errors,
            ]);
            $this->error_messages["content"] = $e->getMessage();
            return false;
        }
    }

    private function testConnectionMSSQL($username, $password, $port, $host, $wait = 0): bool
    {
        if($wait > 0) {
            sleep($wait);
        }
        $started_time = time();
        $connected = false;
        $last_errors = [];
        while (true) {
            $current_time = time();
            if(($current_time - $started_time) > 15) {
                break;
            }
            try {
                set_error_handler(function () {});
                if($host == "localhost")
                    $host = "127.0.0.1";
                $fp = @fsockopen($host, $port, $err_code, $err_msg,3);
                if ($fp) {
                    $connected = true;
                    fclose($fp);
                    break;
                }
                else {
                    $last_errors = [
                        $err_code => $err_msg,
                    ];
                }
            }
            finally {
                restore_error_handler();
            }
            sleep(1);
        }
        if(!$connected) {
            $this->error_messages[] =  __('Microsoft SQL Server, port or database type is wrong. Error Message: %err_msg%', ["err_msg" => array_values($last_errors)[0]]);
            $this->log->error([
                "File" => "DatabaseServer => connection",
                "Error" => $this->error_messages[0],
            ]);
            return false;
        }

        try {
            $serverName = "tcp:$host, $port";
            $connectionOptions = [
                "Database" => "master",
                "UID" => $username,
                "PWD" => $password,
            ];
            $conn = sqlsrv_connect($serverName, $connectionOptions);

            if($conn == false) {
                $this->error_messages['content'] =  __('Microsoft SQL Server Username or password is wrong');
                return false;
            }
            else {
                sqlsrv_close($conn);
            }
            return true;
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "DatabaseServer->testConnectionMySQL",
                "data" => [
                    "username" => $username,
                    "password" => $password,
                    "port" => $port,
                    "host" => $host,
                    "wait" => $wait,
                ],
                "errors" => $e->getMessage(),
                "message" => $last_errors,
            ]);
            return false;
        }
    }

}