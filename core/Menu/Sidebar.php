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
namespace FPanel\Menu;

use FPanel\Library\Library;
use FPanel\Models\Domains;

/**
 * Class Sidebar
 * @package FPanel\Sidebar
 */
class Sidebar extends Library
{
    public function sidebar($user_data, $user_type, $system): array
    {
        $this->view->setVar("search_url", $this->uri->encode("dashboard/search"));
        // @TODO permission içinde gelen array aşağıda işleme alınacak buradan linkleri kapatılması sağlanacak
        $menu = array();
        switch ($user_type) {
            case SUPPORT_USER:
            case ADMIN_USER:
            case ROOT_USER:
                $menu = $this->sideMenuConfig($system);
                unset($menu['web_hosting']);
                unset($menu['file_settings']);
                unset($menu['mail_settings']);
                unset($menu['advanced_settings']);
                break;
            case RESELLER_USER:
                $menu = $this->sideMenuConfig();
                unset($menu['web_hosting']);
                unset($menu['file_settings']);
                unset($menu['mail_settings']);
                unset($menu['advanced_settings']);
                unset($menu['system_settings']);
                break;
            case CLIENT_USER:
                $menu = $this->sideMenuConfig();
                unset($menu['web_hosting']);
                unset($menu['hosting_settings']['submenu']['users']);
                unset($menu['file_settings']);
                unset($menu['mail_settings']);
                unset($menu['advanced_settings']);
                unset($menu['system_settings']);
                break;
            case DOMAIN_USER:
                $domain_name = $user_data['user']['domain_info']['domain_name'];
                $menu = $this->sideMenuConfig(null, $domain_name);
                unset($menu['hosting_settings']);
                unset($menu['system_settings']);
                break;
        }
        return $menu;
    }

