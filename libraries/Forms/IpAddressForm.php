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

use FPanel\Models\IpAddress;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class IpAddressForm
 * @package FPanel\Forms
 */
class IpAddressForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {
        if($options['page'] == "hostname") {
            $this->hostFormElements($options);
        }
        else {
            $this->formElements($options);
        }
    }

    /**
     * @param $options
     */
    private function formElements($options)
    {
        $sys_ipaddrss = IpAddress::findFirst("gateway NOT LIKE ''");
        if($sys_ipaddrss)
            $g_status = true;
        else
            $g_status = false;

        if($options['action'] == "edit") {
            $interfaces = array(
                '' => $options['network_data']['interface']
            );
        }
        else {
            $interfaces = $options['interfaces'];
        }

        // Interface
        $interface = new Select('interface',
            $interfaces,
            array(
                'id' => 'interface',
                'name' => 'interface',
            ));

        $format = new Check('format',
            array(
                'id' => 'format',
                'name' => 'format',
            ));

        // Ip Address
        $ip_address = new Text('ip_address', array(
            'id' => 'ip_address',
            'name' => 'ip_address',
        ));

        // Gateway
        $gateway = new Text('gateway', array(
            'id' => 'gateway',
            'name' => 'gateway',
        ));

        // Netmask
        $netmask_cidr = new Select('netmask_cidr',array(
            '16' => '16',
            '17' => '17',
            '18' => '18',
            '19' => '19',
            '20' => '20',
            '21' => '21',
            '22' => '22',
            '23' => '23',
            '24' => '24',
            '25' => '25',
            '26' => '26',
            '27' => '27',
            '28' => '28',
            '29' => '29',
            '30' => '30',
        ), array(
            'id' => 'netmask_cidr',
            'name' => 'netmask',
        ));

        // Netmask
        $netmask = new Select('netmask',array(
            '16' => '255.255.0.0',
            '17' => '255.255.128.0',
            '18' => '255.255.192.0',
            '19' => '255.255.224.0',
            '20' => '255.255.240.0',
            '21' => '255.255.248.0',
            '22' => '255.255.252.0',
            '23' => '255.255.254.0',
            '24' => '255.255.255.0',
            '25' => '255.255.255.128',
            '26' => '255.255.255.192',
            '27' => '255.255.255.224',
            '28' => '255.255.255.240',
            '29' => '255.255.255.248',
            '30' => '255.255.255.252',
        ), array(
            'id' => 'netmask',
            'name' => 'netmask',
        ));

        // Used Type
        $used_type = new Select('used_type',
            array(
                SHARED    => __('Shared'),
                EXCLUSIVE => __('Exclusive'),
            ),
            array(
                'id' => 'used_type',
                'name' => 'used_type',
            ));

        if($options['action'] == "edit") {
            $ip_address->setAttribute('value', $options['network_data']['ip_address']);

            $used_type->setDefault($options['network_data']['type']);
            $interface->setAttribute('disabled','disabled');
            $ip_address->setAttribute('disabled','disabled');

            if($options['network_data']['format'] == CIDR) {
                $format->setAttribute('checked','checked');

                $netmask->setAttribute('disabled','disabled');
                $netmask->setAttribute('style','display:none');
            }
            else {
                $netmask_cidr->setAttribute('disabled','disabled');
                $netmask_cidr->setAttribute('style','display:none');
            }
            $netmask->setDefault($options['network_data']['netmask']);
            $netmask_cidr->setDefault($options['network_data']['netmask']);

            if($options['network_data']['gateway'] != null) {
                $gateway->setAttribute('value', $options['network_data']['gateway']);
            }
            else {
                if($g_status == true) {
                    $gateway->setAttribute('disabled','disabled');
                }
            }

        }
        else {
            $netmask_cidr->setAttribute('disabled','disabled');
            $netmask_cidr->setAttribute('style','display:none');

            if($g_status == true)
                $gateway->setAttribute('disabled','disabled');

        }


        $this->add($format);
        $this->add($gateway);
        $this->add($netmask);
        $this->add($netmask_cidr);
        $this->add($ip_address);
        $this->add($interface);
        $this->add($used_type);
    }

    private function hostFormElements($options)
    {
        // Hostname
        $this->add(new Text('hostname', array(
            'id' => 'hostname',
            'name' => 'hostname',
            'value' => $options['hostname']
        )));
        // Domain
        $this->add(new Text('host_domain', array(
            'id' => 'host_domain',
            'name' => 'host_domain',
            'value' => $options['host_domain']
        )));
    }
}