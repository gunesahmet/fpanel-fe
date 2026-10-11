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
 * Class Packages
 * @package FPanel\Process
 */
class Packages extends Process
{
    public function pkgList()
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('list'))
                ->create()
                ->send(600, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function systemCheck()
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('system-check'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getSourceLists()
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('source-lists'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function createSourceList($params)
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('create-source-lists'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function updateSourceList($params)
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('edit-source-lists'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeSourceList($params)
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('remove-source-lists'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function initSourcelist()
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('init-source-lists'))
                ->params()
                ->create()
                ->send(0, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getDepsPackage($params)
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('get-deps-package'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function installPackage($params)
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('install-package'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function unInstallPackage($params)
    {
        try {
            return $this->socket->process('packagemng')
                ->job(array('uninstall-package'))
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