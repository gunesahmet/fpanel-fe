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

use FPanel\Common\Domain\Builder;
use FPanel\Components\Components;
use FPanel\Models\Domains;
use FPanel\Models\IpAddress;
use Phalcon\Mvc\User\Component;

/**
 * Class Users
 * @package FPanel\Common\Server
 */
class Users extends Component
{
    public function restoreUser($user_data, $parent)
    {
        $username = $user_data["user"]["username"];

        $users = \FPanel\Models\Users::findFirst("username = '$username'");
        if (!$users) {
            $ip_list = array();
            $sys_ip = IpAddress::findFirst("is_default = '1'");
            $ip_list[] = $sys_ip->id;

            if(is_numeric($user_data['limits']['disk_limit']))
                $disk_limit = $user_data['limits']['disk_limit']/1024/1024;
            else
                $disk_limit = UNLIMITED;

            if(is_numeric($user_data['limits']['traffic_limit']))
                $traffic_limit = $user_data['limits']['traffic_limit']/1024/1024;
            else
                $traffic_limit = UNLIMITED;

            if(is_numeric($user_data['limits']['email_box_size_limit']))
                $email_box_size_limit = $user_data['limits']['email_box_size_limit']/1024/1024;
            else
                $email_box_size_limit = UNLIMITED;

            if(is_numeric($user_data['limits']['database_size_limit']))
                $database_size_limit = $user_data['limits']['database_size_limit']/1024/1024;
            else
                $database_size_limit = UNLIMITED;

            $post_data = array(
                'access_panel_perm' => $user_data['limits']['access_panel_perm'],
                'man_client' => $user_data['limits']['man_client'],
                'man_domain' => $user_data['limits']['man_domain'],
                'man_subdomain' => $user_data['limits']['man_subdomain'],
                'man_domain_alias' => $user_data['limits']['man_domain_alias'],
                'man_email_alias' => $user_data['limits']['man_email_alias'],
                'man_hosting_settings' => $user_data['limits']['man_hosting_settings'],
                'man_task' => $user_data['limits']['man_task'],
                'man_dns' => $user_data['limits']['man_dns'],
                'man_mailing_lists' => $user_data['limits']['man_mailing_lists'],
                'man_spam_filters' => $user_data['limits']['man_spam_filters'],
                'man_backup' => $user_data['limits']['man_backup'],
                'man_email' => $user_data['limits']['man_email'],
                'man_database' => $user_data['limits']['man_database'],
                'man_protect_dir' => $user_data['limits']['man_protect_dir'],
                'man_ssl' => $user_data['limits']['man_ssl'],
                'man_template' => $user_data['limits']['man_template'],

                'client_limit' => $user_data['limits']['client_limit'],
                'domain_limit' => $user_data['limits']['domain_limit'],
                'disk_limit' => $disk_limit,
                'traffic_limit' => $traffic_limit,
                'subdomain_limit' => $user_data['limits']['subdomain_limit'],
                'domain_alias_limit' => $user_data['limits']['domain_alias_limit'],
                'email_box_limit' => $user_data['limits']['email_box_limit'],
                'email_box_size_limit' => $email_box_size_limit,
                'email_forward_limit' => $user_data['limits']['email_forward_limit'],
                'email_groups_limit' => $user_data['limits']['email_groups_limit'],
                'email_autoresponder_limit' => $user_data['limits']['email_autoresponder_limit'],
                'email_lists_limit' => $user_data['limits']['email_lists_limit'],
                'database_limit' => $user_data['limits']['database_limit'],
                'database_size_limit' => $database_size_limit,
                'template_limit' => $user_data['limits']['template_limit'],
                'postgresql_limit'  => "0",
                'mssql_limit'  => "0",
                'mysql_limit'  => "0",
                'expire_time_limit' => "1",
                'overuse' => $user_data['limits']['overuse'],

                'php' => $user_data['limits']['php'],
                'php_version' => $user_data['limits']['php_version'],
                'cgi' => $user_data['limits']['cgi'],
                'perl' => $user_data['limits']['perl'],
                'python' => $user_data['limits']['python'],
                'fast_cgi' => $user_data['limits']['fast_cgi'],
                'statistic_support' => $user_data['limits']['statistic_support'],
                'statistic_type' => $user_data['limits']['statistic_type'],
                'asp' => $user_data['limits']['asp'],
                'aspnet_core' => $user_data['limits']['aspnet_core'],
                'aspnet' => $user_data['limits']['aspnet'],
                'aspnet_version' => $user_data['limits']['aspnet_version'],
                'dedicated_pool' => $user_data['limits']['dedicated_pool'],
                'max_cpu' => $user_data['limits']['max_cpu'],
                'application_pool_mode' => $user_data['limits']['application_pool_mode'],
                'ssl' => $user_data['limits']['ssl'],
                'web_statistics'  => $user_data['limits']['statistic_support'],
                'web_statistics_type'  => $user_data['limits']['statistic_type'],

                // User Info
                'username'   => $username,
                'type'   => RESELLER_USER,
                'password'   => $user_data['user']['password'],
                'fullname'   => $user_data['userdata']['fullname'],
                'email'   => $user_data['userdata']['email'],
                'company' => $user_data['userdata']['company'],
                'telephone' => $user_data['userdata']['telephone'],
                'address' => $user_data['userdata']['address'],
                'city' => $user_data['userdata']['city'],
                'zipcode' => $user_data['userdata']['zipcode'],
                'country' => $user_data['userdata']['country'],
                // Ip adressleri
                'ipaddr_id'    => $ip_list,
            );

            $user_components = new Components();

            $user_status = $user_components->userData(array(
                'user_id' => $parent['id'],
                'user_type' => $parent['type'],
            ))
                ->options(array())
                ->post($post_data,true)
                ->create(USER);

            if($user_status == true) {
                $users = \FPanel\Models\Users::findFirst("username = '$username'");
                return $users->id;
            }
            else {
                return false;
            }
        }
        return $users->id;
    }

