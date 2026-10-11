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

use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;


/**
 * Class InstallForm
 * @package FPanel\Forms
 */
class InstallForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {

        switch($options['page']) {
            case "serverinfo":
                $this->step1($options);
                break;
            case "generalinfo":
                $this->step2($options);
                break;
            case "finish":
                break;
        }


    }

    /**
     * @param $options
     */
    private function step1($options)
    {

        $ip_list = array();
        foreach ($options['ip_list'] as $ip_address) {
            $ip_list[$ip_address['2']] = $ip_address['2'];
        }
        $server_hostname = new Text('hostname', array(
            'id' => 'hostname',
            'name' => 'hostname',
            'value' => $options['server_hostname'],
        ));

        $admin_email = new Text('admin_email', array(
            'id' => 'admin_email',
            'name' => 'admin_email',
            'value' => 'admin@'.$options['server_hostname'],
        ));

        $default_ip_address = new Text('default_ip_address', array(
            'id' => 'default_ip_address',
            'name' => 'default_ip_address',
            'value' => $options['default_ip_address'],
        ));


        $ipaddress_list = new Select(
            "ipaddress_list",
            $ip_list,
            array(
                'multiple' => 'multiple',
                'id' => 'ipaddress_lists',
                'class' => 'multiselect',
                'name' => 'ipaddress_list[]',
                'data-selectable' => __('Available IP Addresses'),
                'data-selection' => __('Selected IP Addresses'),
            )
        );

        $admin_username = new Text('username', array(
            'id' => 'username',
            'name' => 'username',
            'value' => 'admin',
        ));

        $admin_password = new Password('password', array(
            'id' => 'password',
            'name' => 'password',
        ));

        $this->add($server_hostname);
        $this->add($admin_email);
        $this->add($default_ip_address);
        $this->add($ipaddress_list);
        $this->add($admin_username);
        $this->add($admin_password);
    }

    private function step2($options)
    {
        $company_name = new Text('company_name', array(
            'id' => 'company_name',
            'name' => 'company_name',
        ));

        $fullname = new Text('fullname', array(
            'id' => 'fullname',
            'name' => 'fullname',
        ));

        $telephone = new Text('telephone', array(
            'id' => 'telephone',
            'name' => 'telephone',
        ));

        $fax = new Text('fax', array(
            'id' => 'fax',
            'name' => 'fax',
        ));

        $address = new TextArea('address', array(
            'id' => 'address',
            'name' => 'address',
        ));

        $city = new Text('city', array(
            'id' => 'city',
            'name' => 'city',
        ));

        $town = new Text('town', array(
            'id' => 'town',
            'name' => 'town',
        ));

        $zip_code = new Text('zip_code', array(
            'id' => 'zip_code',
            'name' => 'zip_code',
        ));

        $country = new Select('country', $this->env->getCountryList(),array(
            'id' => 'country',
            'name' => 'country',
        ));
        $country->setDefault("United States");

        $language = new Select('language', $options['languages'], array(
            'id' => 'language',
            'name' => 'language',
        ));

        $themes = new Select('themes', $options['themes'], array(
            'id' => 'themes',
            'name' => 'themes',
        ));

        $email_address = new Text('email', array(
            'id' => 'email',
            'name' => 'email',
            'value' => $options['email'],
        ));

        $this->add($company_name);
        $this->add($fullname);
        $this->add($telephone);
        $this->add($fax);
        $this->add($address);
        $this->add($city);
        $this->add($town);
        $this->add($zip_code);
        $this->add($country);
        $this->add($language);
        $this->add($themes);
        $this->add($email_address);
    }

}