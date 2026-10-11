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
 */namespace FPanel\Acl;

use Phalcon\Mvc\User\Component;

class Builder extends Component
{
    use Domain;
    use Clients;
    use Resellers;
    use Administrator;
    use SupportUser;

    private $identity;
    private $user_id;
    private $user_type;
    private $domain_name;
    private $generated_sql;
    private $result_query;

    private $user_info;
    private $user_data;
    private $usage;
    private $limits;
    private $permissions;
    private $domain_settings;
    private $user_statistics;
    private $remaining_rights;
    private $domain_info;
    private $ip_address;
    private $system_info;

    public function user(array $identity, $system_info = null): Builder
    {
        $this->identity = $identity;
        $this->user_type = $identity['type'];
        $this->user_id = $identity['id'];
        if($this->user_type == DOMAIN_USER) {
            $this->domain_name = $identity['username'];
        }

        $this->system_info = $system_info;
        return $this;
    }

    public function execute(): Builder
    {

        if(REDIS_STATUS) {
            if(!$this->redis->hExists("Users", $this->user_id)) {
                $this->generate($this->user_type, $this->user_id);
                $result_query = $this->db->fetchOne("$this->generated_sql");
                $this->redis->hSet("Users", $this->user_id,json_encode($result_query));
            }
            else {
                $result_query = $this->redis->hGet("Users", $this->user_id);
                if($result_query == "false") {
                    $this->generate($this->user_type, $this->user_id);
                    $result_query = $this->db->fetchOne("$this->generated_sql");
                    $this->redis->hSet("Users", $this->user_id, json_encode($result_query));
                }
                else {
                    $result_query = json_decode($result_query,true);
                }
            }
        }
        else {
            $this->generate($this->user_type, $this->user_id);
            $result_query = $this->db->fetchOne("$this->generated_sql");
        }

        if(!$result_query) {
            $this->result_query = false;
            return $this;
        }

        $user_data = array();
        foreach ($result_query as $key => $value) {
            if(!is_numeric($key)) {
                $user_data[$key] = $value;
            }
        }

        if(!empty($this->identity['parent'])) {
            if((isset($this->identity['parent']['id'])) && ($this->identity['parent']['id'] != ADMIN_USER)) {
                $this->generate($this->identity['parent']['type'], $this->identity['parent']['id']);
                $result_query1 = $this->db->fetchOne("$this->generated_sql");
                foreach ($result_query1 as $key => $value) {
                    if(!is_numeric($key)) {
                        $user_data['parent'][$key] = $value;
                    }
                }
                if(!empty($this->identity['parent']['parent'])) {
                    if(($this->identity['parent']['parent']['type'] != ROOT_USER) && ($this->identity['parent']['parent']['id'] != null)) {
                        $this->generate($this->identity['parent']['parent']['type'], $this->identity['parent']['parent']['id']);
                        $result_query2 = $this->db->fetchOne("$this->generated_sql");
                        foreach ($result_query2 as $key => $value) {
                            if(!is_numeric($key)) {
                                $user_data['parent']['parent'][$key] = $value;
                            }
                        }
                    }
                }
            }
            $user_data['parent']['type'] = $this->identity["parent"]["type"];
        }

        $this->result_query = $user_data;
        return $this;
    }

    public function build()
    {
        if(!$this->result_query) {
            $this->response->setStatusCode(403, "Forbidden");
            $this->view->setVar('error_content', __("User data not found"));
            $this->view->pick("errors/403");
            return false;
        }
        if($this->user_type == DOMAIN_USER) {
            $this->buildDomainInfo();
        }
        else {
            $this->buildIpAddress();
        }


        return $this->buildInfo()
            ->buildUserData()
            ->buildUsage()
            ->buildLimits()
            ->buildPermissions()
            ->buildDomainSettings()
            ->buildStatistics()
            ->buildRemainingRights()
            ->calculate()
            ->results();
    }

