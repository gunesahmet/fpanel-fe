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

use Exception;
use FPanel\Common\Base;
use FPanel\Environments\Smtp;
use FPanel\Models\Backup;
use FPanel\Models\CloudStorage;
use FPanel\Models\Databases;
use FPanel\Models\Domains;
use FPanel\Models\FtpRepo;
use FPanel\Models\SystemSettings;
use FPanel\Process\BackupRestore;
use FPanel\Process\SqlManager;

/**
 * Class BackupDomain
 * @package FPanel\Common\Domain
 */
class BackupDomain extends Base
{
    // Ana gereksinimler
    private $domain_name;
    private $create_date;
    private $backup_name;
    private $domain_id;
    private $domain_prefix;
    private $backup_id;
    private $repository;
    private $ftp_repo = array();
    private $email;
    private $crypted_configs;

    // Json decode işlemi sonrası alınacak yedeklerin true false değerleri
    private $www_content;
    private $mail_content;
    private $dns_content;
    private $database_content;
    private $subdomain_content;

    private $backup_parts;

    private $subdomain_list;
    private $domain_configs = array();
    private $storage_configs = [];

    public function backup($status, $backup_id, $size, $server = null)
    {
        if($server == null) {
            if(!$this->get($backup_id)) {
                $this->log->error([
                    "File" => "BackupDomain",
                    "Error" => "Backup config cant create",
                ]);
                return false;
            }
        }
        switch($status) {
            case "directory":
                if($this->configs() == true) {
                    $this->www();
                    $this->subdomains();
                    $this->mails();
                    $this->databases();
                    $this->backup_parts = $this->backup_parts . "#" . "finish";
                    $this->uploadToStorage(Backup::findFirstById($backup_id));
                    $this->backupFinish($this->backup_parts);
                }
                else {
                    $this->backupFailed();
                }
                break;
            case "finish":
                $this->backupClear($size);
                break;
            default:
                break;
        }
    }

    private function get($backup_id): bool
    {
        $backup = Backup::findFirstById($backup_id);
        if($backup) {
            $this->backup_id = $backup_id;

            $this->backup_name = $backup->name;
            $this->domain_id = $backup->domain_id;
            $this->repository = $backup->repository;
            $this->create_date = $backup->created_date;
            $this->email = $backup->email;

            $content = json_decode($backup->content,true);

            $this->www_content = $content['www_content'];
            $this->database_content = $content['database_content'];
            $this->dns_content = $content['dns_content'];
            $this->mail_content = $content['mail_content'];
            $this->subdomain_content = $content['subdomain_content'];

            if($backup->repository == "ftp") {
                $ftp_repos = FtpRepo::findFirstById($backup->ftp_repository_id);
                if($ftp_repos) {
                    $this->ftp_repo = $ftp_repos->toArray();
                }
                else {
                    $this->repository = "server";
                }
            }

            $domain = Domains::findFirstById($this->domain_id);
            $this->domain_name = $domain->name;
            $this->domain_prefix = $domain->prefix;

            return true;
        }
        else {
            return false;
        }
    }

    private function configs(): bool
    {
        $config = new Data();
        $config->backupdata($this->backup_id)
            ->dns()
            ->domainalias()
            ->cronjobs()
            ->subdomains()
            ->databases()
            ->mails();

        if($config->error_status == false) {
            $domain_configs = $config->domain_data;
            $this->domain_configs = $domain_configs;

            $domain_config_json = json_encode($domain_configs);
            $this->crypted_configs = $this->crypto->fencode($domain_config_json);
        }
        else {
            return false;
        }
        return true;
    }

    private function www()
    {
        if($this->www_content == true) {
            $this->backup_parts = "www";
        }
    }

    private function subdomains()
    {
        if($this->subdomain_content == true) {
            $this->backup_parts = $this->backup_parts . "#" . "subdomains";
            $sub_list = "";
            if(!empty($this->domain_configs['subdomains'])) {
                $subdomains = $this->domain_configs['subdomains'];
                foreach($subdomains as $sub) {
                    if($sub_list == null) {
                        $sub_list = $sub['subdomain_name'];
                    }
                    else {
                        $sub_list = $sub_list . "#" . $sub['subdomain_name'];
                    }
                }
            }
            $this->subdomain_list = $sub_list;
        }
    }

