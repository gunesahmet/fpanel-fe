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
 */namespace FPanel\Common\Migration;

use FPanel\Components\Components;
use FPanel\Domain\Cronjobs;
use FPanel\Domain\DomainAlias;
use FPanel\Domain\DomainDatabase;
use FPanel\Domain\Mails;
use FPanel\Domain\NamedService;
use FPanel\Domain\Protectdir;
use FPanel\Domain\Sslcertificate;
use FPanel\Domain\Subdomain;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\IpAddress;
use FPanel\Models\Mails as Mail;
use FPanel\Models\ProtectDirectory;
use FPanel\Models\ProtectDirectoryUser;
use FPanel\Models\Subdomains;
use FPanel\Models\Users;

/**
 * Class Builder
 * @package FPanel\Common\Migration
 */
class Builder extends Migration
{
    public function getUser($data, $ip_addr, $root, $root_pass): array
    {
        return $this->env->remote($ip_addr, $root);
    }

    public function getDomain($data, $ip_addr, $root, $root_pass): array
    {
        return $this->env->remote($ip_addr, $root);
    }

    public function createUser($user_data): bool
    {
        if($this->checkUser($user_data['users']['username']) == false) {
            $system_ip_addresses = IpAddress::findFirst("is_default = '1'");
            $default_ip = $system_ip_addresses->id;
            $limits = $user_data['client_limits_permission'];
            $user = $user_data['users'];
            $userdata = $user_data['userdata'];
            $parent = $user_data['parents'];

            if(is_numeric($limits['disk_limit']))
                $disk_limit = $limits['disk_limit']/1024/1024;
            else
                $disk_limit = UNLIMITED;

            if(is_numeric($limits['traffic_limit']))
                $traffic_limit = $limits['traffic_limit']/1024/1024;
            else
                $traffic_limit = UNLIMITED;

            if(is_numeric($limits['email_box_size_limit']))
                $email_box_size_limit = $limits['email_box_size_limit']/1024/1024;
            else
                $email_box_size_limit = UNLIMITED;

            if(is_numeric($limits['database_size_limit']))
                $database_size_limit = $limits['database_size_limit']/1024/1024;
            else
                $database_size_limit = UNLIMITED;

            $post_data = array(
                'man_client' => $limits['man_client'],
                'man_domain' => $limits['man_domain'],
                'man_template'   => $limits['man_template'],
                'access_panel_perm'  => $limits['access_panel_perm'],
                'man_hosting_settings'   => $limits['man_hosting_settings'],
                'man_subdomain'  => $limits['man_subdomain'],
                'man_database'   => $limits['man_database'],
                'man_domain_alias'   => $limits['man_domain_alias'],
                'man_email'  => $limits['man_email'],
                'man_email_alias'    => $limits['man_email_alias'],
                'man_mailing_lists'  => $limits['man_mailing_lists'],
                'man_spam_filters'   => $limits['man_spam_filters'],
                'man_task'   => $limits['man_task'],
                'man_dns'    => $limits['man_dns'],
                'man_ssl'    => $limits['man_ssl'],
                'man_backup'   => $limits['man_backup'],
                'man_protect_dir'    => $limits['man_protect_dir'],

                'client_limit'    => $limits['client_limit'],
                'domain_limit'    => $limits['domain_limit'],
                'template_limit' => $limits['template_limit'],
                'disk_limit'  => $disk_limit,
                'traffic_limit'   => $traffic_limit,
                'subdomain_limit' => $limits['subdomain_limit'],
                'domain_alias_limit'  => $limits['domain_alias_limit'],
                'email_box_limit' => $limits['email_box_limit'],
                'email_box_size_limit'    => $email_box_size_limit,
                'email_forward_limit' => $limits['email_forward_limit'],
                'email_groups_limit'  => $limits['email_groups_limit'],
                'email_autoresponder_limit'   => $limits['email_autoresponder_limit'],
                'email_lists_limit'    => $limits['email_lists_limit'],
//                'postgresql_limit'  => $limits['postgresql_limit'],
//                'mssql_limit'  => $limits['mssql_limit'],
//                'mysql_limit'  => $limits['mysql_limit'],
                'database_limit'  => $limits['database_limit'],
                'database_size_limit' => $database_size_limit,
                'expire_time_limit' => UNLIMITED,
                'overuse' => $limits['overuse'],

                'php'  => $limits['php'],
                'php_version'  => $limits['php_version'],
                'cgi'  => $limits['cgi'],
                'perl'  => $limits['perl'],
                'python'  => $limits['python'],
                'fast_cgi'  => OFF,
                'web_statistics'  => $limits['statistic_support'],
                'web_statistics_type'  => $limits['statistic_type'],
                'asp'  => $limits['asp'],
                'aspnet_core'  => $limits['aspnet_core'],
                'aspnet'  => $limits['aspnet'],
                'aspnet_version'  => $limits['aspnet_version'],
                'dedicated_pool'  => $limits['dedicated_pool'],
                'max_cpu'  => $limits['max_cpu'],
                'application_pool_mode'  => $limits['application_pool_mode'],
                'ssl'  => PASSIVE,

                // User Info
                'username'   => $user['username'],
                'type'   => $user['type'],
                'password'   => $user['password'],
                'fullname'   => $userdata['fullname'],
                'email'   => $userdata['email'],
                'company' => $userdata['company'],
                'telephone' => $userdata['telephone'],
                'address' => $userdata['address'],
                'city' => $userdata['city'],
                'zipcode' => $userdata['zipcode'],
                'country' => $userdata['country'],
                // Ip adressleri
                'ipaddr_id'    => array($default_ip),
            );

            $parents = Users::findFirst("username = '$parent[username]'");
            if(!$parents) {
                $parents = Users::findFirst("username = 'admin'");
            }
            $parent_id = $parents->id;
            $parent_type = $parents->type;

            $user_id = $parent_id;
            $user_type = $parent_type;

            $user_components = new Components();

            $status = $user_components->userData(array(
                'user_id' => $user_id,
                'user_type' => $user_type,
            ))
                ->options(array())
                ->post($post_data,true)
                ->create(USER);
        }
        else {
            $status= $this->updateUser($user_data);
        }
        if($status == true)
            return true;
        else
            return false;
    }

