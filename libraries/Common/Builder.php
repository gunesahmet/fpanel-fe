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
namespace FPanel\Common;

use FPanel\Common\Client\BackupClient;
use FPanel\Common\Client\DomainConfig;
use FPanel\Common\Client\RestoreClient;
use FPanel\Common\Client\UserConfig;
use FPanel\Common\Domain\BackupDomain;
use FPanel\Common\Domain\RestoreDomain;
use FPanel\Common\Migration\Migration;
use FPanel\Common\Server\BackupServer;
use FPanel\Common\Server\RestoreServer;
use FPanel\Common\Statistics\Client as ClientStats;
use FPanel\Common\Statistics\Domain as DomainStats;
use FPanel\Domain\DomainAlias;
use FPanel\Domain\DomainDatabase;
use FPanel\Domain\Mails;
use FPanel\Domain\Subdomain;
use FPanel\Models\Databases;
use FPanel\Models\Domains;
use FPanel\Models\Mails as Mail;
use FPanel\Models\Servers;
use FPanel\Models\Subdomains;
use FPanel\Models\SystemSettings;
use FPanel\Models\Updates as Update;
use FPanel\Models\Users;
use FPanel\System\SysCore;
use Phalcon\Mvc\User\Component;

/**
 * Class Builder
 * @package FPanel\Backend
 */
class Builder extends Component
{
    public function backup($data)
    {
        $type = $data["type"];
        $status = $data["status"];
        $backup_id = $data["backup_id"];
        $size = $data["size"];

        switch ($type) {
            case "domain":
                $domain_backup = new BackupDomain();
                $domain_backup->backup($status, $backup_id, $size);
                break;
            case "client":
                $client_backup = new BackupClient();
                $client_backup->backup($status, $backup_id, $size);
                break;
            case "server":
                $server_backup = new BackupServer();
                $server_backup->backup($status, $backup_id, $size);
                break;
        }
    }

    public function restore($data)
    {
        $type = $data["type"];
        $status = $data["status"];
        $backup_id = $data["backup_id"];
        switch ($type) {
            case "domain":
                $domain = new RestoreDomain();
                $domain->restoreDomain($status, $backup_id);
                break;
            case "client":
                $client = new RestoreClient();
                $client->restore($status, $backup_id);
                break;
            case "server":
                $server = new RestoreServer();
                $server->restore($status, $backup_id);
                break;
        }
    }

    public function getMigrationData(): array
    {
        $migration = new Migration();
        return $migration->get();
    }

    public function startMigration(): bool
    {
        $migration = new Migration();
        $migration->start()
            ->accounts()
            ->serverConfigs();
        return true;
    }

    public function getUserData($data)
    {
        $migration = new Migration();
        return $migration->createUserData($data);
    }

    public function getDomainData($data)
    {
        $migration = new Migration();
        return $migration->createDomainData($data);
    }

    public function uploadBackup($data)
    {
        $type = $data["type"];
        switch ($type) {
            case "domain":
                $domain_backup = new BackupDomain();
                $domain_backup->upload($data);
                break;
            case "client":
                $client_backup = new BackupClient();
                $client_backup->upload($data);
                break;
            case "server":
                $server_backup = new BackupServer();
                $server_backup->upload($data);
                break;
        }
    }

    public function domainStatistics($data, $key, $iv): bool
    {
        $domain_statistics = new DomainStats();
        $result = $this->crypto->bdecode($data["result"], $key, $iv);
        $res = json_decode(trim($result), true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return false;
        }
        $domain_statistics->initDomain($data["domain_name"], $res);
        return true;
    }

    public function clientStatistics(): bool
    {
        $client_statistics = new ClientStats();
        $client_statistics->initClient();
        return true;
    }

    public function resellerStatistics(): bool
    {
        $client_statistics = new ClientStats();
        $client_statistics->initReseller();
        return true;
    }

    public function serverStatistics(): bool
    {
        $client_statistics = new ClientStats();
        $client_statistics->initClient();
        $client_statistics->initReseller();
        return true;

    }

    public function getUserList($data): array
    {
        $result = [];
        if (isset($data["client_id"]))
            $users = Users::find("parent = '$data[client_id]' and type != '4'");
        else
            $users = Users::find();
        foreach ($users as $user) {
            if (!isset($data["client_id"])) {
                $result[] = [
                    "username" => $user->username,
                    "user_id" => $user->id,
                    "last_init" => $user->getClientStatistics()->last_init,
                ];
            } else {
                $config = new UserConfig();
                $user_configs = $config->user($user->id)
                    ->userdata()
                    ->limits()
                    ->serviceplans()
                    ->domains();
                $user_configs_json = json_encode($user_configs);
                $result[] = [
                    "username" => $user->username,
                    "user_id" => $user->id,
                    "user_type" => $user->type,
                    "user_conf" => $this->crypto->fencode($user_configs_json),
                ];
            }
        }
        return $result;
    }

