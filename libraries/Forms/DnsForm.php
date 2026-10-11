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
 */namespace FPanel\Forms;

use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class DnsForm
 * @package FPanel\Forms
 */
class DnsForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options = null)
	{
		$this->requiredDNSFormElements($options);
	}

    /**
     * @param $options
     */
    private function requiredDNSFormElements($options = null)
	{

        $record_type = new Select("record_type", array(
                'A' => 'A',
                'NS' => 'NS',
                'CNAME' => 'CNAME',
                'MX'    => 'MX',
                'PTR'   => 'PTR',
                'TXT'   => 'TXT',
                'SRV'   => 'SRV',
            ), array(
                'id' => 'record_type',
            ));
        $ns_record = new Text('ns_record', array(
            'id' => 'ns_record',
            'name' => 'ns_record',
        ));
        $a_record = new Text('a_record', array(
            'id' => 'a_record',
            'name' => 'a_record',
        ));
        $a_record_ip_address = new Text('a_record_ip_address', array(
            'id' => 'a_record_ip_address',
            'name' => 'a_record_ip_address',
        ));
        $cname_record_domain_name = new Text('cname_record_domain_name', array(
            'id' => 'cname_record_domain_name',
            'name' => 'cname_record_domain_name',
        ));
        $cname_record = new Text('cname_record', array(
            'id' => 'cname_record',
            'name' => 'cname_record',
        ));
        $mx_record = new Text('mx_record', array(
            'id' => 'mx_record',
            'name' => 'mx_record',
        ));
        $mx_domain = new Text('mx_domain', array(
            'id' => 'mx_domain',
            'name' => 'mx_domain',
        ));
        $mx_record_priority = new Select("mx_record_priority", array(
                '0' => __('Highest (0)'),
                '5' => '5',
                '10' => '10',
                '15'    => '15',
                '20'   => '20',
                '25'   => '25',
                '30'   => '30',
                '35'   => '35',
                '40'   => '40',
                '45'   => '45',
                '50' => __('Lowest (50)')
            ), array(
                'id' => 'mx_record_priority',
            ));
        $ptr_record_ip_address = new Text('ptr_record_ip_address', array(
            'id' => 'ptr_record_ip_address',
            'name' => 'ptr_record_ip_address',
        ));
        $ptr_record_ip_block = new Text("ptr_record_ip_block", array(
            'id' => 'ptr_record_ip_block',
            'name' => 'ptr_record_ip_block',
        ));

        $ptr_record = new Text('ptr_record', array(
            'id' => 'ptr_record',
            'name' => 'ptr_record',
        ));
        $txt_record_prefix = new Text('txt_record_prefix',array(
            'id' => 'txt_record_prefix',
            'name' => 'txt_record_prefix',
        ));
        $txt_record = new Text('txt_record', array(
            'id' => 'txt_record',
            'name' => 'txt_record',
        ));
        // SRV Kaydı için alanlar
        $srv_service_name = new Text('srv_service_name',array(
            'id' => 'srv_service_name',
            'name' => 'srv_service_name',
        ));
        $srv_protocol = new Text('srv_protocol',array(
            'id' => 'srv_protocol',
            'name' => 'srv_protocol',
        ));
        $srv_domain_name = new Text('srv_domain_name',array(
            'id' => 'srv_domain_name',
            'name' => 'srv_domain_name',
        ));
        $srv_priority = new Select('srv_priority',array(
            '0' => __('Highest (0)'),
            '5' => '5',
            '10' => '10',
            '15'    => '15',
            '20'   => '20',
            '25'   => '25',
            '30'   => '30',
            '35'   => '35',
            '40'   => '40',
            '45'   => '45',
            '50' => __('Lowest (50)')
        ),array(
            'id' => 'srv_priority',
            'name' => 'srv_priority',
        ));
        $srv_priority_weight = new Select('srv_priority_weight',array(
            '0' => __('Lowest (0)'),
            '5' => '5',
            '10' => '10',
            '15'    => '15',
            '20'   => '20',
            '25'   => '25',
            '30'   => '30',
            '35'   => '35',
            '40'   => '40',
            '45'   => '45',
            '50' => __('Highest (50)')
        ),array(
            'id' => 'srv_priority_weight',
            'name' => 'srv_priority_weight',
        ));
        $srv_target_host = new Text('srv_target_host',array(
            'id' => 'srv_target_host',
            'name' => 'srv_target_host',
        ));
        $srv_target_port = new Text('srv_target_port',array(
            'id' => 'srv_target_port',
            'name' => 'srv_target_port',
        ));

        if (!empty($options)) {
            if($options['action'] == "edit") {
                $dns_records = $options['dns_records'];
                $record_type->setAttribute('disabled','disabled');
                $record_type->setDefault($dns_records['type']);
                switch($dns_records['type']) {
                    case "NS":
                        $ns_record->setAttribute('value', $dns_records['value']);
                        break;
                    case "A":
                        $a_record->setAttribute('value',str_replace('.'.$options['domain_name'],'', $dns_records['host']));
                        $a_record_ip_address->setAttribute('value', $dns_records['value']);
                        break;
                    case "CNAME":
                        $cname_record_domain_name->setAttribute('value',str_replace('.'.$options['domain_name'],'', $dns_records['host']));
                        $cname_record->setAttribute('value', $dns_records['value']);
                        break;
                    case "MX":
                        $mx_domain->setAttribute('value',rtrim(str_replace($options['domain_name'],'', $dns_records['host']),'.'));
                        $mx_record->setAttribute('value', $dns_records['value']);
                        $mx_record_priority->setDefault($dns_records['opt']);
                        break;
                    case "PTR":

                        $ptr_record_ip_address->setAttribute('value', $dns_records['host']);
                        $ptr_record_ip_block->setDefault($dns_records['opt']);
                        // PTR Record Domain Name
                        if($dns_records['value'] == $options['domain_name'])
                            $ptr_rec_value = "";
                        else {
                            $ptr_rec_value = str_replace('.'.$options['domain_name'],'', $dns_records['value']);
                        }
                        $ptr_record->setAttribute('value', $ptr_rec_value);
                        break;
                    case "TXT":
                        $txt_prefix = "";
                        if($dns_records['host'] != $options['domain_name'])
                            $txt_prefix = str_replace('.'.$options['domain_name'],'', $dns_records['host']);

                        $txt_record_prefix->setAttribute('value', $txt_prefix);
                        $txt_record->setAttribute('value', $dns_records['value']);
                        break;
                    case "SRV":
                        $host = explode('.', $dns_records['host']);
                        $domain = str_replace($host['0'].'.'.$host['1'].'.','', $dns_records['host']);
                        if($domain == $options['domain_name'])
                            $domain = "";
                        else
                            $domain = str_replace('.'.$options['domain_name'],'', $domain);
                        $srv_service_name->setAttribute('value',str_replace('_','', $host['0']));
                        $srv_protocol->setAttribute('value',str_replace('_','', $host['1']));
                        $srv_domain_name->setAttribute('value', $domain);

                        $srv_priority->setDefault($dns_records['opt']);
                        $srv_priority_weight->setDefault($dns_records['weight']);
                        $srv_target_port->setAttribute('value', $dns_records['port']);


                        $srv_target_host->setAttribute('value', $dns_records['value']);
                        break;
                }
            }
        }
        else {
            $srv_priority->setDefault('5');
            $srv_priority_weight->setDefault('25');
        }

        $this->add($record_type);
        $this->add($ns_record);
        $this->add($a_record);
        $this->add($a_record_ip_address);
        $this->add($cname_record_domain_name);
        $this->add($cname_record);
        $this->add($mx_record);
        $this->add($mx_record_priority);
        $this->add($ptr_record_ip_address);
        $this->add($ptr_record_ip_block);
        $this->add($ptr_record);
        $this->add($txt_record_prefix);
        $this->add($txt_record);
        $this->add($mx_domain);
        // SRV Kaydı için
        $this->add($srv_service_name);
        $this->add($srv_protocol);
        $this->add($srv_domain_name);
        $this->add($srv_priority);
        $this->add($srv_priority_weight);
        $this->add($srv_target_host);
        $this->add($srv_target_port);


    }


}