    private function buildInfo(): Builder
    {
        if($this->user_type != SUPPORT_USER) {
            $user_info_data = array(
                'fullname' => $this->result_query['fullname'],
                'email' => $this->result_query['email'],
                'company' => $this->result_query['company'],
                'fax' => $this->result_query['fax'],
                'telephone' => $this->result_query['telephone'],
                'address' => $this->result_query['address'],
                'city' => $this->result_query['city'],
                'region' => $this->result_query['region'],
                'zipcode' => $this->result_query['zipcode'],
                'country' => $this->result_query['country'],
            );
        }
        else {
            $user_info_data = array(
                'fullname'  => "",
                'email'  => "",
                'company'  =>"",
                'fax'  => "",
                'telephone'  => "",
                'address'  => "",
                'city'  => "",
                'region'  => "",
                'zipcode'  => "",
                'country'  => "",
            );
        }
        $this->user_info = $user_info_data;
        return $this;
    }

    private function buildUserData(): Builder
    {

        if($this->result_query['changed_data'] != null)
            $changed_data = json_decode($this->result_query['changed_data'],true);
        else
            $changed_data = array();
        $user_data = array(
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
            'username' => $this->result_query['username'],
            'last_login' => $this->result_query['last_login'],
            'last_login_ip' => $this->result_query['last_login_ip'],
            'parent_id' => $this->result_query['parent_id'],
            'must_change_password' => $this->result_query['must_change_password'],
            'created_date' => $this->result_query['created_date'],
            'expire_date' => $this->result_query['expire_date'],
            'changed_data' => $changed_data,
            'status' => "1",
            'lang' => $this->result_query['language'],
            'status_msg' => $this->result_query['status_msg'],
        );
//        $users = Users::findFirstById($this->user_id);
//        $user_data["last_login"] = $users->last_login;
//        $user_data["last_login_ip"] = $users->last_login_ip;
        if(($this->identity['type'] != ADMIN_USER) && ($this->identity['type'] != ROOT_USER) && ($this->identity['type'] != SUPPORT_USER)) {
            $user_data['status'] = $this->result_query['statistic_status'];
        }
        $this->user_data = $user_data;
        return $this;
    }

    private function buildUsage(): Builder
    {
        $usage = array();
        switch($this->user_type) {
            case SUPPORT_USER:
            case ADMIN_USER:
            case ROOT_USER:
                $usage['reseller_count'] = $this->result_query['used_resellers'];
                $usage['clients'] = $this->result_query['used_clients'];
                $usage['client_count'] = $this->result_query['used_clients'] + $this->result_query['used_resellers'];
                $usage['domains'] = $this->result_query['used_domains'];
                $usage['serviceplans'] = $this->result_query['used_templates'];
                break;
            case RESELLER_USER:
                $usage['clients'] = $this->result_query['used_clients'];
                $usage['domains'] = $this->result_query['used_domains'];
                $usage['serviceplans'] = $this->result_query['used_templates'];
                $usage['subdomains'] = $this->result_query['used_subdomains'];
                $usage['domainalias'] = $this->result_query['used_domain_alias'];
                $usage['domainemail'] = $this->result_query['used_email'];
                $usage['database'] = $this->result_query['used_database'];
                $usage['email_alias'] = $this->result_query['used_email_alias'];
                break;
            case CLIENT_USER:
                $usage['clients'] = "0";
                $usage['domains'] = $this->result_query['used_domains'];
                $usage['serviceplans'] = $this->result_query['used_templates'];
                $usage['subdomains'] = $this->result_query['used_subdomains'];
                $usage['domainalias'] = $this->result_query['used_domain_alias'];
                $usage['domainemail'] = $this->result_query['used_email'];
                $usage['database'] = $this->result_query['used_database'];
                $usage['email_alias'] = $this->result_query['used_email_alias'];
                break;
            case DOMAIN_USER:
                $usage['clients'] = "0";
                $usage['domains'] = "0";
                $usage['serviceplans'] = "0";
                $usage['subdomains'] = $this->result_query['used_subdomains'];
                $usage['domainalias'] = $this->result_query['used_domain_alias'];
                $usage['domainemail'] = $this->result_query['used_email'];
                $usage['database'] = $this->result_query['used_database'];
                $usage['email_alias'] = $this->result_query['used_email_alias'];
                break;
        }

        $this->usage = $usage;

        return $this;
    }