    public function createDomain($domain_data): bool
    {
        if($this->checkDomain($domain_data['domains']['name']) == false) {
            $system_ip_addresses = IpAddress::findFirst("is_default = '1'");
            $default_ip = $system_ip_addresses->id;
            $limits = $domain_data['client_limits_permission'];
            $userdata = $domain_data['userdata'];
            $domain = $domain_data['domains'];
            $ftp_user = $domain_data['ftpusers'];
            $parent = $domain_data['parent'];

            if(is_numeric($limits['disk_limit']))
                $disk_limit = $limits['disk_limit']/1024/1024;
            else
                $disk_limit = UNLIMITED;

            if(is_numeric($limits['traffic_limit']))
                $traffic_limit = $limits['traffic_limit']/1024/1024;
            else
                $traffic_limit = UNLIMITED;

            if(is_numeric($limits['email_box_size_limit']))
                $email_box_size_limit = $limits['email_box_size_limit']/1024/1024;
            else
                $email_box_size_limit = UNLIMITED;

            if(is_numeric($limits['database_size_limit']))
                $database_size_limit = $limits['database_size_limit']/1024/1024;
            else
                $database_size_limit = UNLIMITED;

            $post_data = array(
                // Limit
                'client_limit'    => '0',
                'domain_limit'    => '0',
                'template_limit' => '0',
                'disk_limit'  => $disk_limit,
                'traffic_limit'   => $traffic_limit,
                'subdomain_limit' => $limits['subdomain_limit'],
                'domain_alias_limit'  => $limits['domain_alias_limit'],
                'email_box_limit' => $limits['email_box_limit'],
                'email_box_size_limit'    => $email_box_size_limit,
                'email_forward_limit' => $limits['email_forward_limit'],
                'email_groups_limit'  => $limits['email_groups_limit'],
                'email_autoresponder_limit'   => $limits['email_autoresponder_limit'],
                'email_lists_limit'    => $limits['email_lists_limit'],
//                'postgresql_limit'  => $limits['postgresql_limit'],
//                'mssql_limit'  => $limits['mssql_limit'],
//                'mysql_limit'  => $limits['mysql_limit'],
                'database_limit'  => $limits['database_limit'],
                'database_size_limit' => $database_size_limit,
                'expire_time_limit' => UNLIMITED,
                'overuse' => $limits['overuse'],

                'man_client' => $limits['man_client'],
                'man_domain' => $limits['man_domain'],
                'man_template'   => $limits['man_template'],
                'access_panel_perm'  => $limits['access_panel_perm'],
                'man_hosting_settings'   => $limits['man_hosting_settings'],
                'man_subdomain'  => $limits['man_subdomain'],
                'man_database'   => $limits['man_database'],
                'man_domain_alias'   => $limits['man_domain_alias'],
                'man_email'  => $limits['man_email'],
                'man_email_alias'    => $limits['man_email_alias'],
                'man_mailing_lists'  => $limits['man_mailing_lists'],
                'man_spam_filters'   => $limits['man_spam_filters'],
                'man_task'   => $limits['man_task'],
                'man_dns'    => $limits['man_dns'],
                'man_ssl'    => $limits['man_ssl'],
                'man_backup'   => $limits['man_backup'],
                'man_protect_dir'    => $limits['man_protect_dir'],

                'php'  => $limits['php'],
                'php_version'  => $limits['php_version'],
                'cgi'  => $limits['cgi'],
                'perl'  => $limits['perl'],
                'python'  => $limits['python'],
                'fast_cgi'  => PASSIVE,
                'web_statistics'  => $limits['statistic_support'],
                'web_statistics_type'  => $limits['statistic_type'],
                'asp'  => $limits['asp'],
                'aspnet_core'  => $limits['aspnet_core'],
                'aspnet'  => $limits['aspnet'],
                'aspnet_version'  => $limits['aspnet_version'],
                'dedicated_pool'  => $limits['dedicated_pool'],
                'max_cpu'  => $limits['max_cpu'],
                'application_pool_mode'  => $limits['application_pool_mode'],
                'ssl'  => PASSIVE,

                'domain_name'   => $domain['name'],
                'name'   => $domain['name'],
                'username'   => $domain['name'],
                'fullname'   => $userdata['fullname'],
                'type'  => DOMAIN_USER,
                'email'   => "info@".$domain['name'],
                'ftpname'  => $ftp_user['ftpname'],
                'password'  => trim($this->crypto->bdecode($ftp_user['password'])),
                'company' => $userdata['company'],
                'telephone' => $userdata['telephone'],
                'address' => $userdata['address'],
                'city' => $userdata['city'],
                'zipcode' => $userdata['zipcode'],
                'country' => $userdata['country'],
                'ipaddr_id'    => $default_ip,
                'system_type'    => PANEL_TYPE,
                'web_service' => "on",
                'dns_service' => "on",
                'mail_service' => "on",
                );

            $parents = Users::findFirst("username = '$parent[username]'");
            if($parents) {
                $parent_data = $parents->toArray();
                $user_id = $parent_data['id'];
                $user_type = $parent_data['type'];
            }
            else {
                $user_id = "1";
                $user_type = ROOT_USER;
            }

            $domain_components = new Components();

            $domain_components->userData(array(
                'user_id' => $user_id,
                'user_type' => $user_type,
            ))
                ->options(array())
                ->post($post_data,true)
                ->create(DOMAIN);
        }
        $this->createDomainDatabases($domain_data);
        $this->createDomainSubs($domain_data);
        $this->createDomainAlias($domain_data);
        $this->createDomainDNS($domain_data);
        $this->updateMailParams($domain_data);
        $this->createDomainMails($domain_data);
        $this->createDomainProtectDirs($domain_data);
        $this->createDomainTasks($domain_data);
        $this->createSSLCert($domain_data);
        return true;
    }

