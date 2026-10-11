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
use FPanel\MailServer\Mail as MailServerSettings;
use FPanel\MailServer\SpamSettings;
use FPanel\Models\IpAddress;
use FPanel\Models\ServerFtpSettings;
use FPanel\Models\Servers;
use FPanel\Models\Settings;
use FPanel\Models\SystemPackages;
use FPanel\Models\SystemSettings;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use FPanel\Models\WebServer;
use FPanel\Process\Admin;
use FPanel\Process\Networking;
use FPanel\DatabaseServer\DatabaseServer;
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
class StandaloneInstaller extends Library
{
    protected $ip_list = array();
    protected $hostname;
    protected $sub_hostname;
    protected $host;
    protected $panel_type = STANDALONE;
    protected $server_os;
    protected $adminMail;
    protected $default_ip;
    protected $username;
    protected $password;
    protected $pkg_list = array();
    protected $php_status = false;
    protected $php56 = false;
    protected $php72 = false;
    protected $php74 = false;
    protected $installed_packages = array();
    protected $server_id;

    public function hostname($hostname = null): StandaloneInstaller
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

    public function contactMail($mail = null): StandaloneInstaller
    {

        if($mail != null) {
            if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $this->adminMail = $mail;
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

    public function defaultIpAddr($defaul_ip = null): StandaloneInstaller
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

    public function adminUsername($username = null): StandaloneInstaller
    {

        if($username != null) {
            $this->username = $username;
        }
        else {
            $this->error_messages['username'] = __('Username is required');
        }
        return $this;
    }

    public function adminPassword($password = null): StandaloneInstaller
    {

        if($password != null) {
            $this->password = $this->security->hash($password);
        }
        else {
            $this->error_messages['password'] = __('Password is required');
        }
        return $this;
    }

    public function ipAddresses($ip_list = null, $posted_ip = null): StandaloneInstaller
    {
        $networkmng = new Networking();
        $default_ip = trim(preg_replace('/\s+/', ' ', $networkmng->defaultIpAddress()));
        if($default_ip == null) {
            $this->error_messages['default_ip'] = __('Default ip address can not set');
        }

        $db_ip_save_list = array();
        if(($ip_list != null) && ($posted_ip != null)) {
            foreach ($ip_list as $ip_address) {
                $type = EXCLUSIVE;
                $default = "0";
                foreach ($posted_ip as $selected_ip) {
                    // $ip_address['2'] => ip adresi
                    if ($selected_ip == $ip_address['2'] ) {
                        $type = SHARED;
                    }
                }
                if($ip_address['2'] == $this->default_ip) {
                    $default = "1";
                }

                if((!empty($ip_address['4']))) {
                    $gateway = $ip_address['4'];
                }
                else {
                    $gateway = "";
                }
                $ip_interface = $ip_address['1'];
                $index = "";
                if(PANEL_OS_TYPE == WINDOWS) {
                    $ip_interface = base64_decode($ip_address['1']);
                    $index = $ip_address['0'];
                }

                $db_ip_save_list[] = array(
                    'ip_address' => $ip_address['2'],
                    'interface' => $ip_interface,
                    'index' => $index,
                    'is_default' => $default,
                    'netmask' => $ip_address['3'],
                    'gateway' => $gateway,
                    'type' => $type,
                    'format' => "normal",
                    'server_id' => $this->server_id,
                );
            }
            if((is_array($ip_list)) && (count($ip_list) > 0)) {
                foreach ($ip_list as $ip) {
                    if(!filter_var($ip['2'], FILTER_VALIDATE_IP)) {
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

    public function panelType($panel_type = null): StandaloneInstaller
    {
        if($panel_type != null) {
            $this->panel_type = $panel_type;
        }
        else {
            $this->error_messages['panel_type'] = __('Please select panel type');
        }
        return $this;
    }

    public function serverOs($server_os = null): StandaloneInstaller
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

            if(!$this->initDatabaseServer()) {
                return false;
            }

            if(!$this->initWebServer()) {
                return false;
            }

            if(!$this->initMailServer()) {
                return false;
            }
            if(!$this->initSpamSettings()) {
                return false;
            }
            if(!$this->initBlackList()) {
                return false;
            }
            if(!$this->initUpdateSettings()) {
                return false;
            }
            if(!$this->initFtpSettings()) {
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
        $status = $black_list->defaultRulesSettingsSave();
        if(!$status) {
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
                "Module" => "StandaloneInstaller->initServers",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function initPackages(): bool
    {
//        try {
//            $pkgmng = $this->socket->process('packagemng')
//                ->job(array('list'))
//                ->create()
//                ->send(300)
//                ->response();
//        }
//        catch (Exception $e) {
//            $this->error_messages["packages"] = __("Package not initialized. Please contact with FPanel Developers...");
//            return false;
//        }
        $ctx = null;
        $pkg_list = fopen(ROOTPATH . "/packages.list", "r") or die("Unable to open file!");
        $pkgmng = fread($pkg_list,filesize(ROOTPATH . "/packages.list"));
        fclose($pkg_list);
        $pkgmng = trim($pkgmng);
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
                if(empty($package['status'])) {
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
                    "Module" => "StandaloneInstaller->initPackages",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
        else {
            $this->error_messages["packages"] = __("System packages can't get from database");
            return false;
        }

        $this->installed_packages = $this->queryBuilder->select(["*"])->from("system_packages")->find();
        return true;
    }

    private function initMySQLDatabaseServer(): void
    {
        $database_server = new DatabaseServer();
        $database_server->setPost("db_server_name", "main");
        $connection = $database_server->connection('mysql',USER_SQL_ADMIN,PANEL_SQL_PASS,'3306','127.0.0.1');
        if($connection) {
            $status = $database_server->saveMySQLDefaultDatabaseServer(USER_SQL_ADMIN, PANEL_SQL_PASS, '3306', 'localhost', $this->server_id);
            if (!$status) {
                foreach ($database_server->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        else {
            foreach($database_server->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
    }

    private function initMSSQLDatabaseServer(): void
    {
        $status = false;
        $sys_pkg = file_get_contents(PANEL_DIR . "/etc/.sys.packages");

        $sys_pkg_content = explode('\n', $sys_pkg);
        foreach($sys_pkg_content as $sys_content) {
            if(strpos($sys_content,'mssql') !== false) {
                $status = true;
            }
        }
        if($status == true) {
            $database_server = new DatabaseServer();
            $database_server->setPost("db_server_name", "main_mssql");
            $connection = $database_server->connection('mssql','sa',PANEL_SQL_PASS,'1433','127.0.0.1');
            if($connection) {
                $status = $database_server->saveMSSQLDefaultDatabaseServer('sa',PANEL_SQL_PASS,'1433','localhost', $this->server_id);
                if(!$status) {
                    foreach($database_server->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                }
            }
            else {
                foreach($database_server->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
    }

    private function initDatabaseServer(): bool
    {
        $this->initMySQLDatabaseServer();
        if(PANEL_OS_TYPE == WINDOWS) {
            $this->initMSSQLDatabaseServer();
        }
        return $this->initPostgreSQLServer();
    }

    private function initPostgreSQLServer(): bool
    {
        $status = false;
        $sys_pkg = file_get_contents(PANEL_DIR . "/etc/.sys.packages");

        $sys_pkg_content = explode('\n', $sys_pkg);
        foreach($sys_pkg_content as $sys_content) {
            if(strpos($sys_content,'postgresql') !== false) {
                $status = true;
            }
        }
        if($status) {
            $database_server = new DatabaseServer();
            $database_server->setPost("db_server_name", "main_pgsql");
            $connection = $database_server->connection('pgsql',USER_SQL_ADMIN,PANEL_SQL_PASS,'5432','127.0.0.1');

            if($connection === true) {
                $status = $database_server->savePostgreSQLDefaultDatabaseServer(USER_SQL_ADMIN,PANEL_SQL_PASS,'5432','localhost', $this->server_id);
                if(!$status) {
                    foreach($database_server->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    return false;
                }
                return true;
            }
            else {
                foreach($database_server->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }
        }
        return false;
    }

    private function initMailServer(): bool
    {
        $spam_status = false;
        $mail_settings = new MailServerSettings();
        if(!empty($this->installed_packages)) {
            foreach ($this->installed_packages as $packages) {
                if(PANEL_OS_TYPE == WINDOWS) {
                    if(strpos($packages['package_name'], "spamassassin") !== false) {
                        if($packages['status'] == INSTALLED) {
                            $spam_status = true;
                            break;
                        }
                    }
                }
                else {
                    if("spamassassin" == $packages['package_name']) {
                        if($packages['status'] == INSTALLED) {
                            $spam_status = true;
                            break;
                        }
                    }
                }
            }
        }
        if(!empty($this->installed_packages)) {
            foreach ($this->installed_packages as $packages) {
                if(strpos(strtolower($packages['package_name']), "hmail") !== false) {
                    if($packages['status'] == INSTALLED) {
                        $mail_settings->setPosts([

                            "max_smtp_threads" => "0",
                            "max_msg_size" => "20", // MB

                            "smtp_no_tries" => "4",
                            "smtp_tries_min" => "60",
                            'hostname'  => $this->hostname,

                            "smtp_relayer" => "",
                            "smtp_relayer_port" => "25",
                            "smtp_relayer_auth" => "off",
                            "smtp_relayer_user" => "",
                            "smtp_relayer_passwd" => "",
                            "smtp_relayer_security" => "none",

                            "smtp_auth_plain" => "off",
                            "allow_empty_sender" => "on",
                            "allow_invalid_end" => "on",
                            "dis_invalid_client" => "off",
                            "max_inv_command" => "100",
                            "delivery_bind_to_ip" => "",
                            "max_rec_batch" => "100",
                            "add_del_head" => "off",
                            "rule_loop_limit" => "5",
                            "max_mx_host" => "15",

                            "max_pop_threads" => "0",
                            "max_imap_threads" => "0",

                            "spam_mark_threshold" => "5",
                            "add_header_spam" => "on",
                            "add_reason_spam" => "on",
                            "add_msg_subj" => "off",
                            "add_msg_subj_txt" => "[SPAM]",
                            "spam_del_threshold" => "20",
                            "spam_max_msg_size" => "1", // MB

                            "use_spf" => "off",
                            "use_spf_score" => "3",
                            "check_host_helo" => "off",
                            "check_host_helo_score" => "2",
                            "use_mx_checks" => "off",
                            "use_mx_checks_score" => "2",
                            "verify_dkim" => "off",
                            "verify_dkim_score" => "5",

                            'spam_filter_status'  => $spam_status ? "on" : "off",
                            'server_type'  => HMAIL,
                            'server_id'  => $this->server_id,
                        ]);
                    }
                }
                else if(strpos(strtolower($packages['package_name']), "postfix") !== false) {
                    if($packages['status'] == INSTALLED) {
                        $mail_settings->setPosts(array(
                            'max_message_size'  => '10',
                            'queue_life_time'  => '24',
                            "bounce_queue_lifetime" => "3600",
                            "policyd_spy_timelimit" => "3600",
                            'concurrency_in_que'  => '50',
                            'max_recipients'  => '100',
                            'submission_port'  => 'on',
                            'delete_double_msg'  => 'on',
                            'imap_auth_with_smtp'  => 'on',
                            'spf_protection'  => 'on',
                            'spam_filter_status'  => $spam_status ? "on" : "off",
                            'rbl_status'  => 'off',
                            'rbl_list'  => '',
                            'server_id'  => $this->server_id,
                            'hostname'  => $this->hostname,
                            'server_type'  => POSTFIX,
                        ));
                    }
                }
                else if(strpos(strtolower($packages['package_name']), "mailenable") !== false) {
                    if($packages['status'] == INSTALLED) {
                        $mail_settings->setPosts([
                            'hostname'  => $this->hostname,
                            "sub_port_status" => "on",
                            "sub_port" => "587",
                            "sub_port_auth_mode" => "0",
                            "max_msg_size_status" => "off", // Outbound
                            "max_msg_size" => "10", // Outbound
                            "smtpd_recipient_limit" => "1000", // Security
                            "smtp_in_connection_limit" => "32", // Inbound Advanced
                            "smtp_out_connection_limit" => "32", // Outbound
                            "max_queue_lifetime" => "30", // Delivery
                            "dns_rbl_status" => "off",
                            "dns_rbl_mode" => "0",
                            "me_dns_black_lists" => "",
                            "url_rbl_status" => "off",
                            "url_rbl_mode" => "0",
                            "me_url_black_lists" => "",
                            "spf_protection" => "off",
                            "spf_best_guess" => "off",
                            "spf_best_guesses" => "",
                            "spf_white_lists_status" => "off",
                            "spf_white_lists" => "",
                            "spf_pass_local" => "off",
                            'spam_filter_status'  => $spam_status ? "on" : "off",
                            'server_type'  => MAILENABLE,
                            'server_id'  => $this->server_id,
                        ]);
                    }
                }
                else if(strpos(strtolower($packages['package_name']), "qmail") !== false) {
                    if($packages['status'] == INSTALLED) {
                        $mail_settings->setPosts(array(
                            'max_message_size'  => '10',
                            'queue_life_time'  => '24',
                            "bounce_queue_lifetime" => "3600",
                            "policyd_spy_timelimit" => "3600",
                            'concurrency_in_que'  => '50',
                            'concurrency_out_que'  => '100',
                            'max_recipients'  => '100',
                            'submission_port'  => '0',
                            'delete_double_msg'  => '0',
                            'imap_auth_with_smtp'  => '1',
                            'smtp_auth'  => '1',
                            'spf_protection'  => '1',
                            'rbl_status'  => '0',
                            'rbl_list'  => '',
                            'server_id'  => $this->server_id,
                            'spam_filter_status'  => $spam_status ? "on" : "off",
                            'hostname'  => $this->hostname,
                            'server_type'  => QMAIL,
                        ));
                    }
                }
                else if(strpos(strtolower($packages['package_name']), "exchange") !== false) {
                    if($packages['status'] == INSTALLED) {
                        $mail_settings->setPosts(array(
                            'max_message_size'  => '10',
                            'queue_life_time'  => '24',
                            "bounce_queue_lifetime" => "3600",
                            "policyd_spy_timelimit" => "3600",
                            'concurrency_in_que'  => '50',
                            'concurrency_out_que'  => '100',
                            'max_recipients'  => '100',
                            'submission_port'  => '0',
                            'delete_double_msg'  => '0',
                            'imap_auth_with_smtp'  => '1',
                            'smtp_auth'  => '1',
                            'spf_protection'  => '1',
                            'rbl_status'  => '0',
                            'rbl_list'  => '',
                            'server_id'  => $this->server_id,
                            'spam_filter_status'  => $spam_status ? "on" : "off",
                            'hostname'  => $this->hostname,
                            'server_type'  => MSEXCHANGE,
                        ));
                    }
                }
            }
        }
        $status = $mail_settings->saveMailSettings(true, $this->server_id);
        if(!$status) {
            foreach($mail_settings->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
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
                    "Module" => "StandaloneInstaller->initIp",
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

    private function initWebServer(): bool
    {

        if(PANEL_OS_TYPE == WINDOWS) {
            $php_string = [
                'PHP 5.6' => 'php56',
                'PHP 7.2' => 'php72',
                'PHP 7.4' => 'php74',
            ];
        }
        else {
            $php_string = ['php'];
        }

        $this->phpStatus($php_string);
        $perl_pkg = SystemPackages::findFirst("package_name = 'perl'");
        $python_pkg = SystemPackages::findFirst("package_name = 'python'");

        $perl_status = 0;
        if($perl_pkg) {
            $perl_status = 1;
        }

        $python_status = 0;
        if($python_pkg) {
            $python_status = 1;
        }
        $phps = [
            'php56' => false,
            'php72' => false,
            'php74' => false,
        ];
        $php_status = 0;

        if($this->php56 == true) {
            $phps['php56'] = true;
            $this->php_status = true;
        }
        if($this->php72 == true) {
            $phps['php72'] = true;
            $this->php_status = true;
        }
        if($this->php74 == true) {
            $phps['php74'] = true;
            $this->php_status = true;
        }
        if($this->php_status == true) {
            $php_status = 1;
        }

        $main_modules = new WebServer();
        $main_modules->assign(array(
            'php_status'    => $php_status,
            'php_versions'  => json_encode($phps),
            'perl_status'   => $perl_status,
            'python_status' => $python_status,
            'server_id'     => $this->server_id,
        ));

        if(!$main_modules->create()) {
            foreach ($main_modules->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "StandaloneInstaller->initWebServer",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
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

    private function initSpamSettings(): bool
    {
        $installed = PASSIVE;
        if(!empty($this->installed_packages)) {
            foreach ($this->installed_packages as $packages) {
                if("spamassassin" == $packages['package_name']) {
                    if($packages['status'] == INSTALLED) {
                        $installed = ACTIVE;
                        break;
                    }
                    else {
                        $installed = PASSIVE;
                    }
                }
            }
            $spam_settings = new SpamSettings();
            return $spam_settings->saveDefaultSpamSettings(ACTIVE, $this->server_id, $installed);
        }
        return true;
    }

    private function initSystemCommand()
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
            $this->error_messages["connection"] = __("Could not connect to database");
            return false;
        }

        $database = new Database();
        $connection->execute("DROP DATABASE IF EXISTS ". DATABASE_NAME .";");
        $connection->execute("CREATE DATABASE ". DATABASE_NAME .";");

        $script_parts = [
            "Backup",
            "Certificates",
            "ClientIpAddress",
            "ClientLimitsPermissions",
            "CloudStorage",
            "DatabaseRemoteHosts",
            "DatabaseServerSettings",
            "DatabaseUsers",
            "Databases",
            "DnsRecords",
            "DnsZone",
            "DomainAlias",
            "DomainServices",
            "Domains",
            "FailedLogins",
            "FtpRepo",
            "FtpUsers",
            "Ipaddress",
//            "Letsencrypt",
            "MailAlias",
            "MailSettings",
            "Mails",
            "SystemSettings",
            "ProtectDirectory",
            "ProtectDirectoryUsers",
            "ScheduledTasks",
            "ServerFtpRepo",
            "Servers",
            "Serviceplans",
            "Settings",
            "ClientStatistics",
            "DomainStatistics",
            "Subdomains",
            "SuccessLogins",
            "Support",
            "Blacklist",
            "DatabaseServer",
            "DnsTemplate",
            "ServerFtpSettings",
            "ServerLogSettings",
            "MailServerSettings",
            "Messages",
            "SystemPackages",
            "SpamList",
            "SpamSettings",
            "WebServer",
//            "Telegram",
//            "TelegramUsers",
            "Updates",
            "Userdata",
            "Users",
            "VirtualDirectory",
            "SystemVersions",
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
            'server_id'     => $this->server_id,
            'auto_update'   => '0',
            'update_time'   => '02:00',
            'update_server' => 'repo.fpanel.net',
            'random_time'   => '1',
        ));
        if(!$update->create()) {
            foreach ($update->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "StandaloneInstaller->initUpdateSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function initFtpSettings(): bool
    {
        if(PANEL_OS_TYPE == WINDOWS) {
            $params = array(
                'ftp_type' => 'iis',
                'timeout_idle' => '120',
                'timeout_no_transfer' => '600',
                'maxinstances' => '30',
                'max_clients' => '4294967295',
            );
        }
        else {
            $params = array(
                'ftp_type' => 'proftpd',
                'timeout_idle' => '1200',
                'timeout_no_transfer' => '600',
                'maxinstances' => '30',
                'max_clients' => '10',
            );
        }
        $ftp_settings = new ServerFtpSettings();
        $ftp_settings->assign(
            array(
                'params' => json_encode($params),
                'server_id' => $this->server_id
            )
        );

        if(!$ftp_settings->create()) {
            foreach ($ftp_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "StandaloneInstaller->initFtpSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function phpStatus($php_string)
    {
        foreach ($this->installed_packages as $packages) {
            foreach ($php_string as $package_name => $php_version) {
                if($package_name == $packages['package_name']) {
                    if($packages['status'] == INSTALLED) {
                        $this->php_status = true;
                        switch($php_version) {
                            case "php56":
                                $this->php56 = true;
                                break;
                            case "php72":
                                $this->php72 = true;
                                break;
                            case "php74":
                                $this->php74 = true;
                                break;
                        }
                    }
                }
            }
        }
    }
}