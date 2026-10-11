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
 */namespace FPanel\Server;

use FPanel\Library\Library;
use FPanel\Models\SystemPackages;
use FPanel\Process\Services as Servicemng;

class Services extends Library
{
    public function servicesList($server_id, $server_os): array
    {
        $servicemng = new Servicemng();
        if($server_id != "1") {
            $servicemng->setServerId($server_id);
        }
        $services = $servicemng->serviceStatus();

        $service_list = json_decode($services,true);

        $system_pkg = SystemPackages::find("server_id = '$server_id' and status='2'");
        $packages = $system_pkg->toArray();

        return $this->packageServices($service_list, $packages, $server_os);
    }

    private function packageServices($services, &$packages, $server_os): array
    {
        $service_list = [];
        foreach($services as $group_name => $service_group) {
            if($server_os == WINDOWS) {
                if($group_name == "WEB") {
                    $service_list["WEB"][] = array(
                        'service_name' => "W3SVC",
                        'service_desc' => "IIS Web Server",
                        'service_status' => $services["WEB"]['iis'],
                        'service_state' => "installed",
                    );
                }
                if($group_name == "SSH") {
                    $service_list["SSH"][] = array(
                        'service_name' => "TermService",
                        'service_desc' => "Remote Desktop",
                        'service_status' => $services["SSH"]['rdp'],
                        'service_state' => "installed",
                    );
                }
                if($group_name == "CRON") {
                    $service_list["CRON"][] = array(
                        'service_name' => "Schedule",
                        'service_desc' => "Task Scheduler",
                        'service_status' => $services["CRON"]['schedule'],
                        'service_state' => "installed",
                    );
                }
                if($group_name == "FTP") {
                    $service_list["FTP"][] = array(
                        'service_name' => "FTPSVC",
                        'service_desc' => "IIS FTP",
                        'service_status' => $services["FTP"]['FTPSVC'],
                        'service_state' => "installed",
                    );
                }
                if($group_name == "DATABASE") {
                    $service_list["DATABASE"][] = array(
                        'service_name' => "MySQLServer",
                        'service_desc' => "MySQL Server",
                        'service_status' => $services["DATABASE"]['MySQLServer'],
                        'service_state' => "installed",
                    );
                    $service_list["DATABASE"][] = array(
                        'service_name' => "MSSQL\$SQLEXPRESS",
                        'service_desc' => "Microsoft SQL Server",
                        'service_status' => $services["DATABASE"]['MSSQL$SQLEXPRESS'],
                        'service_state' => "installed",
                    );
                }

                foreach($service_group as $service_name => $service_status) {
                    foreach($packages as $pkg) {
                        if(($group_name == "SMTP") || ($group_name == "IMAP")) {
                            if(($service_name == "MESMTPCS") || ($service_name == "MEIMAPS")) {
                                $pkg_name = "MailEnable";
                            }
                            else if(($service_name == "HMSMTP") || ($service_name == "HMIMAP")) {
                                $pkg_name = "hMailServer";
                            }
                            else {
                                $pkg_name = $service_name;
                            }

                            if(strpos($pkg['package_name'], $pkg_name) !== false) {
                                if($pkg['status'] == INSTALLED) {
                                    $installed_status = "installed";
                                }
                                else {
                                    $installed_status = "not-installed";
                                    $service_status = "off";
                                }

                                $service_list[$group_name][] = array(
                                    'service_name' => $service_name,
                                    'service_desc' => $pkg['package_desc'] . " ($group_name)",
                                    'service_status' => $service_status,
                                    'service_state' => $installed_status,
                                );
                            }
                        }
                        else {
                            if(strpos($pkg['package_name'], $service_name) !== false) {
                                if($pkg['status'] == INSTALLED) {
                                    $installed_status = "installed";
                                }
                                else {
                                    $installed_status = "not-installed";
                                    $service_status = "off";
                                }

                                $service_list[$group_name][] = array(
                                    'service_name' => $service_name,
                                    'service_desc' => $pkg['package_desc'],
                                    'service_status' => $service_status,
                                    'service_state' => $installed_status,
                                );
                            }
                        }
                    }
                }
            }
            else {
                $service_names = array(
                    'spamd' => array(
                        'package_name' => 'spamassassin',
                        'name' => 'Spamassassin',
                    ),
                    'clamav' => array(
                        'package_name' => 'clamav',
                        'name' => 'ClamAV'
                    ),
                    'amavis' => array(
                        'package_name' => 'amavis',
                        'name' => 'Amavis'
                    ),
                    'sshd' => array(
                        'package_name' => 'openssh',
                        'name' => 'OpenSSH'
                    ),
                    'dropbear' => array(
                        'package_name' => 'dropbear',
                        'name' => 'Drop Bear'
                    ),
                    'crond' => array(
                        'package_name' => 'crontabs',
                        'name' => 'Crontab'
                    ),
                    'dovecot' => array(
                        'package_name' => 'dovecot',
                        'name' => 'Dovecot POP3/IMAP Server'
                    ),
                    'qmail' => array(
                        'package_name' => 'qmail',
                        'name' => 'QMail SMTP Server'
                    ),
                    'postfix' => array(
                        'package_name' => 'postfix',
                        'name' => 'Postfix SMTP Server'
                    ),
                    'proftpd' => array(
                        'package_name' => 'proftpd',
                        'name' => 'ProFTP FTP Server'
                    ),
                    'vsftpd' => array(
                        'package_name' => 'vsftpd',
                        'name' => 'vsftpd FTP Server'
                    ),
                    'bind' => array(
                        'package_name' => 'bind',
                        'name' => 'BIND DNS Server'
                    ),
                    'djbdns' => array(
                        'package_name' => 'djbdns',
                        'name' => 'DjbDNS Server'
                    ),
                    'powerdns' => array(
                        'package_name' => 'powerdns',
                        'name' => 'Power DNS Server'
                    ),
                    'maria' => array(
                        'package_name' => 'mariadb',
                        'name' => 'Maria Database Server'
                    ),
                    'mysql' => array(
                        'package_name' => 'mysql-community-server',
                        'name' => 'MySQL Database Server'
                    ),
                    'postgresql' => array(
                        'package_name' => 'postgresql-server',
                        'name' => 'PostgreSQL Database Server'
                    ),
                    'nginx' => array(
                        'package_name' => 'nginx',
                        'name' => 'Nginx HTTP Server'
                    ),
                    'lighttpd' => array(
                        'package_name' => 'lighttpd',
                        'name' => 'Lighttpd HTTP Server'
                    ),
                    'apache2' => array(
                        'package_name' => 'apache2',
                        'name' => 'Apache HTTP Server'
                    ),
                    'php_fpm' => array(
                        'package_name' => 'php-fpm',
                        'name' => 'PHP-FPM (FastCGI Process Manager)'
                    ),
                );

                foreach($service_group as $service_name => $service_status) {
                    $result = $this->env->searchArray($service_names[$service_name]['package_name'],'package_name', $packages);

                    if($service_name == "nginx") {
                        if(empty($result)) {
                            $result = $this->env->searchArray("nginx-extras",'package_name', $packages);
                        }
                    }
                    if($service_name == "maria") {
                        if(empty($result))
                            $result = $this->env->searchArray("mariadb-server",'package_name', $packages);
                    }
                    if($service_name == "bind") {
                        if(empty($result))
                            $result = $this->env->searchArray("bind9",'package_name', $packages);
                    }
                    if($service_name == "proftpd") {
                        if(empty($result))
                            $result = $this->env->searchArray("proftpd-basic",'package_name', $packages);
                    }
                    if($service_name == "dovecot") {
                        if(empty($result))
                            $result = $this->env->searchArray("dovecot-core",'package_name', $packages);
                    }
                    if($service_name == "crond") {
                        if(empty($result))
                            $result = $this->env->searchArray("cron",'package_name', $packages);
                    }
                    if($service_name == "sshd") {
                        if(empty($result))
                            $result = $this->env->searchArray("openssh-server",'package_name', $packages);
                    }
                    if($service_name == "postgresql") {
                        if(empty($result))
                            $result = $this->env->searchArrayWithRegex("postgresql-[\d]",'package_name', $packages);
                    }
                    if($service_name == "php_fpm") {
                        if(empty($result)) {
                            $result = $this->env->searchArrayWithRegex("php.*-fpm",'package_name', $packages);
                        }
                    }
                    if($service_name == "apache2") {
                        if(empty($result)) {
                            $result = $this->env->searchArrayWithRegex("httpd",'package_name', $packages);
                        }
                    }
                    if(strpos(strtolower($server_os), "ubuntu") !== false) {
                        if($service_name == "mysql") {
                            if(empty($result)) {
                                $result = $this->env->searchArray("mysql-server-.*",'package_name', $packages);
                            }
                        }
                    }
                    $srv_srvs = array(
                        'service_name' => $service_name,
                        'service_desc' => $service_names[$service_name]['name'],
                        'service_status' => "off",
                        'service_state' => "not-installed",
                    );
                    if(!empty($result)) {
                        $srv_srvs['service_state'] = 'installed';
                        $srv_srvs['service_status'] = $service_status;
                        $srv_srvs['package_name'] = $service_status;
                    }
                    $service_list[$group_name][] = $srv_srvs;
                }
            }
        }
        return $service_list;
    }

    public function start($server_id, $service)
    {
        $servicemng = new Servicemng();
        $servicemng->setServerId($server_id);
        $servicemng->serviceStart($service);
    }

    public function stop($server_id, $service)
    {
        $servicemng = new Servicemng();
        $servicemng->setServerId($server_id);
        $servicemng->serviceStop($service);
    }

    public function restart($server_id, $service)
    {
        $servicemng = new Servicemng();
        $servicemng->setServerId($server_id);
        $servicemng->serviceRestart($service);
    }
}

