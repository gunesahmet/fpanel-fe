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
 * Class File
 * @package FPanel\Process
 */
class File extends Process
{
    public function folderList($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('folder-list'))
                ->params($params)
                ->create()
                ->send(3, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function fileList($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('file-list'))
                ->params($params)
                ->create()
                ->send(3, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function renameFile($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('rename-file'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function renameFolder($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('rename-folder'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function moveFile($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('move-file'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getFileContent($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('get-file-content'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function saveFileContent($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('save-file-content'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeFile($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('remove-file'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function removeFolder($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('remove-folder'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function createFolder($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('create-folder'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function createFile($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('create-file'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function uploadFtp($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('upload-ftp'))
                ->params($params)
                ->create()
                ->send(300, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function getFilePermission($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('get-permission'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }

    public function setFilePermission($params)
    {
        try {
            return $this->socket->process('filemng')
                ->job(array('set-permission'))
                ->params($params)
                ->create()
                ->send(10, $this->server_id)
                ->response();
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
}
    }
}