    private function buildLimits(): Builder
    {
        if(($this->user_type > ADMIN_USER) && ($this->user_type < SUPPORT_USER)) {
            $limits = array(
                'client_limit' => $this->result_query['client_limit'],
                'domain_limit' => $this->result_query['domain_limit'],
                'disk_limit' => $this->result_query['disk_limit'],
                'traffic_limit' => $this->result_query['traffic_limit'],
                'subdomain_limit' => $this->result_query['subdomain_limit'],
                'domain_alias_limit' => $this->result_query['domain_alias_limit'],
                'email_box_limit' => $this->result_query['email_box_limit'],
                'email_box_size_limit' => $this->result_query['email_box_size_limit'],
                'email_forward_limit' => $this->result_query['email_forward_limit'],
                'email_groups_limit' => $this->result_query['email_groups_limit'],
                'email_autoresponder_limit' => $this->result_query['email_autoresponder_limit'],
                'email_lists_limit' => $this->result_query['email_lists_limit'],
                'database_limit' => $this->result_query['database_limit'],
                'database_size_limit' => $this->result_query['database_size_limit'],
                'template_limit' => $this->result_query['template_limit'],
                'overuse' => $this->result_query['overuse']
            );
        }
        else {
            $limits = array(
                'client_limit' => UNLIMITED,
                'domain_limit' => UNLIMITED,
                'disk_limit' => UNLIMITED,
                'traffic_limit' => UNLIMITED,
                'subdomain_limit' => UNLIMITED,
                'domain_alias_limit' => UNLIMITED,
                'email_box_limit' => UNLIMITED,
                'email_box_size_limit' => UNLIMITED,
                'email_forward_limit' => UNLIMITED,
                'email_groups_limit' => UNLIMITED,
                'email_autoresponder_limit' => UNLIMITED,
                'email_lists_limit' => UNLIMITED,
                'database_limit' => UNLIMITED,
                'database_size_limit' => UNLIMITED,
                'template_limit' => UNLIMITED,
                'overuse' => PASSIVE,
            );
        }
        $this->limits = $limits;
        return $this;
    }

    private function buildPermissions(): Builder
    {
        if(($this->user_type > ADMIN_USER) && ($this->user_type < SUPPORT_USER)) {
            $permissions = array();
            $perm_array = array(
                'access_panel',
                'man_client',
                'man_domain',
                'man_subdomain',
                'man_domain_alias',
                'man_email_alias',
                'man_hosting_settings',
                'man_task',
                'man_dns',
                'man_mailing_lists',
                'man_spam_filters',
                'man_backup',
                'man_email',
                'man_database',
                'man_protect_dir',
                'man_ssl',
                'man_template',
            );

            foreach($perm_array as $perm) {
                $data_perm = $this->result_query[$perm];
                if(!empty($this->result_query['parent']) && $this->result_query['parent']["type"] != ROOT_USER) {
                    if($this->result_query['parent'][$perm] == NOT_ALLOWED) {
                        $data_perm = NOT_ALLOWED;
                    }
                    else {
                        if(!empty($this->result_query['parent']['parent']))
                            if($this->result_query['parent']['parent'][$perm] == NOT_ALLOWED)
                                $data_perm = NOT_ALLOWED;
                    }
                }
                $permissions[$perm] = $data_perm;
            }
        }
        else {
            $permissions = array(
                'access_panel' => ALLOWED,
                'man_client' => ALLOWED,
                'man_domain' => ALLOWED,
                'man_subdomain' => ALLOWED,
                'man_domain_alias' => ALLOWED,
                'man_email_alias' => ALLOWED,
                'man_hosting_settings' => ALLOWED,
                'man_task' => ALLOWED,
                'man_dns' => ALLOWED,
                'man_mailing_lists' => ALLOWED,
                'man_spam_filters' => ALLOWED,
                'man_backup' => ALLOWED,
                'man_email' => ALLOWED,
                'man_database' => ALLOWED,
                'man_protect_dir' => ALLOWED,
                'man_ssl' => ALLOWED,
                'man_template' => ALLOWED,
            );
        }
        $this->permissions = $permissions;

        return $this;
    }

