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
 */namespace FPanel\Common\Domain;

use FPanel\Domain\Cronjobs;
use FPanel\Domain\DomainAlias;
use FPanel\Domain\DomainDatabase;
use FPanel\Domain\Mails;
use FPanel\Domain\NamedService;
use FPanel\Domain\Protectdir;
use FPanel\Domain\Subdomain;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseUsers;
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\IpAddress;
use FPanel\Models\Mails as Mail;
use FPanel\Models\ProtectDirectory;
use FPanel\Models\ProtectDirectoryUser;
use FPanel\Models\Subdomains;
use FPanel\Process\SqlManager;
use Phalcon\Mvc\User\Component;

class Builder extends Component
{
    public $subdomain_list = null;
    public $mail_list = null;
    public $sub_users = null;

    public function restoreDomainAlias($domain_id, $domain_name, $domain_aliases): Builder
    {
        if(!empty($domain_aliases)) {
            foreach($domain_aliases as $alias) {
                $alias_name = $alias['alias_name'];
                $alias_check = \FPanel\Models\DomainAlias::findFirst("alias_name = '$alias_name'");
                if(!$alias_check) {
                    $domain_alias = new DomainAlias();
                    $post_data = array(
                        'update'            => false,
                        'domain_name'       => $domain_name,
                        'domain_id'         => $domain_id,
                        'alias_name'        => $alias['alias_name'],
                        'syn_pri_dom'       => "1",
                        'dns'               => "1",
                        'status'            => "1",
                        'mail'              => $alias['mail'],
                        'web'               => $alias['web'],
                        'old_alias'         => "",
                    );

                    $domain_alias->setPosts($post_data);
                    $domain_alias->createAlias();
                }
            }
        }
        return $this;
    }

    public function restoreDomainSubs($domain_id, $domain_name, $subdomains): Builder
    {
        if(!empty($subdomains)) {
            $domain = Domains::findFirst("name = '$domain_name'");
            $ftpusers = FtpUsers::findFirstById($domain->ftp_user_id);
            $sys_ip_address = IpAddress::findFirstById($domain->ipaddr_id);
            $domain_name = $domain->name;
            $domain_ftp_user_id = $domain->ftp_user_id;
            $domain_ftpname = $ftpusers->username;
            $domain_ip_addr = $sys_ip_address->ip_address;
            $domain_ip_addr_id = $domain->ipaddr_id;
            $domain_parent = $domain->client_id;

            // Once create edilmemis olanlar create ediliyor
            foreach($subdomains as $subdomain) {
                $sub = Subdomains::findFirst("subdomain_name = '$subdomain[subdomain_name]' AND domain_id = '$domain_id'");
                if($sub) {
                    continue;
                }


                $ftp_type = false;
                if($subdomain["ftpuser_type"] != MAIN) {
                    $ftp_type = true;
                }
                $post_data = array(
                    'domain_id' => $domain_id,
                    'domain_name' => $domain_name,
                    'domain_ftpuser' => $domain_ftpname, // Domaine ait ftp user
                    'ipaddress' => $domain_ip_addr, // Domaine ait ipadresi
                    'domain_ftp_user_id' => $domain_ftp_user_id, // Domaine ait ftp user idsi
                    'domain_parent' => $domain_parent, // Domainin sahibinin idsi
                    'ipaddr_id' => $domain_ip_addr_id, // Domainin ip adresinin idsi

                    'subdomain_name' => $subdomain['subdomain_name'],
                    'full_name' => $subdomain['full_name'],
                    'status' => ACTIVE,

                    // Özel kural devreye girecek
                    'use_native_ftp' => $ftp_type,
                    'ftpname'   => $subdomain['ftpname'],
                    'password'   => $subdomain['password'],

                    // Subdomain host parametreleri
                    'php'               => $subdomain['php'],
                    'php_version'       => $subdomain['php_version'],
                    'asp'               => $subdomain['asp'],
                    'aspnet_core'       => $subdomain['aspnet_core'],
                    'aspnet'            => $subdomain['aspnet'],
                    'aspnet_version'    => $subdomain['aspnet_version'],
                    'cgi'               => $subdomain['cgi'],
                    'perl'              => $subdomain['perl'],
                    'python'            => $subdomain['python'],
                    'dedicated_pool'    => $subdomain['dedicated_pool'],
                    'max_cpu'           => $subdomain['max_cpu'],
                    'ssl'               => PASSIVE,
                );
                $subdomain = new Subdomain();
                $subdomain->setPosts($post_data);
                $result = $subdomain->createSubdomain(true);
                if ($result == false) {
                    $this->log->error([
                        "Module" => "Domain\\Builder",
                        "Action" => "createSubdomain",
                        "Message" => $subdomain->getMessages()
                    ]);
                }
            }

            foreach($subdomains as $subdomain) {
                if($this->subdomain_list == null)
                    $this->subdomain_list = $subdomain['subdomain_name'];
                else
                    $this->subdomain_list = $this->subdomain_list.'#'.$subdomain['subdomain_name'];



                $sub = Subdomains::findFirst("subdomain_name = '$subdomain[subdomain_name]' AND domain_id = '$domain_id'");
                if(!$sub) {
                    $this->log->error([
                        "Module" => "Subdomain",
                        "Action" => "Get",
                        "Message" => "Subdomain not found while in restoring"
                    ]);
                    continue;
                }

                if($this->sub_users == null)
                    $this->sub_users = $sub->getFtpUsers()->username;
                else
                    $this->sub_users = $this->sub_users.'#'.$sub->getFtpUsers()->username;
            }
        }
        return $this;
    }

