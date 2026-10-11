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
 */namespace FPanel\Menu;

use FPanel\Library\Library;
use FPanel\Models\IpAddress;

class Builder extends Library
{
    public $domain_data = array();
    public $client_data = array();

    protected $user_data;
    protected $user_type;
    protected $nested;
    protected $user_buttons = array();
    protected $domain_id;
    protected $client_id;
    protected $client_type;

    public function build($user_data, $client_type = null, $client_id = null, $nested = false): array
    {
        if ($nested) {
            $this->nested = $nested;
            $this->client_type = $client_type;
            if($client_type == DOMAIN_USER) {
                $this->domain_id = $client_id;
            }
            else {
                $this->client_id = $client_id;
            }
        }
        $this->user_data = $user_data;
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->generateButtons();
        $this->createButtons();
        $this->acl();

        return $this->user_buttons;
    }

    private function createButtons()
    {
        switch ($this->user_type) {
            case 6:
            case 0:
            case 1:
            case 2:
                break;
            case 3:
                unset($this->user_buttons['user-create']);
                unset($this->user_buttons['user-list']);
//                return $this->user_buttons;
                break;
            case 4:
                unset($this->user_buttons['template-list']);
                unset($this->user_buttons['template-create']);
                unset($this->user_buttons['user-list']);
                unset($this->user_buttons['user-create']);
                unset($this->user_buttons['domain-list']);
                unset($this->user_buttons['domain-create']);
                unset($this->user_buttons['client-backup-list']);
                unset($this->user_buttons['client-backup-create']);
                break;
        }

        if((isset($this->nested)) && $this->nested) {
            $this->user_buttons = $this->nestedClientButtons($this->user_buttons);
        }
        else {
            if($this->user_type == DOMAIN_USER) {
                $this->domain_data = $this->user_data;
                $this->user_buttons = $this->checkDomainServices($this->user_buttons);
            }
        }
//        return $this->user_buttons;
    }

