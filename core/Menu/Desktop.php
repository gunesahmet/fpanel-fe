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

class Desktop extends Library
{
    public function desktopButtons($params): array
    {
        $client_type = $params['client_type'];
        $buttons = [];
        switch($client_type) {
            case SUPPORT_USER:
            case ROOT_USER:
            case ADMIN_USER:
                if(isset($params['nested']))
                    $buttons = $this->nestedButtons($params);
                else
                    $buttons = $this->adminButtons();
                break;
            case RESELLER_USER:
                if(isset($params['nested']))
                    $buttons = $this->nestedButtons($params);
                else
                    $buttons = $this->resellerButtons($params);
                break;
            case CLIENT_USER:
                if(isset($params['nested']))
                    $buttons = $this->nestedButtons($params);
                else
                    $buttons = $this->clientButtons($params);
                break;
            case DOMAIN_USER:
                $buttons = $this->domainButtons($params);
                break;
        }
        return $buttons;
    }

    private function nestedButtons($params): array
    {
        switch($params['nested_client_type']) {
            case RESELLER_USER:
                $buttons = $this->resellerButtons($params);
                break;
            case CLIENT_USER:
                $buttons = $this->clientButtons($params);
                break;
            case DOMAIN_USER:
                $buttons = $this->domainButtons($params);
                break;
            default:
                return array();
        }
        return $buttons;
    }

