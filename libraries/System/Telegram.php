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

use FPanel\Environments\Datatable;
use FPanel\Environments\Parser;
use FPanel\Library\Library;
use FPanel\Models\TelegramUsers;

class Telegram extends Library
{
    public function userList(): array
    {
        $datatable = new Datatable();

        $delete_button = $datatable->buttons("telegram/deactivate/$1",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'), __('Delete'));
        $data = $datatable
            ->select([
                "chat_id",
                "username",
                "fullname",
                "status",
                "created_date",
            ])
            ->from("telegram_users")
            ->searchColumn([
                "fullname",
                "username",
            ])
            ->addColumn('actions', $delete_button,"chat_id")
            ->unsetColumns('chat_id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            unset($data['aaData'][$key][0]);
            $data['aaData'][$key] = array_values($data['aaData'][$key]);
        }

        return $data;
    }

    public function saveSettings(): bool
    {
        if(filter_var($this->getPost("url_prefix").$this->getPost("api_key"), FILTER_VALIDATE_URL)) {
            $telegram = new \FPanel\Telegram\Telegram($this->config->telegram->api_key);
            $telegram->deleteWebhook();
            $result = $telegram->setWebhook($this->getPost("url_prefix").$this->getPost("api_key"));
            if((!$result["ok"])) {
                $this->error_messages["api_key"] = $result["description"];
                return false;
            }
            else if(!$result["description"]) {
                $this->error_messages["api_key"] = $result["description"];
                return false;
            }
        }

        $telegram = \FPanel\Models\Telegram::findFirst();
        if(!$telegram) {
            $telegram = new \FPanel\Models\Telegram();
        }

        $telegram->api_key = $this->getPost("api_key");
        $telegram->settings = json_encode([
            "telegram_status" => $this->has("telegram_status") && $this->getPost("telegram_status") == "on",
            "service_status" => $this->has("service_status") && $this->getPost("service_status") == "on",
            "backup_restore" => $this->has("backup_restore") && $this->getPost("backup_restore") == "on",
            "domain_create" => $this->has("domain_create") && $this->getPost("domain_create") == "on",
            "network_changes" => $this->has("network_changes") && $this->getPost("network_changes") == "on",
            "system_updates" => $this->has("system_updates") && $this->getPost("system_updates") == "on",
            "load_average" => $this->has("load_average") && $this->getPost("load_average") == "on",
            "disk_status" => $this->has("disk_status") && $this->getPost("disk_status") == "on",
            "ddos_attack" => $this->has("ddos_attack") && $this->getPost("ddos_attack") == "on",
            "expire_left" => $this->has("expire_left") ? $this->getPost("expire_left") : "",
            "disk_status_value" => $this->has("disk_status_value") ? $this->getPost("disk_status_value") : "",
            "load_average_1min" => $this->has("load_average_1min") ? $this->getPost("load_average_1min") : "",
            "load_average_5min" => $this->has("load_average_5min") ? $this->getPost("load_average_5min") : "",
            "load_average_15min" => $this->has("load_average_15min") ? $this->getPost("load_average_15min") : "",
        ]);
        if(!$telegram->save()) {
            foreach ($telegram->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }
        else {
            $ini = new Parser(ROOTPATH.'/config/config.ini');
            $ini->data['telegram']['status'] = "1";
            $ini->data['telegram']['api_key'] = $this->getPost("api_key");
            $ini->write();
        }
        return true;
    }

    public function init(): bool
    {
        if($this->request->getRawBody() != null) {
            $telegram = new \FPanel\Telegram\Telegram($this->config->telegram->api_key);
            $post_data = json_decode($this->request->getRawBody(), true);

            $from_id = $post_data["message"]["from"]["id"];
            $is_bot = $post_data["message"]["from"]["is_bot"];
            $first_name = $post_data["message"]["from"]["first_name"];
            $last_name = $post_data["message"]["from"]["last_name"];
            $username = $post_data["message"]["from"]["username"];
            $language_code = $post_data["message"]["from"]["language_code"];


            $chat_id = $post_data["message"]["chat"]["id"];
            $date = $post_data["message"]["date"];
            $text = $post_data["message"]["text"];
            if(!$is_bot) {
                $telegram_user = TelegramUsers::findFirst("chat_id = '$chat_id'");

                if(!$telegram_user) {
                    $code = $this->generateTelegramCode();
                    $telegram_user = new TelegramUsers();

                    // TODO: Activasyon islemi icin user tablosuna kayit gerceklestiriliyor ama status 0 olmali
                    // Ardindan telegram aktivasyon sayfasinda kullanicidan kod istenecek orda kod onaylanirsa status 1 e cekilecek
                    // Status 1 olduktan sonra kullaniciya mesajlar gonderilmeye baslanacak
                    $telegram_user->assign([
                        "username" => $username,
                        "chat_id" => $chat_id,
                        "language" => $language_code,
                        "fullname" => $first_name . " ". $last_name,
                        "activation_code" => $code,
                        "status" => PASSIVE,
                    ]);

                    if(!$telegram_user->create()) {
                        $this->log->telegramError([
                            "result" => $telegram_user->getMessages(),
                        ]);
                        foreach ($telegram_user->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        return false;
                    }
                    $result = $telegram->sendMessage([
                        "chat_id" => $chat_id,
                        "parse_mode" => "HTML",
                        "text" => "Your activation code: <b>$code</b>"
                    ]);
                }
                else {
                    if($telegram_user->status == 0) {
                        $telegram->sendMessage([
                            "chat_id" => $chat_id,
                            "parse_mode" => "HTML",
                            "text" => "Your activation code: <b>$telegram_user->activation_code</b>"
                        ]);
                    }
                }
            }
        }
        return true;
    }

    public function activation(): bool
    {
        $code = $this->getPost("code");
        $telegram_user = TelegramUsers::findFirst("activation_code = '$code'");

        if($telegram_user) {
            $telegram = new \FPanel\Telegram\Telegram($this->config->telegram->api_key);
            $result = $telegram->sendMessage([
                "chat_id" => $telegram_user->chat_id,
                "parse_mode" => "HTML",
                "text" => "Hello <b style='color:#00c5ad'>$telegram_user->fullname</b>. Your activation is completed. After that, you can start receiving the <b style='color:red'>defined alerts</b>."
            ]);
            $telegram_user->status = 1;
            if(!$telegram_user->create()) {
                foreach ($telegram_user->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
            return true;
        }
        else {
            $this->error_messages["code"] = __("User not found");
        }
        return false;
    }

    public function deactivate($chat_id): bool
    {
        $telegram_user = TelegramUsers::findFirst("chat_id = '$chat_id'");
        $telegram = new \FPanel\Telegram\Telegram($this->config->telegram->api_key);
        $result = $telegram->sendMessage([
            "chat_id" => $telegram_user->chat_id,
            "parse_mode" => "HTML",
            "text" => "Hello <b style='color:#00c5ad'>$telegram_user->fullname</b>. After that, you will not receiving <b style='color:red'>any alerts</b>."
        ]);

        if($telegram_user) {
            if(!$telegram_user->delete()) {
                foreach ($telegram_user->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
            return true;
        }
        else {
            $this->error_messages["code"] = __("User not found");
        }
        return false;
    }

    private function generateTelegramCode(): string
    {
        $unique =   false;
        $chr_db  =   array('0','1','2','3','4','5','6','7','8','9');

        while (!$unique){
            $code = '';
            for ($count = 0; $count < 6; $count++){
                $chr = $chr_db[rand(0,count($chr_db)-1)];
                if (rand(0,1) == 0){
                    $chr = strtolower($chr);
                }
                $code .= $chr;
            }

            $users_telegram = TelegramUsers::findFirst("activation_code = '$code'");
            if (!$users_telegram){
                $unique = true;
            }
        }
        return $code;
    }
}