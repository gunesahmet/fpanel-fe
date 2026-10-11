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

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class SystemLogForm
 * @package FPanel\Forms
 */
class SystemLogForm extends Form
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
        // Peridically Delete Logs
        $periodic_del = new Check('periodic_del', array(
            'id'    => 'periodic_del',
            'name'  => 'periodic_del',
        ));

        if($options['periodic_delete'] == true) {
            $periodic_del->setAttribute('checked','checked');
        }

        // Store Time Logs
        $store_time = new Text('store_time', array(
            'id'    => 'store_time',
            'name'  => 'store_time',
            'value' => $options['store_time'],
        ));

        // Compression
        $log_compress = new Check('log_compress', array(
            'id' => 'log_compress',
            'name' => 'log_compress',
            'checked'   => 'checked'
        ));
        if($options['log_compress'] == true) {
            $log_compress->setAttribute('checked','checked');
        }

        // Send Information
        $send_information = new Check('send_information', array(
            'id' => 'send_information',
            'name' => 'send_information',
        ));
        $email_address = new Text('email_address', array(
            'id' => 'email_address',
            'name' => 'email_address',
            'value' => $options['email_address'],
        ));

        if($options['send_information'] == true) {
            $send_information->setAttribute('cheked','checked');
        }
        else {
            $email_address->setAttribute('disabled','disabled');
        }


        $this->add($periodic_del);
        $this->add($store_time);
        $this->add($log_compress);
        $this->add($email_address);
        $this->add($send_information);

    }

}