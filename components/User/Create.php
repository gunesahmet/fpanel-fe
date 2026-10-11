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
 */namespace FPanel\Components\User;

use FPanel\Components\Base;
use FPanel\Server\Ipaddress;
use FPanel\System\LimitsPermission;
use FPanel\System\Statistics;
use FPanel\Users\Usersdata;

class Create extends Base
{
    protected $created_user_id;
    use Helper;

    public function validation(): bool
    {
        if($this->validator() == false) {
            $this->error_messages =  $this->getMessages();
            return false;
        }
        return true;
    }

    public function validationForApi(): bool
    {
        if($this->apiValidator() == false) {
            return false;
        }
        return true;
    }

    public function saveUser($restore = false): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $user = new Usersdata();
        $user->setPosts($this->post);
        $user->parent_id = $this->parent_id;
        $user->options = $this->options;
        $created_user_id = $user->create($this->dates, false, $restore);

        if (!$created_user_id) {
            foreach ($user->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        else {
            $this->created_user_id = $created_user_id;
        }
        return $this;
    }

    public function saveIpAddresses(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $ip = new Ipaddress();
        $ip_list = $this->post['ipaddr_id'];
        foreach($ip_list as $ip_id) {
            if(!$ip->save($ip_id, $this->created_user_id)) {
                foreach ($ip->getMessages() as $key => $message) {
                    $this->error_messages[$key] = $message;
                }
            }
        }
        return $this;
    }

    public function saveLimitsPermissions($restore): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $save_limits = new LimitsPermission();
        $save_limits->setPosts($this->post);
        $save_limits->user_id = $this->created_user_id;
        $save_limit_messages = $save_limits->save($restore);

        if (!$save_limit_messages) {
            foreach ($save_limits->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this;
    }

    public function saveStatistics(): Create
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $statistics = new Statistics();
        $statistics->setPosts($this->post);
        $status = $statistics->saveClientStatistic($this->created_user_id, $this->parent_id, $this->dates);

        if (!$status) {
            foreach ($statistics->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this;
    }
}