    private function checkUser($username): bool
    {
        $user = Users::findFirst("username = '$username'");
        if($user) {
            return true;
        }
        else {
            return false;
        }
    }

    private function checkDomain($domain_name): bool
    {
        $domain = Domains::findFirst("name = '$domain_name'");
        if($domain) {
            return true;
        }
        else {
            return false;
        }
    }

    public function updateUser($user_data): bool
    {
        $user = $user_data['users'];

        if($user['type'] == RESELLER_USER) {
            $users = Users::findFirst("username = '$user[username]'");
            if($users) {
                if($users->type == CLIENT_USER) {
                    $users->type = RESELLER_USER;
                    $users->update();
                }
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return true;
        }
    }

    private function createDomainDatabases($domain_data): bool
    {
        //@TODO: Eğer açılmış bir domainin database'i yoksa açılacak

        $databases = $domain_data['databases'];
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];
        foreach($databases as $database) {
            switch($database['type']) {
                case "mysql":
                    $mysql_database_server = DatabaseServer::findFirst("type = 'mysql'");
                    if($mysql_database_server)
                        $db_server_id = $mysql_database_server->id;
                    else
                        $db_server_id = null;
                    break;
                case "mssql":
                    $mssql_database_server = DatabaseServer::findFirst("type = 'mssql'");
                    if($mssql_database_server)
                        $db_server_id = $mssql_database_server->id;
                    else
                        $db_server_id = null;
                    break;
                default:
                    $db_server_id = null;
                    break;
            }
            if($db_server_id == null)
                continue;
            $db_status = Databases::findFirst("name = '$database[name]' AND type = '$database[type]' AND domain_id = '$domain_id'");


            $database_name = $database['name'];

            if(!$db_status) {
                $post_data = array(
                    'action' => "create",
                    'domain_id' => $domain_id,
                    'domain_name' => $domain_name,
                    'database_type' => $database['type'],
                    'database_server_id' => $db_server_id,
                    'database_name' => $database_name,
                    'db_user_name' => $database['username'],
                    'db_user_password' => $database['password'],
                    'database_server_list' => "",
                    'prefix' => $domain->prefix,
                );

                $database = new DomainDatabase();
                $database->setPosts($post_data);
                $database->createDatabase(true);
            }
        }
        return true;
    }