    private function generateButtons()
    {
        $client_link = "";
        $template_count = "";
        $client_count = "";
        $domain_count = "";

        $sys_ip = IpAddress::findFirst("is_default = '1'");
        $default_ip = $sys_ip->ip_address;
        if(!empty($this->domain_data)) {
            $default_ip = $this->domain_data['user']['domain_info']['ip_addr']['ip_address'];
        }

        if($this->nested) {
            $user_type = $this->client_type;
            if(isset($this->client_id)) {
                $client_link = '/client/'.$this->client_id;
            }
            if(isset($this->domain_id)) {
                $client_link = '/domain/'.$this->domain_id;
            }
        }
        else {
            $user_type = $this->user_type;
        }

        if($this->nested) {
            if($this->client_type > ADMIN_USER) {
                $user_settings = $this->client_data;
                if($this->client_type != DOMAIN_USER) {
                    $template_count = $user_settings['user']['usage']['serviceplans'];
                    $domain_count = $user_settings['user']['usage']['domains'];
                    $client_count = $user_settings['user']['usage']['clients'];
                }
            }
        }
        else {
            if($this->user_type != DOMAIN_USER) {
                $template_count = $this->user_data['user']['usage']['serviceplans'];
                $domain_count = $this->user_data['user']['usage']['domains'];
                $client_count = $this->user_data['user']['usage']['clients'];
            }
        }
        $subdomain_count = "";
        $database_count = "";
        $domain_alias_count = "";
        $domain_mails_count = "";
        if($user_type == DOMAIN_USER) {
            if($this->nested) {
                $subdomain_count = $this->domain_data['user']['usage']['subdomains'];
                $database_count = $this->domain_data['user']['usage']['database'];
                $domain_alias_count = $this->domain_data['user']['usage']['domainalias'];
                $domain_mails_count = $this->domain_data['user']['usage']['domainemail'];
            }
            else {
                $subdomain_count = $this->user_data['user']['usage']['subdomains'];
                $database_count = $this->user_data['user']['usage']['database'];
                $domain_alias_count = $this->user_data['user']['usage']['domainalias'];
                $domain_mails_count = $this->user_data['user']['usage']['domainemail'];
            }
        }

        $buttons = array(
            'template-list' => array(
                'name' => __('Serviceplans'),
                'link' => $this->uri->url_encrypt('serviceplans'.$client_link),
                'count' => $template_count,
                'access' => 'template',
            ),
            'template-create' => array(
                'name' => __('Create Serviceplan'),
                'link' => $this->uri->url_encrypt('serviceplans/create'.$client_link),
                'count' => '',
                'access' => 'template',
            ),
            'user-list' => array(
                'name' => __('Users'),
                'link' => $this->uri->url_encrypt('users'.$client_link),
                'count' => $client_count,
                'access' => 'users',
            ),
            'user-create' => array(
                'name' => __('Create User'),
                'link' => $this->uri->url_encrypt('users/create'.$client_link),
                'count' => '',
                'access' => 'users',
            ),
            'domain-list' => array(
                'name' => __('Domains'),
                'link' => $this->uri->url_encrypt('domains'.$client_link),
                'count' => $domain_count,
                'access' => 'domains',
            ),
            'domain-create' => array(
                'name' => __('Create Domain'),
                'link' => $this->uri->url_encrypt('domains/create'.$client_link),
                'count' => '',
                'access' => 'domains',
            ),
            'client-backup-list' => array(
                'name' => __('Backup Manager'),
                'link' => $this->uri->url_encrypt('backupclient'.$client_link),
                'count' => '',
                'access' => 'backup',
            ),
            'client-backup-create' => array(
                'name' => __('Create Backup'),
                'link' => $this->uri->url_encrypt('backupclient/create'.$client_link),
                'count' => '',
                'access' => 'backup',
            ),

            // Domain İçin
            'web_site_settings' => array(
                'name' => __('Web Site Settings'),
                'link' => $this->uri->url_encrypt('webhosting'.$client_link),
                'count' => '',
                'access' => 'hosting',
            ),
            'php_settings' => array(
                'name' => __('PHP Settings'),
                'link' => $this->uri->url_encrypt('php'.$client_link),
                'count' => '',
                'access' => 'hosting',
            ),
            'asp_net_settings' => array(
                'name' => __('ASP.NET Settings'),
                'link' => $this->uri->url_encrypt('aspnet'.$client_link),
                'count' => '',
                'access' => 'hosting',
            ),
            'subdomains' => array(
                'name' => __('Subdomains'),
                'link' => $this->uri->url_encrypt('subdomain'.$client_link),
                'count' => $subdomain_count,
                'access' => 'subdomains',
            ),
            'databases' => array(
                'name' => __('Databases'),
                'link' => $this->uri->url_encrypt('database'.$client_link),
                'count' => $database_count,
                'access' => 'database',
            ),
            'domain-dns-settings' => array(
                'name' => __('DNS Settings'),
                'link' => $this->uri->url_encrypt('dns'.$client_link),
                'count' => '',
                'access' => 'dns',
            ),
            'domain_alias' => array(
                'name' => __('Domain Alias'),
                'link' => $this->uri->url_encrypt('domainalias'.$client_link),
                'count' => $domain_alias_count,
                'access' => 'domain-alias',
            ),
            'ssl_certificates' => array(
                'name' => __('SSL Certificates'),
                'link' => $this->uri->url_encrypt('certificate'.$client_link),
                'count' => '',
                'access' => 'ssl',
            ),
            'protect-directory' => array(
                'name' => __('Protect Directory Settings'),
                'link' => $this->uri->url_encrypt('protectdir'.$client_link),
                'count' => '',
                'access' => 'protect-dir',
            ),
            'file-manager' => array(
                'name' => __('File Manager'),
                'link' => $this->uri->url_encrypt('filemanager'.$client_link),
                'count' => '',
                'access' => 'file-manager',
            ),
            'domain-backup-manager' => array(
                'name' => __('Backup Manager'),
                'link' => $this->uri->url_encrypt('backup'.$client_link),
                'count' => '',
                'access' => 'backup',
            ),
            'virtual-directory-manager' => array(
                'name' => __('Virtual Directory Settings'),
                'link' => $this->uri->url_encrypt('vdir'.$client_link),
                'count' => '',
                'access' => 'virtual-dir',
            ),
            'domain-mail-settings' => array(
                'name' => __('Mail Settings'),
                'link' => $this->uri->url_encrypt('mail/mailsettings'.$client_link),
                'count' => '',
                'access' => 'mail',
            ),
            'domain-mail-accounts' => array(
                'name' => __('Mail Accounts'),
                'link' => $this->uri->url_encrypt('mail/accounts'.$client_link),
                'count' => $domain_mails_count,
                'access' => 'mail',
            ),
            'domain-mail-account-create' => array(
                'name' => __('Create Mail Account'),
                'link' => $this->uri->url_encrypt('mail/create'.$client_link),
                'count' => '',
                'access' => 'mail',
            ),

            'domain-webmail' => array(
                'name' => __('Webmail'),
                'target' => '_blank',
                'link' => 'http://'. $default_ip . '/webmail',
                'count' => '',
                'access' => 'mail',
            ),

            'domain-cron' => array(
                'name' => __('Scheduled Task Management'),
                'link' => $this->uri->url_encrypt('tasks'.$client_link),
                'count' => '',
                'access' => 'cron',
            ),
            'domain-resource-viewer' => array(
                'name' => __('Resource Viewer'),
                'link' => $this->uri->url_encrypt('resources'.$client_link),
                'count' => '',
                'access' => '',
            ),
            'domain-web-statistics' => array(
                'name' => __('Web Statistics'),
                'link' => $this->uri->url_encrypt('webstatistics'.$client_link),
                'count' => '',
                'access' => 'statistics',
            ),
        );

        if($user_type == DOMAIN_USER) {
            // ASP.NET Settings
            if(PANEL_OS_TYPE == WINDOWS) {
                if($this->nested) {
                    $domain_settings = $this->domain_data['user']['domain_settings'];
                }
                else {
                    $domain_settings = $this->user_data['user']['domain_settings'];
                }
                if(!$domain_settings['aspnet']) {
                    $buttons['asp_net_settings']['link'] = '#';
                    $buttons['asp_net_settings']['count'] = 'X';
                }
            }

            // PHP Settings
            if($this->nested) {
                $domain_settings = $this->domain_data['user']['domain_settings'];
            }
            else {
                $domain_settings = $this->user_data['user']['domain_settings'];
            }
            if(!$domain_settings['php']) {
                $buttons['php_settings']['link'] = '#';
                $buttons['php_settings']['count'] = 'X';
            }
        }
        $this->user_buttons = $buttons;
    }

