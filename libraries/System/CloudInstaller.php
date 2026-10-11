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
 */namespace FPanel\System;

use Exception;
use FPanel\Environments\Parser;
use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\Models\IpAddress;
use FPanel\Models\Servers;
use FPanel\Models\Settings;
use FPanel\Models\SystemSettings;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use FPanel\Process\Admin;
use FPanel\Process\Networking;
use FPanel\Server\NamedServer;
use FPanel\Server\Network;
use FPanel\Server\ServerSettings;
use FPanel\System\Blacklists as BlacklistManagement;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Adapter\Pdo\Postgresql;

/**
 * Class StandaloneInstaller
 * @package FPanel\System
 */
class CloudInstaller extends Library
{
    protected $ip_list = array();
    protected $hostname;
    protected $sub_hostname;
    protected $host;
    protected $panel_type = CLOUD;
    protected $server_os;
    protected $admin_mail;
    protected $default_ip;
    protected $username;
    protected $password;
    protected $pkg_list = array();
    protected $installed_packages = array();
    protected $server_id;

    public function hostname($hostname = null): CloudInstaller
    {
        if($hostname != null) {
            $host_array = explode('.', $hostname);
            if(count($host_array) >= "3") {
                $tld = end($host_array);
                $this->host = prev($host_array).'.'.$tld;
                $this->sub_hostname = str_replace('.'.$this->host,'', $hostname);
            }
            else {
                $this->host = $host_array;
                $this->sub_hostname = "";
            }
            $this->hostname = $hostname;
        }
        else {
            $this->error_messages['hostname'] = __('Hostname is required');
        }
        return $this;
    }

