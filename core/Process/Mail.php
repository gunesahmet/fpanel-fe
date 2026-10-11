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
 * Class Mail
 * @package FPanel\Process
 */
class Mail extends Process
{
    public function createDomain($params = array())
    {
        try {
            return $this->socket->process('mailmng')
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

    public function updateDomain($data = array())
    {
        try {
            $params = array(
                'domain'    => $data['domain'],
                'new-passwd'    => $data['new-passwd'], // Domainin ftp passwordu gönderilecek
            );

            if(!empty($data['quota'])) {
                $params['quota'] = $data['quota']; // Unlimited gönderilecek yada rakam olacak
            }
            return $this->socket->process('mailmng')
                ->job(array("update"))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeDomain($data = array())
    {
        try {
            $params = array(
                'domain'    => $data['domain'],
            );

            return $this->socket->process('mailmng')
                ->job(array("remove"))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function createMail($data = array())
    {
        try {
            $params = array(
                'mail-addr'    => $data['mail-addr'], // Mail adresi mail@domain.com şeklinde gönderilecek
                'passwd'    => $data['passwd'],
                'quota'    => $data['quota'],
            );

            return $this->socket->process('mailmng')
                ->job(array('create-mail'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function updateMail($params = array())
    {
        try {
            return $this->socket->process('mailmng')
                ->job(array('update-mail'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeMail($data = array())
    {
        try {
            $params = array(
                'mail-addr'    => $data['mail-addr'],
            );

            return $this->socket->process('mailmng')
                ->job(array('remove-mail'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function domainSettings($params = array())
    {
        try {
            return $this->socket->process('mailmng')
                ->job(array("domain-settings"))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function mailAddressSettingsAutoResponder($params = array())
    {
        try {
            return $this->socket->process('mailmng')
                ->job(array("create-mail-autoreponder"))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function createAlias($data = array())
    {
        try {
            $params = array(
                'mail-addr'    => $data['mail-addr'], // Mail adresi mail@domain.com şeklinde gönderilecek
                'mail-alias'    => $data['mail-alias'],
            );
            return $this->socket->process('mailmng')
                ->job(array('create-mail-alias'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeAlias($data = array())
    {
        try {
            $params = array(
                'mail-addr'    => $data['mail-addr'], // Mail adresi mail@domain.com şeklinde gönderilecek
                'mail-alias'    => $data['mail-alias'],
            );
            return $this->socket->process('mailmng')
                ->job(array('remove-mail-alias'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function createForward($params = array())
    {
        try {
            return $this->socket->process('mailmng')
                ->job(array('create-mail-forward'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeForward($params = array())
    {
        try {
            return $this->socket->process('mailmng')
                ->job(array('remove-mail-forward'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function suspendDomain($params = array())
    {
        try {
            return $this->socket->process('mailmng')
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

    public function unsuspendDomain($params = array())
    {
        try {
            return $this->socket->process('mailmng')
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

    public function suspendMail($data = array())
    {
        try {
            $params = array(
                'domain'    => $data['domain'], // Domain göndermeye gerek yok
                'mail-addr'    => $data['mail-addr'], // Domain göndermeye gerek yok
            );
            return $this->socket->process('mailmng')
                ->job(array($data['suspend-mail']))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function unsuspendMail($data = array())
    {
        try {
            $params = array(
                'domain'    => $data['domain'], // Domain göndermeye gerek yok
                'mail-addr'    => $data['mail-addr'], // Domain göndermeye gerek yok
            );
            return $this->socket->process('mailmng')
                ->job(array($data['unsuspend-mail']))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function queue($params)
    {
        try {
            return $this->socket->process('mailqueuemng')
                ->job(array('queue'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function retryQueue()
    {
        try {
            return $this->socket->process('mailqueuemng')
                ->job(array('flush'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function clearQueue()
    {
        try {
            return $this->socket->process('mailqueuemng')
                ->job(array('clear'))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeQueueMsg($id)
    {
        try {
            return $this->socket->process('mailqueuemng')
                ->job(array('remove'))
                ->params(array('file' => $id))
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}