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
 */namespace FPanel\Common\Server;

use FPanel\Common\Base;
use FPanel\Models\Backup;
use FPanel\Models\Domains;
use FPanel\Process\BackupRestore;

/**
 * Class BackupServer
 * @package FPanel\Common\Client
 */
class BackupServer extends Base
{
    // Ana gereksinimler
    private $created_date;
    private $backup_name;
    private $server_id;
    private $backup_id;
    private $repository;
    private $content        = [];
    private $server_configs = [];

    public function backup($status, $backup_id, $size)
    {
        if(!$this->get($backup_id)) {
            $this->log->error([
                "File" => "BackupServer",
                "Error" => "Backup config cant create",
            ]);
            return false;
        }
        if($status == "directory") {
            if($this->configs() == true) {
                $this->backupFinish($size);
                $this->backupClear();
            }
            else {
                $this->backupFailed();
            }
        }
    }

    private function get($backup_id): bool
    {
        $backup = Backup::findFirstById($backup_id);
        if($backup) {
            $this->backup_id = $backup_id;
            $this->server_id = $backup->server_id;
            $this->backup_name = $backup->name;
            $this->repository = $backup->repository;
            $this->created_date = $backup->created_date;
            $this->content = json_decode($backup->content,true);
            return true;
        }
        else {
            return false;
        }
    }

    private function configs(): bool
    {
        $config = new Data();
        $config->backupdata($this->backup_id);
        $config->serviceplans();
        $config->users();

        if(!$config->error_status) {
            $this->server_configs = $config->server_config;
            $selected_domains = $this->content['domains'];
            foreach($selected_domains as $selected_id) {
                $domain = Domains::findFirstById($selected_id);
                $this->server_configs['selected_domains'][] = $domain->name;
            }
            return true;
        }
        else {
            return false;
        }
    }

    private function backupFinish($size)
    {
        $backup = Backup::findFirstById($this->backup_id);
        $backup->assign(array(
            'status' => BACKUP_FINISHED,
            'progress'  => '100',
            'sizes' => json_encode($size)
        ));
        if(!$backup->update()) {
            $error_messages = [];
            foreach ($backup->getMessages() as $message) {
                $error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "message" => "BackupServer",
                "data" => "Backup finish failed",
                "errors" => $error_messages,
            ]);
        }
    }

    private function backupFailed()
    {
        $this->log->error([
            "message" => "BackupServer",
            "data" => "Backup failed",
        ]);
        $backup = Backup::findFirstById($this->backup_id);
        if($backup) {
            $backup->assign(array(
                'status' => BACKUP_FAILED,
            ));
            if(!$backup->update()) {
                $error_messages = [];
                foreach ($backup->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "message" => "BackupServer",
                    "data" => "Backup update failed",
                    "errors" => $error_messages,
                ]);
            }
        }
    }

    private function backupClear()
    {
        $backupmng = new BackupRestore();
        $params = array(
            'date' => $this->date->getTimeStamp($this->created_date),
            'name' => $this->backup_name,
            'backup-id' => $this->backup_id,
            'data' => 'finish',
            "server-conf" => $this->crypto->fencode(json_encode($this->server_configs)),
            "server-addr" => $this->request->getServerAddress(),
        );
        $backupmng->backupServer($params);
    }

    public function upload($data)
    {
        $backup = Backup::findFirstById($data['backup_id']);
        if($backup) {
            if (!isset($data['error']) || ($data['error'] == null)) {
                $backup->status = BACKUP_FINISHED;
                $backup->progress = "100";
                $backup->sizes = json_encode($data["size"]);
            }
            else {
                $backup->status = BACKUP_FAILED;
                $backup->description = "Corrupted";
                $backup->progress = "0";
            }

            if(!$backup->update()) {
                $error_messages = [];
                foreach ($backup->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "message" => "BackupServer",
                    "data" => "Backup upload update failed",
                    "errors" => $error_messages,
                ]);
            }
        }

    }

}