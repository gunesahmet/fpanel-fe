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
use FPanel\Library\Library;
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;
use FPanel\Models\DomainAlias as DomainAliasModel;
use FPanel\Models\Domains;
use FPanel\Process\Bind;

/**
 * Class DomainAlias
 * @package FPanel\DomainAlias
 */
class DomainAlias extends Library
{
    protected $alias_name;
    protected $change_alias_name = false;
    protected $new_alias_name;

    public function createAlias(): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        if(!$this->validate()) {
            return false;
        }

        $domain_alias = new DomainAliasModel();
        $domain_alias->assign(array(
            "id"                => $this->env->createUuid(),
            'domain_id'         => $this->getPost("domain_id"),
            'alias_name'        => $this->getPost("alias_name"),
            'syn_pri_dom'       => $this->getPost("syn_pri_dom") == ON ? ACTIVE : PASSIVE,
            'dns'               => $this->getPost("dns", OFF) == ON ? ACTIVE : PASSIVE,
            'mail'              => $this->getPost("mail", OFF) == ON ? ACTIVE : PASSIVE,
            'web'               => $this->getPost("web", OFF) == ON ? ACTIVE : PASSIVE,
            'status'            => $this->getPost("status"),
        ));

        if(!$domain_alias->create()) {
            foreach ($domain_alias->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DomainAlias->createAlias",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            $this->createAliasCurl();
            $this->dnsInitCurl($domain_alias->id, $this->getPost("domain_id"));
            return true;
        }
    }

