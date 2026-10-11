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
 */namespace FPanel\Process;



/**
 * Class Cloud
 * @package FPanel\Process
 */
class Cloud extends Process
{
    public function connection($params = array())
    {
        try {
            return $this->socket->process('cloudmng')
                ->job(array('connect'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function install($params = array())
    {
        try {
            return $this->socket->process('cloudmng')
                ->job(array('install'))
                ->params($params)
                ->create()
                ->send(7200, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getConfig($params = array())
    {
        try {
            return $this->socket->process('cloudmng')
                ->job(array('get-configs'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function setMain($params = array())
    {
        try {
            return $this->socket->process('cloudmng')
                ->job(array('set-main'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function rebootServer($params = array())
    {
        try {
            return $this->socket->process('cloudmng')
                ->job(array('reboot'))
                ->params($params)
                ->create()
                ->send(0, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

}