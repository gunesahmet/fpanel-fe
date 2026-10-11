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

use FPanel\Common\Base;
use FPanel\Models\Certificates;
use FPanel\Models\ClientLimitsPermissions;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseUsers;
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;
use FPanel\Models\DomainAlias;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\MailAlias;
use FPanel\Models\Mails;
use FPanel\Models\MailSettings;
use FPanel\Models\ProtectDirectory;
use FPanel\Models\ProtectDirectoryUser;
use FPanel\Models\ScheduledTasks;
use FPanel\Models\Serviceplans;
use FPanel\Models\Subdomains;
use FPanel\Models\Userdata;
use FPanel\Models\Users;

/**
 * Class Migration
 * @package FPanel\Common\Migration
 */
class Migration extends Base
{

    public $ip_address;
    public $server_os;
    public $panel_type;
    public $migration_id;

    // Remote ROOT & PASS
    public $root;
    public $root_pass;

    // Remote USER & PASS
    public $remote_user;
    public $remote_pass;

    // User & Domains
    public $selected_resellers;
    public $selected_clients;
    public $selected_domains;

    public $options;
    public $progress;

    public function get(): array
    {

        $reseller_lists = array();
        $client_lists = array();
        $domain_lists = array();

        // Reseller Hesapları
        $resellers = Users::find("parent = '1' AND type = '2'");
        if($resellers) {
            foreach($resellers->toArray() as $reseller) {
                $clients = Users::find("parent = '$reseller[id]' AND type = '3'");
                $reseller_lists[$reseller['username']]['clients'] = array();
                if($clients) {
                    // Reseller altındaki client hesapları
                    foreach($clients->toArray() as $client) {
                        $reseller_lists[$reseller['username']]['clients'][$client['username']] = array();
                        // Reseller->Client altındaki domain hesapları
                        $client_domains = Domains::find("client_id = '$client[id]'");
                        if($client_domains) {
                            foreach($client_domains->toArray() as $domain) {
                                $reseller_lists[$reseller['username']]['clients'][$client['username']]['domains'][] = $domain['name'];
                            }
                        }
                    }
                }


                //Reseller altındaki domain hesapları
                $reseller_domains = Domains::find("client_id = '$reseller[id]'");
                if($reseller_domains) {
                    foreach($reseller_domains->toArray() as $domain) {
                        $reseller_lists[$reseller['username']]['domains'][] = $domain['name'];
                    }
                }
            }
        }

        // Client hesapları
        $clients = Users::find("parent = '1' AND type = '3'");
        if($clients) {
            foreach($clients->toArray() as $client) {
                $client_lists[$client['username']]['domains'] = array();
                // Client altındaki domain hesapları
                $client_domains = Domains::find("client_id = '$client[id]'");
                if($client_domains) {
                    foreach($client_domains->toArray() as $domain) {
                        $client_lists[$client['username']]['domains'][] = $domain['name'];
                    }
                }
            }
        }

        $admin_domains = Domains::find("client_id = '1'");
        if($admin_domains) {
            foreach($admin_domains->toArray() as $domain) {
                $domain_lists[] = $domain['name'];
            }
        }

        return array(
            'resellers' => $reseller_lists,
            'clients' => $client_lists,
            'domains' => $domain_lists,
        );
    }

    public function start(): Migration
    {
        $config = $this->env->readConfig(ROOTPATH.'/tmp/.migration.config');

        $this->ip_address = $config['ip_address'];
        $this->server_os = $config['system'];
        $this->panel_type = $config['system_type'];
        $this->migration_id = $config['migration_id'];

        $this->root = $config['root'];
        $this->root_pass = $config['root_pass'];

        $this->remote_user = $config['remote_user'];
        $this->remote_pass = $config['remote_pass'];

        $this->selected_resellers = $config['resellers'];
        $this->selected_clients = $config['clients'];
        $this->selected_domains = $config['domains'];

        $this->options = $config['options'];
        $this->progress = $config['progress'];

        return $this;
    }

    public function accounts(): Migration
    {
        $this->resellers();
        $this->clients();
        return $this;
    }

