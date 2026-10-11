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
 */namespace FPanel\Api\Domain;

use FPanel\Domain\NamedService;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;
use FPanel\Models\Domains;
use Phalcon\Mvc\User\Component;

class DNS extends Component
{
    public function dns($process, $params, $client_id)
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
            return "You dont have access for this domain";
        }
        switch($process) {
            case "list":
                return $this->lists($params);
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            case "restore":
                return $this->restore($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function lists($params)
    {
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        $dns_zone = DnsZone::findFirst("domain_id = '$domains->id'");
        if(!$dns_zone) {
            return ["domain_name" => __("Dns record not found for %s", $domain_name)];
        }
        $qbuilder = new QueryBuilder();
        return $qbuilder->select([
            "dns_records.id",
            "dns_records.type",
            "dns_records.host",
            "dns_records.value",
            "dns_records.opt",
            "dns_records.time_stamp",
            "dns_records.port",
            "dns_records.weight",
        ])
            ->from("dns_records")
            ->innerJoin("dns_zone", "dns_zone.id = dns_records.dns_zone_id")
            ->where([
                "conditions" => "dns_zone.domain_id = ?1",
                "bind" => [1 => $domains->id]
            ])
            ->find();
    }

    public function create($params)
    {
        $error_messages = [];

        $requeired_keys = [
            "domain_name",
            "type",
            "host",
            "value",
            "opt",
            "port",
            "weight",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        $record_type = strtoupper($params["type"]);

        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        $post_data = array(
            'action' => "create",
            'update' => false,
            // Değişkenler
            'domain_id'   => $domains->id,
            'domain_name'   => $domain_name,
            'time_stamp'   => time(),
            'type'   => $record_type,
        );

        switch ($record_type) {
            case "A":
                $post_data["a_host"] = $params["host"];
                $post_data["a_value"] = $params["value"];
                $post_data["a_opt"] = null;
                break;
            case "AAAA":
                $post_data["aaaa_host"] = $params["host"];
                $post_data["aaaa_value"] = $params["value"];
                $post_data["aaaa_opt"] = $params["opt"];
                break;
            case "CNAME":
                $post_data["cname_host"] = $params["host"];
                $post_data["cname_value"] = $params["value"];
                $post_data["cname_opt"] = $params["opt"];
                break;
            case "MX":
                $post_data["mx_host"] = $params["host"];
                $post_data["mx_value"] = $params["value"];
                $post_data["mx_opt"] = $params["opt"];
                break;
            case "NS":
                $post_data["ns_host"] = $params["host"];
                $post_data["ns_value"] = $params["value"];
                $post_data["ns_opt"] = $params["opt"];
                break;
            case "PTR":
                $post_data["ptr_host"] = $params["host"];
                $post_data["ptr_value"] = $params["value"];
                $post_data["ptr_opt"] = $params["opt"];
                break;
            case "SRV":
                $host_str_arr = explode(".", $params["host"]);
                $srv_service_name = str_replace("_", "", $host_str_arr[0]);
                $srv_protocol = str_replace("_", "", $host_str_arr[1]);
                $srv_domain_name_str = str_replace(sprintf("_%s._%s.", $srv_service_name, $srv_protocol), "", $params["host"]);
                $srv_domain_name = null;
                if($srv_domain_name_str != $domain_name)
                    $srv_domain_name = str_replace(".$domain_name", "", $srv_domain_name_str);

                $post_data['srv_protocol'] = $srv_protocol;
                $post_data['srv_service_name'] = $srv_service_name;
                $post_data['srv_domain_name'] = $srv_domain_name;
                $post_data['srv_target_host'] = $params["value"];
                $post_data['srv_priority'] = $params["opt"];
                $post_data['srv_target_port'] = $params["port"];
                $post_data['srv_priority_weight'] = $params["weight"];
                break;
            case "TXT":
                $post_data["txt_host"] = $params["host"];
                $post_data["txt_value"] = $params["value"];
                $post_data["txt_opt"] = $params["opt"];
                break;
//            case "CAA":
//                break;
//            case "DS":
//                break;
//            case "DNSKEY":
                break;
        }
        $dns = new NamedService();
        $dns->domain_name = $domain_name;
        $dns->setPosts($post_data);
        $dns_record_status = $dns->createDNSRecords();
        $dns->initCurl($domains->id);
        if ($dns_record_status) {
            return "Success";
        }
        else {
            foreach ($dns->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
        }
        return $error_messages;
    }

    public function update($params)
    {
        $error_messages = [];
        $requeired_keys = [
            "domain_name",
            "type",
            "host",
            "value",
            "opt",
            "weight",
            "port",

            "old_host",
            "old_value",
            "old_opt",
            "old_weight",
            "old_port",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages["domain_name"] = "Domain not found";
        }
        $dns_zone = DnsZone::findFirst("domain_id = '$domains->id'");
        if(!$dns_zone) {
            return $error_messages["domain_name"] = "Dns zone not found";
        }

        $record_type = $params["type"];

        $host = $params["host"];
        $value = $params["value"];
        $opt = $params["opt"];
        $weight = $params["weight"];
        $port = $params["port"];

        $old_host = $params["old_host"];
        $old_value = $params["old_value"];
        $old_opt = $params["old_opt"];
        $old_weight = $params["old_weight"];
        $old_port = $params["old_port"];

        $post_data = array(
            'action' => "update",
            'update' => true,
            // Değişkenler
            'domain_id'   => $domains->id,
            'domain_name'   => $domain_name,
            'dns_zone_id'   => $dns_zone->id,
            'time_stamp'   => time(),
            'type'   => $record_type,

            'old_host' => $params['old_host'],
            'old_value' => $params['old_value'],
        );

        switch ($record_type) {
            case "A":
                $post_data["a_host"] = $params["host"];
                $post_data["a_value"] = $params["value"];
                $post_data["a_opt"] = null;
                break;
            case "AAAA":
                $post_data["aaaa_host"] = $params["host"];
                $post_data["aaaa_value"] = $params["value"];
                $post_data["aaaa_opt"] = $params["opt"];
                break;
            case "CNAME":
                $post_data["cname_host"] = $params["host"];
                $post_data["cname_value"] = $params["value"];
                $post_data["cname_opt"] = $params["opt"];
                break;
            case "MX":
                $post_data["mx_host"] = $params["host"];
                $post_data["mx_value"] = $params["value"];
                $post_data["mx_opt"] = $params["opt"];
                break;
            case "NS":
                $post_data["ns_host"] = $params["host"];
                $post_data["ns_value"] = $params["value"];
                $post_data["ns_opt"] = $params["opt"];
                break;
            case "PTR":
                $post_data["ptr_host"] = $params["host"];
                $post_data["ptr_value"] = $params["value"];
                $post_data["ptr_opt"] = $params["opt"];
                break;
            case "SRV":
                $host_str_arr = explode(".", $params["host"]);
                $srv_service_name = str_replace("_", "", $host_str_arr[0]);
                $srv_protocol = str_replace("_", "", $host_str_arr[1]);
                $srv_domain_name_str = str_replace(sprintf("_%s._%s.", $srv_service_name, $srv_protocol), "", $params["host"]);
                $srv_domain_name = null;
                if($srv_domain_name_str != $domain_name)
                    $srv_domain_name = str_replace(".$domain_name", "", $srv_domain_name_str);

                $post_data['srv_protocol'] = $srv_protocol;
                $post_data['srv_service_name'] = $srv_service_name;
                $post_data['srv_domain_name'] = $srv_domain_name;
                $post_data['srv_target_host'] = $params["value"];
                $post_data['srv_priority'] = $params["opt"];
                $post_data['srv_target_port'] = $params["port"];
                $post_data['srv_priority_weight'] = $params["weight"];
                break;
            case "TXT":
                $post_data["txt_host"] = $params["host"];
                $post_data["txt_value"] = $params["value"];
                $post_data["txt_opt"] = $params["opt"];
                break;
        }


        $condition = "host = '$old_host' and type = '$record_type' and value = '$old_value'";
        if(!empty($old_opt)) {
            $condition .= " and opt = '$old_opt'";
        }
        if(!empty($old_weight)) {
            $condition .= " and weight = '$old_weight'";
        }
        if(!empty($old_port)) {
            $condition .= " and port = '$old_port'";
        }
        $dns = DnsRecords::findFirst($condition);
        if(!$dns) {
            return $error_messages["domain_name"] = "Dns record not found";
        }

        $post_data["id"] = $dns->id;

        $named = new NamedService();
        $named->domain_name = $domain_name;
        $named->setPosts($post_data);
        $status = $named->updateDNSRecords();
        if($status) {
            return "Success";
        }
        else {
            foreach ($named->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function delete($params)
    {
        $error_messages = [];

        $requeired_keys = [
            "domain_name",
            "type",
            "host",
            "value",
            "opt",
            "weight",
            "port",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }

        $record_type = strtoupper($params["type"]);

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages["domain_name"] = "Domain not found";
        }
        $dns_zone = DnsZone::findFirst("domain_id = '$domains->id'");
        if(!$dns_zone) {
            return $error_messages['domain_name'] = "Dns zone not found";
        }

        $post_data = array(
            'action' => "delete",
            'update' => false,
            // Değişkenler
            'domain_id'   => $domains->id,
            'domain_name'   => $domain_name,
            'time_stamp'   => time(),
            'type'   => $record_type,
        );
        $host = $params["host"];
        $value = $params["value"];
        $opt = $params["opt"];
        $weight = $params["weight"];
        $port = $params["port"];


        $condition = "host = '$host' and type = '$record_type' and value = '$value'";
        if(!empty($opt)) {
            $condition .= " and opt = '$opt'";
        }
        if(!empty($weight)) {
            $condition .= " and weight = '$weight'";
        }
        if(!empty($port)) {
            $condition .= " and port = '$port'";
        }
        $dns_records = DnsRecords::findFirst($condition);
        if(!$dns_records) {
            return $error_messages["id"] = "Dns record not found";
        }
        $named = new NamedService();
        $post_data["id"] = $dns_records->id;

        $named->setPosts($post_data);
        $named->domain_name = $domain_name;
        $named->deleteDNSRecords(null, $domains->id);
        return "Success";
    }

    public function restore($params): string
    {
        $error_messages = array();
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages["domain_name"] = "Domain not found";
        }
        $restore_dns = new NamedService();
        $restore_dns->restoreDNSRecords($domains->id);
        return "Success";
    }
}