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
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;

/**
 * Class FirewallForm
 * @package FPanel\Forms
 */
class FirewallForm extends Form
{
    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
    }

    private function formElements($options)
    {
        $firewall_status = new Check("status", array(
            "id" => "status",
            "name" => "status",
        ));

        $firewall_direction = new Select("direction", [
            0 => "INPUT",
            1 => "OUTPUT",
        ], [
            "id" => "direction",
            "name" => "direction",
        ]);

        $firewall_action = new Select("action", [
            0 => __("Accept"),
            1 => __("Drop"),
            2 => __("Queue"),
            3 => __("Return"),
        ], [
            "id" => "action",
            "name" => "action",
        ]);

        $firewall_source = new Text("source", [
            "id" => "source",
            "name" => "source",
        ]);

        $firewall_source_port = new Text("source_port", [
            "id" => "source_port",
            "name" => "source_port",
        ]);

        $firewall_destination = new Text("destination", [
            "id" => "destination",
            "name" => "destination",
        ]);

        $firewall_destination_port = new Text("destination_port", [
            "id" => "destination_port",
            "name" => "destination_port",
        ]);

        $firewall_protocol = new Select("protocol", [
            0 => "TCP",
            1 => "UDP",
        ], [
            "id" => "protocol",
            "name" => "protocol",
        ]);

        $firewall_options = new Text("options", [
            "id" => "options",
            "name" => "options",
        ]);

        $firewall_description = new TextArea("description", [
            "id" => "description",
            "name" => "description",
        ]);

        $this->add($firewall_status);
        $this->add($firewall_direction);
        $this->add($firewall_action);
        $this->add($firewall_source);
        $this->add($firewall_source_port);
        $this->add($firewall_destination);
        $this->add($firewall_destination_port);
        $this->add($firewall_protocol);
        $this->add($firewall_options);
        $this->add($firewall_description);
    }
}