    private function sideMenuConfig($system = null, $domain_name = null): array
    {
        $menuconfig = array(
            // Dashboard
            'dashboard' => array(
                'link' => $this->uri->url_encrypt('dashboard'),
                'name' => __('Dashboard'),
                'liclass' => $this->getActiveClass(array("dashboard"), '', array('index')),
                'icon' => '<i class="fa fa-home"></i>',
                'data_toggle' => '',
            ),
            // Hosting Settings
            'hosting_settings' => array(
                'link' => '#hosting_settings',
                'name' => __('Hosting Settings'),
                'data_toggle' => 'data-toggle="collapse"',
                'icon' => '<i class="fa fa-home"></i>',
                'id' => 'hosting_settings',
                'liclass' => $this->getActiveClass(array("users", "serviceplans", "domains"), 'hasSubmenu', array("index", "create", "edit")),
                'ulclass' => $this->getCollapseClassUL(array("users", "serviceplans", "domains"), array("index", "create", "edit")),
                'submenu' => array(
                    'users' => array(
                        'sublink' => '#users',
                        'subname' => __('Users'),
                        'subliclass' => $this->getActiveClass(array("users"), 'hasSubmenu', array('index', 'create', "edit")),
                        'subulclass' => $this->getCollapseClassUL(array('users'), array("index", "create", "edit")),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'users',
                        'icon' => '<i class="fas fa-users"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('users'),
                                'sub_subname' => __('List Users'),
                                'sub_subliclass' => $this->getActiveClass(array("users"), '', array("index")),
                                'icon' => ''
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('users/create'),
                                'sub_subname' => __('Create User'),
                                'sub_subliclass' => $this->getActiveClass(array("users"), '', array("create")),
                                'icon' => ''
                            )
                        )
                    ),
                    'domains' => array(
                        'sublink' => '#domains',
                        'subname' => __('Domains'),
                        'subliclass' => $this->getActiveClass(array("domains"), 'hasSubmenu', array('index', 'create', "edit")),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('domains'), array("index", "create")),
                        'span' => '2',
                        'subid' => 'domains',
                        'icon' => '<i class="fas fa-globe"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domains'),
                                'sub_subname' => __('List Domains'),
                                'sub_subliclass' => $this->getActiveClass(array("domains"), '', array("index")),
                                'icon' => ''
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domains/create'),
                                'sub_subname' => __('Create Domain'),
                                'sub_subliclass' => $this->getActiveClass(array("domains"), '', array("create")),
                                'icon' => ''
                            )
                        ),
                    ),
                    'service_plans' => array(
                        'sublink' => '#service_plans',
                        'subname' => __('Serviceplans'),
                        'subliclass' => $this->getActiveClass(array("serviceplans"), 'hasSubmenu', array('index', 'create', "edit")),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('serviceplans'), array("index", "create", "edit")),
                        'span' => '2',
                        'subid' => 'service_plans',
                        'icon' => '<i class="far fa-list-alt"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('serviceplans'),
                                'sub_subname' => __('List Serviceplans'),
                                'sub_subliclass' => $this->getActiveClass(array("serviceplans"), '', array("index")),
                                'icon' => ''
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('serviceplans/create'),
                                'sub_subname' => __('Create Serviceplan'),
                                'sub_subliclass' => $this->getActiveClass(array("serviceplans"), '', array("create")),
                                'icon' => ''
                            )
                        )
                    ),
                )
            ),
            // Web Hosting
            'web_hosting' => array(
                'link' => '#hosting_settings',
                'name' => __('Hosting Settings'),
                'data_toggle' => 'data-toggle="collapse"',
                'icon' => '',
                'id' => 'hosting_settings',
                'liclass' => $this->getActiveClass(array("webhosting", "php", "subdomain", 'database', 'named', 'domainalias', 'certificate'), 'hasSubmenu', array("index", "create", "edit", "security")),
                'ulclass' => $this->getCollapseClassUL(array("webhosting", "php", "subdomain", "database", 'named', 'domainalias', 'certificate'), array("index", "create", "edit", "security")),
                'submenu' => array(
                    array(
                        'sublink' => $this->uri->url_encrypt('webhosting'),
                        'subname' => __('Web Hosting Settings'),
                        'subliclass' => $this->getActiveClass(array("webhosting"), '', array("index")),
                        'subulclass' => $this->getCollapseClassUL(array('webhosting'), array("index", "edit")),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'webhosting',
                        'icon' => '',
                    ),
                    array(
                        'sublink' => '#subdomain',
                        'subname' => __('Subdomains'),
                        'subliclass' => $this->getActiveClass(array("subdomain"), 'hasSubmenu', array('index', 'create', 'edit')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('subdomain'), array("index", "create", "edit")),
                        'span' => '2',
                        'subid' => 'subdomain',
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('subdomain'),
                                'sub_subname' => __('List Subdomains'),
                                'sub_subliclass' => $this->getActiveClass(array("subdomain"), '', array("index")),
                                'icon' => ''
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('subdomain/create'),
                                'sub_subname' => __('Create Subdomain'),
                                'sub_subliclass' => $this->getActiveClass(array("subdomain"), '', array("create")),
                                'icon' => ''
                            )
                        ),
                    ),
                    array(
                        'sublink' => '#database',
                        'subname' => __('Database'),
                        'subliclass' => $this->getActiveClass(array("database"), 'hasSubmenu', array('index', 'create')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('database'), array("index", "create", "edit")),
                        'span' => '2',
                        'icon' => '',
                        'subid' => 'database',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('database'),
                                'sub_subname' => __('List Databases'),
                                'sub_subliclass' => $this->getActiveClass(array("database"), '', array("index")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('database/create'),
                                'sub_subname' => __('Create Database'),
                                'sub_subliclass' => $this->getActiveClass(array("database"), '', array("create")),
                                'icon' => '',
                            )
                        )
                    ),
                    array(
                        'sublink' => '#dns',
                        'subname' => __('DNS'),
                        'subliclass' => $this->getActiveClass(array("named"), 'hasSubmenu', array('index', 'create')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('named'), array("index", "create", "edit")),
                        'span' => '2',
                        'subid' => 'dns',
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('dns'),
                                'sub_subname' => __('List DNS Records'),
                                'sub_subliclass' => $this->getActiveClass(array("named"), '', array("index")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('dns/create'),
                                'sub_subname' => __('Create DNS Record'),
                                'sub_subliclass' => $this->getActiveClass(array("named"), '', array("create")),
                                'icon' => '',
                            )
                        )
                    ),
                    array(
                        'sublink' => '#domainalias',
                        'subname' => __('Domain Alias'),
                        'subliclass' => $this->getActiveClass(array("domainalias"), 'hasSubmenu', array('index', 'create')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('domainalias'), array("index", "create", "edit")),
                        'span' => '2',
                        'subid' => 'domainalias',
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domainalias'),
                                'sub_subname' => __('List Domain Alias'),
                                'sub_subliclass' => $this->getActiveClass(array("domainalias"), '', array("index")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domainalias/create'),
                                'sub_subname' => __('Create Domain Alias'),
                                'sub_subliclass' => $this->getActiveClass(array("domainalias"), '', array("create")),
                                'icon' => '',
                            )
                        )
                    ),
                    array(
                        'sublink' => '#certificate',
                        'subname' => __('SSL Certificates'),
                        'subliclass' => $this->getActiveClass(array("certificate"), 'hasSubmenu', array('index', 'create')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('certificate'), array("index", "create", "edit")),
                        'span' => '2',
                        'subid' => 'certificate',
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('certificate'),
                                'sub_subname' => __('List SSL Certificates'),
                                'sub_subliclass' => $this->getActiveClass(array("certificate"), '', array("index")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('certificate/create'),
                                'sub_subname' => __('Create SSL Certificate'),
                                'sub_subliclass' => $this->getActiveClass(array("certificate"), '', array("create")),
                                'icon' => '',
                            )
                        )
                    ),
                )
            ),
            // File Settings
            'file_settings' => array(
                'link' => '#file_settings',
                'name' => __('File Settings'),
                'data_toggle' => 'data-toggle="collapse"',
                'icon' => '',
                'id' => 'file_settings',
                'liclass' => $this->getActiveClass(array("protectdir", "filemanager", "backup"), 'hasSubmenu', array("index", "create", "edit")),
                'ulclass' => $this->getCollapseClassUL(array("protectdir", "filemanager", "backup"), array("index", "create", "edit")),
                'submenu' => array(
                    array(
                        'sublink' => '#protectdir',
                        'subname' => __('Protect Directory'),
                        'subliclass' => $this->getActiveClass(array("protectdir"), 'hasSubmenu', array('index', 'create', 'edit')),
                        'subulclass' => $this->getCollapseClassUL(array('protectdir'), array('index', 'create', 'edit')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'protectdir',
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('protectdir'),
                                'sub_subname' => __('Protect Directory List'),
                                'sub_subliclass' => $this->getActiveClass(array("protectdir"), '', array("index")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('protectdir/create'),
                                'sub_subname' => __('Create Protect Directory'),
                                'sub_subliclass' => $this->getActiveClass(array("protectdir"), '', array("index")),
                                'icon' => '',
                            )
                        )
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('filemanager'),
                        'subname' => __('File Manager (FTP)'),
                        'subliclass' => '',
                        'subdata-toggle' => '',
                        'subulclass' => '',
                        'span' => '',
                        'subid' => '',
                        'icon' => '',
                    ),
                    array(
                        'sublink' => '#backup',
                        'subname' => __('Backup'),
                        'subliclass' => $this->getActiveClass(array("backup"), 'hasSubmenu', array('index', 'create')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'subulclass' => $this->getCollapseClassUL(array('backup'), array("index", "create", "edit")),
                        'span' => '2',
                        'subid' => 'backup',
                        'icon' => '<i class="fas fa-hdd"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('backup'),
                                'sub_subname' => __('List Backups'),
                                'sub_subliclass' => $this->getActiveClass(array("backup"), '', array("index")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('backup/create'),
                                'sub_subname' => __('Create Backup'),
                                'sub_subliclass' => $this->getActiveClass(array("backup"), '', array("create")),
                                'icon' => '',
                            )
                        )
                    ),
                )
            ),
            // Mail Settings
            'mail_settings' => array(
                'link' => '#mail_management',
                'name' => __('Mail'),
                'data_toggle' => 'data-toggle="collapse"',
                'icon' => '',
                'id' => 'mail_management',
                'liclass' => $this->getActiveClass(array('mail'), 'hasSubmenu', array('index', 'accounts', 'create', 'mailsettings')),
                'ulclass' => $this->getCollapseClassUL(array('mail'), array('index', 'accounts', 'create', 'mailsettings')),
                'submenu' => array(
                    array(
                        'sublink' => $this->uri->url_encrypt('mail/mailsettings'),
                        'subname' => __('Mail Settings'),
                        'subliclass' => $this->getActiveClass(array("mail"), '', array('mailsettings')),
                        'subdata-toggle' => '',
                        'subulclass' => $this->getCollapseClassUL(array('mail'), array('mailsettings')),
                        'span' => '2',
                        'subid' => 'mailsettings',
                        'icon' => '<i class="fas fa-at"></i>',
                    ),
                    array(
                        'sublink' => '#mailaccounts',
                        'subname' => __('Mail Accounts'),
                        'subliclass' => $this->getActiveClass(array("mail"), 'hasSubmenu', array('index', 'accounts', 'edit')),
                        'subulclass' => $this->getCollapseClassUL(array('mail'), array('index', 'accounts', 'edit')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'mailaccounts',
                        'icon' => '<i class="fas fa-envelope"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('mail/accounts'),
                                'sub_subname' => __('Mail Accounts'),
                                'sub_subliclass' => $this->getActiveClass(array("mail"), '', array("accounts")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('mail/create'),
                                'sub_subname' => __('Create Mail Account'),
                                'sub_subliclass' => $this->getActiveClass(array("mail"), '', array("create")),
                                'icon' => '',
                            )
                        )
                    ),
//                    array(
//                        'sublink'          => '#',
//                        'subname'          => __('Mailing Lists'),
//                        'subliclass'       => 'disabled',
//                        'subdata-toggle'   => '',
//                        'subulclass'       => 'disabled',
//                        'span'             => '',
//                        'subid'            => '',
//                        'icon'            => '',
//                    ),
                    array(
                        'sublink' => 'http://' . 'webmail.' . $domain_name,
                        'subname' => __('Webmail'),
                        'subliclass' => '',
                        'subdata-toggle' => '',
                        'subulclass' => '',
                        'span' => '',
                        'subid' => '',
                        'icon' => '<i class="fas fa-envelope-open-text"></i>',
                    ),
                )
            ),
            // Advanced Settings
            'advanced_settings' => array(
                'link' => '#advanced_settings',
                'name' => __('Advanced Settings'),
                'data_toggle' => 'data-toggle="collapse"',
                'icon' => '',
                'id' => 'advanced_settings',
                'liclass' => $this->getActiveClass(array('tasks', 'resources', 'applications'), 'hasSubmenu', array('index', 'create', 'edit')),
                'ulclass' => $this->getCollapseClassUL(array('tasks', 'resources', 'applications'), array('index', 'create', 'edit')),
                'submenu' => array(
                    array(
                        'sublink' => '#tasks',
                        'subname' => __('Scheduled Tasks'),
                        'subliclass' => $this->getActiveClass(array("tasks"), 'hasSubmenu', array('index', 'create', 'edit')),
                        'subulclass' => $this->getCollapseClassUL(array('tasks'), array('index', 'create', 'edit')),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'tasks',
                        'icon' => '<i class="fas fa-calendar-check"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('tasks'),
                                'sub_subname' => __('Scheduled Task List'),
                                'sub_subliclass' => $this->getActiveClass(array("tasks"), '', array("index")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('tasks/create'),
                                'sub_subname' => __('Create Scheduled Task'),
                                'sub_subliclass' => $this->getActiveClass(array("tasks"), '', array("create")),
                                'icon' => '',
                            )
                        )
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('resources'),
                        'subname' => __('Resource Usage'),
                        'subliclass' => $this->getActiveClass(array("resources"), 'hasSubmenu', array('index')),
                        'subdata-toggle' => '',
                        'subulclass' => $this->getCollapseClassUL(array('resources'), array('index')),
                        'span' => '2',
                        'subid' => 'resources',
                        'icon' => '',
                    ),
                    array(
                        'sublink' => 'http://' . $domain_name . '/stats',
                        'subname' => __('Web Statistics'),
                        'subliclass' => '',
                        'subdata-toggle' => '',
                        'subulclass' => '',
                        'subid' => 'webstatistics',
                        'icon' => '',
                    ),
                )
            ),
        );

        if ($system['system_type'] == CLOUD) {
            $menuconfig[] = array(
                'link' => '#system_settings',
                'name' => __('System Settings'),
                'data_toggle' => 'data-toggle="collapse"',
                'id' => 'system_settings',
                'icon' => '<i class="fas fa-cogs"></i>',
                'liclass' => $this->getActiveClass(array("system"), 'hasSubmenu', array("index", "create", "edit")),
                'ulclass' => $this->getCollapseClassUL(array("system"), array("index", "create", "edit")),
                'submenu' => array(
                    array(
                        'sublink' => $this->uri->url_encrypt('system'),
                        'subname' => __('Cloud Dashboard'),
                        'subliclass' => $this->getActiveClass(array("system"), '', array('index')),
                        'icon' => '<i class="fas fa-cloud"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('network'),
                        'subname' => __('Network Management'),
                        'subliclass' => $this->getActiveClass(array("network"), '', array('index', 'create', 'edit', "hostname")),
                        'icon' => '<i class="fas fa-network-wired"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('update'),
                        'subname' => __('System Update'),
                        'subliclass' => $this->getActiveClass(array("update"), '', array('index', 'settings')),
                        'icon' => '<i class="fas fa-sync-alt"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('server/timesettings'),
                        'subname' => __('System Time'),
                        'subliclass' => $this->getActiveClass(array("server"), '', array('timesettings')),
                        'icon' => '<i class="fas fa-calendar-check"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('blacklist/ftpusers'),
                        'subname' => __('Blacklist Management'),
                        'subliclass' => $this->getActiveClass(array("blacklist"), '', array('ftpusers')),
                        'icon' => '<i class="fas fa-user-slash"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('apisettings'),
                        'subname' => __('Api Management'),
                        'subliclass' => $this->getActiveClass(array("apisettings"), '', array('index')),
                        'icon' => '<i class="fas fa-code"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('messages'),
                        'subname' => __('System Messages'),
                        'subliclass' => $this->getActiveClass(array("messages"), '', array('index', 'edit')),
                        'icon' => '<i class="fas fa-comments"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('session'),
                        'subname' => __('Access Panel'),
                        'subliclass' => $this->getActiveClass(array("session"), '', array('index', 'settings')),
                        'icon' => '<i class="fas fa-user-lock"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('support'),
                        'subname' => __('Help Support'),
                        'subliclass' => $this->getActiveClass(array("support"), '', array('index', 'create', "edit")),
                        'icon' => '<i class="fas fa-life-ring"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('system/notify'),
                        'subname' => __('Notifications & Alerts'),
                        'subliclass' => $this->getActiveClass(array("system"), '', array('notify')),
                        'icon' => '<i class="fas fa-bell"></i>',
                    ),
                    array(
                        'sublink' => $this->uri->url_encrypt('system/passwordsettings'),
                        'subname' => __('Advanced Settings'),
                        'icon' => '<i class="fas fa-tools"></i>',
                        'subliclass' => $this->getActiveClass(array("system"), '', array('passwordsettings', "certificate", "recaptchasettings", "addnewadmin")),
                    ),
                )
            );
        } else {
            $menuconfig['system_settings'] = array(
                'link' => '#system_settings',
                'name' => __('System Settings'),
                'data_toggle' => 'data-toggle="collapse"',
                'id' => 'system_settings',
                'icon' => '<i class="fas fa-server"></i>',
                'liclass' => $this->getActiveClass(["system", "ftp", "webserver", "serverphp", "mailserver", "dbserver", "dnsserver", "network", "server", "services", "packages", "blacklist", "apisettings", "spamassassin", "systemapp", "messages", "session", "cron", "support", "backup", "update", "serverdns"],
                    'hasSubmenu', ["index", "settings", "create", "edit", "dashboard", "hostname", "sourcelists", "generalsettings", "spamsettings", "management", "logsettings", "timesettings", "ftpusers", "downloads", "resource", "restore", "modules", "mimetypes", "webSetting", "addMime", "editmime", "notify", "blacklist", "blacklistcreate", "editblack", "whitelist", "whitelistcreate", "editwhite", "sessions", "smtp"]),
                'ulclass' => $this->getCollapseClassUL(["system", "ftp", "webserver", "serverphp", "mailserver", "dbserver", "dnsserver", "network", "server", "services", "blacklist", "packages", "apisettings", "systemapp", "messages", "session", "cron", "support", "backup", "update", "serverdns", "spamassassin"],
                    ["index", "settings", "create", "edit", "dashboard", "hostname", "sourcelists", "generalsettings", "spamsettings", "management", "logsettings", "timesettings", "ftpusers", "downloads", "resource", "restore", "modules", "mimetypes", "webSetting", "addMime", "editmime", "notify", "blacklist", "blacklistcreate", "editblack", "whitelist", "whitelistcreate", "editwhite", "sessions", "smtp"]),
                'submenu' => array(
                    array(
                        'sublink' => $this->uri->url_encrypt('system'),
                        'subname' => __('System Dashboard'),
                        'subliclass' => $this->getActiveClass(array("system"), 'hasSubmenu', array('index')),
                        'subulclass' => $this->getCollapseClassUL(array('system'), array('index')),
                        'subdata-toggle' => '',
                        'span' => '',
                        'subid' => '',
                        'icon' => '<i class="fa fa-dashboard"></i>',
                        'subsubmenu' => array()
                    ),
                    array(
                        'sublink' => '#system_management',
                        'subname' => __('System Management'),
                        'subliclass' => $this->getActiveClass(["ftp", "webserver", "serverphp", "mailserver", "dbserver", "dnsserver", "network", "packages", "server", "services", "update", "system", "serverdns", "spamassassin"],
                            'hasSubmenu',
                            ["index", "settings", "create", "edit", "hostname", "sourcelists", "generalsettings", "spamsettings", "management", "logsettings", "timesettings", "modules", "mimetypes", "webSetting", "addMime", "editmime", "blacklist", "blacklistcreate", "editblack", "whitelist", "whitelistcreate", "editwhite", "sessions", "smtp"]),
                        'subulclass' => $this->getCollapseClassUL(["ftp", "webserver", "serverphp", "mailserver", "dbserver", "dnsserver", "network", "server", "packages", "services", "update", "system", "serverdns", "spamassassin"],
                            ["index", "settings", "create", "edit", "hostname", "sourcelists", "generalsettings", "spamsettings", "management", "logsettings", "timesettings", "modules", "mimetypes", "webSetting", "addMime", "editdmime", "blacklist", "blacklistcreate", "editblack", "whitelist", "whitelistcreate", "editwhite", "sessions", "smtp"]),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'system_management',
                        'icon' => '<i class="fas fa-cogs"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('webserver/web'),
                                'sub_subname' => __('Web Server'),
                                'sub_subliclass' => $this->getActiveClass(array("webserver"), '', array('settings', 'modules', 'webSetting', 'mimetypes', "addMime")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('ftp/settings'),
                                'sub_subname' => __('FTP Settings'),
                                'sub_subliclass' => $this->getActiveClass(array("ftp"), '', array('settings', 'sessions', 'kill')),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('serverphp/settings'),
                                'sub_subname' => __('PHP Settings'),
                                'sub_subliclass' => $this->getActiveClass(array("serverphp"), '', array('settings')),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('mailserver/generalsettings'),
                                'sub_subname' => __('Mail Server'),
                                'sub_subliclass' => $this->getActiveClass(array("mailserver", "spamassassin"), '', array('generalsettings', "spamsettings", "blacklist", "blacklistcreate", "editblack", "whitelist", "whitelistcreate", "editwhite", "smtp")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('dbserver'),
                                'sub_subname' => __('Database Server'),
                                'sub_subliclass' => $this->getActiveClass(array("dbserver"), '', array('index', 'create', 'edit', "settings")),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('dnsserver'),
                                'sub_subname' => __('DNS Server'),
                                'sub_subliclass' => $this->getActiveClass(array("serverdns"), '', array('index', 'create', 'edit')),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('network'),
                                'sub_subname' => __('Network Management'),
                                'sub_subliclass' => $this->getActiveClass(array("network"), '', array('index', 'create', 'edit', "hostname")),
                                'icon' => '<i class="fas fa-network-wired"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('services/management'),
                                'sub_subname' => __('Service Management'),
                                'sub_subliclass' => $this->getActiveClass(array("services"), '', array('index', 'management')),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('packages'),
                                'sub_subname' => __('Server Packages'),
                                'sub_subliclass' => $this->getActiveClass(array("packages"), '', array('index', 'sourcelists')),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('update'),
                                'sub_subname' => __('System Update'),
                                'sub_subliclass' => $this->getActiveClass(array("update"), '', array('index', 'settings')),
                                'icon' => '<i class="far fa-sync-alt"></i>',
                            ),
//                            array(
//                                'sub_sublink'          => $this->uri->url_encrypt('server/logsettings'),
//                                'sub_subname'          => __('Server Log Settings'),
//                                'sub_subliclass'       => $this->getActiveClass(array("server"),'',array('logsettings')),
//                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('server/timesettings'),
                                'sub_subname' => __('System Time'),
                                'sub_subliclass' => $this->getActiveClass(array("server"), '', array('timesettings')),
                                'icon' => '<i class="fas fa-calendar-check"></i>',
                            ),
                        )
                    ),
                    array(
                        'sublink' => '#back_migration',
                        'subname' => __('Backup & Migration'),
                        'subliclass' => $this->getActiveClass(array("migration", "serverbackup"), 'hasSubmenu', array('index', "restore", "create", "upload", "repo", "createrepo", "editrepo", "storage", "addstorage", "editstorage")),
                        'subulclass' => $this->getCollapseClassUL(array('serverbackup', "migration"), array('index', "create", "restore")),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'back_migration',
                        'icon' => '<i class="fa fa-exchange"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('serverbackup'),
                                'sub_subname' => __('Backup Management'),
                                'sub_subliclass' => $this->getActiveClass(array("serverbackup"), '', array('index', "restore", "create", "upload", "repo", "createrepo", "editrepo", "storage", "addstorage", "editstorage")),
                                'icon' => '',
                            ),
//                            array(
//                                'sub_sublink'          => $this->uri->url_encrypt('migration'),
//                                'sub_subname'          => __('Migration'),
//                                'sub_subliclass'       => $this->getActiveClass(array("migration"),'',array("migration")),
//                                'icon'            => '',
//                            ),
                        )
                    ),
                    array(
                        'sublink' => '#panel_settings',
                        'subname' => __('Panel Settings'),
                        'subliclass' => $this->getActiveClass(array("blacklist", "apisettings", "systemapp", "messages", "session", "cron", "support", "telegram", "system", "server"), 'hasSubmenu', array("index", "downloads", "create", "edit", "management", "settings", "notify", "passwordsettings", "resource", "notify", 'ftpusers', 'addftpuser', "domains", "adddomain", "dbusers", "adddbuser", "emailusers", "addemailuser", "edit", "certificate", "recaptchasettings", "addnewadmin")),
                        'subulclass' => $this->getCollapseClassUL(array('telegram', 'blacklist', "apisettings", "systemapp", "messages", "session", "cron", "support", "server", "system"), array("index", "downloads", "create", "edit", "management", "settings", "notify", "passwordsettings", "resource", "notify", 'ftpusers', 'addftpuser', "domains", "adddomain", "dbusers", "adddbuser", "emailusers", "addemailuser", "edit", "certificate", "recaptchasettings", "addnewadmin")),
                        'subdata-toggle' => 'data-toggle="collapse"',
                        'span' => '2',
                        'subid' => 'panel_settings',
                        'icon' => '<i class="fas fa-cogs"></i>',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('blacklist/ftpusers'),
                                'sub_subname' => __('Blacklist Management'),
                                'sub_subliclass' => $this->getActiveClass(array("blacklist"), '', array('ftpusers', 'addftpuser', "domains", "adddomain", "dbusers", "adddbuser", "emailusers", "addemailuser", "edit")),
                                'icon' => '<i class="fas fa-user-slash"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('apisettings'),
                                'sub_subname' => __('Api Management'),
                                'sub_subliclass' => $this->getActiveClass(array("apisettings"), '', array('index')),
                                'icon' => '<i class="fas fa-code"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('messages'),
                                'sub_subname' => __('System Messages'),
                                'sub_subliclass' => $this->getActiveClass(array("messages"), '', array('index', "create", 'edit')),
                                'icon' => '<i class="fas fa-comments-alt"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('server/resource'),
                                'sub_subname' => __('Resource Management'),
                                'sub_subliclass' => $this->getActiveClass(array("server"), '', array('resource')),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('session'),
                                'sub_subname' => __('Access Panel'),
                                'sub_subliclass' => $this->getActiveClass(array("session"), '', array('index', 'settings')),
                                'icon' => '<i class="fas fa-user-lock"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('cron'),
                                'sub_subname' => __('Scheduled Task'),
                                'sub_subliclass' => $this->getActiveClass(array("cron"), '', array('index', 'create', "edit")),
                                'icon' => '<i class="fas fa-tasks"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('support'),
                                'sub_subname' => __('Help Support'),
                                'sub_subliclass' => $this->getActiveClass(array("support"), '', array('index', 'create', "edit")),
                                'icon' => '<i class="fas fa-user-headset"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('system/notify'),
                                'sub_subname' => __('Notifications & Alerts'),
                                'sub_subliclass' => $this->getActiveClass(array("system"), '', array('notify')),
                                'icon' => '<i class="fas fa-bells"></i>',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('system/passwordsettings'),
                                'sub_subname' => __('Advanced Settings'),
                                'sub_subliclass' => $this->getActiveClass(array("system"), '', array('passwordsettings', "certificate", "recaptchasettings", "addnewadmin")),
                                'icon' => '<i class="fas fa-tools"></i>',
                            ),
