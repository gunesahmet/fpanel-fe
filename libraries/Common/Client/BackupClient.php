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
 */namespace FPanel\Common\Client;

use FPanel\Common\Base;
use FPanel\Models\Backup;
use FPanel\Models\Domains;
use FPanel\Models\Users;
use FPanel\Process\BackupRestore;

/**
 * Class BackupClient
 * @package FPanel\Common\Client
 */
class BackupClient extends Base
{
    // Ana gereksinimler
    private $client_configs = [];
    private $user_name;
    private $create_date;
    private $backup_name;
    private $user_id;
    private $client_name;
    private $backup_id;
    private $repository;
    private $content = array();

    private $user_configs = array();

    public function backup($status, $backup_id, $size)
    {
        if(!$this->get($backup_id)) {
            $this->log->error([
                "File" => "BackupClient",
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
            $backup_data = $backup->toArray();
            $this->backup_id = $backup_id;

            $this->backup_name = $backup_data['name'];
            $this->user_id = $backup_data['client_id'];
            $client = Users::findFirstById($this->user_id);
            $this->client_name = $client->username;
            $this->repository = $backup_data['repository'];
            $this->create_date = $backup_data['created_date'];
            $this->content = json_decode($backup_data['content'],true);

            $user = Users::findFirstById($backup_data['client_id']);
            if($user)
                $this->user_name = $user->username;

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
        $config->domains($this->content['domains']);
        $config->users($this->user_id);

        if($config->error_status == false) {
            $user_configs = $config->user_data;
            $this->user_configs = $user_configs;
            $selected_domains = $this->content['domains'];
            foreach($selected_domains as $selected_id) {
                $domain = Domains::findFirstById($selected_id);
                $user_configs['selected_domains'][] = $domain->name;
            }

            $this->client_configs = $user_configs;
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
                "message" => "BackupClient",
                "data" => "Backup finish update failed",
                "errors" => $error_messages
            ]);
        }
    }

    private function backupFailed()
    {
        $this->log->error([
            "message" => "BackupClient",
            "data" => "Backup failed"
        ]);
        $backup = Backup::findFirstById($this->backup_id);
        if($backup) {
            $backup->assign(array(
                'status' => BACKUP_FAILED,
            ));
            $error_messages = [];
            if(!$backup->update()) {
                foreach ($backup->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "message" => "BackupClient",
                    "data" => "Backup update failed",
                    "errors" => $error_messages
                ]);
            }
        }
    }

    private function backupClear()
    {
        $backupmng = new BackupRestore();
        $params = array(
            'date' => $this->date->getTimeStamp($this->create_date),
            'name' => $this->backup_name,
            'backup-id' => $this->backup_id,
            'data' => 'finish',
            'client-name' => $this->client_name,
            "client-conf" => $this->crypto->fencode(json_encode($this->client_configs))
        );
        $this->log->debug([
            "message" => "BackupClient",
            "data" => "backup finish process sending"
        ]);
        $params["server-addr"] = $this->request->getServerAddress();
        $backupmng->backupClient($params);
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
            $error_messages = [];
            if(!$backup->update()) {
                foreach ($backup->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "message" => "BackupClient",
                    "data" => "Backup upload update failed",
                    "errors" => $error_messages
                ]);
            }
        }
    }
}