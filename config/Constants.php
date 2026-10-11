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
class Constants
{
    public function load()
    {
        define("ROOT_USER", 0);
        define("ADMIN_USER", 1);
        define("RESELLER_USER", 2);
        define("CLIENT_USER", 3);
        define("DOMAIN_USER", 4);
        define("EMAIL_USER", 5);
        define("SUPPORT_USER", 6);

        define("RESELLERS", 2);
        define("CLIENTS", 3);

        define("PASSIVE", 0);
        define("ACTIVE", 1);
        define("UPDATED", 2);
        define("CLOSED", 3);

        define("UNLIMITED", "unlimited");

        define("CLOUD", "cloud");
        define("STANDALONE", "standalone");

        define("DOMAIN_CREATE_FAILED", 4);

        define("TRAFFIC_LIMIT_EXCEEDED", 2);
        define("DISK_LIMIT_EXCEEDED", 3);
        define("MANUAL_PASSIVE_BY_ADMIN", 4);
        define("MANUAL_PASSIVE_BY_OWNER", 5);
        define("ERROR_STATUS", 6);

        define("NO_OVERUSE", "0");
        define("OVERUSE", "1");

        define("BACKUP_WAITING", 0);
        define("BACKUP_FINISHED", 1);
        define("BACKUP_RESTORING", 2);
        define("BACKUP_READING", 3);
        define("BACKUP_FAILED", 4);
        define("RESTORE_FAILED", 5);

        define("POSTFIX", 0);
        define("MAILENABLE", 1);
        define("HMAIL", 2);
        define("MSEXCHANGE", 3);
        define("QMAIL", 4);

        define("USER", 0);
        define("DOMAIN", 1);

        define("LINUX", "linux");
        define("WINDOWS", "windows");

        define("CLIENT", "client");
        define("RESELLER", "reseller");

        define("DIR_SEPARATOR", "/");

        define("SYSTEM_EXTENSION", "fpanel");
        define("PORTAL_URL", "fpanel.net");
        define("DATABASE_NAME", "fpanel");
        define("USER_SQL_ADMIN", "fpaneladmin");

        define("OFF", "off");
        define("ON", "on");

        define("DOMAIN_CRON", 0);
        define("SERVER_CRON", 1);

        define("MAIN", 1);
        define("NATIVE", 2);

        define("FORWARD", "1");
        define("REJECT", "2");

        define("NOT_ALLOWED", "0");
        define("ALLOWED", "1");

        define("SHARED", 1);
        define("EXCLUSIVE", 2);
        define("SSL", 3);
        define("MAIN_CLOUD", 4);

        define("CIDR", "0");
        define("NORMAL", "1");

        define("NO_DEFAULT", "0");
        define("IS_DEFAULT", "1");

        define("NOT_INITED", "0");
        define("INITED", "1");

        define("v20", "1");
        define("v40", "2");

        define("CLASSIC", "1");
        define("INTEGRATED", "2");

        define("NOT_INSTALLED", 1);
        define("INSTALLED", 2);

        define("NO_STATS", "0");
        define("AWSTATS", "1");
        define("WEBALIZER", "2");

        define("PHP56", "1");
        define("PHP72", "2");
        define("PHP74", "3");

        define("DOMAIN_BACKUP", 1);
        define("CLIENT_BACKUP", 2);
        define("SERVER_BACKUP", 3);

        define("MYSQL", 1);
        define("MARIADB", 2);
        define("PGSQL", 3);
        define("MSSQL", 4);

        define("MSDNS", "msdns");
        define("BIND", "bind");
        define("DJBDNS", "djbdns");
    }
}