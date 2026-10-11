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
 */namespace FPanel\System;

use FPanel\Environments\Datatable;
use FPanel\Environments\Parser;
use FPanel\Library\Library;
use FPanel\Models\Settings;
use FPanel\Process\Statistics;

/**
 * Class Update
 * @package FPanel\Update
 */
class Update extends Library
{
    /**
     * @param $server_id
     * @return array
     */
    public function updateList($server_id): array
    {
        $datatable = new Datatable();

        $datatable->select([
            'updates.package',
            'updates.version',
            'updates.created_date',
            'updates.description'
        ])
            ->from("`updates`");
        if($server_id == "1")
            $datatable->innerJoin("servers", "servers.id = updates.server_id")
            ->where("servers.type = 'main'");
        else
            $datatable->where("updates.server_id = '$server_id'");
        return  $datatable->orderDesc("updates.created_date")
            ->searchColumn(["updates.description", "updates.package"])
            ->generate()
            ->execute()
            ->result();
    }

    /**
     * @param $server_id
     * @return bool
     */
    public function settingsUpdate($server_id): bool
    {
        if($this->validate() == false)
            return false;

        $server_settings = Settings::findFirst("server_id = '$server_id'");


        if($server_settings) {
            $auto_update = PASSIVE;
            if($this->getPost("auto_update") == ON)
                $auto_update = ACTIVE;

            $random_time = PASSIVE;
            if($this->getPost("random_time") == ON)
                $random_time = ACTIVE;

            $statistics_random = PASSIVE;
            if($this->getPost("statistics_random") == ON)
                $statistics_random = ACTIVE;

            $ini = new Parser(ROOTPATH.'/config/config.ini');


            $data = array(
                'auto_update' => $auto_update,
                'update_time' => $this->has("update_time") && $this->getPost("update_time") != null ? $this->getPost("update_time") : $ini->data["update"]["time"] ,
                'random_time' => $random_time,
            );
            $server_settings->assign($data);
            if (!$server_settings->update()) {
                foreach ($server_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Update->settingsUpdate",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            $ini->data['update']['time'] = $data["update_time"];

            if($this->has("statistics_time") && $this->getPost("statistics_time") != null) {
                $ini->data['statistics']['time'] = $this->getPost("statistics_time");
            }
            $ini->data['update']['random'] = "$random_time";
            $ini->data['update']['auto'] = "$auto_update";
            $ini->data['statistics']['random'] = "$statistics_random";
            $ini->write();

            $statisticsmng = new Statistics();
            $statisticsmng->setServerId($server_id);
            $statisticsmng->setConfig([
                'statistics-random' => $this->getPost("statistics_random"),
                'statistics-time' => $this->has("statistics_time") && $this->getPost("statistics_time") == null ? $this->getPost("statistics_time") : $ini->data["statistics"]["time"] ,
                'update-random' => $this->getPost("random_time"),
                'update-time' => $this->has("update_time") && $this->getPost("update_time") == null ? $this->getPost("update_time") : $ini->data["update"]["time"] ,
                'update-auto' => $this->getPost("auto_update"),
                "server-addr" => $this->request->getServerAddress()
            ]);
            return true;
        }
        else {
            $this->error_messages = array('content' => __('System update record not found for this server'));
            return false;
        }
    }

    /**
     * @return bool
     */
    private function validate(): bool
    {
        if($this->getPost("random_time") != "on") {
            $this->validation->Required(array(array('item' => $this->getPost("update_time"),'field' => 'update_time', 'message' => __('Update time is required'))))
                ->Clock($this->getPost("update_time"),'update_time',__('Please enter valid time'));
        }

//        $this->validation->IpOrDomain($this->getPost("update_server"),'update_server',__('Please enter valid Hostname or Ip Address'));
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;

    }

}