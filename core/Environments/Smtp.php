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
 */namespace FPanel\Environments;

use Exception;
use FPanel\Mailer\Mailer;
use FPanel\Mailer\MailerException;
use Phalcon\Mvc\User\Component;

class Smtp extends Component
{
    public $from = [];
    public $recipients = [];

    public $username = null;
    public $password = null;
    public $host = null;
    public $port = null;

    public $subject;
    public $message;
    public $secure = "tls";

    public $cc = [];

    public $is_html = true;
    public $connection_timeout = 3;
    private $error_message = [];

    public function send(): bool
    {
        $mail = new Mailer();
        try {
            $mail->SMTPDebug = 4;
            $mail->isSMTP();

            $mail->CharSet = 'UTF-8';

            $mail->SMTPAuth = true;
            if ($this->username == null) {
                $this->error_message = __("Mail account is empty");
                return false;
            }
            else {
                $mail->Username = $this->username;
                $mail->Password = $this->password;
                $mail->Host = $this->host;
                $mail->SMTPSecure = $this->secure;
                $mail->Port = $this->port;
                $mail->Timeout = $this->connection_timeout;
                $mail->SMTPAuth = true;
                foreach ($this->from as $from => $from_name) {
                    $mail->setFrom($from, $from_name);
                }
                $mail->isHTML($this->is_html);
            }

            foreach ($this->recipients as $recipient) {
                $mail->addAddress($recipient['address'], $recipient['name']);
            }

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => false
                )
            );

            $mail->SMTPAutoTLS = false;

            $mail->Subject = $this->subject;
            $mail->Body    = $this->message;
            try {
                if (!$mail->send()) {
                    $this->error_message["debugs"] = $mail->getDebugLogs();
                    $this->error_message["error"] = $mail->getErrorMessage();
                    return false;
                }
                else {
                    return true;
                }
            }
            catch (Exception $e) {
                $this->error_message["debugs"] = $mail->getDebugLogs();
                $this->error_message["error"] = $e->getMessage();
                return false;
            }
        }
        catch (MailerException $e) {
            $this->error_message["debugs"] = $mail->getDebugLogs();
            $this->error_message["error"] = $e->getTraceAsString();
            return false;
        }
    }

    public function getErrorMessage()
    {
        return $this->error_message;
    }
}