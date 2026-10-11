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
 */namespace FPanel\Cloud;

use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\MailServer\Mail;
use FPanel\MailServer\SpamSettings;
use FPanel\Models\IpAddress;
use FPanel\Models\ServerFtpSettings;
use FPanel\Models\Settings;
use FPanel\Models\SystemPackages;
use FPanel\Models\WebServer;
use FPanel\Process\Admin;
use FPanel\Process\Networking;
use FPanel\Process\Packages;
use FPanel\DatabaseServer\DatabaseServer;
use FPanel\Server\NamedServer;
use FPanel\Server\Network;
use FPanel\Server\ServerSettings;

class ServerInit extends Library
{
    private $installed_packages = [];
    private $server_os = LINUX;
    private $hostname;
    private $server_id;
    private $default_ip;
    private $ip_list = [];
    private $selected_packages = [];
    private $php_status = false;
    private $test_mode = false;
    private $server_data;

    public function setServer(\FPanel\Models\Servers $server): ServerInit
    {
        $this->server_id = $server->id;
        $this->server_data = json_decode($this->crypto->bdecode($server->data), true);
        return $this;
    }

    public function setSelectedPackages(array $selected_packages): ServerInit
    {
        $this->selected_packages = $selected_packages;
        return $this;
    }

    public function setServerOs($server_os): ServerInit
    {
        $this->server_os = $server_os;
        return $this;
    }

    public function setDefaultIp($default_ip): ServerInit
    {
        $this->default_ip = $default_ip;
        return $this;
    }

    public function setHostname($hostname): ServerInit
    {
        $this->hostname = $hostname;
        return $this;
    }

    public function initPackages(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
        $pkgmng = new Packages();
        $pkgmng->setServerId($this->server_id);
        $packages = $pkgmng->pkgList();

        $pkg_list = json_decode(json_decode(json_encode($packages), true), true);
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->error_messages["packages"] = __("Package not initialized. Please contact with FPanel Developers... %error%", ["error" => json_last_error_msg()]);
            return $this;
        }

        if($this->server_os == WINDOWS) {
            $new_pkg_list = array_values(array_unique($pkg_list, SORT_REGULAR));
            $pkg_list1 = $new_pkg_list;
        }
        else {
            $pkg_list1 = $pkg_list;
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
            foreach($pkg_list1 as $package) {
                $status = NOT_INSTALLED;
                if($package["status"] == "installed") {
                    $status = INSTALLED;
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
            }
        }
        else {
            $this->error_messages["packages"] = __("System packages can't get from database");
            return $this;
        }

        $qbuilder = new QueryBuilder();
        $this->installed_packages = $qbuilder->select(["*"])
            ->from("system_packages")
            ->where([
                'conditions' => "system_packages.server_id = ?1",
                'bind' => [1 => $this->server_id]
            ])->find();
        return $this;
    }

    public function initIp(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }

        if(!empty($this->error_messages)) {
            return $this;
        }

