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
 */
namespace FPanel\Process;

/**
 * Class Vhost
 * @package FPanel\Process
 */
class Vhost extends Process
{
    public function create($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('create'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('update'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function remove($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('remove'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function suspend($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('suspend'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function unsuspend($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('unsuspend'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateUser($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('update-user'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function changeIpAddress($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('change-ipaddr'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function aspnetSettings($params = array())
    {
        try {

            return $this->socket->process('vhostmng')
                ->job(array('list-setting'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function vDirSettings($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('list-setting'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function savevDirSettings($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('save-vdir-setting'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function saveAspNetSettings($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('aspnet-setting'))
                ->params($params)
                ->create()
                ->send(3, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function saveConnStr($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('aspnet-setting'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function removeConnStr($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('conn-remove'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function removeCustomError($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('error-remove'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function saveCustomError($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('aspnet-setting'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createVdir($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('create-vdir'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateVdir($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('update-vdir'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function removeVdir($params = array())
    {
        try {
            return $this->socket->process('vhostmng')
                ->job(array('remove-vdir'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}