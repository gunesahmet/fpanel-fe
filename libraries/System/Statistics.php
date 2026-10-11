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
 */namespace FPanel\System;

use Exception;
use FPanel\Environments\Environments;
use FPanel\Library\Library;
use FPanel\Models\ClientStatistics;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\Models\FtpUsers;
use FPanel\Process\Mail;
use FPanel\Process\User;
use FPanel\Process\Vhost;

/**
 * Class Statistics
 * @package FPanel\Statistics
 */
class Statistics extends Library
{
    public function saveDomainStatistic($domain_id, $parent_id, $date): bool
    {
        try {
            $domain_statistics = new DomainStatistics();
            $calculated_data = $this->calculateSize();
            $over_use = ACTIVE;
            if ($this->getPost("overuse") == OFF)
                $over_use = PASSIVE;

            if($this->getPost("web_statistics_type") == null)
                $statistic = "none";
            else
                $statistic = $this->getPost("web_statistics_type");

            $domain_statistics->assign(array(
                'domain_id' => $domain_id,
                'client_id' => $parent_id,
                'domain_name' => $this->getPost("name"),
                // Limit Bilgileri
                'mail' => '0',
                'mail_max' => $calculated_data['email_box_size_limit'],
                'db' => '0',
                'db_max' => $calculated_data['database_size_limit'],
                'disk' => '0',
                'disk_max' => $calculated_data['disk_limit'],
                'traffic' => '0',
                'traffic_max' => $calculated_data['traffic_limit'],
                'httpdoc' => '0',
                'logs' => '0',
                'status' => '1',
                'statistic' => $statistic,
                'over_use' => $over_use,
                'created_date' => $this->date->tsToDate($date['created_date']),
                'expire_date' => $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date']),
                'web_server_id' => $this->getPost("web_servers"),
                'dns_server_id' => $this->getPost("dns_servers"),
                'mail_server_id' => $this->getPost("mail_servers"),
                'last_init' => $this->date->tsToDate($date['created_date']),
            ));

            if ($domain_statistics->create()) {
                return true;
            }
            else {
                foreach ($domain_statistics->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Statistics->saveDomainStatistic",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        catch(Exception $e) {
            $this->error_messages['content'] = $e->getMessage();
            return false;
        }
    }

    public function saveClientStatistic(string $user_id, string $parent_id, array $dates): bool
    {
        $statistics_user = new ClientStatistics();

        $calculated_data = $this->calculateSize();
        $over_use = ACTIVE;
        if ($this->getPost("overuse") == OFF)
            $over_use = PASSIVE;

        if($this->getPost("web_statistics_type") == null)
            $this->setPost("web_statistics_type", "none");
        $type = "";
        switch($this->getPost("type")) {
            case RESELLER_USER:
                $type = RESELLERS;
                break;
            case CLIENT_USER:
                $type = CLIENTS;
                break;
        }

        $assign = array(
            'account_name'      => $this->getPost("username"),
            'client_id'         => $user_id,
            'parent_id'         => $parent_id,
            'type'              => $type,
            // Limit Bilgileri
            'mail'              => '0',
            'mail_max'          => $calculated_data['email_box_size_limit'],
            'db'                => '0',
            'db_max'            => $calculated_data['database_size_limit'],
            'httpdoc'           => '0',
            'disk'              => '0',
            'disk_max'          => $calculated_data['disk_limit'],
            'traffic'           => '0',
            'traffic_max'       => $calculated_data['traffic_limit'],
            'logs'              => '0',
            'status'            => '1',
            'statistic'         => $this->getPost("web_statistics_type"),
            'over_use'          => $over_use,
            'created_date'      => $this->date->tsToDate($dates['created_date']),
            'expire_date'       => $dates['expire_date'] == null || $dates['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($dates['expire_date']),
            'last_init'         => $this->date->dateTime(),
        );
        $statistics_user->assign($assign);

        if (!$statistics_user->create()) {
            foreach ($statistics_user->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Statistics->saveClientStatistic",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function deleteDomainStatistic($domain_id)
    {
        $statistics = DomainStatistics::findFirst("domain_id = '$domain_id'");
        if ($statistics) {
            if(!$statistics->delete()) {
                foreach($statistics->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Statistics->deleteDomainStatistic",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
    }

    public function deleteClientStatistic($user_id)
    {
        $statistics = ClientStatistics::findFirst("client_id = '$user_id'");
        if ($statistics) {
            // @TODO: User altındaki tüm userlar ve tüm domainler silinecek
            if(!$statistics->delete()) {
                foreach($statistics->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Statistics->deleteClientStatistic",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
    }

    private function calculateSize(): array
    {
        $email_box_size_limit = UNLIMITED;
        $database_size_limit = UNLIMITED;
        $disk_limit = UNLIMITED;
        $traffic_limit = UNLIMITED;

        if ($this->getPost("email_box_size_limit") != UNLIMITED)
            $email_box_size_limit = $this->getPost("email_box_size_limit")*1024*1024;

        if ($this->getPost("database_size_limit") != UNLIMITED)
            $database_size_limit = $this->getPost("database_size_limit")*1024*1024;

        if ($this->getPost("disk_limit") != UNLIMITED)
            $disk_limit = $this->getPost("disk_limit")*1024*1024;

        if ($this->getPost("traffic_limit") != UNLIMITED)
            $traffic_limit = $this->getPost("traffic_limit")*1024*1024;

        return array(
            'email_box_size_limit'  => $email_box_size_limit,
            'database_size_limit'  => $database_size_limit,
            'disk_limit'  => $disk_limit,
            'traffic_limit'  => $traffic_limit,
        );
    }

    public function updateDomainStatistic($domain_id, $date): bool
    {
        try {
            $domain_statistics = DomainStatistics::findFirst("domain_id = '$domain_id'");
            if($domain_statistics) {
                $traffic = $domain_statistics->traffic;
                if($traffic == null) {
                    $traffic = "0";
                }
            }
            else {
                $this->error_messages['domain'] = __('Domain statistics not found');
                return false;
            }
            if($this->getPost("status") == PASSIVE) {
                $status = "0";
            }
            else {
                $status = "1";
            }

            $calculated_data = $this->calculateSize();
            $over_use = ACTIVE;

            if ($this->getPost("overuse") == OFF)
                $over_use = PASSIVE;

            $assign = array(
                // Limit Bilgileri
                'mail_max' => $calculated_data['email_box_size_limit'] == UNLIMITED ? "0" : $calculated_data['email_box_size_limit'],
                'db_max' => $calculated_data['database_size_limit'] == UNLIMITED ? "0" : $calculated_data['database_size_limit'],
                'disk_max' => $calculated_data['disk_limit'] == UNLIMITED ? "0" : $calculated_data['disk_limit'],
                'traffic_max' => $calculated_data['traffic_limit'] == UNLIMITED ? "0" : $calculated_data['traffic_limit'],
                'traffic' => "$traffic",
                'status' => "$status",
                'over_use' => "$over_use",
            );
            if(isset($date["expire_date"])) {
                $assign["expire_date"] = $this->date->tsToDate($date["expire_date"]);
            }
            $domain_statistics->assign($assign);
            if(!$domain_statistics->update()) {
                foreach ($domain_statistics->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "System->updateDomainStatistic",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function updateUserStatistic($user_id): bool
    {
        $statistics_user = ClientStatistics::findFirst("client_id = '$user_id'");
        $created_date = new Environments();
        $date = $created_date->createDates($statistics_user->created_date, $this->getPost("expire_time_limit"));

        $calculated_data = $this->calculateSize();
        $over_use = ACTIVE;
        if ($this->getPost("overuse") == OFF)
            $over_use = PASSIVE;

        $statistics_user->assign(array(
            'account_name'  => $this->getPost("username"),
            'client_id' => $user_id,
//            'type' => $type,
            // Limit Bilgileri
            'mail_max' => $calculated_data['email_box_size_limit'],
            'db_max' => $calculated_data['database_size_limit'],
            'disk_max' => $calculated_data['disk_limit'],
            'traffic_max' => $calculated_data['traffic_limit'],
            'statistic' => $this->getPost("web_statistics_type"),
            'over_use' => $over_use,
            'expire_date' => $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date']),
        ));

        if($statistics_user->update()) {
            foreach ($statistics_user->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Statistics->updateUserStatistic",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function suspend($type, $id, $status = PASSIVE): bool
    {
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        switch($type) {
            case DOMAIN:
                $statistics = DomainStatistics::findFirst("domain_id = '$id'");
                if($statistics) {
                    $statistics->status = $status;
                    if (!$statistics->update()) {
                        foreach ($statistics->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "Statistics->suspend domain",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    $this->changeStatusCurl($status, $id);
                    return true;
                }
                else {
                    return false;
                }
            case USER:
                $statistics = ClientStatistics::findFirst("client_id = '$id'");
                if($statistics) {
                    $statistics->status = $status;
                    if (!$statistics->update()) {
                        foreach ($statistics->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "Statistics->suspend user",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    return true;
                }
                else {
                    return false;
                }
            default:
                return false;
        }
    }

    public function unsuspend($type, $id): bool
    {
        switch($type) {
            case DOMAIN:
                $statistics = DomainStatistics::findFirst("domain_id = '$id'");
                if($statistics) {
                    $statistics->status = ACTIVE;
                    if (!$statistics->update()) {
                        foreach ($statistics->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "Statistics->unsuspend domain",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    $this->changeStatusCurl(ACTIVE, $id);
                    return true;
                }
                else {
                    return false;
                }
            case USER:
                $statistics = ClientStatistics::findFirst("client_id = '$id'");
                if($statistics) {
                    $statistics->assign(array(
                        'status' => ACTIVE,
                    ));
                    if (!$statistics->update()) {
                        foreach ($statistics->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "Statistics->unsuspend user",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    return true;
                }
                else {
                    return false;
                }
            default:
                return false;
        }
    }

    private function changeStatusCurl($status, $domain_id)
    {
        $domains = Domains::findFirstById($domain_id);
        $ftp_users = FtpUsers::findFirstById($domains->ftp_user_id);

        $params = array(
            'domain' => $domains->name,
            'user' => $ftp_users->username
        );
        $usermng = new User();
        $vhostmng = new Vhost();
        $mailmng = new Mail();
        $usermng->setServerId($domains->web_server_id);
        $vhostmng->setServerId($domains->web_server_id);
        $mailmng->setServerId($domains->mail_server_id);
        if($status == ACTIVE) {
            $usermng->unsuspend($params);
            $vhostmng->unsuspend($params);
            $mailmng->unsuspendDomain($params);
        }
        else {
            $usermng->suspend($params);
            $vhostmng->suspend($params);
            $mailmng->suspendDomain($params);
        }
    }
}