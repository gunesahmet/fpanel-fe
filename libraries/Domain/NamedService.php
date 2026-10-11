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
 */namespace FPanel\Domain;

use FPanel\Environments\Datatable;
use FPanel\Environments\Environments;
use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;
use FPanel\Models\DomainAlias;
use FPanel\Models\Domains;
use FPanel\Models\DomainServices;
use FPanel\Models\IpAddress;
use FPanel\Models\Subdomains;
use FPanel\Process\Bind;

/**
 * Class NamedService
 * @package FPanel\Named
 */
class NamedService extends Library
{
    public $dns_zone_id;
    public $ip_address;
    public $domain_name;
    protected $type;
    protected $value;
    protected $host;
    protected $opt = null;
    protected $old_value;
    protected $old_host;
    protected $new_value;
    protected $new_host;
    protected $weight = null;
    protected $port = null;

    public function createDNSRecords($restore = false): bool
    {
        try {
            if(!$restore) {
                if(!$this->validate()) {
                    return false;
                }
            }
            $record_type = $this->getPost("type");
            $zone_data = $this->getZoneData();

            switch($record_type) {
                case "NS":
                    $status = $this->createNSRecords($zone_data);
                    break;
                case "A":
                    $status = $this->createARecords($zone_data);
                    break;
                case "CNAME":
                    $status = $this->createCNAMERecords($zone_data);
                    break;
                case "MX":
                    $status = $this->createMXRecords($zone_data);
                    break;
                case "PTR":
                    $status = $this->createPTRRecords($zone_data);
                    break;
                case "TXT":
                    $status = $this->createTXTRecords($zone_data);
                    break;
                case "SRV":
                    $status = $this->createSRVRecords($zone_data);
                    break;
                case "AAAA":
                    $status = $this->createAAAARecords($zone_data);
                    break;
                case "CAA":
                    $status = $this->createCAARecords($zone_data);
                    break;
                case "DS":
                    $status = $this->createDSRecords($zone_data);
                    break;
                case "DNSKEY":
                    $status = $this->createDNSKEYRecords($zone_data);
                    break;
                default:
                    $status = false;
            }
            return $status;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    private function createNSRecords($zone_data): bool
    {
        if ((!empty($this->getPost("ns_host"))) && (!empty($this->getPost("ns_value")))) {
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'NS',
                'host'          => $this->getPost("ns_host"),
                'value'         => $this->getPost("ns_value"),
                'opt'           => $this->getPost("ns_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                $this->host = $this->getPost("ns_host").'.';
                $this->type = 'NS';
                $this->value = $this->getPost("ns_value").'.';
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createARecords($zone_data): bool
    {
        if ((!empty($this->getPost("a_value")))) {
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'A',
                'host'          => $this->getPost("a_host").'.'.$this->getPost("domain_name"),
                'value'         => $this->getPost("a_value"),
                'opt'           => $this->getPost("a_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            if($this->getPost("a_host") == null)
                $host_record = $this->getPost("domain_name").'.';
            else
                $host_record = $this->getPost("a_host").'.'.$this->getPost("domain_name").'.';

            $this->host = $host_record;
            $this->type = 'A';
            $this->opt = $this->getPost("a_opt");
            $this->value = $this->getPost("a_value");

            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createCNAMERecords($zone_data): bool
    {
        if ((!empty($this->getPost("cname_host"))) && (!empty($this->getPost("cname_value")))) {

            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'CNAME',
                'host'          => $this->getPost("cname_host").'.'.$this->getPost("domain_name"),
                'value'         => $this->getPost("cname_value"),
                'opt'           => $this->getPost("cname_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            $this->host = $this->getPost("cname_host").'.'.$this->getPost("domain_name").'.';
            $this->type = 'CNAME';
            $this->opt = $this->getPost("cname_opt");
            if (filter_var($this->getPost("cname_value"), FILTER_VALIDATE_IP)) {
                $value = $this->getPost("cname_value");
            }
            else {
                $value = $this->getPost("cname_value").'.';
            }
            $this->value = $value;
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createMXRecords($zone_data): bool
    {
        if ((!empty($this->getPost("mx_host"))) && (!empty($this->getPost("mx_value")))) {
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'MX',
                'host'          => $this->getPost("mx_host"),
                'value'         => $this->getPost("mx_value"),
                'opt'           => $this->getPost("mx_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            $this->host = $this->getPost("mx_host").'.';
            $this->type = 'MX';
            $this->opt = $this->getPost("mx_opt");
            $this->value = $this->getPost("mx_value").'.';
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createPTRRecords($zone_data): bool
    {
        if ((!empty($this->getPost("ptr_host")))) {
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'PTR',
                'host'          => $this->getPost("ptr_host"),
                'value'         => $this->getPost("ptr_value"),
                'opt'           => $this->getPost("ptr_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            $this->host = $this->getPost("ptr_host");
            $this->type = 'PTR';
            $this->opt = $this->getPost("ptr_opt");
            $this->value = $this->getPost("ptr_value").'.';
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createTXTRecords($zone_data): bool
    {
        if ((!empty($this->getPost("txt_host"))) && (!empty($this->getPost("txt_value")))) {
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'TXT',
                'host'          => $this->getPost("txt_host"),
                'value'         => $this->getPost("txt_value"),
                'opt'         => $this->getPost("txt_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            $this->host = $this->getPost("txt_host").'.';
            $this->type = 'TXT';
            $this->opt = $this->getPost("txt_opt");
            $this->value = $this->getPost("txt_value");
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createSRVRecords($zone_data): bool
    {
        if ((!empty($this->getPost("srv_service_name"))) && (!empty($this->getPost("srv_target_host")))) {
            if($this->getPost("srv_domain_name") != null)
                $srv_domain_name = $this->getPost("srv_domain_name").'.'.$this->getPost("domain_name");
            else
                $srv_domain_name = $this->getPost("domain_name");
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'SRV',
                'host'          => '_'.$this->getPost("srv_service_name").'.'.'_'.$this->getPost("srv_protocol").'.'.$srv_domain_name,
                'value'         => $this->getPost("srv_target_host"),
                'opt'           => $this->getPost("srv_priority"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'port'          => $this->getPost("srv_target_port"),
                'weight'        => $this->getPost("srv_priority_weight"),
                'order'         => "1",
            );
            $this->host = '_'.$this->getPost("srv_service_name").'.'.'_'.$this->getPost("srv_protocol").'.'.$srv_domain_name.'.';
            $this->type = 'SRV';
            $this->opt = $this->getPost("srv_priority");
            $this->value = $this->getPost("srv_target_host").'.';
            $this->port = $this->getPost("srv_target_port");
            $this->weight = $this->getPost("srv_priority_weight");
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return true;
        }
    }

    private function createAAAARecords($zone_data): bool
    {
        if ((!empty($this->getPost("aaaa_host"))) && (!empty($this->getPost("aaaa_value")))) {
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'AAAA',
                'host'          => $this->getPost("aaaa_host"),
                'value'         => $this->getPost("aaaa_value"),
                'opt'         => $this->getPost("aaaa_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            $this->host = $this->getPost("aaaa_host").'.';
            $this->type = 'AAAA';
            $this->opt = $this->getPost("aaaa_opt");
            $this->value = $this->getPost("aaaa_value");
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createCAARecords($zone_data): bool
    {
        if ((!empty($this->getPost("caa_host"))) && (!empty($this->getPost("caa_value")))) {
            $data = array(
                'dns_zone_id'   => $zone_data->id,
                'type'          => 'CAA',
                'host'          => $this->getPost("caa_host"),
                'value'         => $this->getPost("caa_value"),
                'opt'         => $this->getPost("caa_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'order'         => "1",
            );
            $this->host = $this->getPost("caa_host").'.';
            $this->type = 'CAA';
            $this->opt = $this->getPost("caa_opt");
            $this->value = $this->getPost("caa_value");
            if(!$this->createRecords($data)) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function createDSRecords($zone_data)
    {

    }

    private function createDNSKEYRecords($zone_data)
    {

    }

    public function updateDNSRecords(): bool
    {
        $record_type = $this->getPost("type");
        if(!$this->validate()) {
            return false;
        }
        $id = $this->getPost("id");
        $dns_zone_id = $this->getPost("dns_zone_id");
        $dns = DnsRecords::findFirst("id = '$id' AND dns_zone_id = '$dns_zone_id'");
        if(!$dns) {
            $this->error_messages["content"] = __("DNS Record not found");
            return false;
        }
        switch($record_type) {
            case "NS":
                $status = $this->updateNSRecords($dns);
                break;
            case "A":
                $status = $this->updateARecords($dns);
                break;
            case "CNAME":
                $status = $this->updateCNAMERecords($dns);
                break;
            case "MX":
                $status = $this->updateMXRecords($dns);
                break;
            case "PTR":
                $status = $this->updatePTRRecords($dns);
                break;
            case "TXT":
                $status = $this->updateTXTRecords($dns);
                break;
            case "SRV":
                $status = $this->updateSRVRecords($dns);
                break;
            default:
                $status = false;
        }
        $this->initCurl($this->getPost("domain_id"));

        return $status;
    }

    private function updateNSRecords(DnsRecords $dns): bool
    {
        $new_value = $dns->value;
        if($this->getPost("ns_value") != null) {
            if(trim($this->getPost("old_value")) != trim($this->getPost("ns_value"))) {
                $new_value = $this->getPost("ns_value");
            }
            $dns->value = $new_value;

            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
            else {
                $this->initCurl($this->getPost("domain_id"));
                return true;
            }
        }
        return false;
    }

    private function updateARecords(DnsRecords $dns): bool
    {
        if($this->getPost("a_value") != null) {
            $dns->value = $this->getPost("a_value");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
        }
        else {
            return false;
        }

        if($this->getPost("a_host") != null) {
            $dns->host = $this->getPost("a_host").'.'.$this->getPost("domain_name");

            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
        }
        else {
            return false;
        }
        return true;
    }

    private function updateCNAMERecords(DnsRecords $dns): bool
    {
        if(($this->getPost("cname_value") == null) || ($this->getPost("cname_host") == null))
            return false;

        $dns->value = $this->getPost("cname_value");
        if (!$dns->update()) {
            foreach ($dns->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        $dns->host = $this->getPost("cname_host").'.'.$this->getPost("domain_name");
        if (!$dns->update()) {
            foreach ($dns->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }
        return true;
    }

    private function updateMXRecords(DnsRecords $dns): bool
    {
        if($this->getPost("mx_value") != null) {
            $dns->value = $this->getPost("mx_value");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
        }
        else {
            return false;
        }

        if($this->getPost("mx_host") != null) {
            $dns->host = $this->getPost("mx_host");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
        }

        if($this->getPost("mx_opt") != null) {
            $dns->opt = $this->getPost("mx_opt");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
        }
        else {
            return false;
        }
        return true;
    }

    private function updatePTRRecords(DnsRecords $dns): bool
    {
        $dns->host = $this->getPost("ptr_host");
        $dns->value = $this->getPost("ptr_value");
        $dns->opt = $this->getPost("ptr_opt");

        if (!$dns->update()) {
            foreach ($dns->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }


        return true;
    }

    private function updateTXTRecords(DnsRecords $dns): bool
    {
        if($this->getPost("txt_value") != null) {
            $dns->value = $this->getPost("txt_value");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }

    private function updateSRVRecords(DnsRecords $dns): bool
    {
        $dns_value = $dns->value;
        if($this->getPost("srv_domain_name") != null)
            $srv_domain_name = $this->getPost("srv_domain_name").'.'.$this->getPost("domain_name");
        else
            $srv_domain_name = $this->getPost("domain_name");

        $dns_host = '_'.$this->getPost("srv_service_name").'.'.'_'.$this->getPost("srv_protocol").'.'.$srv_domain_name;

        $this->old_host = $dns->host;
        $this->new_host = $dns_host;

        $this->old_value = $dns_value;
        $this->new_value = $this->getPost("srv_target_host");

        $dns->assign(array(
            'host'          => '_'.$this->getPost("srv_service_name").'.'.'_'.$this->getPost("srv_protocol").'.'.$srv_domain_name,
            'value'         => $this->getPost("srv_target_host"),
            'opt'         => $this->getPost("srv_priority"),
            'port'    => $this->getPost("srv_target_port"),
            'weight'    => $this->getPost("srv_priority_weight"),
        ));

        if (!$dns->update()) {
            foreach ($dns->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        $this->host = '_'.$this->getPost("srv_service_name").'.'.'_'.$this->getPost("srv_protocol").'.'.$srv_domain_name.'.';
        $this->type = 'SRV';
        $this->opt = $this->getPost("srv_priority");
        $this->value = $this->getPost("srv_target_host").'.';
        $this->port = $this->getPost("srv_target_port");
        $this->weight = $this->getPost("srv_priority_weight");
        return true;
    }

    public function deleteDNSRecords($host, $domain_id): bool
    {
        $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
        if($dns_zone) {
            $dns_zone_id = $dns_zone->id;
            if($host != null) {
                $dns = DnsRecords::findFirst("host = '$host' AND dns_zone_id = '$dns_zone_id'");
                if(!$dns) {
                    return false;
                }
                else {
                    $dns_data = $dns->toArray();

                    if(!$dns->delete()) {
                        foreach($dns->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                    }
                }
            }
            else {
                $id = $this->getPost("id");
                $dns = DnsRecords::findFirst("id = '$id' AND dns_zone_id = '$dns_zone_id'");
                if(!$dns)
                    return false;
                else
                    $dns_data = $dns->toArray();

                if(!$dns->delete()) {
                    foreach($dns->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                }
            }


            if (filter_var($dns_data['host'], FILTER_VALIDATE_IP)) {
                $host = $dns_data['host'];
            }
            else {
                $host = $dns_data['host'].'.';
            }
            if (filter_var($dns_data['value'], FILTER_VALIDATE_IP)) {
                $value = $dns_data['value'];
            }
            else {
                if($dns_data['type'] == "TXT")
                    $value = $dns_data['value'];
                else
                    $value = $dns_data['value'].'.';
            }
            $this->host = $host;
            $this->type = $dns_data['type'];
            $this->opt = $dns_data['opt'];
            $this->value = $value;
            $this->initCurl($domain_id);
            return true;
        }
        else {
            return false;
        }

    }

    public function saveDefaultDNS($domain_id, $dns_server_id): bool
    {
        $default_dns_records = new Environments();

        $get_default_dns_records = $default_dns_records->getDefaultDNSRecords($dns_server_id);

        if(empty($get_default_dns_records)) {
            $this->error_messages['content'] = __('Request Failed. Default DNS Records not found');
            return false;
        }

        $time_stamp = time();

        $dns_records = array();

        foreach ($get_default_dns_records as $value) {
            if($value['opt'] == null)
                $opt = "null";
            else
                $opt = $value['opt'];

            if (strpos($value['host'],'[ip]') !== false) {
                $value_host = str_replace('[ip]', $this->ip_address, $value['host']);
            }
            else {
                $value_host = str_replace('[domain]', $this->getPost("name"), $value['host']);
            }

            if (strpos($value['value'],'[ip]') !== false) {
                $value_value = str_replace('[ip]', $this->ip_address, $value['value']);
            }
            else {
                $value_value = str_replace('[domain]', $this->getPost("name"), $value['value']);
            }

            if($value['host'])
                $dns_records[] = array(
                    'dns_zone_id'   => $this->dns_zone_id,
                    'type'          => $value['type'],
                    'host'          => $value_host,
                    'value'         => $value_value,
                    'opt'           => $opt,
                    'time_stamp'    => $time_stamp,
                    'order'         => $value['order']
                );
        }


        $qbuilder = new QueryBuilder();
        $qbuilder->into("dns_records")
            ->columns([
                "id",
                'dns_zone_id',
                'type',
                'host',
                'value',
                'opt',
                'time_stamp',
                'order'
            ]);

        foreach ($dns_records as $dns_record) {
            $qbuilder->assign([
                $this->env->createUuid(),
                $dns_record['dns_zone_id'],
                $dns_record['type'],
                $dns_record['host'],
                $dns_record['value'],
                $dns_record['opt'],
                $dns_record['time_stamp'],
                $dns_record['order'],
            ]);
        }
        if(!$qbuilder->create()) {
            foreach ($qbuilder->getMessages() as $message) {
                $this->error_messages[] = $message;
            }
            $this->log->error([
                "Module" => "NamedService->saveDefaultDNS",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        $this->initCurl($domain_id);
        return true;
    }

    public function saveZone()
    {
        $dns_zone = new DnsZone();
        $dns_zone->assign(array(
            "id"            => $this->env->createUuid(),
            'zone_type'     => $this->getPost("zone_type"),
            'domain_id'     => $this->getPost("domain_id"),
            'domain_name'   => $this->getPost("domain_name"),
            'status'        => $this->getPost("status"),
            'email'         => $this->getPost("email"),
            'type'          => $this->getPost("type"),
            'ttl'           => $this->getPost("ttl"),
            'refresh'       => $this->getPost("refresh"),
            'retry'         => $this->getPost("retry"),
            'expire'        => $this->getPost("expire"),
            'minimum'       => $this->getPost("minimum"),
            'serial'        => $this->getPost("serial"),
            'serial_format' => $this->getPost("serial_format"),
            'server_id'     => $this->getPost("server_id"),
        ));

        if(!$dns_zone->create()) {
            foreach ($dns_zone->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "NamedService->saveZone",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            return $dns_zone->id;
        }

    }

    public function deleteDomainZone($domain_id): bool
    {
        $dnszone = DnsZone::findFirst("domain_id = '$domain_id'");
        if($dnszone) {
            $this->dns_zone_id = $dnszone->id;
        }
        return true;
    }

    public function deleteDomainRecords($domain_id): bool
    {
        $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
        if($dns_zone) {
            $dns = DnsRecords::find("dns_zone_id = '$dns_zone->id'");
            if(!empty($dns)) {
                if(!$dns->delete()) {
                    foreach($dns->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    $this->log->error([
                        "Module" => "NamedService->deleteDomainRecords DnsRecords",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                }
            }

            if(!$dns_zone->delete()) {
                foreach($dns_zone->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "NamedService->deleteDomainRecords DnsZone",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
        return true;
    }

    public function dnsRecordList($domain_id = null, bool $nested = false): array
    {
        $datatable = new Datatable();
        if($nested) {
            $delete_button = $datatable->buttons("dns/delete/$1/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("dns/edit/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('dns/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('dns/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
        }


        $datatable->select(array('dns_records.id','dns_records.host','dns_records.type','dns_records.value','dns_records.order','dns_records.id as rec_id'))
            ->from("domains")
            ->innerJoin('dns_zone','dns_zone.domain_id = domains.id')
            ->innerJoin('dns_records','dns_records.dns_zone_id = dns_zone.id')
            ->where("domains.id = '$domain_id'")
            ->searchColumn(array('dns_records.host','dns_records.type','dns_records.value'))
            ->addColumn('actions', $edit_button.$delete_button,'dns_records.id')
            ->editColumn('dns_records.id','<input type="checkbox" class="checkbox-blue" value="$1" name="records[]"/>', 'dns_records.id')
            ->unsetColumns('dns_records.order')
            ->unsetColumns('rec_id');

        if($nested) {
            $datatable->editColumn('dns_records.host','<a href="'. "dns/edit/$1/domain/$domain_id\">$2</a>", 'rec_id, dns_records.host');
        }
        else {
            $datatable->editColumn('dns_records.host','<a href="'. 'dns/edit/$1">$2</a>', 'rec_id, dns_records.host');
        }

        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value) {
            $data['aaData'][$key][4] = $value[6];
            unset($data['aaData'][$key][5]);
            unset($data['aaData'][$key][6]);
        }
        return $data;

    }

    public function initCurl($domain_id): bool
    {
        $domains = Domains::findFirstById($domain_id);
        $domain_alias = DomainAlias::find("domain_id = '$domain_id'");

        $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
        $dns_records = DnsRecords::find("dns_zone_id = '$dns_zone->id'");
        $time_stamp = $this->date->getCustomDateTime("now", "Ymd") . "01"; // Your SOA serial number is: 2022080302. This appears to be in the recommended format of YYYYMMDDnn

        $soa_records = null;

        $dns_temp = null;
        $i = 0;
        foreach($dns_records as $dns) {
            if(!$this->env->isIP($dns->value)) {
                if($dns->type == "TXT") {
                    $value = "\"$dns->value\"";
                }
                else {
                    $value = $dns->value.".";
                }
            }
            else
                $value = $dns->value;


            if($dns->type == "SRV") {
                $dns_temp .= "$dns->host.\t\t86400 IN $dns->type\t$dns->opt $dns->weight $dns->port\t$value\n";
                continue;
            }

            if($dns->type == "NS") {
                if($soa_records == null)
                    $soa_records = $dns->value .". ";
                else {
                    if($i == 1) {
                        if($dns->value == null) {
                            $soa_records .= "admin@$domains->name.";
                        }
                        else {
                            $soa_records .= $dns->value .".";
                        }
                    }
                }
                $i++;
            }


            if(($dns->opt != null) && ($dns->opt != "null") && ($dns->opt != "0"))
                $opt = "$dns->opt\t";
            else
                $opt = "";
            $dns_temp .= "$dns->host.\t\tIN $dns->type\t$opt $value\n";
        }

        $dns_zone_temp = "; *** This file is automatically generated by System ***
\$TTL    86400
@       IN      SOA     $soa_records (
\t\t\t$time_stamp      ; Serial
\t\t\t10800   ; Refresh
\t\t\t3600    ; Retry
\t\t\t604800  ; Expire
\t\t\t10800 ) ; Minimum
\t\t\t;\n";

        $dns_zone_temp .= $dns_temp;
        foreach($domain_alias as $alias) {
            $dnsmng = new Bind();
            $domain_alias_temps = str_replace($domains->name, $alias->alias_name, $dns_zone_temp);
            $dnsmng->setServerId($domains->dns_server_id);
            $params = array(
                'domain' => $alias->alias_name,
                "zone-data" => $this->crypto->staticEncode($domain_alias_temps)
            );
            $dnsmng->init($params);
        }

        $dnsmng = new Bind();
        $dnsmng->setServerId($domains->dns_server_id);
        $params = array(
            'domain' => $domains->name,
            "zone-data" => $this->crypto->staticEncode($dns_zone_temp)
        );
        $dnsmng->init($params);
        return true;
    }

    public function restoreDNSRecords($domain_id): bool
    {
        $domain = Domains::findFirstById($domain_id);
        if($domain) {
            $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
            if($dns_zone) {
                $ip = IpAddress::findFirstById($domain->ipaddr_id);
                if($ip) {
                    $dns_records = DnsRecords::find("dns_zone_id = '$dns_zone->id'");
                    if(!empty($dns_records)) {
                        if(!$dns_records->delete()) {
                            foreach($dns_records->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            $this->log->error([
                                "Module" => "NamedService->restoreDNSRecords DnsRecords",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                        }
                    }
                    $this->ip_address = $ip->ip_address;
                    $this->dns_zone_id = $dns_zone->id;
                    $this->setPost("name", $domain->name);
                    if(!$this->saveDefaultDNS($domain_id, $domain->dns_server_id)) {
                        return false;
                    }
                    $domain_alias = DomainAlias::find("domain_id = '$domain_id'");
                    $alias_list = "";
                    if($domain_alias) {
                        foreach($domain_alias->toArray() as $alias) {
                            if($alias_list == null)
                                $alias_list = $alias['alias_name'];
                            else
                                $alias_list = $alias_list . '#' . $alias['alias_name'];
                        }
                    }

                    $subdomains = Subdomains::find("domain_id = '$domain->id'");
                    if($subdomains) {
                        foreach($subdomains->toArray() as $subdomain) {
                            $dns_post_data = array(
                                // Değişkenler
                                'update'   => false,
                                'domain_id'   => $domain->id,
                                'domain_name'   => $domain->name,
                                'time_stamp'   => time(),
                                'type'   => 'A',

                                // A
                                'a_host' => $subdomain['subdomain_name'],
                                'a_value' => $ip->ip_address,
                                'a_opt' => '',

                                'old_host' => '',
                                'old_type' => '',
                                'old_value' => '',
                            );
                            $this->setPosts($dns_post_data);
                            $this->createDNSRecords();
                        }
                    }
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    public function validate(): bool
    {
        $required = array();
        $required[] = array(
            'field' => 'record_type',
            'item' => $this->getPost("type"),
            'message' => __('Please select a type')
        );
        $this->validation->Required($required);
        $this->validation->InclusionIn($this->getPost("type"),'record_type',array('NS','A','MX','PTR','CNAME','TXT','SRV'),__('Please select a type.'));
        switch($this->getPost("type")) {
            case "NS":
                $required[] = array(
                    'field' => 'ns_record',
                    'item' => $this->getPost("ns_value"),
                    'message' => __('NS Record is required')
                );
                $this->validation->Required($required);
                $this->validation->CheckRecord("NS", $this->getPost("ns_value"),'ns_record',__('Please enter valid ns record.'));
                $this->validation->CheckDnsUnique(array(
                    'field' => 'ns_record',
                    'type' => 'NS',
                    'host' => $this->getPost("domain_name"),
                    'value' => $this->getPost("ns_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("domain_id"), $this->getPost("update"));
                break;
            case "A":
                $required[] = array(
                    'field' => 'a_record_ip_address',
                    'item' => $this->getPost("a_value"),
                    'message' => __('Please enter A Record Value')
                );
                $this->validation->Required($required);
                $this->validation->Ip($this->getPost("a_value"),'a_record_ip_address');
                $this->validation->CheckRecord('A', $this->getPost("a_host"),'a_record');
                $this->validation->CheckDnsUnique(array(
                    'field' => 'a_record',
                    'type' => 'A',
                    'host' => $this->getPost("a_host") . '.'. $this->getPost("domain_name"),
                    'value' => $this->getPost("a_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("domain_id"), $this->getPost("update"));
                break;
            case "MX":
                $required[] = array(
                    'field' => 'mx_record',
                    'item' => $this->getPost("mx_value"),
                    'message' => __('MX Record is required')
                );
                $this->validation->Required($required);
                $this->validation->InclusionIn($this->getPost("mx_opt"),'mx_record_priority',array('0','5','10','15','20','25','30','35','40','45','50'), __('Please select priority.'));
                $this->validation->CheckRecord("NS", $this->getPost("mx_value"),'mx_record',__('Please enter valid mx record.'));
                $this->validation->CheckDnsUnique(array(
                    'field' => 'mx_record',
                    'type' => 'MX',
                    'host' => $this->getPost("domain_name"),
                    'value' => $this->getPost("mx_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("domain_id"), $this->getPost("update"));
                break;
            case "PTR":
                $required[] = array(
                    'field' => 'ptr_record_ip_address',
                    'item' => $this->getPost("ptr_host"),
                    'message' => __('IP Address is required')
                );
                $required[] = array(
                    'field' => 'ptr_record_domain_name',
                    'item' => $this->getPost("ptr_value"),
                    'message' => __('Domain name is required')
                );
                $this->validation->Required($required);
                $this->validation->InclusionIn($this->getPost("ptr_opt"),'ptr_record_ip_address',array('8','16','24'), __('Please select ip block.'));
                $this->validation->Ip($this->getPost("ptr_host"),'ptr_record_ip_address');
                $this->validation->CheckRecord('A', $this->getPost("ptr_value"),'ptr_record_domain_name');
                $this->validation->CheckDnsUnique(array(
                    'field' => 'ptr_record_domain_name',
                    'type' => 'PTR',
                    'host' => $this->getPost("ptr_host"),
                    'value' => $this->getPost("ptr_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("domain_id"), $this->getPost("update"));
                break;
            case "CNAME":
                $required[] = array(
                    'field' => 'cname_record_domain_name',
                    'item' => $this->getPost("cname_host"),
                    'message' => __('CNAME Domain Name is required')
                );
                $required[] = array(
                    'field' => 'cname_record',
                    'item' => $this->getPost("cname_value"),
                    'message' => __('CNAME Record is required')
                );
                $this->validation->Required($required);
                $this->validation->CheckRecord('CNAME', $this->getPost("cname_value"),'cname_record',__('Please enter valid record.'));
                $this->validation->CheckRecord('A', $this->getPost("cname_host"),'cname_record_domain_name',__('Please enter CNAME value.'));
                $this->validation->CheckDnsUnique(array(
                    'field' => 'cname_record_domain_name',
                    'type' => 'CNAME',
                    'host' => $this->getPost("cname_host").'.'.$this->getPost("domain_name"),
                    'value' => $this->getPost("cname_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("domain_id"), $this->getPost("update"));
                break;
            case "TXT":
                $required[] = array(
                    'field' => 'txt_record',
                    'item' => $this->getPost("txt_value"),
                    'message' => __('TXT Record is required')
                );
                $this->validation->Required($required);
                $this->validation->TXTSyntax($this->getPost("txt_value"),'txt_record',__('Please enter valid syntax'));
                $this->validation->CheckDnsUnique(array(
                    'field' => 'txt_record',
                    'type' => 'TXT',
                    'host' => $this->getPost("txt_host"),
                    'value' => $this->getPost("txt_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("domain_id"), $this->getPost("update"));
                break;
            case "SRV":
                if($this->getPost("srv_domain_name") != null)
                    $srv_domain_name = $this->getPost("srv_domain_name").'.'.$this->getPost("domain_name");
                else
                    $srv_domain_name = $this->getPost("domain_name");
                $required[] = array(
                    'field' => 'srv_service_name',
                    'item' => $this->getPost("srv_service_name"),
                    'message' => __('Service name is required')
                );
                $required[] = array(
                    'field' => 'srv_protocol',
                    'item' => $this->getPost("srv_protocol"),
                    'message' => __('Protocol is required')
                );
                $required[] = array(
                    'field' => 'srv_priority',
                    'item' => $this->getPost("srv_priority"),
                    'message' => __('Priority is required')
                );
                $required[] = array(
                    'field' => 'srv_priority_weight',
                    'item' => $this->getPost("srv_priority_weight"),
                    'message' => __('Priority Weight is required')
                );
                $required[] = array(
                    'field' => 'srv_target_host',
                    'item' => $this->getPost("srv_target_host"),
                    'message' => __('Target Host is required')
                );
                $required[] = array(
                    'field' => 'srv_target_port',
                    'item' => $this->getPost("srv_target_port"),
                    'message' => __('Target Port is required')
                );
                $this->validation->Required($required);
                $this->validation->CheckDnsUnique(array(
                    'field' => 'srv_record',
                    'type' => 'SRV',
                    'host' => '_'.$this->getPost("srv_service_name").'.'.'_'.$this->getPost("srv_protocol").'.'.$srv_domain_name,
                    'value' => $this->getPost("srv_target_host"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("domain_id"), $this->getPost("update"));
                $this->validation->Domain($this->getPost("srv_target_host"),'srv_target_host');
                $this->validation->Domain($this->getPost("srv_domain_name").'.'.$this->getPost("domain_name"),'srv_domain_name');
                $this->validation->Port($this->getPost("srv_target_port"),'srv_target_port');
                $this->validation->InclusionIn($this->getPost("srv_priority"),'srv_priority',array('0','5','10','15','20','25','30','35','40','45','50'), __('Please select priority.'));
                $this->validation->InclusionIn($this->getPost("srv_priority_weight"),'srv_priority_weight',array('0','5','10','15','20','25','30','35','40','45','50'), __('Please select priority.'));

                break;
        }

        if(!$this->validation->Validate()) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    private function getZoneData(): DnsZone
    {
        $domain_id = $this->getPost("domain_id");
        return DnsZone::findFirst("domain_id = '$domain_id'");
    }

    private function createRecords($data): bool
    {
        $dns = new DnsRecords();
        $assign = array(
            "id"            => $this->env->createUuid(),
            'dns_zone_id'   => $data['dns_zone_id'],
            'type'          => $data['type'],
            'host'          => preg_replace('{^\.}', '', $data['host'], 1),
            'value'         => $data['value'],
            'opt'           => $data['opt'],
            'time_stamp'    => $data['time_stamp'],
            'order'         => $data['order'],
        );
        if($data['type'] == "SRV") {
            $assign['port'] = $data['port'];
            $assign['weight'] = $data['weight'];
        }

        $dns->assign($assign);
        if (!$dns->create()) {
            foreach ($dns->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "NamedService->createRecords",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            return true;
        }
    }

    public function changeDnsSettings($dns_service, $domain_id): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        $services = DomainServices::findFirst("domain_id = '$domain_id'");
        $domain = Domains::findFirstById($domain_id);
        $dnsmng = new Bind();
        $dnsmng->setServerId($domain->dns_server_id);
        $domain_aliases = DomainAlias::find("domain_id = '$domain_id'");

        $alias_list = null;
        foreach($domain_aliases as $alias) {
            if($alias_list == null)
                $alias_list = $alias->alias_name;
            else
                $alias_list = $alias_list . ' ' . $alias->alias_name;
        }
        if($dns_service == ACTIVE) {
            if(!$this->restoreDNSRecords($domain_id))
                return false;
            $dnsmng = new Bind();
            $params = array(
                'domain' => $domain->name,
            );
            $dnsmng->create($params);
            $this->initCurl($domain_id);
        }
        else {
            $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
            $dns = DnsRecords::find("dns_zone_id = '$dns_zone->id'");
            if(!empty($dns)) {
                if(!$dns->delete()) {
                    foreach($dns->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    $this->log->error([
                        "Module" => "NamedService->changeDnsSettings DnsRecords",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                }
            }
            $params = array(
                'domain' => $domain->name,
                'alias' => $alias_list
            );
            $dnsmng->remove($params);
        }

        $services->dns = $dns_service;
        if (!$services->update()) {
            foreach ($services->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "NamedService->changeDnsSettings DomainServices",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

}