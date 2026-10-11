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
 * Class Backup
 * @package FPanel\Process
 */
class Cloudbackup extends Process
{
    public function backupDomain($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('backup-domain'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function restoreDomain($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('restore-domain'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeBackup($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('remove'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeAllBackup($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('remove-all'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function uploadBackup($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('upload-domain'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function backupClient($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('backup-client'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function restoreClient($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('restore-client'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function restoreClientDomains($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('restore-client-domains'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function restoreClientMainDomains($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('restore-client-main-domains'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function uploadClientBackup($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('upload-backup-client'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getConfig($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('get-config'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function extractSubUsers($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('extract-sub-user'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function gDriveTest($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('gdrive-test'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function backupServer($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('backup-server'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function restoreServer($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('restore-server'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function extractServerUser($params = [])
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('extract-server-user'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function uploadServerBackup($params = array())
    {
        try {
            return $this->socket->process('cloudbackupmng')
                ->job(array('upload-backup-server'))
                ->params($params)
                ->create()
                ->send(1, $this->server_id);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}