//                            array(
//                                'sub_sublink'          => $this->uri->url_encrypt('telegram'),
//                                'sub_subname'          => __('Telegram'),
//                                'sub_subliclass'       => $this->getActiveClass(array("telegram"),'',array('index')),
//                                'icon'            => '',
//                            ),
//                            array(
//                                'sub_sublink'          => $this->uri->url_encrypt('letsencrypt'),
//                                'sub_subname'          => __('LetsEncrypt'),
//                                'sub_subliclass'       => $this->getActiveClass(array("letsencrypt"),'',array('index')),
//                                'icon'            => '',
//                            ),
                        )
                    )
                )
            );
        }

        $menuconfig['my_account'] = array(
            'link' => '#my_account',
            'name' => __('My Account'),
            'data_toggle' => 'data-toggle="collapse"',
            'id' => 'my_account',
            'icon' => '<i class="fa fa-user"></i>',
            'liclass' => $this->getActiveClass(array("account"), 'hasSubmenu', array('index')),
            'ulclass' => $this->getCollapseClassUL(array('account'), array('index')),
            'submenu' => array(
                array(
                    'sublink' => $this->uri->url_encrypt('account'),
                    'subname' => __('Profile'),
                    'subliclass' => $this->getActiveClass(array("account"), 'hasSubmenu', array('index')),
                    'subulclass' => $this->getCollapseClassUL(array('account'), array('index')),
                    'subdata-toggle' => '',
                    'span' => '',
                    'subid' => '',
                    'icon' => '<i class="far fa-id-card"></i>',
                    'subsubmenu' => array()
                ),
                array(
                    'sublink' => $this->uri->url_encrypt("logout", true),
                    'subname' => __('Logout'),
                    'subliclass' => false,
                    'subulclass' => false,
                    'subdata-toggle' => 'data-toggle="collapse"',
                    'span' => '2',
                    'id' => 'logout',
                    'icon' => '<i class="fas fa-sign-out-alt"></i>',
                    'subsubmenu' => array()
                )
            )
        );

        return $menuconfig;

    }

    private function getActiveClass($controllers, $defclass, $methods = null): string
    {
        $var = $defclass;
        foreach ($controllers as $controller) {
            if ($controller == $this->router->getControllerName()) {
                if ($methods != null) {
                    foreach ($methods as $method) {
                        if ($method == $this->router->getActionName()) {
                            $var = $defclass . " active";
                        }
                    }
                }
            }
        }
        return $var;
    }

    private function getCollapseClassUL($controllers, $methods): string
    {

        $class = " ";
        foreach ($controllers as $controller) {
            if ($controller == $this->router->getControllerName()) {
                $class = $class . " in";
                if ($methods != null) {

                    foreach ($methods as $method) {
                        if ($method == $this->router->getActionName()) {
                            return $class;
                        }

                    }
                }
            }
        }
        return $class;
    }

    private function defaultMenu($client_id, $client_type, $domain_name = null): array
    {
        return array(
            // Dashboard
            'dashboard' => array(
                'link' => $this->uri->url_encrypt('dashboard/' . $client_type . '/' . $client_id),
                'name' => __('Dashboard'),
                'icon' => '',
                'submenu' => array(),
            ),
            // Hosting Settings
            'hosting_settings' => array(
                'link' => '#hosting_settings',
                'name' => __('Hosting Settings'),
                'icon' => '',
                'submenu' => array(
                    'users' => array(
                        'sublink' => '#users',
                        'subname' => __('Users'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('users/client/' . $client_id),
                                'sub_subname' => __('List Users'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('users/create/client/' . $client_id),
                                'sub_subname' => __('Create User'),
                                'icon' => '',
                            )
                        )
                    ),
                    'domains' => array(
                        'sublink' => '#domains',
                        'subname' => __('Domains'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domains/client/' . $client_id),
                                'sub_subname' => __('List Domains'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domains/create/client/' . $client_id),
                                'sub_subname' => __('Create Domain'),
                                'icon' => '',
                            )
                        ),
                    ),
                    'service_plans' => array(
                        'sublink' => '#service_plans',
                        'subname' => __('Serviceplans'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('serviceplans/client/' . $client_id),
                                'sub_subname' => __('List Serviceplans'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('serviceplans/create/client/' . $client_id),
                                'sub_subname' => __('Create Serviceplan'),
                                'icon' => '',
                            )
                        )
                    ),
                )
            ),
            // Web Hosting
            'web_hosting' => array(
                'link' => '#hosting_settings',
                'name' => __('Web Hosting'),
                'icon' => '',
                'submenu' => array(
                    'webhosting' => array(
                        'sublink' => '#webhosting',
                        'subname' => __('Web Hosting'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('webhosting/domain/' . $client_id),
                                'sub_subname' => __('General Settings'),
                                'icon' => '',
                            ),
                        )
                    ),
                    'subdomain' => array(
                        'sublink' => '#subdomain',
                        'subname' => __('Subdomains'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('subdomain/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('subdomain/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        ),
                    ),
                    'database' => array(
                        'sublink' => '#database',
                        'subname' => __('Database Management'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('database/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('database/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
                    'dns' => array(
                        'sublink' => '#dns',
                        'subname' => __('DNS Management'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('dns/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('dns/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
                    'domain_alias' => array(
                        'sublink' => '#domainalias',
                        'subname' => __('Domain Alias Management'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domainalias/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('domainalias/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
                    'certificate' => array(
                        'sublink' => '#certificate',
                        'subname' => __('SSL Certificates'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('certificate/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('certificate/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
                )
            ),
            // File Settings
            'file_settings' => array(
                'link' => '#file_settings',
                'name' => __('File Settings'),
                'icon' => '',
                'submenu' => array(
                    'protect_dir' => array(
                        'sublink' => '#protectdir',
                        'subname' => __('Protect Directory'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('protectdir/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('protectdir/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
                    'file_manager' => array(
                        'sublink' => $this->uri->url_encrypt('filemanager/' . $client_id),
                        'subname' => __('File Management (FTP)'),
                        'icon' => '',
                    ),
                    'backup' => array(
                        'sublink' => '#backup',
                        'subname' => __('Backup Management'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('backup/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('backup/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
                )
            ),
            // Mail Settings
            'mail_settings' => array(
                'link' => '#mail_management',
                'name' => __('Mail Management'),
                'icon' => '',
                'submenu' => array(
                    'mail_settings' => array(
                        'sublink' => $this->uri->url_encrypt('mail/mailsettings/domain/' . $client_id),
                        'subname' => __('Mail Settings'),
                        'icon' => '',
                    ),
                    'mail_accounts' => array(
                        'sublink' => '#mailaccounts',
                        'subname' => __('Mail Accounts'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('mail/accounts/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('mail/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
                    'webmail' => array(
                        'sublink' => '/' . 'webmail.' . $domain_name,
                        'subname' => __('Webmail'),
                        'icon' => '',
                    ),
                )
            ),
            // Advanced Settings
            'advanced_settings' => array(
                'link' => '#advanced_settings',
                'name' => __('Advanced Settings'),
                'icon' => '',
                'submenu' => array(
                    'tasks' => array(
                        'sublink' => '#tasks',
                        'subname' => __('Scheduled Tasks'),
                        'icon' => '',
                        'subsubmenu' => array(
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('tasks/domain/' . $client_id),
                                'sub_subname' => __('List'),
                                'icon' => '',
                            ),
                            array(
                                'sub_sublink' => $this->uri->url_encrypt('tasks/create/domain/' . $client_id),
                                'sub_subname' => __('Create'),
                                'icon' => '',
                            )
                        )
                    ),
//                    array(
//                        'sublink'          => $this->uri->url_encrypt('applications/domain/'.$client_id),
//                        'subname'          => __('Application Installer'),
//                        'icon'           => 'cargo',
//                    ),
                    'resource' => array(
                        'sublink' => $this->uri->url_encrypt('resources/domain/' . $client_id),
                        'subname' => __('Resource Management'),
                        'icon' => '',
                    ),
                    'statistics' => array(
                        'sublink' => '/' . $domain_name . '/stats',
                        'subname' => __('Web Statistics'),
                        'icon' => '',
                    ),
                )
            ),
        );
    }

    public function clientMenu($client_type, $client_id)
    {
        if (($client_type == RESELLER_USER)) {
            $menu = $this->defaultMenu($client_id, "client");
            unset($menu['web_hosting']);
            unset($menu['file_settings']);
            unset($menu['mail_settings']);
            unset($menu['advanced_settings']);
            unset($menu['system_settings']);

        } else if (($client_type == CLIENT_USER)) {
            $menu = $this->defaultMenu($client_id, "client");
            unset($menu['web_hosting']);
            unset($menu['hosting_settings']['submenu']['users']);
            unset($menu['file_settings']);
            unset($menu['mail_settings']);
            unset($menu['advanced_settings']);
            unset($menu['system_settings']);


        } else if ($client_type == DOMAIN_USER) {
            $domains = Domains::findFirstById($client_id);
            $menu = $this->defaultMenu($client_id, "domain", $domains->name);

            unset($menu['hosting_settings']);
            unset($menu['system_settings']);
        } else {
            return false;
        }

        return $menu;
    }

    public function getSideLangsMenu(): array
    {
        if (!$this->fcookies->has("language")) {
            $this->fcookies->set("language", "en_US", time() + (86400 * 365)); // 86400 = 1 day
            $language = "en_US";
        } else
            $language = $this->fcookies->get("language");
        $lang_list = $this->translate->getLangList();
        $data = array();
        foreach ($lang_list as $key => $value) {
            $lang = array(
                'class' => '',
                'img' => $key,
                'title' => $value
            );
            if ($language == $key) {
                $lang['class'] = "active";
            }
            $data[] = $lang;
        }

        return $data;
    }

    public function installationSideBar()
    {
        return [
            "serverinfo" => array(
                'link' => $this->uri->url_encrypt('install/serverinfo', true),
                'name' => __('Server Informations'),
                'liclass' => $this->getActiveClass(array("install"), '', array('serverinfo')),
                'icon' => '<i class="fa fa-cogs"></i>',
                'data_toggle' => '',
            ),
            "generalinfo" => array(
                'link' => $this->uri->url_encrypt('install/generalinfo', true),
                'name' => __('General Information'),
                'liclass' => $this->getActiveClass(array("install"), '', array('generalinfo')),
                'icon' => '<i class="fa fa-user"></i>',
                'data_toggle' => '',
            ),
            "finish" => array(
                'link' => $this->uri->url_encrypt('install/finish', true),
                'name' => __('Finish'),
                'liclass' => $this->getActiveClass(array("install"), '', array('finish')),
                'icon' => '<i class="fas fa-check-double"></i>',
                'data_toggle' => '',
            ),
        ];
    }

}