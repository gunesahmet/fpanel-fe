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
 */namespace FPanel\Api\Domain;

use FPanel\Components\Components;
use FPanel\Domain\Webhosting;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\ClientIpAddress;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\IpAddress;
use FPanel\Models\Serviceplans;
use FPanel\Models\Users;
use FPanel\System\SysCore;
use Phalcon\Mvc\User\Component;

/**
 * Class Domain
 * @package FPanel\Api\Domain
 */
class Domain extends Component
{
    private $error_messages = [];
    
    public function domainCreate($params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
            "ip_address",
            "template_name",
            "ftp_username",
            "ftp_password",
            "random_server",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        try {
            $sys_core = new SysCore();
            $system_data = $sys_core->getSystemData();
            $template_name = $params['template_name'];
            $serviceplans = Serviceplans::findFirst("name = '$template_name' AND client_id = '$client_id'");
            if(!$serviceplans) {
                return "Serviceplan not found";
            }

            $template_data = $serviceplans->toArray();

            $client = Users::findFirstById($client_id);
            if($client) {
                $client_type = $client->type;
            }
            else {
                return $this->error_messages["client"] = "Domain parent not found";
            }

            if(!empty($params["ip_address"])) {
                $system_ip_address = IpAddress::findFirst("ip_address = '$params[ip_address]'");
                if(($client_type == ROOT_USER) || ($client_type == ADMIN_USER)) {
                    if($system_ip_address)
                        $ip_addr_id = $system_ip_address->id;
                    else
                        return "Ip address not found";

                }
                else {
                    if($system_ip_address) {
                        $ip_addr_id = $system_ip_address->id;
                        $client_ips = ClientIpAddress::findFirst("client_id = '$client_id' AND ipaddr_id = '$ip_addr_id'");
                        if(!$client_ips) {
                            return "Client ip not found";
                        }
                    }
                    else {
                        return "Ip not found on system";
                    }
                }
            }
            else {
                $system_ip_address = IpAddress::findFirst("is_default = '1'");
                $ip_addr_id = $system_ip_address->id;
            }

            $post_data = array(
                // Limit
                'client_limit'    => '0',
                'domain_limit'    => '0',
                'template_limit' => '0',
                'disk_limit'  => ($template_data['disk_limit'] != UNLIMITED ? $template_data['disk_limit']/1024/1024 : UNLIMITED),
                'traffic_limit'  => ($template_data['traffic_limit'] != UNLIMITED ? $template_data['traffic_limit']/1024/1024 : UNLIMITED),
                'email_box_size_limit'  => ($template_data['email_box_size_limit'] != UNLIMITED ? $template_data['email_box_size_limit']/1024/1024 : UNLIMITED),
                'database_size_limit'  => ($template_data['database_size_limit'] != UNLIMITED ? $template_data['database_size_limit']/1024/1024 : UNLIMITED),
                'subdomain_limit' => $template_data['subdomain_limit'],
                'domain_alias_limit'  => $template_data['domain_alias_limit'],
                'email_box_limit' => $template_data['email_box_limit'],
                'email_forward_limit' => $template_data['email_forward_limit'],
                'email_groups_limit'  => $template_data['email_groups_limit'],
                'email_autoresponder_limit'   => $template_data['email_autoresponder_limit'],
                'email_lists_limit'    => $template_data['email_lists_limit'],
                'database_limit'  => $template_data['database_limit'],
                'expire_time_limit' => $template_data['expire_time'],
                'overuse' => $template_data['overuse'] == "1" ? ON : OFF,

                // Permission
                'man_client' => OFF,
                'man_domain' => OFF,
                'man_template'   => OFF,
                'access_panel'  => $template_data['access_panel'] == "1" ? ON : OFF,
                'man_hosting_settings'   => $template_data['man_hosting_settings'] == "1" ? ON : OFF,
                'man_subdomain'  => $template_data['man_subdomain'] == "1" ? ON : OFF,
                'man_database'   => $template_data['man_database'] == "1" ? ON : OFF,
                'man_domain_alias'   => $template_data['man_domain_alias'] == "1" ? ON : OFF,
                'man_email'  => $template_data['man_email'] == "1" ? ON : OFF,
                'man_email_alias'    => $template_data['man_email_alias'] == "1" ? ON : OFF,
                'man_mailing_lists'  => $template_data['man_mailing_lists'] == "1" ? ON : OFF,
                'man_spam_filters'   => $template_data['man_spam_filters'] == "1" ? ON : OFF,
                'man_task'   => $template_data['man_task'] == "1" ? ON : OFF,
                'man_dns'    => $template_data['man_dns'] == "1" ? ON : OFF,
                'man_ssl'    => $template_data['man_ssl'] == "1" ? ON : OFF,
                'man_backup'   => $template_data['man_backup'] == "1" ? ON : OFF,
                'man_protect_dir'    => $template_data['man_protect_dir'] == "1" ? ON : OFF,

                // Domain Settings
                'php'  => $template_data['php'] == "1" ? ON : OFF,
                'php_version'  => $template_data['php_version'],
                'cgi'  => $template_data['cgi'] == "1" ? ON : OFF,
                'perl'  => $template_data['perl'] == "1" ? ON : OFF,
                'python'  => $template_data['python'] == "1" ? ON : OFF,
                'fast_cgi'  => PASSIVE,
                'web_statistics'  => $template_data['statistic_support'] == "1" ? ON : OFF,
                'web_statistics_type'  => $template_data['statistic_type'],
                'asp'  => $template_data['asp'] == "1" ? ON : OFF,
                'aspnet_core'  => $template_data['aspnet_core'] == "1" ? ON : OFF,
                'aspnet'  => $template_data['aspnet'] == "1" ? ON : OFF,
                'aspnet_version'  => $template_data['aspnet_version'],
                'dedicated_pool'  => $template_data['dedicated_pool'] == "1" ? ON : OFF,
                'max_cpu'  => $template_data['max_cpu'],
                'application_pool_mode'  => $template_data['application_pool_mode'],
                'ssl'  => PASSIVE,

                // Service
                'dns_service'   => ON,
                'web_service'   => ON,
                'mail_service'   => ON,

                // Userdata
                'domain_name'   => $params['domain_name'],
                'name'          => $params['domain_name'],
                'username'      => $params['domain_name'],
                'fullname'      => $params['domain_name'],
                'type'          => DOMAIN_USER,
                'email'         => "info@".$params['domain_name'],
                'ftpname'       => $params['ftp_username'],
                'password'      => $params['ftp_password'],
                'company'       => null,
                'telephone'     => null,
                'address'       => null,
                'city'          => null,
                'zipcode'       => null,
                'country'       => null,
                // Selecteds
                'selected_user' => $client_id,
                'ipaddr_id'     => $ip_addr_id,
                'web_server_ip'     => !empty($params['web_server_ip']) ? $params['web_server_ip']: null,
                'dns_server_ip'     => !empty($params['dns_server_ip']) ? $params['dns_server_ip']: null,
                'mail_server_ip'     => !empty($params['mail_server_ip']) ? $params['dns_server_ip']: null,
            );

            if($system_data["system_type"] == CLOUD) {
                $post_data["manual_select_server"] = $params["random_server"] ? OFF : ON;
            }

            $domain_components = new Components();

            $domain_status = $domain_components->userData(array(
                'user_id' => $client_id,
                'user_type' => $client_type,
            ))
                ->options(array())
                ->post($post_data,false,true)
                ->create(DOMAIN);

            if(!$domain_status) {
                return ["errors" => $domain_components->getMessages()];
            }
            else {
                return "Success";
            }
        }
        catch (\Exception $e) {
            return ["exception" => $e->getMessage()];
        }
    }