    public function updateAlias($alias_id, $domain_id): bool
    {
        $this->db->begin();
        try {
            $status = $this->validate();
            if(!$status) {
                return false;
            }
            $domain_alias = DomainAliasModel::findFirst("id = '$alias_id' AND domain_id = '$domain_id'");
            if(!$domain_alias) {
                $this->error_messages["content"] = __("Domain alias not found");
                return false;
            }
            if($this->getPost("web") != null) {
                $domain_alias->web = $this->getPost("web", OFF) == ON ? ACTIVE : PASSIVE;
                if(!$domain_alias->update()) {
                    foreach ($domain_alias->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "DomainAlias->updateAlias",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }

            if($this->getPost("mail") != null) {
                $domain_alias->mail = $this->getPost("mail", OFF) == ON ? ACTIVE : PASSIVE;
                if(!$domain_alias->update()) {
                    foreach ($domain_alias->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->db->rollback();
                    return false;
                }
            }
            if($this->getPost("alias_name") != null) {
                $this->alias_name = $domain_alias->alias_name;
                if($domain_alias->alias_name != $this->getPost("alias_name")) {
                    $this->change_alias_name = true;
                    $this->new_alias_name = $this->getPost("alias_name");
                }
                $domain_alias->alias_name = $this->getPost("alias_name");
                if(!$domain_alias->update()) {
                    foreach ($domain_alias->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "DomainAlias->updateAlias",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    $this->db->rollback();
                    return false;
                }
                else {
                    $this->updateAliasCurl();
                }
            }
            $this->db->commit();
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "DomainAlias->updateAlias",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function deleteAlias($id, $domain_name, $domain_id): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        $domain_alias = DomainAliasModel::findFirst("id = '$id' AND domain_id = '$domain_id'");
        if(!$domain_alias) {
            return false;
        }
        else {
            $this->setPost("domain_name", $domain_name);
            $this->setPost("alias_name", $domain_alias->alias_name);
            if(!$domain_alias->delete()) {
                foreach($domain_alias->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainAlias->deleteAlias",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            $this->removeAliasCurl($domain_id);
            return true;
        }
    }

    public function listAlias($domain_id = null, $nested = false): array
    {
        $datatable = new Datatable();
        if($nested) {
            $delete_button = $datatable->buttons("domainalias/delete/$1/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("domainalias/edit/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('domainalias/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('domainalias/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
        }
        $datatable->select(array('domain_alias.id','domain_alias.alias_name','domain_alias.mail','domain_alias.web'))
            ->from("domain_alias")
            ->where("domain_alias.domain_id = '$domain_id'")
            ->searchColumn(array('domain_alias.alias_name'))
            ->addColumn('actions', $edit_button.$delete_button,'domain_alias.id');

        if($nested) {
            $datatable->editColumn('domain_alias.alias_name','<a href="'. "domainalias/edit/$1/domain/$domain_id\">$2</a>", 'domain_alias.id, domain_alias.alias_name');
        }
        else {
            $datatable->editColumn('domain_alias.alias_name','<a href="'. 'domainalias/edit/$1">$2</a>', 'domain_alias.id, domain_alias.alias_name');
        }
        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = '<input type="checkbox" value="'. $value[0] .'" name="aliases[]" class="checkbox-blue">';

            switch($value[2]) {
                case true:
                    $data['aaData'][$key][2] = '<span class="label label-success"> '. __('On') .' </span>';
                    break;
                case false:
                    $data['aaData'][$key][2] = '<span class="label label-danger"> '. __('Off') .' </span>';
                    break;
            }

            switch($value[3]) {
                case true:
                    $data['aaData'][$key][3] = '<span class="label label-success"> '. __('On') .' </span>';
                    break;
                case false:
                    $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Off') .' </span>';
                    break;
            }
        }
        return $data;
    }

    public function createAliasCurl()
    {
        $domalias = new \FPanel\Process\Domainalias();

        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $domalias->setServerId($domain->web_server_id);

        $ip_addr = $domain->getIpAddress()->ip_address;
        $alias_name = $this->getPost("alias_name");

        $params = array(
            "domain"=> $this->getPost("domain_name"),
            "alias" => $alias_name,
            "ipaddr" => $ip_addr,
        );

        if($this->getPost("mail") == ON) {
            $params['mail-serv'] = "on";
        }
        if($this->getPost("web") == ON) {
            $params['web-serv'] = "on";
        }
        $domalias->create($params);
    }

    public function dnsInitCurl($alias_id, $domain_id): bool
    {
        $domain_alias = DomainAliasModel::findFirstById($alias_id);
        $domains = Domains::findFirstById($domain_id);

        $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
        $dns_records = DnsRecords::find("dns_zone_id = '$dns_zone->id'");
        $time_stamp = time();

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
                            $soa_records .= "admin@$domain_alias->alias_name.";
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

        $dnsmng = new Bind();
        $domain_alias_temps = str_replace($domains->name, $domain_alias->alias_name, $dns_zone_temp);
        $dnsmng->setServerId($domains->dns_server_id);
        $params = array(
            'domain' => $domain_alias->alias_name,
            "zone-data" => $this->crypto->staticEncode($domain_alias_temps)
        );
        $dnsmng->init($params);
        return true;
    }

    public function updateAliasCurl()
    {
        try {
            $domalias = new \FPanel\Process\Domainalias();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $domalias->setServerId($domain->web_server_id);

            $params = array(
                'domain' => $this->getPost("domain_name"),
                'alias' => $this->getPost("alias_name")
            );

            if($this->getPost("syn_pri_dom") == ON) {
                $params['sync'] = "on";
            }
            if($this->getPost("mail") == ON) {
                $params['mail-serv'] = "on";
            }
            if($this->getPost("web") == ON) {
                $params['web-serv'] = "on";
            }
            $domalias->remove($params);

            if($this->change_alias_name) {
                $params['alias'] = $this->new_alias_name;
            }

            $domalias->create($params);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function removeAliasCurl($domain_id)
    {
        $domalias = new \FPanel\Process\Domainalias();

        $domain = Domains::findFirstById($domain_id);

        $domalias->setServerId($domain->web_server_id);

        $params = array(
            'domain' => $this->getPost("domain_name"),
            'alias' => $this->getPost("alias_name")
        );

        $domalias->remove($params);

        $dnsmng = new Bind();
        $dnsmng->setServerId($domain->dns_server_id);
        $params = array(
            'domain' => $this->getPost("alias_name"),
        );
        $dnsmng->remove($params);
    }

    public function validate(): bool
    {
        $required = array(array(
            'field' => 'alias_name',
            'item' => $this->getPost("alias_name"),
            'message' => __('Alias name is required.')
        ));

        $status = $this->validation->Required($required)
            ->Domain($this->getPost("alias_name"),'alias_name',__('Please enter valid alias name'))
            ->UniqueAlias($this->getPost("alias_name"),'alias_name',__('Alias name must be unique'), $this->getPost("update"), $this->getPost("old_alias"))
            ->Validate();

        if(!$status) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}