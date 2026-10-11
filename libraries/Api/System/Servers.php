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
 */namespace FPanel\Api\System;

use FPanel\Environments\QueryBuilder;
use Phalcon\Mvc\User\Component;

class Servers extends Component
{
    public function init($sub, $request_data)
    {
        switch($sub) {
            case "list":
                return $this->list($request_data);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function list($request_data)
    {
        $qbuilder = new QueryBuilder();

        $server_list = $qbuilder->select([
            "servers.name",
            "servers.os",
            "servers.type",
        ])
            ->from("servers")
            ->where([
                "conditions" => "servers.type NOT LIKE ?1",
                "bind" => [1 => "main"]
            ])->find();

        if(!empty($qbuilder->getMessages())) {
            $server_list = array(
                'status' => false,
                'errors' => $qbuilder->getMessages()
            );
        }
        else {
            foreach ($server_list as $k => $v) {
                $server_list[$k]["type"] = json_decode($v["type"], true);
            }
            $server_list['status'] = "1";
        }
        return $server_list;
    }
}