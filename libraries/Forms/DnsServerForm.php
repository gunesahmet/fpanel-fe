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
 * Class DnsServerForm
 * @package FPanel\Forms
 */
class DnsServerForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options = null)
    {
        $this->formElements($options);
    }

    /**
     * @param $options
     */
    private function formElements($options = null)
    {

        if (!empty($options)) {
            if($options['action'] == "edit") {
                $dns_records = $options['dns_records'];
                // Record Type
                $this->add(new Select(
                    "record_type",
                    array(
                        $dns_records['type']    => $dns_records['type'],
                    ),
                    array(
                        'id' => 'record_type',
                        'disabled' => 'disabled',
                    )));

                $a_host = str_replace('[domain]','', $dns_records['host']);
                $a_host = rtrim($a_host, '.');
                $cname_host = str_replace('[domain]','', $dns_records['host']);
                $cname_host = rtrim($cname_host, '.');



                switch($dns_records['type']) {
                    case "NS":
                        // NS Record
                        $this->add(new Text('ns_record', array(
                            'id' => 'ns_record',
                            'name' => 'ns_record',
                            'value' => $dns_records['value'],
                        )));
                        break;
                    case "A":
                        // A Record Domain Name
                        $this->add(new Text('a_record', array(
                            'id' => 'a_record',
                            'name' => 'a_record',
                            'value' => $a_host,
                        )));

                        // A Record IP Address
                        $this->add(new Text('a_record_ip_address', array(
                            'id' => 'a_record_ip_address',
                            'name' => 'a_record_ip_address',
                            'value' => $dns_records['value'],
                        )));
                        break;
                    case "CNAME":
                        // CNAME Record Domain Name
                        $this->add(new Text('cname_record_domain_name', array(
                            'id' => 'cname_record_domain_name',
                            'name' => 'cname_record_domain_name',
                            'value' => $cname_host,
                        )));

                        // CNAME Record
                        $this->add(new Text('cname_record', array(
                            'id' => 'cname_record',
                            'name' => 'cname_record',
                            'value' => $dns_records['value'],
                        )));
                        break;
                    case "MX":
                        // MX Record
                        $this->add(new Text('mx_record', array(
                            'id' => 'mx_record',
                            'name' => 'mx_record',
                            'value' => $dns_records['value'],
                        )));

                        // MX Record Priority
                        $this->add(new Select(
                            "mx_record_priority",
                            array(
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
                            ),
                            array(
                                'id' => 'mx_record_priority',
                            )));
                        break;
                    case "PTR":
                        $ptr_value = str_replace('[domain]','', $dns_records['value']);
                        $ptr_value = rtrim($ptr_value, '.');
                        // PTR Record IP Address
                        $this->add(new Text('ptr_record_ip_address', array(
                            'id' => 'ptr_record_ip_address',
                            'name' => 'ptr_record_ip_address',
                            'value' => $dns_records['host'],
                        )));

                        // PTR Record IP Block
                        $this->add(new Text("ptr_record_ip_block", array(
                                'id' => 'ptr_record_ip_block',
                                'name' => 'ptr_record_ip_block',
                                'value' => $dns_records['opt'],
                            )));

                        // PTR Record Domain Name
                        $this->add(new Text('ptr_record', array(
                            'id' => 'ptr_record',
                            'name' => 'ptr_record',
                            'value' => $ptr_value,
                        )));
                        break;
                    case "TXT":
                        // TXT Record
                        $txt_prefix = "";
                        if($dns_records['host'] != "[domain]")
                            $txt_prefix = str_replace('.[domain]','', $dns_records['host']);

                        $this->add(new Text('txt_record', array(
                            'id' => 'txt_record',
                            'name' => 'txt_record',
                            'value' => $dns_records['value'],
                        )));

                        // TXT Record Prefix
                        $this->add(new Text('txt_record_prefix', array(
                            'id' => 'txt_record_prefix',
                            'name' => 'txt_record_prefix',
                            'value' => $txt_prefix,
                        )));
                        break;
                }
            }
        }
        else {
            // Record Type
            $this->add(new Select(
                "record_type",
                array(
                    'NS' => 'NS',
                    'A' => 'A',
                    'CNAME' => 'CNAME',
                    'MX'    => 'MX',
                    'PTR'   => 'PTR',
                    'TXT'   => 'TXT'
                ),
                array(
                    'id' => 'record_type',
                )));

            // NS Record
            $this->add(new Text('ns_record', array(
                'id' => 'ns_record',
                'name' => 'ns_record',
            )));

            // A Record Domain Name
            $this->add(new Text('a_record', array(
                'id' => 'a_record',
                'name' => 'a_record',
            )));

            // A Record IP Address
            $this->add(new Text('a_record_ip_address', array(
                'id' => 'a_record_ip_address',
                'name' => 'a_record_ip_address',
            )));

            // CNAME Record Domain Name
            $this->add(new Text('cname_record_domain_name', array(
                'id' => 'cname_record_domain_name',
                'name' => 'cname_record_domain_name',
            )));

            // CNAME Record
            $this->add(new Text('cname_record', array(
                'id' => 'cname_record',
                'name' => 'cname_record',
            )));

            // MX Record
            $this->add(new Text('mx_record', array(
                'id' => 'mx_record',
                'name' => 'mx_record',
            )));

            // MX Record Priority
            $this->add(new Select(
                "mx_record_priority",
                array(
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
                ),
                array(
                    'id' => 'mx_record_priority',
                )));

            // PTR Record IP Address
            $this->add(new Text('ptr_record_ip_address', array(
                'id' => 'ptr_record_ip_address',
                'name' => 'ptr_record_ip_address',
            )));

            // PTR Record IP Block
            $this->add(new Text(
                "ptr_record_ip_block",array(
                    'id' => 'ptr_record_ip_block',
                    'name' => 'ptr_record_ip_block',
                )));

            // PTR Record
            $this->add(new Text('ptr_record', array(
                'id' => 'ptr_record',
                'name' => 'ptr_record',
            )));

            // TXT Record
            $this->add(new Text('txt_record', array(
                'id' => 'txt_record',
                'name' => 'txt_record',
            )));

            // TXT Record Prefix
            $this->add(new Text('txt_record_prefix', array(
                'id' => 'txt_record_prefix',
                'name' => 'txt_record_prefix',
            )));
        }

    }


}