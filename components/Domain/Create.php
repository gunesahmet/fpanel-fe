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
 */namespace FPanel\Components\Domain;

use FPanel\Components\Base;
use FPanel\Domain\DomainSettings;
use FPanel\Domain\Mails;
use FPanel\Domain\NamedService;
use FPanel\Domain\Services;
use FPanel\Models\Domains;
use FPanel\Models\IpAddress;
use FPanel\Models\Servers;
use FPanel\Process\Admin;
use FPanel\Process\Bind;
use FPanel\Process\Directory;
use FPanel\Process\Mail;
use FPanel\Process\User;
use FPanel\Process\Vhost;
use FPanel\System\LimitsPermission;
use FPanel\System\Statistics;
use FPanel\Users\Usersdata;

/**
 * Class Create
 * @package FPanel\Components\Create
 */
class Create extends Base
{
    use Helper;

    protected $ip_address;

    protected $domain_id;
    protected $created_user_id;

    protected $dns_zone_id;

    public function isCloud($api = false): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            if($this->system_type == CLOUD) {
                $manual_select = $this->post["manual_select_server"];
                //@Note: RANDOM SELECTION
                if($manual_select != ON || $manual_select === false) {
                    $web_servers = [];
                    $dns_servers = [];
                    $mail_servers = [];
                    $servers = Servers::find("type != 'main'");
                    foreach ($servers as $server) {
                        $total_domains_right = $this->getRemainingDomainRight($server->id);

                        $web_servers[$server->id] = 0;
                        $mail_servers[$server->id] = 0;
                        $dns_servers[$server->id] = 0;

                        $server_types = json_decode($server->type, true);
                        if($server_types["WEB"]) {
                            $total_domains = Domains::find("web_server_id = '$server->id'")->count();
                            if((int)$total_domains < $total_domains_right) {
                                $web_servers[$server->id] = $total_domains;
                            }
                        }
                        if($server_types["MAIL"]) {
                            $total_domains = Domains::find("mail_server_id = '$server->id'")->count();
                            if((int)$total_domains < $total_domains_right) {
                                $mail_servers[$server->id] = $total_domains;
                            }
                        }
                        if($server_types["DNS"]) {
                            $total_domains = Domains::find("dns_server_id = '$server->id'")->count();
                            if((int)$total_domains < $total_domains_right) {
                                $dns_servers[$server->id] = $total_domains;
                            }
                        }
                    }
                    if(empty($web_servers)) {
                        $this->error_messages[] = __("There is no WEB server or used all domain rights");
                        return $this;
                    }
                    if(empty($dns_servers)) {
                        $this->error_messages[] = __("There is no DNS server or used all domain rights");
                        return $this;
                    }
                    if(empty($mail_servers)) {
                        $this->error_messages[] = __("There is no MAIL server or used all domain rights");
                        return $this;
                    }
                    asort($web_servers);
                    asort($dns_servers);
                    asort($mail_servers);
                    $web_server_id = key($web_servers);
                    $dns_server_id = key($dns_servers);
                    $mail_server_id = key($mail_servers);

                    $this->post["web_servers"] = $web_server_id;
                    $this->post["dns_servers"] = $dns_server_id;
                    $this->post["mail_servers"] = $mail_server_id;
                    $server_ips = Servers::findFirstById($web_server_id)->getIpAddress("type = '1'");
                    if(!empty($server_ips)) {
                        $this->post["ipaddr_id"] = $server_ips[0]->id;
                    }
                }
                else {
                    if(!$api) {
                        $web_server_ip_id = $this->post["web_servers"];
                        $dns_server_ip_id = $this->post["dns_servers"];
                        $mail_server_ip_id = $this->post["mail_servers"];
                        $web_server = IpAddress::findFirst("id = '$web_server_ip_id'");
                        $dns_server = IpAddress::findFirst("id = '$dns_server_ip_id'");
                        $mail_server = IpAddress::findFirst("id = '$mail_server_ip_id'");

                        $web_remaining = $this->getRemainingDomainRight($web_server->server_id);
                        $dns_remaining = $this->getRemainingDomainRight($dns_server->server_id);
                        $mail_remaining = $this->getRemainingDomainRight($mail_server->server_id);


                        $total_domains_web = Domains::find("web_server_id = '$web_server->id'")->count();
                        $total_domains_dns = Domains::find("dns_server_id = '$dns_server->id'")->count();
                        $total_domains_mail = Domains::find("mail_server_id = '$mail_server->id'")->count();

                        if($web_remaining <= $total_domains_web) {
                            $this->error_messages[] = __("There is no WEB server or used all domain rights");
                            return $this;
                        }

                        if($dns_remaining <= $total_domains_dns) {
                            $this->error_messages[] = __("There is no DNS server or used all domain rights");
                            return $this;
                        }

                        if($mail_remaining <= $total_domains_mail) {
                            $this->error_messages[] = __("There is no MAIL server or used all domain rights");
                            return $this;
                        }
                        $this->post["ipaddr_id"] = $this->post["web_servers"];

                        $this->post["web_servers"] = IpAddress::findFirstById($this->post["web_servers"])->getServers()->id;
                        $this->post["dns_servers"] = IpAddress::findFirstById($this->post["dns_servers"])->getServers()->id;
                        $this->post["mail_servers"] = IpAddress::findFirstById($this->post["mail_servers"])->getServers()->id;
                    }
                    else {
                        $web_server_ip = $this->post['web_server_ip'];
                        $dns_server_ip = $this->post['dns_server_ip'];
                        $mail_server_ip = $this->post['mail_server_ip'];
                        $web_server_ips = IpAddress::findFirst("ip_address = '$web_server_ip'");
                        if(!$web_server_ips) {
                            throw new \Exception(sprintf("IP: %s not found", $web_server_ip));
                        }
                        $dns_server_ips = IpAddress::findFirst("ip_address = '$dns_server_ip'");
                        if(!$dns_server_ips) {
                            throw new \Exception(sprintf("IP: %s not found", $dns_server_ip));
                        }
                        $mail_server_ips = IpAddress::findFirst("ip_address = '$mail_server_ip'");
                        if(!$mail_server_ips) {
                            throw new \Exception(sprintf("IP: %s not found", $mail_server_ip));
                        }

                        $web_remaining = $this->getRemainingDomainRight($web_server_ips->server_id);
                        $dns_remaining = $this->getRemainingDomainRight($dns_server_ips->server_id);
                        $mail_remaining = $this->getRemainingDomainRight($mail_server_ips->server_id);

                        $total_domains_web = Domains::find("web_server_id = '$web_server_ips->server_id'")->count();
                        $total_domains_dns = Domains::find("dns_server_id = '$dns_server_ips->server_id'")->count();
                        $total_domains_mail = Domains::find("mail_server_id = '$mail_server_ips->server_id'")->count();

                        if($web_remaining <= $total_domains_web) {
                            $this->error_messages[] = __("There is no WEB server or used all domain rights");
                            return $this;
                        }

                        if($dns_remaining <= $total_domains_dns) {
                            $this->error_messages[] = __("There is no DNS server or used all domain rights");
                            return $this;
                        }

                        if($mail_remaining <= $total_domains_mail) {
                            $this->error_messages[] = __("There is no MAIL server or used all domain rights");
                            return $this;
                        }

                        $this->post["ipaddr_id"] = $web_server_ips->id;

                        $this->post["web_servers"] = $web_server_ips->getServers()->id;
                        $this->post["dns_servers"] = $dns_server_ips->getServers()->id;
                        $this->post["mail_servers"] = $mail_server_ips->getServers()->id;
                    }

                }
            }
            else {
                $ip_address_id = $this->post['ipaddr_id'];
                $web_server_ips = IpAddress::findFirst("id = '$ip_address_id'");
                if(!$web_server_ips) {
                    throw new \Exception(sprintf("IP: %s not found", $ip_address_id));
                }
                $this->post["web_servers"] = $web_server_ips->getServers()->id;
                $this->post["dns_servers"] = $web_server_ips->getServers()->id;
                $this->post["mail_servers"] = $web_server_ips->getServers()->id;
                $this->ip_address = $web_server_ips->ip_address;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->isCloud",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            $this->error_messages[] = $e->getMessage();
        }

