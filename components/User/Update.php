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
use FPanel\Components\Domain\Update as DomainUpdate;
use FPanel\Models\Domains;
use FPanel\Models\Users;
use FPanel\Server\Ipaddress;
use FPanel\System\LimitsPermission;
use FPanel\System\Statistics;
use FPanel\Users\Usersdata;

/**
 * Class Update
 * @package FPanel\Components\Update
 */
class Update extends Base
{
    use Helper;

    public function validation(): bool
    {
        $old_username = $this->post['old_username'];

        if($this->validator(true, $old_username) == false) {
            $this->error_messages =  $this->getMessages();
            return false;
        }
        return true;
    }

    public function validationForApi(): bool
    {
        $old_username = isset($this->post['old_username']) ? $this->post["old_username"] : $this->post["username"];
        if($this->apiValidator(true, $old_username) == false) {
            return false;
        }
        return true;
    }

    public function updateUser(): Update
    {
        $user = new Usersdata();
        $user->setPosts($this->post);
        if(!$user->update($this->user_id)) {
            $this->error_messages = $user->getMessages();
        }
        return $this;
    }

    public function updateStatistics(): Update
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $statistics = new Statistics();
        $statistics->setPosts($this->post);
        if($statistics->updateUserStatistic($this->user_id)) {
            $this->error_messages = $statistics->getMessages();
        }
        return $this;
    }

    public function updateIpAddress(): Update
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $ip = new Ipaddress();
        $ip->delete($this->user_id);
        $ip_list = $this->post['ipaddr_id'];
        foreach($ip_list as $ip_id) {
            if(!$ip->save($ip_id, $this->user_id)) {
                foreach ($ip->getMessages() as $key => $message) {
                    $this->error_messages[$key] = $message;
                }
            }
        }
        return $this;
    }

    public function updateLimitsPermissions(): Update
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $save_limits = new LimitsPermission();
        $save_limits->setPosts($this->post);
        $save_limits->user_id = $this->user_id;
        if (!$save_limits->update($this->user_id)) {
            foreach ($save_limits->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this;
    }


    public function suspend($client_id, $parent_id, $message = null, $status = PASSIVE): bool
    {
        $userdata = new Usersdata();
        if(!$userdata->suspend($client_id, $parent_id, $message)) {
            return false;
        }
        else {
            $statistics = new Statistics();
            if(!$statistics->suspend(USER, $client_id, $status)) {
                return false;
            }
        }
        $this->suspendChildUsers($client_id);
        $this->suspendChildUserDomains($client_id);
        return true;
    }

    public function unsuspend($client_id, $parent_id, $message = null): bool
    {
        $userdata = new Usersdata();
        if(!$userdata->unsuspend($client_id, $parent_id, $message)) {
            return false;
        }
        else {
            $statistics = new Statistics();
            if(!$statistics->unsuspend(USER, $client_id)) {
                return false;
            }
        }
        $this->unSuspendChildUsers($client_id);
        $this->unSuspendChildUserDomains($client_id);

        return true;
    }

    public function suspendChildUsers($client_id)
    {
        $userdata = new Usersdata();
        $statistics = new Statistics();

        $users = Users::find("parent = '$client_id'");
        if($users) {
            foreach($users as $child) {
                $userdata->suspend($child->id, $child->id);
                $statistics->suspend(USER, $child->id);
                $this->suspendChildUserDomains($child->id);
            }
        }
    }

    public function suspendChildUserDomains($client_id)
    {
        $domain_update = new DomainUpdate();
        $domains = Domains::find("client_id = '$client_id'");
        foreach($domains as $domain) {
            $domain_update->suspend($domain->id, $client_id);
        }
    }

    public function unSuspendChildUsers($client_id)
    {
        $userdata = new Usersdata();
        $statistics = new Statistics();

        $users = Users::find("parent = '$client_id'");
        if($users) {
            foreach($users as $child) {
                $userdata->unsuspend($child->id, $child->parent);
                $statistics->unsuspend(USER, $child->id);
                $this->unSuspendChildUserDomains($child->id);
            }
        }
    }

    public function unSuspendChildUserDomains($client_id)
    {
        $domains = new DomainUpdate();
        $domain_list = Domains::find("client_id = '$client_id'");
        foreach($domain_list as $domain) {
            $domains->unsuspend($domain->id, $client_id);
        }
    }
}