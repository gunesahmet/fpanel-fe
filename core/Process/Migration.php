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
 * Class Migration
 * @package FPanel\Process
 */
class Migration extends Process
{
    public function connection($params = array())
    {
        try {
            return $this->socket->process('migrationmng')
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

    public function enable($params = array())
    {
        try {
            return $this->socket->process('migrationmng')
                ->job(array('enable'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function disable($params = array())
    {
        try {
            return $this->socket->process('migrationmng')
                ->job(array('disable'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function start($params = array())
    {
        try {
            return $this->socket->process('migrationmng')
                ->job(array('start'))
                ->params($params)
                ->send(1, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getDomainData($params = array())
    {
        try {
            return $this->socket->process('migrationmng')
                ->job(array('get-domain'))
                ->params($params)
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}