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
 * Class Log
 * @package FPanel\Process
 */
class Ssl extends Process
{
    public function createCSR(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('create-csr-key'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function installSSL(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('install-ssl'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeSSL(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('remove-ssl'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeWeb(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('remove-ssl-webserver'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function insertSystemCert(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('insert-system-ssl-cert'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function insertSystemCertBundle(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('insert-system-ssl-ca'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function installSystemSsl()
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('system-ssl'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function deactivateSystemSsl(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('deactivate-system-ssl'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function writeBundle(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('write-bundle'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function writeCrt(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('write-crt'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function writePkey(array $params = array())
    {
        try {
            return $this->socket->process('sslmng')
                ->job(array('write-pkey'))
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