    private function buildDomainSettings(): Builder
    {
        $domain_settings = array();
        $settings = array(
            'php',
            'cgi',
            'perl',
            'python',
            'fast_cgi',
            'statistic_support',
            'asp',
            'aspnet_core',
            'aspnet',
            'dedicated_pool',
        );

        //@Fix
        if(($this->user_type > ADMIN_USER) && ($this->user_type < SUPPORT_USER)) {

            if(!empty($this->result_query['server_id']))
                $server_id = $this->result_query['server_id'];
            else
                $server_id = "1";

            $domain_settings = array(
                'server_id' => $server_id,
                'php_version' => $this->result_query['php_version'],
                'aspnet_version' => $this->result_query['aspnet_version'],
                'max_cpu' => $this->result_query['max_cpu'],
                'application_pool_mode' => $this->result_query['application_pool_mode'],
            );
            foreach($settings as $setting) {
                $dom_setting = $this->result_query[$setting];
                if(!empty($this->result_query['parent']) && $this->result_query['parent']["type"] != ROOT_USER) {
                    if($this->result_query['parent'][$setting] == NOT_ALLOWED) {
                        $dom_setting = NOT_ALLOWED;
                    }
                    else {
                        if(!empty($this->result_query['parent']['parent']))
                            if($this->result_query['parent']['parent'][$setting] == NOT_ALLOWED)
                                $dom_setting = NOT_ALLOWED;
                    }
                }
                $domain_settings[$setting] = $dom_setting;
            }
        }
        $this->domain_settings = $domain_settings;
        return $this;
    }

    private function buildStatistics(): Builder
    {
        $statistics = array();
        if(($this->user_type > ADMIN_USER) && ($this->user_type < SUPPORT_USER)) {
            $statistics = array(
                'mail' => $this->result_query['mail'],
                'db' => $this->result_query['db'],
                'httpdoc' => $this->result_query['httpdoc'],
                'disk' => $this->result_query['disk'],
                'traffic' => $this->result_query['traffic'],
                'logs' => $this->result_query['logs'],
            );
        }
        $this->user_statistics = $statistics;

        return $this;
    }

