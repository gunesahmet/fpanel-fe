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
class Firewall extends Process
{
    public function list(array $params = [])
    {
        try {
            return $this->socket->process('firewallmng')
                ->job(array('list'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function flush(array $params = [])
    {
        try {
            return $this->socket->process('firewallmng')
                ->job(array('flush'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function delete(array $params = [])
    {
        try {
            return $this->socket->process('firewallmng')
                ->job(array('delete'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function create(array $params = [])
    {
        try {
            return $this->socket->process('firewallmng')
                ->job(array('create'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}