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
 * Class Apache
 * @package FPanel\WebServer
 */
trait Apache
{
    protected function saveApache($server_id): bool
    {
        if(!$this->validateApache()) {
            return false;
        }
        $params = array(
            'keep-alive' => $this->getPost("keep_alive", "off"),
            'keep-alive-timeout' => $this->getPost("keepalive_timeout"),
            'server-timeout' => $this->getPost("timeout"),
            'max-keep-alive-req' => $this->getPost("max_keepalive_req"),
            'start-server' => $this->getPost("start_server"),
            'min-spare-server' => $this->getPost("min_spare_server"),
            'max-clients' => $this->getPost("max_client"),
            'max-req-per-child' => $this->getPost("max_req_per_child"),
        );
        $process = new Webservermng();
        $process->setServerId($server_id);
        $process->saveWebSertting($params);
        return true;
    }

    private function validateApache(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("keepalive_timeout"),
                'field' => 'keepalive_timeout',
                'message' => __('Keepalive Timeout is required'),
            ),
            array(
                'item' => $this->getPost("timeout"),
                'field' => 'timeout',
                'message' => __('Timeout value is required'),
            ),
            array(
                'item' => $this->getPost("max_keepalive_req"),
                'field' => 'max_keepalive_req',
                'message' => __('Max. Keep Alive Requests is required'),
            ),
            array(
                'item' => $this->getPost("start_server"),
                'field' => 'start_server',
                'message' => __('Start Server Value is required'),
            ),
            array(
                'item' => $this->getPost("min_spare_server"),
                'field' => 'min_spare_server',
                'message' => __('Min. Spare Server setting is required'),
            ),
            array(
                'item' => $this->getPost("max_client"),
                'field' => 'max_client',
                'message' => __('Max. Clients is required'),
            ),
            array(
                'item' => $this->getPost("max_req_per_child"),
                'field' => 'max_req_per_child',
                'message' => __('Max. Requests Per Child is required'),
            ),
        );
        $status = $this->validation->Required($required)
            ->Numeric($this->getPost("keepalive_timeout"),"keepalive_timeout",__('Please enter valid setting.'))
            ->Numeric($this->getPost("timeout"),"timeout",__('Please enter valid setting.'))
            ->Numeric($this->getPost("max_keepalive_req"),"max_keepalive_req",__('Please enter valid setting.'))
            ->Numeric($this->getPost("start_server"),"start_server",__('Please enter valid setting.'))
            ->Numeric($this->getPost("min_spare_server"),"min_spare_server",__('Please enter valid setting.'))
            ->Numeric($this->getPost("max_client"),"max_client",__('Please enter valid setting.'))
            ->Numeric($this->getPost("max_req_per_child"),"max_req_per_child",__('Please enter valid setting.'))
            ->Validate();

        if(!$status) {
            $this->web_error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}