    public function getDomainList($data): array
    {
        $result = [];
        if (isset($data["client_id"]))
            $domains = Domains::find("client_id = '$data[client_id]'");
        else
            $domains = Domains::find();
        foreach ($domains as $domain) {
            if (!isset($data["client_id"])) {
                $result[] = [
                    "domain_name" => $domain->name,
                    "last_init" => $domain->getDomainStatistics()->last_init,
                    "user" => $domain->getFtpUsers()->username,
                    "prefix" => $domain->prefix,
                    "web_server_id" => $domain->getWebServers()->id,
                    "mail_server_id" => $domain->getMailServers()->id,
                ];
            } else {

                $mails = [];
                $subdomains = [];
                $databases = [];
                /**
                 * @var Databases $database
                 */
                foreach ($domain->getDatabases() as $database) {
                    if ($database->getSystemDatabaseServer()->host == "localhost") {
                        $host = "127.0.0.1";
                    } else {
                        $host = $database->getSystemDatabaseServer()->host;
                    }
                    $databases[] = [
                        "name" => $database->name,
                        "clear_name" => str_replace($domain->prefix . "_", "", $database->name),
                        "user" => $database->getDatabaseUsers()->login,
                        "pass" => trim($this->crypto->bdecode($database->getDatabaseUsers()->password)),
                        "type" => $database->type,
                        "host" => $host,
                        "port" => $database->getSystemDatabaseServer()->port,
                    ];
                }

                /**
                 * @var Subdomains $subdomain
                 */
                foreach ($domain->getSubdomains() as $subdomain) {
                    $subdomains[] = [
                        "name" => $subdomain->subdomain_name,
                        "user" => $subdomain->FtpUsers->username,
                    ];
                }

                /**
                 * @var Mail $mail
                 */
                foreach ($domain->getMail() as $mail) {
                    $mails[] = [
                        "name" => $mail->mail_name,
                    ];
                }

                $config = new DomainConfig();
                $config->domain($domain->id)
                    ->ftpuser()
                    ->user()
                    ->userdata()
                    ->limits()
                    ->dns()
                    ->domainalias()
                    ->mailsettings()
                    ->cronjobs()
                    ->subdomains()
                    ->databases()
                    ->mails();

                $domain_config_json = json_encode($config->domain_data);
                $result[] = [
                    "domain_name" => $domain->name,
                    "user" => $domain->FtpUsers->username,
                    "domain_conf" => $this->crypto->fencode($domain_config_json),
                    "databases" => $databases,
                    "subdomains" => $subdomains,
                    "mails" => $mails,
                    "web_server_id" => $domain->getWebServers()->id,
                    "mail_server_id" => $domain->getMailServers()->id,
                    "prefix" => $domain->prefix,
                ];
            }
        }
        return $result;
    }

    public function systemUpdate($data)
    {
        $update_version = Update::findFirst("version = '$data[version]'");

        if (!$update_version) {
            $update = new Update();
            $timestamp = strtotime($data["create_date"]);

            $update->assign(array(
                "created_date" => $timestamp,
                "version" => $data["version"],
                "description" => $data["description"],
                "server_id" => "1",
                "ignore" => "0",
            ));

            if (!$update->create()) {
                $error_messages = [];
                foreach ($update->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "Builder",
                    "data" => "systemUpdate failed",
                    "errors" => $error_messages
                ]);
            }
        }

