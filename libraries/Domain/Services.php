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
 */namespace FPanel\Domain;

use FPanel\Library\Library;
use FPanel\Models\DomainServices;

class Services extends Library
{
    public function create(): bool
    {
        $services = new DomainServices();
        $web = "0";
        $dns = "0";
        $mail = "0";
        if($this->getPost("web") == "on")
            $web = "1";
        if($this->getPost("dns") == "on")
            $dns = "1";
        if($this->getPost("mail") == "on")
            $mail = "1";

        $services->assign(array(
            'domain_id' => $this->getPost("domain_id"),
            'web'       => $web,
            'dns'       => $dns,
            'mail'      => $mail,
        ));
        if (!$services->create()) {
            foreach ($services->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DomainServices->create",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function delete($domain_id): bool
    {
        $services = DomainServices::find("domain_id = '$domain_id'");
        if(!empty($services)) {
            if(!$services->delete()) {
                foreach($services->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainServices->delete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
        return true;
    }

    public function checkDomainService($domain_id, $page, $redirect_url)
    {
        $services = DomainServices::findFirst("domain_id = '$domain_id'");
        if($services->$page == "0") {
            $this->response->redirect($redirect_url,true);
        }
    }
}