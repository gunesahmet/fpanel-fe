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
 */
namespace FPanel\Common\Statistics;

use DateInterval;
use DateTime;
use FPanel\Console\Output as Output;
use FPanel\Domain\DomainDatabase;
use FPanel\Domain\Mails;
use FPanel\Domain\NamedService;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\Models\FtpUsers;
use FPanel\Models\Users;
use FPanel\Process\Mail;
use FPanel\Process\User;
use FPanel\Process\Vhost;
use PDO;
use PDOException;
use Phalcon\Mvc\User\Component;

class Domain extends Component
{
    private $error_messages = [];

    public function initDomain($domain_name, $stats): bool
    {
        if ($this->env->isDomain($domain_name) == false) {
            $this->error_messages["domain_name"] = __("Invalid domain");
            return false;
        }
        $status = ACTIVE;

        $domain_statistics = DomainStatistics::findFirst("domain_name = '$domain_name'");

        if (!$domain_statistics) {
            $this->error_messages["domain_name"] = __("Domain not found");
            return false;
        } else {
            $domains = Domains::findFirstById($domain_statistics->domain_id);
            if (($domain_statistics->last_init != null)) {
                $last_init = $domain_statistics->last_init;
            } else {
                $last_init = $domains->created_date;
            }

            $d1 = new DateTime($last_init);
            $d2 = new DateTime("now");
            $months = 0;

            $d1->add(new DateInterval('P1M'));
            while ($d1 <= $d2) {
                $months++;
                $d1->add(new DateInterval('P1M'));
            }

            if ($months > 1) {
                $last_init = strtotime("+1 month", $last_init);
            }

            $total_db_disk = $this->checkDatabases($domain_statistics->domain_id);

            $total_disk = (is_numeric($stats['disk_httpd']) ? $stats['disk_httpd'] : 0) + (is_numeric($total_db_disk) ? $total_db_disk : 0) + (is_numeric($stats['disk_mail']) ? $stats['disk_mail'] : 0) + (is_numeric($stats['disk_log']) ? $stats['disk_log'] : 0);
            $total_traffic = (is_numeric($stats['http_traffic']) ? $stats['http_traffic'] : 0) + (is_numeric($stats['ftp_traffic']) ? $stats['ftp_traffic'] : 0);
            $message = "";

            if (($domain_statistics->db_max != UNLIMITED) && is_numeric($domain_statistics->db_max)) {
                if ($domain_statistics->over_use == OVERUSE) {
                    $message .= "\nDatabase disk size limit overused";
                } else {
                    $domain_database = new DomainDatabase();
                    if ((int)$domain_statistics->db_max <= (int)$total_db_disk) {
                        $lock_status = $domain_database->lockAllMySQLPrivileges($domain_statistics->domain_id);
                        if (!$lock_status) {
                            $this->log->error([
                                "Module" => "Domain->initDomain->lockAllMySQLPrivileges",
                                "data" => ["domain_id" => $domain_statistics->domain_id, "data" => $stats],
                                "errors" => $domain_database->getMessages(),
                            ]);
                        }

                        $lock_status = $domain_database->lockAllPostgreSQLPrivileges($domain_statistics->domain_id);
                        if (!$lock_status) {
                            $this->log->error([
                                "Module" => "Domain->initDomain->lockAllPostgreSQLPrivileges",
                                "data" => ["domain_id" => $domain_statistics->domain_id, "data" => $stats],
                                "errors" => $domain_database->getMessages(),
                            ]);
                        }

                        $lock_status = $domain_database->lockAllMSSQLPrivileges($domain_statistics->domain_id);
                        if (!$lock_status) {
                            $this->log->error([
                                "Module" => "Domain->initDomain->lockAllMSSQLPrivileges",
                                "data" => ["domain_id" => $domain_statistics->domain_id, "data" => $stats],
                                "errors" => $domain_database->getMessages(),
                            ]);
                        }
                    } else {
                        $unlock_status = $domain_database->unlockAllMySQLPrivileges($domain_statistics->domain_id);
                        if (!$unlock_status) {
                            $this->log->error([
                                "Module" => "Domain->initDomain->unlockAllMySQLPrivileges",
                                "data" => ["domain_id" => $domain_statistics->domain_id, "data" => $stats],
                                "errors" => $domain_database->getMessages(),
                            ]);
                        }

                        $unlock_status = $domain_database->unlockAllPostgreSQLPrivileges($domain_statistics->domain_id);
                        if (!$unlock_status) {
                            $this->log->error([
                                "Module" => "Domain->initDomain->unlockAllPostgreSQLPrivileges",
                                "data" => ["domain_id" => $domain_statistics->domain_id, "data" => $stats],
                                "errors" => $domain_database->getMessages(),
                            ]);
                        }

                        $unlock_status = $domain_database->unlockAllMSSQLPrivileges($domain_statistics->domain_id, $domain_statistics->db_max);
                        if (!$unlock_status) {
                            $this->log->error([
                                "Module" => "Domain->initDomain->unlockAllMSSQLPrivileges",
                                "data" => ["domain_id" => $domain_statistics->domain_id, "data" => $stats],
                                "errors" => $domain_database->getMessages(),
                            ]);
                        }
                    }
                }
            }

            if (($domain_statistics->disk_max != UNLIMITED) && is_numeric($domain_statistics->disk_max)) {
                if (($status == MANUAL_PASSIVE_BY_ADMIN) || ($status == MANUAL_PASSIVE_BY_OWNER)) {
                    $message .= "\nDomain disabled by owner or admin";
                } else {
                    if ($domain_statistics->over_use == OVERUSE) {
                        $message .= "\nDisk size limit overused";
                    } else {
                        if ((int)$domain_statistics->disk_max <= (int)$total_disk) {
                            $status = DISK_LIMIT_EXCEEDED;
                            $message .= "\nDisk limit exceeded";
                        }
                    }
                }

            }

            if (($domain_statistics->traffic_max != UNLIMITED) && is_numeric($domain_statistics->traffic_max)) {
                if (($status == MANUAL_PASSIVE_BY_ADMIN) || ($status == MANUAL_PASSIVE_BY_OWNER)) {
                    $message .= "\nDomain disabled by owner or admin";
                } else {
                    if ($domain_statistics->over_use == OVERUSE) {
                        $message = "\nTraffic size limit overused";
                    } else {
                        if ((int)$domain_statistics->traffic_max <= (int)$total_traffic) {
                            $status = TRAFFIC_LIMIT_EXCEEDED;
                            $message = "\nTraffic limit exceeded";
                        }
                    }
                }
            }

            if (($domain_statistics->mail_max != UNLIMITED) && is_numeric($domain_statistics->mail_max)) {
                if (($status == MANUAL_PASSIVE_BY_ADMIN) || ($status == MANUAL_PASSIVE_BY_OWNER)) {
                    if (($status == DISK_LIMIT_EXCEEDED))
                        $message = "\nMail Disk limit exceeded";
                    else
                        $message = "\nMail service disabled by owner";
                } else {
                    if ($domain_statistics->over_use == OVERUSE) {
                        $message = "\nMail disk size limit overused";
                    } else {
                        if ($stats['disk_mail'] >= $domain_statistics->mail_max) {
                            $message = 'Mail Disk limit exceeded';
                            $this->changeStatusDomainMailService($domain_statistics->domain_id, PASSIVE);
                        } else {
                            if ($status == DISK_LIMIT_EXCEEDED) {
                                $this->changeStatusDomainMailService($domain_statistics->domain_id, PASSIVE);
                            } else {
                                $this->changeStatusDomainMailService($domain_statistics->domain_id, ACTIVE);
                            }
                        }
                    }
                }
            }

            if (($status != MANUAL_PASSIVE_BY_ADMIN) && ($status != MANUAL_PASSIVE_BY_OWNER)) {
                if ($domain_statistics->over_use == OVERUSE) {
                    $status = ACTIVE;
                }
            } else {
                $status = $domain_statistics->status;
            }

            if (($months < 1) || ($months == 1)) {
                $assign = array(
                    'mail' => $stats['disk_mail'],
                    'db' => $total_db_disk,
                    'httpdoc' => $stats['disk_httpd'],
                    'logs' => $stats['disk_log'],
                    'disk' => $total_disk,
                    'traffic' => $total_traffic,
                    'status' => $status,
                    'last_init' => $last_init,
                    "expire_date" => $domains->expire_date,
                );
            } else {
                $status = ACTIVE;
                $assign = array(
                    'mail' => $stats['disk_mail'],
                    'db' => $total_db_disk,
                    'httpdoc' => $stats['disk_httpd'],
                    'logs' => $stats['disk_log'],
                    'disk' => $total_disk,
                    'traffic' => "0",
                    'status' => $status,
                    'last_init' => $last_init,
                    "expire_date" => $domains->expire_date,
                );
            }

            $domain_statistics->assign($assign);
            if (!$domain_statistics->update()) {
                foreach ($domain_statistics->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Domain->initDomain->DomainStatistics",
                    "data" => ["domain" => $domain_name, "data" => $stats],
                    "errors" => $this->error_messages,
                ]);
            }

            $domain_backend = new Domain();

            if ($status != ACTIVE) {
                $this->log->info([
                    "domain_name" => $domain_name,
                    "message" => "Suspending"
                ]);
                $domain_backend->suspend($domain_statistics->domain_id, $message, $status);
                $this->changeStatusDomainDnsService($domain_statistics->domain_id, PASSIVE);
                $this->changeStatusDomainMailService($domain_statistics->domain_id, PASSIVE);
            } else {
                $domain_backend->unsuspend($domain_statistics->domain_id);
                $this->changeStatusDomainDnsService($domain_statistics->domain_id, ACTIVE);
                $this->changeStatusDomainMailService($domain_statistics->domain_id, ACTIVE);
                $this->log->info([
                    "domain_name" => $domain_name,
                    "message" => "Unsuspending"
                ]);
            }
        }
        if (!empty($this->error_messages)) {
            $this->log->error([
                "Module" => "Domain->initDomain",
                "data" => ["domain_id" => $domain_name, "data" => $stats],
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        $this->log->info([
            "message" => "$domain_name stats calculate is done"
        ]);
        return true;
    }

    public function suspend($id, $message, $status)
    {
        $domain = Domains::findFirstById($id);
        if ($domain) {
            $domain->assign(array('status' => $status, 'status_msg' => "$message"));
            $domain->update();

            $user = Users::findFirst("username = '$domain->name'");
            $user->assign(array('status' => $status, 'status_msg' => "$message"));
            $user->update();

            $statistics = DomainStatistics::findFirst("domain_id = '$id'");
            $statistics->assign(array('status' => $status));
            $statistics->update();

            $this->changeStatusCurl("-1", $id);
        }
    }

    public function unsuspend($id)
    {
        $domain = Domains::findFirstById($id);
        if ($domain) {
            $domain->status = "1";
            $domain->status_msg = "reset";
            if (!$domain->update()) {
                foreach ($domain->getMessages() as $message) {
                    if ((php_sapi_name() == 'cli')) {
                        $msg = Output::COLOR_RED . "[ERROR]" . Output::COLOR_NONE . $message;
                        Output::stdout($msg);
                    }
                }
            }


            $user = Users::findFirst("username = '$domain->name'");
            if (!$user) {
                if ((php_sapi_name() == 'cli')) {
                    $msg = Output::COLOR_RED . "[ERROR]" . Output::COLOR_NONE . "Domain not found";
                    Output::stdout($msg);
                }
            }
            $user->status = "1";
            $user->status_msg = "reset";
            if (!$user->update()) {
                foreach ($user->getMessages() as $message) {
                    if ((php_sapi_name() == 'cli')) {
                        $msg = Output::COLOR_RED . "[ERROR]" . Output::COLOR_NONE . $message;
                        Output::stdout($msg);
                    }
                }
            }

            $statistics = DomainStatistics::findFirst("domain_id = '$id'");
            if (!$statistics) {
                if ((php_sapi_name() == 'cli')) {
                    $msg = Output::COLOR_RED . "[ERROR]" . Output::COLOR_NONE . "Domain not found";
                    Output::stdout($msg);
                }
            }
            $statistics->status = "1";
            if (!$statistics->update()) {
                foreach ($statistics->getMessages() as $message) {
                    if ((php_sapi_name() == 'cli')) {
                        $msg = Output::COLOR_RED . "[ERROR]" . Output::COLOR_NONE . $message;
                        Output::stdout($msg);
                    }

                }
            }

            $this->changeStatusCurl("1", $id);
        } else {
            if (php_sapi_name() == 'cli') {
                $msg = Output::COLOR_RED . "[ERROR]" . Output::COLOR_NONE . "Domain not found";
                Output::stdout($msg);
            }
        }
    }

    private function changeStatusCurl($status, $domain_id)
    {
        $domains = Domains::findFirstById($domain_id);
        if ($domains) {
            $ftp_users = FtpUsers::findFirstById($domains->ftp_user_id);
            if ($ftp_users) {
                $params = array(
                    'domain' => $domains->name,
                    'user' => $ftp_users->username
                );
                $usermng = new User();
                $vhostmng = new Vhost();
                $mailmng = new Mail();
                $usermng->setServerId($domains->web_server_id);
                $vhostmng->setServerId($domains->web_server_id);
                $mailmng->setServerId($domains->mail_server_id);
                // usermng
                // vhost
                // mail
                if ($status == "1") {
                    $usermng->unsuspend($params);
                    $vhostmng->unsuspend($params);
                    $mailmng->unsuspendDomain($params);
                } else {
                    $usermng->suspend($params);
                    $vhostmng->suspend($params);
                    $mailmng->suspendDomain($params);
                }
            }
        }
    }

    private function changeStatusDomainMailService($domain_id, $status)
    {
        $mail = new Mails();
        $mail->changeMailSettings($status, $domain_id);
    }

    private function changeStatusDomainDnsService($domain_id, $status)
    {
        $mail = new NamedService();
        $mail->changeDnsSettings($status, $domain_id);
    }

    private function checkDatabases($domain_id)
    {
        $mysql_db_size = 0;
        $pgsql_db_size = 0;
        $mssql_db_size = 0;
        $total_db_disk = 0;

        $mysql_databases = Databases::find("domain_id = '$domain_id' and type='mysql'");
        $postgresql_databases = Databases::find("domain_id = '$domain_id' and type='pgsql'");
        $mssql_databases = Databases::find("domain_id = '$domain_id' and type='mssql'");

        foreach ($mysql_databases as $mysql_database) {
            $mysql_db_servers = DatabaseServer::findFirstById($mysql_database->database_server_id);
            if ($mysql_db_servers) {
                try {
                    $dbh = new PDO("mysql:host=$mysql_db_servers->host:$mysql_db_servers->port", $mysql_db_servers->username, trim($this->crypto->bdecode($mysql_db_servers->password)));
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stm = $dbh->query("SELECT table_schema \"dbname\", SUM(data_length + index_length) \"size\" 
                                FROM information_schema.tables where table_schema = '$mysql_database->name' GROUP BY table_schema;");

                    $result = $stm->fetch(PDO::FETCH_ASSOC);
                    $mysql_db_size += $result["size"];
                } catch (PDOException $e) {
                }
            }
        }

        foreach ($postgresql_databases as $postgresql_database) {
            $pgsql_db_servers = DatabaseServer::findFirstById($postgresql_database->database_server_id);
            if ($pgsql_db_servers) {
                try {
                    $dbh = new PDO('pgsql:dbname=postgres;host=' . $pgsql_db_servers->host . ';port=' . $pgsql_db_servers->port . ';user=' . $pgsql_db_servers->username . ';password=' . trim($this->crypto->bdecode($pgsql_db_servers->password)));
                    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stm = $dbh->query("select t1.datname AS dbname, pg_database_size(t1.datname) as size from pg_database t1 where t1.datname = '$postgresql_database->name'
                                    order by pg_database_size(t1.datname) desc;");

                    $result = $stm->fetch(PDO::FETCH_ASSOC);
                    $pgsql_db_size += $result["size"];
                } catch (PDOException $e) {
                }
            }
        }

        foreach ($mssql_databases as $mssql_database) {
            $mssql_db_servers = DatabaseServer::findFirstById($mssql_database->database_server_id);
            if ($mssql_db_servers) {
                try {
                    $serverName = "$mssql_db_servers->host, $mssql_db_servers->port";
                    $connectionInfo = array("UID" => $mssql_db_servers->username, "PWD" => trim($this->crypto->bdecode($mssql_db_servers->password)));
                    $conn = sqlsrv_connect($serverName, $connectionInfo);
                    if ($conn) {
                        $response = sqlsrv_query($conn, "SELECT 
      database_name = DB_NAME(database_id)
    , log_size = CAST(SUM(CASE WHEN type_desc = 'LOG' THEN size END) * 8 AS DECIMAL(8,2))
    , row_size = CAST(SUM(CASE WHEN type_desc = 'ROWS' THEN size END) * 8 AS DECIMAL(8,2))
    , total_size = CAST(SUM(size) * 8 AS DECIMAL(8,2))
FROM sys.master_files WITH(NOWAIT)
WHERE database_id = DB_ID('$mssql_database->name')
GROUP BY database_id;");
                        $result = sqlsrv_fetch_array($response, SQLSRV_FETCH_ASSOC);
                        if (!empty($result)) {
                            $total_ms_size = $result["total_size"];
                            $total_ms_size = $total_ms_size * 1024;
                            $mssql_db_size += $total_ms_size;
                        }
                        sqlsrv_close($conn);
                    }
                } catch (PDOException $e) {
                }
            }
        }

        $total_db_disk += $mysql_db_size;
        $total_db_disk += $pgsql_db_size;
        $total_db_disk += $mssql_db_size;
        return $total_db_disk;
    }
}