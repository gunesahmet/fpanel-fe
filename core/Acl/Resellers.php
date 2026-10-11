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

trait Resellers
{
    private $reseller_sql;

    private $reseller_user_id;

    public function setResellerUser($reseller_user_id): Builder
    {
        $this->reseller_user_id = $reseller_user_id;
        return $this;
    }

    public function resellerUser(): Builder
    {
        $database_name = DATABASE_NAME;
        $this->reseller_sql = " $database_name.users.password_last_set,
        $database_name.users.last_login,
        $database_name.users.last_login_ip,
        $database_name.users.must_change_password,
        $database_name.users.created_date,
        $database_name.users.expire_date,
        $database_name.users.`language`,
        $database_name.users.`status_msg`,
        $database_name.users.interface,
        $database_name.users.parent as parent_id,
        $database_name.users.changed_data,
        $database_name.users.username,
        $database_name.users.`password`,
        $database_name.userdata.fullname,
        $database_name.userdata.email,
        $database_name.userdata.company,
        $database_name.userdata.fax,
        $database_name.userdata.telephone,
        $database_name.userdata.address,
        $database_name.userdata.city,
        $database_name.userdata.region,
        $database_name.userdata.zipcode,
        $database_name.userdata.country,";
        return $this;
    }

    public function resellerInfo(): Builder
    {
        $this->reseller_sql = $this->reseller_sql . " (SELECT GROUP_CONCAT( ip_address.id, ':', ip_address.ip_address ) FROM users INNER JOIN client_ip_address ON users.id = client_ip_address.client_id INNER JOIN ip_address ON client_ip_address.ipaddr_id = ip_address.id WHERE users.id = client_ip_address.client_id) AS ip_adress_list,";
        return $this;

    }

    public function resellerLimits(): Builder
    {
        $this->reseller_sql = $this->reseller_sql . ' limits.client_limit,
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

    public function resellerStatistics(): Builder
    {
        $this->reseller_sql = $this->reseller_sql . " client_statistics.mail,
        client_statistics.db,
        client_statistics.httpdoc,
        client_statistics.disk,
        client_statistics.traffic,
        client_statistics.`status` as statistic_status,
        client_statistics.`logs`,";
        return $this;
    }

    public function resellerUsage(): Builder
    {
        $database_name = DATABASE_NAME;
        $this->reseller_sql = $this->reseller_sql . " (SELECT COUNT(*) FROM $database_name.users WHERE $database_name.users.parent =  '$this->reseller_user_id' AND $database_name.users.type < '4') as used_clients,
                            ((SELECT COUNT(*) FROM domains WHERE domains.client_id = '$this->reseller_user_id') + (SELECT COUNT(*) FROM domains INNER JOIN users AS clients ON clients.id = domains.client_id WHERE clients.parent = '$this->reseller_user_id')) as used_domains,
                            ((SELECT COUNT(*) FROM serviceplans WHERE serviceplans.client_id = '$this->reseller_user_id') + (SELECT COUNT(*) FROM serviceplans INNER JOIN users AS clients ON clients.id = serviceplans.client_id WHERE clients.parent = '$this->reseller_user_id')) as used_templates,
                            ((SELECT COUNT(*) FROM subdomains INNER JOIN domains ON domains.id = subdomains.domain_id WHERE domains.client_id = '$this->reseller_user_id') + (SELECT COUNT(*) FROM subdomains INNER JOIN domains ON domains.id = subdomains.domain_id INNER JOIN users ON users.id = domains.client_id WHERE users.parent = '$this->reseller_user_id')) as used_subdomains,
                            ((SELECT COUNT(*) FROM domain_alias INNER JOIN domains ON domains.id = domain_alias.domain_id WHERE domains.client_id = '$this->reseller_user_id') + (SELECT COUNT(*) FROM domain_alias INNER JOIN domains ON domains.id = domain_alias.domain_id INNER JOIN users ON users.id = domains.client_id WHERE users.parent = '$this->reseller_user_id')) as used_domain_alias,
                            ((SELECT COUNT(*) FROM mails INNER JOIN domains ON domains.id = mails.domain_id WHERE domains.client_id = '$this->reseller_user_id') + (SELECT COUNT(*) FROM mails INNER JOIN domains ON domains.id = mails.domain_id INNER JOIN users ON users.id = domains.client_id WHERE users.parent = '$this->reseller_user_id')) as used_email,
                            ((SELECT COUNT(*) FROM `databases` INNER JOIN domains ON domains.id = `databases`.domain_id WHERE domains.client_id = '$this->reseller_user_id') + (SELECT COUNT(*) FROM `databases` INNER JOIN domains ON domains.id = `databases`.domain_id INNER JOIN users ON users.id = domains.client_id WHERE users.parent = '$this->reseller_user_id')) as used_database,
                            ((SELECT COUNT(*) FROM mail_alias INNER JOIN mails ON mails.id = mail_alias.mail_id INNER JOIN domains ON domains.id = mails.domain_id WHERE domains.client_id = '$this->reseller_user_id') + (SELECT COUNT(*) FROM mail_alias INNER JOIN mails ON mails.id = mail_alias.mail_id INNER JOIN domains ON domains.id = mails.domain_id INNER JOIN users ON users.id = domains.client_id WHERE users.parent = '$this->reseller_user_id')) as used_email_alias,
                            (SELECT COUNT(*) from users WHERE users.type < '4' AND users.type > '2') as total_clients,
                            (SELECT COUNT(*) from domains) as total_domains";
        return $this;

    }

    public function resellerSql(): string
    {
        $database_name = DATABASE_NAME;
        $this->reseller_sql = "SELECT " . $this->reseller_sql . " FROM $database_name.users ";
        $this->reseller_sql = $this->reseller_sql . " LEFT JOIN $database_name.users as clients ON $database_name.users.id = clients.parent";
        $this->reseller_sql = $this->reseller_sql . " INNER JOIN $database_name.userdata ON $database_name.users.id = $database_name.userdata.user_id";
        $this->reseller_sql = $this->reseller_sql . " INNER JOIN client_statistics ON $database_name.users.id = client_statistics.client_id";
        $this->reseller_sql = $this->reseller_sql . " INNER JOIN $database_name.client_limits_permissions AS limits ON $database_name.users.id = limits.client_id";
        $this->reseller_sql = $this->reseller_sql . " LEFT JOIN $database_name.domains ON $database_name.users.id = $database_name.domains.client_id";
        $this->reseller_sql = $this->reseller_sql . " WHERE users.id = '$this->reseller_user_id'";

        $reseller_sql = $this->reseller_sql;
        $this->reseller_sql = null;
        $this->reseller_user_id = null;
        return $reseller_sql;
    }
}