        $system = new SysCore();
        $server_data = $system->getSystemData();
        $sys_data = array(
            "system_type" => $server_data["system_type"],
            "panel_version" => $data["version"],
            "os" => $server_data["os"]
        );
        $misc = SystemSettings::findFirst("key = 'system_data'");
        $misc->assign(array(
            "value" => json_encode($sys_data)
        ));
        if (!$misc->update()) {
            $error_messages = [];
            foreach ($misc->getMessages() as $message) {
                $error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Builder",
                "data" => "System settings update failed",
                "errors" => $error_messages
            ]);
        }
    }

    public function getServerConfig($data): array
    {
        $server = Servers::findFirst("id = '$data[server_id]'");
        $server_data = json_decode(trim($this->crypto->bdecode($server->data)),true);
        return [
            "key" => $server_data["key"],
            "iv" => $server_data["iv"],
            "ip_address" => $server_data["default_ip"],
        ];
    }

    public function createSubdomain($data)
    {
        $post_data = array(
            "domain_id" => $data["domain_id"],
            "domain_name" => $data["domain_name"],
            "domain_ftpuser" => $data["domain_ftpname"],
            "ipaddress" => $data["domain_ip_address"], // Domaine ait ipadresi
            "domain_ftp_user_id" => $data["domain_ftp_user_id"], // Domaine ait ftp user idsi
            "domain_parent" => $data["client_id"], // Domainin sahibinin idsi
            "ipaddr_id" => $data["domain_ipaddr_id"], // Domainin ip adresinin idsi
            "subdomain_name" => $data["subdomain_name"],
            "full_name" => $data["subdomain_name"].".".$data["domain_name"],
            "status" => isset($data["status"]) ? $data["status"] == 1 ? ON : OFF : ON,

            "use_native_ftp" => $data["ftpuser_type"] == NATIVE ? ON : OFF,
            "ftpname"   => $data["ftp_users"]["username"],
            "password"   => $data["ftp_users"]["password"],

            // Subdomain host parametreleri
            "php" => isset($data["php"]) ? $data["php"] == 1 ? ON : OFF : ON,
            "php_version"   => isset($data["php_version"]) ? $data["php_version"] : PHP72,
            "asp" => isset($data["asp"]) ? $data["asp"] == 1 ? ON : OFF : ON,
            "aspnet_core" => isset($data["aspnet_core"]) ? $data["aspnet_core"] == 1 ? ON : OFF : ON,
            "aspnet" => isset($data["aspnet"]) ? $data["aspnet"] == 1 ? ON : OFF : ON,
            "asp_net_version"   => isset($data["asp_net_version"]) ? $data["asp_net_version"] : v40,
            "cgi" => isset($data["cgi"]) ? $data["cgi"] == 1 ? ON : OFF : ON,
            "perl" => isset($data["perl"]) ? $data["perl"] == 1 ? ON : OFF : ON,
            "python" => isset($data["python"]) ? $data["python"] == 1 ? ON : OFF : ON,
            "ssl" => OFF,
        );
        $subdomains = new Subdomain();
        $subdomains->setPosts($post_data);
        $result = $subdomains->createSubdomain(false);
        if($result)
            return "success";
        else {
            return $subdomains->getMessages();
        }
    }

    public function createMail($data)
    {
        $post_data = array(
            "domain_name"       => $data["domain_name"],
            "domain_id"         => $data["domain_id"],
            "mail_account"      => $data["mail_name"],
            "password"          => $data["password"],
            "mailbox_quota"     => isset($data["mailbox_quota"]) && $data["mailbox_quota"] != null ? $data["mailbox_quota"] : null,
            "unlimited_quota"   => ((!isset($data["mail_quota"]) || $data["mail_quota"] == null) || isset($data["unlimited_quota"])) ? ON : OFF,
            "status"            => $data["status"],
        );
        $mail_account = new Mails();
        $mail_account->setPosts($post_data);
        $result =  $mail_account->createMailAccount();
        if($result)
            return "success";
        else {
            return $mail_account->getMessages();
        }
    }

    public function createDatabase($data)
    {
        /**
        [database_name] => testms2
        [database_server] =>
        [database_server_type] => mssql
        [database_username] => xeKmBXNX
        [domain_id] => 4444E91D-3D61-543C-4BA3-A9C6510F5AF3
        [domain_name] => domain.com
        [password] => R9Sr4hg
        [prefix] => wtu2itob

         */
        $post_data = array(
            "domain_name"           => $data["domain_name"],
            "prefix"                => $data["prefix"],
            "domain_id"             => $data["domain_id"],
            "database_name"         => $data["database_name"],
            "database_server_type"  => $data["database_server_type"],
            "database_server"       => $data["database_server"],
            "database_username"     => $data["database_username"],
            "password"              => $data["password"],
        );
        $mail_account = new DomainDatabase();
        $mail_account->setPosts($post_data);
        $result =  $mail_account->createDatabase(true);
        if($result)
            return "success";
        else {
            return $mail_account->getMessages();
        }
    }

    public function createMailAlias($data)
    {
        $post_data = array(
            "mail_account"          => $data["mail_account"],
            "domain_name"           => $data["domain_name"],
            "domain_id"             => $data["domain_id"],
            "mail_id"               => $data["mail_id"],
            "alias_name"            => $data["alias_name"],
        );
        $mail_account = new Mails();
        $mail_account->setPosts($post_data);
        $result =  $mail_account->createMailAlias();
        if($result)
            return "success";
        else {
            return $mail_account->getMessages();
        }
    }

    public function createDomainAlias($data)
    {
        $post_data = array(
            'update'            => false,
            "domain_name"       => $data["domain_name"],
            "domain_id"         => $data["domain_id"],
            "alias_name"        => $data["alias_name"],
            'syn_pri_dom'       => "1",
            'dns'               => "1",
            'status'            => "1",
            "mail"              => $data["mail"] == "1" ? ON : OFF,
            "web"               => $data["web"] == "1" ? ON : OFF,
            'old_alias'         => "",
        );
        $domain_alias = new DomainAlias();
        $domain_alias->setPosts($post_data);
        $result =  $domain_alias->createAlias();
        if($result)
            return "success";
        else {
            return $domain_alias->getMessages();
        }
    }
}