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
 * Class Appinstaller
 * @package FPanel\Process
 */
class Appinstaller extends Process
{
    public function install($package_name, $params = array())
    {
        try {
            return $this->socket->process('appinstaller')
                ->job(array($package_name))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function pkgList()
    {
        try {
            return $this->socket->process('appinstaller')
                ->job(array('get-list'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getpkg($params = array())
    {
        try {
            return $this->socket->process('appinstaller')
                ->job(array('get-pkg'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}