    private function adminButtons(): array
    {
        $buttons['buttons'] = array(
            md5("users") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("users"),
                'id'            => md5("users"),
                'url'           => $this->uri->url_encrypt('users'),
                'description'   => '',
                'name'          => __('List Users'),
                'icon'          => '<span class="icon-users fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("user_create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("user_create"),
                'id'            => md5("user_create"),
                'url'           => $this->uri->url_encrypt('users/create'),
                'description'   => '',
                'name'          => __('Create User'),
                'icon'          => '<span class="icon-user-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("list_domains") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("list_domains"),
                'id'            => md5("list_domains"),
                'url'          => $this->uri->url_encrypt('domains'),
                'description'   => '',
                'name'          => __('List Domains'),
                'icon'             => '<span class="icon-domains fs2"><span class="path1"></span><span class="path2"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("domain_create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("domain_create"),
                'id'            => md5("domain_create"),
                'url'          => $this->uri->url_encrypt('domains/create'),
                'description'   => '',
                'name'          => __('Create Domain'),
                'icon'             => '<span class="icon-domain-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("serviceplans") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("serviceplans"),
                'id'            => md5("serviceplans"),
                'url'            => $this->uri->url_encrypt('serviceplans'),
                'description'   => '',
                'name'          => __('List Serviceplans'),
                'icon'             => '<span class="icon-serviceplans fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            ),
            md5("serviceplan_create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("serviceplan_create"),
                'id'            => md5("serviceplan_create"),
                'url'          => $this->uri->url_encrypt('serviceplans/create'),
                'description'   => '',
                'name'          => __('Create Serviceplan'),
                'icon'             => '<span class="icon-serviceplans-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            ),
            md5("system_management") => array(
                'col'           => '1',
                'row'           => '2',
                'target'            => md5("system_management"),
                'id'            => md5("system_management"),
                'name'          => __('System Management'),
                'url'           => "",
                'description'   => '',
                'icon'          => '<span class="icon-dedicated-server fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span></span>',
                'has_child'     => true,
                'childs'       => array(
                    md5("webserver_settings") => array(
                        'target'            => md5("webserver_settings"),
                        'id'            => md5("webserver_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('webserver/default'),
                        'name'          => __('Web Server'),
                        'icon'       => '<span class="icon-web-server fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                    ),
                    md5("ftp_server_settings") => array(
                        'target'            => md5("ftp_server_settings"),
                        'id'            => md5("ftp_server_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('ftp'),
                        'name'          => __('FTP Settings'),
                        'icon'       => '<span class="icon-ftp-settings2 fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span></span>',
                    ),
                    md5("serverphp_settings") => array(
                        'target'            => md5("serverphp_settings"),
                        'id'            => md5("serverphp_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('serverphp/settings'),
                        'name'          => __('PHP Settings'),
                        'icon'       => '<span class="icon-php fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                    ),
                    md5("mailserver_general_settings") => array(
                        'target'            => md5("mailserver_general_settings"),
                        'id'            => md5("mailserver_general_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('mailserver/default'),
                        'name'          => __('Mail Server'),
                        'icon'       => '<span class="icon-mail-server fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
                    ),
                    md5("database_server_settings") => array(
                        'target'            => md5("database_server_settings"),
                        'id'            => md5("database_server_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('dbserver'),
                        'name'          => __('Database Server'),
                        'icon'       => '<span class="icon-database-server-settings fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></span>',
                    ),
                    md5("redis") => array(
                        'col'           => '1',
                        'row'           => '1',
                        'target'        => md5("redis"),
                        'id'            => md5("redis"),
                        'url'           => $this->uri->url_encrypt('redis'),
                        'description'   => '',
                        'name'          => __('Redis Management'),
                        'icon'          => '<span class="icon-server-security fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span></span>',
                        'childs'        => array(),
                        'has_child'     => false,
                    ),
                    md5("dns_server_settings") => array(
                        'target'            => md5("dns_server_settings"),
                        'id'            => md5("dns_server_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('dnsserver'),
                        'name'          => __('DNS Server'),
                        'icon'       => '<span class="icon-dns-server fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span></span>',
                    ),
                    md5("network_settings") => array(
                        'target'            => md5("network_settings"),
                        'id'            => md5("network_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('network'),
                        'name'          => __('Network Management'),
                        'icon'       => '<span class="icon-network-manager-2 fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span></span>',
                    ),
                    md5("service_management") => array(
                        'target'            => md5("service_management"),
                        'id'            => md5("service_management"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('services/management'),
                        'name'          => __('Service Management'),
                        'icon'       => '<span class="icon-client-settings fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span></span>',
                    ),
                    md5("server_packages") => array(
                        'target'            => md5("server_packages"),
                        'id'            => md5("server_packages"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('packages'),
                        'name'          => __('Server Packages'),
                        'icon'       => '<span class="icon-file-browser fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></span>',
                    ),
                    md5("update_settings") => array(
                        'target'            => md5("update_settings"),
                        'id'            => md5("update_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('update'),
                        'name'          => __('System Update'),
                        'icon'       => '<span class="icon-exchange fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span></span>',
                    ),
//                    md5("log_settings") => array(
//                        'target'            => md5("log_settings"),
//                        'id'            => md5("log_settings"),
//                        'has_child'     => false,
//                        'childs'        => array(),
//                        'url'          => $this->uri->url_encrypt('server/logsettings'),
//                        'name'          => __('Server Log Settings'),
//                        'icon'       => '<span class="icon-ftp-security fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span></span>',
//                    ),
                    md5("time_settings") => array(
                        'target'            => md5("time_settings"),
                        'id'            => md5("time_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('server/timesettings'),
                        'name'          => __('System Time'),
                        'icon'       => '<span class="icon-server-time fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
                    ),
                ),
            ),
            md5("back_migration") => array(
                'col'           => '2',
                'row'           => '2',
                'target'            => md5("back_migration"),
                'id'            => md5("back_migration"),
                'name'          => __('Backup & Migration'),
                'url'           => "",
                'description'   => '',
                'icon'          => '<span class="icon-resync fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
                'has_child'     => true,
                'childs'       => array(
                    md5("server_backup") => array(
                        'target'            => md5("server_backup"),
                        'id'            => md5("server_backup"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('serverbackup'),
                        'name'          => __('Backup Management'),
                        'icon'       => '<span class="icon-disk-lock fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
                    ),
                    md5("migration_settings") => array(
                        'target'            => md5("migration_settings"),
                        'id'            => md5("migration_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('migration'),
                        'name'          => __('Migration'),
                        'icon'       => '<span class="icon-exchange fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span></span>',
                    ),
                )
            ),
            md5("panel_settings") => array(
                'col'           => '3',
                'row'           => '2',
                'target'            => md5("panel_settings"),
                'id'            => md5("panel_settings"),
                'name'          => __('Panel Settings'),
                'url'           => "",
                'description'   => '',
                'icon'          => '<span class="icon-general-settings fs2"><span class="path1"></span><span class="path2"></span></span>',
                'has_child'     => true,
                'childs'       => array(
                    md5("blacklist_settings") => array(
                        'target'            => md5("blacklist_settings"),
                        'id'            => md5("blacklist_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('blacklist'),
                        'name'          => __('Blacklist Management'),
                        'icon'       => '<span class="icon-firewall fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                    ),
                    md5("api_settings") => array(
                        'target'            => md5("api_settings"),
                        'id'            => md5("api_settings"),
                        'has_child'     => false,
                        'childs'        => array(),
                        'url'          => $this->uri->url_encrypt('apisettings'),
                        'name'          => __('Api Management'),
                        'icon'       => '<span class="icon-cloud-settings2 fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span><span class="path40"></span><span class="path41"></span><span class="path42"></span><span class="path43"></span><span class="path44"></span><span class="path45"></span><span class="path46"></span><span class="path47"></span><span class="path48"></span><span class="path49"></span><span class="path50"></span><span class="path51"></span><span class="path52"></span><span class="path53"></span><span class="path54"></span><span class="path55"></span><span class="path56"></span><span class="path57"></span><span class="path58"></span><span class="path59"></span><span class="path60"></span><span class="path61"></span><span class="path62"></span><span class="path63"></span><span class="path64"></span><span class="path65"></span><span class="path66"></span><span class="path67"></span><span class="path68"></span><span class="path69"></span><span class="path70"></span><span class="path71"></span><span class="path72"></span><span class="path73"></span><span class="path74"></span><span class="path75"></span></span>',
                    ),
//                            array(
//                                'url'          => $this->uri->url_encrypt('systemapp'),
//                                'name'          => __('Application Management'),
//                                'icon'       => "",
//                            ),
                    md5("system_messages") => array(
                        'target'            => md5("system_messages"),
                        'id'            => md5("system_messages"),
                        'has_child'     => false,
                        'url'          => $this->uri->url_encrypt('messages'),
                        'name'          => __('System Messages'),
                        'icon'       => '<span class="icon-sys-messages fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span></span>',
                    ),
                    md5("server_resource") => array(
                        'target'            => md5("server_resource"),
                        'id'            => md5("server_resource"),
                        'has_child'     => false,
                        'url'          => $this->uri->url_encrypt('server/resource'),
                        'name'          => __('Resource Viewer'),
                        'icon'       => '<span class="icon-server-dashboard fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
                    ),
                    md5("session_management") => array(
                        'target'            => md5("session_management"),
                        'id'            => md5("session_management"),
                        'has_child'     => false,
                        'url'          => $this->uri->url_encrypt('session'),
                        'name'          => __('Access Management'),
                        'icon'       => '<span class="icon-firewall2 fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                    ),
                    md5("server_cron_settings") => array(
                        'target'            => md5("server_cron_settings"),
                        'id'            => md5("server_cron_settings"),
                        'has_child'     => false,
                        'url'          => $this->uri->url_encrypt('cron'),
                        'name'          => __('Scheduled Task'),
                        'icon'       => '<span class="icon-scheduled-tasks fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></span>',
                    ),
                    md5("support_management") => array(
                        'target'            => md5("support_management"),
                        'id'            => md5("support_management"),
                        'has_child'     => false,
                        'url'          => $this->uri->url_encrypt('support'),
                        'name'          => __('Help Support'),
                        'icon'       => '<span class="icon-technical-support fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
                    ),
                    md5("system_notification") => array(
                        'target'            => md5("system_notification"),
                        'id'            => md5("system_notification"),
                        'has_child'     => false,
                        'url'          => $this->uri->url_encrypt('system/notify'),
                        'name'          => __('Notifications & Alerts'),
                        'icon'       => '<span class="icon-notify-alert fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                    ),
                    md5("system_advanced_settings") => array(
                        'target'            => md5("system_advanced_settings"),
                        'id'            => md5("system_advanced_settings"),
                        'has_child'     => false,
                        'url'          => $this->uri->url_encrypt('system/advanced-settings'),
                        'name'          => __('Advanced Settings'),
                        'icon'       => '<span class="icon-settings fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></span>',
                    ),
                )
            )
        );
        return $buttons;
    }

    private function domainButtons($params): array
    {
        if((isset($params['nested'])) && ($params['nested'] == true)) {
            $domain_name = $params['nested_client_data']['user']['domain_info']['domain_name'];
            $append_link = "/domain/".$params['nested_client_data']['user']['domain_info']['domain_id'];
        }
        else {
            $domain_name = $params['client_data']['user']['domain_info']['domain_name'];
            $append_link = "";
        }
//        $sys_ip = SystemIpAddress::findFirst("is_default = '1'");
//        $default_ip = $sys_ip->ip_address;

        $buttons['buttons'] = array(
            md5('webhosting_general_settings') => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("webhosting_general_settings"),
                'id'            => md5("webhosting_general_settings"),
                'url'           => $this->uri->url_encrypt('webhosting'.$append_link),
                'description'   => '',
                'name'          => __('General Settings'),
                'icon'          => '<span class="icon-web-hostings-settings-2 fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('webhosting_aspnet_settings') => array(
                'col'           => '2',
                'row'           => '1',
                'target'        => md5("webhosting_aspnet_settings"),
                'id'            => md5("webhosting_aspnet_settings"),
                'url'           => $this->uri->url_encrypt('aspnet'.$append_link),
                'description'   => '',
                'name'          => __('ASP.NET Settings'),
                'icon'          => '<span class="icon-aspnet fs2"></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
//            md5('webhosting_php_settings') => array(
//                'col'           => '2',
//                'row'           => '1',
//                'target'        => md5("webhosting_php_settings"),
//                'id'            => md5("webhosting_php_settings"),
//                'url'           => $this->uri->url_encrypt('php'.$append_link),
//                'description'   => '',
//                'name'          => __('PHP Settings'),
//                'icon'          => '<span class="icon-php fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
//                'childs'        => array(),
//                'has_child'     => false,
//            ),
            md5('subdomains') => array(
                'col'           => '3',
                'row'           => '1',
                'target'        => md5("subdomains"),
                'id'            => md5("subdomains"),
                'url'           => $this->uri->url_encrypt('subdomain'.$append_link),
                'description'   => '',
                'name'          => __('Subdomains'),
                'icon'          => '<span class="icon-cloud-browse fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span><span class="path40"></span><span class="path41"></span><span class="path42"></span><span class="path43"></span><span class="path44"></span><span class="path45"></span><span class="path46"></span><span class="path47"></span><span class="path48"></span><span class="path49"></span><span class="path50"></span><span class="path51"></span><span class="path52"></span><span class="path53"></span><span class="path54"></span><span class="path55"></span><span class="path56"></span><span class="path57"></span><span class="path58"></span><span class="path59"></span><span class="path60"></span><span class="path61"></span><span class="path62"></span><span class="path63"></span><span class="path64"></span><span class="path65"></span><span class="path66"></span><span class="path67"></span><span class="path68"></span><span class="path69"></span><span class="path70"></span><span class="path71"></span><span class="path72"></span><span class="path73"></span><span class="path74"></span><span class="path75"></span><span class="path76"></span><span class="path77"></span><span class="path78"></span><span class="path79"></span><span class="path80"></span><span class="path81"></span><span class="path82"></span><span class="path83"></span><span class="path84"></span><span class="path85"></span><span class="path86"></span><span class="path87"></span><span class="path88"></span><span class="path89"></span><span class="path90"></span><span class="path91"></span><span class="path92"></span><span class="path93"></span><span class="path94"></span><span class="path95"></span><span class="path96"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('domain_databases') => array(
                'col'           => '4',
                'row'           => '1',
                'target'        => md5("domain_databases"),
                'id'            => md5("domain_databases"),
                'url'           => $this->uri->url_encrypt('database'.$append_link),
                'description'   => '',
                'name'          => __('Database Management'),
                'icon'          => '<span class="icon-database fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('domain_dns') => array(
                'col'           => '5',
                'row'           => '1',
                'target'        => md5("domain_dns"),
                'id'            => md5("domain_dns"),
                'url'           => $this->uri->url_encrypt('dns'.$append_link),
                'description'   => '',
                'name'          => __('DNS Management'),
                'icon'          => '<span class="icon-dns fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('domain_alias') => array(
                'col'           => '6',
                'row'           => '1',
                'target'        => md5("domain_alias"),
                'id'            => md5("domain_alias"),
                'url'           => $this->uri->url_encrypt('domainalias'.$append_link),
                'description'   => '',
                'name'          => __('Domain Alias Management'),
                'icon'          => '<span class="icon-web-hosting-settings fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('domain_certificate') => array(
                'col'           => '7',
                'row'           => '1',
                'target'        => md5("domain_certificate"),
                'id'            => md5("domain_certificate"),
                'url'           => $this->uri->url_encrypt('certificate'.$append_link),
                'description'   => '',
                'name'          => __('SSL Certificates Management'),
                'icon'          => '<span class="icon-control-security fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            // File Settings
            md5('virtual-dir-settings') => array(
                'col'           => '1',
                'row'           => '2',
                'target'        => md5("virtual-dir-settings"),
                'id'            => md5("virtual-dir-settings"),
                'url'           => $this->uri->url_encrypt('vdir'.$append_link),
                'description'   => '',
                'name'          => __('Virtual Directory Settings'),
                'icon'          => '<span class="icon-ftp-settings fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('protect_dir') => array(
                'col'           => '2',
                'row'           => '2',
                'target'        => md5("protect_dir"),
                'id'            => md5("protect_dir"),
                'url'           => $this->uri->url_encrypt('protectdir'.$append_link),
                'description'   => '',
                'name'          => __('Protect Directory'),
                'icon'          => '<span class="icon-accept-ftp fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('file_manager') => array(
                'col'           => '3',
                'row'           => '2',
                'target'        => md5("file_manager"),
                'id'            => md5("file_manager"),
                'url'           => $this->uri->url_encrypt('filemanager'.$append_link),
                'description'   => '',
                'name'          => __('File Management (FTP)'),
                'icon'          => '<span class="icon-file-server fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('domain_backup') => array(
                'col'           => '4',
                'row'           => '2',
                'target'        => md5("domain_backup"),
                'id'            => md5("domain_backup"),
                'url'           => $this->uri->url_encrypt('backup'.$append_link),
                'description'   => '',
                'name'          => __('Backup Management'),
                'icon'          => '<span class="icon-exchange fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            // Mail Settings
            md5('domain_mail_settings') => array(
                'col'           => '1',
                'row'           => '3',
                'target'        => md5("domain_mail_settings"),
                'id'            => md5("domain_mail_settings"),
                'url'           => $this->uri->url_encrypt('mail/mailsettings'.$append_link),
                'description'   => '',
                'name'          => __('Mail Settings'),
                'icon'          => '<span class="icon-mails fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('mail_accounts') => array(
                'col'           => '2',
                'row'           => '3',
                'target'        => md5("mail_accounts"),
                'id'            => md5("mail_accounts"),
                'url'           => $this->uri->url_encrypt('mail/accounts'.$append_link),
                'description'   => '',
                'name'          => __('Mail Accounts'),
                'icon'          => '<span class="icon-mail-accounts fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('webmail') => array(
                'col'           => '3',
                'row'           => '3',
                'target'        => md5("webmail"),
                'id'            => md5("webmail"),
                'url'       => 'http://'.'webmail.'.$domain_name,
                'description'   => '',
                'name'          => __('Webmail'),
                'icon'          => '<span class="icon-mail-server fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            // Advanced Settings
            md5('domain_tasks') => array(
                'col'           => '1',
                'row'           => '4',
                'target'        => md5("domain_tasks"),
                'id'            => md5("domain_tasks"),
                'url'           => $this->uri->url_encrypt('tasks'.$append_link),
                'description'   => '',
                'name'          => __('Scheduled Tasks'),
                'icon'          => '<span class="icon-scheduled-tasks fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
//                    array(
//                        'sublink'          => $this->uri->url_encrypt('applications'),
//                        'subname'          => __('Application Installer'),
//                    ),
            md5('domain_resource') => array(
                'col'           => '2',
                'row'           => '4',
                'target'        => md5("domain_resource"),
                'id'            => md5("domain_resource"),
                'url'           => $this->uri->url_encrypt('resources'.$append_link),
                'description'   => '',
                'name'          => __('Resource Viewer'),
                'icon'          => '<span class="icon-performance fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5('domain_statistics') => array(
                'col'           => '3',
                'row'           => '4',
                'target'        => md5("domain_statistics"),
                'id'            => md5("domain_statistics"),
                'url'           => $this->uri->url_encrypt('webstatistics'.$append_link),
                'description'   => '',
                'name'          => __('Web Statistics'),
                'icon'          => '<span class="icon-statistics fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
        );
        return $buttons;
    }

    private function clientButtons($params): array
    {
        if((isset($params['nested'])) && ($params['nested'] == true)) {
            $append_link = "/client/".$params['nested_client_data']['user']['info']['user_id'];
        }
        else {
            $append_link = "";
        }

        $buttons['buttons'] = array(
            md5("list_domains") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("list_domains"),
                'id'            => md5("list_domains"),
                'url'          => $this->uri->url_encrypt('domains'.$append_link),
                'description'   => '',
                'name'          => __('List Domains'),
                'icon'             => '<span class="icon-domains fs2"><span class="path1"></span><span class="path2"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("domain_create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("domain_create"),
                'id'            => md5("domain_create"),
                'url'          => $this->uri->url_encrypt('domains/create'.$append_link),
                'description'   => '',
                'name'          => __('Create Domain'),
                'icon'             => '<span class="icon-domain-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("serviceplans") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("serviceplans"),
                'id'            => md5("serviceplans"),
                'url'            => $this->uri->url_encrypt('serviceplans'.$append_link),
                'description'   => '',
                'name'          => __('List Serviceplans'),
                'icon'             => '<span class="icon-serviceplans fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            ),
            md5("serviceplan_create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("serviceplan_create"),
                'id'            => md5("serviceplan_create"),
                'url'          => $this->uri->url_encrypt('serviceplans/create'.$append_link),
                'description'   => '',
                'name'          => __('Create Serviceplan'),
                'icon'             => '<span class="icon-serviceplans-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            ),
            md5("client-backup-list") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("client-backup-list"),
                'id'            => md5("client-backup-list"),
                'url'           => $this->uri->url_encrypt('backupclient'.$append_link),
                'description'   => '',
                'name'          => __('Backup Manager'),
                'icon'          => '<span class="icon-exchange fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            )
        );
        return $buttons;
    }

    private function resellerButtons($params): array
    {
        if((isset($params['nested'])) && ($params['nested'] == true)) {
            $append_link = "/client/".$params['nested_client_data']['user']['info']['user_id'];
        }
        else {
            $append_link = "";
        }

        $buttons['buttons'] = array(
            md5("reseller-users") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("reseller-users"),
                'id'            => md5("reseller-users"),
                'url'           => $this->uri->url_encrypt('users'.$append_link),
                'description'   => '',
                'name'          => __('List Users'),
                'icon'          => '<span class="icon-users fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("reseller-users-create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("reseller-users-create"),
                'id'            => md5("reseller-users-create"),
                'url'           => $this->uri->url_encrypt('users/create'.$append_link),
                'description'   => '',
                'name'          => __('Create User'),
                'icon'          => '<span class="icon-user-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("list_domains") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("list_domains"),
                'id'            => md5("list_domains"),
                'url'          => $this->uri->url_encrypt('domains'.$append_link),
                'description'   => '',
                'name'          => __('List Domains'),
                'icon'             => '<span class="icon-domains fs2"><span class="path1"></span><span class="path2"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("domain_create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("domain_create"),
                'id'            => md5("domain_create"),
                'url'          => $this->uri->url_encrypt('domains/create'.$append_link),
                'description'   => '',
                'name'          => __('Create Domain'),
                'icon'             => '<span class="icon-domain-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>',
                'childs'        => array(),
                'has_child'     => false,
            ),
            md5("serviceplans") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("serviceplans"),
                'id'            => md5("serviceplans"),
                'url'            => $this->uri->url_encrypt('serviceplans'.$append_link),
                'description'   => '',
                'name'          => __('List Serviceplans'),
                'icon'             => '<span class="icon-serviceplans fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            ),
            md5("serviceplan_create") => array(
                'col'           => '1',
                'row'           => '1',
                'target'            => md5("serviceplan_create"),
                'id'            => md5("serviceplan_create"),
                'url'          => $this->uri->url_encrypt('serviceplans/create'.$append_link),
                'description'   => '',
                'name'          => __('Create Serviceplan'),
                'icon'             => '<span class="icon-serviceplans-add fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            ),
            md5("client-backup-list") => array(
                'col'           => '1',
                'row'           => '1',
                'target'        => md5("client-backup-list"),
                'id'            => md5("client-backup-list"),
                'url'           => $this->uri->url_encrypt('backupclient'.$append_link),
                'description'   => '',
                'name'          => __('Backup Manager'),
                'icon'          => '<span class="icon-exchange fs2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span></span>',
                'has_child'     => false,
                'childs'        => array(),
            )
        );
        return $buttons;
    }
}