    private function mails()
    {
        if($this->mail_content == true) {
            $this->backup_parts = $this->backup_parts . "#" . "mail";
        }
    }

    private function databases()
    {
        if($this->database_content == true) {
            $dblist = $this->domain_configs['databases'];
            $sqlmng = new SqlManager();
            foreach($dblist as $database) {
                $database_server = Databases::findFirstById($database["id"])->getSystemDatabaseServer();
                $sqlmng->setServerId($database_server->server_id);

                if($database_server->host == "localhost") {
                    $host = "127.0.0.1";
                }
                else {
                    $host = $database_server->host;
                }

                if($database['type'] == "mysql") {
                    $params = array(
                        'domain'        => $this->domain_name,
                        'host'          => $host,
                        'port'          => $database_server->port,
                        'dbname'        => $database['name'],
                        'dbuser'        => $database['dbusername'],
                        'dbpass'        => $database['dbpassword'],
                        'date'          => $this->date->getTimeStamp($this->create_date),
                        'backup-path'   => PANEL_BACKUP_DIR . "/$this->domain_name/" . $this->date->getTimeStamp($this->create_date) . "/database/" ,
                        'dump-type'     => DOMAIN,
                        'type'          => "mysql",
                    );
                    $sqlmng->dump($params);
                }
                else if($database['type'] == "mssql") {
                    $backup_dir = PANEL_BACKUP_DIR . DIRECTORY_SEPARATOR;
                    if(PANEL_OS_TYPE == WINDOWS) {
                        $backup_dir = $this->env->replaceAll("\\","/", $backup_dir);
                    }
                    $backup_dir = $this->env->replaceAll("//","/", $backup_dir);
                    $params = array(
                        'domain'    => $this->domain_name,
                        'host'      => $host,
                        'port'      => $database_server->port,
                        'dbname'    => $database['name'],
                        'dbuser'    => $database['dbusername'],
                        'dbpass'    => $database['dbpassword'],
                        'date'      => $this->date->getTimeStamp($this->create_date),
                        'backup-path'   => $backup_dir . "/$this->domain_name/" . $this->date->getTimeStamp($this->create_date) . "/database/" ,
                        'dump-path'     => "",
                        'dump-type'     => DOMAIN,
                        'type'          => "mssql",
                    );
                    $serverName = "$database_server->host, $database_server->port"; //serverName\instanceName, portNumber (default is 1433)

                    $connectionInfo = array( "UID" => $database_server->username, "PWD" => $this->crypto->bdecode($database_server->password));
                    sqlsrv_configure("WarningsReturnAsErrors", 0);
                    $conn = sqlsrv_connect( $serverName, $connectionInfo);
                    if ($conn) {
                        $fstmt = sqlsrv_query($conn, "DECLARE @filepath NVARCHAR(260)
                                                        EXEC master.dbo.xp_instance_regread 
                                                                N'HKEY_LOCAL_MACHINE', N'Software\Microsoft\MSSQLServer\MSSQLServer', 
                                                                N'DefaultData', 
                                                                @filepath output, 'no_output' 
                                                        SELECT @filepath as path");
                        $result = sqlsrv_fetch_array($fstmt, SQLSRV_FETCH_ASSOC);
                        if(!empty($result)) {
                            $data_path = $result["path"];
                            $data_path = trim($data_path,"\\");
                            $data_path_exp = explode("\\", $data_path);
                            unset($data_path_exp[count($data_path_exp) -1]);
                            $new_data_path = implode("\\", $data_path_exp);
                            $new_data_path .= "\\Backup";
                            sqlsrv_query($conn, "declare 
                                                    @dirPath varchar(1024) 
                                                    set @dirPath = '$new_data_path';
                                                    exec xp_create_subdir @dirPath;");

                            $db_name = $database["name"];
                            $backup_path = $new_data_path ."\\". $db_name;
                            $tstmt = sqlsrv_query($conn, "use master; ALTER DATABASE $db_name SET RECOVERY FULL; BACKUP DATABASE [$db_name] TO  DISK = N'$backup_path.dump' WITH NOFORMAT, NOINIT, NAME = N'$db_name-Full Database Backup', SKIP, NOREWIND, NOUNLOAD, STATS = 10;");
                            do {
                            } while ( sqlsrv_next_result($tstmt) ) ;
                            if($tstmt === false) {
                                $this->log->error([
                                    "message" => "BackupDomain/SQL",
                                    "data" => $result,
                                    "err" => sqlsrv_errors(SQLSRV_ERR_ALL),
                                ]);
                            }
                            sqlsrv_close($conn);
                            $params["dump-path"] = $backup_path . ".dump";
                        }
                    }
                    //@TODO: Write agent functions for backupmng transfer to backup server and zip with together full backup
                    $sqlmng->dump($params);
                }
                else if($database['type'] == "pgsql") {
                    $params = array(
                        'domain'    => $this->domain_name,
                        'host'      => $host,
                        'port'      => $database_server->port,
                        'dbname'    => $database['name'],
                        'dbuser'    => $database['dbusername'],
                        'dbpass'    => $database['dbpassword'],
                        'date'      => $this->date->getTimeStamp($this->create_date),
                        'backup-path'   => PANEL_BACKUP_DIR . "/$this->domain_name/" . $this->date->getTimeStamp($this->create_date) . "/database/" ,
                        'dump-type'     => DOMAIN,
                        'type'          => "pgsql",
                    );
                    $sqlmng->dump($params);
                }
            }
        }
    }

    private function backupClear($size)
    {
        $backup = Backup::findFirstById($this->backup_id);
        $backup->assign(array(
            'status' => BACKUP_FINISHED,
            'progress'  => '100',
            'sizes' => json_encode($size)
        ));
        if(!$backup->update()) {
            $error_messages = [];
            foreach ($backup->getMessages() as $message) {
                $error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "message" => "BackupDomain",
                "data" => "Backup clear update failed",
                "errors" => $error_messages
            ]);
        }
        $this->sendMail();
    }

    private function backupFailed()
    {
        $backup = Backup::findFirstById($this->backup_id);
        if($backup) {
            $backup->assign(array(
                'status' => BACKUP_FAILED,
            ));
            if(!$backup->update()) {
                $error_messages = [];
                foreach ($backup->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "message" => "BackupDomain",
                    "data" => "Backup failed update failed",
                    "errors" => $error_messages
                ]);
            }
        }
    }

