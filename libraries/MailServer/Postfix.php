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
 */namespace FPanel\MailServer;

use FPanel\Models\MailServerSettings;
use FPanel\Models\SpamSettings;
use FPanel\Process\MailServer;

/**
 * Class Mail
 * @package FPanel\Server
 */
trait Postfix
{
    protected function savePostfixSettings(bool $default, $server_id): bool
    {
        if($default == false) {
            if($this->postfixSettingsValidation() == false) {
                return false;
            }
        }
        $data = [
            'hostname'              => $this->getPost("hostname"),
            'max_mail_size'         => $this->getPost("max_message_size"),
            'queue_lifetime'        => $this->getPost("queue_life_time"),
            'bounce_queue_lifetime' => $this->getPost("bounce_queue_lifetime"),
            'policyd_spy_timelimit' => $this->getPost("policyd_spy_timelimit"),
            'conn_incoming_queue'   => $this->getPost("concurrency_in_que"),
            'conn_outgoing_queue'   => $this->getPost("conn_outgoing_queue"),
            'max_recipients'        => $this->getPost("max_recipients"),
            'enable_sub_port'       => $this->getPost("submission_port", OFF),
            'del_doublebounce_msg'  => $this->getPost("delete_double_msg", OFF),
            'pop_imap_with_smtp'    => $this->getPost("imap_auth_with_smtp", OFF),
            'smtp_auth'             => $this->getPost("smtp_auth", OFF),
            'spf_protection'        => $this->getPost("spf_protection", OFF),
            'rbl_status'            => $this->getPost("rbl_status", OFF),
            'rbl_list'              => $this->getPost("rbl_list"),
        ];

        $mail_server_settings = new MailServerSettings();
        if(!$default) {
            $mail_server_settings = MailServerSettings::findFirst("server_id = '$server_id'");
        }

        $mail_server_settings->assign([
            "data"          => json_encode($data),
            'server_type'   => POSTFIX,
            'server_id'     => $this->getPost("server_id"),
        ]);

        if(!$mail_server_settings->save()) {
            foreach ($mail_server_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Postfix->savePostfixSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        if($this->getPost("spam_filter_status", OFF) == ON) {
            $spam_status = ACTIVE;
        }
        else {
            $spam_status = PASSIVE;
        }

        $spam_settings = SpamSettings::findFirst("server_id = '" . $this->getPost("server_id") . "'");
        if($spam_settings) {
            $spam_settings->status = $spam_status;
            if(!$spam_settings->update()) {
                foreach ($spam_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Postfix->savePostfixSettings SpamSettings",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    protected function postfixSettingsValidation(): bool
    {
        $required = array(
            array(
                'field' => 'max_message_size',
                'item' => $this->getPost("max_message_size"),
                'message' => __('Max message size is required')
            ),
            array(
                'field' => 'queue_life_time',
                'item' => $this->getPost("queue_life_time"),
                'message' => __('Queue Life Time is required')
            ),
            array(
                'field' => 'bounce_queue_lifetime',
                'item' => $this->getPost("bounce_queue_lifetime"),
                'message' => __('Queue Life Time is required')
            ),
            array(
                'field' => 'policyd_spy_timelimit',
                'item' => $this->getPost("policyd_spy_timelimit"),
                'message' => __('Policyd Spf Timelimit is required')
            ),
            array(
                'field' => 'concurrency_in_que',
                'item' => $this->getPost("concurrency_in_que"),
                'message' => __('Concurrencry Incoming Queue is required')
            ),
            array(
                'field' => 'max_recipients',
                'item' => $this->getPost("max_recipients"),
                'message' => __('Max. Recipients is required')
            ),
        );

        $this->validation->Required($required)
            ->Numeric($this->getPost("max_message_size"),'max_message_size')
            ->Numeric($this->getPost("queue_life_time"),'queue_life_time')
            ->Numeric($this->getPost("bounce_queue_lifetime"),'bounce_queue_lifetime')
            ->Numeric($this->getPost("concurrency_in_que"),'concurrency_in_que')
            ->Numeric($this->getPost("max_recipients"),'max_recipients');

        if($this->getPost("rbl_status") == ON) {
            $this->validation->Domain($this->getPost("hostname"),'hostname');
        }
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    protected function postfixCommand($server_id)
    {
        $mailserver = new MailServer();
        $mailserver->setServerId($server_id);
        $params = array(
            'message-size-limit'        => $this->getPost("max_message_size"),
            'maximal-queue-lifetime'    => $this->getPost("queue_life_time"),
            'smtpd-client-connection-count-limit' => $this->getPost("concurrency_in_que"),
            'smtpd-recipient-limit'  => $this->getPost("max_recipients"),
            'bounce-queue-lifetime'  => $this->getPost("bounce_queue_lifetime"),
            'policyd-spf-timelimit'  => $this->getPost("policyd_spy_timelimit"),
            'mf-check'          => OFF,
            'soft-bounce'       => OFF,
            'smtp-auth'         => OFF,
            'pop-smtp-auth'     => OFF,
            'sub-port'          => OFF,
            'spf'               => OFF,
            'enable-spam'       => OFF,
        );

        if($this->getPost("spam_filter_status") == ON) {
            $params['enable-spam'] = ON;
        }

        // Submission Port
        if($this->getPost("submission_port") == ON) {
            $params['sub-port'] = ON;
        }

        // POP SMTP Auth
        if($this->getPost("imap_auth_with_smtp") == ON) {
            $params['pop-smtp-auth'] = ON;
        }

        // SMTP Auth
        if($this->getPost("smtp_auth") == ON) {
            $params['smtp-auth'] = ON;
        }

        // Delete soft-bounce Message
        if($this->getPost("delete_double_msg") == ON) {
            $params['soft-bounce'] = ON;
        }


        if($this->getPost("spf_protection") == ON) {
            $params['spf'] = ON;
        }

        if($this->getPost("rbl_status") == ON) {
            $params['rbl'] = ON;
            $rbl_list = "";
            if(!empty($this->getPost("rbl_list"))) {
                foreach($this->getPost("rbl_list") as $rbl) {
                    if($rbl_list == null)
                        $rbl_list = $rbl;
                    else
                        $rbl_list = $rbl_list . ','. $rbl;
                }
            }
            if($rbl_list != null)
                $params['rbl-list'] = $rbl_list;
        }
        else {
            $params['rbl'] = OFF;
        }

        $params['hostname'] = $this->getPost("hostname");

        $mailserver->init($params);
    }
}