    public function nestedClientButtons($buttons)
    {
        $licence_limit = $this->user_data['system_info']['licence'];
        switch($this->client_type) {
            case DOMAIN_USER:
                $buttons = $this->checkDomainServices($buttons);
                break;
            case CLIENT_USER:
                unset($buttons['user-create']);
                unset($buttons['user-list']);
                break;
            case RESELLER_USER:
            case ADMIN_USER:
                break;
        }
        if($this->client_type != DOMAIN_USER) {
            if($licence_limit['domain_limit_exceeded']) {
                $buttons['domain-create']['link'] = "#";
                $buttons['domain-create']['count'] = "x";
            }
            if($licence_limit['client_limit_exceeded']) {
                $buttons['user-create']['link'] = "#";
                $buttons['user-create']['count'] = "x";
            }
        }
        return $buttons;
    }

    private function checkDomainServices($buttons): array
    {
        $data = $this->domain_data;
        if($data['user']['domain_info']['web_service'] != ACTIVE) {
            $buttons['web_site_settings']['link'] = "#";
            $buttons['web_site_settings']['count'] = "x";
            $buttons['subdomains']['link'] = "#";
            $buttons['subdomains']['count'] = "x";
            $buttons['domain_alias']['link'] = "#";
            $buttons['domain_alias']['count'] = "x";

            $buttons['ssl_certificates']['link'] = "#";
            $buttons['ssl_certificates']['count'] = "x";

            $buttons['web_site_preview']['link'] = "#";
            $buttons['web_site_preview']['count'] = "x";

            $buttons['file-manager']['link'] = "#";
            $buttons['file-manager']['count'] = "x";

            $buttons['protect-directory']['link'] = "#";
            $buttons['protect-directory']['count'] = "x";

            $buttons['domain-cron']['link'] = "#";
            $buttons['domain-cron']['count'] = "x";

            $buttons['domain-show-logs']['link'] = "#";
            $buttons['domain-show-logs']['count'] = "x";

            $buttons['domain-web-statistics']['link'] = "#";
            $buttons['domain-web-statistics']['count'] = "x";
        }

        if($data['user']['domain_info']['dns_service'] != ACTIVE) {
            $buttons['domain-dns-settings']['link'] = "#";
            $buttons['domain-dns-settings']['count'] = "x";
        }

        if($data['user']['domain_info']['mail_service'] != ACTIVE) {
            $buttons['domain-mail-settings']['link'] = "#";
            $buttons['domain-mail-settings']['count'] = "x";

            $buttons['domain-mail-accounts']['link'] = "#";
            $buttons['domain-mail-accounts']['count'] = "x";

            $buttons['domain-mail-account-create']['link'] = "#";
            $buttons['domain-mail-account-create']['count'] = "x";
        }

        if(($data['user']['domain_info']['web_service'] != ACTIVE) && ($data['user']['domain_info']['mail_service'] != ACTIVE) && ($data['user']['domain_info']['dns_service'] != ACTIVE)) {
            $buttons['domain-dns-settings']['link'] = "#";
            $buttons['domain-dns-settings']['count'] = "x";
        }
        return $buttons;
    }

