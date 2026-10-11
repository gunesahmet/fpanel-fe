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

class System extends Library
{
    public function getButtons($system_type, $server = false, $server_id = null): array
    {
        if($system_type != CLOUD) {
            $system_buttons = array(
                'system_management' => array(
                    'name'  => __('System Management'),
                    'sub' => array(
                        'licence' => array(
                            'name'  => __('Licence Management'),
                            'link'  => $this->uri->url_encrypt('system/licence'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-certificate-alt"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span></i>',
                            'type' => 'normal'
                        ),
                        'web' => array(
                            'name'  => __('Web Server Management'),
                            'link'  => $this->uri->url_encrypt('webserver/web'),
                            'class' => '',
                            'icon' => '<i class="fp fp-server-settings-alt-new"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'ftp' => array(
                            'name'  => __('FTP Server Management'),
                            'link'  => $this->uri->url_encrypt('ftp/settings'),
                            'class' => '',
                            'icon' => '<i class="fp fp-ftp"></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'mail' => array(
                            'name'  => __('Mail Server'),
                            'link'  => $this->uri->url_encrypt('mailserver/generalsettings'),
                            'class' => '',
                            'icon' => '<i class="fp fp-mail-settings"></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'database' => array(
                            'name'  => __('Database Server'),
                            'link'  => $this->uri->url_encrypt('dbserver'),
                            'class' => '',
                            'icon' => '<i class="fp-db-settings"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'dns' => array(
                            'name'  => __('DNS Server'),
                            'link'  => $this->uri->url_encrypt('dnsserver'),
                            'class' => '',
                            'icon' => '<i class="fp fp-dns-alt"></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'network' => array(
                            'name'  => __('Network Management'),
                            'link'  => $this->uri->url_encrypt('network'),
                            'class' => '',
                            'icon' => '<i class="fp fp-network-settings-alt-new"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span><span class="path40"></span><span class="path41"></span><span class="path42"></span><span class="path43"></span><span class="path44"></span><span class="path45"></span><span class="path46"></span><span class="path47"></span><span class="path48"></span><span class="path49"></span><span class="path50"></span><span class="path51"></span><span class="path52"></span><span class="path53"></span><span class="path54"></span><span class="path55"></span><span class="path56"></span><span class="path57"></span><span class="path58"></span><span class="path59"></span><span class="path60"></span><span class="path61"></span><span class="path62"></span><span class="path63"></span><span class="path64"></span><span class="path65"></span><span class="path66"></span><span class="path67"></span><span class="path68"></span><span class="path69"></span><span class="path70"></span><span class="path71"></span><span class="path72"></span><span class="path73"></span><span class="path74"></span><span class="path75"></span><span class="path76"></span><span class="path77"></span><span class="path78"></span><span class="path79"></span><span class="path80"></span><span class="path81"></span><span class="path82"></span><span class="path83"></span><span class="path84"></span><span class="path85"></span></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'services' => array(
                            'name'  => __('Service Management'),
                            'link'  => $this->uri->url_encrypt('services/management'),
                            'class' => '',
                            'icon' => '<i class="fp fp-server-settings-alt"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'components' => array(
                            'name'  => __('Server Packages'),
                            'link'  => $this->uri->url_encrypt('packages'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-server-pack"></i>',
                            'type' => 'normal'
                        ),
                        'update' => array(
                            'name'  => __('System Update'),
                            'link'  => $this->uri->url_encrypt('update'),
                            'class' => '',
                            'icon' => '<i class="fp fp-server-restore"></i>',
                            'id' => '',
                            'type' => 'normal'
                        ),
                        'time' => array(
                            'name'  => __('System Time'),
                            'link'  => $this->uri->url_encrypt('server/timesettings'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="far fa-calendar-alt"></i>',
                            'type' => 'normal'
                        ),
                        'optimize' => array(
                            'name'  => __('Optimize System'),
                            'link'  => $this->uri->url_encrypt('server/optimize'),
                            'class' => '',
                            'id' => 'confirm',
                            'icon' => '<i class="fas fa-sync-alt"></i>',
                            'type' => 'ajax'
                        ),
                        'reboot' => array(
                            'name'  => __('Reboot System'),
                            'link'  => $this->uri->url_encrypt('server/reboot'),
                            'class' => '',
                            'id' => 'confirm',
                            'icon' => '<i class="fas fa-power-off"></i>',
                            'type' => 'ajax'
                        ),
                    )
                ),
                'backup_migrations' => array(
                    'name'  => __('Backup') . ' & ' . __('Migration'),
                    'sub' => array(
                        'backup' => array(
                            'name'  => __('Backup Management'),
                            'link'  => $this->uri->url_encrypt('serverbackup'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-data-usage2"></i>',
                            'type' => 'normal'
                        ),
                        'migration' => array(
                            'name'  => __('Migration'),
                            'link'  => $this->uri->url_encrypt('serverbackup/migration/'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-server-migrate"></i>',
                            'type' => 'normal'
                        ),
                    )
                ),
                'panel_settings' => array(
                    'name'  => __('Panel Settings'),
                    'sub' => array(
                        'blacklist' => array(
                            'name'  => __('Blacklist Management'),
                            'link'  => $this->uri->url_encrypt('blacklist/ftpusers'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fas fa-users-slash"></i>',
                            'type' => 'normal'
                        ),
                        'api' => array(
                            'name'  => __('Api Management'),
                            'link'  => $this->uri->url_encrypt('apisettings'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-shared-server"></i>',
                            'type' => 'normal'
                        ),
                        'messages' => array(
                            'name'  => __('System Messages'),
                            'link'  => $this->uri->url_encrypt('messages'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fas fa-comment"></i>',
                            'type' => 'normal'
                        ),
                        'resource' => array(
                            'name'  => __('Resource Usage'),
                            'link'  => $this->uri->url_encrypt('server/resource'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-internet-speed"></i>',
                            'type' => 'normal'
                        ),
                        'access' => array(
                            'name'  => __('Access Management'),
                            'link'  => $this->uri->url_encrypt('session'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-user-shield"></i>',
                            'type' => 'normal'
                        ),
                        'cronjob' => array(
                            'name'  => __('Cronjob'),
                            'link'  => $this->uri->url_encrypt('cron'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-tasks"></i>',
                            'type' => 'normal'
                        ),
                        'support' => array(
                            'name'  => __('Support') . ' & ' . __('Help'),
                            'link'  => $this->uri->url_encrypt('support'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fp fp-technical-support"></i>',
                            'type' => 'normal'
                        ),
                        'notify' => array(
                            'name'  => __('Notifications') . ' & ' . __('Alerts'),
                            'link'  => $this->uri->url_encrypt('system/notify'),
                            'class' => '',
                            'id' => '',
                            'icon' => '<i class="fas fa-bell"></i>',
                            'type' => 'normal'
                        ),
                        'advanced_settings' => array(
                            'name'  => __('Advanced Settings'),
                            'link'  => $this->uri->url_encrypt('system/passwordsettings'),
                            'class' => '',
                            'id' => '',
                            'type' => 'normal',
                            'icon' => '<i class="fp fp-managedhosting"></i>',
                        ),
                    )
                ),
            );
        }
        else {
            if($server == false) {
                $system_buttons = array(
                    'network' => array(
                        'name'  => __('Network Management'),
                        'link'  => $this->uri->url_encrypt('network'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fp fp-network-settings-alt-new"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span><span class="path40"></span><span class="path41"></span><span class="path42"></span><span class="path43"></span><span class="path44"></span><span class="path45"></span><span class="path46"></span><span class="path47"></span><span class="path48"></span><span class="path49"></span><span class="path50"></span><span class="path51"></span><span class="path52"></span><span class="path53"></span><span class="path54"></span><span class="path55"></span><span class="path56"></span><span class="path57"></span><span class="path58"></span><span class="path59"></span><span class="path60"></span><span class="path61"></span><span class="path62"></span><span class="path63"></span><span class="path64"></span><span class="path65"></span><span class="path66"></span><span class="path67"></span><span class="path68"></span><span class="path69"></span><span class="path70"></span><span class="path71"></span><span class="path72"></span><span class="path73"></span><span class="path74"></span><span class="path75"></span><span class="path76"></span><span class="path77"></span><span class="path78"></span><span class="path79"></span><span class="path80"></span><span class="path81"></span><span class="path82"></span><span class="path83"></span><span class="path84"></span><span class="path85"></span></i>',
                        'type' => 'normal'
                    ),
                    'components' => array(
                        'name'  => __('Server Packages'),
                        'link'  => $this->uri->url_encrypt('packages'),
                        'class' => '',
                        'icon' => '<i class="fp fp-server-pack"></i>',
                        'id' => '',
                        'type' => 'normal'
                    ),
                    'update' => array(
                        'name'  => __('System Update'),
                        'link'  => $this->uri->url_encrypt('update'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fp fp-server-restore"></i>',
                        'type' => 'normal'
                    ),
                    'time' => array(
                        'name'  => __('System Time'),
                        'link'  => $this->uri->url_encrypt('server/timesettings'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="far fa-calendar-alt"></i>',
                        'type' => 'normal'
                    ),
                    'optimize' => array(
                        'name'  => __('Optimize System'),
                        'link'  => $this->uri->url_encrypt('server/optimize'),
                        'class' => '',
                        'id' => 'confirm',
                        'icon' => '<i class="fas fa-sync-alt"></i>',
                        'type' => 'ajax'
                    ),
                    'reboot' => array(
                        'name'  => __('Reboot System'),
                        'link'  => $this->uri->url_encrypt('server/reboot'),
                        'class' => '',
                        'id' => 'confirm',
                        'icon' => '<i class="fas fa-power-off"></i>',
                        'type' => 'ajax'
                    ),
                    'blacklist' => array(
                        'name'  => __('Blacklist Management'),
                        'link'  => $this->uri->url_encrypt('blacklist/ftpusers'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fas fa-users-slash"></i>',
                        'type' => 'normal'
                    ),
                    'api' => array(
                        'name'  => __('Api Management'),
                        'link'  => $this->uri->url_encrypt('apisettings'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fp fp-shared-server"></i>',
                        'type' => 'normal'
                    ),
                    'messages' => array(
                        'name'  => __('System Messages'),
                        'link'  => $this->uri->url_encrypt('messages'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fas fa-comment"></i>',
                        'type' => 'normal'
                    ),
                    'access' => array(
                        'name'  => __('Access Management'),
                        'link'  => $this->uri->url_encrypt('session'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fp fp-user-shield"></i>',
                        'type' => 'normal'
                    ),
                    'support' => array(
                        'name'  => __('Support') . ' & ' . __('Help'),
                        'link'  => $this->uri->url_encrypt('support'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fp fp-technical-support"></i>',
                        'type' => 'normal'
                    ),
                    'notify' => array(
                        'name'  => __('Notifications') . ' & ' . __('Alerts'),
                        'link'  => $this->uri->url_encrypt('system/notify'),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fas fa-bell"></i>',
                        'type' => 'normal'
                    ),
                    'password_str' => array(
                        'name'  => __('Advanced Settings'),
                        'link'  => $this->uri->url_encrypt('system/passwordsettings'),
                        'class' => '',
                        'icon' => '<i class="fp fp-managedhosting"></i>',
                        'id' => '',
                        'type' => 'normal'
                    ),
                    'logs' => array(
                        'name'  => __('Logs'),
                        'link'  => $this->uri->url_encrypt('logs/server/'.$server_id),
                        'class' => '',
                        'id' => '',
                        'icon' => '<i class="fp fp-details"></i>',
                        'type' => 'ajax'
                    ),
                );
            }
            else {
                $system_buttons = array(
                    'system_management' => array(
                        'name'  => __('System Management'),
                        'sub' => array(
                            'licence' => array(
                                'name'  => __('Licence Management'),
                                'link'  => $this->uri->url_encrypt('system/licence/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-certificate-alt"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span></i>',
                                'type' => 'normal'
                            ),
                            'web' => array(
                                'name'  => __('Web Server Management'),
                                'link'  => $this->uri->url_encrypt('webserver/web/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-server-settings-alt-new"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span></i>',
//                                'icon' => '<i class="fp fp-server-settings-alt-new"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span></i><i class="fa fa-cogs second"></i>',
                                'type' => 'normal'
                            ),
                            'ftp' => array(
                                'name'  => __('FTP Server Management'),
                                'link'  => $this->uri->url_encrypt('ftp/settings/server/'.$server_id),
                                'class' => '',
                                'icon' => '<i class="fp fp-ftp"></i>',
                                'id' => '',
                                'type' => 'normal'
                            ),
                            'mail' => array(
                                'name'  => __('Mail Server'),
                                'link'  => $this->uri->url_encrypt('mailserver/generalsettings/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-mail-settings"></i>',
                                'type' => 'normal'
                            ),
                            'database' => array(
                                'name'  => __('Database Server'),
                                'link'  => $this->uri->url_encrypt('dbserver/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-db-settings"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i>',
                                'type' => 'normal'
                            ),
                            'dns' => array(
                                'name'  => __('DNS Server'),
                                'link'  => $this->uri->url_encrypt('dnsserver/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-dns-alt"></i>',
                                'type' => 'normal'
                            ),
                            'network' => array(
                                'name'  => __('Network Management'),
                                'link'  => $this->uri->url_encrypt('network/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-network-settings-alt-new"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span><span class="path27"></span><span class="path28"></span><span class="path29"></span><span class="path30"></span><span class="path31"></span><span class="path32"></span><span class="path33"></span><span class="path34"></span><span class="path35"></span><span class="path36"></span><span class="path37"></span><span class="path38"></span><span class="path39"></span><span class="path40"></span><span class="path41"></span><span class="path42"></span><span class="path43"></span><span class="path44"></span><span class="path45"></span><span class="path46"></span><span class="path47"></span><span class="path48"></span><span class="path49"></span><span class="path50"></span><span class="path51"></span><span class="path52"></span><span class="path53"></span><span class="path54"></span><span class="path55"></span><span class="path56"></span><span class="path57"></span><span class="path58"></span><span class="path59"></span><span class="path60"></span><span class="path61"></span><span class="path62"></span><span class="path63"></span><span class="path64"></span><span class="path65"></span><span class="path66"></span><span class="path67"></span><span class="path68"></span><span class="path69"></span><span class="path70"></span><span class="path71"></span><span class="path72"></span><span class="path73"></span><span class="path74"></span><span class="path75"></span><span class="path76"></span><span class="path77"></span><span class="path78"></span><span class="path79"></span><span class="path80"></span><span class="path81"></span><span class="path82"></span><span class="path83"></span><span class="path84"></span><span class="path85"></span></i>',
                                'type' => 'normal'
                            ),
                            'services' => array(
                                'name'  => __('Service Management'),
                                'link'  => $this->uri->url_encrypt('services/management/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-server-settings-alt"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span><span class="path11"></span><span class="path12"></span><span class="path13"></span><span class="path14"></span><span class="path15"></span><span class="path16"></span><span class="path17"></span><span class="path18"></span><span class="path19"></span><span class="path20"></span><span class="path21"></span><span class="path22"></span><span class="path23"></span><span class="path24"></span><span class="path25"></span><span class="path26"></span></i>',
                                'type' => 'normal'
                            ),
                            'components' => array(
                                'name'  => __('Server Packages'),
                                'link'  => $this->uri->url_encrypt('packages/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-server-pack"></i>',
                                'type' => 'normal'
                            ),
                            'time' => array(
                                'name'  => __('System Time'),
                                'link'  => $this->uri->url_encrypt('server/timesettings/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="far fa-calendar-alt"></i>',
                                'type' => 'normal'
                            ),
                            'optimize' => array(
                                'name'  => __('Optimize System'),
                                'link'  => $this->uri->url_encrypt('server/optimize/server/'.$server_id),
                                'class' => '',
                                'id' => 'confirm',
                                'icon' => '<i class="fas fa-sync-alt"></i>',
                                'type' => 'ajax'
                            ),
                            'reboot' => array(
                                'name'  => __('Reboot System'),
                                'link'  => $this->uri->url_encrypt('server/reboot/server/'.$server_id),
                                'class' => '',
                                'icon' => '<i class="fas fa-power-off"></i>',
                                'id' => 'confirm',
                                'type' => 'ajax'
                            ),
                        )
                    ),
                    'backup_migrations' => array(
                        'name'  => __('Backup') . ' & ' . __('Migration'),
                        'sub' => array(
                            'backup' => array(
                                'name'  => __('Backup Management'),
                                'link'  => $this->uri->url_encrypt('serverbackup/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-data-usage2"></i>',
                                'type' => 'normal'
                            ),
                            'migration' => array(
                                'name'  => __('Migration'),
                                'link'  => $this->uri->url_encrypt('serverbackup/migration/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-server-migrate"></i>',
                                'type' => 'normal'
                            ),
                        )
                    ),
                    'panel_settings' => array(
                        'name'  => __('Panel Settings'),
                        'sub' => array(
                            'resource' => array(
                                'name'  => __('Resource Usage'),
                                'link'  => $this->uri->url_encrypt('server/resource/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-internet-speed"></i>',
                                'type' => 'normal'
                            ),
                            'cronjob' => array(
                                'name'  => __('Cronjob'),
                                'link'  => $this->uri->url_encrypt('cron/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-tasks"></i>',
                                'type' => 'normal'
                            ),
                            'logs' => array(
                                'name'  => __('Logs'),
                                'link'  => $this->uri->url_encrypt('logs/server/'.$server_id),
                                'class' => '',
                                'id' => '',
                                'icon' => '<i class="fp fp-details"></i>',
                                'type' => 'normal'
                            ),
                        )
                    ),
                );
                if(!empty($types)) {
                    if(!$types["WEB"]) {
                        unset($system_buttons['system_management']['sub']['web']);
                        unset($system_buttons['system_management']['sub']['ftp']);
                    }
                    if(!$types["DATABASE"]) {
                        unset($system_buttons['system_management']['sub']['database']);
                    }
                    if(!$types["MAIL"]) {
                        unset($system_buttons['system_management']['sub']['mail']);
                    }
                    if(!$types["DNS"]) {
                        unset($system_buttons['system_management']['sub']['dns']);
                    }
                }
            }
        }

        return $system_buttons;
    }
}