    private function resellers()
    {
        $total_resellers = count($this->selected_resellers);
        $get_accounts = new Builder();

        if(!empty($this->selected_resellers)) {
            foreach($this->selected_resellers as $reseller => $status) {
                $progress = 5;
                $this->progress = round($this->progress + ($progress/$total_resellers),2);

                if($this->options['replace_exists_accounts'] != "on") {
                    $reseller_status = Users::findFirst("username = '$reseller'");
                    if(!$reseller_status) {
                        $data = array(
                            'username' => $this->remote_user,
                            'password' => $this->remote_pass,
                            'module'    => 'migration',
                            'action'  => 'get-user',
                            'data'     => array(
                                'client_name'    => $reseller,
                            )
                        );
                        $reseller_data = $get_accounts->getUser($data, $this->ip_address, $this->root, $this->root_pass);
                        $reseller_create_status = $get_accounts->createUser($reseller_data);
                        $this->selected_resellers[$reseller] = $reseller_create_status;
                    }
                }
                else {
                    $data = array(
                        'username' => $this->remote_user,
                        'password' => $this->remote_pass,
                        'module'    => 'migration',
                        'action'  => 'get-user',
                        'data'     => array(
                            'client_name'    => $reseller,
                        )
                    );
                    $reseller_data = $get_accounts->getUser($data, $this->ip_address, $this->root, $this->root_pass);
                    $reseller_create_status = $get_accounts->createUser($reseller_data);
                    $this->selected_resellers[$reseller] = $reseller_create_status;
                }
                $this->statusUpdate();
            }
        }

    }

    private function clients()
    {
        $get_accounts = new Builder();
        $total_clients = count($this->selected_clients);

        foreach($this->selected_clients as $client => $status) {
            $progress = 5;
            $this->progress = round($this->progress + ($progress/$total_clients),2);

            if($this->options['replace_exists_accounts'] != "on") {
                $client_status = Users::findFirst("username = '$client'");
                if(!$client_status) {
                    $data = array(
                        'username' => $this->remote_user,
                        'password' => $this->remote_pass,
                        'module'    => 'migration',
                        'action'  => 'get-user',
                        'data'     => array(
                            'client_name'    => $client,
                        )
                    );
                    $client_data = $get_accounts->getUser($data, $this->ip_address, $this->root, $this->root_pass);
                    $client_create_status = $get_accounts->createUser($client_data);
                    $this->selected_clients[$client] = $client_create_status;
                }
            }
            else {
                $data = array(
                    'username' => $this->remote_user,
                    'password' => $this->remote_pass,
                    'module'    => 'migration',
                    'action'  => 'get-user',
                    'data'     => array(
                        'client_name'    => $client,
                    )
                );
                $client_data = $get_accounts->getUser($data, $this->ip_address, $this->root, $this->root_pass);
                $client_create_status = $get_accounts->createUser($client_data);
                $this->selected_clients[$client] = $client_create_status;
            }
            $this->statusUpdate();
        }
    }

