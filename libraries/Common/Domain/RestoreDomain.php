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
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\SystemSettings;
use FPanel\Process\BackupRestore;

/**
 * Class RestoreDomain
 * @package FPanel\Common\Domain
 */
class RestoreDomain extends Base
{
    // Ana gereksinimler
    private $domain_name;
    private $create_date;
    private $backup_name;
    private $domain_id;
    private $domain_prefix;
    private $backup_id;
    private $email;

    // Alınan içerikler
    private $backup_contents;

    // Restore edilecek içerikler
    private $restore_contents;
    private $restore_domain_configs;
    private $restore_www;
    private $restore_mail;
    private $restore_dns;
    private $restore_database;
    private $restore_subdomain;
    private $domain_ftp_user;

    private $parts;
    private $domain_configs = array();
    private $sub_list;
    private $mail_list;
    private $subdomain_users;

    public function restoreDomain($status, $backup_id): bool
    {
        $this->backup_id = $backup_id;
        switch($status) {
            case "extract":
                if(!$this->getDomainConfigs()) {
                    return false;
                }
                /**
                 * Not: Dosyalar arşivden geçici klasöre çıkarıldı. Bu adımdan sonra domain config okunacak ve create işlemleri tamamlanacak.
                 * Ardından curl ile vhost mail ve db gibi dosyaların yerlerine konması sağlanacak.
                 */
                if($this->restore_www == true) {
                    $this->parts = "www";
                }
                $this->restoreDomainConfigs();
                $this->restoreCurl();
                break;
            case "finish":
                /**
                 * Not: Cli'dan Finish döndükten sonra database tablosunda restore işlemi tamamlandı gösterilecek ve restore tablosundan ilgili kayıt silinecek...
                 */
                $this->finishRestore();
                if(REDIS_STATUS == true) {
                    if($this->redis->exists('Users'))
                        $this->redis->del("Users");
                }
                break;
        }
        return true;
    }

    private function getDomainConfigs(): bool
    {
        $backup = Backup::findFirstById($this->backup_id);
        $this->domain_id = $backup->domain_id;

        $domain = Domains::findFirstById($this->domain_id);
        $ftp_users = FtpUsers::findFirstById($domain->ftp_user_id);
        $this->domain_ftp_user = $ftp_users->username;
        $this->domain_name = $domain->name;
        $this->domain_prefix = $domain->prefix;

        $this->create_date = $backup->created_date;
        $this->backup_name = $backup->name;
        $this->email = $backup->email;
        $backupmng = new BackupRestore();

        $domain_config_file = $backupmng->getConfig([
            "server-addr" => $this->request->getServerAddress(),
            "name" => $this->backup_name,
            "backup-path" => PANEL_BACKUP_DIR . "/$this->domain_name/" . $this->date->getTimeStamp($this->create_date) . "/",
        ]);

        $this->domain_configs = json_decode($this->crypto->fdecode($domain_config_file), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            $backup->status = RESTORE_FAILED;
            if(!$backup->update()) {
                $error_messages = [];
                foreach ($backup->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "message" => "RestoreDomain",
                    "data" => "Backup getDomainConfigs update failed",
                    "errors" => $error_messages
                ]);
            }
            return false;
        }

        $this->backup_contents = $this->domain_configs['backup_data']['content'];
        $this->restore_contents = json_decode($backup->restore_contents,true);

        foreach($this->backup_contents as $key => $value) {
            switch($key) {
                case "domain_settings":
                    if($this->restore_contents['restore_domain_configs']['value'] && $value)
                        $this->restore_domain_configs = true;
                    break;
                case "www_content":
                    if($this->restore_contents['restore_httpdoc']['value'] && $value)
                        $this->restore_www = true;
                    break;
                case "dns_content":
                    if($this->restore_contents['restore_dns']['value'] && $value)
                        $this->restore_dns = true;
                    break;
                case "subdomain_content":
                    if($this->restore_contents['restore_subdomains']['value'] && $value)
                        $this->restore_subdomain = true;
                    break;
                case "mail_content":
                    if($this->restore_contents['restore_mails']['value'] && $value)
                        $this->restore_mail = true;
                    break;
                case "database_content":
                    if($this->restore_contents['restore_databases']['value'] && $value)
                        $this->restore_database = true;
                    break;
            }
        }
        return true;
    }

    private function restoreDomainConfigs()
    {
        $builder = new Builder();
        if($this->restore_domain_configs) {
            $builder->restoreDomainProtectDirs($this->domain_id, $this->domain_name, $this->domain_configs['prodirs']);
            $builder->restoreDomainTasks($this->domain_id, $this->domain_name, $this->domain_configs['cronjobs']);
        }

        if($this->restore_database)
            $builder->restoreDomainDatabases($this->domain_id, $this->domain_name, $this->domain_configs['databases'], $this->date->getTimeStamp($this->create_date));

        if($this->restore_subdomain) {
            $builder->restoreDomainSubs($this->domain_id, $this->domain_name, $this->domain_configs['subdomains']);
            $this->parts = $this->parts . '#subdomains';
            $this->sub_list = $builder->subdomain_list;
            $this->subdomain_users = $builder->sub_users;
        }

        $builder->restoreDomainAlias($this->domain_id, $this->domain_name, $this->domain_configs['domain_alias']);
        $builder->restoreDomainDNS($this->domain_id, $this->domain_name, $this->domain_configs['dns']);


        if($this->restore_mail) {
            $builder->restoreDomainMails($this->domain_id, $this->domain_name, $this->domain_configs['mails']);
            $this->mail_list = $builder->mail_list;
            $this->parts = $this->parts . '#mail';
        }
    }

    private function restoreCurl()
    {
        $params = array(
            'backup-id' => $this->backup_id,
            'domain' => $this->domain_name,
            'prefix' => $this->domain_prefix,
            'domain-user' => $this->domain_ftp_user,
            'date'   => $this->date->getTimeStamp($this->create_date),
            'name'   => $this->backup_name,
            'data'   => $this->parts . '#' . "tmp",
            'sub-domain-users' => $this->subdomain_users,
            'sub-list' => $this->sub_list,
            "mail-list" => $this->mail_list
        );
        $params["server-addr"] = $this->request->getServerAddress();

        $backupmng = new BackupRestore();
        $backupmng->restoreDomain($params);
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

    private function finishRestore()
    {
        $backup = Backup::findFirstById($this->backup_id);
        $backup->status = "1";
        if(!$backup->update()) {
            $error_messages = [];
            foreach ($backup->getMessages() as $message) {
                $error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "message" => "RestoreDomain",
                "data" => "Backup finishRestore update failed",
                "errors" => $error_messages
            ]);
        }
        $this->sendMail();
    }
}