    private function backupFinish($part)
    {
        $backupmng = new BackupRestore();
        $params = array(
            'domain' => $this->domain_name,
            'prefix' => $this->domain_prefix,
            'date' => $this->date->getTimeStamp($this->create_date),
            'name' => $this->backup_name,
            'backup-id' => $this->backup_id,
            'data' => $part,
            'repo' => $this->repository,
            'domain-conf' => $this->crypted_configs
        );

        if($this->subdomain_list != null)
            $params["sub-list"] = $this->subdomain_list;

        if($this->repository == "ftp") {
            $params['ftp-user'] = $this->ftp_repo['ftp_user'];
            $params['ftp-passwd'] = trim($this->crypto->bdecode($this->ftp_repo['ftp_pass']));
            $params['dest-path'] = $this->ftp_repo['ftp_folder'];
            $params['ftp'] = $this->ftp_repo['ftp_server'];
        }
        else if($this->repository == "storage") {
            $params["repo"] = $this->storage_configs["type"];
            $params = array_merge($params, $this->storage_configs["configs"]);
        }
        $params["server-addr"] = $this->request->getServerAddress();
        $backupmng->backupDomain($params);
    }

    public function upload($data)
    {
        $backup = Backup::findFirstById($data['backup_id']);
        if($backup) {
            if (!isset($data['error']) || ($data['error'] == null)) {
                $backup->status = BACKUP_FINISHED;
                $backup->progress = "100";
                $backup->sizes = json_encode($data["size"]);
            }
            else {
                $backup->status = BACKUP_FAILED;
                $backup->description = "Corrupted";
                $backup->progress = "0";
            }

            if(!$backup->update()) {
                $error_messages = [];
                foreach ($backup->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "message" => "BackupDomain",
                    "data" => "Backup upload update failed",
                    "errors" => $error_messages
                ]);
            }
        }

    }

    // Send Status Mail

    public function sendMail(): bool
    {
        if($this->email == null)
            return false;

        $misc = SystemSettings::findFirst("key = 'notyfy'");
        $params = json_decode($misc->value,true);

        if($params['email'] == null)
            return false;

        $smtp = new Smtp();

        $smtp->username = $params['email'];
        try {
            $smtp->password = trim($this->crypto->bdecode($params['password']));
        }
        catch(Exception $e) {
            return false;
        }
        $smtp->host = $params['host'];
        $smtp->port = $params['port'];

        $smtp->from = [
            $params['email'] => $params['from_fullname']
        ];

        $smtp->recipients[] = [
            "address" => $this->email,
            "name" => "System Message",
        ];

        $smtp->subject = "System Message";
        $smtp->message = $this->emailTemplate();


        $smtp->is_html = true;
        $smtp->secure = "tls";
        $smtp->connection_timeout = 5;
        return $smtp->Send();
    }

    private function emailTemplate(): string
    {
        $data = "Your backup process is completed. Please check your data.";

        return '<!DOCTYPE html>
<html>
<head><meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>System Message</title>
<style>
*{margin:0;padding:0;font-family:"Helvetica Neue",Helvetica,Helvetica,Arial,sans-serif}body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100%!important;height:100%}p.callout{padding:15px;background-color:#ECF8FF;margin-bottom:15px}.callout a{font-weight:700;color:#2BA6CB}table.head-wrap{width:100%}.header.container table td.logo{padding:15px}.header.container table td.label{padding:15px 15px 15px 0}table.body-wrap{width:100%}h1,h2,h3,h4,h5,h6{font-family:HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;line-height:1.1;margin-bottom:15px;color:#000}h3{font-weight:500;font-size:27px}.collapse{margin:0!important}p,ul{margin-bottom:10px;font-weight:400;font-size:14px;line-height:1.6}p.lead{font-size:17px}p.last{margin-bottom:0}.container{display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important}.content{padding:15px;max-width:600px;margin:0 auto;display:block}.content table{width:100%}.clear{display:block;clear:both}@media only screen and (max-width:600px){a[class=btn]{display:block!important;margin-bottom:10px!important;background-image:none!important;margin-right:0!important}div[class=column]{width:auto!important;float:none!important}table.social div[class=column]{width:auto!important}}
</style>
</head>
<body bgcolor="#FFFFFF">
<table class="head-wrap" bgcolor="#999999">
<tr>
<td class="header container">
<div class="content">
<table bgcolor="#999999">
<tr>
</tr>
</table>
</div>
</td>
</tr>
</table>
<table class="body-wrap">
<tr>
<td class="container" bgcolor="#FFFFFF">
<div class="content">
<table>
<tr>
<td>
<h3>System Notifications</h3>
<p>'. $data .'</p>
</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</body>
</html>';
    }

    private function uploadToStorage($backup)
    {
        if($backup->storage_id != null) {
            $storage = CloudStorage::findFirstById($backup->storage_id);
            if ($storage) {
                $params = json_decode($this->crypto->staticDecode($storage->params),true);
                $this->storage_configs = [
                    "type" => $storage->type,
                    "configs" => [
                        "dest-path" => $params['folder'],
                    ],
                ];
                if($storage->type == "dropbox") {
                    $this->storage_configs["configs"]["token"] = $params['access_token'];
                }
                else if($storage->type == "gdrive") {
                    $this->storage_configs["configs"]["project-id"] = $params["project_id"];
                    $this->storage_configs["configs"]["private-key"] = $params["private_key"];
                    $this->storage_configs["configs"]["private-key-id"] = $params["private_key_id"];
                    $this->storage_configs["configs"]["client-email"] = $params["client_email"];
                    $this->storage_configs["configs"]["client-id"] = $params["client_id"];
                    $this->storage_configs["configs"]["client-cert-url"] = $params["client_cert_url"];
                }
            }
        }
    }
}