    public function domains(): bool
    {
        // Not: Burada domain tek tek işleneceği için buradaki fonksiyon ayrı olarak işleme alınacaktır.
        $get_accounts = new Builder();
        $domain_name = null;
        if($this->progress == "0")
            $current_prog = "100";
        else
            $current_prog = abs(100 - $this->progress);

        $last_prog = abs($this->progress + ($current_prog/count($this->selected_domains)));
        $this->progress = round($last_prog);

        $state = false;
        foreach($this->selected_domains as $domain => $status) {
            if(!$status) {
                $state = true;
                $domain_name = $domain;
                break;
            }
        }

        if($domain_name != null) {
            if($this->options['replace_exists_accounts'] != "on") {

                $domain_status = Domains::findFirst("name = '$domain_name'");
                if(!$domain_status) {
                    $data = array(
                        'username' => $this->remote_user,
                        'password' => $this->remote_pass,
                        'module'    => 'migration',
                        'action'  => 'get-domain',
                        'data'     => array(
                            'domain_name'    => $domain_name,
                        )
                    );
                    $domain_data = $get_accounts->getDomain($data, $this->ip_address, $this->root, $this->root_pass);
                    $domain_create_status = $get_accounts->createDomain($domain_data);
                    $this->selected_domains[$domain_name] = $domain_create_status;
                    $this->statusUpdate();
                    $this->getDomainData($domain_name, $this->ip_address, $this->panel_type, $this->server_os, $this->migration_id, $this->root, $this->root_pass);
                }
                else {
                    $this->selected_domains[$domain_name] = true;
                    $this->statusUpdate();
                    $this->getDomainData($domain_name, $this->ip_address, $this->panel_type, $this->server_os, $this->migration_id, $this->root, $this->root_pass,"on");
                }
            }
            else {

                $data = array(
                    'username' => $this->remote_user,
                    'password' => $this->remote_pass,
                    'module'    => 'migration',
                    'action'  => 'get-domain',
                    'data'     => array(
                        'domain_name'    => $domain_name,
                    )
                );
                $domain_data = $get_accounts->getDomain($data, $this->ip_address, $this->root, $this->root_pass);
                $domain_create_status = $get_accounts->createDomain($domain_data);
                $this->selected_domains[$domain_name] = $domain_create_status;
                $this->statusUpdate();
                $this->getDomainData($domain_name, $this->ip_address, $this->panel_type, $this->server_os, $this->migration_id, $this->root, $this->root_pass);
            }
        }

        if($state == false) {
            $this->statusUpdate(true);
        }
        return true;
    }

    public function serverConfigs(): Migration
    {
        // Not: Burada domain işlemleri start alıyor ki sonraki işlemlere devam edilebilsin.
        $this->domains();
        return $this;
    }

    private function statusUpdate($finish = false): bool
    {
        if($finish == true) {
            $migration = \FPanel\Models\Migration::findFirstById($this->migration_id);
            $migration->assign(array(
                'status' => '0'
            ));
            $migration->update();
            $config_data = array(
                'migration_id' => $this->migration_id,
                'remote_user' => $this->remote_user,
                'remote_pass' => $this->remote_pass,
                'domains' => $this->selected_domains,
                'clients'   => $this->selected_clients,
                'resellers' => $this->selected_resellers,
                'options'   => $this->options,
                'ip_address' => $this->ip_address,
                'root'      => $this->root,
                'root_pass' => $this->root_pass,
                'system_type' => $this->panel_type,
                'system' => $this->server_os,
                'progress' => '100',
            );
        }
        else {
            $config_data = array(
                'migration_id' => $this->migration_id,
                'remote_user' => $this->remote_user,
                'remote_pass' => $this->remote_pass,
                'domains' => $this->selected_domains,
                'clients'   => $this->selected_clients,
                'resellers' => $this->selected_resellers,
                'options'   => $this->options,
                'ip_address' => $this->ip_address,
                'root'      => $this->root,
                'root_pass' => $this->root_pass,
                'system_type' => $this->panel_type,
                'system' => $this->server_os,
                'progress' => $this->progress,
            );
        }
        $this->env->writeConfig(ROOTPATH . '/tmp',".migration.config", $config_data);
        return true;
    }

    public function createUserData($data)
    {
        $username = $data['client_name'];
        $users = Users::findFirst("username = '$username'");
        if($users) {
            $user = $users->toArray();
            $user_id = $user['id'];

            $userdata = array();
            $serviceplans = array();
            $limits = array();
            $parents = Users::findFirstById($user['parent']);
            $parent_data = $parents->toArray();


            $userinfo = Userdata::findFirst("user_id = '$user_id'");
            if($userinfo) {
                $userdata = $userinfo->toArray();
            }
            $client_limit_permission = ClientLimitsPermissions::findFirst("client_id = '$user_id'");
            if($client_limit_permission) {
                $limits = $client_limit_permission->toArray();
            }
            $sp = Serviceplans::find("client_id = '$user_id'");
            if($sp) {
                $serviceplans = $sp->toArray();
            }

            return array(
                'users' => $user,
                'parents' => $parent_data,
                'userdata' => $userdata,
                'client_limits_permission' => $limits,
                'serviceplans' => $serviceplans,
            );
        }
        else {
            return false;
        }
    }

