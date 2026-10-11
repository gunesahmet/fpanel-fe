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
namespace FPanel\Domain;

use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\Models\Certificates;
use FPanel\Models\ClientLimitsPermissions;
use FPanel\Models\DomainAlias;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\IpAddress;
use FPanel\Models\Servers;
use FPanel\Models\Subdomains;
use FPanel\Process\Ssl;
use FPanel\Process\Subvhost;
use FPanel\Process\User;
use FPanel\Process\Vhost;

/**
 * Class Webhosting
 * @package FPanel\DomainSettings
 */
class Webhosting extends Library
{

    public $ip_list;
    public $ssl_list;

    public $change_username = false;
    private $change_ipaddress = false;

    private $old_user_name;
    private $old_ip_address;

    private $overuse;
    private $disk_limit;
    public $nested = false;

    public function updateFTPUser(): bool
    {
        try {
            $ftp_id = $this->getPost("ftp_id");
            $ftp_password = $this->crypto->bencode($this->getPost("password"));

            $ftp_user_passwd_update = FtpUsers::findFirst("id = '$ftp_id'");
            $ftp_user_old_data = $ftp_user_passwd_update->toArray();

            if ($this->getPost("ftp_username") != null) {
                $ftp_user_oldname = $ftp_user_old_data['username'];
                $this->old_user_name = $ftp_user_oldname;
                if ($ftp_user_oldname != $this->getPost("ftp_username")) {
                    $this->change_username = true;
                    $ftp_user_passwd_update->username = $this->getPost("ftp_username");

                    if (!$ftp_user_passwd_update->update()) {
                        foreach ($ftp_user_passwd_update->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "Webhosting->updateFTPUser",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                }
            }
            if ($this->getPost("password") != null) {
                $ftp_user_oldpassword = trim($this->crypto->bdecode($ftp_user_old_data['password']));
                if ($ftp_user_oldpassword != $ftp_password) {
                    $ftp_user_passwd_update->password = $ftp_password;

                    if (!$ftp_user_passwd_update->update()) {
                        foreach ($ftp_user_passwd_update->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "Webhosting->updateFTPUser 2",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                }
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Webhosting->updateFTPUser:exception",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function updateDomainIPAddr(): bool
    {
        try {
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            if ($domain->ipaddr_id != $this->getPost("ipaddr_id")) {
                $sys_ip = IpAddress::findFirstById($domain->ipaddr_id);
                $this->old_ip_address = $sys_ip->ip_address;
                $this->change_ipaddress = true;
                $domain->assign(array('ipaddr_id' => $this->getPost("ipaddr_id")));

                if (!$domain->update()) {
                    foreach ($domain->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Webhosting->updateDomainIPAddr",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Webhosting->updateDomainIPAddr:exception",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function updateDomainSettings($nested = false): bool
    {
        try {
            $domain_id = $this->getPost("domain_id");
            $domain_limits = ClientLimitsPermissions::findFirst("domain_id = '$domain_id'");
            if ($domain_limits) {

                $domain_limit_data = $domain_limits->toArray();
                $this->overuse = $domain_limit_data['overuse'];
                $this->disk_limit = $domain_limit_data['disk_limit'];

                $ssl_support = PASSIVE;
                if ($this->getPost("ssl") == ON) {
                    $ssl_support = ACTIVE;
                }

                if ($nested) {
                    $assign = array(
                        'ssl' => $ssl_support,
//                    'cgi'                   => $this->getPost("cgi"),
//                    'fast_cgi'              => $this->getPost("fast_cgi"),
                        'python' => $this->getPost("python") == ON ? ACTIVE : PASSIVE,
                        'perl' => $this->getPost("perl") == ON ? ACTIVE : PASSIVE,
                        'asp' => $this->getPost("asp") == ON ? ACTIVE : PASSIVE,
                        'aspnet_core' => $this->getPost("aspnet_core") == ON ? ACTIVE : PASSIVE,
                        'aspnet' => $this->getPost("aspnet") == ON ? ACTIVE : PASSIVE,
                        'php' => $this->getPost("php") == ON ? ACTIVE : PASSIVE,
                        'statistic_support' => $this->getPost("statistic_support") == ON ? ACTIVE : PASSIVE,
                        'dedicated_pool' => $this->getPost("dedicated_pool") == ON ? ACTIVE : PASSIVE,

                        'max_cpu' => $this->getPost("max_cpu", 0),
                        'php_version' => $this->getPost("php_version", PHP56),
                        'aspnet_version' => $this->getPost("aspnet_version", v40),
                        'statistic_type' => $this->getPost("statistic_type", NO_STATS),
                        'application_pool_mode' => $this->getPost("application_pool_mode", INTEGRATED),
                    );
                }
                else {
                    $assign = array(
                        'ssl' => $ssl_support,
                    );
                }
                $domain_limits->assign($assign);

                if ($domain_limits->update()) {

                    $domain_data = Domains::findFirstById($domain_id);
                    if ($this->getPost("ssl") == ON) {
                        $domain_data->ssl_cert_id = $this->getPost("ssl_list");
                        if ($domain_data->update()) {
                            if ($this->vhostmng()) {
                                return true;
                            }
                        }
                        else {
                            foreach ($domain_data->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            $this->log->error([
                                "Module" => "Webhosting->updateDomainSettings",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                            return false;
                        }
                    }
                    else {
                        if ($this->vhostmng()) {
                            return true;
                        }
                    }

                }
                else {
                    foreach ($domain_limits->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Webhosting->updateDomainSettings ClientLimitsPermissions",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Webhosting->updateDomainSettings:exception",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function validation($update = false): bool
    {
        $required = array();

        $this->validation->FtpUser($this->getPost("ftp_username"), "ftp_username", __('Please enter valid username.'));
        $this->validation->CheckBlacklist($this->getPost("ftp_username"), 'ftp_username', 'ftp_user', false, __('This username can\'t use.'));

        if ($this->getPost("password") != null) {
            // FTP User & Password
            $this->validation->Password($this->getPost("password"), "password", null, $update);
        }


        if ($this->nested) {
            $ip_list = array();
            foreach ($this->ip_list as $ip) {
                $ip_list[$ip['id']] = $ip['ip_address'];
            }

            $this->validation->InclusionIn($this->getPost("ipaddr_id"), 'ipaddress_list', $ip_list, __('Please select an ip address.'), true);
            if (PANEL_OS_TYPE == WINDOWS) {
                // Max CPU Usage
                if ($this->getPost("dedicated_pool") == ON) {
                    $required[] = array(
                        'field' => 'max_cpu_usage',
                        'item' => $this->getPost("max_cpu"),
                        'message' => __('Max CPU Usage is Required')
                    );

                    $this->validation->Required($required);
                    if ($this->getPost("max_cpu") != null) {
                        $this->validation->Between($this->getPost("max_cpu"), "max_cpu_usage", "1", "99", __('Please enter 1 to 99.'));
                    }
                }

                if ($this->getPost("ssl") == ON) {
                    $required[] = array(
                        'field' => 'ssl_list',
                        'item' => $this->getPost("ssl_list"),
                        'message' => __('Please select a ssl certificate')
                    );
                    $this->validation->Required($required);
                    $this->validation->InclusionIn($this->getPost("ssl_list"), 'ssl_list', $this->ssl_list, __('Please select a ssl certificate'), true);
                }

                // PHP & PHP Version
                if ($this->getPost("php") == ON) {
                    $required[] = array(
                        'field' => 'php_version',
                        'item' => $this->getPost("php_version"),
                        'message' => __('PHP Version is Required')
                    );
                    $this->validation->Required($required);
                    if ($this->getPost("php_version") != null) {
                        $this->validation->InclusionIn($this->getPost("php_version"), 'php_version', array(PHP56, PHP72, PHP74));
                    }
                }

                // ASP.NET & ASP.NET Version
                if ($this->getPost("aspnet") == ON) {
                    $required[] = array(
                        'field' => 'aspnet_version',
                        'item' => $this->getPost("aspnet_version"),
                        'message' => __('ASP.NET Version is required.')
                    );
                    $required[] = array(
                        'field' => 'application_pool_mode',
                        'item' => $this->getPost("application_pool_mode"),
                        'message' => __('Application Mode is Required')
                    );

                    $this->validation->Required($required);
                    if ($this->getPost("aspnet_version") != null) {
                        $this->validation->InclusionIn($this->getPost("aspnet_version"), 'aspnet_version', array(v40, v20));
                    }

                    if ($this->getPost("application_pool_mode") != null) {
                        $this->validation->InclusionIn($this->getPost("application_pool_mode"), 'application_pool_mode',
                            [INTEGRATED, CLASSIC], __('Please select an application mode.'), true);
                    }
                }
            }

            // Web Statistic Type
            if ($this->getPost("statistic_support") == ON) {
                $required[] = array(
                    'field' => 'web_statistics_type',
                    'item' => $this->getPost("statistic_type"),
                    'message' => __('Statistic Type is Required')
                );
                $this->validation->Required($required);
                if ($this->getPost("statistic_type") != null) {
                    $this->validation->InclusionIn($this->getPost("statistic_type"), 'web_statistics_type', array(AWSTATS, WEBALIZER));
                }
            }
        }

        if ($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;

    }

    private function vhostmng(): bool
    {
        $vhostmng = new Vhost();
        $domain_id = $this->getPost("domain_id");

        $domain_settings = $this->getDomainSettings($domain_id);
        $vhostmng->setServerId($domain_settings["web_server_id"]);
        $server = Servers::findFirstById($domain_settings["web_server_id"]);
        $sslmng = new Ssl();
        $sslmng->setServerId($domain_settings['web_server_id']);
        if (!empty($domain_settings)) {

            $general_params = array(
                'quota' => $domain_settings['disk_limit'],
                'prefix' => $domain_settings['prefix'],
                'bw-quota' => $domain_settings['traffic_limit'],
                'user' => $domain_settings['ftpname'],
                'domain' => $domain_settings['name'],
                'ipaddr' => $domain_settings['ip_address'],
                'quota-over' => "off",
                'php' => "off",
                'asp' => "off",
                'aspnet' => "off",
                'asp-net-core' => "off",
                'cgi' => "off",
                'perl' => "off",
                'python' => "off",
                'ssl' => "off",
                'appool' => "",
                'stats' => "",
            );
            $ssl_id = $this->getPost("ssl_list");
            $ssl = Certificates::findFirst("domain_id = '$domain_id' AND id = '$ssl_id'");
            if ($domain_settings['ssl'] == ACTIVE) {
                $general_params['ssl'] = "on";
                $general_params['ssl-name'] = $ssl->name;
                $this->writeSSL($this->getPost("ssl_list"), $domain_settings, $server->os);
            }
            else {
                $params = array(
                    'domain' => $domain_settings['name'],
                );
                $sslmng->removeWeb($params);
            }
            if ($domain_settings['php'] == ACTIVE) {
                $general_params['php'] = "on";
                $general_params['php-ver'] = $domain_settings['php_version'];
            }
            if ($domain_settings['statistic_support'] == ACTIVE) {
                if ($this->getPost("statistic_support") == ACTIVE) {
                    $general_params['stats'] = $this->getPost("statistic_type");
                }
            }
            if ($domain_settings['aspnet'] == ACTIVE) {
                $general_params['aspnet'] = "on";
                $general_params['aspnet-ver'] = $domain_settings['aspnet_version'];
            }
            if ($domain_settings['aspnet_core'] == ACTIVE) {
                $general_params['asp-net-core'] = "on";
            }
            if ($domain_settings['asp'] == ACTIVE) {
                $general_params['asp'] = "on";
            }
            if ($domain_settings['cgi'] == ACTIVE) {
                $general_params['cgi'] = "on";
            }
            if ($domain_settings['perl'] == ACTIVE) {
                $general_params['perl'] = "on";
            }
            if ($domain_settings['python'] == ACTIVE) {
                $general_params['python'] = "on";
            }
            if ($domain_settings['overuse'] == ACTIVE) {
                $general_params['quota-over'] = "on";
            }

            if (strpos(strtolower($server->os), WINDOWS) !== false) {
                if ($domain_settings['dedicated_pool'] == ACTIVE) {
                    $general_params['appool'] = "on";
                    $general_params['limit'] = $domain_settings['max_cpu'];
                }
                $general_params['pool-mode'] = $domain_settings['application_pool_mode'];
            }

            $other_params = $this->changeipaddress();
            $params = array_merge($general_params, $other_params);

            $result_user = "";
            $user_parameters = array();
            $ftp = FtpUsers::findFirstById($this->getPost("ftp_id"));
            $user_parameters['passwd'] = trim($this->crypto->bdecode($ftp->password));

            $usermng = new User();
            $usermng->setServerId($domain_settings["web_server_id"]);

            if ($this->change_username == true) {
                if ($domain_settings['dedicated_pool'] == ACTIVE) {
                    $user_parameters['appool'] = "on";
                }
                else {
                    $user_parameters['appool'] = "off";
                }
                $user_parameters['new-user'] = $this->getPost("ftp_username");
                $user_parameters['disk-quota'] = $this->disk_limit;
                $user_parameters['quota-over'] = $this->overuse;
                $usermng->update($user_parameters);
            }

            if (!empty($user_parameters)) {
                $user_parameters['user'] = $this->old_user_name;
                $user_parameters['domain'] = $domain_settings['name'];
                $user_parameters['new-passwd'] = trim($this->crypto->bdecode($ftp->password));
                $usermng->update($user_parameters);
            }

            if (strpos(strtolower($server->os), WINDOWS) !== false) {
                $params['user'] = $ftp->username;
                $params['passwd'] = trim($this->crypto->bdecode($ftp->password));
            }

            $result_vhost = $vhostmng->update($params);

            $result = $result_user . " " . $result_vhost;

            $result = str_replace('Success', '', trim($result));

            if (trim($result) != null) {
                if (strpos($result, "Err") !== false) {
                    $result = str_replace('Success', ' ', $result);
                    $this->error_messages['content'] = $result;
                    return false;
                }
                else {
                    return true;
                }
            }
            else
                return true;
        }
        $this->error_messages[] = __('Domain not found');
        return false;
    }

    private function writeSSL($ssl_id, $domain_settings, $server_os): void
    {
        $certificates = Certificates::findFirstById($ssl_id);
        if ($certificates) {
            $crt = trim($certificates->crt);
            $ca_crt = trim($certificates->ca);
            $private_key = trim($certificates->private_key);
            $ssl = new Ssl();
            $ssl->setServerId($domain_settings["web_server_id"]);
            if (strpos(strtolower($server_os), WINDOWS) !== false) {
                $params = [
                    "domain" => $domain_settings["name"],
                    "ssl-name" => $certificates->name,
                    "common-name" => $certificates->common_name
                ];

                $write_bundle_params = [
                    "domain" => $domain_settings["name"],
                    "ssl-name" => $certificates->name,
                    "bundle" => base64_encode($ca_crt),
                ];

                $write_crt_params = [
                    "domain" => $domain_settings["name"],
                    "ssl-name" => $certificates->name,
                    "crt" => base64_encode($crt),
                ];

                $write_pkey_params = [
                    "domain" => $domain_settings["name"],
                    "ssl-name" => $certificates->name,
                    "private-key" => base64_encode($private_key),
                ];

                $ssl->writeBundle($write_bundle_params);
                $ssl->writeCrt($write_crt_params);
                $ssl->writePkey($write_pkey_params);
                $result_first = $ssl->installSSL($params);
                $result_last = $ssl->installSSL($params);
                if (trim($result_first) != null || trim($result_last) != null) {
                    $this->debug->debug([
                        $result_first,
                        $result_last
                    ], false);
                }
            }
            else {
                $params = [
                    "domain" => $domain_settings["name"],
                    "ssl-name" => $certificates->name,
                    "private-key" => $this->crypto->staticEncode($private_key),
                    "crt" => $this->crypto->staticEncode($crt),
                    "bundle" => $this->crypto->staticEncode($ca_crt),
                    "common-name" => $certificates->common_name
                ];
                $ssl->installSSL($params);
                return;
            }

            return;
        }
    }

    private function getDomainSettings($domain_id)
    {
        $qbuilder = new QueryBuilder();
        return $qbuilder->select([
            "domains.name",
            "domains.prefix",
            "domains.web_server_id",
            "domains.dns_server_id",
            "domains.mail_server_id",
            "ip_address.ip_address",
            "client_limits_permissions.disk_limit",
            "client_limits_permissions.traffic_limit",
            "client_limits_permissions.overuse",
            "client_limits_permissions.php",
            "client_limits_permissions.php_version",
            "client_limits_permissions.cgi",
            "client_limits_permissions.perl",
            "client_limits_permissions.fast_cgi",
            "client_limits_permissions.python",
            "client_limits_permissions.statistic_support",
            "client_limits_permissions.statistic_type",
            "client_limits_permissions.asp",
            "client_limits_permissions.aspnet_core",
            "client_limits_permissions.aspnet",
            "client_limits_permissions.aspnet_version",
            "client_limits_permissions.dedicated_pool",
            "client_limits_permissions.max_cpu",
            "client_limits_permissions.application_pool_mode",
            "client_limits_permissions.ssl",
            "ftp_users.username as ftpname",
        ])
            ->from("domains")
            ->innerJoin("ip_address", "domains.ipaddr_id = ip_address.id")
            ->innerJoin("client_limits_permissions", "domains.id = client_limits_permissions.domain_id")
            ->innerJoin("ftp_users", "domains.ftp_user_id = ftp_users.id")
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();
    }

    private function changeipaddress(): array
    {
        $data = array(
            'sub-list' => "",
            'domain-list' => "",
            'old-ipaddr' => ""
        );

        if ($this->change_ipaddress) {
            $domain_id = $this->getPost("domain_id");
            $domain_aliases = DomainAlias::find("domain_id = '$domain_id'");
            $alias_list = null;
            if ($domain_aliases) {
                $domain_alias_list = $domain_aliases->toArray();
                foreach ($domain_alias_list as $value) {
                    if ($alias_list == null)
                        $alias_list = $value['alias_name'];
                    else
                        $alias_list = $alias_list . ' ' . $value['alias_name'];
                }
            }

            $subdomains = Subdomains::find("domain_id = '$domain_id'");
            $sub_list = null;
            if ($subdomains) {
                foreach ($subdomains as $subdomain) {
                    if ($sub_list == null)
                        $sub_list = $subdomain->subdomain_name;
                    else
                        $sub_list = $sub_list . ' ' . $subdomain->subdomain_name;
                }

            }
            $data['sub-list'] = $sub_list;
            $data['domain-list'] = $alias_list;
            $data['old-ipaddr'] = $this->old_ip_address;
        }

        return $data;
    }

    public function subdomainUpdateCurl($params)
    {
        $subdomain = new Subvhost();
        $subdomain->setServerId($this->getPost("server_id"));
        $subdomain->update($params);
    }

    public function isIpChanged(): bool
    {
        return $this->change_ipaddress;
    }
}