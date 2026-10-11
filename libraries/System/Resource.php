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

use FPanel\Library\Library;
use FPanel\Models\Domains;
use FPanel\Models\Users;

/**
 * Class Resource
 * @package FPanel\Resource
 */
class Resource extends Library
{
    public $parent_id;
    public $user_id;
    public $user_type;

    public function updateDomain(): bool
    {
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        $domain_id = $this->getPost("domain_id");
        $date = $this->env->createDates($this->getPost("created_date"), $this->getPost("expire_time_limit"));

        $domain = Domains::findFirstById($domain_id);
        if($domain) {
            $domain_name = $domain->name;
            $domain->assign(array(
                'expire_date' => $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date'])
            ));
            if (!$domain->update()) {
                foreach ($domain->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Resource->updateDomain domain",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $this->error_messages = array('domain' => __('Domain not found'));
            return false;
        }

        $user_data = Users::findFirst("username = '$domain_name'");

        if($user_data) {
            $this->user_id = $user_data->id;
            $user_data->assign(array(
                'expire_date' => $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date'])
            ));
            if (!$user_data->update()) {
                foreach ($user_data->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Resource->updateDomain users",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $this->error_messages = array('user' => __('Domain user not found'));
            return false;
        }

        if($this->limitsUpdateDomain() == false) {
            return false;
        }

        if($this->statisticsUpdateDomain() == false)
            return false;

        return true;
    }

    public function limitsUpdateDomain(): bool
    {
        if($this->user_id == null) {
            $this->error_messages = array('user' => __('Domain user not found'));
            return false;
        }
        $client_limits_permissions = new LimitsPermission();
        $client_limits_permissions->setPosts($this->getPost());
        $status = $client_limits_permissions->update($this->user_id);
        if(!$status) {
            foreach ($client_limits_permissions->getMessages() as $key => $value) {
                $this->error_messages[$key] = $value;
            }
            return false;
        }
        return true;
    }

    public function statisticsUpdateDomain(): bool
    {
        $statistics = new Statistics();
        $date = $this->env->createDates($this->getPost("created_date"), $this->getPost("expire_time_limit"));
        $expire_date = $date['expire_date'];

        $statistics->setPosts($this->getPost());
        $status = $statistics->updateDomainStatistic($this->getPost("domain_id"),array('expire_date' => $expire_date));
        if($status != true) {
            foreach ($statistics->getMessages() as $key => $value) {
                $this->error_messages[$key] = $value;
            }
            return false;
        }
        return true;
    }

}