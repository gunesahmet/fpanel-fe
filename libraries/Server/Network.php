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

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\IpAddress;
use FPanel\Models\Servers;
use FPanel\Models\Users;
use FPanel\Process\Networking;
use FPanel\Process\WebServer;
use MongoDB\Driver\Server;

/**
 * Class NetworkManagement
 * @package FPanel\System
 */
class Network extends Library
{
    protected $index;
    protected $interface;
    protected $ip_address;

    public function listNetwork($server_id, $system_type): array
    {
        $datatable = new Datatable();

        if($system_type == CLOUD) {
            $delete_button = $datatable->buttons("network/delete/$1/server/$server_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("network/edit/$1/server/$server_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('network/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('network/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable ->select([
            /*0*/ 'ip_address.id',
            /*1*/ 'ip_address.ip_address',
            /*2*/ 'ip_address.interface',
            /*3*/ '(SELECT COUNT(*) from client_ip_address where client_ip_address.ipaddr_id = ip_address.id) as count_user',
            /*4*/ '(SELECT COUNT(*) from domains where domains.ipaddr_id = ip_address.id) as count_domain',
            /*5*/ 'ip_address.type',
            /*6*/ 'ip_address.is_default',
            /*7*/ 'ip_address.process'
        ])
            ->from("ip_address");
        if($system_type == CLOUD) {
            $datatable->innerJoin('servers','ip_address.server_id = servers.id');
            if($server_id == "1")
                $datatable->where("servers.type = 'main'");
            else
                $datatable->where("ip_address.server_id = '$server_id'");
        }
        $datatable->searchColumn(array('ip_address.ip_address','ip_address.interface','ip_address.type'));


        $data = $datatable->addColumn('actions', $edit_button.$delete_button,'ip_address.id')
            ->generate()
            ->execute()
            ->result();

        $server_link = "";
        if($system_type == CLOUD) {
            $server_link = "/server/$server_id";
        }
        foreach($data['aaData'] as $key => $value){

            $ip_actions = $value[8];
            // IP Address
            if($value[7] == "delete") {
                $ip_actions = "";
                $data['aaData'][$key][0] = "<span style=\"text-decoration:line-through;\">" . $value[1] . "</span>";
            }
            else {
                $data['aaData'][$key][0] = '<a data-id="nested" class="text-success" href="'.$this->uri->url_encrypt('network/edit/'. $value[0].$server_link).'">'. $value[1].'</a>';
            }
            $data['aaData'][$key][1] = '<a class="text-success"> '. $value[2] .' </a>';
            $data['aaData'][$key][2] = $value[3];
            $data['aaData'][$key][3] = $value[4];

            // Type
            switch($value[5]) {
                case SHARED:
                    $data['aaData'][$key][4] = '<span class="label label-info"> '. __('Shared') .' </span>';
                    break;
                case EXCLUSIVE:
                    $data['aaData'][$key][4] = '<span class="label label-warning"> '. __('Exclusive') .' </span>';
                    break;
                case SSL:
                    $data['aaData'][$key][4] = '<span class="label label-default"> '. __('SSL') .' </span>';
                    break;
                case MAIN_CLOUD:
                    $data['aaData'][$key][4] = '<span class="label label-default"> '. __('Cloud Main') .' </span>';
                    break;
            }

            // Is Default
            if ($value[6] == '1'){
                $data['aaData'][$key][5] = '<span class="label label-success"> '. __('Default') .' </span>';
                $data['aaData'][$key][6] =  '<a data-id="nested" href="'. $this->uri->url_encrypt('network/edit/'. $value[6].$server_link).'" class="text-success btn btn-sm-block btn-xs-block"><i class="fa fa-edit"></i> '. __('Edit').'</a>';
            }
            else {
                $data['aaData'][$key][5] = "";
                $data['aaData'][$key][6] = $ip_actions;
            }

            unset($data['aaData'][$key][7]);
            unset($data['aaData'][$key][8]);

        }
        return $data;
    }

    public function createNetwork(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("ip_address"),
                'field' => 'ip_address',
                'message' => __('IP Address is required')
            ),
            array(
                'item' => $this->getPost("netmask"),
                'field' => 'netmask',
                'message' => __('Netmask is required')
            ),
            array(
                'item' => $this->getPost("type"),
                'field' => 'used_type',
                'message' => __('Please select an using type')
            ),
            array(
                'item' => $this->getPost("interface"),
                'field' => 'interface',
                'message' => __('Please select an interface')
            ),

        );
        $this->validation->Required($required)
            ->Ip($this->getPost("ip_address"),'ip_address')
            ->InclusionIn($this->getPost("interface"),'interface', $this->getPost("interfaces"))
            ->InclusionIn($this->getPost("type"),'used_type',array(SHARED, EXCLUSIVE))
            ->UniqueIp($this->getPost("ip_address"),'ip_address')
            ->Netmask($this->getPost("netmask"),'netmask');

        if($this->getPost("gateway") != null) {
            $this->validation->Ip($this->getPost("gateway"),'gateway');
        }

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        switch($this->getPost("server_os_type")) {
            case WINDOWS:
                $status = $this->windowsCreateNetwork();
                break;
            default:
                $status = $this->linuxCreateNetwork();
                break;
        }
        return $status;
    }

    public function linuxCreateNetwork(): bool
    {
        $interface = $this->getPost("interface");
        $ip_address = $this->getPost("ip_address");
        $netmask = $this->getPost("netmask");

        $type = $this->getPost("type");
        $server_id = $this->getPost("server_id");

        if($server_id == "1" && PANEL_TYPE == CLOUD) {
            $type = EXCLUSIVE;
        }
        else {
            if($type != SHARED) {
                $type = EXCLUSIVE;
            }
        }


        if($server_id == "1") {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $network = $this->queryBuilder->select([
            "ip_address.interface"
        ])
            ->from("ip_address")
            ->where([
                'conditions' => "ip_address.server_id = ?1",
                'bind' => [1 => "$server_id"]
            ])
            ->andWhere([
                'conditions' => "ip_address.interface LIKE ?1",
                'bind' => [1 => "%$interface%"]
            ])
            ->findFirst();

        if($network) {
            if($this->getPost("format") == "on") {
                $format = CIDR;
            }
            else {
                $format = NORMAL;
            }

            $data = array(
                "id"            => $this->env->createUuid(),
                'index'         => '',
                'ip_address'    => $ip_address,
                'ssl_status'    => PASSIVE,
                'netmask'       => $netmask,
                'interface'     => $interface,
                'gateway'       => $this->getPost("gateway"),
                'format'        => $format,
                'type'          => $type,
                'is_default'    => PASSIVE,
                'server_id'     => $server_id,
                'init'          => PASSIVE,
                'process'       => 'create',
            );

            if($this->updateChangedUser() == false) {
                return false;
            }

            $ip_address = new IpAddress();

            $ip_address->assign($data);
            if (!$ip_address->create()) {
                foreach ($ip_address->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Network->linuxCreateNetwork",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            return true;

        }
        return false;
    }

    public function windowsCreateNetwork(): bool
    {
        $ip_address = $this->getPost("ip_address");
        $netmask = $this->getPost("netmask");
        $type = $this->getPost("type");
        $server_id = $this->getPost("server_id");

        if($type != SHARED) {
            $type = EXCLUSIVE;
        }
        $interface = $this->getPost("interface");
        $network = IpAddress::findFirst("interface = '$interface' and server_id = '$server_id'");

        if($network) {
            $this->index = $network->index;

            if($this->getPost("format") == "on") {
                $format = CIDR;
            }
            else {
                $format = NORMAL;
            }

            $data = array(
                "id"            => $this->env->createUuid(),
                'index'         => $this->index,
                'ip_address'    => $ip_address,
                'ssl_status'    => '0',
                'netmask'       => $netmask,
                'interface'     => $network->interface,
                'gateway'       => $this->getPost("gateway"),
                'format'        => $format,
                'type'          => $type,
                'is_default'    => '0',
                'server_id'     => $server_id,
                'init'          => '0',
                'process'       => 'create',
            );

            $ip_address = new IpAddress();

            if($this->updateChangedUser() == false) {
                return false;
            }

            $ip_address->assign($data);

            if (!$ip_address->create()) {
                foreach ($ip_address->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Network->windowsCreateNetwork",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            return true;
        }
        else {
            $this->error_messages['interface'] = __('Please select network');
            return false;
        }
    }

    public function deleteNetwork(): bool
    {
        if($this->getPost("server_id") == "1") {
            $servers = Servers::findFirst("type = 'main'");
            $this->setPost("server_id", $servers->id);
        }
        switch($this->getPost("server_os_type")) {
            case WINDOWS:
                $status = $this->windowsDeleteNetwork();
                break;
            case LINUX:
                $status = $this->linuxDeleteNetwork();
                break;
            default:
                $status = false;
                break;
        }
        return $status;
    }

    public function windowsDeleteNetwork(): bool
    {
        $ip_address_id = $this->getPost("ip_address_id");
        $server_id = $this->getPost("server_id");
        $ip_address = IpAddress::findFirst("id = '$ip_address_id' AND server_id = '$server_id'");
        if(!$ip_address) {
            return false;
        }
        else {
            if($this->updateChangedUser() == false) {
                return false;
            }
            $this->index = $ip_address->index;
            $this->ip_address = $ip_address->ip_address;
            $this->interface = $ip_address->interface;
            $ip_address->process = 'delete';
            $ip_address->init = '0';
            if (!$ip_address->update()) {
                foreach ($ip_address->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Network->windowsDeleteNetwork",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    public function linuxDeleteNetwork(): bool
    {
        $ip_address_id = $this->getPost("ip_address_id");
        $server_id = $this->getPost("server_id");
        $ip_address = IpAddress::findFirst("id = '$ip_address_id' AND server_id = '$server_id'");
        if(!$ip_address) {
            return false;
        }
        else {
            if($this->updateChangedUser() == false) {
                return false;
            }
            $this->index = $ip_address->index;
            $this->ip_address = $ip_address->ip_address;
            $ip_address->process = 'delete';
            $ip_address->init = '0';
            if (!$ip_address->update()) {
                foreach ($ip_address->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Network->linuxDeleteNetwork",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
    }

    public function updateNetwork(): bool
    {
        $ip_address_id = $this->getPost("ip_address_id");
        $server_id = $this->getPost("server_id");
        $network = IpAddress::findFirst("id = '$ip_address_id' AND server_id = '$server_id'");

        $type = $this->getPost("type");

        if($type != SHARED) {
            $type = EXCLUSIVE;
        }

        $netmask = $this->getPost("netmask");
        $gateway = $this->getPost("gateway");


        if($this->getPost("format") == "on") {
            $format = CIDR;
        }
        else {
            $format = NORMAL;
        }

        if($type != null) {

            if($this->updateChangedUser() == false) {
                return false;
            }

            if($network->process == "create") {
                $update = "create";
                $old_data = array();
            }
            else if($network->process == "update") {
                $update = "update";
                $old_data = json_decode($network->old_data,true);
            }
            else {
                $update = "update";
                $old_data = array(
                    'netmask' => $network->netmask,
                    'format' => $network->format,
                    'gateway' => $network->gateway,
                );
            }

            $network->assign(array(
                'type' => $type,
                'format' => $format,
                'netmask' => $netmask,
                'gateway' => $gateway,
                'process' => $update,
                'old_data' => json_encode($old_data),
                'init' => '0',
            ));

            if (!$network->update()) {
                foreach ($network->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Network->updateNetwork",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }

        return true;

    }

    public function getInterfaceList($server_id = "1"): array
    {
        if($server_id == "1") {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }
        $networks = $this->queryBuilder->select([
            "ip_address.interface",
        ])
            ->from("ip_address")
            ->where([
                'conditions' => "ip_address.server_id = ?1",
                'bind' => [1 => $server_id]
            ])
            ->find();


        $interfaces = array();
        foreach($networks as $value) {
            if (strpos($value['interface'],':') == false) {
                $interfaces[$value['interface']] = $value['interface'];
            }
        }
        return $interfaces;
    }

    public function reReadIpAddress($server_id): bool
    {
        $network_list = $this->showCurl($server_id);
        $network_list = json_decode($network_list,true);

        if($server_id != "1") {
            $installed_network = IpAddress::find("server_id = '$server_id'")->toArray();
        }
        else {
            $installed_network = IpAddress::find()->toArray();
        }

        $curl_list = array();
        $notexists_ip_addresses = array();
        foreach($installed_network as $installed) {
            $curl_list[$installed['ip_address']] = $installed['netmask'];
        }
        foreach($network_list as $network) {
            if(PANEL_OS_TYPE == WINDOWS) {
                $index = $network['0'];
                $ip_address = $network['2'];
                $netmask = $this->env->convertToCidr($network['3']);
                $interface = base64_decode($network['1']);
            }
            else {
                $index = "";
                $ip_address = $network['2'];
                $netmask = $network['3'];
                $interface = $network['1'];
            }



            if(!array_key_exists($ip_address, $curl_list)) {
                $notexists_ip_addresses[] = array(
                    'index'         => $index,
                    'ip_address'    => $ip_address,
                    'netmask'       => $netmask,
                    'interface'     => $interface,
                    'type'          => PANEL_TYPE == CLOUD ? ($server_id == "1" ? MAIN_CLOUD : EXCLUSIVE) : EXCLUSIVE,
                    'is_default'    => PASSIVE,
                    'server_id'     => PANEL_TYPE == CLOUD ? ($server_id == "1" ? Servers::findFirst("type = 'main'")->id : $server_id) : Servers::findFirst()->id,
                );
            }
        }

        foreach($notexists_ip_addresses as $network) {
            $system_ip_address = new IpAddress();
            $network["id"] = $this->env->createUuid();
            $system_ip_address->assign($network);

            if (!$system_ip_address->create()) {
                foreach ($system_ip_address->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Network->reReadIpAddress",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    public function updateHostname($server_id = "1"): bool
    {

        $this->validation->Required(array(
            array(
                'field' => 'host_domain',
                'item' => $this->getPost("domain"),
                'message' => __('Domain name is required')
            ),
            array(
                'field' => 'hostname',
                'item' => $this->getPost("hostname"),
                'message' => __('Hostname is required')
            )
        ))->Domain($this->getPost("domain"),'host_domain',__('Please enter valid domain'));

        if(PANEL_OS_TYPE == WINDOWS) {
            $this->validation->WindowsHostname($this->getPost("hostname"),'hostname');
        }
        else {
            $this->validation->Domain($this->getPost("hostname").'.'.$this->getPost("domain"),'hostname',__('Please enter valid hostname'));
        }

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        $ipaddr = IpAddress::findFirst("is_default = '1' and server_id ='$server_id'");
        if(!$ipaddr) {
            $this->error_messages["ipaddr"] = __("Ip address not found");
            return false;
        }
        $this->setPost("ipaddr", $ipaddr->ip_address);
        $this->hostnameCurl($server_id);
        return true;
    }

    public function showCurl($server_id)
    {
        $networkmng = new Networking();
        $networkmng->setServerId($server_id);
        return $networkmng->show();
    }

    private function hostnameCurl($server_id)
    {
        $networkmng = new Networking();
        $networkmng->setServerId($server_id);
        $params = array(
            'host' => $this->getPost("hostname"),
            'domain' => $this->getPost("domain"),
            'ipaddr' => $this->getPost("ipaddr")
        );

        $networkmng->hostname($params);
    }

    public function writeNetworkConf($server_id, $server_type): bool
    {
        $networks = IpAddress::find("server_id = '$server_id'");

        if(!empty($networks)) {
            // Ethernet,Index,IpAddress,Netmask,Gateway
            $config = "";
            foreach($networks as $network) {
                if(strpos($server_type, LINUX) === false) {
                    $netmask = $this->env->cidrConvert($network->netmask);
                    $network_int = base64_encode($network->interface);
                    $network_interface = [0 => $network_int];
                    $network_interface['0'] = base64_decode($network_interface['0']);
                }
                else {
                    $netmask = $network->netmask;
                    $network_interface = explode(':', $network->interface);
                }

                if($config == null)
                    $config = $network_interface['0'] . ',' . $network->index . ',' . $network->ip_address . ',' . $netmask . ',' . $network->gateway . "\n";
                else
                    $config = $config . $network_interface['0'] . ',' . $network->index . ',' . $network->ip_address . ',' . $netmask . ',' . $network->gateway . "\n";
            }

            $networkmng = new Networking();
            $networkmng->setServerId($server_id);
            $networkmng->setConfig([
                "config" => $this->crypto->staticEncode($config),
            ]);
            $networkmng->init();
        }

        $websrvmng = new WebServer();
        $websrvmng->setServerId($server_id);
        $websrvmng->initVhost();
        return true;

    }

    private function updateChangedUser(): bool
    {
        $users = Users::findFirstById($this->auth->getUserId());
        if($users->changed_data != null)
            $changed_data = json_decode($users->changed_data,true);
        else
            $changed_data = array();

        $changed_data["network"] = true;
        $users->assign(array(
            'changed_data' => json_encode($changed_data),
        ));
        if (!$users->update()) {
            foreach ($users->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Network->updateChangedUser",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        if(REDIS_STATUS == true) {
            if($this->redis->hExists("Users", $this->auth->getUserId())) {
                $this->redis->hDel("Users", $this->auth->getUserId());
            }
        }
        return true;
    }

    public function applyChanges(): bool
    {
        $servers = Servers::find();

        foreach ($servers as $server) {
            $changed = false;
            $networks = IpAddress::find("server_id = '$server->id'");
            if(!empty($networks)) {
                foreach($networks as $network) {
                    if(empty($network->process))
                        continue;
                    /**
                     * DELETE
                     */
                    if($network->process == "delete") {
                        if(!$network->delete()) {
                            foreach($network->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            $this->log->error([
                                "Module" => "Network->applyChanges IpAddress delete",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                        }
                        $changed = true;
                    }

                    /**
                     * UPDATE
                     */
                    else if($network->process == "update") {
                        $sys_ipaddress = IpAddress::findFirst("id = '$network->id' AND server_id = '$server->id'");
                        $sys_ipaddress->process = "";
                        $sys_ipaddress->init = "1";
                        $sys_ipaddress->old_data = json_encode([]);
                        if(!$sys_ipaddress->update()) {
                            foreach($sys_ipaddress->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            $this->log->error([
                                "Module" => "Network->applyChanges IpAddress update",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                        }
                        $changed = true;
                    }

                    /**
                     * CREATE
                     */
                    else if($network->process == "create") {
                        $sys_ipaddress = IpAddress::findFirst("id = '$network->id' AND server_id = '$server->id'");
                        $sys_ipaddress->process = "";
                        $sys_ipaddress->init = "1";
                        $sys_ipaddress->old_data = json_encode([]);
                        if(!$sys_ipaddress->update()) {
                            foreach($sys_ipaddress->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            $this->log->error([
                                "Module" => "Network->applyChanges IpAddress update2",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                        }
                        $changed = true;
                    }
                }
                if($changed) {
                    $this->writeNetworkConf($server->id, $server->os);
                }
            }
        }

        $users = Users::findFirstById($this->auth->getUserId());
        $changed_data = json_decode($users->changed_data,true);
        if(!empty($changed_data['network'])) {
            unset($changed_data['network']);
        }

        $users->assign(array(
            'changed_data' => json_encode($changed_data)
        ));
        if(!$users->update()) {
            foreach($users->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            $this->log->error([
                "Module" => "Network->applyChanges Users update",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        if(REDIS_STATUS == true) {
            if($this->redis->hExists("Users", $this->auth->getUserId())) {
                $this->redis->hDel("Users", $this->auth->getUserId());
            }
        }
        return true;
    }

    public function cancelChanges(): bool
    {
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        $servers = Servers::find();
        foreach ($servers as $server) {
            $networks = IpAddress::find("server_id = '$server->id'");
            foreach($networks as $network) {
                if($network->process == "create") {
                    $sys_ipaddress = IpAddress::findFirst("id = '$network->id' AND server_id = '$server->id'");
                    if($sys_ipaddress) {
                        if(!$sys_ipaddress->delete()) {
                            foreach($sys_ipaddress->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            $this->log->error([
                                "Module" => "Network->cancelChanges IpAddress delete",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                        }
                    }
                    continue;
                }


                if($network->process == "delete") {
                    $sys_ipaddress = IpAddress::findFirst("id = '$network->id' AND server_id = '$server->id'");
                    $sys_ipaddress->assign(array(
                        'process' => '',
                        'init' => '1',
                        'old_data' => json_encode(array()),
                        "server_id" => $server->id
                    ));
                    if(!$sys_ipaddress->update()) {
                        foreach($sys_ipaddress->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        $this->log->error([
                            "Module" => "Network->cancelChanges IpAddress delete2",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                    }
                    continue;
                }

                if($network->process == "update") {
                    $sys_ipaddress = IpAddress::findFirst("id = '$network->id' AND server_id = '$server->id'");
                    if($this->env->isJson($network->old_data) == true) {
                        $old_data = json_decode($network->old_data,true);
                        $netmask = $old_data['netmask'];
                        $gateway = $old_data['gateway'];
                        $format = $old_data['format'];
                    }
                    else {
                        $netmask = $network->netmask;
                        $gateway = $network->gateway;
                        $format = $network->format;
                    }

                    $assign = array(
                        'process' => '',
                        'init' => '1',
                        'netmask' => $netmask,
                        'gateway' => $gateway,
                        'format' => $format,
                        'old_data' => json_encode(array()),
                        "server_id" => $server->id
                    );

                    $sys_ipaddress->assign($assign);

                    if(!$sys_ipaddress->update()) {
                        foreach($sys_ipaddress->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        $this->log->error([
                            "Module" => "Network->cancelChanges IpAddress update",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                    }
                }
            }
        }

        $users = Users::findFirstById($this->auth->getUserId());
        $changed_data = json_decode($users->changed_data,true);
        if(!empty($changed_data['network'])) {
            unset($changed_data['network']);
        }

        $users->assign(array(
            'changed_data' => json_encode($changed_data)
        ));
        if(!$users->update()) {
            foreach($users->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            $this->log->error([
                "Module" => "Network->cancelChanges Users update",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }
}