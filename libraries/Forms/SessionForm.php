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
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class SessionForm
 * @package FPanel\Forms
 */
class SessionForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {

        $this->settingFormElements($options);

    }

    /**
     * @param $options
     */
    private function settingFormElements($options)
    {
        $allowed_ip_addresses = $options["allowed_ip_addresses"];
        $blocked_ip_addresses = $options["blocked_ip_addresses"];

        $block_ip_list = array();

        if(!empty($blocked_ip_addresses)) {
            foreach($blocked_ip_addresses as $value) {
                $block_ip_list[$value] = $value;
            }
        }

        $allow_ip_list = array();
        if(!empty($allowed_ip_addresses)) {
            foreach($allowed_ip_addresses as $value) {
                $allow_ip_list[$value] = $value;
            }
        }


        $session_time = new Text("session_time", array(
            "id" => "session_time",
            "name" => "session_time",
            "value" => $options["session_time"],
        ));

        $allow_ip_address = new Check("allow_ip_address", array(
            "id" => "allow_ip_address",
            "name" => "allow_ip_address",
        ));

        $block_ip_address = new Check("block_ip_address", array(
            "id" => "block_ip_address",
            "name" => "block_ip_address",
        ));

        $add_to_allow_list = new Text("add_to_allow_list", array(
            "id" => "add_to_allow_list",
            "name" => "add_to_allow_list",
        ));

        $add_to_block_list = new Text("add_to_block_list", array(
            "id" => "add_to_block_list",
            "name" => "add_to_block_list",
        ));

        $allowed_ip_addresses = new Select(
            "allowed_ip_addresses",
            $allow_ip_list,
            array(
                "id" => "allowed_ip_addresses",
                "multiple" => "multiple",
                "name" => "allowed_ip_addresses[]"
            )
        );

        $blocked_ip_addresses = new Select(
            "blocked_ip_addresses",
            $block_ip_list,
            array(
                "id" => "blocked_ip_addresses",
                "multiple" => "multiple",
                "name" => "blocked_ip_addresses[]"
            )
        );

        if($options["allow_ip"] == true) {
            $allow_ip_address->setAttribute("checked","checked");
        }
        else {
            $allowed_ip_addresses->setAttribute("disabled","disabled");
            $add_to_allow_list->setAttribute("disabled","disabled");
        }

        if($options["block_ip"] == true) {
            $block_ip_address->setAttribute("checked","checked");
        }
        else {
            $blocked_ip_addresses->setAttribute("disabled","disabled");
            $add_to_block_list->setAttribute("disabled","disabled");
        }


        $this->add($session_time);
        $this->add($allow_ip_address);
        $this->add($block_ip_address);
        $this->add($add_to_allow_list);
        $this->add($add_to_block_list);
        $this->add($allowed_ip_addresses);
        $this->add($blocked_ip_addresses);

    }

}