    public function restoreClientDomainDatabases($domain_id, $domain_name, $databases): Builder
    {
        $domain = Domains::findFirstById($domain_id);
        $pgsql_server = DatabaseServer::findFirst("type = 'pgsql' and status = '1'");
        $mysql_server = DatabaseServer::findFirst("type = 'mysql' and status = '1'");
        $mssql_server = DatabaseServer::findFirst("type = 'mssql' and status = '1'");
        foreach($databases as $database) {
            $domain_database = new DomainDatabase();
            $created_database = Databases::findFirst("name = '$domain->prefix". "_" ."$database[clear_name]' AND type = '$database[type]' AND domain_id = '$domain_id'");
            if($created_database) {
                $domain_database->deleteDatabase($created_database->id, $domain_id);
            }
        }

        foreach($databases as $database) {
            if($database["type"] == "mysql") {
                $sql_server = $mysql_server;
            }
            else if($database["type"] == "pgsql") {
                $sql_server = $pgsql_server;
            }
            else if($database["type"] == "mssql") {
                $sql_server = $mssql_server;
            }
            else {
                continue;
            }

            $domain_database = new DomainDatabase();

            $created_database = Databases::findFirst("name = '$database[name]' AND type = '$database[type]' AND domain_id = '$domain_id'");
            if($created_database) {
                $sql_server = $created_database->getSystemDatabaseServer();
            }

            $db_user_status = DatabaseUsers::findFirst("login = '$database[dbusername]'");
            if($db_user_status) {
                $database['dbusername'] = $this->env->createPrefix();
            }

            $domain_database->setPosts([
                'action' => "create",
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'database_type' => $database["type"],
                'database_server_id' => $sql_server->id,
                'database_name' => $database["clear_name"],
                'db_user_name' => $database["dbusername"],
                'db_user_password' => $database["dbpassword"],
                'prefix' => $domain->prefix,
            ]);
            $domain_database->createDatabase(true);
        }
        return $this;
    }

