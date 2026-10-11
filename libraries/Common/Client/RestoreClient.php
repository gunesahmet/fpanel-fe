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
 */namespace FPanel\Common\Client;

use FPanel\Common\Base;
use FPanel\Environments\Smtp;
use FPanel\Models\Backup;
use FPanel\Models\SystemSettings;
use FPanel\Models\Users;
use FPanel\Process\BackupRestore;

/**
 * Class RestoreClient
 * @package FPanel\Common\Client
 */
class RestoreClient extends Base
{
    // Ana gereksinimler
    private $user_name;
    private $create_date;
    private $backup_name;
    private $user_id;
    private $backup_id;
    private $repository;
    private $restore_contents = [];
    private $progress_data = [];
    private $email;
    private $user_type;
    private $backup_config = array();

    public function restore($status, $backup_id): bool
    {
        if($this->get($backup_id) == false)
            return false;

        switch($status) {
            case "extract":
                $this->restoreUsers();
                break;
            case "user-extract":
                if($this->createClientDomains()) {
                    $this->restoreClientDomains();
                }
                break;
            case "domains":
                if($this->createDomains()) {
                    $this->restoreDomains();
                }
                break;
            case "finish":
                $this->finishRestore();
                if(REDIS_STATUS == true) {
                    if($this->redis->exists('Users'))
                        $this->redis->del("Users");
                }
                break;
        }
        return true;
    }

    private function get($backup_id): bool
    {
        $backup = Backup::findFirstById($backup_id);
        if($backup) {
            $this->backup_id = $backup_id;
            $this->backup_name = $backup->name;
            $this->user_id = $backup->client_id;
            $this->repository = $backup->repository;
            $this->create_date = $backup->created_date;
            $this->email = $backup->email;
            $this->restore_contents = json_decode($backup->restore_contents, true);
            if(json_last_error() != JSON_ERROR_NONE) {
                $backup->status = RESTORE_FAILED;
                if(!$backup->update()) {
                    $error_messages = [];
                    foreach ($backup->getMessages() as $message) {
                        $error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "message" => "RestoreClient",
                        "data" => "Backup get update failed",
                        "errors" => $error_messages
                    ]);
                }
                $this->log->error([
                    "Module" => "RestoreClient",
                    "Action" => "Restore content data set error",
                    "Messages" => json_last_error_msg(),
                    "line" => __LINE__,
                    "file" => str_replace(ROOTPATH,"", __FILE__)
                ]);
                return false;
            }

            if($backup->progress_data != null) {
                $this->progress_data = json_decode($backup->progress_data, true);
                if(json_last_error() != JSON_ERROR_NONE) {
                    $backup->status = RESTORE_FAILED;
                    if(!$backup->update()) {
                        $error_messages = [];
                        foreach ($backup->getMessages() as $message) {
                            $error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "message" => "RestoreClient",
                            "data" => "Backup get update failed",
                            "errors" => $error_messages
                        ]);
                    }
                    $this->log->error([
                        "Module" => "RestoreClient",
                        "Action" => "Backup progress data set error",
                        "Messages" => json_last_error_msg(),
                        "line" => __LINE__,
                        "file" => str_replace(ROOTPATH,"", __FILE__)
                    ]);
                    return false;
                }
            }

            $user = Users::findFirstById($this->user_id);
            if($user) {
                $this->user_name = $user->username;
                $this->user_type = $user->type;
            }
            else {
                $this->log->error([
                    "Module" => "RestoreClient",
                    "Action" => "Backup read config",
                    "Messages" => "Backup user not found",
                ]);
                return false;
            }