    public function createDomainData($data)
    {
        $domain_name = $data['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if($domains) {
            $domain = $domains->toArray();
            $domain_id = $domain['id'];
            $user = array();
            $userdata = array();
            $limits = array();
            $parent = array();
            $users = Users::findFirst("username = '$domain_name'");
            if($users) {
                $user = $users->toArray();
                $user_id = $user['id'];
                $userinfo = Userdata::findFirst("user_id = '$user_id'");
                if($userinfo) {
                    $userdata = $userinfo->toArray();
                }
                $client_limit_permission = ClientLimitsPermissions::findFirst("domain_id = '$domain_id'");
                if($client_limit_permission) {
                    $limits = $client_limit_permission->toArray();
                }
                $parents = Users::findFirstById($domain['client_id']);
                $parent = $parents->toArray();
            }
            return array(
                'domains' => $domain,
                'users' => $user,
                'parent' => $parent,
                'userdata' => $userdata,
                'client_limits_permission' => $limits,
                'databases' => $this->getDomainDatabases($domain_id),
                'dns' => $this->getDomainDNS($domain_id),
                'domain_alias' => $this->getDomainAlias($domain_id),
                'ftpusers' => $this->getDomainFtpUsers($domain['ftp_user_id']),
                'mail_params' => $this->getDomainMailParams($domain_id),
                'mails' => $this->getDomainMails($domain_id),
                'protect_dirs' => $this->getDomainProtectDirs($domain_id),
                'scheduled_tasks' => $this->getDomainScheduledTasks($domain_id),
                'subdomains' => $this->getDomainSubdomains($domain_id),
                'certificates' => $this->getDomainCertificates($domain_id),
            );
        }
        else {
            return false;
        }
    }

    private function getDomainDatabases($domain_id): array
    {
        $database_list = array();
        $databases = Databases::find("domain_id = '$domain_id'");
        if($databases) {
            foreach($databases->toArray() as $database) {
                $database_servers = DatabaseServer::findFirstById($database['database_server_id']);
                $database_uid = $database['database_user_id'];
                $database_user = DatabaseUsers::findFirstById($database_uid);
                $database_list[] = array(
                    'name' => $database['name'],
                    'type'  => $database['type'],
                    'username'  => $database_user->login,
                    'password'  => $database_user->password,
                    'host'      => $database_servers->host,
                );
            }
        }
        return $database_list;
    }

    private function getDomainDNS($domain_id): array
    {
        $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
        if($dns_zone) {
            $dns_zone_id = $dns_zone->id;
            $dns_records = DnsRecords::find("dns_zone_id = '$dns_zone_id'");
            if($dns_records) {
                return $dns_records->toArray();
            }
        }
        return array();
    }

    private function getDomainAlias($domain_id): array
    {
        $domain_alias = DomainAlias::find("domain_id = '$domain_id'");
        if($domain_alias) {
            return $domain_alias->toArray();
        }
        return array();
    }

    private function getDomainFtpUsers($ftp_user_id): array
    {
        $ftp_users = FtpUsers::findFirstById($ftp_user_id);
        if($ftp_users) {
            return $ftp_users->toArray();
        }
        return array();
    }

    private function getDomainMailParams($domain_id): array
    {
        $mail_params = MailSettings::findFirst("domain_id = '$domain_id'");
        if($mail_params) {
            return $mail_params->toArray();
        }
        return array();
    }

    private function getDomainMails($domain_id): array
    {
        $mails = Mails::find("domain_id = '$domain_id'");
        $mail_list = array();
        if($mails) {
            foreach($mails->toArray() as $mail) {
                $aliases = array();
                $mail_id = $mail['id'];
                $mail_alias = MailAlias::find("mail_id = '$mail_id'");
                if($mail_alias)
                    $aliases = $mail_alias->toArray();

                $mail_list[] = array(
                    'mail_name' => $mail['mail_name'],
                    'password' => $mail['password'],
                    'forward' => $mail['forward'],
                    'forward_addr' => $mail['forward_addr'],
                    'mail_group' => $mail['mail_group'],
                    'autoresponder' => $mail['autoresponder'],
                    'mailbox_quota' => $mail['mailbox_quota'],
                    'alias_list'    => $aliases,
                );
            }
        }
        return $mail_list;
    }

