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
namespace FPanel\Api\Domain;

use FPanel\Environments\QueryBuilder;
use FPanel\Models\Domains;
use FPanel\Models\Subdomains;
use Phalcon\Mvc\User\Component;

class Subdomain extends Component
{
    private $error_messages = [];
    
    public function subdomains($process, $params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        if(!$this->env->checkDomainOwner($params["domain_name"], $client_id)) {
            return $this->error_messages['domain_name'] = "You dont have access for this domain";
        }
        switch($process) {
            case "list":
                return $this->list($params);
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            default:
                return array('process' => __('Process is required'));
        }
    }

    public function list($params): array
    {
        $domain_name = $params['domain_name'];

        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }
        $qbuilder = new QueryBuilder();
        $qbuilder->select([
            "subdomains.subdomain_name",
            "subdomains.ftpuser_type",
            "status",
            "ip_address.ip_address"
        ])
            ->from('subdomains')
            ->innerJoin("ip_address","ip_address.id = subdomains.ipaddr_id")
            ->where([
                'conditions' => "subdomains.domain_id = ?1",
                'bind' => [1 => $domains->id]
            ]);

        $subdomain_list = $qbuilder->find();
        foreach ($subdomain_list as $k => $v) {
            $subdomain_list[$k]["status"] = $v["status"] == "1";
            $subdomain_list[$k]["ftpuser_type"] = $this->env->getFtpUserTypes($v["ftpuser_type"]);

        }
        $subdomain_list['status'] = "1";
        return $subdomain_list;
    }

    public function create($params)
    {
        $requeired_keys = [
            "domain_name",
            "subdomain_name",
            "native_ftp",
            "php",
//            "php_version",
//            "asp",
//            "aspnet_core",
//            "asp_net",
//            "asp_net_version",
            "cgi",
            "perl",
            "python",
            "statistics",
            "statistics_type",
//            "dedicated_pool",
//            "cpu_limit",
//            "application_pool_mode",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }
        $domain_data = $this->getdomain($domains->id);

        $post_data = array(
            'domain_id' => $domains->id,
            'domain_name' => $domain_name,
            'domain_ftpuser' => $domain_data['ftpname'], // Domaine ait ftp user
            'ipaddress' => $domain_data['ip_address'], // Domaine ait ipadresi
            'domain_ftp_user_id' => $domain_data['ftp_user_id'], // Domaine ait ftp user idsi
            'domain_parent' => $domain_data['client_id'], // Domainin sahibinin idsi
            'ipaddr_id' => $domain_data['ipaddr_id'], // Domainin ip adresinin idsi
            'status' => ON,

            'subdomain_name' => $params['subdomain_name'],
            'full_name' => $params['subdomain_name'].'.'.$domain_name,

            'use_native_ftp' => $params['native_ftp'] ? ON : OFF,
            'ftpname'   => $params['ftp_username'],
            'password'   => $params['ftp_password'],

            // Subdomain host parametreleri
            'ssl' => PASSIVE,
        );

        $post_data['php'] = $params['php'] ? ON : OFF;
        $post_data['php_version'] = !empty($params['php_version']) ? $this->env->getPhpVersion($params['php_version'], true) : PHP72;
        $post_data['asp'] = $params['asp'] ? ON : OFF;
        $post_data['aspnet_core'] = !empty($params['aspnet_core']) ? ON : OFF;
        $post_data['aspnet'] = !empty($params['asp_net']) ? ON : OFF;
        $post_data['aspnet_version'] = !empty($params['asp_net_version']) ? $this->env->getNetVersion($params['asp_net_version'], true) : 0;
        $post_data['cgi'] = $params['cgi'] ? ON : OFF;
        $post_data['perl'] = $params['perl'] ? ON : OFF;
        $post_data['python'] = $params['python'] ? ON : OFF;
        $post_data['statistic_support'] = !empty($params['statistics']) ? ON : OFF;
        $post_data['statistic_type'] = !empty($params['statistics_type']) ? $this->env->getStatisticsType($params['statistics_type'], true) : NO_STATS;
        $post_data['dedicated_pool'] = !empty($params['dedicated_pool']) ? ON : OFF;
        $post_data['max_cpu'] = !empty($params['cpu_limit']) ? $params['cpu_limit'] : 0;
        $post_data['application_pool_mode'] = !empty($params['application_pool_mode']) && !empty($params['dedicated_pool']) ? $this->env->getApplicationMode($params['application_pool_mode'], true) : 1;

        $subdomain = new \FPanel\Domain\Subdomain();
        $subdomain->setPosts($post_data);
        if (!$subdomain->createSubdomain()) {
            foreach ($subdomain->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return $this->error_messages;
        }
        return "Success";
    }

    public function update($params)
    {
        $requeired_keys = [
            "domain_name",
            "subdomain_name",
            "native_ftp",
            "php",
            "status",
//            "php_version",
//            "asp",
//            "aspnet_core",
//            "asp_net",
//            "asp_net_version",
            "cgi",
            "perl",
            "python",
            "statistics",
            "statistics_type",
//            "dedicated_pool",
//            "cpu_limit",
//            "application_pool_mode",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }
        $subs = Subdomains::findFirst("subdomain_name = '$params[subdomain_name]' AND domain_id = '$domains->id'");

        if(!$subs) {
            return $this->error_messages['subdomain_name'] = "Subdomain doesnt exists on system";
        }

        $subdomain_data = $this->getsubdomain($subs->id, $domains->id);

        $subdomain_name = $subdomain_data['subdomain_name'];
        $post_data = array(
            'domain_id' => $domains->id,
            'domain_name' => $domain_name,
            'old_ftpname' => $subdomain_data['ftpname'],
            'ipaddress' => $subdomain_data['ip_address'],
            'domain_ftp_user_id' => $subdomain_data['ftp_user_id'],
            'domain_parent' => $subdomain_data['domain_parent'],
            'ipaddr_id' => $subdomain_data['ipaddr_id'],
            'subdomain_name' => $subdomain_name,
            'full_name' => $subdomain_name.'.'.$domain_name,
            'use_native_ftp' => $params['native_ftp'] ? ON : OFF,
            'status' => $params['status'] ? ON : OFF,
            'ftpname' => $params['ftp_username'] ?? "",
            'password' => $params['ftp_password'] ?? "",

            // Subdomain host parametreleri
            'ssl' => PASSIVE,
            'subdomain_data'    => $subdomain_data,
        );

        $post_data['php'] = $params['php'] ? ON : OFF;
        $post_data['php_version'] = !empty($params['php_version']) ? $this->env->getPhpVersion($params['php_version'], true) : PHP72;
        $post_data['asp'] = $params['asp'] ? ON : OFF;
        $post_data['aspnet_core'] = !empty($params['aspnet_core']) ? ON : OFF;
        $post_data['aspnet'] = !empty($params['asp_net']) ? ON : OFF;
        $post_data['aspnet_version'] = !empty($params['asp_net_version']) ? $this->env->getNetVersion($params['asp_net_version'], true) : 0;
        $post_data['cgi'] = $params['cgi'] ? ON : OFF;
        $post_data['perl'] = $params['perl'] ? ON : OFF;
        $post_data['python'] = $params['python'] ? ON : OFF;
        $post_data['statistic_support'] = !empty($params['statistics']) ? ON : OFF;
        $post_data['statistic_type'] = !empty($params['statistics_type']) ? $this->env->getStatisticsType($params['statistics_type'], true) : NO_STATS;
        $post_data['dedicated_pool'] = !empty($params['dedicated_pool']) ? ON : OFF;
        $post_data['max_cpu'] = !empty($params['cpu_limit']) ? $params['cpu_limit'] : 0;
        $post_data['application_pool_mode'] = !empty($params['application_pool_mode']) && !empty($params['dedicated_pool']) ? $this->env->getApplicationMode($params['application_pool_mode'], true) : 1;


        $subdomain = new \FPanel\Domain\Subdomain();
        $subdomain->setPosts($post_data);

        $update_status = $subdomain->updateSubdomain();
        if($update_status) {
            return "Success";
        }
        else {
            foreach ($subdomain->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    public function delete($params)
    {
        $error_messages = array();
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        $subs = Subdomains::findFirst("subdomain_name = '$params[subdomain_name]' AND domain_id = '$domains->id'");

        if(!$subs) {
            $this->error_messages['subdomain_name'] = "Subdomain doesnt exists on system";
            return $error_messages;
        }

        $subdomain = new \FPanel\Domain\Subdomain();
        $subdomain->setPosts(array(
            'id' => $subs->id,
            'domain_id' => $domains->id,
        ));
        $subdomain->deleteSubdomain($domains->id);
        return "Success";
    }

    public function getdomain($domain_id)
    {
        $qbuilder = new QueryBuilder();
        return $qbuilder->select([
            "domains.name",
            "domains.ftp_user_id",
            "domains.client_id",
            "domains.status",
            "dns_zone.id as dns_zone_id",
            "ip_address.id as ipaddr_id",
            "ip_address.ip_address",
            "ftp_users.username as ftpname",
            "client_limits_permissions.php",
            "client_limits_permissions.asp",
            "client_limits_permissions.cgi",
            "client_limits_permissions.perl",
            "client_limits_permissions.python",
            "client_limits_permissions.aspnet_core",
            "client_limits_permissions.aspnet",
            "client_limits_permissions.aspnet_version",
            "client_limits_permissions.php_version",
        ])
            ->from("domains")
            ->innerJoin("ip_address","domains.ipaddr_id = ip_address.id")
            ->innerJoin("client_limits_permissions","domains.id = client_limits_permissions.domain_id")
            ->innerJoin("dns_zone","domains.id = dns_zone.domain_id")
            ->innerJoin("ftp_users","domains.ftp_user_id = ftp_users.id")
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();
    }

    /**
     * @param $sub_id
     * @param $domain_id
     * @return array|bool
     */
    public function getsubdomain($sub_id, $domain_id)
    {
        $qbuilder = new QueryBuilder();
        return $qbuilder->select([
            "subdomains.id",
            "subdomains.php",
            "subdomains.php_version",
            "subdomains.cgi",
            "subdomains.perl",
            "subdomains.python",
            "subdomains.fast_cgi",
            "subdomains.asp",
            "subdomains.status",
            "subdomains.aspnet_core",
            "subdomains.aspnet",
            "subdomains.aspnet_version",
            "subdomains.dedicated_pool",
            "subdomains.max_cpu",
            "subdomains.ssl",
            "subdomains.ftpuser_id",
            "subdomains.ftpuser_type",
            "subdomains.subdomain_name",
            "client_limits_permissions.aspnet_core",
            "client_limits_permissions.aspnet",
            "client_limits_permissions.asp",
            "client_limits_permissions.fast_cgi",
            "client_limits_permissions.python",
            "client_limits_permissions.perl",
            "client_limits_permissions.cgi",
            "client_limits_permissions.php",
            "dns_zone.id AS dns_zone_id",
            "ip_address.ip_address",
            "domains.ipaddr_id",
            "domains.client_id as domain_parent",
            "domains.`name`",
            "domains.`status`",
            "domains.ftp_user_id",
            "ftp_users.username as ftpname",
            "ftp_users.`password`",
        ])
            ->from("subdomains")
            ->innerJoin("domains","subdomains.domain_id = domains.id")
            ->innerJoin("client_limits_permissions","subdomains.domain_id = client_limits_permissions.domain_id")
            ->innerJoin("dns_zone","subdomains.domain_id = dns_zone.domain_id")
            ->innerJoin("ip_address","subdomains.ipaddr_id = ip_address.id")
            ->innerJoin("ftp_users","subdomains.ftpuser_id = ftp_users.id")
            ->where([
                'conditions' => "subdomains.id = ?1",
                'bind' => [1 => $sub_id]
            ])
            ->andWhere([
                'conditions' => "subdomains.domain_id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();
    }
}