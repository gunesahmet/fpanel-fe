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

trait Domain
{
    private $domain_sql;

    private $domain_user_id;

    public function setDomainUser($domain_user_id): Builder
    {
        $this->domain_user_id = $domain_user_id;
        return $this;
    }

    public function domainUser(): Builder
    {
        $this->domain_sql = " users.password_last_set,
        users.last_login,
        users.last_login_ip,
        users.must_change_password,
        users.created_date,
        users.expire_date,
        users.`language`,
        users.interface,
        users.parent as parent_id,
        users.status_msg,
        users.changed_data,
        users.username,
        users.`password`,
        userdata.fullname,
        userdata.email,
        userdata.company,
        userdata.fax,
        userdata.telephone,
        userdata.address,
        userdata.city,
        userdata.region,
        userdata.zipcode,
        userdata.country,";
        return $this;
    }

    public function domainInfo(): Builder
    {
        $this->domain_sql = $this->domain_sql . " domains.ftp_user_id,
        domains.id as domain_id,
        domains.name as domain_name,
        domains.ssl_cert_id,
        domains.ipaddr_id,
        domains.created_date,
        domains.expire_date,
        domains.`status`,
        domains.status_msg,
        domains.prefix,
        domains.web_server_id,
        domains.dns_server_id,
        domains.mail_server_id,
        ip_address.ip_address,
        ftp_users.username as ftpname,
        ftp_users.`password` as ftp_password,
        domain_services.`web` as web_service,
        domain_services.`dns` as dns_service,
        domain_services.`mail` as mail_service,
        ";
        return $this;

    }

    public function domainLimits(): Builder
    {
        $this->domain_sql = $this->domain_sql . ' limits.client_limit,
        limits.domain_limit,
        limits.disk_limit,
        limits.traffic_limit,
        limits.subdomain_limit,
        limits.domain_alias_limit,
        limits.email_box_limit,
        limits.email_box_size_limit,
        limits.email_forward_limit,
        limits.email_groups_limit,
        limits.email_autoresponder_limit,
        limits.email_lists_limit,
        limits.database_limit,
        limits.database_size_limit,
        limits.template_limit,
        limits.access_panel,
        limits.man_client,
        limits.man_domain,
        limits.man_subdomain,
        limits.man_domain_alias,
        limits.man_email_alias,
        limits.man_hosting_settings,
        limits.man_task,
        limits.man_dns,
        limits.man_mailing_lists,
        limits.man_spam_filters,
        limits.man_backup,
        limits.man_email,
        limits.man_database,
        limits.man_protect_dir,
        limits.man_ssl,
        limits.man_template,
        limits.overuse,
        limits.php,
        limits.php_version,
        limits.cgi,
        limits.perl,
        limits.python,
        limits.fast_cgi,
        limits.statistic_support,
        limits.statistic_type,
        limits.asp,
        limits.aspnet_core,
        limits.aspnet,
        limits.aspnet_version,
        limits.dedicated_pool,
        limits.max_cpu,
        limits.application_pool_mode,
        limits.ssl,';
        return $this;

    }

    public function domainStatistics(): Builder
    {
        $this->domain_sql = $this->domain_sql . " domain_statistics.mail,
        domain_statistics.db,
        domain_statistics.httpdoc,
        domain_statistics.disk,
        domain_statistics.traffic,
        domain_statistics.`status` as statistic_status,
        domain_statistics.`logs`,";
        return $this;

    }

    public function domainUsage(): Builder
    {
        $this->domain_sql = $this->domain_sql . " (SELECT COUNT(*) FROM subdomains WHERE subdomains.domain_id =  domains.id) as used_subdomains,
                            (SELECT COUNT(*) FROM domain_alias WHERE domain_alias.domain_id =  domains.id) as used_domain_alias,
                            (SELECT COUNT(*) FROM mails WHERE mails.domain_id =  domains.id) as used_email,
                            (SELECT COUNT(*) FROM `databases` WHERE `databases`.domain_id =  domains.id) as used_database,
                            (SELECT COUNT(*) FROM mails INNER JOIN mail_alias ON mails.id = mail_alias.mail_id WHERE mails.domain_id = domains.id) as used_email_alias";
        return $this;

    }

    public function domainSql(): string
    {
        $this->domain_sql = "SELECT " . $this->domain_sql . " FROM users ";
        $this->domain_sql = $this->domain_sql . " INNER JOIN domains ON users.username = domains.`name`";
        $this->domain_sql = $this->domain_sql . " INNER JOIN client_limits_permissions AS limits ON domains.id = limits.domain_id";
        $this->domain_sql = $this->domain_sql . " INNER JOIN ip_address ON domains.ipaddr_id = ip_address.id";
        $this->domain_sql = $this->domain_sql . " INNER JOIN ftp_users ON domains.ftp_user_id = ftp_users.id";
        $this->domain_sql = $this->domain_sql . " INNER JOIN userdata ON users.id = userdata.user_id";
        $this->domain_sql = $this->domain_sql . " INNER JOIN domain_statistics ON domains.id = domain_statistics.domain_id";
        $this->domain_sql = $this->domain_sql . " INNER JOIN domain_services ON domains.id = domain_services.domain_id";
        $this->domain_sql = $this->domain_sql . " WHERE users.id = '$this->domain_user_id'";

        $domain_sql = $this->domain_sql;
        $this->domain_sql = null;
        $this->domain_user_id = null;
        return $domain_sql;
    }
}