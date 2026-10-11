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

use Exception;
use FPanel\Environments\Datatable;
use FPanel\Environments\Parser;
use FPanel\Library\Library;
use FPanel\Models\Backup;
use FPanel\Models\ClientIpAddress;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseServerSettings;
use FPanel\Models\DnsTemplate;
use FPanel\Models\Domains;
use FPanel\Models\IpAddress;
use FPanel\Models\MailServerSettings;
use FPanel\Models\ScheduledTasks;
use FPanel\Models\ServerFtpSettings;
use FPanel\Models\ServerLogSettings;
use FPanel\Models\Servers as ServerModel;
use FPanel\Models\Settings;
use FPanel\Models\SpamList;
use FPanel\Models\SpamSettings;
use FPanel\Models\SystemPackages;
use FPanel\Models\WebServer;
use FPanel\Process\Cloud as Cloudmng;
use FPanel\Process\Networking;

class Servers extends Library
{
    protected $server_info = array();
    protected $sql_pass;
    private $server_pkgs;
    private $installed_pkgs;
    private $server_id;
    private $test_mode = false;
    private $test_server_id;
    private $test_server_data;

    const CONNECT = 0;
    const INSTALL = 1;
    const CREATE = 2;
    const SAVE = 3;

    public function serverList(): array
    {
        $datatable = new Datatable();
        $delete_button = $datatable->buttons('server/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        $edit_button = $datatable->buttons('server/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');

        $data = $datatable
            ->select([
                "servers.id",
                "servers.name",
                "servers.os",
                "servers.type",
                "servers.created_date",
            ])
            ->from("servers")
            ->where("servers.type NOT LIKE 'main'")
            ->searchColumn(["servers.name"])
            ->addColumn('actions', $edit_button.$delete_button,'servers.id')
            ->generate()
            ->execute()
            ->result();
        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] =  '<div class="input-group-append">
                                            <a class="dropdown-toggle" style="float: left" data-toggle="dropdown"><i class="fa fa-cog"></i></a>
                                            &nbsp; <a href="'. $this->uri->url_encrypt('server/dashboard/server/'. $value[0]).'">'. $value[1] .'</a>
                                            <ul class="dropdown-menu">
                                                <li><a>'. $value[1] .'</a></li>
                                                <li><hr /></li>
                                                <li><a href="#"><i class="fas fa-power-off"></i> ' . __('Restart') . '</a></li>
                                                <li><a href="#"><i class="fas fa-sync"></i> ' . __('System Optimize') . '</a></li>
                                                <li><a href="#"><i class="fas fa-edit"></i> ' . __('Edit Server') . '</a></li>
                                                <li><a href="#"><i class="fas fa-save"></i> ' . __('Create Backup') . '</a></li>
                                                <li><a href="#"><i class="fa fa-home"></i> ' . __('Dashboard') . '</a></li>
                                                <li><hr/></li>
                                                <li><a href="#"><i class="fa fa-times"></i> ' . __('Delete Server') . '</a></li>
                                            </ul>
                                        </div>';
            $data['aaData'][$key][1] = ($value[2] == LINUX ? '<b class="text-danger"><i class="fp fp-linux"></i> ' : '<b class="text-success"><i class="fp fp-windows"></i> ') . ucfirst($value[2]) . "</b>";

            $data['aaData'][$key][3] = $this->date->getOnlyDate($this->env->convertDate($value[4],false));
            $installeds = [];
            if($value[3] != "main") {
                $services = json_decode($value[3], true);
                foreach ($services as $service => $status) {
                    if($service == "system_packages") {
                        continue;
                    }
                    if($status) {
                        $installeds[] = '<b class="text-navy">' .$service . '</b>';
                    }
                    else {
                        $installeds[] = '<b class="text-danger">' .$service . '</b>';
                    }
                }
            }

            $data['aaData'][$key][2] = implode(" / ", $installeds);
            $data['aaData'][$key][3] = $value[4];
            $data['aaData'][$key][4] = $value[5];

        }
        return $data;
    }

    public function connectionServer(): bool
    {
        if($this->test_mode) {
            return true;
        }
        if($this->validateServer(self::CONNECT) == false)
            return false;

        $status = $this->serverConnectionTest();

        if(!$status)
            return false;
        else
            return true;
    }

    public function installationServer(): bool
    {
        if(!$this->test_mode) {
            if($this->validateServer(self::INSTALL) == false) {
                return false;
            }
        }

        $status = $this->installation();
        if(!$status)
            return false;
        else
            return true;
    }

    public function getServerConfiguration(): bool
    {
        if($this->test_mode) {
            return true;
        }

        $cloud = new Cloudmng();
        $params = [
            'user' => $this->session->get("username"),
            'passwd' => $this->session->get("password"),
            'os' => $this->session->get("server_os"),
            'ipaddr' => $this->session->get("ip_address"),
        ];

        $result = $cloud->getConfig($params);
        if(strpos($result,'Err') !== false) {
            $this->error_messages['content'] = trim($result);
            return false;
        }
        else {
            if(trim($result) == "Connection error") {
                $this->error_messages['content'] = trim($result);
                return false;
            }
        }

        $config = json_decode($result, true);
        if(json_last_error() == JSON_ERROR_NONE) {
            return $this->createServer($config["result"]);
        }

        $this->log->error([
            "File" => "Cloud Server -> getServerConfiguration",
            "Error" => trim($result),
        ]);
        return false;
    }

    public function setMainServer(): bool
    {
        if($this->test_mode) {
            return true;
        }
        $cloud = new Cloudmng();
        $networkmng = new Networking();
        $params = [
            'user' => $this->session->get("username"),
            'passwd' => $this->session->get("password"),
            'os' => $this->session->get("server_os"),
            'ipaddr' => $this->session->get("ip_address"),
            'main-ip' => $networkmng->defaultIpAddress(),
        ];

        $result = $cloud->setMain($params);
        if(strpos($result,'Err') !== false) {
            $this->error_messages['content'] = trim($result);
            return false;
        }
        if(trim($result) == "Connection error") {
            $this->error_messages['content'] = trim($result);
            return false;
        }

        return true;
    }

    public function rebootServer(): bool
    {
        if($this->test_mode)
            return true;
        $cloud = new Cloudmng();
        $params = [
            'user' => $this->session->get("username"),
            'passwd' => $this->session->get("password"),
            'os' => $this->session->get("server_os"),
            'ipaddr' => $this->session->get("ip_address"),
        ];

        $cloud->rebootServer($params);
        return true;
    }

    public function initServer($server_id)
    {
        // We have to wait until server is up after reboot
        if(!$this->test_mode) {
            executeWithoutErrorHandler(function () {
                $started_time = time();
                $host = $this->session->get("ip_address");
                $port = 8889;
                $connected = false;
                while (true) {
                    $current_time = time();
                    if(($current_time - $started_time) > 1800) {
                        break;
                    }
                    try {
                        $fp = @fsockopen($host, $port, $errno, $errstr, 1);
                        if ($fp) {
                            $connected = true;
                            fclose($fp);
                            break;
                        }
                    }
                    catch (Exception $e) {
                    }
                    sleep(1);
                }
                if(!$connected) {
                    $this->log->error([
                        "File" => "Cloud Server Connection",
                        "Error" => "Connection timeout after reboot please contact with FPanel Developers",
                    ]);
                    $this->error_messages["content"] = __("Connection timeout after reboot please contact with FPanel Developers");
                }
            });
        }

        $server = ServerModel::findFirstById($server_id);
        $server_init = new ServerInit();
//        $server_init->setTestMode();
        $server_init
            ->setServer($server)
            ->setSelectedPackages($this->installed_pkgs)
            ->setServerOs($this->session->get("server_os"))
            ->setDefaultIp($this->session->get("ip_address"))
            ->setHostname($this->getPost("hostname"))
            ->ipAddresses($this->session->get("ips"))
            ->initPackages()
            ->initIp()
            ->initDatabaseServer()
            ->initSystemCommand()
            ->initWebServer()
            ->initDns()
            ->initLogSettings()
            ->initSpamSettings()
            ->initMailServer()
            ->initFtpSettings();
        if(!empty($server_init->getMessages())) {
            $this->error_messages = $server_init->getMessages();
            return false;
        }

        $config_ini = new Parser(ROOTPATH ."/config/config.ini");
        $config_ini->data['clients']["address"][] = $this->session->get("ip_address");
        $config_ini->write();
        return json_decode($server->type, true);
    }

    public function saveServer(): bool
    {
        if($this->validateServer(self::SAVE) == false) {
            return false;
        }

        $server = ServerModel::findFirstById($this->getPost("server_id"));
        if($server) {
            $server->name = $this->getPost("server_name");
            $server->domain_limit = $this->getPost("domain_limit", UNLIMITED) == "0" ? UNLIMITED : $this->getPost("domain_limit", UNLIMITED);
            $server->database_limit = $this->getPost("database_limit", UNLIMITED) == "0" ? UNLIMITED : $this->getPost("database_limit", UNLIMITED);
            $server->mail_limit = $this->getPost("mail_limit", UNLIMITED) == "0" ? UNLIMITED : $this->getPost("mail_limit", UNLIMITED);
            $server->description = $this->getPost("description");
            if(!$server->update()) {
                foreach ($server->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
            $this->server_id = $server->id;
            return true;
        }
        else {
            $this->error_messages["content"] = __("Server not found");
            return false;
        }
    }

    public function deleteServer(): bool
    {
        $server_id = $this->getPost("server_id");
        $domain = Domains::findFirst("web_server_id = '$server_id' or dns_server_id = '$server_id' or mail_server_id = '$server_id'");
        if($domain) {
            $this->error_messages['content'] = __('Server can not delete. Please migrate domains to another server.');
            return false;
        }
        $ip_addresses = IpAddress::find("server_id = '$server_id'");
        foreach ($ip_addresses as $ip_address) {
            $users = ClientIpAddress::findFirst("ipaddr_id = '$ip_address->id'");
            if($users) {
                $this->error_messages['content'] = __('Server can not delete. Please migrate users to another server.');
                return false;
            }
        }
        if($this->deleteServerData($server_id) == true) {
            return true;
        }
        else {
            return false;
        }
    }

    private function deleteServerData($server_id): bool
    {
        //@TODO: Check domains if exists
        $server = ServerModel::findFirstById($server_id);
        $packages = SystemPackages::find("server_id = '$server_id'");
        $settings = Settings::find("server_id = '$server_id'");

        $ip_addresses = IpAddress::find("server_id = '$server_id'");
        $client_ipaddresses = $ip_addresses->toArray();
        $log_settings = ServerLogSettings::find("server_id = '$server_id'");
        $dns = DnsTemplate::find("server_id = '$server_id'");
        $ftp_server = ServerFtpSettings::find("server_id = '$server_id'");
        $web_server = WebServer::find("server_id = '$server_id'");
        $mail_server = MailServerSettings::find("server_id = '$server_id'");

        $database_servers = DatabaseServer::find("server_id = '$server_id'");

        $cronjobs = ScheduledTasks::find("server_id = '$server_id'");
        $backups = Backup::find("server_id = '$server_id'");
        $spam_list = SpamList::find("server_id = '$server_id'");
        $system_spam_settings = SpamSettings::find("server_id = '$server_id'");


        if(!empty($packages))
            $packages->delete();
        if(!empty($settings))
            $settings->delete();
        if(!empty($ip_addresses))
            $ip_addresses->delete();
        if(!empty($log_settings))
            $log_settings->delete();
        if(!empty($dns))
            $dns->delete();
        if(!empty($ftp_server))
            $ftp_server->delete();
        if(!empty($web_server))
            $web_server->delete();
        if(!empty($mail_server))
            $mail_server->delete();
        foreach ($database_servers as $database_server) {
            $database_server_settings = DatabaseServerSettings::find("database_server_id = '$database_server->id'");
            if(!empty($database_server_settings))
                $database_server_settings->delete();
        }
        if(!empty($database_servers))
            $database_servers->delete();
        if(!empty($cronjobs))
            $cronjobs->delete();
        if(!empty($backups))
            $backups->delete();
        if(!empty($spam_list))
            $spam_list->delete();
        if(!empty($system_spam_settings))
            $system_spam_settings->delete();
        if($server)
            $server->delete();
        $config_ini = new Parser(ROOTPATH ."/config/config.ini");
        foreach ($config_ini->data["clients"] as $index => $client) {
            foreach ($client_ipaddresses as $client_ipaddress) {
                if($client == $client_ipaddress["ip_address"]) {
                    unset($config_ini->data["clients"][$index]);
                }
            }
        }
        $config_ini->write();
        return true;
    }

    private function createServer($config): bool
    {
        $domain_limit = UNLIMITED;
        $database_limit = UNLIMITED;
        $mail_limit = UNLIMITED;
        $server = new ServerModel();
        $is_core = strpos(strtolower($this->session->get("installed_os")), "core") !== false;

        $installed_services = [
            "WEB" => false,
            "DATABASE" => false,
            "DNS" => false,
            "MAIL" => false,
        ];
        $this->serverPackages();

        foreach ($this->server_pkgs as $server_pkg) {
            if($this->has($server_pkg) && $this->getPost($server_pkg) == "on") {
                if($this->session->get("server_os") == WINDOWS && $is_core) {
                    $installed_services["MAIL"] = false;
                }
                else {
                    if(in_array($server_pkg, ["mailenable", "hmail", "exchange", "qmail", "postfix"])) {
                        $installed_services["MAIL"] = true;
                    }
                }
                if(in_array($server_pkg, ["iis", "apache", "nginx"])) {
                    $installed_services["WEB"] = true;
                }
                if(in_array($server_pkg, ["mysql", "mariadb", "postgresql", "mssql12", "mssql16", "mysql57", "mysql80"])) {
                    $installed_services["DATABASE"] = true;
                }
                if(in_array($server_pkg, ["bind", "msdns", "djbdns"])) {
                    $installed_services["DNS"] = true;
                }
            }
        }

        $installed_services["system_packages"] = [];
        foreach ($config["system_packages"] as $key => $value) {
            $installed_services["system_packages"][$key] = $value;
        }

        $server_data = array(
            'key' => $config['key'],
            'iv' => $config['iv'],
            'default_ip' => $this->session->get("ip_address"),
            'sql_pass' => $config['sql_pass'],
            'sql_secret' => $config['sql_secret'],
        );
        $this->sql_pass = $config['sql_pass'];

        $server->assign(array(
            "id"                => $this->env->createUuid(),
            'name'              => $this->session->get("hostname"),
            'description'       => $this->session->get("installed_os"),
            "is_core"           => $is_core,
            'os'                => $this->session->get("server_os"),
            'type'              => json_encode($installed_services),
            'ip_address'        => $this->session->get("ip_address"),
            'domain_limit'      => $domain_limit,
            'database_limit'    => $database_limit,
            'mail_limit'        => $mail_limit,
            'data'              => $this->crypto->bencode(json_encode($server_data))
        ));

        if(!$server->create()) {
            foreach ($server->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Cloud Server -> createServer",
                "Error" => $this->error_messages,
            ]);
            return false;
        }

        $this->server_id = $server->id;
        return true;
    }

    private function validateServer($type): bool
    {
        $server_types = null;
        $required = [];
        if($type == self::CONNECT) {
            $required = [
                [
                    'item' => $this->getPost("ip_address"),
                    'field' => 'ip_address',
                    'message' => __('IP Address is required')
                ],
                [
                    'item' => $this->getPost("username"),
                    'field' => 'username',
                    'message' => __('Username is required')
                ],
                [
                    'item' => $this->getPost("password"),
                    'field' => 'password',
                    'message' => __('Password is required')
                ],
                [
                    'item' => $this->getPost("server_os"),
                    'field' => 'server_os',
                    'message' => __('Please select a server type')
                ],
                [
                    'item' => $this->getPost("hostname"),
                    'field' => 'hostname',
                    'message' => __('Please enter a hostname')
                ],
            ];
        }
        else if($type == self::SAVE) {
            $required = [
                [
                    'item' => $this->getPost("server_name"),
                    'field' => 'server_name',
                    'message' => __('Server Name is required')
                ],
                [
                    'item' => $this->getPost("server_id"),
                    'field' => 'server_id',
                    'message' => __('Unidentified data')
                ],
            ];
            if($this->has("server_id") && $this->getPost("server_id") != null) {
                $server = ServerModel::findFirstById($this->getPost("server_id"));
                $server_types = json_decode($server->type, true);
                if($server_types["WEB"]) {
                    $required[] = [
                        'item' => $this->getPost("domain_limit"),
                        'field' => 'domain_limit',
                        'message' => __('Please enter domain limit')
                    ];
                }
                if($server_types["DATABASE"]) {
                    $required[] = [
                        'item' => $this->getPost("database_limit"),
                        'field' => 'database_limit',
                        'message' => __('Please enter database limit')
                    ];
                }
                if($server_types["MAIL"]) {
                    $required[] = [
                        'item' => $this->getPost("mail_limit"),
                        'field' => 'mail_limit',
                        'message' => __('Please enter mail limit')
                    ];
                }
            }
            $hostname = $this->request->getPost("hostname");
            $server_validation = ServerModel::findFirst("name = '$hostname'");
            if($server_validation) {
                $this->error_messages["hostname"] = __("This server hostname already set");
                return false;
            }
        }
        $local = false;
        $atleast_one_selected = true;

        $this->validation->Required($required);
        if($type == self::CONNECT) {
            $this->validation->Ip($this->getPost("ip_address"),'ip_address')
                ->InclusionIn($this->getPost("server_os"), "server_os", array(WINDOWS, LINUX));

            $this->validation->Domain($this->getPost("hostname"),'hostname');

            $networkmng = new Networking();
            $local_ips = $networkmng->show();
            foreach (json_decode($local_ips, true) as $item) {
                if($item["2"] == $this->getPost("ip_address")) {
                    $local = true;
                    $this->error_messages["ip_address"] = __("You can not use this server");
                    break;
                }
            }

            $ip_address = $this->getPost("ip_address");
            $server_validation = ServerModel::findFirst("ip_address = '$ip_address'");
            if($server_validation) {
                $this->error_messages["ip_address"] = __("This server ip already defined");
                return false;
            }
        }
        else if($type == self::INSTALL) {
//            $this->validation->Domain($this->request->getPost("hostname"),"hostname",__('Please enter valid hostname'));
            $server_pkgs = [
                'perl',
                'python',
                'php56',
                'php72',
                'php74',
                'roundcube',
                'phpmyadmin',
                'awstats',
                'webalizer',
                'bind',
                'postgresql',
                'spamassassin',
            ];
            if($this->session->get("server_os") == WINDOWS) {
                $server_pkgs[] ='iis';
                $server_pkgs[] ='mylittleadmin';
                $server_pkgs[] ='msdns';
                $server_pkgs[] ='mssql12';
                $server_pkgs[] ='mssql16';
                $server_pkgs[] ='mysql57';
                $server_pkgs[] ='mysql80';
                $server_pkgs[] ='hmail';
                $server_pkgs[] ='mailenable';
            }
            else {
                $server_pkgs[] ='apache';
                $server_pkgs[] ='nginx';
                $server_pkgs[] ='proftpd';
                $server_pkgs[] ='mysql';
                $server_pkgs[] ='mariadb';
                $server_pkgs[] ='postfix';
                $server_pkgs[] ='dovecot';
            }
            $atleast_one_selected = false;
            foreach ($server_pkgs as $server_pkg) {
                if($this->has($server_pkg) && $this->getPost($server_pkg) == "on") {
                    $atleast_one_selected = true;
                    break;
                }
            }
            if(!$atleast_one_selected) {
                $this->error_messages["content"] = __("Please select at least one service");
            }
        }
        else if($type == self::SAVE) {
            if($this->has("server_id") && $this->getPost("server_id") != null) {
                if($server_types["MAIL"]) {
                    $this->validation->Numeric($this->getPost("mail_limit"), "mail_limit");
                }
                if($server_types["DATABASE"]) {
                    $this->validation->Numeric($this->getPost("database_limit"), "database_limit");
                }
                if($server_types["WEB"]) {
                    $this->validation->Numeric($this->getPost("domain_limit"), "domain_limit");
                }
            }
        }
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        if($local) {
            return false;
        }

        if(!$atleast_one_selected) {
            return false;
        }

        return true;
    }

    private function serverConnectionTest(): bool
    {
        if($this->test_mode) {
            return true;
        }
        $cloud = new Cloudmng();
        $params = [
            'user' => $this->getPost("username"),
            'passwd' => $this->getPost("password"),
            'os' => $this->getPost("server_os"),
            'ipaddr' => $this->getPost("ip_address")
        ];

        $result = $cloud->connection($params);

        if(strpos($result,'Err') !== false) {
            if(strpos($result, "Connection")) {
                $this->error_messages['content'] = __("Connection failed. Please check server info and try again.");
            }
            else {
                $this->error_messages['content'] = __("Connection error. Please make sure your information is correct or the server is accessible.");
            }
            return false;
        }
        else {
            if(trim($result) == "Connection error") {
                $this->error_messages['content'] = __("Connection error. Please make sure your information is correct or the server is accessible.");
                return false;
            }
        }

        $server_info = json_decode($result, true);
        if(json_last_error() == JSON_ERROR_NONE) {
            $this->server_info = $server_info;
            return true;
        }
        $this->log->error([
            "File" => "Cloud Server Connection",
            "Error" => $result,
        ]);
        return false;
    }

    private function installation(): bool
    {
        $networkmng = new Networking();

        $cloud = new Cloudmng();
        $params = [
            'user' => $this->session->get("username"),
            'passwd' => $this->session->get("password"),
            'os' => $this->session->get("server_os"),
            'ipaddr' => $this->session->get("ip_address"),
            'hostname' => $this->session->get("hostname"),
        ];
        $is_core = (strpos(strtolower($this->session->get("installed_os")), "core") !== false);

        $this->serverPackages();

        $install_params = [];
        foreach ($this->server_pkgs as $server_pkg) {
            if($this->has($server_pkg) && $this->getPost($server_pkg) == "on") {
                if($this->session->get("server_os") == WINDOWS) {
                    if(in_array($server_pkg, ["apache", "nginx", "proftpd", "mariadb", "postfix", "dovecot", "mysql"])) {
                        continue;
                    }
                    if($is_core) {
                        if(in_array($server_pkg, ["mailenable", "hmail", "exchange", "mssql12"])) {
                            continue;
                        }
                    }
                    if($server_pkg == "phpmyadmin")
                        $server_pkg = "phpmyadmin50";
                    if($server_pkg == "postgresql")
                        $server_pkg = "postgresql13";
                    if($server_pkg == "python")
                        $server_pkg = "python27#python39";
                }
                else {
                    if(in_array($server_pkg, ["iis", "mylittleadmin", "msdns", "mssql12", "mssql16", "mysql57", "mysql80", "hmail", "mailenable"])) {
                        continue;
                    }
                }
                $install_params[] = $server_pkg;
            }
        }

        $params["parts"] = implode("#", $install_params);
        $params["main-ip"] = $networkmng->defaultIpAddress();

        if($this->test_mode)
            return true;

        $result = $cloud->install($params);

        if(strpos($result,'Err') !== false) {
            $this->error_messages['content'] = trim($result);
            return false;
        }
        else {
            if(trim($result) == "Connection error") {
                $this->error_messages['content'] = trim($result);
                $this->log->error([
                    "File" => "Cloud Server Connection",
                    "Error" => $result,
                ]);
                return false;
            }
        }
        return true;
    }

    private function serverPackages()
    {
        $is_core = (strpos(strtolower($this->session->get("installed_os")), "core") !== false);
        $server_pkgs = [
            'perl',
            'python',
            'php56',
            'php72',
            'php74',
            'roundcube',
            'phpmyadmin',
            'awstats',
            'webalizer',
            'bind',
            'postgresql',
            'spamassassin',
        ];
        if($this->session->get("server_os") == WINDOWS) {
            $server_pkgs[] ='iis';
            $server_pkgs[] ='mylittleadmin';
            $server_pkgs[] ='msdns';
            $server_pkgs[] ='mssql12';
            $server_pkgs[] ='mssql16';
            $server_pkgs[] ='mysql57';
            $server_pkgs[] ='mysql80';
            if(!$is_core) {
                $server_pkgs[] ='hmail';
                $server_pkgs[] ='mailenable';
            }
        }
        else {
            $server_pkgs[] ='apache';
            $server_pkgs[] ='nginx';
            $server_pkgs[] ='proftpd';
            $server_pkgs[] ='mysql';
            $server_pkgs[] ='mariadb';
            $server_pkgs[] ='postfix';
            $server_pkgs[] ='dovecot';
        }

        $this->server_pkgs = $server_pkgs;

        foreach ($this->server_pkgs as $server_pkg) {
            if($this->has($server_pkg) && $this->getPost($server_pkg) == "on") {
                $this->installed_pkgs[] = $server_pkg;
            }
        }
    }

    public function getServerInfo(): array
    {
        if($this->test_mode) {
            $this->server_info["result"] = [
                "memory" => [
                    "used" => "0.7GB",
                    "total" => "2GB",
                ],
                "disk" => [
                    "used" => "5GB",
                    "total" => "50GB",
                ],
                "cpu" => "AMD Ryzen9",
                "hostname" => "ubuntu20",
                "os" => "linux",
                "default_ip" => "192.168.139.42",
                "ips" => [
                    "192.168.139.42"
                ],
            ];
        }
        else {

//            $old = $this->server_info["result"];
            $this->server_info["result"]["memory"]["used"] = $this->env->convert(trim($this->server_info["result"]["memory"]["used"]));
            $this->server_info["result"]["memory"]["total"] = $this->env->convert($this->server_info["result"]["memory"]["total"]);


            $this->server_info["result"]["disk"]["used"] = $this->env->convert($this->server_info["result"]["disk"]["used"]);
            $this->server_info["result"]["disk"]["total"] = $this->env->convert($this->server_info["result"]["disk"]["total"]);

        }

        return $this->server_info;
    }

    public function getSqlPass()
    {
        if($this->test_mode)
            return $this->test_server_data["sql_pass"];

        return $this->sql_pass;
    }

    public function getServerId()
    {
        if($this->test_mode)
            return $this->test_server_id;
        return $this->server_id;
    }

    public function setTest()
    {
        $this->test_mode = true;
        $this->test_server_id = "29C0E2B2-BFDA-F5E8-9ED3-C14FB9A1B748";
        $server = ServerModel::findFirst("id = '$this->test_server_id'");
        if(!$server) {
            $this->debug->debug("$this->test_server_id", false);
        }
        $this->server_id = $server->id;
        $server_data = json_decode($this->crypto->bdecode($server->data), true);
        $this->test_server_data = $server_data;
    }

    public function updateServer($server_id)
    {
        $server = ServerModel::findFirstById($server_id);
        if(!$server) {
            $this->error_messages["content"] = __("Server not found");
            return false;
        }

        if(!$this->updateServerValidation($server_id)) {
            return false;
        }


        $server->description = $this->getPost("description");
        $server->domain_limit = $this->getPost("domain_limit") == null ? "unlimited" : $this->getPost("domain_limit");
        $server->mail_limit = $this->getPost("mail_limit") == null ? "unlimited" : $this->getPost("mail_limit");
        $server->database_limit = $this->getPost("database_limit") == null ? "unlimited" : $this->getPost("database_limit");
        $server->ip_address = $this->getPost("ip_address");
        $server->name = $this->getPost("hostname");

        if(!$server->update()) {
            foreach ($server->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }
        return true;
    }

    private function updateServerValidation($server_id)
    {
        $required = [
            [
                'item' => $this->getPost("ip_address"),
                'field' => 'ip_address',
                'message' => __('IP Address is required')
            ],
            [
                'item' => $this->getPost("hostname"),
                'field' => 'hostname',
                'message' => __('Please enter a hostname')
            ],
        ];

        $this->validation->Required($required);

        $this->validation->Domain($this->getPost("hostname"),'hostname');

        if($this->has("domain_limit") && $this->getPost("domain_limit") != null) {
            $this->validation->Numeric($this->getPost("domain_limit"), "domain_limit");
        }
        if($this->has("database_limit") && $this->getPost("database_limit") != null) {
            $this->validation->Numeric($this->getPost("database_limit"), "database_limit");
        }
        if($this->has("mail_limit") && $this->getPost("mail_limit") != null) {
            $this->validation->Numeric($this->getPost("mail_limit"), "mail_limit");
        }

        $this->validation->Ip($this->getPost("ip_address"),'ip_address');
        $ip_address = $this->getPost("ip_address");
        $server_validation = ServerModel::findFirst("ip_address = '$ip_address' and id != '$server_id'");
        if($server_validation) {
            $this->error_messages["ip_address"] = __("This server ip already defined");
            return false;
        }
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}