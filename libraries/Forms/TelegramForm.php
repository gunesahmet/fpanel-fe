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
 * Class TasksForm
 * @package FPanel\Forms
 */
class TelegramForm extends Form
{

    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
    }

    /**
     * @param $options
     */
    private function formElements($options)
    {
        $api_key = new Text("api_key", array(
            "id" => "api_key",
            "name" => "api_key",
        ));

        $telegram_status = new Check("telegram_status", array(
            "id" => "telegram_status",
            "name" => "telegram_status",
        ));

        $service_status = new Check("service_status", array(
            "id" => "service_status",
            "name" => "service_status",
        ));

        $backup_restore = new Check("backup_restore", array(
            "id" => "backup_restore",
            "name" => "backup_restore",
        ));

        $domain_create = new Check("domain_create", array(
            "id" => "domain_create",
            "name" => "domain_create",
        ));

        $licence_expiring = new Check("licence_expiring", array(
            "id" => "licence_expiring",
            "name" => "licence_expiring",
        ));

        $expire_left = new Text("expire_left", array(
            "id" => "expire_left",
            "name" => "expire_left",
        ));

        $network_changes = new Check("network_changes", array(
            "id" => "network_changes",
            "name" => "network_changes",
        ));

        $system_updates = new Check("system_updates", array(
            "id" => "system_updates",
            "name" => "system_updates",
        ));

        $load_average = new Check("load_average", array(
            "id" => "load_average",
            "name" => "load_average",
        ));

        $load_average_1min = new Text("load_average_1min", array(
            "id" => "load_average_1min",
            "name" => "load_average_1min",
        ));

        $load_average_5min = new Text("load_average_5min", array(
            "id" => "load_average_5min",
            "name" => "load_average_5min",
        ));

        $load_average_15min = new Text("load_average_15min", array(
            "id" => "load_average_15min",
            "name" => "load_average_15min",
        ));

        $disk_status = new Check("disk_status", array(
            "id" => "disk_status",
            "name" => "disk_status",
        ));

        $disk_status_value = new Text("disk_status_value", array(
            "id" => "disk_status_value",
            "name" => "disk_status_value",
        ));

        $ddos_attack = new Check("ddos_attack", array(
            "id" => "ddos_attack",
            "name" => "ddos_attack",
        ));

        if(!empty($options)) {
            if(!empty($options["data"])) {
                $api_key->setAttribute("value", $options["data"]['api_key']);
                $services = json_decode($options["data"]["settings"], true);
                if($services["telegram_status"])
                    $telegram_status->setAttribute('checked','checked');
                if($services["service_status"])
                    $service_status->setAttribute('checked','checked');
                if($services["backup_restore"])
                    $backup_restore->setAttribute('checked','checked');
                if($services["domain_create"])
                    $domain_create->setAttribute('checked','checked');
                if($services["licence_expiring"])
                    $licence_expiring->setAttribute('checked','checked');
                if($services["network_changes"])
                    $network_changes->setAttribute('checked','checked');
                if($services["system_updates"])
                    $system_updates->setAttribute('checked','checked');
                if($services["load_average"])
                    $load_average->setAttribute('checked','checked');
                if($services["disk_status"])
                    $disk_status->setAttribute('checked','checked');
                if($services["ddos_attack"])
                    $ddos_attack->setAttribute('checked','checked');
                if($services["expire_left"] != null)
                    $expire_left->setAttribute("value", $services['expire_left']);
                if($services["load_average_1min"] != null)
                    $load_average_1min->setAttribute("value", $services['load_average_1min']);
                if($services["load_average_5min"] != null)
                    $load_average_5min->setAttribute("value", $services['load_average_5min']);
                if($services["load_average_15min"] != null)
                    $load_average_15min->setAttribute("value", $services['load_average_15min']);
                if($services["disk_status_value"] != null)
                    $disk_status_value->setAttribute("value", $services['disk_status_value']);
            }
        }
        $this->add($api_key);
        $this->add($telegram_status);
        $this->add($service_status);
        $this->add($backup_restore);
        $this->add($domain_create);
        $this->add($licence_expiring);
        $this->add($network_changes);
        $this->add($system_updates);
        $this->add($load_average);
        $this->add($load_average_1min);
        $this->add($load_average_5min);
        $this->add($load_average_15min);
        $this->add($disk_status);
        $this->add($disk_status_value);
        $this->add($expire_left);
        $this->add($ddos_attack);
    }

}