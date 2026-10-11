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
 * Class MailServer
 * @package FPanel\Process
 */
class MailServer extends Process
{
    public function init(array $params = array())
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('init'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function spam($params = array())
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('spam'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function initWhiteList($params = array())
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('init-wlist'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function initBlackList($params = array())
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('init-blist'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function initSpam($data = array())
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('init-spam'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getMailEnableBlacklists($data = array())
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('get-blacklists'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function retrieve()
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('retrieve'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getPolicyConf()
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('get-policyd'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function savePolicyConf($params)
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('init-policyd'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function addPolicy($params)
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('add-policyd'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function updatePolicy($params)
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('update-policyd'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function deletePolicy($params)
    {
        try {
            return $this->socket->process('mailsrvmng')
                ->job(array('delete-policyd'))
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