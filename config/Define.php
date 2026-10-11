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
class Define
{
    public function defines()
    {
        define("URL_CRYPT", true);
        define("SESSION_DELIM", "|");

        define("SUPPORT_MAIL", "support@fpanel.net");

        $os_type = explode(" ",trim(php_uname()));

        if(strpos(trim(strtolower($os_type["0"])),LINUX) !== false) {
            $panel_dir = "/usr/local/".SYSTEM_EXTENSION;
            $panel_conf = $panel_dir."/etc/panel.conf";
            $panel_os_type = LINUX;
            $vhost_dir = "/var/www";
            $panel_backup_dir = $panel_dir."/backup";
        }
        else {
            $panel_dir = "c:/".SYSTEM_EXTENSION;
            $panel_conf = $panel_dir."/etc/panel.conf";
            $panel_os_type = WINDOWS;
            $vhost_dir = "c:/vhosts";
            $panel_backup_dir = str_replace("/", "\\", $panel_dir."/backup");
        }

        define("F_VERSION","2.0.0");
        define("VHOST_DIR", $vhost_dir);
        define("PANEL_DIR", $panel_dir);
        define("SYSTEM_CONFIG_FILE", $panel_conf);
        define("PANEL_ETC_DIR", PANEL_DIR."/etc");
        define("PANEL_OS_TYPE", $panel_os_type);
        define("STATIC_KEY", "7EDbOEyDQAdg8AjZjRgcX4KOExvmfuWP"); //@TODO: Crypto
        define("STATIC_IV", "5AfcicxDnO7H6u7K"); //@TODO: Crypto

        define("PANEL_HTDOC_DIR", PANEL_DIR."/httpd");
        define("PANEL_LOG_DIR", PANEL_HTDOC_DIR. "/var/logs/");
        define("PANEL_BACKUP_DIR", $panel_backup_dir);
        define("PANEL_LOG", PANEL_LOG_DIR. SYSTEM_EXTENSION."-panel.log");

        define("DATABASE_TYPES", [
            "pgsql" => "PostgreSQL",
            "mssql" => "Microsoft SQL",
            "mysql" => "MySQL",
        ]);

        if(!file_exists($panel_conf)) {
            echo "<style>pre {background: #fdf5ce none repeat scroll 0 0 !important;border-collapse: collapse !important;}pre{padding:10px} pre span{color:#da0024  !important}</style>";
            echo "<pre>";
            echo print_r("Configuration files missing...", true);
            echo "\n";
            echo "Line 40";
            echo "</pre>";
            exit(255);
        }
        $conf_content = file_get_contents($panel_conf);
        $conf_content = explode(" ", $conf_content);

        define("CRYPT_KEY", trim($conf_content[0]));
        define("CRYPT_IV", trim(substr($conf_content[1], 0, 16)));
        define("PANEL_SQL_PASS", trim($conf_content[2]));

        define("CLI_USERNAME" , CRYPT_KEY );
        define("CLI_PASSWORD" , CRYPT_IV );

        define("config_ini", ROOTPATH . "/config/config.ini");

        $demo = false;
        if(file_exists(ROOTPATH."/.demo")) {
            $demo = true;
        }
        define("demo", $demo);

        define("XML", "xml");
        define("JSON", "json");

        define("MAIL_SERVER_TYPES", [
            0 => POSTFIX,
            1 => MAILENABLE,
            2 => HMAIL,
            3 => MSEXCHANGE,
            4 => QMAIL,
        ]);

        define("USER_TYPES", [
            ROOT_USER => "Root",
            ADMIN_USER => "Admin",
            RESELLER_USER => "Reseller",
            CLIENT_USER => "Client",
            DOMAIN_USER => "Domain",
            EMAIL_USER => "Email",
            SUPPORT_USER => "Support",
        ]);

        define("STATUSES", [
            0 => "Passive",
            1 => "Active",
        ]);

        define("STATISTICS", [
            AWSTATS => "awstats",
            WEBALIZER => "webalizer",
        ]);

        define("PHP_VERSIONS", [
            PHP56 => "php56",
            PHP72 => "php72",
            PHP74 => "php74",
        ]);

        define("NET_VERSIONS", [
            v20 => "v20",
            v40 => "v40",
        ]);
        define("POOL_MODE", [
            CLASSIC => "classic",
            INTEGRATED => "integrated",
        ]);
        define("INSTALL_STATUS", [
            NOT_INSTALLED => "not_installed",
            INSTALLED => "installed",
        ]);

        define("FTP_USER_TYPES", [
            MAIN => "main",
            NATIVE => "native",
        ]);

        define("DB_TYPES", [
            "mysql"     => 1,
            "mariadb"   => 2,
            "pgsql"     => 3,
            "mssql"     => 4,
        ]);

        define("DB_TYPES_ARR", [
            MYSQL     => "mysql",
            MARIADB   => "mariadb",
            PGSQL     => "pgsql",
            MSSQL     => "mssql",
        ]);

        define("FTP_USER_TYPES_ARR", [
            MAIN     => "main",
            NATIVE   => "native",
        ]);

        define("DNS_RECORD_TYPES", [
            "A",
            "AAAA",
            "CNAME",
            "MX",
            "NS",
            "PTR",
            "SRV",
            "TXT",
            "CAA",
            "DS",
            "DNSKEY",
        ]);
    }
}