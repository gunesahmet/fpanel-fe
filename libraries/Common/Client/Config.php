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

use FPanel\Models\Backup;
use FPanel\Models\Domains;
use FPanel\Models\Serviceplans;
use FPanel\Models\Users;

trait Config
{
    public $user_data = [
        'version' => "1.0",
        'backup_data' => [],
        'serviceplans' => [],
        'domains' => [],
        'users' => [],
    ];
    public $user_id;
    public $error_status = false;

    public function backupdata($backup_id): Data
    {
        $backup = Backup::findFirstById($backup_id);
        if($backup) {
            $this->user_id = $backup->client_id;
            $this->user_data['backup_data'] = array(
                'type' => $backup->type,
                'repository' => $backup->repository,
                'ftp_repository' => array(
                    'user_name' => '',
                    'password' => ''
                ),
                'name' => $backup->name,
                'email' => $backup->email,
                'created_date' => $backup->created_date,
                'description' => $backup->description,
                'content' => json_decode($backup->content,true),
                'sizes' => json_decode($backup->sizes,true)
            );
        }
        return $this;
    }

    public function serviceplans(): Data
    {
        if($this->error_status == true) {
            return $this;
        }

        $serviceplans = Serviceplans::find("client_id = '$this->user_id'");
        if($serviceplans) {
            foreach($serviceplans->toArray() as $sp) {
                unset($sp['id']);
                unset($sp['client_id']);
                $this->user_data['serviceplans'][] = $sp;
            }
        }
        return $this;
    }

    public function domains($selecteds): Data
    {
        if($this->error_status == true) {
            return $this;
        }
        $domains = Domains::find("client_id = '$this->user_id'");
        if($domains) {
            foreach($domains as $domain) {
                if(in_array($domain->id, $selecteds)) {
                    $this->user_data['domains'][] = $domain->name;
                }
            }
        }
        return $this;
    }

    public function users($user_id): Data
    {
        $users = Users::find("parent = '$user_id' AND type NOT LIKE '4'");
        if($users) {
            foreach($users as $user) {
                $domains = Domains::find("client_id = '$user->id'");
                if($domains) {
                    foreach($domains as $domain) {
                        $this->user_data['users'][$user->username][] = $domain->name;
                    }
                }
            }
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }
}