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
 */namespace WebHosting\Controller;

use FPanel\Environments\QueryBuilder;
use FPanel\Models\Domains;
use FPanel\Models\Users;

trait Parents
{
    public function getdomain($domain_id)
    {
        $builder = new QueryBuilder();
        return $builder->select([
            "domains.id",
            "domains.name",
            "domains.client_id",
            "domains.ftp_user_id",
            "domains.ssl_cert_id",
            "domains.ipaddr_id",
            "domains.status",
            "domains.status_msg",
            "domains.created_date",
            "domains.expire_date",
            "domains.web_server_id",
            "domains.dns_server_id",
            "domains.mail_server_id",

            "ftp_users.username as ftpname",
            "ftp_users.password",

            "client_limits_permissions.man_ssl",
            "client_limits_permissions.php",
            "client_limits_permissions.php_version",
            "client_limits_permissions.cgi",
            "client_limits_permissions.perl",
            "client_limits_permissions.python",
            "client_limits_permissions.fast_cgi",
            "client_limits_permissions.statistic_support",
            "client_limits_permissions.statistic_type",
            "client_limits_permissions.asp",
            "client_limits_permissions.aspnet_core",
            "client_limits_permissions.aspnet",
            "client_limits_permissions.aspnet_version",
            "client_limits_permissions.dedicated_pool",
            "client_limits_permissions.max_cpu",
            "client_limits_permissions.application_pool_mode",
            "client_limits_permissions.ssl",

            "ip_address.ip_address",

        ])
            ->from("domains")
            ->innerJoin("ftp_users","ftp_users.id = domains.ftp_user_id")
            ->innerJoin("client_limits_permissions","client_limits_permissions.domain_id = domains.id")
            ->innerJoin("ip_address","ip_address.id = domains.ipaddr_id")
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();
    }

    public function getparentip($parent_id, $ip_id, $server_id): array
    {
        $ip_list = array();
        $builder = new QueryBuilder();
        $parent = Users::findFirstById($parent_id);
        if($parent->type == ROOT_USER) {
            $ip_address_list = $builder->select(array(
                'ip_address.ip_address',
                'ip_address.id',
                'ip_address.type',
                'ip_address.server_id'
            ))
                ->from("ip_address")
                ->where([
                    'conditions' => "ip_address.server_id = ?1",
                    'bind' => [1 => $server_id]
                ])
                ->find();

            foreach ($ip_address_list as $ip_addr) {
                if($ip_addr['type'] == SHARED) {
                    $ip_list[] = $ip_addr;
                }
                if($ip_addr['id'] != $ip_id) {
                    $used_ip = Domains::findFirst("ipaddr_id = '$ip_addr[id]'");
                    if(!$used_ip) {
                        $ip_list[] = $ip_addr;
                    }
                }
                else {
                    $ip_list[] = $ip_addr;
                }
            }
        }
        else {
            $ip_address_list = $builder->select(array(
                'ip_address.ip_address',
                'ip_address.id',
                'ip_address.type',
                'ip_address.server_id'
            ))
                ->from("ip_address")
                ->innerJoin("client_ip_address", "client_ip_address.ipaddr_id = ip_address.id")
                ->where([
                    'conditions' => "client_ip_address.client_id = ?1",
                    'bind' => [1 => $parent_id]
                ])
                ->andWhere([
                    'conditions' => "ip_address.server_id = ?1",
                    'bind' => [1 => $server_id]
                ])
                ->find();

            foreach ($ip_address_list as $ip_addr) {
                if($ip_addr['type'] == SHARED) {
                    $ip_list[] = $ip_addr;
                }
            }
        }

        return $ip_list;
    }
}