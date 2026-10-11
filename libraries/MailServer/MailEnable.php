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
trait MailEnable
{
    protected function saveMailEnableSettings($default, $server_id): bool
    {
        if($default == false) {
            if($this->mailenableSettingsValidation() == false) {
                return false;
            }
        }
        $data = [
            "hostname"              => $this->getPost("hostname"),
            "sub_port_status"       => $this->has("sub_port_status") ? $this->getPost("sub_port_status") : OFF,
            "sub_port"              => $this->getPost("sub_port"),
            "sub_port_auth_mode"    => $this->getPost("sub_port_auth_mode"),
            "max_msg_size_status"   => $this->has("max_msg_size_status") ? $this->getPost("max_msg_size_status") : OFF,
            "max_msg_size"          => $this->getPost("max_msg_size"),
            "smtpd_recipient_limit" => $this->getPost("smtpd_recipient_limit"),
            "smtp_in_connection_limit" => $this->getPost("smtp_in_connection_limit"),
            "smtp_out_connection_limit" => $this->getPost("smtp_out_connection_limit"),
            "max_queue_lifetime"    => $this->getPost("max_queue_lifetime"),
            "dns_rbl_status"        => $this->has("dns_rbl_status") ? $this->getPost("dns_rbl_status") : OFF,
            "dns_rbl_mode"          => $this->getPost("dns_rbl_mode"),
            "me_dns_black_lists"    => $this->getPost("me_dns_black_lists"),
            "url_rbl_status"        => $this->has("url_rbl_status") ? $this->getPost("url_rbl_status") : OFF,
            "url_rbl_mode"          => $this->getPost("url_rbl_mode"),
            "me_url_black_lists"    => $this->getPost("me_url_black_lists"),
            "spf_protection"        => $this->has("spf_protection") ? $this->getPost("spf_protection") : OFF,
            "spf_best_guess"        => $this->has("spf_best_guess") ? $this->getPost("spf_best_guess") : OFF,
            "spf_best_guesses"      => $this->getPost("spf_best_guesses"),
            "spf_white_lists_status" => $this->getPost("spf_white_lists_status"),
            "spf_white_lists"       => $this->getPost("spf_white_lists"),
            "spf_pass_local"        => $this->has("spf_pass_local") ? $this->getPost("spf_pass_local") : OFF,
        ];

        $mail_server_settings = MailServerSettings::findFirst("server_id = '$server_id'");
        if(!$mail_server_settings) {
            $mail_server_settings = new MailServerSettings();
        }
        $mail_server_settings->assign([
            "data"          => json_encode($data),
            'server_type'   => MAILENABLE,
            'server_id'     => $server_id,
        ]);

        if(!$mail_server_settings->save()) {
            foreach ($mail_server_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "MailEnable->saveMailEnableSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        if($this->getPost("spam_filter_status") == ON) {
            $spam_status = ACTIVE;
        }
        else {
            $spam_status = PASSIVE;
        }

        $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");
        if($spam_settings) {
            $spam_settings->status = $spam_status;
            if(!$spam_settings->update()) {
                foreach ($spam_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "MailEnable->saveMailEnableSettings SpamSettings",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    private function mailenableSettingsValidation(): bool
    {
        $spf_check_mode_list = array('only_spf_header','dns_errors','spf_fail','spf_softfail','spf_neutral','spf_not_solved');

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
            ->Numeric($this->getPost("concurrency_in_que"),'concurrency_in_que')
            ->Numeric($this->getPost("max_recipients"),'max_recipients');

        if($this->getPost("spf_protection") == ON) {
            $this->validation->InclusionIn($this->getPost("spf_check_mode"),'spf_check_mode', $spf_check_mode_list,__('Please select SPF Check Mode'));
        }
        if($this->getPost("rbl_status") == ON) {
            $this->validation->Domain($this->getPost("hostname"),'hostname');
        }
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    protected function mailenableCommand($server_id)
    {
        $mailserver = new MailServer();
        $mailserver->setServerId($server_id);

        $params = array(
            "hostname"             => $this->getPost("hostname"),

            'sub-port-status'      => OFF,
            'sub-port'             => $this->getPost("sub_port"),
            'sub-port-auth-mode'   => $this->getPost("sub_port_auth_mode"),
            'max-msg-size-status'  => OFF,

            'databytes'         => $this->getPost("max_msg_size")*1024*1024,
            'max-rcpt'          => $this->getPost("smtpd_recipient_limit"),
            'conn-in'           => $this->getPost("smtp_in_connection_limit"),
            'conn-out'          => $this->getPost("smtp_out_connection_limit"),
            'lifetime'          => $this->getPost("max_queue_lifetime"),

            'dns-rbl-status'    => OFF,
            "dns-rbl-mode"      => $this->getPost("dns_rbl_mode"),
            "me-dns-blacklist"  => $this->has("me_dns_black_lists") && $this->getPost("me_dns_black_lists") != null ? implode(",", $this->getPost("me_dns_black_lists")) : "",

            "url-rbl-status"    => OFF,
            "url-rbl-mode"      => $this->getPost("url_rbl_mode"),
            "me-url-blacklist"  => $this->has("me_url_black_lists") && $this->getPost("me_url_black_lists") != null ? implode(",", $this->getPost("me_url_black_lists")) : "",

            "enable-spam"       => OFF,
            "spf"               => OFF,
            'spf-best-guess'    => OFF,
            'spf-best-guesses'  => $this->getPost("spf_best_guesses"),
            'spf-pass-local'    => OFF,
            'spf-white-lists'   => $this->getPost("spf_white_lists"),
            'spf-white-lists-status'  => OFF,
        );

        if($this->getPost("spam_filter_status") == ON) {
            $params['enable-spam'] = ON;
        }

        if($this->getPost("sub_port_status") == ON) {
            $params['sub-port-status'] = ON;
        }

        if($this->getPost("dns_rbl_status") == ON) {
            $params['dns-rbl-status'] = ON;
        }

        if($this->getPost("url_rbl_status") == ON) {
            $params['url-rbl-status'] = ON;
        }

        if($this->getPost("spf_protection") == ON) {
            $params['spf'] = ON;
        }
        if($this->getPost("spf_best_guess") == ON) {
            $params['spf-best-guess'] = ON;
        }
        if($this->getPost("spf_white_lists_status") == ON) {
            $params['spf-white-lists-status'] = ON;
        }
        if($this->getPost("spf_pass_local") == ON) {
            $params['spf-pass-local'] = ON;
        }
        if($this->getPost("max_msg_size_status") == ON) {
            $params['max-msg-size-status'] = ON;
        }

        $mailserver->init($params);
    }
}