            $backupmng = new BackupRestore();
            $config = $backupmng->getConfig([
                "server-addr" => $this->request->getServerAddress(),
                "name" => $this->backup_name,
                "backup-path" => PANEL_BACKUP_DIR . "/clients/$this->user_name/" . $this->date->getTimeStamp($this->create_date) . "/",
            ]);
            $this->backup_config = json_decode($this->crypto->fdecode($config), true);
            if(json_last_error() != JSON_ERROR_NONE) {
                $backup->status = RESTORE_FAILED;
                if(!$backup->update()) {
                    $error_messages = [];
                    foreach ($backup->getMessages() as $message) {
                        $error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "message" => "RestoreClient",
                        "data" => "Backup get update failed",
                        "errors" => $error_messages
                    ]);
                }
                $this->log->error([
                    "Module" => "RestoreClient",
                    "Action" => "Backup read config",
                    "Messages" => "Backup config cant decode",
                ]);
                return false;
            }
            return true;
        }
        else {
            $this->log->error([
                "Module" => "RestoreClient",
                "Action" => "Backup update",
                "Messages" => "Backup not found",
            ]);
            return false;
        }
    }

    private function restoreUsers()
    {
        $restorable_users = $this->backup_config["users"];
        $selected_domains = $this->restore_contents["domain_list"];

        $restore_users = [];
        foreach($selected_domains as $selected_domain) {
            foreach ($restorable_users as $restorable_user => $user_domains) {
                foreach ($user_domains as $user_domain) {
                    if($user_domain == $selected_domain) {
                        $restore_users[$restorable_user] = true;
                    }
                }
            }
        }
        $extracting_users = "";
        // TODO: Restore edilecek userlari backup db sine yazki daha sonra cekebilesin
        foreach ($restore_users as $key => $value) {
            $backupmng = new BackupRestore();
            $config = $backupmng->getConfig([
                "server-addr" => $this->request->getServerAddress(),
                "name" => $key,
                "backup-path" => PANEL_BACKUP_DIR . "/clients/$this->user_name/". $this->date->getTimeStamp($this->create_date) ."/.tmp/users/",
            ]);

            $config = json_decode($this->crypto->fdecode($config), true);
            if(json_last_error() != JSON_ERROR_NONE) {
                $this->log->error([
                    "Module" => "RestoreClient User",
                    "Action" => "Backup restore",
                    "Messages" => "Client user: $key backup config cant read",
                ]);
                continue;
            }
            else {
                $users = new \FPanel\Common\Client\Users();
                $parent = array(
                    'id' => $this->user_id,
                    'type' => $this->user_type
                );
                $created_user_id = $users->restoreUser($config["user_data"], $parent);
                if(!$created_user_id) {
                    $this->log->error([
                        "Module" => "RestoreClient User",
                        "Action" => "Backup restore",
                        "Messages" => "Client user: $key cant restore",
                    ]);
                }
                else {
                    $restore_users[$key] = [
                        "user_id" => $created_user_id,
                        "config" => $this->crypto->fencode(json_encode($config))
                    ];
                }
            }
            if($extracting_users == null) {
                $extracting_users = $key;
            }
            else {
                $extracting_users .= "#".$key;
            }
        }


        $backup = Backup::findFirstById($this->backup_id);
        $backup->progress_data = json_encode($restore_users);
        if(!$backup->update()) {
            $error_messages = [];
            foreach ($backup->getMessages() as $message) {
                $error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "RestoreClient",
                "Action" => "Backup update",
                "Messages" => $error_messages,
                "line" => __LINE__,
                "file" => str_replace(ROOTPATH,"", __FILE__)
            ]);
        }
        else {
            $this->progress_data = $restore_users;
            if(empty($this->progress_data)) {
                $this->log->error([
                    "Module" => "RestoreClient Domain",
                    "Action" => "Progress data is empty",
                    "line" => __LINE__,
                    "file" => str_replace(ROOTPATH,"", __FILE__)
                ]);
            }

            $backupmng = new BackupRestore();
            $backupmng->extractSubUsers([
                "server-addr" => $this->request->getServerAddress(),
                "client-name" => $this->user_name,
                "client-sub-names" => $extracting_users,
                "name" => $this->backup_name,
                "date" => $this->date->getTimeStamp($this->create_date),
                "backup-id" => $this->backup_id,
            ]);
        }
    }

    private function createClientDomains(): bool
    {
        if((isset($this->restore_contents["domain_list"])) && (!empty($this->restore_contents["domain_list"]))) {
            if(empty($this->progress_data)) {
                $this->log->error([
                    "Module" => "RestoreClient Domain",
                    "Action" => "Progress data is empty",
                    "line" => __LINE__,
                    "file" => str_replace(ROOTPATH,"", __FILE__)
                ]);
            }
            foreach ($this->progress_data as $client_name => $progress_data) {
                $data = json_decode($this->crypto->fdecode($progress_data["config"]), true);
                if(json_last_error() != JSON_ERROR_NONE) {
                    $this->log->error([
                        "Module" => "RestoreClient User",
                        "Action" => "Backup restore",
                        "Messages" => "Client user: $client_name backup config cant read",
                    ]);
                    continue;
                }

                $backup_config = $data["user_data"];
                foreach ($backup_config["domains"] as $domain) {
                    if(in_array($domain["domain_name"], $this->restore_contents["domain_list"])) {
                        $backupmng = new BackupRestore();
                        $config = $backupmng->getConfig([
                            "server-addr" => $this->request->getServerAddress(),
                            "name" => $domain["domain_name"],
                            "backup-path" => PANEL_BACKUP_DIR . "/clients/$this->user_name/". $this->date->getTimeStamp($this->create_date) ."/.tmp/users/$client_name/domains/$domain[domain_name]",
                        ]);

                        $decoded_config = json_decode($this->crypto->fdecode($config), true);
                        if(json_last_error() != JSON_ERROR_NONE) {
                            $this->log->error([
                                "Module" => "RestoreClient User Domain",
                                "Action" => "Backup restore domain",
                                "Messages" => "Domain: $domain[domain_name] backup config cant read",
                            ]);
                            continue;
                        }
                        $user = new \FPanel\Common\Client\Users();
                        $user->restoreDomain($decoded_config, $progress_data["user_id"], CLIENT_USER);
                    }
                }
            }
        }
        return true;
    }

    public function restoreClientDomains()
    {
        $client_ids = [];
        $client_names = [];
        if(empty($this->progress_data)) {
            $this->log->error([
                "Module" => "RestoreClient Domain",
                "Action" => "Progress data is empty",
                "line" => __LINE__,
                "file" => str_replace(ROOTPATH,"", __FILE__)
            ]);
        }
        foreach ($this->progress_data as $name => $progress_data) {
            $client_ids[] = $progress_data["user_id"];
            $client_names[] = $name;
        }
        if(!empty($client_ids)) {
            $backupmng = new BackupRestore();
            $params = array(
                "date" => $this->date->getTimeStamp($this->create_date),
                'name' => $this->backup_name,
                'client-name' => $this->user_name,
                'backup-id' => $this->backup_id,
                'client-ids' => implode("#", $client_ids),
                'client-names' => implode("#", $client_names),
            );
            $params["server-addr"] = $this->request->getServerAddress();
            $backupmng->restoreClientDomains($params);
        }
        else {
            $this->log->error([
                "Module" => "RestoreClient Domain",
                "Action" => "ClientIDs empty",
                "line" => __LINE__,
                "file" => str_replace(ROOTPATH,"", __FILE__)
            ]);
        }
    }

    public function createDomains(): bool
    {
        foreach ($this->backup_config["domains"] as $domain) {
            if(in_array($domain, $this->restore_contents["domain_list"])) {
                $backupmng = new BackupRestore();
                $config = $backupmng->getConfig([
                    "server-addr" => $this->request->getServerAddress(),
                    "name" => $domain,
                    "backup-path" => PANEL_BACKUP_DIR . "/clients/$this->user_name/". $this->date->getTimeStamp($this->create_date) ."/.tmp/domains/",
                ]);
                $decoded_config = json_decode($this->crypto->fdecode($config), true);
                if(json_last_error() != JSON_ERROR_NONE) {
                    $this->log->error([
                        "Module" => "RestoreClient Domain",
                        "Action" => "Backup restore domain",
                        "Messages" => "Domain: $domain backup config cant read",
                    ]);
                    continue;
                }

                $user = new \FPanel\Common\Client\Users();
                $user->restoreDomain($decoded_config, $this->user_id, $this->user_type);
            }
        }
        return true;
    }

    public function restoreDomains()
    {
        $client_ids = [];
        foreach ($this->progress_data as $progress_data) {
            $client_ids[] = $progress_data["user_id"];
        }
        if(!empty($client_ids)) {
            $backupmng = new BackupRestore();
            $params = array(
                "date" => $this->date->getTimeStamp($this->create_date),
                'name' => $this->backup_name,
                'client-name' => $this->user_name,
                'backup-id' => $this->backup_id,
                'client-id' => $this->user_id,
            );
            $params["server-addr"] = $this->request->getServerAddress();
            $backupmng->restoreClientMainDomains($params);
        }
    }

    private function finishRestore()
    {
        $backup = Backup::findFirstById($this->backup_id);
        $backup->status = "1";
        $backup->progress_data = "";
        $backup->restore_contents = "";
        if(!$backup->update()) {
            $error_messages = [];
            foreach ($backup->getMessages() as $message) {
                $error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "RestoreClient Finish",
                "Action" => "Backup update",
                "Messages" => $error_messages,
            ]);
        }
        $this->sendMail();

        $backupmng = new BackupRestore();
        $params = array(
            "date" => $this->date->getTimeStamp($this->create_date),
            'name' => $this->backup_name,
            'client-name' => $this->user_name,
            'backup-id' => $this->backup_id,
            'data' => "clear",
        );
        $params["server-addr"] = $this->request->getServerAddress();

        $backupmng->restoreClient($params);
    }

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
        $smtp->password = trim($this->crypto->bdecode($params['password']));
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
        $data = "Your restore process is completed. Please check your data.";
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

}