    public function restoreDomainDatabases($domain_id, $domain_name, $databases, $date_folder): Builder
    {
        $domain = Domains::findFirstById($domain_id);
        $sqlmng = new SqlManager();
        $pgsql_server = DatabaseServer::findFirst("type = 'pgsql' and status = '1'");
        $mysql_server = DatabaseServer::findFirst("type = 'mysql' and status = '1'");
        $mssql_server = DatabaseServer::findFirst("type = 'mssql' and status = '1'");
        foreach($databases as $database) {
            $domain_database = new DomainDatabase();
            $created_database = Databases::findFirst("name = '$domain->prefix". "_" ."$database[clear_name]' AND type = '$database[type]' AND domain_id = '$domain_id'");
            if($created_database) {
                $domain_database->deleteDatabase($created_database->id, $domain_id);
            }
        }

        foreach($databases as $database) {
            if($database["type"] == "mysql") {
                $sql_server = $mysql_server;
            }
            else if($database["type"] == "pgsql") {
                $sql_server = $pgsql_server;
            }
            else if($database["type"] == "mssql") {
                $sql_server = $mssql_server;
            }
            else {
                continue;
            }

            $domain_database = new DomainDatabase();

            $created_database = Databases::findFirst("name = '$database[name]' AND type = '$database[type]' AND domain_id = '$domain_id'");
            if($created_database) {
                $sql_server = $created_database->getSystemDatabaseServer();
            }

            $db_user_status = DatabaseUsers::findFirst("login = '$database[dbusername]'");
            if($db_user_status) {
                $database['dbusername'] = $this->env->createPrefix();
            }

            $domain_database->setPosts([
                'action' => "create",
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'database_type' => $database["type"],
                'database_server_id' => $sql_server->id,
                'database_name' => $database["clear_name"],
                'db_user_name' => $database["dbusername"],
                'db_user_password' => $database["dbpassword"],
                'prefix' => $domain->prefix,
            ]);
            $status = $domain_database->createDatabase(true);
            if (!$status) {
                $this->log->error([
                    "module" => "database/restore",
                    "messages" => $domain_database->getMessages(),
                ]);
            }
            else {
                $sqlmng->setServerId($sql_server->server_id);
                if($sql_server->host == "localhost") {
                    $host = "127.0.0.1";
                }
                else {
                    $host = $sql_server->host;
                }
                if($database['type'] == "mysql") {
                    $params = array(
                        'domain'        => $domain_name,
                        'host'          => $host,
                        'port'          => $sql_server->port,
                        'old-dbname'    => $database["name"],
                        'dbname'        => $domain->prefix.'_'.$database["clear_name"],
                        'dbuser'        => $database['dbusername'],
                        'dbpass'        => $database['dbpassword'],
                        'date'          => $date_folder,
                        'backup-path'   => PANEL_BACKUP_DIR . "/$domain_name/" . $date_folder . "/.tmp/database",
                        'dump-type'     => "domain",
                        'type'          => "mysql",
                    );
                    $sqlmng->restore($params);
                }
                else if($database['type'] == "mssql") {
                    $backup_dir = PANEL_BACKUP_DIR . DIRECTORY_SEPARATOR;
                    if(PANEL_OS_TYPE == WINDOWS) {
                        $backup_dir = $this->env->replaceAll("\\","/", $backup_dir);
                    }
                    $backup_dir = $this->env->replaceAll("//","/", $backup_dir);

                    $backup_path = $backup_dir . "/$domain_name/" . $date_folder . "/.tmp/database/";
                    $backup_path = $this->env->replaceAll("//","/", $backup_path);
                    $backup_path = $this->env->replaceAll("/","\\", $backup_path);
                    $serverName = "$host, $sql_server->port"; //serverName\instanceName, portNumber (default is 1433)

                    $connectionInfo = array( "UID" => $sql_server->username, "PWD" => $this->crypto->bdecode($sql_server->password));
                    sqlsrv_configure("WarningsReturnAsErrors", 0);
                    $conn = sqlsrv_connect( $serverName, $connectionInfo);
                    if ($conn) {
                        $db_name = $domain->prefix.'_'.$database["clear_name"];
                        $old_db_name = $database["name"];
                        $stmt = sqlsrv_query($conn, "USE [master];
                        ALTER DATABASE $db_name SET RECOVERY FULL; 
                        RESTORE DATABASE [$db_name] FROM  DISK = N'$backup_path.$old_db_name.dump' WITH  FILE = 1,  NOUNLOAD,  STATS = 5");

                        do {
                        } while (sqlsrv_next_result($stmt) ) ;
                        if($stmt === false) {
                            $this->log->error([
                                "message" => "BackupDomain/SQL",
                                "err" => sqlsrv_errors(SQLSRV_ERR_ALL),
                            ]);
                        }
                        sqlsrv_close($conn);
                    }
                }
                else if($database['type'] == "pgsql") {
                    $params = array(
                        'domain'        => $domain_name,
                        'host'          => $host,
                        'port'          => $sql_server->port,
                        'old-dbname'    => $database["name"],
                        'dbname'        => $domain->prefix.'_'.$database["clear_name"],
                        'dbuser'        => $database['dbusername'],
                        'dbpass'        => $database['dbpassword'],
                        'date'          => $date_folder,
                        'backup-path'   => PANEL_BACKUP_DIR . "/$domain_name/" . $date_folder . "/.tmp/database" ,
                        'dump-type'     => "domain",
                        'type'      => "pgsql",
                    );
                    $sqlmng->restore($params);
                }
            }
        }
        return $this;
    }

    public function restoreDomainMails($domain_id, $domain_name, $mails): Builder
    {
        foreach($mails as $mail) {
            $mail_account = new Mails();
            $mail_status = Mail::findFirst("mail_name = '$mail[mail_name]' AND domain_id = '$domain_id'");
            if($mail_status) {
                continue;
            }

            $unlimited = true;
            if($mail['mailbox_quota'] != UNLIMITED)
                $unlimited = false;

            $post_data = array(
                'domain_name'       => $domain_name,
                'domain_id'         => $domain_id,
                'mail_account'      => $mail['mail_name'],
                'password'          => $mail['password'],
                'mailbox_quota'     => $mail['mailbox_quota'],
                'unlimited_quota'   => $unlimited,
                'status'            => ($mail['status'] ?? "on"),
            );
            $mail_account->setPosts($post_data);
            $mail_account_status = $mail_account->createMailAccount(true);
            if($mail_account_status == true) {
                $mail_model = Mail::findFirst("mail_name = '$mail[mail_name]'");
                $mail_id = $mail_model->id;

                $post_data = array(
                    'mail_id'           => $mail_id,
                    'domain_name'       => $domain_name,
                    'domain_id'         => $domain_id,
                    'mail_account'      => $mail['mail_name'],
                    'password'          => $mail['password'],
                    'mailbox_quota'     => $mail['mailbox_quota'],
                    'unlimited_quota'   => $unlimited,
                    'forward'           => $mail['forward'],
                    'autoresponder'     => $mail['autoresponder'],
                    'forward_addr'      => $mail['forward_addr'],
                    'old_address'       => $mail['mail_name'],
                );
                $mail_account->setPosts($post_data);
                $mail_account->updateMailAccount();

                if(!empty($mail['mail_alias'])) {
                    foreach($mail['mail_alias'] as $mail_alias) {
                        $post_data = array(
                            'mail_account'    => $mail['mail_name'],
                            'domain_name'     => $domain_name,
                            'domain_id'       => $domain_id,
                            'mail_id'         => $mail_id,
                            'alias_name'      => $mail_alias
                        );
                        $mail_account->setPosts($post_data);
                        $mail_account->createMailAlias(true);
                    }
                }
            }
        }

        $mail_list = "";
        foreach($mails as $mail) {
            if ($mail_list == null)
                $mail_list = $mail["mail_name"];
            else
                $mail_list = $mail_list . "#" . $mail["mail_name"];
        }
        $this->mail_list = $mail_list;
        return $this;
    }

    public function restoreDomainProtectDirs($domain_id, $domain_name, $protect_dirs): Builder
    {
        $protectdir = new Protectdir();
        foreach($protect_dirs as $pro_dir) {
            $pro_status = ProtectDirectory::findFirst("path = '$pro_dir[path]' AND domain_id = '$domain_id'");
            if(!$pro_status) {
                $sub_id = "0";
                if((array_key_exists("subdomain", $protect_dirs)) && ($pro_dir['subdomain'] != null)) {
                    $subdomains = Subdomains::findFirst("subdomain_name = '$pro_dir[subdomain]'");
                    $sub_id = $subdomains->id;
                }
                $post_data = array(
                    'domain_id'     => $domain_id,
                    'path'          => $pro_dir['path'],
                    'ssl'           => PASSIVE,
                    'cgi_bin'       => PASSIVE,
                    'header'        => $pro_dir['header'],
                    'subdomain_id'  => $sub_id,
                );

                $protectdir->domain_name = $domain_name;
                $protectdir->setPosts($post_data);

                $protectdir_save_status = $protectdir->saveProtectdir(true);
                $protect_id = $protectdir_save_status;
            }
            else {
                $protect_id = $pro_status->id;
            }

            if(is_numeric($protect_id)) {
                foreach($pro_dir['users'] as $user) {
                    $pro_user_status = ProtectDirectoryUser::findFirst("username = '$user[username]' AND protect_dir_id = '$protect_id'");
                    if($pro_user_status) {
                        continue;
                    }
                    $post_data = array(
                        'domain_id'         => $domain_id,
                        'protect_dir_id'    => $protect_id,
                        'path'              => $pro_dir['path'],
                        'username'          => $user['username'],
                        'password'          => $user['password'],
                    );
                    $protectdir_user = new Protectdir();
                    $protectdir_user->setPosts($post_data);
                    $protectdir_user->domain_name = $domain_name;
                    $protectdir_user->saveProtectDirUser();
                }
            }
        }
        return $this;
    }

    public function restoreDomainTasks($domain_id, $domain_name, $tasks): Builder
    {
        $domains = Domains::findFirst("name = '$domain_name'");
        $ftp_user_id = $domains->ftp_user_id;

        foreach($tasks as $task) {
            $status = false;
            if($task['status'] == "1") {
                $status = true;
            }
            $post_data = array(
                // Task Status
                'ftp_user_id'           => $ftp_user_id,
                'domain_id'             => $domain_id,
                'domain_name'           => $domain_name,
                'status'                => $status,
                'minute'                => $task['minute'],
                'hour'                  => $task['hour'],
                'day'                   => $task['day'],
                'week'                  => $task['week'],
                'month'                 => $task['month'],
                'command'               => $task['command'],
                'description'           => $task['description'],
            );
            $task = new Cronjobs();
            $task->setPosts($post_data);
            $task->saveDomain();
        }
        return $this;
    }

    public function restoreDomainDNS($domain_id, $domain_name, $dns_records): Builder
    {
        if(!empty($dns_records)) {
            $dns = new NamedService();
            $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
            $dns_rec = DnsRecords::find("dns_zone_id = '$dns_zone->id'");

            if(!$dns_rec->delete()) {
                return $this;
            }
            foreach($dns_records as $record) {
                $post_data = array(
                    'action' => "create",
                    'update' => false,
                    // Değişkenler
                    'domain_id'   => $domain_id,
                    'domain_name'   => $domain_name,
                    'time_stamp'   => $record['time_stamp'],
                    // Post Dataları
                    'type'   => $record['type'],

                    // NS
                    'ns_host'  => '',
                    'ns_value' => '',
                    'ns_opt' => '',

                    // A
                    'a_host' => '',
                    'a_value' => '',
                    'a_opt' => '',

                    // CNAME
                    'cname_host' => '',
                    'cname_value' => '',
                    'cname_opt' => '',

                    // MX
                    'mx_host'  => '',
                    'mx_value' => '',
                    'mx_opt' => '',

                    // PTR
                    'ptr_host' => '',
                    'ptr_value' => '',
                    'ptr_opt' => '',

                    // TXT
                    'txt_host' => '',
                    'txt_value' => '',
                    'txt_opt' => '',

                    'old_type' => '',
                    'old_host' => '',
                    'old_value' => '',
                );
//                $dns->domain_name = $domain_name;

                switch($record['type']) {
                    case "NS":
                        $post_data['ns_host'] = $record['host'];
                        $post_data['ns_value'] = $record['value'];
                        break;
                    case "A":
                        $post_data['a_host'] = str_replace('.'.$domain_name,"", $record['host']);
                        $post_data['a_value'] = $record['value'];
                        break;

                    case "PTR":
                        $post_data['ptr_host'] = $record['host'];
                        $post_data['ptr_value'] = str_replace('.'.$domain_name,"", $record['value']);
                        $post_data['ptr_opt'] = $record['opt'];
                        break;

                    case "MX":
                        $post_data['mx_opt'] = $record['opt'];
                        $post_data['mx_value'] = $record['value'];
                        $post_data['mx_host'] = $record['host'];
                        break;

                    case "TXT":
                        $post_data['txt_value'] = $record['value'];
                        $post_data['txt_host'] = $record['host'];
                        break;

                    case "CNAME":
                        $post_data['cname_host'] = str_replace('.'.$domain_name,"", $record['host']);
                        $post_data['cname_value'] = $record['value'];
                        break;
                }
                $dns->domain_name = $domain_name;
                $dns->setPosts($post_data);
                $dns->createDNSRecords(true);
            }
            $dns = new NamedService();
            $dns->initCurl($domain_id);
        }
        return $this;
    }

    public function restoreDomainMailParams($domain_id, $domain_name, $mail_params): Builder
    {
        if(!empty($mail_params)) {
            if($mail_params['mail_forward_addr'] == null) {
                $forwarding = false;
            }
            else {
                $forwarding = true;
            }

            if($mail_params['nonexistend_user'] == "reject")
                $reject_incoming = true;
            else {
                $reject_incoming = false;
            }

            $mail_settings = new Mails();
            $post_data = array(
                'domain_name'             => $domain_name,
                'domain_id'             => $domain_id,
                'forward_to_address'    => $forwarding,
                'mail_address'          => $mail_params['mail_forward_addr'],
                'reject_incoming'       => $reject_incoming,
            );
            $mail_settings->domain_name = $domain_name;
            $mail_settings->setPosts($post_data);
            $mail_settings->updateMailSettings();
        }
        return $this;
    }
}