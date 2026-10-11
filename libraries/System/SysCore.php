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

use Exception;
use FPanel\Library\Library;
use FPanel\Models\SystemSettings;

/**
 * Class SysCore
 * @package FPanel\SysCore
 */
class SysCore extends Library
{
    /**
     * @return array|mixed
     */
    public function getSystemData()
    {
        $system_settings = SystemSettings::findFirst("key = 'system_data'");

        if($system_settings != null) {
            $result = json_decode($system_settings->value,true);
            if($result['system_type'] == CLOUD) {
                $params = $this->dispatcher->getParams();
                $server_id = "1";
                if(isset($params["server_id"])) {
                    $server_id = $params['server_id'];
                }
                $result["server_id"] = $server_id;
            }
            else {
                $result["server_id"] = "1";
            }

            return $result;
        }
        else {
            return array();
        }
    }

    /**
     * @return array
     */
    public function getCloudServers(): array
    {
        return $this->queryBuilder->select([
            'id',
            'name',
            'os',
            'type'
        ])
            ->from("servers")
            ->find();
    }
}