    private function createDomainSubs($domain_data): bool
    {
        //@TODO: Eğer açılmış bir domainin subdomaini yoksa açılacak
        $subdomains = $domain_data['subdomains'];
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $ftpusers = FtpUsers::findFirstById($domains->ftp_user_id);
        $sys_ip_address = IpAddress::findFirstById($domain['ipaddr_id']);
        $domain_id = $domains->id;
        $domain_name = $domain['name'];
        $domain_ftp_user_id = $domains->ftp_user_id;
        $domain_ftpname = $ftpusers->username;
        $domain_ip_addr = $sys_ip_address->ip_address;
        $domain_ip_addr_id = $sys_ip_address->id;
        $domain_parent = $domains->client_id;

        foreach($subdomains as $subdomain) {

            $sub_status = Subdomains::findFirst("subdomain_name = '$subdomain[subdomain_name]' AND domain_id = '$domain_id'");

            if($sub_status) {
                continue;
            }

            $ftp_type = false;
            if($subdomain['ftpuser_type'] == NATIVE)
                $ftp_type = true;

            if(!empty($subdomain['ftp_username'])) {
                $sub_ftp_name = $subdomain['ftp_username'];
                $sub_ftp_passwd = trim($this->crypto->bdecode($subdomain['ftp_password']));
            }
            else {
                $sub_ftp_name = null;
                $sub_ftp_passwd = null;
            }


            $post_data = array(
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'domain_ftpuser' => $domain_ftpname, // Domaine ait ftp user
                'ipaddress' => $domain_ip_addr, // Domaine ait ipadresi
                'domain_ftp_user_id' => $domain_ftp_user_id, // Domaine ait ftp user idsi
                'domain_parent' => $domain_parent, // Domainin sahibinin idsi
                'ipaddr_id' => $domain_ip_addr_id, // Domainin ip adresinin idsi

                'subdomain_name' => $subdomain['subdomain_name'],
                'full_name' => $subdomain['subdomain_name'].'.'.$domain_name,
                'status' => "1",

                // Özel kural devreye girecek
                'use_native_ftp' => $ftp_type,
                'ftpname'   => $sub_ftp_name,
                'password'   => $sub_ftp_passwd,

                // Subdomain host parametreleri
                'php'               => $subdomain['php'],
                'php_version'       => $subdomain['php_version'],
                'asp'               => $subdomain['asp'],
                'aspnet_core'       => $subdomain['aspnet_core'],
                'aspnet'            => $subdomain['aspnet'],
                'aspnet_version'    => $subdomain['aspnet_version'],
                'cgi'               => $subdomain['cgi'],
                'perl'              => $subdomain['perl'],
                'python'            => $subdomain['python'],
                'dedicated_pool'    => $subdomain['dedicated_pool'],
                'max_cpu'           => $subdomain['max_cpu'],
                'ssl'               => PASSIVE,
            );
            $subdomain = new Subdomain();
            $subdomain->setPosts($post_data);
            $create_subdomain_messages = $subdomain->createSubdomain(false);
            if(!$create_subdomain_messages) {
                $this->log->error([
                    "Module" => "Migration",
                    "Action" => "createSubdomain",
                    "Message" => $subdomain->getMessages()
                ]);
            }
        }
        return true;
    }