    private function acl()
    {
        $licence_limit = $this->user_data['system_info']['licence'];

        $process_array = array(
            // Process => Permission,Limit
            // Reseller-Client için olan bölüm başlıyor
            'template'              => array('limit' => 11111111,'permission' => 'man_template'),
            'users'                 => array('limit' => 11111111,'permission' => 'man_client'),
            'domains'               => array('limit' => 11111111,'permission' => 'man_domain'),
            // Domain için olan bölüm başlıyor
            'hosting'               => array('limit' => 11111111,'permission' => 'man_hosting_settings'),
            'subdomains'            => array('limit' => 11111111,'permission' => 'man_subdomain'),
            'database'              => array('limit' => 11111111,'permission' => 'man_database'),
            'dns'                   => array('limit' => 11111111,'permission' => 'man_dns'),
            'domain-alias'          => array('limit' => 11111111,'permission' => 'man_domain_alias'),
            'ssl'                   => array('limit' => 11111111,'permission' => 'man_ssl'),
            'protect-dir'           => array('limit' => 11111111,'permission' => 'man_protect_dir'),
            'backup'                => array('limit' => 11111111,'permission' => 'man_backup'),
            'mail'                  => array('limit' => 11111111,'permission' => 'man_email'),
            'cron'                  => array('limit' => 11111111,'permission' => 'man_task'),
        );

        $user_permissions = $this->user_data['user']['permissions'];
        $user_domain_settings = $this->user_data['user']['domain_settings'];
        $user_remaining_rights = $this->user_data['user']['remaining'];
        $user_buttons = $this->user_buttons;

        if(!isset($this->nested)) {
            if($this->user_type == DOMAIN_USER) {
                if(($user_remaining_rights['domainemail'] != UNLIMITED) && ($user_remaining_rights['domainemail'] < 1)) {
                    $user_buttons["domain-mail-account-create"]['link'] = "#";
                }

                foreach ($user_buttons as $menu_items => $item) {
                    if($item['access'] == "php_settings") {
                        if(!$user_domain_settings['php']) {
                            $user_buttons[$menu_items]['link'] = "#";
                            $user_buttons[$menu_items]['count'] = "X";
                        }
                    }

                    if(array_key_exists($item['access'], $process_array)) {
                        if($process_array[$item['access']]['permission'] != null) {
                            if($user_permissions[$process_array[$item['access']]['permission']] != ALLOWED) {
                                $user_buttons[$menu_items]['link'] = "#";
                                $user_buttons[$menu_items]['count'] = "X";
                            }
                        }

                    }
                }
            }
            else {
                if($licence_limit['domain_limit_exceeded']) {
                    $user_buttons['domain-create']['link'] = "#";
                    $user_buttons['domain-create']['count'] = "x";
                }
                if($licence_limit['client_limit_exceeded']) {
                    $user_buttons['user-create']['link'] = "#";
                    $user_buttons['user-create']['count'] = "x";
                }

            }
        }
        $this->user_buttons = $user_buttons;
    }
}