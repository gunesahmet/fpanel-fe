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

/**
 * Class Builder
 * @package FPanel\Dashboard
 */
class BuilderDefault extends Library
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
        $this->nested = $nested;
        if ($nested == true) {
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
        if($this->user_type == CLIENT_USER) {
            unset($this->user_buttons['user-create']);
            unset($this->user_buttons['user-list']);
            return;
//            return $this->user_buttons;
        }

        if($this->nested == true) {
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
        if(!empty($this->domain_data)) {
            $default_ip = $this->domain_data['user']['domain_info']['ip_addr']['ip_address'];
        }
        $client_link = "";
        $template_count = "";
        $client_count = "";
        $domain_count = "";

        if($this->nested == true) {
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

        if(($this->nested == true) && ($this->client_type > ADMIN_USER)) {
            $user_settings = $this->client_data;
            if($this->client_type != DOMAIN_USER) {
                $template_count = $user_settings['user']['usage']['serviceplans'];
                $domain_count = $user_settings['user']['usage']['domains'];
                $client_count = $user_settings['user']['usage']['client_count'] ?? $user_settings['user']['usage']['clients'];
            }
        }
        else {
            if($this->user_type != DOMAIN_USER) {
                $template_count = $this->user_data['user']['usage']['serviceplans'];
                $domain_count = $this->user_data['user']['usage']['domains'];
                $client_count = $this->user_data['user']['usage']['client_count'] ?? $this->user_data['user']['usage']['clients'];
            }
        }

        if($user_type != DOMAIN_USER) {
            $this->user_buttons = array(
                'template-list' => array(
                    'icon' => '<i class="fp fp-folder-open"></i>',
                    'class' => '',
                    'name' => __('Serviceplans'),
                    'link' => $this->uri->url_encrypt('serviceplans'.$client_link),
                    'count' => $template_count,
                    'process' => 'template',
                ),
                'template-create' => array(
                    'icon' => '<i class="fp fp-folder-add"></i>',
                    'class' => '',
                    'name' => __('Create Serviceplan'),
                    'link' => $this->uri->url_encrypt('serviceplans/create'.$client_link),
                    'count' => '',
                    'process' => 'template_create',
                ),
                'user-list' => array(
                    'icon' => '<i class="fp fp-users"></i>',
                    'name' => __('Users'),
                    'class' => '',
                    'link' => $this->uri->url_encrypt('users'.$client_link),
                    'count' => $client_count,
                    'process' => 'user',
                ),
                'user-create' => array(
                    'icon' => '<i class="fp fp-user-plus"></i>',
                    'class' => '',
                    'name' => __('Create User'),
                    'link' => $this->uri->url_encrypt('users/create'.$client_link),
                    'count' => '',
                    'process' => 'user_create',
                ),
                'domain-list' => array(
                    'icon' => '<i class="fp fp-domain-alt-2"></i>',
                    'class' => '',
                    'name' => __('Domains'),
                    'link' => $this->uri->url_encrypt('domains'.$client_link),
                    'count' => $domain_count,
                    'process' => 'domain',
                ),
                'domain-create' => array(
                    'icon' => '<i class="fp fp-domainaddon"></i>',
                    'class' => '',
                    'name' => __('Create Domain'),
                    'count' => '',
                    'link' => $this->uri->url_encrypt('domains/create'.$client_link),
                    'process' => 'domain_create',
                ),
            );

            if($user_type > ADMIN_USER) {
                $this->user_buttons['backup-list'] = array(
                    "icon" => '<i class="fp fp-data-usage"></i>',
                    'name' => __('Backup Manager'),
                    'class' => '',
                    'link' => $this->uri->url_encrypt('backupclient'.$client_link),
                    'count' => '',
                    'process' => 'backup',
                );
                $this->user_buttons['backup-create'] =  array(
                    "icon" => '<i class="fp fp-server-download"></i>',
                    'name' => __('Create Backup'),
                    'class' => '',
                    'link' => $this->uri->url_encrypt('backupclient/create'.$client_link),
                    'count' => '',
                    'process' => 'backup',
                );
            }
        }
        else {
            if($this->nested == true) {
                $domain_name = $this->domain_data['user']['domain_info']['domain_name'];
                $subdomain_count = $this->domain_data['user']['usage']['subdomains'];
                $database_count = $this->domain_data['user']['usage']['database'];
                $domain_alias_count = $this->domain_data['user']['usage']['domainalias'];
                $domain_mails_count = $this->domain_data['user']['usage']['domainemail'];
            }
            else {
                $domain_name = $this->user_data['user']['info']['username'];
                $subdomain_count = $this->user_data['user']['usage']['subdomains'];
                $database_count = $this->user_data['user']['usage']['database'];
                $domain_alias_count = $this->user_data['user']['usage']['domainalias'];
                $domain_mails_count = $this->user_data['user']['usage']['domainemail'];
            }

            // Web Hosting Settings
            $web_hosting_settings_buttons = array(
                'web_site_settings' => array('name' => __('Web Site Settings'),
                    'class' => 'domain-dash secondary',
                    'icon' => '<i class="fp fp-domain-name"></i>',
                    'link' => $this->uri->url_encrypt('webhosting'.$client_link),
                    'process' => 'webhosting',
                    'count' => '',
                ),
                'asp_net_settings' => array('name' => __('ASP.NET Settings'),
                    'icon' => '<i class="fp fp-dotnet"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('aspnet'.$client_link),
                    'process' => 'aspnet_settings',
                    'count' => '',
                ),
                'subdomains' => array('name' => __('Subdomains'),
                    'icon' => '<i class="fp fp-subdomain"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('subdomain'.$client_link),
                    'count' => $subdomain_count,
                    'process' => 'subdomains',
                ),
                'databases' => array('name' => __('Databases'),
                    'icon' => '<i class="fp fp-database-alt-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>',
                    'class' => 'domain-dash primary',
                    'link' => $this->uri->url_encrypt('database'.$client_link),
                    'count' => $database_count,
                    'process' => 'database',
                ),
                'dns_settings' => array('name' => __('DNS Settings'),
                    'icon' => '<i class="fp fp-dns-alt"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('dns'.$client_link),
                    'count' => '',
                    'process' => 'dns',
                ),
                'domain_alias' => array('name' => __('Domain Alias'),
                    'icon' => '<i class="fp fp-redirect"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('domainalias'.$client_link),
                    'count' => $domain_alias_count,
                    'process' => 'domain_alias',
                ),
                'ssl_certificates' => array('name' => __('SSL Certificates'),
                    'icon' => '<i class="fp fp-ssl-certificated-alt"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('certificate'.$client_link),
                    'process' => 'ssl',
                    'count' => '',
                ),
            );
            $php_settings = false;

            // ASP.NET Settings
            if($this->user_data["server_os"] == WINDOWS) {
                if($this->nested == true) {
                    $domain_settings = $this->domain_data['user']['domain_settings'];
                }
                else {
                    $domain_settings = $this->user_data['user']['domain_settings'];
                }
                if($domain_settings['aspnet'] != true) {
                    $web_hosting_settings_buttons['asp_net_settings']['class'] = 'domain-dash disabled';
                    $web_hosting_settings_buttons['asp_net_settings']['link'] = '#';
                    $web_hosting_settings_buttons['asp_net_settings']['count'] = 'X';
                }
                $php_settings = true;
            }
            else {
                unset($web_hosting_settings_buttons['asp_net_settings']);

                if($this->system_packages["HTTPD_SERVER"] == "nginx") {
                    $php_settings = true;
                }
            }

            $web_hosting_settings = array(
                'menu_name' => __('Web Hosting'),
                'menu_items' => $web_hosting_settings_buttons
            );
            if($php_settings == false) {
                unset($web_hosting_settings_buttons['php_settings']);
            }
            // File Setting
            $file_settings_buttons = array(
                array('name' => __('Protect Directory Settings'),
                    'icon' => '<i class="fp fp-ftpaccounts"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('protectdir'.$client_link),
                    'process' => 'protect_dir',
                    'count' => '',
                ),
                array('name' => __('File Manager'),
                    'icon' => '<i class="fp fp-shared-folder"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('filemanager'.$client_link),
                    'count' => '',
                    'process' => 'filemanager',
                ),
                array(
                    'name' => __('Backup Manager'),
                    'icon' => '<i class="fp fp-data-usage2"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('backup'.$client_link),
                    'count' => '',
                    'process' => 'backup',
                ),
                array(
                    'name' => __('Virtual Directory Settings'),
                    "icon" => '<i class="fp fp-folder-settings"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('vdir'.$client_link),
                    'count' => '',
                    'process' => 'vdir',
                ),
            );

            if($this->user_data["server_os"] != WINDOWS) {
                unset($file_settings_buttons['3']);
            }
            else {
                unset($file_settings_buttons['0']);
            }

            $file_settings = array(
                'menu_name' => __('File Settings'),
                'menu_items' => $file_settings_buttons
            );

            // Mail Settings

            $mail_settings_buttons = array(
                array(
                    'name' => __('Mail Settings'),
                    'icon' => '<i class="fp fp-mail-settings"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('mail/mailsettings'.$client_link),
                    'count' => '',
                    'process' => 'mail',
                ),
                array(
                    'name' => __('Mail Accounts'),
                    'icon' => '<i class="fp fp-envelopes-bulk"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('mail/accounts'.$client_link),
                    'count' => $domain_mails_count,
                    'process' => 'mail',
                ),
                array(
                    'name' => __('Create Mail Account'),
                    'icon' => '<i class="fp fp-mail-add"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('mail/create'.$client_link),
                    'count' => '',
                    'process' => 'mail_box',
                ),

                array(
                    'name' => __('Webmail'),
                    'icon' => '<i class="fp fp-web-mail"></i>',
                    'class' => 'domain-dash',
                    'target' => '_blank',
                    'link' => 'http://'. $default_ip . '/webmail',
                    'count' => '',
                    'process' => null,
                ),
            );
            $mail_settings = array(
                'menu_name' => __('Mail Settings'),
                'menu_items' => $mail_settings_buttons
            );

            // Advanced Settings
            $advanced_settings_buttons = array(
                array(
                    'name' => __('Scheduled Task Management'),
                    'icon' => '<i class="fp fp-tasks"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('tasks'.$client_link),
                    'count' => '',
                    'process' => 'task',
                ),
                array(
                    'name' => __('Resource Viewer'),
                    'icon' => '<i class="fp fp-settings"></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('resources'.$client_link),
                    'count' => '',
                    'process' => 'resource',
                ),
                array(
                    'name' => __('Web Statistics'),
                    'icon' => '<i class="fp fp-window-domain"></i>',
                    'class' => 'domain-dash',
                    'target' => '_blank',
                    'link' => "http://".$domain_name.'/stats/index.html',
                    'count' => '',
                    'process' => 'statistics',
                ),
                array(
                    'name' => __('Show Logs'),
                    'icon' => '<i class="fp fp-http-settings"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span></i>',
                    'class' => 'domain-dash',
                    'link' => $this->uri->url_encrypt('logs'.$client_link),
                    'count' => '',
                    'process' => 'logs',
                ),
            );

            $advanced_settings = array(
                'menu_name' => __('Advanced Settings'),
                'menu_items' => $advanced_settings_buttons
            );

            // Buttons Array
            $buttons = array(
                'web_hosting_settings' => $web_hosting_settings,
                'file_settings' => $file_settings,
                'mail_settings' => $mail_settings,
                'advanced_settings' => $advanced_settings
            );

            $this->user_buttons = $buttons;
        }

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
            case ROOT_USER:
                break;
        }
        if($this->client_type < 3) {
            if($licence_limit['domain_limit_exceeded'] == true) {
                $buttons['domain-create']['class'] = $buttons['domain-create']['class']." limit-over disabled";
                $buttons['domain-create']['link'] = "#";
                $buttons['domain-create']['count'] = "x";
            }
            if($licence_limit['client_limit_exceeded'] == true) {
                $buttons['user-create']['class'] = $buttons['user-create']['class']." limit-over disabled";
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
            $buttons['web_hosting_settings']['menu_items'][0]['class'] = "disabled";
            $buttons['web_hosting_settings']['menu_items'][0]['link'] = "#";
            $buttons['web_hosting_settings']['menu_items'][0]['count'] = "x";

            $buttons['web_hosting_settings']['menu_items'][2]['class'] = "disabled";
            $buttons['web_hosting_settings']['menu_items'][2]['link'] = "#";
            $buttons['web_hosting_settings']['menu_items'][2]['count'] = "x";

            $buttons['web_hosting_settings']['menu_items'][6]['class'] = "disabled";
            $buttons['web_hosting_settings']['menu_items'][6]['link'] = "#";
            $buttons['web_hosting_settings']['menu_items'][6]['count'] = "x";

            $buttons['web_hosting_settings']['menu_items'][7]['class'] = "disabled";
            $buttons['web_hosting_settings']['menu_items'][7]['link'] = "#";
            $buttons['web_hosting_settings']['menu_items'][7]['count'] = "x";
            unset($buttons['web_hosting_settings']['menu_items'][7]['target']);

            $buttons['file_settings']['menu_items'][0]['class'] = "disabled";
            $buttons['file_settings']['menu_items'][0]['link'] = "#";
            $buttons['file_settings']['menu_items'][0]['count'] = "x";

            $buttons['file_settings']['menu_items'][1]['class'] = "disabled";
            $buttons['file_settings']['menu_items'][1]['link'] = "#";
            $buttons['file_settings']['menu_items'][1]['count'] = "x";

            $buttons['advanced_settings']['menu_items'][0]['class'] = "disabled";
            $buttons['advanced_settings']['menu_items'][0]['link'] = "#";
            $buttons['advanced_settings']['menu_items'][0]['count'] = "x";

            $buttons['advanced_settings']['menu_items'][1]['class'] = "disabled";
            $buttons['advanced_settings']['menu_items'][1]['link'] = "#";
            $buttons['advanced_settings']['menu_items'][1]['count'] = "x";

            $buttons['advanced_settings']['menu_items'][3]['class'] = "disabled";
            $buttons['advanced_settings']['menu_items'][3]['link'] = "#";
            $buttons['advanced_settings']['menu_items'][3]['count'] = "x";
        }

        if($data['user']['domain_info']['dns_service'] != ACTIVE) {
            $buttons['web_hosting_settings']['menu_items']['dns_settings']['class'] = "disabled";
            $buttons['web_hosting_settings']['menu_items']['dns_settings']['link'] = "#";
            $buttons['web_hosting_settings']['menu_items']['dns_settings']['count'] = "x";
        }

        if($data['user']['domain_info']['mail_service'] != ACTIVE) {
            foreach($buttons['mail_settings']['menu_items'] as $key => $value) {
                $buttons['mail_settings']['menu_items'][$key]['class'] = "disabled";
                $buttons['mail_settings']['menu_items'][$key]['link'] = "#";
                $buttons['mail_settings']['menu_items'][$key]['count'] = "x";
            }

        }

        if(($data['user']['domain_info']['web_service'] != ACTIVE) && ($data['user']['domain_info']['mail_service'] != ACTIVE) && ($data['user']['domain_info']['dns_service'] != ACTIVE)) {
            $buttons['web_hosting_settings']['menu_items']['dns_settings']['class'] = "disabled";
            $buttons['web_hosting_settings']['menu_items']['dns_settings']['link'] = "#";
            $buttons['web_hosting_settings']['menu_items']['dns_settings']['count'] = "x";
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
            'user'                  => array('limit' => 11111111,'permission' => 'man_client'),
            'domain'                => array('limit' => 11111111,'permission' => 'man_domain'),
            // Domain için olan bölüm başlıyor
            'webhosting'            => array('limit' => 11111111,'permission' => 'man_hosting_settings'),
            'php_settings'          => array('limit' => 11111111,'permission' => 'man_hosting_settings'),
            'rewrite'          => array('limit' => 11111111,'permission' => 'man_hosting_settings'),
            'subdomains'            => array('limit' => 11111111,'permission' => 'man_subdomain'),
            'database'              => array('limit' => 11111111,'permission' => 'man_database'),
            'dns'                   => array('limit' => 11111111,'permission' => 'man_dns'),
            'domain_alias'          => array('limit' => 11111111,'permission' => 'man_domain_alias'),
            'ssl'                   => array('limit' => 11111111,'permission' => 'man_ssl'),
            'protect_dir'           => array('limit' => 11111111,'permission' => 'man_protect_dir'),
            'backup'                => array('limit' => 11111111,'permission' => 'man_backup'),
            'mail'                  => array('limit' => 11111111,'permission' => 'man_email'),
            'mail_box'              => array('limit' => 11111111,'permission' => 'man_email'),
            'task'                  => array('limit' => 11111111,'permission' => 'man_task'),
        );

        $user_permissions = $this->user_data['user']['permissions'];
        $user_domain_settings = $this->user_data['user']['domain_settings'];
        $user_remaining_rights = $this->user_data['user']['remaining'];
//        $user_buttons = $this->user_buttons;

        if(!isset($this->nested)) {
            if($this->user_type == DOMAIN_USER) {
                if(($user_remaining_rights['domainemail'] != UNLIMITED) && ($user_remaining_rights['domainemail'] < 1)) {
                    $this->user_buttons["mail_settings"]['menu_items'][2]['class'] = "limit-over";
                    $this->user_buttons["mail_settings"]['menu_items'][2]['link'] = "#";
                }
                foreach ($this->user_buttons as $section => $buttons) {
                    foreach ($buttons['menu_items'] as $menu_items => $item) {
                        if($item['process'] == "php_settings") {
                            if($user_domain_settings['php'] != true) {
                                $this->user_buttons[$section]['menu_items'][$menu_items]['class'] = "not-allowed";
                                $this->user_buttons[$section]['menu_items'][$menu_items]['link'] = "#";
                            }
                        }

                        if(array_key_exists($item['process'], $process_array)) {
                            if($process_array[$item['process']]['permission'] != null) {
                                if($user_permissions[$process_array[$item['process']]['permission']] != ALLOWED) {
                                    $this->user_buttons[$section]['menu_items'][$menu_items]['class'] = "not-allowed";
                                    $this->user_buttons[$section]['menu_items'][$menu_items]['link'] = "#";
                                }
                            }
                        }
                    }
                }
            }
            else {
                if($licence_limit['domain_limit_exceeded'] == true) {
                    $this->user_buttons['domain-create']['link'] = "#";
                    $this->user_buttons['domain-create']['count'] = "x";
                    $this->user_buttons['domain-create']['class'] = $this->user_buttons['domain-create']['class']." limit-over";
                }

                if($this->user_type != CLIENT_USER) {
                    if($licence_limit['client_limit_exceeded'] == true) {
                        $this->user_buttons['user-create']['link'] = "#";
                        $this->user_buttons['user-create']['count'] = "x";
                        $this->user_buttons['user-create']['class'] = $this->user_buttons['user-create']['class'] ." limit-over";
                    }

                    if($user_remaining_rights["clients"] <= "0") {
                        $this->user_buttons["user-create"]["link"] = "#";
                        $this->user_buttons["user-create"]["count"] = "x";
                        $this->user_buttons["user-create"]["class"] = $this->user_buttons['user-create']['class'] . " limit-over";
                    }
                }

                if($user_remaining_rights["domains"] <= "0") {
                    $this->user_buttons["domain-create"]["link"] = "#";
                    $this->user_buttons["domain-create"]["count"] = "x";
                    $this->user_buttons["domain-create"]["class"] = $this->user_buttons['domain-create']['class']." limit-over";
                }

                if($user_remaining_rights["serviceplans"] <= "0") {
                    $this->user_buttons["template-create"]["link"] = "#";
                    $this->user_buttons["template-create"]["count"] = "x";
                    $this->user_buttons["template-create"]["class"] = $this->user_buttons['template-create']['class']." limit-over";
                }

            }
        }
    }

}