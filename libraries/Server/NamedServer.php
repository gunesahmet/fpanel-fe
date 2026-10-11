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
use FPanel\Environments\Environments;
use FPanel\Library\Library;
use FPanel\Models\DnsTemplate;
use FPanel\Process\Bind;

/**
 * Class NamedServer
 * @package FPanel\Named
 */
class NamedServer extends Library
{
    public $dns_zone_id;
    protected $domain;
    protected $host;
    protected $type;
    protected $value;
    protected $opt;
    protected $old_host;
    protected $old_type;
    protected $old_value;

    public function createDNSRecords(): bool
    {
        $status = $this->validate();
        if(!$status) {
            return false;
        }

        $record_type = $this->getPost("type");
        switch($record_type) {
            case "NS":
                $status = $this->createNSRecords();
                break;
            case "A":
                $status = $this->createARecords();
                break;
            case "CNAME":
                $status = $this->createCNAMERecords();
                break;
            case "MX":
                $status = $this->createMXRecords();
                break;
            case "PTR":
                $status = $this->createPTRRecords();
                break;
            case "TXT":
                $status = $this->createTXTRecords();
                break;
            default:
                $status = false;
        }
        return $status;
    }

    private function createRecords($data): bool
    {
        $dns = new DnsTemplate();
        $dns->assign(array(
            'type'          => $data['type'],
            'host'          => $data['host'],
            'value'         => $data['value'],
            'opt'           => $data['opt'],
            'time_stamp'    => $data['time_stamp'],
            'order'         => '0',
            'server_id'     => $this->getPost("server_id"),
        ));
        if (!$dns->create()) {
            foreach ($dns->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "NamedServer->createRecords",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            $this->convertForSystem($data);
            $this->createRecordCurl();
            return true;
        }
    }

    private function createNSRecords(): bool
    {
        if ((!empty($this->getPost("ns_host"))) && (!empty($this->getPost("ns_value")))) {
            $data = array(
                'type'          => 'NS',
                'host'          => $this->getPost("ns_host"),
                'value'         => $this->getPost("ns_value"),
                'opt'           => $this->getPost("ns_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'server_id'     => $this->getPost("server_id"),
            );
            return $this->createRecords($data);
        }
        else {
            return false;
        }
    }

    private function createARecords(): bool
    {
        if ((!empty($this->getPost("a_host"))) && (!empty($this->getPost("a_value")))) {

            $data = array(
                'type'          => 'A',
                'host'          => $this->getPost("a_host"),
                'value'         => $this->getPost("a_value"),
                'opt'           => $this->getPost("a_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'server_id'     => $this->getPost("server_id"),
            );
            return $this->createRecords($data);
        }
        else {
            return false;
        }
    }

    private function createCNAMERecords(): bool
    {
        if ((!empty($this->getPost("cname_host"))) && (!empty($this->getPost("cname_value")))) {
            $data = array(
                'type'          => 'CNAME',
                'host'          => $this->getPost("cname_host"),
                'value'         => $this->getPost("cname_value"),
                'opt'           => $this->getPost("cname_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'server_id'     => $this->getPost("server_id"),
            );
            return $this->createRecords($data);
        }
        else {
            return false;
        }
    }

    private function createMXRecords(): bool
    {
        if ((!empty($this->getPost("mx_host"))) && (!empty($this->getPost("mx_value")))) {
            $data = array(
                'type'          => 'MX',
                'host'          => $this->getPost("mx_host"),
                'value'         => $this->getPost("mx_value"),
                'opt'           => $this->getPost("mx_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'server_id'     => $this->getPost("server_id"),
            );
            $messages = $this->createRecords($data);
            $this->domain = $this->getPost("mx_host");
            return $messages;
        }
        else {
            return false;
        }
    }

    private function createPTRRecords(): bool
    {
        if ((!empty($this->getPost("ptr_host"))) && (!empty($this->getPost("ptr_value")))) {

            $data = array(
                'type'          => 'PTR',
                'host'          => $this->getPost("ptr_host"),
                'value'         => $this->getPost("ptr_value"),
                'opt'           => $this->getPost("ptr_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'server_id'     => $this->getPost("server_id"),
            );
            return $this->createRecords($data);
        }
        else {
            return false;
        }
    }

    private function createTXTRecords(): bool
    {
        if ((!empty($this->getPost("txt_host"))) && (!empty($this->getPost("txt_value")))) {
            $data = array(
                'type'          => 'TXT',
                'host'          => $this->getPost("txt_host"),
                'value'         => $this->getPost("txt_value"),
                'opt'           => $this->getPost("txt_opt"),
                'time_stamp'    => $this->getPost("time_stamp"),
                'server_id'     => $this->getPost("server_id"),
            );
            return $this->createRecords($data);
        }
        else {
            return false;
        }
    }

    public function updateDNSRecords(): bool
    {
        if($this->validate() == false) {
            return false;
        }
        $record_type = $this->getPost("type");
        $dns = DnsTemplate::findFirstById($this->getPost("id"));
        if(!$dns) {
            $this->error_messages["content"] = __("Dns record not found");
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
            default:
                $status = false;
        }
        return $status;
    }

    private function updateNSRecords(DnsTemplate $dns): bool
    {
        $this->old_value = $dns->value;
        $this->value = $this->getPost("ns_value");

        $this->old_host = $dns->host;
        $this->host = $dns->host;

        $this->old_type = $dns->type;
        $this->type = 'NS';

        if($this->getPost("ns_value") != null) {
            $dns->value = $this->getPost("ns_value");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateNSRecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $data = array(
                    'host' => $this->host,
                    'type' => $this->type,
                    'value' => $this->value,
                    'opt' => $this->getPost("ns_opt"),
                    'old_host' => $this->old_host,
                    'old_type' => $this->old_type,
                    'old_value' => $this->old_value,
                );
                $this->convertForSystem($data);
                $this->updateRecordCurl();
                return true;
            }
        }
        return true;
    }

    private function updateARecords(DnsTemplate $dns): bool
    {
        $this->old_value = $dns->value;
        $this->value = $this->getPost("a_value");

        $this->old_host = $dns->host;
        $this->host = $this->getPost("a_host");

        $this->old_type = $dns->type;
        $this->type = 'A';

        if($this->getPost("a_value") != null) {
            $dns->value = $this->getPost("a_value");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateARecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            return false;
        }
        if($this->getPost("a_host") != null) {
            $dns->host = $this->getPost("a_host");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateARecords2",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            return false;
        }
        $data = array(
            'host' => $this->host,
            'type' => $this->type,
            'value' => $this->value,
            'opt' => $this->getPost("a_opt"),
            'old_host' => $this->old_host,
            'old_type' => $this->old_type,
            'old_value' => $this->old_value,
        );
        $this->convertForSystem($data);
        $this->updateRecordCurl();
        return true;
    }

    private function updateCNAMERecords(DnsTemplate $dns): bool
    {
        $this->old_value = $dns->value;
        $this->value = $this->getPost("cname_value");

        $this->old_host = $dns->host;
        $this->host = $this->getPost("cname_host");

        $this->old_type = $dns->type;
        $this->type = 'CNAME';

        if($this->getPost("cname_value") != null) {
            $dns->value = $this->getPost("cname_value");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateCNAMERecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            return false;
        }
        if($this->getPost("cname_host") != null) {
            $dns->host = $this->getPost("cname_host");

            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateCNAMERecords2",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            return false;
        }
        $data = array(
            'host' => $this->host,
            'type' => $this->type,
            'value' => $this->value,
            'opt' => $this->getPost("cname_opt"),
            'old_host' => $this->old_host,
            'old_type' => $this->old_type,
            'old_value' => $this->old_value,
        );
        $this->convertForSystem($data);
        $this->updateRecordCurl();
        return true;
    }

    private function updateMXRecords(DnsTemplate $dns): bool
    {
        $this->value = $this->getPost("mx_value");
        $this->type = 'MX';
        $this->opt = $this->getPost("mx_opt");

        if($this->getPost("mx_value") != null) {
            $dns->value = $this->getPost("mx_value");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateMXRecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            return false;
        }
        if($this->getPost("mx_opt") != null) {
            $dns->opt = $this->getPost("mx_opt");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateMXRecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            return false;
        }
        $data = array(
            'host' => '[domain]',
            'type' => $this->getPost("type"),
            'value' => $this->getPost("mx_value"),
            'opt' => $this->getPost("mx_opt"),
            'old_host' => $this->getPost("old_host"),
            'old_type' => $this->getPost("old_type"),
            'old_value' => $this->getPost("old_value")
        );
        $this->convertForSystem($data);
        $this->updateRecordCurl();
        return true;
    }

    private function updatePTRRecords(DnsTemplate $dns): bool
    {
        $this->old_value = $dns->value;
        $this->value = $this->getPost("ptr_value");

        $this->old_host = $dns->host;
        $this->host = $this->getPost("ptr_host");

        $this->old_type = $dns->type;
        $this->type = 'PTR';

        $this->opt = $this->getPost("ptr_opt");

        $dns->value = $this->getPost("ptr_value");
        $dns->host = $this->getPost("ptr_host");
        $dns->opt = $this->getPost("ptr_opt");

        if (!$dns->update()) {
            foreach ($dns->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "NamedServer->updateTXTRecords",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }


        $data = array(
            'host' => $this->host,
            'type' => "PTR",
            'value' => $this->value,
            'opt' => $this->getPost("ptr_opt"),
            'old_host' => $this->old_host,
            'old_type' => $this->old_type,
            'old_value' => $this->old_value,
        );
        $this->convertForSystem($data);
        $this->updateRecordCurl();
        return true;
    }

    private function updateTXTRecords(DnsTemplate $dns): bool
    {
        $this->old_value = $dns->value;
        $this->value = $this->getPost("txt_value");

        $this->old_host = $dns->host;
        $this->host = $dns->host;

        $this->old_type = 'TXT';
        $this->type = 'TXT';


        if($this->getPost("txt_value") != null) {
            $dns->value = $this->getPost("txt_value");
            $dns->host = $this->getPost("txt_host");
            if (!$dns->update()) {
                foreach ($dns->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "NamedServer->updateTXTRecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $data = array(
                    'host' => $this->getPost("txt_host"),
                    'type' => $this->type,
                    'value' => $this->value,
                    'opt' => $this->getPost("txt_opt"),
                    'old_host' => $this->old_host,
                    'old_type' => $this->old_type,
                    'old_value' => $this->old_value,
                );
                $this->convertForSystem($data);
                $this->updateRecordCurl();
                return true;
            }
        }
        return true;
    }

    public function deleteDNSRecords(): bool
    {
        $id = $this->getPost("id");
        $dns = DnsTemplate::findFirstById($id);
        $type = $dns->type;
        $host = $dns->host;
        $value = $dns->value;

        $data = array(
            'host' => $host,
            'type' => $type,
            'value' => $value
        );
        $this->convertForSystem($data);

        $this->removeRecordCurl();
        if($dns) {
            if(!$dns->delete()) {
                foreach($dns->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "NamedServer->deleteDNSRecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
        return true;
    }

    public function restoreDNSRecords($server_id): bool
    {
        $enviorement = new Environments();
        $default_records = $enviorement->defaultDNSRecordsServer($server_id);

        $dns_records = array();
        $system_dns = DnsTemplate::find("server_id = '$server_id'");
        if(!empty($system_dns)) {
            if(!$system_dns->delete()) {
                foreach($system_dns->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "NamedServer->restoreDNSRecords",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
        foreach ($default_records as $value) {
            if($value['opt'] == null) {
                $opt = "null";
            }
            else {
                $opt = $value['opt'];
            }
            $dns_records[] = array(
                'type'  => $value['type'],
                'host'  =>  $value['host'],
                'value'  =>  $value['value'],
                'opt'  => $opt,
                'time_stamp'  => "0",
                'order'  => $value['order'],
                'server_id' => $value['server_id']
            );
        }

        foreach ($dns_records as $dns_record) {
            $system_dns = new DnsTemplate();
            $system_dns->assign([
                'type'  => $dns_record["type"],
                'host'  =>  $dns_record["host"],
                'value'  =>  $dns_record["value"],
                'opt'  => $dns_record["opt"],
                'order'  => $dns_record["order"],
                'server_id' => $dns_record["server_id"],
            ]);
            if(!$system_dns->create()) {
                foreach($system_dns->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "NamedServer->restoreDNSRecords create DnsTemplate",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }

        $this->restoreCurl($server_id);
        return true;
    }

    public function dnsList(string $server_id, string $system_type): array
    {
        $datatable = new Datatable();

        if($system_type == CLOUD) {
            $delete_button = $datatable->buttons("dnsserver/delete/$1/server/$server_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("dnsserver/edit/$1/server/$server_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('dnsserver/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('dnsserver/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable ->select(array('dns_template.id as dns_id','dns_template.host','dns_template.type','dns_template.value','dns_template.id','dns_template.opt'))
            ->from("dns_template")
            ->orderDesc('dns_template.order')
            ->where("`dns_template`.server_id = '$server_id'")
            ->editColumn('dns_id', '<input type="checkbox" class="checkbox-blue" value="$1" name="dns_chk[]">', 'dns_id');

        if($system_type == CLOUD) {
            $datatable->editColumn('dns_template.host', '<a href="'.'dnsserver/edit/$1/server/'. $server_id .'">$2</a>', 'dns_template.id,dns_template.host');
        }
        else {
            $datatable->editColumn('dns_template.host', '<a href="'.'dnsserver/edit/$1">$2</a>', 'dns_template.id,dns_template.host');
        }

        $data = $datatable->addColumn('actions', $edit_button.$delete_button,'dns_template.id')
            ->unsetColumns('dns_template.id')
            ->unsetColumns('dns_template.opt')
            ->searchColumn(array('dns_template.host','dns_template.value'))
            ->generate()
            ->execute()
            ->result();


        foreach($data['aaData'] as $key => $value) {
            $data['aaData'][$key][4] = $value[6];
            unset($data['aaData'][$key][5]);
            unset($data['aaData'][$key][6]);
        }

        return $data;
    }

    public function createRecordCurl()
    {
        $bind = new Bind();
        $params = array(
            'host' => "'".$this->host."'",
            'type' => "'".$this->type."'",
            'value' => "'".$this->value."'",
            'opt' => "'".$this->opt."'"
        );

        $bind->createTemplateRecord($params);
    }

    public function updateRecordCurl()
    {
        $new_host = $this->host;
        $new_value = $this->value;

        $this->host = $this->old_host;
        $this->value = $this->old_value;

        $this->removeRecordCurl();

        $this->host = $new_host;
        $this->value = $new_value;

        $this->createRecordCurl();

    }

    public function removeRecordCurl()
    {
        $bind = new Bind();
        $params = array(
            'type' => $this->type,
            'value' => $this->value,
            'host' => $this->host
        );

        $bind->removeTemplateRecord($params);
    }

    public function restoreCurl($server_id)
    {
        $bind = new Bind();
        $bind->setServerId($server_id);
        $bind->restoreServer();
    }

    private function convertForSystem($data)
    {
        $value = preg_replace('/\[domain\]/', "DOMAINNAME", $data['value']);
        $value = preg_replace('/\[ip\]/', "IPADDR", $value);

        if(!empty($data['old_value'])) {
            $old_value = preg_replace('/\[domain\]/', "DOMAINNAME", $data['old_value']);
            $old_value = preg_replace('/\[ip\]/', "IPADDR", $old_value);
        }
        else {
            $old_value = "";
        }

        $host = preg_replace('/\[domain\]/', "DOMAINNAME", $data['host']);
        $host = preg_replace('/\[ip\]/', "IPADDR", $host);

        if(!empty($data['old_host'])) {
            $old_host = preg_replace('/\[domain\]/', "DOMAINNAME", $data['old_host']);
            $old_host = preg_replace('/\[ip\]/', "IPADDR", $old_host);
        }
        else {
            $old_host = "";
        }

        $this->type = $data['type'];
        if(!empty($data['opt']))
            $this->opt = $data['opt'];
        else
            $this->opt = "";

        if($data['type'] == "NS") {
            $this->host = $host.'.';
            $this->value = $value.'.';
            $this->old_host = $old_host.'.';
            $this->old_value = $old_value.'.';        }
        if($data['type'] == "A") {
            $this->host = $host.'.';
            $this->value = $value;
            $this->old_host = $old_host.'.';
            $this->old_value = $old_value;        }
        if($data['type'] == "CNAME") {
            $this->host = $host.'.';
            $this->value = $value.'.';
            $this->old_host = $old_host.'.';
            $this->old_value = $old_value.'.';        }
        if($data['type'] == "MX") {
            $this->host = $host.'.';
            $this->value = $value.'.';
            $this->old_host = $old_host.'.';
            $this->old_value = $old_value.'.';
        }
        if($data['type'] == "PTR") {
            $this->host = $host;
            $this->value = $value.'.';
            $this->old_host = $old_host;
            $this->old_value = $old_value.'.';
        }
        if($data['type'] == "TXT") {
            $this->host = $host.'.';
            $this->value = $value;
            $this->old_host = $old_host.'.';
            $this->old_value = $old_value;
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
        $this->validation->InclusionIn($this->getPost("type"),'record_type',array('NS','A','MX','PTR','CNAME','TXT'),__('Please select a type.'));

        switch($this->getPost("type")) {
            case "NS":
                $required[] = array(
                    'field' => 'ns_record',
                    'item' => $this->getPost("ns_value"),
                    'message' => __('NS Record is required')
                );
                $this->validation->Required($required);

                $this->validation->CheckRecordTemplate("NS", $this->getPost("ns_value"),'ns_record',__('Please enter valid ns record.'));
                $this->validation->CheckDnsUniqueServer(array(
                    'field' => 'ns_record',
                    'type' => 'NS',
                    'host' => $this->getPost("ns_host"),
                    'value' => $this->getPost("ns_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("server_id"), $this->getPost("update"));
                break;
            case "A":
                $required[] = array(
                    'field' => 'a_record',
                    'item' => $this->getPost("a_host"),
                    'message' => __('A Record is required')
                );
                $required[] = array(
                    'field' => 'a_record_ip_address',
                    'item' => $this->getPost("a_value"),
                    'message' => __('Please enter A Record Value')
                );
                $this->validation->Required($required);
                $this->validation->IpTemplate($this->getPost("a_value"),'a_record_ip_address');
                $this->validation->CheckDnsUniqueServer(array(
                    'field' => 'a_record',
                    'type' => 'A',
                    'host' => $this->getPost("a_host") . '.'. $this->getPost("domain_name"),
                    'value' => $this->getPost("a_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("server_id"), $this->getPost("update"));
                break;
            case "MX":
                $required[] = array(
                    'field' => 'mx_record',
                    'item' => $this->getPost("mx_value"),
                    'message' => __('MX Record is required')
                );
                $this->validation->Required($required);
                $this->validation->CheckRecordTemplate("NS", $this->getPost("mx_value"),'mx_record',__('Please enter valid mx record.'));
                $this->validation->InclusionIn($this->getPost("mx_opt"),'mx_record_priority',array('0','5','10','15','20','25','30','35','40','45','50'), __('Please select priority.'));
                $this->validation->CheckDnsUniqueServer(array(
                    'field' => 'mx_record',
                    'type' => 'MX',
                    'host' => $this->getPost("domain_name"),
                    'value' => $this->getPost("mx_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("server_id"), $this->getPost("update"));
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
                $this->validation->MinMax($this->getPost("ptr_opt"),'ptr_record_ip_address',"1","32", __('Please select ip block.'));
                $this->validation->IpTemplate($this->getPost("ptr_host"),'ptr_record_ip_address');
                $this->validation->CheckDnsUniqueServer(array(
                    'field' => 'ptr_record_domain_name',
                    'type' => 'PTR',
                    'host' => $this->getPost("ptr_host"),
                    'value' => $this->getPost("ptr_value").'.'.$this->getPost("domain_name"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("server_id"), $this->getPost("update"));
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
                $this->validation->CheckDnsUniqueServer(array(
                    'field' => 'cname_record_domain_name',
                    'type' => 'CNAME',
                    'host' => $this->getPost("cname_host").'.'.$this->getPost("domain_name"),
                    'value' => $this->getPost("cname_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("server_id"), $this->getPost("update"));
                break;
            case "TXT":
                $required[] = array(
                    'field' => 'txt_record',
                    'item' => $this->getPost("txt_value"),
                    'message' => __('TXT Record is required')
                );
                $this->validation->Required($required);
                $this->validation->CheckDnsUniqueServer(array(
                    'field' => 'txt_record',
                    'type' => 'TXT',
                    'host' => $this->getPost("txt_host"),
                    'value' => $this->getPost("txt_value"),
                    'old_host' => $this->getPost("old_host"),
                    'old_value' => $this->getPost("old_value"),
                ), $this->getPost("server_id"), $this->getPost("update"));
                break;
        }

        if($this->validation->Validate() == false) {

            $this->error_messages =  $this->validation->getMessage();
            return false;
        }
        return true;
    }
}