        foreach ($this->ip_list as $ip) {
            $ip['ssl_status'] = "0";
            $new_ip = $ip["ip_address"];
            $ip["id"] = $this->env->createUuid();
            if(IpAddress::findFirst("ip_address = '$new_ip'")) {
                $this->log->error([
                    "File" => "ServerInit",
                    "Error" => vsprintf("New ip can not be set. Cloud server ip address already defined in system. Ip data :%s", $ip),
                ]);
                continue;
            }
            $system_ip_address = new IpAddress();
            $system_ip_address->assign($ip);
            if(!$system_ip_address->create()) {
                foreach ($system_ip_address->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return $this;
            }
        }
        $networkmng = new Network();
        $status = $networkmng->writeNetworkConf($this->server_id, $this->server_os);
        if(!$status) {
            $this->error_messages["content"] = __("initIp failed");
            foreach($networkmng->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this;
    }

    public function initDatabaseServer(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }

        if(in_array("mysql", $this->selected_packages)
            || in_array("mysql57", $this->selected_packages)
            || in_array("mysql80", $this->selected_packages)
            || in_array("mariadb", $this->selected_packages)) {
            $this->initMySQLDatabaseServer();
        }

        if($this->server_os == WINDOWS) {
            if(in_array("mssql12", $this->selected_packages) || in_array("mssql16", $this->selected_packages)) {
                $this->initMSSQLDatabaseServer();
            }
        }
        if(in_array("postgresql", $this->selected_packages)) {
            $this->initPostgreSQLServer();
        }


        return $this;
    }

    public function initSystemCommand(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
        $settings = new Settings();
        $settings->assign([
            "auto_update" => "1",
            "update_time" => "02:00",
            "update_server" => "repo.fpanel.net",
            "random_time" => "0",
            "server_id" => $this->server_id,
        ]);
        if(!$settings->create()) {
            $this->error_messages["content"] = __("initSystemCommand failed");
            foreach ($settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return $this;
        }

        $adminmng = new Admin();
        $adminmng->setServerId($this->server_id);
        $adminmng->conf();
        $adminmng->init();
        return $this;
    }

    public function initWebServer(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
        if(in_array("apache", $this->selected_packages) || in_array("nginx", $this->selected_packages) || in_array("iis", $this->selected_packages)) {
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

            if(in_array("php56", $this->selected_packages)) {
                $phps['php56'] = true;
                $this->php_status = true;
            }

            if(in_array("php72", $this->selected_packages)) {
                $phps['php72'] = true;
                $this->php_status = true;
            }

            if(in_array("php74", $this->selected_packages)) {
                $phps['php74'] = true;
                $this->php_status = true;
            }

            if($this->php_status == true) {
                $php_status = 1;
            }

            $main_modules = new WebServer();
            $main_modules->assign(array(
                'php_status' => $php_status,
                'php_versions' => json_encode($phps),
                'perl_status' => $perl_status,
                'python_status' => $python_status,
                'server_id' => $this->server_id,
            ));

            if(!$main_modules->create()) {
                $this->error_messages["content"] = __("initWebServer failed");
                foreach ($main_modules->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
            }
        }
        return $this;
    }

    public function initDns(): ServerInit
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        if(in_array(MSDNS, $this->selected_packages) || in_array(BIND, $this->selected_packages) || in_array(DJBDNS, $this->selected_packages)) {
            $restore_dns = new NamedServer();
            $status = $restore_dns->restoreDNSRecords($this->server_id);
            if(!$status) {
                $this->error_messages["content"] = __("initDns failed");
                foreach($restore_dns->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        return $this;
    }

    public function initLogSettings(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
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
            $this->error_messages["content"] = __("initLogSettings failed");
            foreach($server->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this;
    }

    public function initMailServer(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
        if(in_array("exchange", $this->selected_packages) ||
            in_array("qmail", $this->selected_packages) ||
            in_array("postfix", $this->selected_packages) ||
            in_array("mailenable", $this->selected_packages) ||
            in_array("hmail", $this->selected_packages)) {
            $spam_status = false;
            $mail_server_settings = new Mail();
            $post_data = [];
            if(!empty($this->installed_packages)) {
                foreach ($this->installed_packages as $packages) {
                    if($this->server_os == WINDOWS) {
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

                foreach ($this->installed_packages as $packages) {
                    if(strpos(strtolower($packages['package_name']), "hmail") !== false) {
                        if($packages['status'] == INSTALLED) {
                            $post_data = [
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
                            ];
                            break;
                        }
                    }
                    else if(strpos(strtolower($packages['package_name']), "mailenable") !== false) {
                        if($packages['status'] == INSTALLED) {
                            $post_data = [
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
                            ];
                            break;
                        }
                    }
                    else if(strpos(strtolower($packages['package_name']), "postfix") !== false) {
                        if($packages['status'] == INSTALLED) {
                            $post_data = [
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
                            ];
                            break;
                        }
                    }
                    else if(strpos(strtolower($packages['package_name']), "exchange") !== false) {
                        if($packages['status'] == INSTALLED) {
                            $post_data = array(
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
                            );
                            break;
                        }
                    }
                    else if(strpos(strtolower($packages['package_name']), "qmail") !== false) {
                        if($packages['status'] == INSTALLED) {
                            $post_data = [
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
                            ];
                            break;
                        }
                    }
                }
            }

            if(!empty($post_data)) {
                $mail_server_settings->setPosts($post_data);
                $status = $mail_server_settings->saveMailSettings(true, $this->server_id);
                if(!$status) {
                    $this->error_messages["content"] = __("initMailServer failed");
                    foreach($mail_server_settings->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    return $this;
                }
            }
            else {
                $this->log->error([
                    "Module" => "Postfix->initMailServer",
                    "data" => $mail_server_settings->getPost(),
                    "post_data" => $post_data,
                    "selected_packages" => $this->selected_packages,
                    "errors" => "post_data is empty",
                ]);
            }

        }
        return $this;
    }

    public function initSpamSettings(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
        if(in_array("spamassassin", $this->selected_packages)) {
            $spam_settings = new SpamSettings();
            $status = $spam_settings->saveDefaultSpamSettings(ACTIVE, $this->server_id, ACTIVE);
            if(!$status) {
                $this->error_messages["content"] = __("Spamassassin settings save failed");
                foreach($spam_settings->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        return $this;
    }

    public function initFtpSettings(): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
        $params = [];
        $ftp = false;
        if($this->server_os == WINDOWS && in_array("iis", $this->selected_packages)) {
            $params = array(
                'ftp_type' => 'iis',
                'timeout_idle' => '120',
                'timeout_no_transfer' => '600',
                'maxinstances' => '30',
                'max_clients' => '4294967295',
            );
            $ftp = true;
        }
        else {
            if(in_array("proftpd", $this->selected_packages)) {
                $params = array(
                    'ftp_type' => 'proftpd',
                    'timeout_idle' => '1200',
                    'timeout_no_transfer' => '600',
                    'maxinstances' => '30',
                    'max_clients' => '10',
                );
                $ftp = true;
            }
        }
        if($ftp) {
            $ftp_settings = new ServerFtpSettings();
            $ftp_settings->assign(
                array(
                    'params' => json_encode($params),
                    'server_id' => $this->server_id
                )
            );
            if(!$ftp_settings->create()) {
                $this->error_messages["content"] = __("initFtpSettings failed");
                foreach ($ftp_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return $this;
            }
        }
        return $this;
    }

    public function ipAddresses($ips): ServerInit
    {
        if($this->test_mode) {
            return $this;
        }
        if(!empty($this->error_messages)) {
            return $this;
        }
        $networkmng = new Networking();
        $networkmng->setServerId($this->server_id);
        $ip_list_string = $networkmng->show();

        $ip_list = json_decode($ip_list_string, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->log->error([
                "File" => "ServerInit",
                "Message" => "IP Listing",
                "Error" => $ip_list_string,
                "ServerId" => $this->server_id,
            ]);
            $this->error_messages["ipaddress_lists"] = __("Reading ip list failed");
            return $this;
        }
        $db_ip_save_list = array();
        foreach ($ip_list as $ip_address) {
            $type = EXCLUSIVE;
            $default = "0";
            foreach ($ips as $selected_ip) {
                if ($selected_ip == $ip_address['2'] ) {
                    $type = SHARED;
                }
            }
            if($ip_address['2'] == $this->default_ip) {
                $default = "1";
            }

            if((!empty($ip_address['4'])) && ($ip_address['4'] != null)) {
                $gateway = $ip_address['4'];
            }
            else {
                $gateway = "";
            }
            $ip_interface = $ip_address['1'];
            $index = "";
            if($this->server_os == WINDOWS) {
                $ip_interface = base64_decode($ip_address['1']);
                $index = $ip_address['0'];
            }

            $db_ip_save_list[] = array(
                'ip_address'    => $ip_address['2'],
                'interface'     => $ip_interface,
                'index'         => $index,
                'is_default'    => $default,
                'netmask'       => $ip_address['3'],
                'gateway'       => $gateway,
                'type'          => $type,
                'format'        => NORMAL,
                'server_id'     => $this->server_id,
            );
        }
        if((is_array($ip_list)) && (count($ip_list) > 0)) {
            foreach ($ip_list as $ip) {
                if(!filter_var($ip['2'], FILTER_VALIDATE_IP)) {
                    $this->error_messages["content"] = __("ipAddresses failed");
                    $this->error_messages['ipaddress_lists'] = __('IP Address is not valid');
                }
            }
            $this->ip_list = $db_ip_save_list;
        }
        return $this;
    }


    private function initMySQLDatabaseServer(): void
    {
        $database_server = new DatabaseServer();
        $database_server->setPost("db_server_name", $this->hostname. " (MySQL)");
        $connection = $database_server->connection('mysql',USER_SQL_ADMIN, $this->server_data["sql_secret"],'3306', $this->default_ip, 5);

        if($connection) {
            $status = $database_server->saveMySQLDefaultDatabaseServer(USER_SQL_ADMIN, $this->server_data["sql_secret"], '3306', $this->default_ip, $this->server_id);
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
        $database_server = new DatabaseServer();
        $database_server->setPost("db_server_name", $this->hostname." (Microsoft SQL)");
        $connection = $database_server->connection('mssql','sa', $this->server_data["sql_secret"],'1433', $this->default_ip, 5);
        if($connection) {
            $status = $database_server->saveMSSQLDefaultDatabaseServer('sa', $this->server_data["sql_secret"],'1433', $this->default_ip, $this->server_id);
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

    private function initPostgreSQLServer(): void
    {
        $database_server = new DatabaseServer();
        $database_server->setPost("db_server_name", $this->hostname." (PostgreSQL)");
        $connection = $database_server->connection('pgsql',USER_SQL_ADMIN, $this->server_data["sql_secret"],'5432', $this->default_ip, 10);
        if($connection == false) {
            foreach($database_server->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return;
        }

        $status = $database_server->savePostgreSQLDefaultDatabaseServer(USER_SQL_ADMIN, $this->server_data["sql_secret"],'5432', $this->default_ip, $this->server_id);
        if(!$status) {
            foreach($database_server->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
    }

    public function setTestMode()
    {
        $this->test_mode = true;
    }
}