    public function contactMail($mail = null): CloudInstaller
    {
        if($mail != null) {
            if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $this->admin_mail = $mail;
            }
            else {
                $this->error_messages['admin_email'] = __('Email Address must be valid');
            }
        }
        else {
            $this->error_messages['admin_email'] = __('Email Address is required');
        }
        return $this;
    }

    public function defaultIpAddr($defaul_ip = null): CloudInstaller
    {
        if($defaul_ip != null) {
            if(!filter_var($defaul_ip, FILTER_VALIDATE_IP)) {
                $this->error_messages['default_ip_address'] = __('IP Address is not valid');
            }
            else {
                $this->default_ip = $defaul_ip;
            }
        }
        else {
            $this->error_messages['default_ip_address'] = __('IP Address is required');
        }
        return $this;
    }

    public function adminUsername($username = null): CloudInstaller
    {
        if($username != null) {
            $this->username = $username;
        }
        else {
            $this->error_messages['username'] = __('Username is required');
        }
        return $this;
    }

    public function adminPassword($password = null): CloudInstaller
    {
        if($password != null) {
            $this->password = $this->security->hash($password);
        }
        else {
            $this->error_messages['password'] = __('Password is required');
        }
        return $this;
    }

    public function ipAddresses($ip_list = null): CloudInstaller
    {
        $networkmng = new Networking();
        $default_ip = trim(preg_replace('/\s+/', ' ', $networkmng->defaultIpAddress()));
        if($default_ip == null) {
            $this->error_messages['default_ip'] = __('Default ip address can not set');
        }

        $db_ip_save_list = array();
        if($ip_list != null) {
            foreach ($ip_list as $ip_address) {
                $type = MAIN_CLOUD;
                $default = PASSIVE;
                if($ip_address[2] == $this->default_ip) {
                    $default = ACTIVE;
                }
                if((!empty($ip_address[4])) && ($ip_address[4] != null)) {
                    $gateway = $ip_address[4];
                }
                else {
                    $gateway = "";
                }
                $ip_interface = $ip_address[1];
                $index = "";
                if(PANEL_OS_TYPE == WINDOWS) {
                    $ip_interface = base64_decode($ip_address[1]);
                    $index = $ip_address[0];
                }

                $db_ip_save_list[] = array(
                    'ip_address'    => $ip_address[2],
                    'interface'     => $ip_interface,
                    'index'         => $index,
                    'is_default'    => $default,
                    'netmask'       => $ip_address[3],
                    'gateway'       => $gateway,
                    'type'          => $type,
                    'format'        => NORMAL,
                    'server_id'     => $this->server_id,
                );
            }
            if((is_array($ip_list)) && (count($ip_list) > 0)) {
                foreach ($ip_list as $ip) {
                    if(!filter_var($ip[2], FILTER_VALIDATE_IP)) {
                        $this->error_messages['ipaddress_lists'] = __('IP Address is not valid');
                    }
                }
                $this->ip_list = $db_ip_save_list;
            }
        }
        else {
            $this->error_messages['ipaddress_lists'] = __('Please select an ip address');
        }

        return $this;
    }

    public function panelType($panel_type = null): CloudInstaller
    {
        if($panel_type != null) {
            $this->panel_type = $panel_type;
        }
        else {
            $this->error_messages['panel_type'] = __('Please select panel type');
        }
        return $this;
    }

    public function serverOs($server_os = null): CloudInstaller
    {
        if($server_os != null) {
            $this->server_os = $server_os;
        }
        else {
            $this->error_messages['server_os'] = __('Server Operating System is not define please restart install again');
        }
        return $this;
    }

    public function save(): bool
    {
        if(!$this->createMySQLDatabase()) {
            return false;
        }
        if(!$this->truncateTables()) {
            return false;
        }
        if(!empty($this->error_messages)) {
            return false;
        }
        else {
            if(!$this->initServers()) {
                return false;
            }
            if(!$this->initPackages()) {
                return false;
            }
            if(empty($this->installed_packages)) {
                $this->error_messages["packages"] = __('Packages not initialized. Please try again');
                return false;
            }
            if(!$this->initIp()) {
                return false;
            }
            if(!$this->initUser()) {
                return false;
            }
            if(!$this->initSystemSettings()) {
                return false;
            }

            if(!$this->initNotifySettings()) {
                return false;
            }
            if(!$this->initDns()) {
                return false;
            }
            if(!$this->initSystemCommand()) {
                return false;
            }
            if(!$this->initLogSettings()) {
                return false;
            }
            if(!$this->initBlackList()) {
                return false;
            }
            if(!$this->initUpdateSettings()) {
                return false;
            }
        }

        if(!empty($this->error_messages)) {
            return false;
        }
        else {
            return true;
        }
    }

    private function initUser(): bool
    {
        $user = new Users();
        $user->assign(array(
            "id"        => $this->env->createUuid(),
            'username'  => $this->username,
            'password'  => $this->password,
            'type'      => ROOT_USER,
            'must_change_password '   => PASSIVE,
            'status'    => ACTIVE,
            'parent'    => '0',
            'interface'    => $this->getPost("interface"),
            'language'    => $this->getPost("language"),
            'api_status'    => ACTIVE,
        ));

        if(!$user->create()) {
            foreach ($user->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "CloudInstaller->initUser users",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        $user_data = new Userdata();
        $user_data->assign(array(
            'user_id'   => $user->id,
            'fullname'  => $this->getPost("fullname"),
            'email'     => $this->getPost("email"),
            'company'   => $this->getPost("company"),
            'fax'       => $this->getPost("fax"),
            'telephone' => $this->getPost("telephone"),
            'address'   => $this->getPost("address"),
            'city'      => $this->getPost("city"),
            'zipcode'   => $this->getPost("zipcode"),
            'country'   => $this->getPost("country"),
            'region'    => $this->getPost("region"),
        ));
        if(!$user_data->create()) {
            foreach ($user_data->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "CloudInstaller->initUser Userdata",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function initBlackList(): bool
    {
        $black_list = new BlacklistManagement();
        if($black_list->defaultRulesSettingsSave() == false) {
            foreach($black_list->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    private function initSystemSettings(): bool
    {
        $ini = new Parser(ROOTPATH.'/config/config.ini');
        $ini->data['clients']['address[]'][] = $this->request->getServerAddress();
        $ini->write();
        $version_number = F_VERSION;

        $server_data = array(
            array(
                'key'   => 'default_ip_address',
                'value' => $this->default_ip
            ),
            array(
                'key'   => 'hostname',
                'value' => json_encode(array('sub' => $this->sub_hostname,'host' => $this->host,'hostname' => $this->hostname))
            ),
            array(
                'key'   => 'system_data',
                'value' => json_encode(array(
                        'system_type' => $this->panel_type,
                        'panel_version' => $version_number,
                        'os' => $this->server_os)
                )
            ));

        foreach ($server_data as $data) {
            $server_misc = new SystemSettings();
            $server_misc->assign($data);
            if(!$server_misc->create()) {
                foreach ($server_misc->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "CloudInstaller->initSystemSettings",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    private function initServers(): bool
    {
        $this->server_id = $this->env->createUuid();
        $assign = array(
            "id"            => $this->server_id,
            'name'          => $this->hostname,
            'description'   => 'Main Server',
            'os'            => $this->server_os,
            'type'          => 'main',
            'status'        => ACTIVE,
            'domain_limit'  => 0,
            'database_limit' => 0,
            'mail_limit'    => 0,
            'ip_address'    => $this->default_ip,
            'os_type'       => $this->server_os,
            "data"          => $this->crypto->bencode(json_encode(["key" => CRYPT_KEY, "iv" => CRYPT_IV ]))
        );

        $servers = new Servers();
        $servers->assign($assign);
        if(!$servers->create()) {
            foreach ($servers->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "CloudInstaller->initServers",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function initPackages(): bool
    {
        try {
            $pkgmng = $this->socket->process('packagemng')
                ->job(array('list'))
                ->create()
                ->send(300)
                ->response();
        }
        catch (Exception $e) {
            $this->error_messages["packages"] = __("Package not initialized. Please contact with FPanel Developers...");
            return false;
        }

        $pkgmng = str_replace("\\+", "+", $pkgmng);

        $pkg_list = json_decode($pkgmng,true, JSON_UNESCAPED_SLASHES);
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->error_messages["packages"] = __("Package not initialized. Please contact with FPanel Developers...");
            return false;
        }

        /**
         * package_name
         * version
         * description
         * status
         * arch
         */

        if(PANEL_OS_TYPE == WINDOWS) {
            $new_pkg_list = array_values(array_unique($pkg_list, SORT_REGULAR));
            $this->pkg_list = $new_pkg_list;
        }
        else {
            $this->pkg_list = $pkg_list;
        }

        if (!empty($pkg_list)) {
            $qbuilder = new QueryBuilder();
            $qbuilder->into("system_packages")
                ->columns([
                    "package_name",
                    "package_desc",
                    "version",
                    "status",
                    "support",
                    "server_id",
                ]);
            foreach($this->pkg_list as $package) {
                if(!isset($package['status']) || empty($package['status'])) {
                    $status = NOT_INSTALLED;
                }
                else {
                    if($package["status"] != "not_installed") {
                        $status = INSTALLED;
                    }
                    else {
                        $status = NOT_INSTALLED;
                    }
                }

                $qbuilder->assign([
                    $package["package_name"],
                    $package["description"],
                    $package["version"],
                    $status,
                    1,
                    $this->server_id,
                ]);
            }
            if(!$qbuilder->create()) {
                $this->error_messages = $qbuilder->getMessages();
                $this->log->error([
                    "Module" => "CloudInstaller->initPackages",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $this->error_messages["packages"] = __("System packages can't get from server");
            return false;
        }

        $qbuilder = new QueryBuilder();
        $this->installed_packages = $qbuilder->select(["*"])->from("system_packages")->find();
        return true;

    }

    private function initIp(): bool
    {
        foreach ($this->ip_list as $ip) {
            $ip["id"] = $this->env->createUuid();
            $ip["server_id"] = $this->server_id;
            $system_ip_address = new IpAddress();
            $system_ip_address->assign($ip);
            if(!$system_ip_address->create()) {
                foreach ($system_ip_address->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "CloudInstaller->initIp",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        $networkmng = new Network();
        $status = $networkmng->writeNetworkConf($this->server_id, $this->server_os);
        if(!$status) {
            foreach($networkmng->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    private function initNotifySettings(): bool
    {
        $notify_settings = new Notyfy();
        $status = $notify_settings->setDefault();
        if(!$status) {
            foreach($notify_settings->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    private function initDns(): bool
    {
        $restore_dns = new NamedServer();
        $status = $restore_dns->restoreDNSRecords($this->server_id);
        if(!$status) {
            foreach($restore_dns->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    private function initLogSettings(): bool
    {
        $post_data = array(
            'periodic_del'          => OFF,
            'store_time'            => "30",
            'log_compress'          => ON,
            'send_information'      => OFF,
            'email_address'         => "",
            'server_id'             => $this->server_id,
        );
        $server = new ServerSettings();
        $server->setPosts($post_data);
        $status = $server->serverLogSettings();
        if(!$status) {
            foreach($server->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;

    }

    private function initSystemCommand(): bool
    {
        $adminmng = new Admin();
        if(strpos($adminmng->conf(), "Err") !== false) {
            return false;
        }
        return true;
    }

    private function createMySQLDatabase(): bool
    {
        $connection = new Mysql(array(
            'host' => '127.0.0.1',
            'username' => USER_SQL_ADMIN,
            'password' => PANEL_SQL_PASS,
            'port' => '3306'
        ));
        if(!$connection->connect()) {
            $this->error_messages["connection"] = __("Could not connec to database");
            return false;
        }

        $database = new Database();
        $connection->execute("DROP DATABASE IF EXISTS ". DATABASE_NAME .";");
        $connection->execute("CREATE DATABASE ". DATABASE_NAME .";");

        $script_parts = [
            "Backup",
            "Blacklist",
            "Certificates",
            "ClientIpAddress",
            "ClientLimitsPermissions",
            "ClientStatistics",
            "CloudStorage",
            "DatabaseRemoteHosts",
            "DatabaseServer",
            "DatabaseServerSettings",
            "DatabaseUsers",
            "Databases",
            "DnsRecords",
            "DnsTemplate",
            "DnsZone",
            "DomainAlias",
            "DomainServices",
            "DomainStatistics",
            "Domains",
            "FailedLogins",
            "FtpRepo",
            "FtpUsers",
            "Ipaddress",
//            "Letsencrypt",
            "MailAlias",
            "MailSettings",
            "Mails",
            "MailServerSettings",
            "Messages",
            "ProtectDirectory",
            "ProtectDirectoryUsers",
            "ScheduledTasks",
            "ServerFtpRepo",
            "ServerFtpSettings",
            "ServerLogSettings",
            "Servers",
            "Serviceplans",
            "Settings",
            "SpamList",
            "SpamSettings",
            "Subdomains",
            "SuccessLogins",
            "Support",
            "SystemPackages",
            "SystemSettings",
            "SystemVersions",
            "Updates",
            "Userdata",
            "Users",
            "VirtualDirectory",
            "WebServer",
//            "Telegram",
//            "TelegramUsers",
        ];
        foreach ($script_parts as $script_part) {
            $method_name = "get".$script_part;
            $mysql_script = $database->mysql($database->$method_name());
            $status = $connection->execute($mysql_script);
        }

        if(!$status) {
            $this->error_messages[] = $connection->getErrorInfo();
            return false;
        }
        $connection->close();

        $postgresql = false;
        $mysql = false;

        if(file_exists(PANEL_DIR . "/etc/.sys.packages")) {
            $sys_pkg = file_get_contents(PANEL_DIR . "/etc/.sys.packages");
            $sys_pkg_content = explode('\n', $sys_pkg);
            foreach($sys_pkg_content as $sys_content) {
                if(strpos($sys_content,'postgresql') !== false) {
                    $postgresql = true;
                }
                if(strpos($sys_content,'mariadb') !== false) {
                    $mysql = true;
                }
                if(strpos($sys_content,'mysql') !== false) {
                    $mysql = true;
                }
            }
        }
        else {
            return false;
        }

        if($mysql) {
            $this->di->set('db', function() {
                return  new Mysql(array(
                    'host'     => '127.0.0.1',
                    'username' => USER_SQL_ADMIN,
                    'password' => PANEL_SQL_PASS,
                    'port' => '3306',
                    'dbname' => DATABASE_NAME,
                    'charset'     => 'utf8',
                ));
            });
        }
        else if($postgresql){
            $this->di->set('db', function () {
                return new Postgresql(array(
                    'host'     => '127.0.0.1',
                    'username' => USER_SQL_ADMIN,
                    'password' => PANEL_SQL_PASS,
                    'port' => '5432',
                    'dbname' => DATABASE_NAME,
                ));
            });
        }

        return $status;
    }

    private function truncateTables(): bool
    {
        $connection = new Mysql(array(
            'host' => '127.0.0.1',
            'username' => USER_SQL_ADMIN,
            'password' => PANEL_SQL_PASS,
            'dbname' => DATABASE_NAME,
            'port' => '3306'
        ));

        $tables = $connection->listTables();

        $sql = null;
        foreach ($tables as $key) {
            if($sql == null)
                $sql = 'TRUNCATE TABLE '.DATABASE_NAME.'.'.$key.';';
            else
                $sql = $sql .'TRUNCATE TABLE '.DATABASE_NAME.'.'.$key.';';
        }
        $status = $connection->execute($sql);
        $connection->close();
        return $status;
    }

    private function initUpdateSettings(): bool
    {
        $update = new Settings();
        $update->assign(array(
            'server_id' => $this->server_id,
            'auto_update' => PASSIVE,
            'update_time' => '02:00',
            'update_server' => 'repo.fpanel.net',
            'random_time' => ACTIVE,
        ));

        if($update->create() == false) {
            foreach($update->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "CloudInstaller->initUpdateSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }
}