    private function buildRemainingRights(): Builder
    {
        if(($this->user_type > ADMIN_USER) && ($this->user_type < SUPPORT_USER)) {

            $remaining_data = array();
            $limits_array = array(
                'client_limit' => 'clients',
                'domain_limit' => 'domains',
                'template_limit' => 'serviceplans',
                'subdomain_limit' => 'subdomains',
                'domain_alias_limit' => 'domainalias',
                'email_box_limit' => 'domainemail',
                'database_limit' => 'database',
            );
            $this->debug->firebug([
                $this->result_query,
                $this->usage,
            ]);
            foreach($limits_array as $key => $value) {

                if ($this->result_query[$key] == "0") {
                    $right = "0";
                }
                else {
                    if(!empty($this->identity['parent'])) {
                        if(($this->identity['parent']['type'] != ROOT_USER) && ($this->identity['parent']['id'] != null)) {
                            if(($this->result_query['parent'][$key] != UNLIMITED) && ($this->result_query['parent'][$key] < $this->result_query[$key])) {
                                if($value == "serviceplans") $value = "templates";
                                if($value == "domainalias") $value = "domain_alias";
                                if($value == "domainemail") $value = "email";
                                $right = $this->result_query['parent'][$key] - $this->result_query['used_'.$value];
                                if($value == "templates") $value = "serviceplans";
                                if($value == "domain_alias") $value = "domainalias";
                                if($value == "email") $value = "domainemail";
                            }
                            else {

                                if(!empty($this->identity['parent']['parent'])) {
                                    if(($this->identity['parent']['parent']['type'] != ROOT_USER) && ($this->identity['parent']['parent']['id'] != null)) {
                                        if($this->result_query['parent']['parent'][$key] < $this->result_query[$key]) {
                                            $right = $this->result_query['parent']['parent'][$key] - $this->usage[$value];
                                        }
                                        else {
                                            $right = $this->result_query[$key] - $this->usage[$value];
                                        }
                                    }
                                    else {
                                        $right = $this->result_query[$key] - $this->usage[$value];
                                    }
                                }
                                else {
                                    if($this->result_query[$key] != UNLIMITED) {
                                        $right = $this->result_query[$key] - $this->usage[$value];
                                    }
                                    else
                                        $right = UNLIMITED;
                                }
                            }

                        }
                        else {
                            if($this->result_query[$key] != UNLIMITED) {
                                $right = $this->result_query[$key] - $this->usage[$value];
                            }
                            else
                                $right = UNLIMITED;
                        }
                    }
                    else {
                        $this->debug->firebug([$key, $value]);
                        if($this->result_query[$key] == UNLIMITED) {
                            $right = UNLIMITED;
                        }
                        else {
                            $right = $this->result_query[$key] - $this->usage[$value];
                        }
                    }
                }

                $remaining_data[$value] = $right;
            }

            if($this->user_type == RESELLER_USER) {

                if(is_numeric($this->system_info['licence']['client']))
                    $licence_remaining_client = $this->system_info['licence']['client'] - $this->result_query['total_clients'];
                else
                    $licence_remaining_client = 9999999999 - $this->result_query['total_clients'];
                if(is_numeric($this->system_info['licence']['domain']))
                    $licence_remaining_domain = $this->system_info['licence']['domain'] - $this->result_query['total_domains'];
                else
                    $licence_remaining_domain = 9999999999 - $this->result_query['total_domains'];


                if($licence_remaining_client < $remaining_data['clients'])
                    $remaining_data['clients'] = $licence_remaining_client;
                if($licence_remaining_domain < $remaining_data['domains'])
                    $remaining_data['domains'] = $licence_remaining_domain;
            }

            if($this->user_type == CLIENT_USER) {
                $licence_remaining_domain = $this->system_info['licence']['domain'] - $this->result_query['total_domains'];
                if($licence_remaining_domain < $remaining_data['domains'])
                    $remaining_data['domains'] = $licence_remaining_domain;
            }
            $this->remaining_rights = $remaining_data;
        }
        else {

            if(!empty($this->system_info['licence'])) {

                $licence_client_limit = $this->system_info['licence']['client'];
                $licence_domain_limit = $this->system_info['licence']['domain'];

                if(is_numeric($licence_client_limit))
                    $client_limit = $this->system_info['licence']['client'] - $this->result_query['used_clients'];
                else
                    $client_limit = 999999999;

                if(is_numeric($licence_domain_limit))
                    $domain_limit = $this->system_info['licence']['domain'] - $this->result_query['used_domains'];
                else
                    $domain_limit = 999999999;

                $this->remaining_rights = array(
                    'clients' => $client_limit,
                    'domains' => $domain_limit,
                    'serviceplans' => 999999999,
                    'subdomains' => 999999999,
                    'domainalias' => 999999999,
                    'domainemail' => 999999999,
                    'database' => 999999999,
                );
            }
        }
        return $this;
    }

    private function calculate(): Builder
    {
        if(($this->user_type > ADMIN_USER) && ($this->user_type < SUPPORT_USER)) {
            $limits = $this->limits;
            $used = $this->user_statistics;

            $percent_disk = '0';
            $percent_traffic = '0';
            $percent_db_size = '0';

            $used_traffic = $used['traffic'];
            $used_db_size = $used['db'];
            $used_disk = $used['disk'];

            if ($limits['traffic_limit'] != UNLIMITED) {
                $percent_traffic = round((($used_traffic / $limits['traffic_limit']) * 100));
            }

            if ($limits['disk_limit'] != UNLIMITED) {
                $percent_disk = round((($used_disk / $limits['disk_limit']) * 100));
            }

            if ($limits['database_size_limit'] != UNLIMITED) {
                $percent_db_size = round((($used_db_size / $limits['database_size_limit']) * 100));
            }

            if ($limits['disk_limit'] != UNLIMITED) {
                $remain_total_disk = $limits['disk_limit'] - $used_disk;
            }
            else {
                $remain_total_disk = UNLIMITED;
            }

            if ($limits['traffic_limit'] != UNLIMITED) {
                $remain_total_traffic = $limits['traffic_limit'] - $used_traffic;
            }
            else {
                $remain_total_traffic = UNLIMITED;
            }

            $statistics = array(
                'mail' => $this->user_statistics['mail'],
                'db' => $this->user_statistics['db'],
                'httpdoc' => $this->user_statistics['httpdoc'],
                'disk' => $this->user_statistics['disk'],
                'traffic' => $this->user_statistics['traffic'],
                'logs' => $this->user_statistics['logs'],
                'total_traffic' => $limits['traffic_limit'],
                'used_total_traffic' => $used_traffic,
                'remain_total_traffic' => $remain_total_traffic,
                'total_disk' => $limits['disk_limit'],
                'percent_traffic' => $percent_traffic,
                'percent_disk' => $percent_disk,
                'percent_db_size' => $percent_db_size,
                'used_total_disk' => $used_disk,
                'remain_total_disk' => $remain_total_disk,
            );
            $this->user_statistics = $statistics;

        }
        return $this;
    }

