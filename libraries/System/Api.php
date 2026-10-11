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

use FPanel\Environments\Parser;
use FPanel\Library\Library;
use FPanel\Models\Users;

class Api extends Library
{
    public function updateApiSettings(): bool
    {

        if($this->validate() == false)
            return false;

        $api_status = "on";

        if($this->getPost("api_status") != ON) {
            $api_status = "off";
        }

        $api_username = "";
        if($this->getPost("username") != null) {
            $api_username = $this->getPost("username");
        }
        $api_password= "";
        if($this->getPost("password") != null) {
            $api_password = $this->getPost("password");
        }
        $api_url = "";
        if($this->getPost("api_url") != null) {
            $api_url = $this->getPost("api_url");
        }

        $ini = new Parser(ROOTPATH.'/config/config.ini');
        $ini->data['api']['status'] = $api_status;
        $ini->data['api']['address'] = $api_url;
        $ini->data['api']['user'] = $api_username;
        $ini->data['api']['pass'] = $this->crypto->bencode($api_password);

        $ini->write();

        $enabled_api_users = Users::find("api_status = '1'");

        /**
         * @var $enabled_user Users
         */
        foreach($enabled_api_users as $enabled_user) {
            $enabled_user->api_status = "0";
            if (!$enabled_user->update()) {
                foreach ($enabled_user->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Api->updateApiSettings Users1",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }

        if($this->getPost("user_list") != null) {
            foreach($this->getPost("user_list") as $user) {
                $api_users = Users::findFirstById($user);
                $api_users->assign(array(
                    'api_status' => '1'
                ));
                if (!$api_users->update()) {
                    foreach ($api_users->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Api->updateApiSettings Users2",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }
        }

        return true;
    }

    private function validate(): bool
    {
        $required = array(
            array(
                'field' => 'username',
                'item' => $this->getPost("username"),
                'message' => __('Username is required')
            ),

            array(
                'field' => 'api_url',
                'item' => $this->getPost("api_url"),
                'message' => __('Api Url is required')
            ),
        );
        if($this->getPost("api_status") == "on") {
            $required[] =   array(
                'field' => 'password',
                'item' => $this->getPost("password"),
                'message' => __('Password is required')
            );
        }

        $this->validation->Required($required);

        $this->validation->Confirm($this->getPost("password"),'confirm-password', $this->getPost("confirm-password"),__('Please confirm password'));

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}