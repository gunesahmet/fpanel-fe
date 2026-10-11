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
 */namespace FPanel\Common\Server;

use Exception;
use FPanel\Common\Base;
use FPanel\Environments\Smtp;
use FPanel\Models\Backup;
use FPanel\Models\SystemSettings;
use FPanel\Models\Users;
use FPanel\Process\BackupRestore;

/**
 * Class RestoreServer
 * @package FPanel\Common\Server
 */
class RestoreServer extends Base
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
        if(!$this->get($backup_id)) {
            $this->log->error([
                "File" => "RestoreServer",
                "Error" => "Restore config cant get",
            ]);
            return false;
        }

        switch($status) {
            case "extract":
                $status = $this->createUsers();
                break;
            case "finish":
                $status = $this->finishRestore();
                if(REDIS_STATUS == true) {
                    if($this->redis->exists('Users'))
                        $this->redis->del("Users");
                }
                break;
            default:
                return false;
        }
        return $status;
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
                        "message" => "RestoreServer",
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
                            "message" => "RestoreServer",
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
                "backup-path" => PANEL_BACKUP_DIR . "/server//",
                "name" => $this->backup_name,
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
                        "Module" => "RestoreClient",
                        "Action" => "Backup update",
                        "Messages" => $error_messages,
                    ]);
                    return false;
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

    private function createUsers(): bool
    {
        $restorable_users = $this->backup_config["users"];
        $selected_domains = $this->restore_contents["domain_list"];
        $restore_users = [
            1,
        ];

        foreach ($restorable_users["resellers"] as $reseller_name => $reseller_childs) {
            $path = PANEL_BACKUP_DIR . "/server/". $this->date->getTimeStamp($this->create_date) ."/.tmp/users/resellers/";

            $created_user_id = $this->createUser($path, $reseller_name, "admin", true);
            if($created_user_id != false)
                $restore_users[] = $created_user_id;

            if(!empty($reseller_childs["clients"])) {
                foreach ($reseller_childs["clients"] as $client_name => $client_childs) {
                    $cpath = $path . "$reseller_name/clients/";

                    $created_user_id = $this->createUser($cpath, $client_name, $reseller_name, true);
                    if($created_user_id != false)
                        $restore_users[] = $created_user_id;

                    if(!empty($client_childs["domains"])) {
                        foreach ($client_childs["domains"] as $domain) {
                            foreach ($selected_domains as $selected_domain) {
                                if($domain == $selected_domain) {
                                    return $this->createDomain($cpath . $client_name . "/domains/" . $domain . DIR_SEPARATOR, $domain, $client_name);
                                }
                            }
                        }
                    }
                }
            }

            if(!empty($reseller_childs["domains"])) {
                foreach ($reseller_childs["domains"] as $domain) {
                    foreach ($selected_domains as $selected_domain) {
                        if($domain == $selected_domain) {
                            $this->createDomain($path . $reseller_name . "/domains/" . $domain . DIR_SEPARATOR, $domain, $reseller_name);
                        }
                    }
                }
            }
        }

        foreach ($restorable_users["clients"] as $client_name => $client_childs) {
            $path = PANEL_BACKUP_DIR . "/server/". $this->date->getTimeStamp($this->create_date) ."/.tmp/users/clients/";

            $created_user_id = $this->createUser($path, $client_name, "admin", true);
            if($created_user_id != false)
                $restore_users[] = $created_user_id;

            if(!empty($client_childs["domains"])) {
                foreach ($client_childs["domains"] as $domain) {
                    foreach ($selected_domains as $selected_domain) {
                        if($domain == $selected_domain) {
                            $this->createDomain($path . $client_name . "/domains/" . $domain . DIR_SEPARATOR, $domain, $client_name);
                        }
                    }
                }
            }
        }

        if(!empty($restorable_users["domains"])) {
            foreach ($restorable_users["domains"] as $domain) {
                foreach ($selected_domains as $selected_domain) {
                    if($domain == $selected_domain) {
                        $this->createDomain(PANEL_BACKUP_DIR . "/server/" . $this->date->getTimeStamp($this->create_date) . "/.tmp/domains/$domain/", $domain, "admin");
                    }
                }
            }
        }

        $this->restoreDomains($restore_users);
        return true;
    }

    private function createUser($path, $username, $parent_name, $extract = false)
    {
        $backupmng = new BackupRestore();
        $config = $backupmng->getConfig([
            "server-addr" => $this->request->getServerAddress(),
            "name" => $username,
            "backup-path" => $path,
        ]);

        $config = json_decode($this->crypto->fdecode($config), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->log->error([
                "Module" => "RestoreClient User",
                "Action" => "Backup restore",
                "Messages" => "User: $path/$username backup config cant read",
            ]);
            return false;
        }
        else {
            $user = Users::findFirst("username = '$username'");
            if(!$user) {
                $users = new \FPanel\Common\Server\Users();
                $parents = Users::findFirst("username = '$parent_name'");

                $parent = array(
                    'id' => $parents->id,
                    'type' => $parents->type,
                );
                $created_user_id = $users->restoreUser($config["user_data"], $parent);
                if(!$created_user_id) {
                    $this->log->error([
                        "Module" => "RestoreClient User",
                        "Action" => "Backup restore",
                        "Messages" => "User: $username cant restore",
                    ]);
                    return false;
                }
            }
            else {
                $created_user_id = $user->id;
            }

            if($extract) {
                $status = $backupmng->extractServerUser([
                    "name" => $username,
                    "backup-path" => $path,
                    "server-addr" => $this->request->getServerAddress(),
                ]);

                if(trim($status) !== "Success") {
                    return false;
                }
            }
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        return $created_user_id;
    }

    private function createDomain($path, $domain_name, $parent_name): bool
    {
        $backupmng = new BackupRestore();
        $config = $backupmng->getConfig([
            "server-addr" => $this->request->getServerAddress(),
            "name" => $domain_name,
            "backup-path" => $path,
        ]);
        $parents = Users::findFirst("username = '$parent_name'");
        $decoded_config = json_decode($this->crypto->fdecode($config), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->log->error([
                "Module" => "RestoreServer Domain",
                "Action" => "Backup restore domain",
                "Messages" => "Domain: $path/$domain_name backup config cant read",
            ]);
        }

        $user = new \FPanel\Common\Server\Users();
        $user->restoreDomain($decoded_config, $parents->id, $parents->type);
        return true;
    }

    private function restoreDomains($restore_users)
    {
        $backupmng = new BackupRestore();
        $backupmng->restoreServer([
            'date' => $this->date->getTimeStamp($this->create_date),
            'name' => $this->backup_name,
            'backup-id' => $this->backup_id,
            'data' => "domains",
            'client-ids' => implode("#", $restore_users),
            "server-addr" => $this->request->getServerAddress(),
        ]);
    }

    private function finishRestore(): bool
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
                "Module" => "RestoreServer Finish",
                "Action" => "Backup update",
                "Messages" => $error_messages,
            ]);
        }

        $backupmng = new BackupRestore();
        $params = array(
            "date" => $this->date->getTimeStamp($this->create_date),
            'name' => $this->backup_name,
            'backup-id' => $this->backup_id,
            'data' => "clear",
            "server-addr" => $this->request->getServerAddress(),
        );
        $backupmng->restoreServer($params);
        return $this->sendMail();
    }

    private function sendMail(): bool
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
        } catch(Exception $e) {

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