    private function buildDomainInfo()
    {
        $domain_info_data = array(
            'domain_id'  => $this->result_query['domain_id'],
            'web_server_id'  => $this->result_query['web_server_id'],
            'mail_server_id'  => $this->result_query['mail_server_id'],
            'dns_server_id'  => $this->result_query['dns_server_id'],
            'domain_name'  => $this->result_query['domain_name'],
            'ftp_user_id'  => $this->result_query['ftp_user_id'],
            'ip_addr'   => array('id' => $this->result_query['ipaddr_id'],'ip_address' => $this->result_query['ip_address']),
            'web_service' => $this->result_query['web_service'],
            'dns_service' => $this->result_query['dns_service'],
            'mail_service' => $this->result_query['mail_service'],
            'prefix' => $this->result_query['prefix'],
        );

        $this->domain_info = $domain_info_data;
    }

    private function buildIpAddress()
    {
        $this->ip_address = [];
        if(!empty($this->result_query['ip_adress_list'])) {
            $ip_address = explode(',', $this->result_query['ip_adress_list']);
            $ip_list = array();
            foreach ($ip_address as $ip) {
                $ipdata = explode(':', $ip);
                $ip_list[$ipdata['0']] = $ipdata['1'];
            }

            $this->ip_address = $ip_list;
        }
    }

    private function generate(int $user_type, string $user_id): Builder
    {
        switch($user_type) {
            case DOMAIN_USER: // Domain User
                $sql = $this->setDomainUser($user_id)
                    ->domainUser()
                    ->domainInfo()
                    ->domainLimits()
                    ->domainStatistics()
                    ->domainUsage()
                    ->domainSql();
                break;
            case CLIENT_USER: // Client User
                $sql = $this->setClientUser($user_id)
                    ->clientUser()
                    ->clientInfo()
                    ->clientLimits()
                    ->clientStatistics()
                    ->clientUsage()
                    ->clientSql();
                break;
            case RESELLER_USER: // Reseller User

                $sql = $this->setResellerUser($user_id)
                    ->resellerUser()
                    ->resellerInfo()
                    ->resellerLimits()
                    ->resellerStatistics()
                    ->resellerUsage()
                    ->resellerSql();

                break;
            case SUPPORT_USER: // Support User
                $sql = $this->supportUser()
                    ->sUserUsage()
                    ->sUserSql($user_id);
                break;
            default: // Root User
                $sql = $this->rootUser()
                    ->rootUsage()
                    ->rootSql($user_id);
                break;
        }
        $this->generated_sql = $sql;
        return $this;
    }

    private function results(): array
    {
        return array(
            'user'  => array(
                'info'              => array_merge($this->user_data, $this->user_info),
                'domain_info'       => $this->domain_info,
                'ip_address'        => $this->ip_address,
                'usage'             => $this->usage,
                'limits'            => $this->limits,
                'permissions'       => $this->permissions,
                'domain_settings'   => $this->domain_settings,
                'statistics'        => $this->user_statistics,
                'remaining'         => $this->remaining_rights,
            ),
        );
    }
}