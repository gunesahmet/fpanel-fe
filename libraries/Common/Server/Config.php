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

use FPanel\Models\Backup;
use FPanel\Models\Blacklist;
use FPanel\Models\DnsTemplate;
use FPanel\Models\MailServerSettings;
use FPanel\Models\ScheduledTasks;
use FPanel\Models\Serviceplans;
use FPanel\Models\SpamList;
use FPanel\Models\SpamSettings;
use FPanel\Models\Users;

trait Config
{

    public $error_status = false;
    public $server_config = [
        'version'           => "1.0",
        'backup_data'       => [],
        'users'             => [
            "admins"    => [],
            "resellers" => [],
            "clients"   => [],
            "domains"   => [],
        ],
        'cronjobs'          => [],
        'serviceplans'      => [],
        'blacklists'        => [],
        'mail_servers'      => [],
        'dns'               => [],
        'spam_settings'     => [],
        'spam_list'         => [],
    ];

    public function backupdata($backup_id): Data
    {
        $backup = Backup::findFirstById($backup_id);
        if($backup) {
            $this->server_config['backup_data'] = array(
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
            );
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function users(): Data
    {
        $this->server_config["users"]["admins"] = $this->admins();
        $this->server_config["users"]["resellers"] = $this->resellers(1);
        $this->server_config["users"]["clients"] = $this->clients(1);
        $this->server_config["users"]["domains"] = $this->domains(1);
        return $this;
    }

    private function admins(): array
    {
        $admin_users = [];
        $admins = Users::find("type = '1'");
        if($admins) {
            foreach($admins as $admin) {
                $admin_users[$admin->username]["resellers"] = $this->resellers($admin->id);
                $admin_users[$admin->username]["clients"] = $this->clients($admin->id);
                $admin_users[$admin->username]["domains"] = $this->domains($admin->id);
            }
        }
        return $admin_users;
    }

    private function resellers($parent_id): array
    {
        $reseller_users = [];
        $resellers = Users::find("type = '2' and parent = '$parent_id'");
        if($resellers) {
            foreach($resellers as $reseller) {
                $reseller_users[$reseller->username] = [];
                $reseller_users[$reseller->username]["clients"] = $this->clients($reseller->id);
                $reseller_users[$reseller->username]["domains"] = $this->domains($reseller->id);
            }
        }
        return $reseller_users;
    }

    private function clients($parent_id): array
    {
        $clients_users = [];
        $clients = Users::find("type = '3' and parent = '$parent_id'");
        if($clients) {
            foreach($clients as $client) {
                $clients_users[$client->username] = [];
                $clients_users[$client->username]["domains"] = $this->domains($client->id);
            }
        }
        return $clients_users;
    }

    private function domains($parent_id): array
    {
        $domain_users = [];
        $domains = Users::find("type = '4' and parent = '$parent_id'");
        if($domains) {
            foreach($domains as $domain) {
                $domain_users[] = $domain->username;
            }
        }
        return $domain_users;
    }

    public function cronjobs(): Data
    {
        if($this->error_status == true) {
            return $this;
        }
        $cronjobs = ScheduledTasks::find("type = 'server'");
        if($cronjobs) {
            foreach($cronjobs as $cron) {
                $this->server_config['cronjobs'][] = $cron->toArray();
            }
        }
        return $this;
    }

    public function blacklist(): Data
    {
        if($this->error_status == true) {
            return $this;
        }
        $blacklists = Blacklist::find();
        if($blacklists) {
            foreach($blacklists as $blacklist) {
                $this->server_config['blacklists'][] = $blacklist->toArray();
            }
        }

        return $this;
    }

    public function dns(): Data
    {
        if($this->error_status == true) {
            return $this;
        }
        $dns = DnsTemplate::find();
        if($dns) {
            foreach($dns as $record) {
                $this->server_config['dns'][] = $record->toArray();
            }
        }
        return $this;
    }

    public function mail(): Data
    {
        if($this->error_status == true) {
            return $this;
        }
        $mail_servers = MailServerSettings::findFirst("server_id = '1'");
        if($mail_servers) {
            $this->server_config['mail_servers'] = $mail_servers->toArray();
        }
        return $this;
    }

    public function spamsettings(): Data
    {
        if($this->error_status == true) {
            return $this;
        }
        $spam_settings = SpamSettings::find();
        if($spam_settings) {
            $this->server_config['spam_settings'] = $spam_settings->toArray();
        }
        return $this;
    }

    public function serviceplans(): bool
    {
        $serviceplans = Serviceplans::find("client_id = '1'");
        if(!$serviceplans)
            $this->server_config["serviceplans"] = $serviceplans->toArray();
        return true;
    }

    public function spamlist(): Data
    {
        if($this->error_status == true) {
            return $this;
        }
        $spam_list = SpamList::find();
        if($spam_list) {
            $this->server_config['spam_list'] = $spam_list->toArray();

        }
        return $this;
    }

}