    public function restoreDomain($domain_data, $parent_id, $parent_type)
    {
        /**
         * Burada önce domain create ediliyor. Ardından domaine tanımlanmış özel veriler update edilecek.
         * Daha sonra Domainin kullandığı datalar import edilecek.
         */
        $domain_name = $domain_data['domain']['domain_name'];
        $domain = Domains::findFirst("name = '$domain_name'");
        $builder = new Builder();

        if(!$domain) {
            $sys_ip = IpAddress::findFirst("is_default = '1'");
            $ip_id = $sys_ip->id;

            if(is_numeric($domain_data['limits']['disk_limit']))
                $disk_limit = $domain_data['limits']['disk_limit']/1024/1024;
            else
                $disk_limit = UNLIMITED;

            if(is_numeric($domain_data['limits']['traffic_limit']))
                $traffic_limit = $domain_data['limits']['traffic_limit']/1024/1024;
            else
                $traffic_limit = UNLIMITED;

            if(is_numeric($domain_data['limits']['email_box_size_limit']))
                $email_box_size_limit = $domain_data['limits']['email_box_size_limit']/1024/1024;
            else
                $email_box_size_limit = UNLIMITED;

            if(is_numeric($domain_data['limits']['database_size_limit']))
                $database_size_limit = $domain_data['limits']['database_size_limit']/1024/1024;
            else
                $database_size_limit = UNLIMITED;

            $domain_limits = $domain_data['limits'];

            $post_data = array(
                // Limit
                'client_limit'          => '0',
                'domain_limit'          => '0',
                'template_limit'        => '0',
                'disk_limit'            => $disk_limit,
                'traffic_limit'         => $traffic_limit,
                'subdomain_limit'       => $domain_limits['subdomain_limit'],
                'domain_alias_limit'    => $domain_limits['domain_alias_limit'],
                'email_box_limit'       => $domain_limits['email_box_limit'],
                'email_box_size_limit'  => $email_box_size_limit,
                'email_forward_limit'   => $domain_limits['email_forward_limit'],
                'email_groups_limit'    => $domain_limits['email_groups_limit'],
                'email_autoresponder_limit'   => $domain_limits['email_autoresponder_limit'],
                'email_lists_limit'     => $domain_limits['email_lists_limit'],
                'database_limit'        => $domain_limits['database_limit'],
                'database_size_limit'   => $database_size_limit,
                'expire_time_limit'     => "1",
                'overuse'               => $domain_limits['overuse'],
                // Permission
                'man_client'            => NOT_ALLOWED,
                'man_domain'            => NOT_ALLOWED,
                'man_template'          => NOT_ALLOWED,
                'access_panel'     => $domain_limits['access_panel'],
                'man_hosting_settings'  => $domain_limits['man_hosting_settings'],
                'man_subdomain'         => $domain_limits['man_subdomain'],
                'man_database'          => $domain_limits['man_database'],
                'man_domain_alias'      => $domain_limits['man_domain_alias'],
                'man_email'             => $domain_limits['man_email'],
                'man_email_alias'       => $domain_limits['man_email_alias'],
                'man_mailing_lists'     => $domain_limits['man_mailing_lists'],
                'man_spam_filters'      => $domain_limits['man_spam_filters'],
                'man_task'              => $domain_limits['man_task'],
                'man_dns'               => $domain_limits['man_dns'],
                'man_ssl'               => $domain_limits['man_ssl'],
                'man_backup'            => $domain_limits['man_backup'],
                'man_protect_dir'       => $domain_limits['man_protect_dir'],
                // Domain Settings
                'php'                   => $domain_limits['php'],
                'php_version'           => $domain_limits['php_version'],
                'cgi'                   => $domain_limits['cgi'],
                'perl'                  => $domain_limits['perl'],
                'python'                => $domain_limits['python'],
                'fast_cgi'              => PASSIVE,
                'web_statistics'        => $domain_limits['statistic_support'],
                'web_statistics_type'   => $domain_limits['statistic_type'],
                'asp'                   => $domain_limits['asp'],
                'aspnet_core'           => $domain_limits['aspnet_core'],
                'aspnet'                => $domain_limits['aspnet'],
                'aspnet_version'        => $domain_limits['aspnet_version'],
                'dedicated_pool'        => $domain_limits['dedicated_pool'],
                'max_cpu'               => $domain_limits['max_cpu'],
                'application_pool_mode' => $domain_limits['application_pool_mode'],
                'ssl'                   => PASSIVE,
                // Userdata
                'domain_name'           => $domain_data['domain']['domain_name'],
                'name'                  => $domain_data['domain']['domain_name'],
                'username'              => $domain_data['domain']['domain_name'],
                'type'                  => DOMAIN_USER,

                'fullname'              => $domain_data['userdata']['fullname'],
                'email'                 => $domain_data['userdata']['email'],

                'ftpname'               => $domain_data['ftpuser']['username'],
                'password'              => $domain_data['ftpuser']['password'],

                'company'               => $domain_data['userdata']['company'],
                'telephone'             => $domain_data['userdata']['telephone'],
                'address'               => $domain_data['userdata']['address'],
                'city'                  => $domain_data['userdata']['city'],
                'zipcode'               => $domain_data['userdata']['zipcode'],
                'country'               => $domain_data['userdata']['country'],

                // Selecteds
                'user_list'             => [$parent_id => $parent_id],
                'ip_list'               => [$ip_id => $ip_id],
                'selected_user'         => $parent_id,
                'ipaddr_id'             => $ip_id,
                'system_type'           => STANDALONE,
                'web_service'           => "on",
                'dns_service'           => "on",
                'mail_service'          => "on",
            );

            $domain_components = new Components();

            $status = $domain_components->userData(array(
                'user_id' => $parent_id,
                'user_type' => $parent_type,
            ))
                ->options(array())
                ->post($post_data)
                ->create(DOMAIN);

            if($status == true) {
                $domains = Domains::findFirst("name = '$domain_name'");
                $builder->restoreDomainSubs($domains->id, $domain_name, $domain_data['subdomains']);
                $builder->restoreDomainTasks($domains->id, $domain_name, $domain_data['cronjobs']);
                $builder->restoreDomainAlias($domains->id, $domain_name, $domain_data['domain_alias']);
                $builder->restoreDomainProtectDirs($domains->id, $domain_name, $domain_data['prodirs']);
                $builder->restoreDomainMails($domains->id, $domain_name, $domain_data['mails']);
                $builder->restoreDomainMailParams($domains->id, $domain_name, $domain_data['mail_params']);
                $builder->restoreClientDomainDatabases($domains->id, $domain_name, $domain_data['databases']);
            }
            else {
                $this->log->error([
                    "Module" => "RestoreClient",
                    "Action" => "restoreDomain",
                    "Error" => $domain_components->getMessages(),
                ]);
            }
        }
        else {
            $this->log->info([
                "Module" => "RestoreClient",
                "Action" => "restoreDomain",
                "Message" => "Domain already exists. Restoring other data",
            ]);
            $domains = Domains::findFirst("name = '$domain_name'");
            $builder->restoreDomainSubs($domains->id, $domain_name, $domain_data['subdomains']);
            $builder->restoreDomainTasks($domains->id, $domain_name, $domain_data['cronjobs']);
            $builder->restoreDomainAlias($domains->id, $domain_name, $domain_data['domain_alias']);
            $builder->restoreDomainProtectDirs($domains->id, $domain_name, $domain_data['prodirs']);
            $builder->restoreDomainMails($domains->id, $domain_name, $domain_data['mails']);
            $builder->restoreDomainMailParams($domains->id, $domain_name, $domain_data['mail_params']);
            $builder->restoreClientDomainDatabases($domains->id, $domain_name, $domain_data['databases']);
        }
    }
}

