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
 */namespace FPanel\WebServer;

use FPanel\Process\WebServer as Webservermng;

/**
 * Class IIS
 * @package FPanel\WebServer
 */
trait IIS
{
    protected function saveIis($server_id): bool
    {
        if(!$this->validateIis()) {
            return false;
        }

        $per_app_pool_status = "false";
        $per_application_pool_size = "0";
        if($this->getPost("per_application_pool_status", OFF) == ON) {
            $per_app_pool_status = "true";
            $per_application_pool_size = $this->getPost("per_application_pool", "0");
        }

        $static_compression = "false";
        if($this->getPost("static_compression", OFF) == ON) {
            $static_compression = "true";
        }

        $compress_file_size = $this->getPost("compress_file_size", "0");

        $dynamic_compression = "false";
        if($this->getPost("dynamic_compression", OFF) == ON) {
            $dynamic_compression = "true";
        }

        $hidden_segments = null;
        if(!empty($this->getPost("hidden_segments"))) {
            foreach ($this->getPost("hidden_segments") as $segment) {
                if($hidden_segments == null) {
                    $hidden_segments = $segment;
                }
                else {
                    $hidden_segments = $hidden_segments . ',' . $segment;
                }
            }
        }

        $params = array(
            'dynamiccomp' => $dynamic_compression,
            'staticcomp' => $static_compression,
            'filesizecomp' => $compress_file_size,
            'diskspace' => $per_app_pool_status,
            'diskspacelimit' => $per_application_pool_size,
        );

        $process = new Webservermng();
        $process->setServerId($server_id);
        $process->saveWebSertting($params);
        return true;
    }

    private function validateIis(): bool
    {
        if($this->getPost("compress_file_size") != null) {
            $this->validation->Numeric($this->getPost("compress_file_size"), "compress_file_size");
        }

        if($this->getPost("per_application_pool_status", OFF) == ON) {
//            $required[] = [
//                "field" => "per_application_pool",
//                "item" => $this->getPost("per_application_pool", null),
//                "message" => __("Per Application Pool Disk Space Limit is required")
//            ];
            $this->validation->Numeric($this->getPost("per_application_pool"),'per_application_pool');
        }

        if($this->validation->Validate() == false) {
            $this->web_error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}
