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
use FPanel\Models\DnsTemplate;
use FPanel\Server\NamedServer;
use Phalcon\Mvc\User\Component;

class DnsServer extends Component
{
    public function init($process, $params)
    {

        switch($process[2]) {
            case "list":
                return $this->dnslist();
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            case "restore":
                return $this->restore();
            default:
                return array("process" => 'Processs is required');
        }
    }

    private function dnslist(): array
    {
        $qbuilder = new QueryBuilder();
        $data = $qbuilder->select([
            "dns_template.host",
            "dns_template.type",
            "dns_template.value",
            "dns_template.opt",
        ])
            ->from("dns_template")
            ->find();
        $data['status'] = ACTIVE;
        return $data;
    }

    private function create($params)
    {
        $error_messages = array();

        $post_data = array(
            // Değişkenler
            'server_id' => "1",
            'domain_name'   => '[domain]',
            'time_stamp'   => '0',
            // Post Dataları
            'type'   => $params['record_type'],

            // NS
            'ns_host'  => '[domain]',
            'ns_value' => $params['ns_value'].'.'.'[domain]',
            'ns_opt' => '',

            // A
            'a_host' => $params['a_host'].'.'.'[domain]',
            'a_value' => $params['a_value'],
            'a_opt' => '',

            // CNAME
            'cname_host' => $params['cname_host'].'.'.'[domain]',
            'cname_value' => $params['cname_value'],
            'cname_opt' => '',

            // MX
            'mx_host'  => '[domain]',
            'mx_value' => $params['mx_value'],
            'mx_opt' => $params['mx_opt'],

            // PTR
            'ptr_host' => $params['ptr_host'],
            'ptr_value' => $params['ptr_value'].'.'.'[domain]',
            'ptr_opt' => $params['ptr_opt'],

            // TXT
            'txt_host' => '[domain]',
            'txt_value' => $params['txt_value'],
            'txt_opt' => '',

            'old_type' => "",
            'old_host' => "",
            'old_value' => "",
            'update' => false,
        );
        $dns = new NamedServer();
        $dns->setPosts($post_data);
        $dns_record_status = $dns->createDNSRecords();
        if ($dns_record_status == true) {
            return "Success";
        }
        else {
            foreach ($dns->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    private function update($params)
    {
        $error_messages = array();

        $dns = DnsTemplate::findFirst("type = '$params[record_type]' AND host ='$params[old_host]' AND value = '$params[old_value]'");
        if(!$dns) {
            return $error_messages['record_type'] = "Dns record not found";
        }

        $post_data = array(
            // Değişkenler
            'server_id' => "1",
            'id'        => $dns->id,
            'domain_name'   => $params['domain_name'],
            'time_stamp'   => '0',

            // Post Dataları
            'type'   => $params['record_type'],

            // NS
            'ns_host'  => $params['ns_host'],
            'ns_value' => $params['ns_value'],
            'ns_opt' => '',

            // A
            'a_host' => $params['a_host'],
            'a_value' => $params['a_value'],
            'a_opt' => '',

            // CNAME
            'cname_host' => $params['cname_host'],
            'cname_value' => $params['cname_value'],
            'cname_opt' => '',

            // MX
            'mx_host'  => $params['mx_host'],
            'mx_value' => $params['mx_value'],
            'mx_opt' => $params['mx_opt'],

            // PTR
            'ptr_host' => $params['ptr_host'],
            'ptr_value' => $params['ptr_value'],
            'ptr_opt' => $params['ptr_opt'],

            // TXT
            'txt_host' => $params['txt_host'],
            'txt_value' => $params['txt_value'],
            'txt_opt' => '',

            'old_type' => $params['record_type'],
            'old_host' => $params['old_host'],
            'old_value' => $params['old_value'],
            'update' => true,
        );
        $dns = new NamedServer();
        $dns->setPosts($post_data);
        $status = $dns->updateDNSRecords();
        if($status == true) {
            return "Success";
        }
        else {
            foreach ($dns->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }

    }

    private function delete($params): string
    {
        $error_messages = array();

        $dns = DnsTemplate::findFirst("type = '$params[record_type]' AND host ='$params[host]' AND value = '$params[value]'");
        if(!$dns) {
            return $error_messages['record_type'] = "Dns record not found";
        }

        $dns_server = new NamedServer();
        $post_data = array(
            'id'    => $dns->id
        );

        $dns_server->setPosts($post_data);
        $status = $dns_server->deleteDNSRecords();
        if ( $status == true) {
            return "Success";
        }
        return $error_messages['record_type'] = "Dns record not found";
    }

    private function restore(): string
    {
        $restore_dns = new NamedServer();
        $restore_dns->restoreDNSRecords("1");
        return "Success";
    }
}