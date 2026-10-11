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
 */namespace FPanel\Api\System;

use FPanel\Environments\QueryBuilder;
use FPanel\Models\IpAddress;
use FPanel\Server\Network as NetworkManagement;
use Phalcon\Mvc\User\Component;

class Network extends Component
{
    public function init($process, $params)
    {
        switch($process[2]) {
            case "list":
                return $this->networkList();
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            case "hostname":
                return $this->hostname($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function networkList(): array
    {
        $qbuilder = new QueryBuilder();
        $networks = $qbuilder->select([
            "ip_address.ip_address,ip_address.interface",
            "(SELECT COUNT(*) from client_ip_address where client_ip_address.ipaddr_id = ip_address.id) as count_user",
            "(SELECT COUNT(*) from domains where domains.ipaddr_id = ip_address.id) as count_domain",
            "ip_address.type",
            "ip_address.is_default",
        ])
            ->from("ip_address")
            ->find();
        $networks['status'] = "1";
        return $networks;
    }

    private function create($params)
    {
        $error_messages = array();
        $network = new NetworkManagement();

        $interfaces = $network->getInterfaceList("1");


        $post_data = array(
            'interfaces' => $interfaces,
            'interface' => $params['interface'],
            'ip_address' => $params['ip_address'],
            'netmask' => $params['netmask'],
            'type' => $params['type'],
            'server_id' => "1",
        );

        $network->setPosts($post_data);
        $network_create_status = $network->createNetwork();
        if ($network_create_status == true) {
            return "Success";
        }
        else {
            foreach ($network->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    private function update($params)
    {
        $error_messages = array();
        $sys_ip_addr = IpAddress::findFirst("ip_address = '$params[ip_address]'");
        if(!$sys_ip_addr) {
            return $error_messages['ip_address'] = "Ip Address not found";
        }
        $post_data = array(
            'ip_address_id' => $sys_ip_addr->id,
            'type' => $params['type'],
        );
        $network = new NetworkManagement();

        $network->setPosts($post_data);
        $network_create_status = $network->updateNetwork();
        if ($network_create_status == true) {
            return "Success";
        }
        else {
            foreach ($network->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    private function delete($params): string
    {
        $error_messages = array();
        $sys_ip_addr = IpAddress::findFirst("ip_address = '$params[ip_address]'");
        if(!$sys_ip_addr) {
            return $error_messages['ip_address'] = "Ip Address not found";
        }

        $post_data = array(
            'ip_address_id' => $sys_ip_addr->id,
            'server_id' => "1",
        );

        $network = new NetworkManagement();
        $network->setPosts($post_data);
        $network_delete_status = $network->deleteNetwork();

        if ($network_delete_status == true) {
            return "Success";
        }
        else {
            return $error_messages['ip_address'] = "Ip Address not found";
        }
    }

    private function hostname($params)
    {
        $error_messages = array();

        $network = new NetworkManagement();
        $post_data = array(
            'hostname' => $params['hostname'],
            'domain' => $params['domain'],
            'server_id' => "1",
        );

        $network->setPosts($post_data);
        $network_hostname_update_status = $network->updateHostname();
        if ($network_hostname_update_status == true) {
            return "Success";
        }
        else {
            foreach ($network->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }
}