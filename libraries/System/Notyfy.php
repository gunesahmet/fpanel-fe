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

use FPanel\Environments\Smtp;
use FPanel\Library\Library;
use FPanel\Models\SystemSettings;

/**
 * Class Notyfy
 * @package FPanel\System
 */
class Notyfy extends Library
{
    public function save(): bool
    {
        if($this->validate() == false)
            return false;

        if($this->checkHost() == false)
            return false;

        if($this->sendTestMail() == false)
            return false;

        $params = array(
            'host' => $this->getPost("host"),
            'port' => $this->getPost("port"),
            'secure' => $this->getPost("secure"),
            'from_fullname' => $this->getPost("from_fullname"),
            'email' => $this->getPost("email"),
            'password' => $this->crypto->bencode($this->getPost("password")),
        );

        $system_settings = SystemSettings::findFirst("key ='notyfy'");
        if($system_settings) {
            $assign = array(
                'value' => json_encode($params),
            );
            $system_settings->assign($assign);
            if(!$system_settings->update()) {
                foreach ($system_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Notyfy->save",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        $this->error_messages['content'] = __('Settings not found');
        return false;
    }

    public function setDefault(): bool
    {
        $params = array(
            'host' => '',
            'port' => '',
            'from_fullname' => '',
            'email' => '',
            'password' => '',
            'status' => 'off',
        );

        $assign = array(
            'key' => 'notyfy',
            'value' => json_encode($params),
        );

        $system_settings = new SystemSettings();
        $system_settings->assign($assign);
        if(!$system_settings->create()) {
            foreach ($system_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Notyfy->setDefault",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        return true;
    }

    private function validate(): bool
    {
        $required = array();
        $required[] = array(
            'item' => $this->getPost("host"),
            'field' => 'host',
            'message' => __('Please enter a host')
        );
        $required[] = array(
            'item' => $this->getPost("from_fullname"),
            'field' => 'from_fullname',
            'message' => __('Please enter a from name')
        );
        $required[] = array(
            'item' => $this->getPost("port"),
            'field' => 'port',
            'message' => __('Please enter a port')
        );
        $required[] = array(
            'item' => $this->getPost("email"),
            'field' => 'email',
            'message' => __('Please enter an email address')
        );
        $required[] = array(
            'item' => $this->getPost("password"),
            'field' => 'password',
            'message' => __('Please enter a password')
        );
        $this->validation->Required($required)
            ->Password($this->getPost("password"),'password')
            ->Email($this->getPost("email"),'email')
            ->IpOrDomain($this->getPost("host"),'host')
            ->Port($this->getPost("port"),'port');

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        else {
            return true;
        }
    }

    public function sendTestMail(): bool
    {
        $smtp = new Smtp();

        $smtp->username = $this->getPost("email");
        $smtp->password = $this->getPost("password");
        $smtp->host = $this->getPost("host");
        $smtp->port = $this->getPost("port");
        $smtp->secure = $this->getPost("secure") == "none" ? "" : $this->getPost("secure") ;

        $smtp->from = [
            $this->getPost("email") => $this->getPost("from_fullname")
        ];
        $smtp->recipients[] = [
            "address" => $this->getPost("email"),
            "name" => "FPanel System",
        ];

        $smtp->subject = "System Message";
        $smtp->message = $this->emailTemplate();

        $smtp->is_html = true;
        $smtp->connection_timeout = 5;
        $status = $smtp->send();

        if(!$status) {
            $this->error_messages['content'] = __('Connection is refused please check your configurations');
            $this->debug_messages = $smtp->getErrorMessage();
            return false;
        }
        return true;

    }

    private function emailTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head><meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>System Message</title>
<style>
*{margin:0;padding:0;font-family:"Helvetica Neue",Helvetica,Helvetica,Arial,sans-serif}body{-webkit-font-smoothing:antialiased;-webkit-text-size-adjust:none;width:100%!important;height:100%}p.callout{padding:15px;background-color:#ECF8FF;margin-bottom:15px}.callout a{font-weight:700;color:#2BA6CB}table.head-wrap{width:100%}.header.container table td.logo{padding:15px}.header.container table td.label{padding:15px 15px 15px 0}table.body-wrap{width:100%}h1,h2,h3,h4,h5,h6{font-family:HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;line-height:1.1;margin-bottom:15px;color:#000}h3{font-weight:500;font-size:27px}.collapse{margin:0!important}p,ul{margin-bottom:10px;font-weight:400;font-size:14px;line-height:1.6}p.lead{font-size:17px}p.last{margin-bottom:0}.container{display:block!important;max-width:600px!important;margin:0 auto!important;clear:both!important}.content{padding:15px;max-width:600px;margin:0 auto;display:block}.content table{width:100%}.clear{display:block;clear:both}@media only screen and (max-width:600px){a[class=btn]{display:block!important;margin-bottom:10px!important;background-image:none!important;margin-right:0!important}div[class=column]{width:auto!important;float:none!important}table.social div[class=column]{width:auto!important}}
</style>
</head>
<body bgcolor="#FFFFFF">
<table class="head-wrap" bgcolor="#999999">
<tr>
<td class="header container">
<div class="content">
<table bgcolor="#999999">
<tr>
</tr>
</table>
</div>
</td>
</tr>
</table>
<table class="body-wrap">
<tr>
<td class="container" bgcolor="#FFFFFF">
<div class="content">
<table>
<tr>
<td>
<h3>System Message Test Mail</h3>
<p>This is your notification test mail. Your configuration is successful.</p>
</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
</body>
</html>';
    }

    public function checkHost(): bool
    {
        try {
            set_error_handler(function () {});
            $fp = @fsockopen($this->getPost("host"), $this->getPost("port"), $errCode, $errStr,5);
            if (!$fp) {
                $this->error_messages[] =  __('No mail server responded on this server on port, validate your configuration');
                $this->log->error([
                    "File" => "Notyfy => connection",
                    "Error" => "No mail server responded on this server on port, validate your configuration",
                ]);
                return false;
            }
            fclose($fp);
        }
        finally {
            restore_error_handler();
        }
        return true;
    }
}