        return $this;
    }

    public function validation(): bool
    {
        if(!$this->validator()) {
            $this->error_messages = $this->getMessages();
            return false;
        }
        return true;
    }

    public function validationForApi(): bool
    {
        if(!$this->apiValidator()) {
            $this->error_messages =  $this->getMessages();
            return false;
        }
        return true;
    }

    public function getIpAddress(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            if($this->system_type == STANDALONE) {
                if(isset($this->post['ipaddr_id'])) {
                    $system_ip_address = new IpAddress();
                    $ip_address = $system_ip_address->findFirstById($this->post['ipaddr_id']);
                    if($ip_address) {
                        $this->ip_address = $ip_address->ip_address;
                    }
                    else {
                        $this->error_messages["ip_address"] = __("Ip Address not found %s", $this->post["ipaddr_id"]);
                    }
                }
                else {
                    $this->error_messages["ip_address"] = __("Please select a valid ip address");
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->getIpAddress",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
        return $this;
    }

    public function domain(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $domain = new DomainSettings();
            $domain->parent_id = $this->parent_id;
            $domain->parent_type = $this->parent_type;
            $domain->setPosts($this->post);

            $status = $domain->create($this->dates);
            if (!$status) {
                foreach ($domain->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
            else {
                $this->domain_id = $status;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->domain",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }

        return $this;
    }

    public function saveUser(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $user = new Usersdata();
            $user->setPosts($this->post);
            $user->parent_id = $this->parent_id;
            $user->options = $this->options;
            $created_user_messages = $user->create($this->dates);

            if ($created_user_messages !== false) {
                $this->created_user_id = $created_user_messages;
            }
            else {
                foreach ($user->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->saveUser",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }

        return $this;
    }

    public function saveLimitsPermissions($restore): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $save_limits = new LimitsPermission();
            $save_limits->setPosts($this->post);
            $save_limits->user_id = $this->created_user_id;
            $save_limits->domain_id = $this->domain_id;
            $status = $save_limits->save($restore);

            if (!$status) {
                foreach ($save_limits->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->saveLimitsPermissions",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
        return $this;
    }

    public function saveStatistics(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $save_domain_statistics = new Statistics();
            $save_domain_statistics->setPosts($this->post);
            $domain_statistics_status = $save_domain_statistics->saveDomainStatistic($this->domain_id, $this->parent_id, $this->dates);

            if (!$domain_statistics_status) {
                foreach ($save_domain_statistics->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->saveStatistics",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
        return $this;
    }

    public function saveDnsZone(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $dns = new NamedService();
            $dns->setPosts(array(
                'zone_type' => 'domain',
                'domain_id' => $this->domain_id,
                'domain_name' => $this->post['name'],
                'status' => '1',
                'email' => 'admin@'.$this->post['name'],
                'type' => 'master',
                'ttl' => '86400',
                'refresh' => '10800',
                'retry' => '3600',
                'expire' => '604800',
                'minimum' => '10800',
                'serial_format' => 'UNIXTIMESTAMP',
                'serial' => time(),
                'ip_address' => $this->ip_address,
                'server_id' => $this->post["dns_servers"]
            ));
            $dns_zone = $dns->saveZone();

            if(!$dns_zone) {
                foreach ($dns->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
            else {
                $this->dns_zone_id = $dns_zone;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->saveDnsZone",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
        return $this;
    }

    public function createDnsRecords(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $dnsmng = new Bind();

            if($this->post['dns_service'] == "on") {
                $dnsmng->setServerId($this->post["dns_servers"]);
                $dnsmng->create([
                    'domain' => $this->post['name'],
                ]);
            }

            $dns = new NamedService();
            $dns->setPosts($this->post);
            $ip_address = $this->ip_address;

            $dns->dns_zone_id = $this->dns_zone_id;
            $dns->ip_address = $ip_address;
            $dns_save_status = $dns->saveDefaultDNS($this->domain_id, $this->post["dns_servers"]);

            if(!$dns_save_status) {
                foreach ($dns->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->createDnsRecords",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
        return $this;
    }

    public function saveServices(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $services = new Services();
            $services->setPosts(array(
                'domain_id'     => $this->domain_id,
                'web'  => $this->post['web_service'],
                'dns'  => $this->post['dns_service'],
                'mail'  => $this->post['mail_service'],
            ));
            $services_status = $services->create();
            if(!$services_status) {
                foreach ($services->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->saveServices",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }

        return $this;
    }

    public function saveMailSettings(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        try {
            $mail = new Mails();
            $mail->setPosts(array(
                'domain_id'             => $this->domain_id,
                'nonexistend_user'      => REJECT,
                'mail_forward_addr'     => "",
                'reject_incoming'       => "1",
                'forward_to_address'    => "0",
                'webmail'               => "roundcube",
                'spamfilter'            => "0",
                'server_id'             => $this->post["mail_servers"]
            ));
            $mail_save_status = $mail->mailSettings();
            if(!$mail_save_status) {
                foreach ($mail->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->saveMailSettings",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
        return $this;
    }

    public function result(): bool
    {
        if(!empty($this->error_messages)) {
            return false;
        }
        else {
            return true;
        }
    }

    public function createCommand(): bool
    {
        if(!empty($this->error_messages)) {
            return false;
        }
        try {
            $vhostmng = new Vhost();
            $mailmng = new Mail();
            $usermng = new User();
            $dirmng = new Directory();

            $domain_name = $this->post["name"];
            $domain_data = Domains::findFirst("name = '$domain_name'");

            $usermng->setServerId($this->post["web_servers"]);
            $usermng->setServerId($this->post["web_servers"]);
            $vhostmng->setServerId($this->post["web_servers"]);
            $mailmng->setServerId($this->post["mail_servers"]);
            $dirmng->setServerId($this->post["web_servers"]);

            //@Note: Only for vhostmng
            $server_os = PANEL_OS_TYPE;
            if($this->system_type == CLOUD) {
                $server = Servers::findFirstById($this->post["web_servers"]);
                if(!$server) {
                    $this->error_messages["content"] = __("Server not found");
                    return false;
                }
                if(strpos(strtolower($server->os), WINDOWS) !== false) {
                    $server_os = WINDOWS;
                }
                else {
                    $server_os = LINUX;
                }
            }

            if($this->post['php'] == ON)
                $php = "on";
            else
                $php = "off";

            if($this->post['cgi'] == ON)
                $cgi = "on";
            else
                $cgi = "off";

            if($this->post['perl'] == ON)
                $perl = "on";
            else
                $perl = "off";

            if($this->post['python'] == ON)
                $python = "on";
            else
                $python = "off";

            if($this->post['email_box_size_limit'] != UNLIMITED)
                $mail_size_limit = $this->post['email_box_size_limit']*1024*1024;
            else
                $mail_size_limit = UNLIMITED;


            // Tüm sistemler için ortak parametreler
            $data = array(
                'domain' => $this->post['name'], // Domain name
                'prefix' => $domain_data->prefix, // Prefix
                'user' => $this->post['ftpname'], // Ftp Username
                'passwd' => $this->post['password'], // Ftp Password
                'ipaddr' => $domain_data->getIpAddress()->ip_address, // DB üzerinde id iler ile işlem yapıldığından bu değeri almak için domain fonksiyonunda ayrıca bir işlem daha yapılmakta
                'php' => $php,
                'cgi' => $cgi,
                'perl' => $perl,
                'python' => $python,
                'ssl' => "off", // Ssl
            );

            // Web Statistics
            if($this->post['web_statistics'] == ON) {
                $data['stats'] = STATISTICS[$this->post['web_statistics_type']];
            }

            // Sadece Windows için olan özel parametreler
            if($server_os == WINDOWS) {
                if($this->post['dedicated_pool'] == ON)
                    $data['appool'] = "on";
                else
                    $data['appool'] = "off";

                $data['limit'] = $this->post['max_cpu'];

                if($this->post['asp'] == ON)
                    $data['asp'] = "on";
                else
                    $data['asp'] = "off";

                if($this->post['aspnet'] == ON)
                    $data['aspnet'] = "on";
                else
                    $data['aspnet'] = "off";

                if($this->post['aspnet_core'] == ON)
                    $data['asp-net-core'] = "on";
                else
                    $data['asp-net-core'] = "off";

                $data['aspnet-ver'] = NET_VERSIONS[$this->post['aspnet_version']];
                $data['pool-mode'] = $this->post['application_pool_mode'] ? POOL_MODE[$this->post['application_pool_mode']] : POOL_MODE[INTEGRATED];
                $data['php-ver'] = PHP_VERSIONS[$this->post['php_version']];
            }

            $user_params = [
                "user" => $this->post["ftpname"],
                "passwd" => $this->post["password"],
                "prefix" => $data["prefix"],
                "domain" => $this->post["name"],
            ];

            $dir_params = [
                "user" => $this->post["ftpname"],
                "passwd" => $this->post["password"],
                "prefix" => $data["prefix"],
                "domain" => $this->post["name"],
            ];

            $mail_params = [
                "domain" => $this->post['name'],
                "passwd" => $this->post['password'],
            ];

            if($mail_size_limit != UNLIMITED) {
                $mail_params["quota"] = $mail_size_limit;
            }

            $resp_user = $usermng->create($user_params);
            if(strpos($resp_user, "Success") !== false || empty($resp_user)) {
                $resp_dir = $dirmng->create($dir_params);
                if(strpos($resp_dir, "Success") !== false || empty($resp_dir)) {
                    $resp_vhost = $vhostmng->create($data);
                    if(strpos($resp_vhost, "Success") !== false || empty($resp_vhost)) {
                        $resp_mail = $mailmng->createDomain($mail_params);
                        if(strpos($resp_mail, "Success") !== false || empty($resp_mail)) {
                            if($this->post['mail_service'] != ON) {
                                $suspend_params = array(
                                    'domain' => $this->post['name']
                                );
                                $mailmng->suspendDomain($suspend_params);
                            }
                            return true;
                        }
                        else {
                            $this->error_messages["resp_mail"] = $resp_mail;
                        }
                    }
                    else {
                        $this->error_messages["resp_vhost"] = $resp_vhost;
                    }
                }
                else {
                    $this->error_messages["resp_dir"] = $resp_dir;
                }
            }
            else {
                $this->error_messages["resp_user"] = $resp_user;
            }
            return false;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->createCommand",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    private function getRemainingDomainRight($server_id): int
    {
        try {
            $servers_system = new Admin();
            $servers_system->setServerId($server_id);
            $server_checked_licence = $servers_system->licenceCheck();
            $decoded_result = json_decode($server_checked_licence,true);
            if(json_last_error() != JSON_ERROR_NONE) {
                return 0;
            }

            if($decoded_result["status"] < 0) {
                return 0;
            }

            if($decoded_result["domains"] == UNLIMITED)  {
                return 99999999;
            }

            if($decoded_result["status"] == LICENCE_EXPIRED) {
                return 0;
            }

            if($decoded_result["status"] == FREE_LICENCE) {
                return 10;
            }

            return (int)$decoded_result["domains"];
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Create->getRemainingDomainRight",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function rollback()
    {

    }
}