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

use FPanel\Models\ClientLimitsPermissions;
use FPanel\Models\Domains;
use FPanel\Models\Serviceplans;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use Phalcon\Mvc\User\Component;

class UserConfig extends Component
{
    public $user_data = array(
        'version' => "1.0",
        'backup_data' => array(),
        'serviceplans' => array(),
        'user' => array(),
        'userdata' => array(),
        'limits' => array(),
        'domains' => array(),
    );

    public $user_id;
    public $error_status = false;

    public function user($user_id): UserConfig
    {
        $user = Users::findFirstById($user_id);
        $this->user_id = $user_id;
        if($user) {
            $this->user_data["user"] = array(
                'username' => $user->username,
                'password' => $user->password,
                'parent' => $user->parent,
                'type' => $user->type,
                'must_change_password' => $user->must_change_password,
                'created_date' => $user->created_date,
                'expire_date' => $user->expire_date,
                'status_msg' => $user->status_msg,
                'status' => $user->status,
                'interface' => $user->interface,
                'language' => $user->language,
            );
        }
        else {
            $this->error_status = true;
        }

        return $this;
    }

    public function userdata(): UserConfig
    {
        $userdata = Userdata::findFirst("user_id = '$this->user_id'");
        if($userdata) {
            $this->user_data["userdata"] = array(
                'fullname' => $userdata->fullname,
                'email' => $userdata->email,
                'company' => $userdata->company,
                'fax' => $userdata->fax,
                'telephone' => $userdata->telephone,
                'address' => $userdata->address,
                'city' => $userdata->city,
                'region' => $userdata->region,
                'zipcode' => $userdata->zipcode,
                'country' => $userdata->country,
            );
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function limits(): UserConfig
    {
        $limits = ClientLimitsPermissions::findFirst("client_id = '$this->user_id'");
        if($limits) {
            $limit_data = $limits->toArray();
            unset($limit_data['id']);
            unset($limit_data['domain_id']);
            unset($limit_data['client_id']);
            $this->user_data["limits"] = $limit_data;
        }

        return $this;

    }

    public function serviceplans(): UserConfig
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

    public function domains(): UserConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        $domains = Domains::find("client_id = '$this->user_id'");
        if($domains) {
            foreach($domains as $domain) {
                $this->user_data['domains'][] = [
                    "domain_name" => $domain->name
                ];
            }
        }
        return $this;
    }
}