    public function domainDelete($params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        $domain_name = $params["domain_name"];
        $error_messages = array();
        if(!$this->env->checkDomainOwner($domain_name, $client_id)) {
            return $this->error_messages['domain_name'] = "You dont have access for this domain or domain doesn't exist";
        }
        $client = Users::findFirstById($client_id);
        if($client) {
            $client_type = $client->type;
        }
        else {
            return $this->error_messages['username'] = "This user doesn't exist on system.";
        }

        $domain = Domains::findFirst("name = '$domain_name'");
        if(!$domain) {
            return $this->error_messages['domain_name'] = "Domain not found";
        }
        $domain_id = $domain->id;

        $domain = new Components();
        $status = $domain->userData(array(
            'user_id' => $client_id,
            'user_type' => $client_type,
        ))
            ->delete(DOMAIN, $domain_id);

        if(!$status) {
            foreach ($domain->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }

    public function domainList($client_id)
    {
        $qbuilder = new QueryBuilder();
        $client = Users::findFirstById($client_id);
        if($client) {
            $client_type = $client->type;
        }
        else {
            return $this->error_messages['username'] = "This user doesn't exist on system.";
        }

        $qbuilder->select([
            "domains.created_date",
            "domains.expire_date",
            "domains.`name`",
            "users.username as parent_name",
            "ip_address.ip_address",
            "domains.`status`",
            "domains.status_msg",
            "domain_statistics.disk",
            "domain_statistics.traffic",
        ])
            ->from("domains")
            ->innerJoin("users","domains.client_id = users.id")
            ->innerJoin("ip_address","domains.ipaddr_id = ip_address.id")
            ->innerJoin("domain_statistics","domains.id = domain_statistics.domain_id");
        if(($client_type != ROOT_USER) && ($client_type != ADMIN_USER)) {
            $qbuilder->where([
                    'conditions' => "users.id = ?1",
                    'bind' => [1 => $client_id]
                ])
                ->orWhere([
                    'conditions' => "users.parent = ?1",
                    'bind' => [1 => $client_id]
                ]);
        }
        $domain_list = $qbuilder->find();


        if(!$domain_list) {
            $domain_list = array(
                'status' => "0"
            );
        }
        else {
            $domain_list['status'] = "1";
        }
        return $domain_list;
    }

    public function webHostingSettings($params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
            "ftp_username",
            "ftp_password",
            "php",
            "cgi",
            "perl",
            "python",
            "statistics",
            "statistics_type",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        $domain_name = $params['domain_name'];
        if(!$this->env->checkDomainOwner($domain_name, $client_id)) {
            return $this->error_messages['domain_name'] = "You dont have access for this domain or domain is not exists";
        }
        $this->db->begin();
        try {
            $domains = Domains::findFirst("name = '$domain_name'");
            if(!$domains) {
                return $this->error_messages['domain_name'] = "Domain not found";
            }
            $domain_id = $domains->id;

            $users = Users::findFirstById($client_id);

            $post_data = array(
                // Ftp Settings
                'ftp_username'    => $params['ftp_username'],
                'password'    => $params['ftp_password'],
            );

            if($users->type != DOMAIN_USER) {
                $ip_address = IpAddress::findFirst("ip_address = '$params[ip_address]'");
                if(!$ip_address) {
                    return $this->error_messages['domain_name'] = "Incorrect ip.";
                }
                $ip_address_id = $ip_address->id;

                // Eğer istekte bulunan kişi client ise client ip adreslerinden bu ip kendisine tanımlanmış mı kontrol ediliyor.
                if($users->type > ADMIN_USER) {
                    $client_ip_addresses = ClientIpAddress::find("client_id = '$client_id'");
                    $ip_status = false;
                    foreach($client_ip_addresses->toArray() as $client_ip) {
                        if($client_ip == $ip_address_id)
                            $ip_status = true;
                    }

                    if(!$ip_status)
                        return $this->error_messages['ip_address'] = "You can not use this ip address";
                }

                $post_data['ipaddr_id'] = $ip_address_id;
                $post_data['php'] = $params['php'] ? ON : OFF;
                $post_data['php_version'] = !empty($params['php_version']) ? $this->env->getPhpVersion($params['php_version'], true) : PHP72;
                $post_data['asp'] = $params['asp'] ? ON : OFF;
                $post_data['aspnet_core'] = !empty($params['aspnet_core']) ? ON : OFF;
                $post_data['aspnet'] = !empty($params['asp_net']) ? ON : OFF;
                $post_data['aspnet_version'] = !empty($params['asp_net_version']) ? $this->env->getNetVersion($params['asp_net_version'], true) : 0;
                $post_data['cgi'] = $params['cgi'] ? ON : OFF;
                $post_data['perl'] = $params['perl'] ? ON : OFF;
                $post_data['python'] = $params['python'] ? ON : OFF;
                $post_data['statistic_support'] = !empty($params['statistics']) ? ON : OFF;
                $post_data['statistic_type'] = !empty($params['statistics_type']) ? $this->env->getStatisticsType($params['statistics_type'], true) : NO_STATS;
                $post_data['dedicated_pool'] = !empty($params['dedicated_pool']) ? ON : OFF;
                $post_data['max_cpu'] = !empty($params['cpu_limit']) ? $params['cpu_limit'] : 0;
                $post_data['application_pool_mode'] = !empty($params['application_pool_mode']) && !empty($params['dedicated_pool']) ? $this->env->getApplicationMode($params['application_pool_mode'], true) : 1;
            }

            $ftp_id = $domains->ftp_user_id;

            $post_data['ftp_id'] = $ftp_id;
            $post_data['domain_id'] = $domain_id;

            $webhosting_settings = new Webhosting();
            $webhosting_settings->setPosts($post_data);
            $webhosting_settings->ip_list = $this->getdomainip($domain_id);

            $validation = $webhosting_settings->validation(true);

            if(!$validation) {
                foreach ($webhosting_settings->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->db->rollback();
            }
            else {
                $ftp_user_update = $webhosting_settings->updateFTPUser();
                if (!$ftp_user_update) {
                    foreach ($webhosting_settings->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    $this->db->rollback();
                }
                else {
                    $ip_addr_update = true;
                    if(isset($this->domain_id)) {
                        $ip_addr_update = $webhosting_settings->updateDomainIPAddr();
                    }
                    if (!$ip_addr_update) {
                        foreach ($webhosting_settings->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        $this->db->rollback();
                    }
                    else {
                        $domain_settings_update = $webhosting_settings->updateDomainSettings(true);
                        if ($domain_settings_update) {
                            $this->db->commit();
                            return "Success";
                        }
                        else {
                            foreach ($webhosting_settings->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            $this->db->rollback();
                        }
                    }
                }
            }
        }
        catch (\Exception $e) {
            $this->db->rollback();
            $this->error_messages["exception"] = $e->getMessage();
        }

        return $this->error_messages;

    }

    public function domainUpdate($params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
            "template_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        $domain_name = $params["domain_name"];

        try {
            if(!$this->env->checkDomainOwner($domain_name, $client_id)) {
                return $this->error_messages['domain_name'] = "You dont have access for this domain or domain is not exists";
            }


            $domains = Domains::findFirst("name = '$domain_name'");
            if(!$domains) {
                return $this->error_messages['domain_name'] = "Domain not found.";
            }
            $domain_id = $domains->id;
            $ftpuser_id = $domains->ftp_user_id;

            $ftp_users = FtpUsers::findFirstById($ftpuser_id);

            $template_name = $params['template_name'];
            $serviceplans = Serviceplans::findFirst("name = '$template_name' AND client_id = '$client_id'");
            if(!$serviceplans) {
                return "Serviceplan not found";
            }

            $template_data = $serviceplans->toArray();

            $client = Users::findFirstById($client_id);
            if($client) {
                $client_type = $client->type;
            }
            else {
                return "Domain parent not found";
            }

            if(!empty($params["ip_address"])) {
                $system_ip_address = IpAddress::findFirst("ip_address = '$params[ip_address]'");
                if(($client_type == ROOT_USER) || ($client_type == ADMIN_USER)) {
                    if($system_ip_address)
                        $ip_addr_id = $system_ip_address->id;
                    else
                        return "Ip address not found";

                }
                else {
                    if($system_ip_address) {
                        $ip_addr_id = $system_ip_address->id;
                        $client_ips = ClientIpAddress::findFirst("client_id = '$client_id' AND ipaddr_id = '$ip_addr_id'");
                        if(!$client_ips) {
                            return "Client ip not found";
                        }
                    }
                    else {
                        return "Ip not found on system";
                    }
                }
            }
            else {
                $system_ip_address = IpAddress::findFirst("is_default = '1'");
                $ip_addr_id = $system_ip_address->id;
            }

            $post_data = array(
                // Limit
                'client_limit'    => '0',
                'domain_limit'    => '0',
                'template_limit' => '0',
                'disk_limit'  => ($template_data['disk_limit'] != UNLIMITED ? $template_data['disk_limit']/1024/1024 : UNLIMITED),
                'traffic_limit'  => ($template_data['traffic_limit'] != UNLIMITED ? $template_data['traffic_limit']/1024/1024 : UNLIMITED),
                'email_box_size_limit'  => ($template_data['email_box_size_limit'] != UNLIMITED ? $template_data['email_box_size_limit']/1024/1024 : UNLIMITED),
                'database_size_limit'  => ($template_data['database_size_limit'] != UNLIMITED ? $template_data['database_size_limit']/1024/1024 : UNLIMITED),
                'subdomain_limit' => $template_data['subdomain_limit'],
                'domain_alias_limit'  => $template_data['domain_alias_limit'],
                'email_box_limit' => $template_data['email_box_limit'],
                'email_forward_limit' => $template_data['email_forward_limit'],
                'email_groups_limit'  => $template_data['email_groups_limit'],
                'email_autoresponder_limit'   => $template_data['email_autoresponder_limit'],
                'email_lists_limit'    => $template_data['email_lists_limit'],
                'database_limit'  => $template_data['database_limit'],
                'expire_time_limit' => $template_data['expire_time'],
                'overuse' => $template_data['overuse'] == "1" ? ON : OFF,

                // Permission
                'man_client' => OFF,
                'man_domain' => OFF,
                'man_template'   => OFF,
                'access_panel'  => $template_data['access_panel'] == "1" ? ON : OFF,
                'man_hosting_settings'   => $template_data['man_hosting_settings'] == "1" ? ON : OFF,
                'man_subdomain'  => $template_data['man_subdomain'] == "1" ? ON : OFF,
                'man_database'   => $template_data['man_database'] == "1" ? ON : OFF,
                'man_domain_alias'   => $template_data['man_domain_alias'] == "1" ? ON : OFF,
                'man_email'  => $template_data['man_email'] == "1" ? ON : OFF,
                'man_email_alias'    => $template_data['man_email_alias'] == "1" ? ON : OFF,
                'man_mailing_lists'  => $template_data['man_mailing_lists'] == "1" ? ON : OFF,
                'man_spam_filters'   => $template_data['man_spam_filters'] == "1" ? ON : OFF,
                'man_task'   => $template_data['man_task'] == "1" ? ON : OFF,
                'man_dns'    => $template_data['man_dns'] == "1" ? ON : OFF,
                'man_ssl'    => $template_data['man_ssl'] == "1" ? ON : OFF,
                'man_backup'   => $template_data['man_backup'] == "1" ? ON : OFF,
                'man_protect_dir'    => $template_data['man_protect_dir'] == "1" ? ON : OFF,

                // Domain Settings
                'php'  => $template_data['php'] == "1" ? ON : OFF,
                'php_version'  => $template_data['php_version'],
                'cgi'  => $template_data['cgi'] == "1" ? ON : OFF,
                'perl'  => $template_data['perl'] == "1" ? ON : OFF,
                'python'  => $template_data['python'] == "1" ? ON : OFF,
                'fast_cgi'  => PASSIVE,
                'web_statistics'  => $template_data['statistic_support'] == "1" ? ON : OFF,
                'web_statistics_type'  => $template_data['statistic_type'],
                'asp'  => $template_data['asp'] == "1" ? ON : OFF,
                'aspnet_core'  => $template_data['aspnet_core'] == "1" ? ON : OFF,
                'aspnet'  => $template_data['aspnet'] == "1" ? ON : OFF,
                'aspnet_version'  => $template_data['aspnet_version'],
                'dedicated_pool'  => $template_data['dedicated_pool'] == "1" ? ON : OFF,
                'max_cpu'  => $template_data['max_cpu'],
                'application_pool_mode'  => $template_data['application_pool_mode'],
                'ssl'  => PASSIVE,


                // Service
                'dns_service'   => ON,
                'web_service'   => ON,
                'mail_service'   => ON,

                // Userdata
                'domain_name'   => $params['domain_name'],
                'name'   => $params['domain_name'],
                'username'   => $params['domain_name'],
                'fullname'   => $params['domain_name'],
                'type'  => DOMAIN_USER,
                'email'   => "info@".$params['domain_name'],
                'ftpname'  => $ftp_users->username,
                'company' => null,
                'telephone' => null,
                'address' => null,
                'city' => null,
                'zipcode' => null,
                'country' => null,
                // Selecteds
                'selected_user'  => $client_id,
                'ipaddr_id'    => $ip_addr_id,
            );

            if(!empty($params['ftp_password'])) {
                $post_data["password"] = $params['ftp_password'];
            }

            if(!empty($params['new_ftp_username'])) {
                $post_data["new_ftpname"] = $params['new_ftp_username'];
            }

            $domain_components = new Components();

            $domain_status = $domain_components->userData(array(
                'user_id' => $client_id,
                'user_type' => $client_type,
            ))
                ->options(array())
                ->post($post_data,false,true)
                ->update(DOMAIN, $domain_id);

            if(!$domain_status) {
                return $domain_components->getMessages();
            }
            else {
                return "Success";
            }
        }
        catch (\Exception $e) {
            return ["exception" => $e->getMessage()];
        }
    }

    /**
     * @param $domain_id
     * @return array|bool
     */
    private function getdomainip($domain_id)
    {
        $qbuilder = new QueryBuilder();
        return $qbuilder->select([
            "ip_address.ip_address",
        ])
            ->from('ip_address')
            ->innerJoin('domains','domains.ipaddr_id = ip_address.id')
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();
    }
}