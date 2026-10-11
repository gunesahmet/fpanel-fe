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
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;

/**
 * Class AccountForm
 * @package FPanel\Forms
 */
class AccountForm extends Form
{
    public function initialize($entity = null, array $options)
    {
        $this->formElements($options);
    }

    private function formElements(array $options)
    {
        $username = new Text('username', array(
            'id' => 'username',
            'name' => 'username',
            'value' => $options['username'],
            'disabled' => 'disabled'
        ));

        $fullname = new Text('fullname', array(
            'id' => 'fullname',
            'name' => 'fullname',
            'value' => $options['fullname'],
        ));

        $old_password = new Password('old_password', array(
            'id' => 'old_password',
            'name' => 'old_password',
        ));

        $password = new Password('password', array(
            'id' => 'password',
            'name' => 'password',
        ));

        $telephone = new Text('telephone', array(
            'id' => 'telephone',
            'name' => 'telephone',
            'value' => $options['telephone'],
        ));

        $fax = new Text('fax', array(
            'id' => 'fax',
            'name' => 'fax',
            'value' => $options['fax'],
        ));

        $email = new Text('email', array(
            'id' => 'email',
            'name' => 'email',
            'value' => $options['email'],
        ));

        $company = new Text('company', array(
            'id' => 'company',
            'name' => 'company',
            'value' => $options['company'],
        ));

        $address = new TextArea('address', array(
            'id' => 'address',
            'name' => 'address',
            'value' => $options['address'],
        ));

        $city = new Text('city', array(
            'id' => 'city',
            'name' => 'city',
            'value' => $options['city'],
        ));

        $region = new Text('region', array(
            'id' => 'region',
            'name' => 'region',
            'value' => $options['region'],
        ));

        $country = new Text('country', array(
            'id' => 'country',
            'name' => 'country',
            'value' => $options['country'],
        ));

        $this->add($username);
        $this->add($password);
        $this->add($old_password);
        $this->add($fullname);
        $this->add($telephone);
        $this->add($fax);
        $this->add($email);
        $this->add($company);
        $this->add($address);
        $this->add($city);
        $this->add($region);
        $this->add($country);
    }
}