    private function getDomainProtectDirs($domain_id): array
    {
        $protect_dir_list = array();
        $pro_dirs = ProtectDirectory::find("domain_id = '$domain_id'");
        if($pro_dirs) {
            foreach($pro_dirs->toArray() as $dir) {
                $subdomain_name = "";

                $subdomain_id = $dir['subdomain_id'];
                if((!empty($subdomain_id)) || ($subdomain_id > "0")) {
                    $subdomains = Subdomains::findFirstById($subdomain_id);
                    if($subdomains) {
                        $subdomain_name = $subdomains->subdomain_name;
                    }
                }

                $pro_users = ProtectDirectoryUser::find("protect_dir_id = '$dir[id]'");

                $protect_dir_list[] = array(
                    'path' => $dir['path'],
                    'nonssl' => $dir['nonssl'],
                    'ssl' => $dir['ssl'],
                    'cgi_bin' => $dir['cgi_bin'],
                    'header' => $dir['header'],
                    'sub_domain_name' => $subdomain_name,
                    'users' => $pro_users->toArray(),
                );
            }
        }

        return $protect_dir_list;
    }

    private function getDomainScheduledTasks($domain_id): array
    {
        $scheduled_tasks = ScheduledTasks::find("domain_id = '$domain_id'");

        return $scheduled_tasks->toArray();
    }

    private function getDomainSubdomains($domain_id): array
    {
        $subdomain_lists = array();
        $subdomains = Subdomains::find("domain_id = '$domain_id'");
        if($subdomains) {
            foreach($subdomains->toArray() as $subdomain) {
                $ftp_users = FtpUsers::findFirstById($subdomain['ftpuser_id']);
                $ftp_username = "";
                $ftp_password = "";
                if($ftp_users) {
                    $ftp_username = $ftp_users->username;
                    $ftp_password = $ftp_users->password;
                }

                $subdomain_lists[] = array(
                    'subdomain_name' => $subdomain['subdomain_name'],
                    'full_name' => $subdomain['full_name'],
                    'ftpuser_type' => $subdomain['ftpuser_type'],
                    'created_date' => $subdomain['created_date'],
                    'ftp_username'          => $ftp_username,
                    'ftp_password'          => $ftp_password,
                    'ssl'               => $subdomain['ssl'],
                    'php'               => $subdomain['php'],
                    'php_version'       => $subdomain['php_version'],
                    'cgi'               => $subdomain['cgi'],
                    'perl'              => $subdomain['perl'],
                    'python'            => $subdomain['python'],
                    'fast_cgi'          => $subdomain['fast_cgi'],
                    'status'            => $subdomain['status'],
                    'asp'               => $subdomain['asp'],
                    'aspnet_core'       => $subdomain['aspnet_core'],
                    'aspnet'            => $subdomain['aspnet'],
                    'aspnet_version'    => $subdomain['aspnet_version'],
                    'dedicated_pool'    => $subdomain['dedicated_pool'],
                    'max_cpu'           => $subdomain['max_cpu'],
                );
            }
        }

        return $subdomain_lists;
    }

    private function getDomainCertificates($domain_id): array
    {
        $certificates = Certificates::find("domain_id = '$domain_id'");

        return $certificates->toArray();
    }

    private function getDomainData($domain_name, $ip_addr, $panel_type, $system_type, $migration_id, $username, $password, $skip = null)
    {
        $migrationmng = new \FPanel\Process\Migration();

        $params = array(
            'system' => $system_type,
            'panel' => $panel_type,
            'domain' => $domain_name,
            'ipaddr' => $ip_addr,
            'user' => $username,
            'passwd' => $password,
            'migration-id' => $migration_id,
            'skip' => $skip
        );
        $migrationmng->getDomainData($params);
    }
}