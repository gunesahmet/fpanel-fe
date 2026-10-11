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
 * Class Subvhost
 * @package FPanel\Process
 */
class Subvhost extends Process
{
    public function create($params = array())
    {
        try {
            return $this->socket->process('subvhostmng')
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

    public function update($params)
    {
        try {
            return $this->socket->process('subvhostmng')
                ->job(array('update'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function remove($data = array())
    {
        try {
            $params = array(
                'domain' => $data['domain'],
                'subdomain' => $data['subdomain'],
            );
            return $this->socket->process('subvhostmng')
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

    public function suspend($data = array())
    {
        try {
            $params = array(
                'domain' => $data['domain'],
                'subdomain' => $data['subdomain'],
            );

            return $this->socket->process('subvhostmng')
                ->job(array('suspend'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function unsuspend($data = array())
    {
        try {
            $params = array(
                'domain' => $data['domain'],
                'subdomain' => $data['subdomain'],
            );

            return $this->socket->process('subvhostmng')
                ->job(array('unsuspend'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function updateUser($data = array())
    {
        try {
            $params = array(
                'domain' => $data['domain'],
                'subdomain' => $data['subdomain'],
                'user' => $data['ftpuser'],
                'new-user' => $data['new_ftpuser'],
            );

            return $this->socket->process('subvhostmng')
                ->job(array('update-user'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function changeIpAddress($data = array())
    {
        try {
            $params = array(
                'domain' => $data['domain'],
                'subdomain' => $data['subdomain'],
                'ipaddr' => $data['new_ip_address'],
                'old-ipaddr' => $data['old_ip_address'],
            );

            return $this->socket->process('subvhostmng')
                ->job(array('change-ipaddr'))
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