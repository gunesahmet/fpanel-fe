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
use FPanel\Models\Userdata;
use FPanel\Models\Users;

/**
 * Class Account
 * @package FPanel\System
 */
class Account extends Library
{
    public function save(): bool
    {
        $user_id = $this->getPost("user_id");
        if($this->changePassword($user_id) == false)
            return false;

        $userdata = Userdata::findFirst("user_id = '$user_id'");
        if($userdata) {
            $userdata->assign(array(
                'fullname' => $this->getPost("fullname"),
                'email' => $this->getPost("email"),
                'company' => $this->getPost("company"),
                'fax' => $this->getPost("fax"),
                'telephone' => $this->getPost("telephone"),
                'address' => $this->getPost("address"),
                'city' => $this->getPost("city"),
                'region' => $this->getPost("region"),
                'country' => $this->getPost("country"),
            ));
            if (!$userdata->update()) {
                foreach ($userdata->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Account->save",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    public function changePassword($user_id): bool
    {
        if($this->getPost("password") != null) {
            $users = Users::findFirst("id = '$user_id'");
            if($users) {
                $user_data = $users->toArray();
                $old_password = $user_data['password'];
                if (!$this->security->checkHash($this->getPost("old_password"), $old_password)) {
                    $this->error_messages = array(
                        'old_password' => __('Your password is incorrect!')
                    );
                    return false;
                }

                if($this->getPost("password") == null) {
                    $this->error_messages = array(
                        'password' => __('Password is required')
                    );
                    return false;
                }


                $users->assign(array(
                    'password' => $this->security->hash($this->getPost("password")),
                ));
                if (!$users->update()) {
                    foreach ($users->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Account->changePassword",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }
            return false;
        }
        return true;
    }
}