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
 */namespace FPanel\Server;

use FPanel\Library\Library;
use FPanel\Models\ServerFtpSettings;
use FPanel\Process\Ftp;

/**
 * Class FtpSettings
 * @package FPanel\Server
 */
class FtpServer extends Library
{
    public function save(): bool
    {
        if($this->validate() == false)
            return false;

        $server_id = $this->getPost("server_id");
        $ftp_settings = ServerFtpSettings::findFirst("server_id = '$server_id'");
        $params = array(
            'ftp_type' => $this->getPost("ftp_type"),
            'timeout_idle' => $this->getPost("timeout_idle"),
            'timeout_no_transfer' => $this->getPost("timeout_no_transfer"),
            'maxinstances' => $this->getPost("maxinstances"),
            'max_clients' => $this->getPost("max_clients"),
            'welcome_message' => $this->getPost("welcome_message"),
        );
        if($ftp_settings) {
            $assign = array(
                'params' => json_encode($params)
            );
            $ftp_settings->assign($assign);
            if (!$ftp_settings->update()) {
                foreach ($ftp_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "FtpServer->save update",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $ftp_settings = new ServerFtpSettings();
            $ftp_settings->assign(array(
                'server_id' => $server_id,
                'params'    => json_encode($params)
            ));
            if (!$ftp_settings->create()) {
                foreach ($ftp_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "FtpServer->save",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        $this->settingsCurl();
        return true;

    }

    private function settingsCurl()
    {

        $ftpmng = new Ftp();
        $ftpmng->setServerId($this->getPost("server_id"));

        $params = array(
            'timeout-idle' => $this->getPost("timeout_idle"),
            'timeout-transfer' => $this->getPost("timeout_no_transfer"),
            'max-instances' => $this->getPost("maxinstances"),
            'max-clients' => $this->getPost("max_clients"),
            'message' => $this->getPost("welcome_message"),
        );

        $ftpmng->settings($params);

    }

    public function activeSessions($server_id): array
    {
        $ftpmng = new Ftp();
        $ftpmng->setServerId($server_id);

        $data = $ftpmng->sessions();
        $list = array();

        if($data != null) {
            $data = json_decode($data,true);
            if($server_id != "1")
                $server_link = "/server/$server_id";
            else
                $server_link = "";
            foreach($data as $user) {

                $input = '<input type="checkbox" id="'. $user['0'] .'" name="sessions[]" value="'. $user['0'] .'"/>';
                $action_buttons = '<a data-id="confirm" data-url="'.  $this->uri->url_encrypt('ftp/kill/'.$user['0'].$server_link) .'" class="text-danger">'. __('Kill Session') .'</a>';
                $list[] = array(
                    '0' => $input,
                    '1' => $user['1'],
                    '2' => $user['2'],
                    '3' => $user['3'],
                    '4' => $action_buttons,
                );
            }
        }

        return array (
            'iTotalRecords'        => count($list),
            'iTotalDisplayRecords' => count($list),
            'aaData'               => $list,
        );
    }

    public function killSession($id): bool
    {
        $ftpmng = new Ftp();
        $ftpmng->setServerId($this->getPost("server_id"));

        $params = array(
            'pid' => $id,
        );
        $status = $ftpmng->kill($params);
        if(strpos($status,"Err") !== false) {
            $this->error_messages['content'] = __('Ftp session not found');
            return false;
        }
        return true;
    }

    public function validate(): bool
    {
        if($this->getPost("server_type") == LINUX) {
            $required = array();
            $this->validation->Required($required)
                ->Numeric($this->getPost("timeout_idle"),'timeout_idle',__('Timeout Idle must be numeric and greater than 10'))
                ->Numeric($this->getPost("timeout_no_transfer"),'timeout_no_transfer',__('Timeout No Transfer must be numeric and greater than 10'))
                ->Numeric($this->getPost("maxinstances"),'maxinstances',__('Max Instances must be numeric'))
                ->Numeric($this->getPost("max_clients"),'max_clients',__('Max Clients must be numeric'));

            if($this->validation->Validate() == false) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }
        }
        return true;
    }
}
