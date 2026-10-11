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

use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\Models\ClientIpAddress;

/**
 * Class Ipaddress
 * @package FPanel\Ipaddress
 */
class Ipaddress extends Library
{
    public $parent_id;
    public $user_id;
    public $user_type;

    public function save($ip_id, $client_id): bool
    {
        $client_ip = new ClientIpAddress();
        $client_ip->assign(array(
            "ipaddr_id" => $ip_id,
            "client_id" => $client_id
        ));
        if(!$client_ip->create()) {
            foreach($client_ip->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            $this->log->error([
                "Module" => "Ipaddress->save",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function delete($user_id): bool
    {
        $client_ip_addresses = ClientIpAddress::find("client_id = '$user_id'");
        if(!empty($client_ip_addresses)) {
            if(!$client_ip_addresses->delete()) {
                foreach($client_ip_addresses->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Ipaddress->delete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
        return true;
    }

    public function getIpAddress($options, $system_type = STANDALONE): array
    {
        $qbuilder = new QueryBuilder();

        $user_id = $options["user_id"];
        $selects = [
            "ip_address.id",
            "ip_address.ip_address",
            "ip_address.ssl_status",
            "ip_address.type",
            "ip_address.server_id",
            "ip_address.is_default",
        ];
        if($system_type == CLOUD) {
            $selects[] = "servers.description as server_desc";
        }
        $qbuilder->select($selects)
            ->from("ip_address")
            ->where([
                "conditions" => "1 = 1",
                "bind" => []
            ]);
        if($system_type == CLOUD) {
            $qbuilder->innerJoin("servers","servers.id = ip_address.server_id")
                ->andWhere([
                    "conditions" => "servers.type != 'main'",
                    "bind" => []
                ]);
        }
        if(ROOT_USER > $options["user_type"] && $options["user_type"] > SUPPORT_USER) {
            $qbuilder
                ->innerJoin("client_ip_address","client_ip_address.ipaddr_id = ip_address.id")
                ->andWhere([
                    "conditions" => "client_ip_address.client_id = ?1",
                    "bind" => [1 => $user_id]
                ])
                ->andWhere([
                    "conditions" => "ip_address.server_id != ?1",
                    "bind" => [1 => 1]
                ]);
        }

        $ip_list = $qbuilder->orderAsc("ip_address.interface")
            ->find();

        $ip_data = array();

        foreach ($ip_list as $value) {
            if ($value["type"] != EXCLUSIVE) {
                if ($value["ssl_status"] != ACTIVE) {
                    $ip_data[$value["id"]] = $value["ip_address"];
                    if($system_type == CLOUD) {
                        $ip_data[$value["id"]] .= " ($value[server_desc])";
                    }
                }
            }
            else {
                if ($value["ssl_status"] != ACTIVE) {
                    $ip_id = $value["id"];
                    $exlusive_ips = $this->modelsManager->createBuilder()
                        ->from("FPanel\Models\ClientIpAddress")
                        ->where("FPanel\Models\ClientIpAddress.ipaddr_id = '$ip_id'")
                        ->andWhere("FPanel\Models\ClientIpAddress.client_id NOT LIKE '$user_id'")
                        ->getQuery()
                        ->execute()
                        ->toArray();
                    if(count($exlusive_ips) == 0) {
                        $ip_data[$ip_id] = $value["ip_address"]." " . __("Exclusive");
                    }
                }
            }
        }
        return $ip_data;
    }
}