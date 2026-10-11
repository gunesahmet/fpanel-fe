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
 * Class Services
 * @package FPanel\Process
 */
class Services extends Process
{
    public function serviceStatus()
    {
        try {
            return $this->socket->process('servicesmng')
                ->job(array("status"))
                ->params(array('service' => "all"))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function serviceReload($service)
    {
        try {
            return $this->socket->process('servicesmng')
                ->job(array("reload"))
                ->params(array('service' => $service))
                ->create()
                ->send(300, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}

    }

    public function serviceRestart($service)
    {
        try {
            return $this->socket->process('servicesmng')
                ->job(array("restart"))
                ->params(array('service' => $service))
                ->create()
                ->send(300, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function serviceStop($service)
    {
        try {
            return $this->socket->process('servicesmng')
                ->job(array("stop"))
                ->params(array('service' => $service))
                ->create()
                ->send(300, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function serviceStart($service)
    {
        try {
            return $this->socket->process('servicesmng')
                ->job(array("start"))
                ->params(array('service' => $service))
                ->create()
                ->send(10, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}