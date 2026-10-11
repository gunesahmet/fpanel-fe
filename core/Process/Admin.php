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
 * Class NetworkData
 * @package FPanel\Network
 */
class Admin extends Process
{
    public function conf()
    {
        try {
            return $this->socket->process('adminmng')
                ->job(array('conf'))
                ->create()
                ->send(1, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function init()
    {
        try {
            return $this->socket->process('adminmng')
                ->job(array('init'))
                ->create()
                ->send("10", $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function changeApache()
    {
        try {
            return $this->socket->process('adminmng')
                ->job(array('change-apache'))
                ->create()
                ->send(1, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function resetFactory()
    {
        try {
            return $this->socket->process('adminmng')
                ->job(array('reset-factory'))
                ->create()
                ->send(1, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function sysRelax()
    {
        try {
            return $this->socket->process('adminmng')
                ->job(array('sys-relax'))
                ->create()
                ->send(1, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function reboot()
    {
        try {
            return $this->socket->process('adminmng')
                ->job(array('reboot'))
                ->create()
                ->send(1, $this->server_id)
                ->response();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function sysinfo()
    {
        try {
            $system_data = $this->socket->process('adminmng')
                ->job(array('sysinfo'))
                ->create()
                ->send(3, $this->server_id)
                ->response();

            if ($this->env->isJson($system_data))
                $system_data = json_decode(trim($system_data), true);
            else {
                $system_data = array(
                    'system' => array(
                        'os' => 'unknown',
                        'kernek' => 'unknown'
                    ),
                    'uptime' => '0',
                    'cpu_info' => 'unknown',
                    'cpu' => array(
                        'total' => '100',
                        'used' => '0',
                        'free' => '100'
                    ),
                    'memory' => array(
                        'total' => '0',
                        'used' => '0',
                        'free' => '0'
                    ),
                    'disk' => array(
                        'total' => '0',
                        'used' => '0',
                        'free' => '0'
                    ),
                    'loadavg' => array(
                        'total' => '0.00',
                        'used' => '0.00',
                        'free' => '0.00'
                    ),
                    'network' => array(
                        'hostname' => 'unknown',
                        'ipaddr' => '127.0.0.1',
                        'traffic' => ''
                    ),

                );
            }
            return $system_data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


}