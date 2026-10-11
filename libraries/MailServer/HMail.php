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
trait HMail
{
    protected function saveHmailSettings($default, $server_id): bool
    {
        if($default == false) {
            if($this->hmailSettingsValidation() == false) {
                return false;
            }
        }
        $smtp_relayer_security = "none";
        if($this->getPost("smtp_relayer_security", "none") == "eCSNone") {
            $smtp_relayer_security = "none";
        }
        else if($this->getPost("smtp_relayer_security", "none") == "eCSTLS") {
            $smtp_relayer_security = "ssl";
        }
        else if($this->getPost("smtp_relayer_security", "none") == "eCSSTARTTLSRequired") {
            $smtp_relayer_security = "starttls_req";
        }
        else if($this->getPost("smtp_relayer_security", "none") == "eCSSTARTTLSOptional") {
            $smtp_relayer_security = "starttls_opt";
        }
        $data = [
            "max_smtp_threads" => $this->getPost("max_smtp_threads"),
            "max_msg_size" => $this->getPost("max_msg_size"),

            "sub_port_status" => $this->getPost("sub_port_status", OFF),
            "sub_port" => $this->getPost("sub_port"),

            "smtp_no_tries" => $this->getPost("smtp_no_tries"),
            "smtp_tries_min" => $this->getPost("smtp_tries_min"),
            "smtp_relayer" => $this->getPost("smtp_relayer"),
            "smtp_relayer_port" => $this->getPost("smtp_relayer_port"),
            "smtp_relayer_auth" => $this->getPost("smtp_relayer_auth", OFF),
            "smtp_relayer_user" => $this->getPost("smtp_relayer_user"),
            "smtp_relayer_passwd" => $this->getPost("smtp_relayer_passwd"),
            "smtp_relayer_security" => $smtp_relayer_security,
            "smtp_auth_plain" => $this->getPost("smtp_auth_plain", OFF),
            "allow_empty_sender" => $this->getPost("allow_empty_sender", OFF),
            "allow_invalid_end" => $this->getPost("allow_invalid_end", OFF),
            "dis_invalid_client" => $this->getPost("dis_invalid_client", OFF),
            "max_inv_command" => $this->getPost("max_inv_command"),
            "delivery_bind_to_ip" => $this->getPost("delivery_bind_to_ip"),
            "max_rec_batch" => $this->getPost("max_rec_batch"),
            "add_del_head" => $this->getPost("add_del_head", OFF),
            "rule_loop_limit" => $this->getPost("rule_loop_limit"),
            "max_mx_host" => $this->getPost("max_mx_host"),
            "hostname" => $this->getPost("hostname"),
            "max_pop_threads" => $this->getPost("max_pop_threads"),
            "max_imap_threads" => $this->getPost("max_imap_threads"),
            "spam_mark_threshold" => $this->getPost("spam_mark_threshold"),
            "add_header_spam" => $this->getPost("add_header_spam", OFF),
            "add_reason_spam" => $this->getPost("add_reason_spam", OFF),
            "add_msg_subj" => $this->getPost("add_msg_subj", OFF),
            "add_msg_subj_txt" => $this->getPost("add_msg_subj_txt"),
            "spam_del_threshold" => $this->getPost("spam_del_threshold"),
            "spam_max_msg_size" => $this->getPost("spam_max_msg_size"),
            "use_spf" => $this->getPost("use_spf", OFF),
            "use_spf_score" => $this->getPost("use_spf_score"),
            "check_host_helo" => $this->getPost("check_host_helo", OFF),
            "check_host_helo_score" => $this->getPost("check_host_helo_score"),
            "use_mx_checks" => $this->getPost("use_mx_checks", OFF),
            "use_mx_checks_score" => $this->getPost("use_mx_checks_score"),
            "verify_dkim" => $this->getPost("verify_dkim", OFF),
            "verify_dkim_score" => $this->getPost("verify_dkim_score"),
        ];

        $mail_server_settings = MailServerSettings::findFirst("server_id = '$server_id'");
        if(!$mail_server_settings) {
            $mail_server_settings = new MailServerSettings();
        }
        $mail_server_settings->assign([
            "data"          => json_encode($data),
            'server_type'   => HMAIL,
            'server_id'     => $server_id,
        ]);

        if(!$mail_server_settings->save()) {
            foreach ($mail_server_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "HMail->saveHmailSettings",
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
                    "Module" => "HMail->saveHmailSettings SpamSettings",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    protected function hmailSettingsValidation(): bool
    {
        $required = array(
            array(
                'field' => 'max_smtp_threads',
                'item' => $this->getPost("max_smtp_threads"),
                'message' => __("Maximum Number of Simultaneous Connections")
            ),
            array(
                'field' => 'max_msg_size',
                'item' => $this->getPost("max_msg_size"),
                'message' => __("SMTP Maximum Message Size")
            ),
            array(
                "field" => "smtp_no_tries",
                "item" => $this->getPost("smtp_no_tries"),
                "message" => __("Number of retries required")
            ),
            array(
                "field" => "smtp_tries_min",
                "item" => $this->getPost("smtp_tries_min"),
                "message" => __("Minutes between every retry required")
            ),
            array(
                "field" => "hostname",
                "item" => $this->getPost("hostname"),
                "message" => __("Hostname is required")
            ),
            array(
                "field" => "max_rec_batch",
                "item" => $this->getPost("max_rec_batch"),
                "message" => __("Maximum number of recipients in batch is required")
            ),
            array(
                "field" => "max_mx_host",
                "item" => $this->getPost("max_mx_host"),
                "message" => __("Maximum number of recipient hosts is required")
            ),
            array(
                "field" => "rule_loop_limit",
                "item" => $this->getPost("rule_loop_limit"),
                "message" => __("Rule Loop Limit is required")
            ),
            array(
                "field" => "max_pop_threads",
                "item" => $this->getPost("max_pop_threads"),
                "message" => __("POP3 simultaneous connections is required")
            ),
            array(
                "field" => "max_imap_threads",
                "item" => $this->getPost("max_imap_threads"),
                "message" => __("IMAP simultaneous connections is required")
            ),
            array(
                "field" => "spam_mark_threshold",
                "item" => $this->getPost("spam_mark_threshold"),
                "message" => __("Spam Mark Threshold is required")
            ),
            array(
                "field" => "spam_del_threshold",
                "item" => $this->getPost("spam_del_threshold"),
                "message" => __("Spam Delete Threshold is required")
            ),
            array(
                "field" => "spam_max_msg_size",
                "item" => $this->getPost("spam_max_msg_size"),
                "message" => __("Maximum Message Size to Scan is required")
            ),
        );

        if($this->getPost("sub_port_status") == ON) {
            $required[] = [
                "field" => "sub_port",
                "item" => $this->getPost("sub_port"),
                "message" => __("Submission Port required")
            ];
            $this->validation->Port($this->getPost("sub_port"),'sub_port');
        }
        if($this->getPost("use_spf") == ON) {
            $required[] = [
                "field" => "use_spf_score",
                "item" => $this->getPost("use_spf_score"),
                "message" => __("SPF Score is required")
            ];
            $this->validation->Numeric($this->getPost("use_spf_score"),'use_spf_score');
        }
        if($this->getPost("check_host_helo") == ON) {
            $required[] = [
                "field" => "check_host_helo_score",
                "item" => $this->getPost("check_host_helo_score"),
                "message" => __("Check Helo Score is required")
            ];
            $this->validation->Numeric($this->getPost("check_host_helo_score"),'check_host_helo_score');
        }
        if($this->getPost("use_mx_checks") == ON) {
            $required[] = [
                "field" => "use_mx_checks_score",
                "item" => $this->getPost("use_mx_checks_score"),
                "message" => __("Check DNS-MX Score is required")
            ];
            $this->validation->Numeric($this->getPost("use_mx_checks_score"),'use_mx_checks_score');
        }
        if($this->getPost("verify_dkim") == ON) {
            $required[] = [
                "field" => "verify_dkim_score",
                "item" => $this->getPost("verify_dkim_score"),
                "message" => __("Verify DKIM Score is required")
            ];
            $this->validation->Numeric($this->getPost("verify_dkim_score"),'verify_dkim_score');
        }
        if($this->getPost("add_msg_subj") == ON) {
            $required[] = [
                "field" => "add_msg_subj_txt",
                "item" => $this->getPost("add_msg_subj_txt"),
                "message" => __("Spam Message Subject Text is required")
            ];
            $this->validation->MinMaxStr($this->getPost("add_msg_subj_txt"), "add_msg_subj_txt", __("Please enter Subject Text min. %min% or max. %max% characters", [ "min" => 4, "max" => 255]));
        }
        if($this->getPost("smtp_relayer_auth") == ON) {
            $required[] = [
                "field" => "smtp_relayer_user",
                "item" => $this->getPost("smtp_relayer_user"),
                "message" => __("SMTP Relayer Username is required")
            ];
            $required[] = [
                "field" => "smtp_relayer_passwd",
                "item" => $this->getPost("smtp_relayer_passwd"),
                "message" => __("SMTP Relayer Password is required")
            ];
            $this->validation->InclusionIn($this->getPost("smtp_relayer_security"),'smtp_relayer_security', ["none", "ssl", "starttls_req", "starttls_opt"], __('Please select SPF Check Mode'));
        }
        if($this->getPost("dis_invalid_client") == ON) {
            $required[] = [
                "field" => "max_inv_command",
                "item" => $this->getPost("max_inv_command"),
                "message" => __("Maximum number of invalid commands is required")
            ];
            $this->validation->Numeric($this->getPost("max_inv_command"),'max_inv_command');
        }

        $this->validation->Required($required);

        $this->validation->Numeric($this->getPost("spam_max_msg_size"),'spam_max_msg_size');
        $this->validation->Numeric($this->getPost("spam_del_threshold"),'spam_del_threshold');
        $this->validation->Numeric($this->getPost("spam_mark_threshold"),'spam_mark_threshold');
        $this->validation->Numeric($this->getPost("max_imap_threads"),'max_imap_threads');
        $this->validation->Numeric($this->getPost("max_pop_threads"),'max_pop_threads');
        $this->validation->Numeric($this->getPost("max_mx_host"),'max_mx_host');
        $this->validation->Numeric($this->getPost("rule_loop_limit"),'rule_loop_limit');
        $this->validation->Numeric($this->getPost("max_rec_batch"),'max_rec_batch');
        $this->validation->Numeric($this->getPost("smtp_tries_min"),'smtp_tries_min');
        $this->validation->Numeric($this->getPost("smtp_no_tries"),'smtp_no_tries');
        $this->validation->Numeric($this->getPost("max_msg_size"),'max_msg_size');
        $this->validation->Numeric($this->getPost("max_smtp_threads"),'max_smtp_threads');
        $this->validation->Domain($this->getPost("hostname"),'hostname');

        if($this->has("smtp_relayer_port") && ($this->getPost("smtp_relayer_port") != null)) {
            $this->validation->Port($this->getPost("smtp_relayer_port"),'smtp_relayer_port');
        }
        if($this->has("smtp_relayer") && ($this->getPost("smtp_relayer") != null)) {
            $this->validation->IpOrDomain($this->getPost("smtp_relayer"),'smtp_relayer');
        }

        if($this->has("delivery_bind_to_ip") && ($this->getPost("delivery_bind_to_ip") != null)) {
            $this->validation->Ip($this->getPost("delivery_bind_to_ip"),'delivery_bind_to_ip');
        }

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    protected function hmailCommand($server_id)
    {
        $mailserver = new MailServer();
        $mailserver->setServerId($server_id);
        $params = [
            "max-smtp-threads" => $this->getPost("max_smtp_threads", "0"),
            "databytes" => (int) $this->getPost("max_msg_size", "20") * 1024,

            "sub-port-status" => $this->getPost("sub_port_status", OFF),
            "sub-port" => $this->getPost("sub_port"),

            "smtp-no-tries" => $this->getPost("smtp_no_tries"),
            "smtp-tries-min" => $this->getPost("smtp_tries_min"),

            "smtp-relayer" => $this->getPost("smtp_relayer"),
            "smtp-relayer-port" => $this->getPost("smtp_relayer_port"),
            "smtp-relayer-auth" => $this->getPost("smtp_relayer_auth", OFF),
            "smtp-relayer-user" => $this->getPost("smtp_relayer_user"),
            "smtp-relayer-passwd" => $this->getPost("smtp_relayer_passwd"),
            "smtp-relayer-security" => $this->getPost("smtp_relayer_security"),

            "smtp-auth-plain" => $this->getPost("smtp_auth_plain", OFF),
            "allow-empty-sender" => $this->getPost("allow_empty_sender", OFF),
            "allow-invalid-end" => $this->getPost("allow_invalid_end", OFF),
            "dis-invalid-client" => $this->getPost("dis_invalid_client", OFF),
            "max-inv-command" => $this->getPost("max_inv_command"),

            "delivery-bind-to-ip" => $this->getPost("delivery_bind_to_ip"),
            "max-rec-batch" => $this->getPost("max_rec_batch"),
            "add-del-head" => $this->getPost("add_del_head", OFF),
            "rule-loop-limit" => $this->getPost("rule_loop_limit"),
            "max-mx-host" => $this->getPost("max_mx_host"),

            "hostname" => $this->getPost("hostname"),

            "max-pop-threads" => $this->getPost("max_pop_threads"),
            "max-imap-threads" => $this->getPost("max_imap_threads"),

            "spam-mark-threshold" => $this->getPost("spam_mark_threshold"),
            "add-header-spam" => $this->getPost("add_header_spam", OFF),
            "add-reason-spam" => $this->getPost("add_reason_spam", OFF),
            "add-msg-subj" => $this->getPost("add_msg_subj", OFF),
            "add-msg-subj-txt" => $this->getPost("add_msg_subj_txt"),
            "spam-del-threshold" => $this->getPost("spam_del_threshold"),
            "spam-max-msg-size" => (int) $this->getPost("spam_max_msg_size") * 1024,

            "use-spf" => $this->getPost("use_spf", OFF),
            "use-spf-score" => $this->getPost("use_spf_score"),

            "check-host-helo" => $this->getPost("check_host_helo", OFF),
            "check-host-helo-score" => $this->getPost("check_host_helo_score"),

            "use-mx-checks" => $this->getPost("use_mx_checks", OFF),
            "use-mx-checks-score" => $this->getPost("use_mx_checks_score"),

            "verify-dkim" => $this->getPost("verify_dkim", OFF),
            "verify-dkim-score" => $this->getPost("verify_dkim_score"),
        ];

        $mailserver->init($params);
    }
}
