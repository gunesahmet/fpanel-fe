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
use Phalcon\Forms\Form;

/**
 * Class ProtectdirForm
 * @package FPanel\Forms
 */
class NotyfyForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
    }


    /**
     * @param $options
     */
    private function formElements($options)
    {

        $host = new Text('host', array(
            'id' => 'host',
            'name' => 'host',
        ));

        $port = new Text('port', array(
            'id' => 'port',
            'name' => 'port',
        ));


        $secure = new Select('secure', [
            "none" => __("None"),
            "tls" => __("TLS"),
            "ssl" => __("SSL"),
        ], array(
            'id' => 'secure',
            'name' => 'secure',
        ));

        $email = new Text('email', array(
            'id' => 'email',
            'name' => 'email',
        ));

        $from = new Text('from', array(
            'id' => 'from',
            'name' => 'from',
        ));

        $from_fullname = new Text('from_fullname', array(
            'id' => 'from_fullname',
            'name' => 'from_fullname',
        ));

        $password = new Password('password', array(
            'id' => 'password',
            'name' => 'password',
        ));

        if(!empty($options['data'])) {
            $host->setAttribute('value', $options['data']['host']);
            $email->setAttribute('value', $options['data']['email']);
            $port->setAttribute('value', $options['data']['port']);
            if(isset($options['data']['secure'])) {
                $secure->setDefault($options['data']['secure']);
            }
            else {
                $secure->setDefault("none");
            }
            $from_fullname->setAttribute('value', $options['data']['from_fullname']);
        }


        $this->add($from_fullname);
        $this->add($from);
        $this->add($host);
        $this->add($port);
        $this->add($secure);
        $this->add($email);
        $this->add($password);
    }

}