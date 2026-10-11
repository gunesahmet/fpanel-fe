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
 * Class NetworkData
 * @package FPanel\Network
 */
class Networking extends Process
{
    public function create(array $params = array())
    {
        try {
            return $this->socket->process('networkmng')
                ->job(array('create'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    /**
     * @param array $params
     * @return mixed|string|null
     */
    public function remove(array $params = array())
    {
        try {
            return $this->socket->process('networkmng')
                ->job(array('remove'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    /**
     * @return mixed|string|null
     */
    public function init()
    {
        try {
            return $this->socket->process('networkmng')
                ->job(array('init'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    /**
     * @return mixed|string|null
     */
    public function show()
    {
        try {
            return $this->socket->process('networkmng')
                ->job(array('show'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    /**
     * @return mixed|string|null
     */
    public function defaultIpAddress()
    {
        try {
            return $this->socket->process('networkmng')
                ->job(array('show-default-ip'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    /**
     * @param $params
     * @return mixed|string|null
     */
    public function hostname($params)
    {
        try {
            return $this->socket->process('networkmng')
                ->job(array('hostname'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    /**
     * @param $params
     * @return mixed|string|null
     */
    public function setConfig($params)
    {
        try {
            return $this->socket->process('networkmng')
                ->job(array('set-config'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}