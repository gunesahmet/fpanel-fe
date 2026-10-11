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

trait Administrator
{
    private $root_sql;

    public function rootUser(): Builder
    {
        $this->root_sql = " users.password_last_set,
        users.last_login,
        users.last_login_ip,
        users.must_change_password,
        users.created_date,
        users.expire_date,
        users.`language`,
        users.`status_msg`,
        users.changed_data,
        users.interface,
        users.parent as parent_id,
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
        userdata.country,
        (SELECT GROUP_CONCAT( ip_address.id, ':', ip_address.ip_address ) FROM ip_address) AS ip_adress_list,
        ";
        return $this;
    }

    public function rootUsage(): Builder
    {
        $this->root_sql = $this->root_sql . " (SELECT COUNT(*) from users WHERE users.type < '4' AND users.type > '2') as used_clients,
                            (SELECT COUNT(*) from users WHERE users.type = '2') as used_resellers,
                            (SELECT COUNT(*) from domains) as used_domains,
                            (SELECT COUNT(*) from serviceplans WHERE serviceplans.client_id = '1') as used_templates,
                            (SELECT COUNT(*) from subdomains) as used_subdomains,
                            (SELECT COUNT(*) from domain_alias) as used_domain_alias,
                            (SELECT COUNT(*) from mails) as used_email,
                            (SELECT COUNT(*) from `databases`) as used_database,
                            (SELECT COUNT(*) from mail_alias) as used_email_alias";
        return $this;

    }

    public function rootSql($user_id): string
    {
        $this->root_sql = "SELECT " . $this->root_sql . " FROM users INNER JOIN userdata ON userdata.user_id = users.id WHERE users.id = '$user_id'";
        $root_sql = $this->root_sql;
        $this->root_sql = null;
        return $root_sql;
    }
}