    private function createDomainAlias($domain_data)
    {
        $domain_aliases = $domain_data['domain_alias'];
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];
        foreach($domain_aliases as $alias) {
            $domain_alias = new DomainAlias();
            $alias_status = \FPanel\Models\DomainAlias::findFirst("alias_name = '$alias[alias_name]'");

            if($alias_status) {
                continue;
            }
            $post_data = array(
                'update'            => false,
                'domain_name'       => $domain_name,
                'domain_id'         => $domain_id,
                'alias_name'        => $alias['alias_name'],
                'syn_pri_dom'       => "1",
                'dns'               => "1",
                'status'            => "1",
                'mail'              => $alias['mail'],
                'web'               => $alias['web'],
                'old_alias'         => "",
            );
            $domain_alias->setPosts($post_data);
            $domain_alias->createAlias();

        }
    }

    private function createDomainDNS($domain_data)
    {
        $dns_records = $domain_data['dns'];
        $dns = new NamedService();
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];

        foreach($dns_records as $record) {
            $post_data = array(
                'action' => "create",
                'update' => false,
                // Değişkenler
                'domain_id'   => $domain_id,
                'domain_name'   => $domain_name,
                'time_stamp'   => time(),
                // Post Dataları
                'type'   => $record['type'],

                // NS
                'ns_host'  => $domain_name,
                'ns_value' => '',
                'ns_opt' => '',

                // A
                'a_host' => '',
                'a_value' => '',
                'a_opt' => '',

                // CNAME
                'cname_host' => '',
                'cname_value' => '',
                'cname_opt' => '',

                // MX
                'mx_host'  => $domain_name,
                'mx_value' => '',
                'mx_opt' => '',

                // PTR
                'ptr_host' => '',
                'ptr_value' => '',
                'ptr_opt' => '',

                // TXT
                'txt_host' => $domain_name,
                'txt_value' => '',
                'txt_opt' => '',

                'old_type' => '',
                'old_host' => '',
                'old_value' => '',
            );
            switch($record['type']) {
                case "NS":
                    $post_data['ns_value'] = str_replace('.'.$domain_name,'', $record['value']);
                    break;
                case "A":
                    $post_data['a_host'] = str_replace('.'.$domain_name,'', $record['host']);
                    $post_data['a_value'] = $record['value'];
                    break;
                case "PTR":
                    if($record['value'] == $domain_name)
                        $value = $record['value'];
                    else
                        $value = str_replace('.'.$domain_name,'', $record['value']);
                    $post_data['ptr_host'] = $record['host'];
                    $post_data['ptr_value'] = $value;
                    $post_data['ptr_opt'] = $record['opt'];
                    break;
                case "MX":
                    $post_data['mx_opt'] = $record['opt'];
                    $post_data['mx_value'] = $record['value'];
                    break;
                case "TXT":
                    $post_data['txt_value'] = $record['value'];
                    break;
                case "CNAME":
                    $post_data['cname_host'] = str_replace('.'.$domain_name,'', $record['host']);
                    $post_data['cname_value'] = $record['value'];
                    break;
            }
            $dns->domain_name = $domain_name;
            $dns->setPosts($post_data);
            $dns->createDNSRecords();
        }
    }

    private function updateMailParams($domain_data)
    {
        $mail_params = $domain_data['mail_params'];
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];
        if($mail_params['mail_forward_addr'] == null) {
            $forwarding = false;
        }
        else {
            $forwarding = true;
        }

        if($mail_params['nonexistend_user'] == "reject")
            $reject_incoming = true;
        else {
            $reject_incoming = false;
        }

        $mail_settings = new Mails();
        $post_data = array(
            'domain_name'             => $domain_name,
            'domain_id'             => $domain_id,
            'forward_to_address'    => $forwarding,
            'mail_address'          => $mail_params['mail_forward_addr'],
            'reject_incoming'       => $reject_incoming,
        );
        $mail_settings->domain_name = $domain_name;
        $mail_settings->setPosts($post_data);
        $mail_settings->updateMailSettings();

    }

    private function createDomainMails($domain_data)
    {
        $mails = $domain_data['mails'];
        $mail_account = new Mails();
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];

        foreach($mails as $mail) {
            $mail_status = Mail::findFirst("mail_name = '$mail[mail_name]' AND domain_id = '$domain_id'");
            if($mail_status) {
                continue;
            }

            $unlimited = true;
            if($mail['mailbox_quota'] != UNLIMITED)
                $unlimited = false;

            $post_data = array(
                'domain_name'       => $domain_name,
                'domain_id'         => $domain_id,
                'mail_account'      => $mail['mail_name'],
                'password'          => trim($this->crypto->bdecode($mail['password'])),
                'mailbox_quota'     => $mail['mailbox_quota'],
                'unlimited_quota'   => $unlimited,
                'status'            => ($mail['status'] ?? "on"),
            );
            $mail_account->setPosts($post_data);

            if($mail_account->createMailAccount(true)) {
                $mail_model = Mail::findFirst("mail_name = '$mail[mail_name]'");
                $mail_id = $mail_model->id;

                $post_data = array(
                    'mail_id'           => $mail_id,
                    'domain_name'       => $domain_name,
                    'domain_id'         => $domain_id,
                    'mail_account'      => $mail['mail_name'],
                    'password'          => trim($this->crypto->bdecode($mail['password'])),
                    'mailbox_quota'     => $mail['mailbox_quota'],
                    'unlimited_quota'   => $unlimited,
                    'forward'           => $mail['forward'],
                    'autoresponder'     => $mail['autoresponder'],
                    'forward_addr'      => $mail['forward_addr'],
                    'old_address'       => $mail['mail_name'],
                );
                $mail_account = new Mails();
                $mail_account->setPosts($post_data);
                $mail_account->updateMailAccount();

                foreach($mail['alias_list'] as $mail_alias) {
                    $post_data = array(
                        'mail_account'    => $mail['mail_name'],
                        'domain_name'     => $domain_name,
                        'domain_id'       => $domain_id,
                        'mail_id'         => $mail_id,
                        'alias_name'      => $mail_alias['alias_name']
                    );
                    $mail_alias = new Mails();
                    $mail_alias->setPosts($post_data);
                    $mail_alias->createMailAlias(true);
                }
            }
        }
    }

    private function createDomainProtectDirs($domain_data)
    {
        $protect_dirs = $domain_data['protect_dirs'];
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];

        foreach($protect_dirs as $pro_dir) {
            $protectdir = new Protectdir();

            $pro_status = ProtectDirectory::findFirst("path = '$pro_dir[path]' AND domain_id = '$domain_id'");
            if($pro_status) {
                $sub_id = "0";
                if($pro_dir['sub_domain_name'] != null) {
                    $subdomains = Subdomains::findFirst("subdomain_name = '$pro_dir[sub_domain_name]'");
                    $sub_id = $subdomains->id;
                }
                $post_data = array(
                    'domain_id'     => $domain_id,
                    'path'          => $pro_dir['path'],
                    'ssl'           => PASSIVE,
                    'cgi_bin'       => PASSIVE,
                    'header'        => $pro_dir['header'],
                    'subdomain_id'  => $sub_id,
                );
                $protectdir->domain_name = $domain_name;
                $protectdir->setPosts($post_data);
                $protectdir_save_status = $protectdir->saveProtectdir(true);
                $protect_id = $protectdir_save_status;

            }
            else
            {
                $protect_id = $pro_status->id;
            }


            if(is_numeric($protect_id)) {
                foreach($pro_dir['users'] as $user) {
                    $pro_user_status = ProtectDirectoryUser::findFirst("username = '$user[username]' AND protect_dir_id = '$protect_id'");
                    if($pro_user_status) {
                        continue;
                    }
                    $post_data = array(
                        'domain_id'         => $domain_id,
                        'protect_dir_id'     => $protect_id,
                        'username'          => $user['username'],
                        'password'          => trim($this->crypto->bdecode($user['password'])),
                    );
                    $protectdir_user = new Protectdir();
                    $protectdir_user->setPosts($post_data);
                    $protectdir_user->domain_name = $domain_name;
                    $protectdir_user->saveProtectDirUser();
                }
            }
        }
    }

    private function createDomainTasks($domain_data)
    {
        $tasks = $domain_data['scheduled_tasks'];
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];
        $ftp_user_id = $domains->ftp_user_id;

        foreach($tasks as $task) {
            $status = false;
            if($task['status'] == "1") {
                $status = true;
            }
            $post_data = array(
                // Task Status
                'ftp_user_id'           => $ftp_user_id,
                'domain_id'             => $domain_id,
                'domain_name'           => $domain_name,
                'status'                => $status,
                'minute'                => $task['minute'],
                'hour'                  => $task['hour'],
                'day'                   => $task['day'],
                'week'                  => $task['week'],
                'month'                 => $task['month'],
                'command'               => $task['command'],
                'description'           => $task['description'],
            );
            $task = new Cronjobs();
            $task->setPosts($post_data);
            $task->saveDomain();
        }
    }

    private function createSSLCert($domain_data)
    {
        $certificates = $domain_data['certificates'];
        $ssl_certificate = new Sslcertificate();
        $domain = $domain_data['domains'];
        $domains = Domains::findFirst("name = '$domain[name]'");
        $domain_id = $domains->id;
        $domain_name = $domain['name'];

        foreach($certificates as $certificate) {

            $post_data = array(
                'name'          => $this->request->getPost('crt_name'),
                'domain_name'   => $domain_name,
                'domain_id'     => $domain_id,
                'private_key'   => $certificate['private_key'],
                'crt'           => $certificate['crt'],
                'ca_crt'        => $certificate['ca'],
            );
            $ssl_certificate->setPosts($post_data);
            $ssl_certificate->uploadCertificateFile();
        }
    }
}