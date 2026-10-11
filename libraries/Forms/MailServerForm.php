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
 */namespace FPanel\Forms;

use FPanel\Models\SystemPackages;
use FPanel\Process\MailServer;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class MailServerForm
 * @package FPanel\Forms
 */
class MailServerForm extends Form
{
    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        if($options['part'] == "general") {
            $this->generalSettingsElements($options);
        }
        else if($options['part'] == "spam") {
            $this->spamSettingsElements($options);
        }
        else if($options['part'] == "blacklist") {
            $this->blacklistSettingsElements($options);
        }
        else if($options['part'] == "whitelist") {
            $this->whitelistSettingsElements($options);
        }
        else if($options['part'] == "policyd") {
            $this->policydSettingsElements($options);
        }
    }

    /**
     * @param null $options
     */
    private function generalSettingsElements($options = null)
    {
        if($options["server_type"] == POSTFIX) {
            $this->postfixSettingsElements($options);
        }
        else if($options["server_type"] == MAILENABLE) {
            $this->mailenableSettingsElements($options);
        }
        else if ($options["server_type"] == HMAIL) {
            $this->hmailSettingsElements($options);
        }
    }

    private function postfixSettingsElements($options = null)
    {
        $mail_server_settings = $options['mail_server_data'];

        // Max Msg Size
        $this->add(new Text('max_message_size', array(
            'id' => 'max_message_size',
            'name' => 'max_message_size',
            'value' => $mail_server_settings['max_mail_size'],
        )));

        // Waiting Time on Queue
        $this->add(new Text('queue_life_time', array(
            'id' => 'queue_life_time',
            'name' => 'queue_life_time',
            'value' => $mail_server_settings['queue_lifetime'],
        )));

        // Bounce on Queue
        $this->add(new Text('bounce_queue_lifetime', array(
            'id' => 'bounce_queue_lifetime',
            'name' => 'bounce_queue_lifetime',
            'value' => $mail_server_settings['bounce_queue_lifetime'],
        )));

        // Bounce on Queue
        $this->add(new Text('policyd_spy_timelimit', array(
            'id' => 'policyd_spy_timelimit',
            'name' => 'policyd_spy_timelimit',
            'value' => $mail_server_settings['policyd_spy_timelimit'],
        )));

        // Concurrency Incoming Queue
        $this->add(new Text('concurrency_in_que', array(
            'id' => 'concurrency_in_que',
            'name' => 'concurrency_in_que',
            'value' => $mail_server_settings['conn_incoming_queue'],
        )));


        // Maximum Recipients
        $this->add(new Text('max_recipients', array(
            'id' => 'max_recipients',
            'name' => 'max_recipients',
            'value' => $mail_server_settings['max_recipients'],
        )));

        // Enable Submission Port
        $submission_port = new Check('submission_port', array(
            'id' => 'submission_port',
            'name' => 'submission_port',
        ));

        // Delete DoubleBounce Messages
        $delete_double_msg = new Check('delete_double_msg', array(
            'id' => 'delete_double_msg',
            'name' => 'delete_double_msg',
        ));


        // Enable Pop3/Imap Authentication with SMTP Auth
        $imap_auth_with_smtp = new Check('imap_auth_with_smtp', array(
            'id' => 'imap_auth_with_smtp',
            'name' => 'imap_auth_with_smtp',
        ));

        // Enable SMTP Authentication
        $smtp_auth = new Check('smtp_auth', array(
            'id' => 'smtp_auth',
            'name' => 'smtp_auth',
        ));

        $spam_filter_status = new Check('spam_filter_status', array(
            'id' => 'spam_filter_status',
            'name' => 'spam_filter_status',
        ));

        // Enable SPF Spam Protection
        $spf_protection = new Check('spf_protection', array(
            'id' => 'spf_protection',
            'name' => 'spf_protection',
        ));

        if($mail_server_settings['enable_sub_port'] == "on") {
            $submission_port->setAttribute('checked','checked');
        }
        if($mail_server_settings['del_doublebounce_msg'] == "on") {
            $delete_double_msg->setAttribute('checked','checked');
        }
        if($mail_server_settings['pop_imap_with_smtp'] == "on") {
            $imap_auth_with_smtp->setAttribute('checked','checked');
        }
        if($mail_server_settings['smtp_auth'] == "on") {
            $smtp_auth->setAttribute('checked','checked');
        }
        if($mail_server_settings['spf_protection'] == "on") {
            $spf_protection->setAttribute('checked','checked');
        }
        if($mail_server_settings['spam_status'] == "1") {
            $spam_filter_status->setAttribute('checked','checked');
        }

        $rbl_status = new Check('rbl_status', array(
            'id' => 'rbl_status',
            'name' => 'rbl_status',
        ));

        if($mail_server_settings['rbl_status'] == "1") {
            $rbl_status->setAttribute('checked','checked');
        }
        $this->add(new Text('hostname', array(
            'id' => 'hostname',
            'name' => 'hostname',
            'value' => $mail_server_settings['hostname'],
        )));
        $this->add(new Text('add_rbl_list', array(
            'id' => 'add_rbl_list',
            'name' => 'add_rbl_list',
        )));
        $rbl_lists = array();
        if($mail_server_settings['rbl_list'] != null) {
            $rbl_list = explode(',', $mail_server_settings['rbl_list']);
            foreach($rbl_list as $rbl) {
                $rbl_lists[$rbl] = $rbl;
            }
        }
        $this->add(new Select('rbl_list', $rbl_lists, array(
            'id' => 'rbl_list',
            'name' => 'rbl_list[]',
            'multiple' => 'multiple'
        )));
        $this->add($rbl_status);
        $this->add($submission_port);
        $this->add($delete_double_msg);
        $this->add($imap_auth_with_smtp);
        $this->add($smtp_auth);
        $this->add($spf_protection);
        $this->add($spam_filter_status);
    }

    private function mailenableSettingsElements($options)
    {
        $packages = SystemPackages::find("package_name LIKE '%mailenable%'");
        $mailenable_pro = false;
        foreach ($packages as  $package) {
            if((strpos($package->package_name, "Professional") !== false) || (strpos($package->package_name, "Enterprise") !== false) || (strpos($package->package_name, "Premium") !== false)) {
                if($package->status == INSTALLED) {
                    $mailenable_pro = true;
                }
            }
        }
        $mail_server_settings = $options['mail_server_data'];
        $mailsrvmng = new MailServer();
        $available_black_lists_str = $mailsrvmng->getMailEnableBlacklists();
        $available_black_lists_arr = explode(",", trim($available_black_lists_str));
        $blacklists = [];
        foreach ($available_black_lists_arr as $item) {
            $blacklists[$item] = $item;
        }
        $hostname = new Text('hostname', array(
            'id' => 'hostname',
            'name' => 'hostname',
            "value" => $mail_server_settings['hostname']
        ));
        $spam_filter_status = new Check('spam_filter_status', array(
            'id' => 'spam_filter_status',
            'name' => 'spam_filter_status',
        ));
        if($mail_server_settings['spam_status'] == "1") {
            $spam_filter_status->setAttribute('checked','checked');
        }

        $spf_protection = new Check('spf_protection', array(
            'id' => 'spf_protection',
            'name' => 'spf_protection',
        ));

        $spf_best_guess = new Check('spf_best_guess', array(
            'id' => 'spf_best_guess',
            'name' => 'spf_best_guess',
        ));
        $spf_best_guesses = new Text('spf_best_guesses', array(
            'id' => 'spf_best_guesses',
            'name' => 'spf_best_guesses',
        ));

        $spf_white_lists_status = new Check('spf_white_lists_status', array(
            'id' => 'spf_white_lists_status',
            'name' => 'spf_white_lists_status',
        ));
        $spf_white_lists = new Text('spf_white_lists', array(
            'id' => 'spf_white_lists',
            'name' => 'spf_white_lists',
        ));

        $spf_pass_local = new Check('spf_pass_local', array(
            'id' => 'spf_pass_local',
            'name' => 'spf_pass_local',
        ));

        $max_msg_size_status = new Check('max_msg_size_status', array(
            'id' => 'max_msg_size_status',
            'name' => 'max_msg_size_status',
        ));
        $max_msg_size = new Text('max_msg_size', array(
            'id' => 'max_msg_size',
            'name' => 'max_msg_size',
        ));

        $sub_port_status = new Check('sub_port_status', array(
            'id' => 'sub_port_status',
            'name' => 'sub_port_status',
        ));
        $sub_port = new Text('sub_port', array(
            'id' => 'sub_port',
            'name' => 'sub_port',
        ));

        $sub_port_auth_mode = new Select('sub_port_auth_mode', [
            "0" => __("Always allow authentication"),
            "1" => __("Never allow authentication"),
            "2" => __("Only allow secure authentication (Usin SSL or TLS)"),
        ], [
            'id' => 'sub_port_auth_mode',
            'name' => 'sub_port_auth_mode',
        ]);

        $smtpd_recipient_limit = new Text('smtpd_recipient_limit', array(
            'id' => 'smtpd_recipient_limit',
            'name' => 'smtpd_recipient_limit',
        ));

        $smtp_in_connection_limit = new Text('smtp_in_connection_limit', array(
            'id' => 'smtp_in_connection_limit',
            'name' => 'smtp_in_connection_limit',
        ));

        $smtp_out_connection_limit = new Text('smtp_out_connection_limit', array(
            'id' => 'smtp_out_connection_limit',
            'name' => 'smtp_out_connection_limit',
        ));

        $max_queue_lifetime = new Text('max_queue_lifetime', array(
            'id' => 'max_queue_lifetime',
            'name' => 'max_queue_lifetime',
        ));

        $dns_rbl_status = new Check('dns_rbl_status', array(
            'id' => 'dns_rbl_status',
            'name' => 'dns_rbl_status',
        ));

        $dns_rbl_mode = new Select('dns_rbl_mode', [
            "0" => __("Dont accept the mail"),
            "1" => __("Mark the email as spam"),
        ], [
            'id' => 'dns_rbl_mode',
            'name' => 'dns_rbl_mode',
        ]);

        $me_dns_black_lists = new Select('me_dns_black_lists', $blacklists, [
            'id' => 'me_dns_black_lists',
            'name' => 'me_dns_black_lists[]',
            'multiple' => 'multiple',
        ]);

        $url_rbl_status = new Check('url_rbl_status', array(
            'id' => 'url_rbl_status',
            'name' => 'url_rbl_status',
        ));

        $url_rbl_mode = new Select('url_rbl_mode', [
            "0" => __("Dont accept the mail"),
            "2" => __("Replace the link"),
            "3" => __("Mark the email as spam"),
        ], [
            'id' => 'url_rbl_mode',
            'name' => 'url_rbl_mode',
        ]);

        $me_url_black_lists = new Select('me_url_black_lists', $blacklists, [
            'id' => 'me_url_black_lists',
            'name' => 'me_url_black_lists[]',
            'multiple' => 'multiple',
        ]);

        if($mail_server_settings["sub_port_status"] == "on") {
            $sub_port_status->setAttribute("checked", "checked");
        }
        if($mail_server_settings["sub_port"] != null) {
            $sub_port->setAttribute("value", $mail_server_settings["sub_port"]);
        }
        if($mail_server_settings["sub_port_auth_mode"] != null) {
            $sub_port_auth_mode->setDefault($mail_server_settings["sub_port_auth_mode"]);
        }

        if($mail_server_settings["max_msg_size_status"] == "on") {
            $max_msg_size_status->setAttribute("checked", "checked");
        }
        if($mail_server_settings["max_msg_size"] != null) {
            $max_msg_size->setAttribute("value", $mail_server_settings["max_msg_size"]);
        }
        if($mail_server_settings["smtpd_recipient_limit"] != null) {
            $smtpd_recipient_limit->setAttribute("value", $mail_server_settings["smtpd_recipient_limit"]);
        }
        if($mail_server_settings["smtp_in_connection_limit"] != null) {
            $smtp_in_connection_limit->setAttribute("value", $mail_server_settings["smtp_in_connection_limit"]);
        }
        if($mail_server_settings["smtp_out_connection_limit"] != null) {
            $smtp_out_connection_limit->setAttribute("value", $mail_server_settings["smtp_out_connection_limit"]);
        }
        if($mail_server_settings["max_queue_lifetime"] != null) {
            $max_queue_lifetime->setAttribute("value", $mail_server_settings["max_queue_lifetime"]);
        }

        if($mail_server_settings["dns_rbl_status"] == "on") {
            $dns_rbl_status->setAttribute("checked", "checked");
        }
        if($mail_server_settings["dns_rbl_mode"] != null) {
            $dns_rbl_mode->setDefault($mail_server_settings["dns_rbl_mode"]);
        }
        if($mail_server_settings["me_dns_black_lists"] != null) {
            $me_dns_black_lists->setDefault($mail_server_settings["me_dns_black_lists"]);
        }

        if($mail_server_settings["url_rbl_status"] == "on") {
            $url_rbl_status->setAttribute("checked", "checked");
        }
        if($mail_server_settings["url_rbl_mode"] != null) {
            $url_rbl_mode->setDefault($mail_server_settings["url_rbl_mode"]);
        }
        if($mail_server_settings["me_url_black_lists"] != null) {
            $me_url_black_lists->setDefault($mail_server_settings["me_url_black_lists"]);
        }

        if($mail_server_settings["spf_protection"] == "on") {
            $spf_protection->setAttribute("checked", "checked");
        }
        if($mail_server_settings["spf_best_guess"] == "on") {
            $spf_best_guess->setAttribute("checked", "checked");
        }
        if($mail_server_settings["spf_best_guesses"] != null) {
            $spf_best_guesses->setAttribute("value", $mail_server_settings["spf_best_guesses"]);
        }
        if($mail_server_settings["spf_white_lists_status"] == "on") {
            $spf_white_lists_status->setAttribute("checked", "checked");
        }
        if($mail_server_settings["spf_white_lists"] != null) {
            $spf_white_lists->setAttribute("value", $mail_server_settings["spf_white_lists"]);
        }
        if($mail_server_settings["spf_pass_local"] == "on") {
            $spf_pass_local->setAttribute("checked", "checked");
        }
        if($mail_server_settings['spam_status'] == "1") {
            $spam_filter_status->setAttribute('checked','checked');
        }
        if(!$mailenable_pro) {
            $spf_pass_local->setAttribute("disabled", "disabled");
            $spf_white_lists->setAttribute("disabled", "disabled");
            $spf_white_lists_status->setAttribute("disabled", "disabled");
            $spf_best_guesses->setAttribute("disabled", "disabled");
            $spf_best_guess->setAttribute("disabled", "disabled");
            $spf_protection->setAttribute("disabled", "disabled");
        }

        $this->add($hostname);
        $this->add($spam_filter_status);
        $this->add($spf_protection);
        $this->add($spf_best_guess);
        $this->add($spf_best_guesses);
        $this->add($spf_white_lists_status);
        $this->add($spf_white_lists);
        $this->add($spf_pass_local);
        $this->add($max_msg_size);
        $this->add($max_msg_size_status);
        $this->add($sub_port_status);
        $this->add($sub_port);
        $this->add($sub_port_auth_mode);
        $this->add($smtpd_recipient_limit);
        $this->add($smtp_in_connection_limit);
        $this->add($smtp_out_connection_limit);
        $this->add($max_queue_lifetime);
        $this->add($dns_rbl_status);
        $this->add($dns_rbl_mode);
        $this->add($me_dns_black_lists);
        $this->add($url_rbl_status);
        $this->add($url_rbl_mode);
        $this->add($me_url_black_lists);
    }

    private function hmailSettingsElements($options)
    {
        $mail_server_settings = $options['mail_server_data'];
        $spam_filter_status = new Check('spam_filter_status', array(
            'id' => 'spam_filter_status',
            'name' => 'spam_filter_status',
        ));
        if($mail_server_settings['spam_status'] == "1") {
            $spam_filter_status->setAttribute('checked','checked');
        }
        $max_smtp_threads = new Text('max_smtp_threads', array(
            'id' => 'max_smtp_threads',
            'name' => 'max_smtp_threads',
        ));
        $max_msg_size = new Text('max_msg_size', array(
            'id' => 'max_msg_size',
            'name' => 'max_msg_size',
        ));
        $sub_port_status = new Check('sub_port_status', array(
            'id' => 'sub_port_status',
            'name' => 'sub_port_status',
        ));
        $sub_port = new Text('sub_port', array(
            'id' => 'sub_port',
            'name' => 'sub_port',
        ));

        $smtp_no_tries = new Text('smtp_no_tries', array(
            'id' => 'smtp_no_tries',
            'name' => 'smtp_no_tries',
        ));
        $smtp_tries_min = new Text('smtp_tries_min', array(
            'id' => 'smtp_tries_min',
            'name' => 'smtp_tries_min',
        ));
        $hostname = new Text('hostname', array(
            'id' => 'hostname',
            'name' => 'hostname',
        ));

        $smtp_relayer = new Text('smtp_relayer', array(
            'id' => 'smtp_relayer',
            'name' => 'smtp_relayer',
        ));
        $smtp_relayer_port = new Text('smtp_relayer_port', array(
            'id' => 'smtp_relayer_port',
            'name' => 'smtp_relayer_port',
        ));
        $smtp_relayer_auth = new Check('smtp_relayer_auth', array(
            'id' => 'smtp_relayer_auth',
            'name' => 'smtp_relayer_auth',
        ));
        $smtp_relayer_user = new Text('smtp_relayer_user', array(
            'id' => 'smtp_relayer_user',
            'name' => 'smtp_relayer_user',
        ));
        $smtp_relayer_passwd = new Password('smtp_relayer_passwd', array(
            'id' => 'smtp_relayer_passwd',
            'name' => 'smtp_relayer_passwd',
        ));
        $smtp_relayer_security = new Select('smtp_relayer_security', [
            "none" => "None",
            "starttls_opt" => "STARTTLS (Optional)",
            "starttls_req" => "STARTTLS (Required)",
            "ssl" => "SSL/TLS",
        ], array(
            'id' => 'smtp_relayer_security',
            'name' => 'smtp_relayer_security',
        ));

        $smtp_auth_plain = new Check('smtp_auth_plain', array(
            'id' => 'smtp_auth_plain',
            'name' => 'smtp_auth_plain',
        ));
        $allow_empty_sender = new Check('allow_empty_sender', array(
            'id' => 'allow_empty_sender',
            'name' => 'allow_empty_sender',
        ));
        $allow_invalid_end = new Check('allow_invalid_end', array(
            'id' => 'allow_invalid_end',
            'name' => 'allow_invalid_end',
        ));
        $dis_invalid_client = new Check('dis_invalid_client', array(
            'id' => 'dis_invalid_client',
            'name' => 'dis_invalid_client',
        ));
        $max_inv_command = new Text('max_inv_command', array(
            'id' => 'max_inv_command',
            'name' => 'max_inv_command',
        ));
        $delivery_bind_to_ip = new Text('delivery_bind_to_ip', array(
            'id' => 'delivery_bind_to_ip',
            'name' => 'delivery_bind_to_ip',
        ));
        $max_rec_batch = new Text('max_rec_batch', array(
            'id' => 'max_rec_batch',
            'name' => 'max_rec_batch',
        ));
        $add_del_head = new Check('add_del_head', array(
            'id' => 'add_del_head',
            'name' => 'add_del_head',
        ));
        $rule_loop_limit = new Text('rule_loop_limit', array(
            'id' => 'rule_loop_limit',
            'name' => 'rule_loop_limit',
        ));
        $max_mx_host = new Text('max_mx_host', array(
            'id' => 'max_mx_host',
            'name' => 'max_mx_host',
        ));

        $max_pop_threads = new Text('max_pop_threads', array(
            'id' => 'max_pop_threads',
            'name' => 'max_pop_threads',
        ));
        $max_imap_threads = new Text('max_imap_threads', array(
            'id' => 'max_imap_threads',
            'name' => 'max_imap_threads',
        ));


        $spam_mark_threshold = new Text('spam_mark_threshold', array(
            'id' => 'spam_mark_threshold',
            'name' => 'spam_mark_threshold',
        ));
        $add_header_spam = new Check('add_header_spam', array(
            'id' => 'add_header_spam',
            'name' => 'add_header_spam',
        ));
        $add_reason_spam = new Check('add_reason_spam', array(
            'id' => 'add_reason_spam',
            'name' => 'add_reason_spam',
        ));
        $add_msg_subj = new Check('add_msg_subj', array(
            'id' => 'add_msg_subj',
            'name' => 'add_msg_subj',
        ));
        $add_msg_subj_txt = new Text('add_msg_subj_txt', array(
            'id' => 'add_msg_subj_txt',
            'name' => 'add_msg_subj_txt',
        ));
        $spam_del_threshold = new Text('spam_del_threshold', array(
            'id' => 'spam_del_threshold',
            'name' => 'spam_del_threshold',
        ));
        $spam_max_msg_size = new Text('spam_max_msg_size', array(
            'id' => 'spam_max_msg_size',
            'name' => 'spam_max_msg_size',
        ));


        $use_spf = new Check('use_spf', array(
            'id' => 'use_spf',
            'name' => 'use_spf',
        ));
        $use_spf_score = new Text('use_spf_score', array(
            'id' => 'use_spf_score',
            'name' => 'use_spf_score',
        ));
        $check_host_helo = new Check('check_host_helo', array(
            'id' => 'check_host_helo',
            'name' => 'check_host_helo',
        ));
        $check_host_helo_score = new Text('check_host_helo_score', array(
            'id' => 'check_host_helo_score',
            'name' => 'check_host_helo_score',
        ));
        $use_mx_checks = new Check('use_mx_checks', array(
            'id' => 'use_mx_checks',
            'name' => 'use_mx_checks',
        ));
        $use_mx_checks_score = new Text('use_mx_checks_score', array(
            'id' => 'use_mx_checks_score',
            'name' => 'use_mx_checks_score',
        ));
        $verify_dkim = new Check('verify_dkim', array(
            'id' => 'verify_dkim',
            'name' => 'verify_dkim',
        ));
        $verify_dkim_score = new Text('verify_dkim_score', array(
            'id' => 'verify_dkim_score',
            'name' => 'verify_dkim_score',
        ));

        $max_smtp_threads->setAttribute("value", $mail_server_settings["max_smtp_threads"]);
        $smtp_no_tries->setAttribute("value", $mail_server_settings["smtp_no_tries"]);
        $smtp_tries_min->setAttribute("value", $mail_server_settings["smtp_tries_min"]);
        $smtp_relayer->setAttribute("value", $mail_server_settings["smtp_relayer"]);
        $smtp_relayer_port->setAttribute("value", $mail_server_settings["smtp_relayer_port"]);

        if($mail_server_settings["smtp_relayer_auth"] == "on") {
            $smtp_relayer_auth->setAttribute("checked", "checked");
            $smtp_relayer_user->setAttribute("value", $mail_server_settings["smtp_relayer_user"]);
            $smtp_relayer_passwd->setAttribute("value", $mail_server_settings["smtp_relayer_passwd"]);
            $smtp_relayer_security->setDefault($mail_server_settings["smtp_relayer_security"]);
        }
        if($mail_server_settings["smtp_auth_plain"] == "on") {
            $smtp_auth_plain->setAttribute("checked", "checked");
        }
        if($mail_server_settings["allow_empty_sender"] == "on") {
            $allow_empty_sender->setAttribute("checked", "checked");
        }
        if($mail_server_settings["allow_invalid_end"] == "on") {
            $allow_invalid_end->setAttribute("checked", "checked");
        }
        if($mail_server_settings["dis_invalid_client"] == "on") {
            $dis_invalid_client->setAttribute("checked", "checked");
        }
        if($mail_server_settings["add_header_spam"] == "on") {
            $add_header_spam->setAttribute("checked", "checked");
        }
        if($mail_server_settings["add_reason_spam"] == "on") {
            $add_reason_spam->setAttribute("checked", "checked");
        }
        if($mail_server_settings["add_msg_subj"] == "on") {
            $add_msg_subj->setAttribute("checked", "checked");
        }
        if($mail_server_settings["use_spf"] == "on") {
            $use_spf->setAttribute("checked", "checked");
        }
        if($mail_server_settings["check_host_helo"] == "on") {
            $check_host_helo->setAttribute("checked", "checked");
        }
        if($mail_server_settings["use_mx_checks"] == "on") {
            $use_mx_checks->setAttribute("checked", "checked");
        }
        if($mail_server_settings["verify_dkim"] == "on") {
            $verify_dkim->setAttribute("checked", "checked");
        }
        if($mail_server_settings["sub_port_status"] == "on") {
            $sub_port_status->setAttribute("checked", "checked");
        }
        $max_inv_command->setAttribute("value", $mail_server_settings["max_inv_command"]);
        $delivery_bind_to_ip->setAttribute("value", $mail_server_settings["delivery_bind_to_ip"]);
        $max_rec_batch->setAttribute("value", $mail_server_settings["max_rec_batch"]);
        $rule_loop_limit->setAttribute("value", $mail_server_settings["rule_loop_limit"]);
        $max_mx_host->setAttribute("value", $mail_server_settings["max_mx_host"]);
        $hostname->setAttribute("value", $mail_server_settings["hostname"]);
        $max_pop_threads->setAttribute("value", $mail_server_settings["max_pop_threads"]);
        $max_imap_threads->setAttribute("value", $mail_server_settings["max_imap_threads"]);
        $spam_mark_threshold->setAttribute("value", $mail_server_settings["spam_mark_threshold"]);
        $add_msg_subj_txt->setAttribute("value", $mail_server_settings["add_msg_subj_txt"]);
        $spam_del_threshold->setAttribute("value", $mail_server_settings["spam_del_threshold"]);
        $spam_max_msg_size->setAttribute("value", $mail_server_settings["spam_max_msg_size"]);
        $use_spf_score->setAttribute("value", $mail_server_settings["use_spf_score"]);
        $check_host_helo_score->setAttribute("value", $mail_server_settings["check_host_helo_score"]);
        $use_mx_checks_score->setAttribute("value", $mail_server_settings["use_mx_checks_score"]);
        $verify_dkim_score->setAttribute("value", $mail_server_settings["verify_dkim_score"]);
        $max_msg_size->setAttribute("value", $mail_server_settings["max_msg_size"]);
        $sub_port->setAttribute("value", $mail_server_settings["sub_port"]);




        $this->add($max_smtp_threads);
        $this->add($max_msg_size);
        $this->add($sub_port_status);
        $this->add($sub_port);
        $this->add($smtp_no_tries);
        $this->add($smtp_tries_min);
        $this->add($hostname);
        $this->add($smtp_relayer);
        $this->add($smtp_relayer_port);
        $this->add($smtp_relayer_auth);
        $this->add($smtp_relayer_user);
        $this->add($smtp_relayer_passwd);
        $this->add($smtp_relayer_security);

        $this->add($smtp_auth_plain);
        $this->add($allow_empty_sender);
        $this->add($allow_invalid_end);
        $this->add($dis_invalid_client);
        $this->add($max_inv_command);
        $this->add($delivery_bind_to_ip);
        $this->add($max_rec_batch);
        $this->add($add_del_head);
        $this->add($rule_loop_limit);
        $this->add($max_mx_host);

        $this->add($max_pop_threads);
        $this->add($max_imap_threads);

        $this->add($spam_mark_threshold);
        $this->add($add_header_spam);
        $this->add($add_reason_spam);
        $this->add($add_msg_subj);
        $this->add($add_msg_subj_txt);
        $this->add($spam_del_threshold);
        $this->add($spam_max_msg_size);

        $this->add($use_spf);
        $this->add($use_spf_score);
        $this->add($check_host_helo);
        $this->add($check_host_helo_score);
        $this->add($use_mx_checks);
        $this->add($use_mx_checks_score);
        $this->add($verify_dkim);
        $this->add($verify_dkim_score);
        $this->add($spam_filter_status);
    }

    /**
     * @param null $options
     */
    private function spamSettingsElements($options)
    {
        // Spam Scores
        $spam_scores = new Text('spam_scores', array(
            'id' => 'spam_scores',
            'name' => 'spam_scores',
        ));

        // Pyzor Support
        $pyzor_support = new Check('pyzor_support', array(
            'id' => 'pyzor_support',
            'name' => 'pyzor_support',
        ));

        // Razor Support
        $razor_support = new Check('razor_support', array(
            'id' => 'razor_support',
            'name' => 'razor_support',
        ));

        // Skip RBL Check
        $skip_rbl = new Check('skip_rbl', array(
            'id' => 'skip_rbl',
            'name' => 'skip_rbl',
        ));

        // Bayes Support
        $bayes_support = new Check('bayes_support', array(
            'id' => 'bayes_support',
            'name' => 'bayes_support',
        ));

        // Enable Spam Report
        $spam_report = new Check('spam_report', array(
            'id' => 'spam_report',
            'name' => 'spam_report',
        ));


        $use_bayes_rules = new Check('use_bayes_rules', array(
            'id' => 'use_bayes_rules',
            'name' => 'use_bayes_rules',
        ));

        $bayes_auto_learn_on_error = new Check('bayes_auto_learn_on_error', array(
            'id' => 'bayes_auto_learn_on_error',
            'name' => 'bayes_auto_learn_on_error',
        ));

        $bayes_auto_learn = new Check('bayes_auto_learn', array(
            'id' => 'bayes_auto_learn',
            'name' => 'bayes_auto_learn',
        ));

        $bayes_auto_learn_threshold_nonspam = new Text('bayes_auto_learn_threshold_nonspam', array(
            'id' => 'bayes_auto_learn_threshold_nonspam',
            'name' => 'bayes_auto_learn_threshold_nonspam',
        ));

        $bayes_auto_learn_threshold_spam = new Text('bayes_auto_learn_threshold_spam', array(
            'id' => 'bayes_auto_learn_threshold_spam',
            'name' => 'bayes_auto_learn_threshold_spam',
        ));

        $rewrite_header = new Text('rewrite_header', array(
            'id' => 'rewrite_header',
            'name' => 'rewrite_header',
        ));


        if($options['spam_data']['installed'] == "0") {
            $spam_scores->setAttribute('disabled','disabled');
            $pyzor_support->setAttribute('disabled','disabled');
            $razor_support->setAttribute('disabled','disabled');
            $skip_rbl->setAttribute('disabled','disabled');
            $bayes_support->setAttribute('disabled','disabled');
            $spam_report->setAttribute('disabled','disabled');
        }

        if(strlen($options['spam_data']['scores']) == 1) {
            $spam_scores->setAttribute('value', $options['spam_data']['scores'].'.0');
        }
        else {
            $spam_scores->setAttribute('value', $options['spam_data']['scores']);
        }

        if($options['spam_data']['pyzor'] == "1")
            $pyzor_support->setAttribute('checked','checked');

        if($options['spam_data']['razor'] == "1")
            $razor_support->setAttribute('checked','checked');

        if($options['spam_data']['skip_rbl'] == "1")
            $skip_rbl->setAttribute('checked','checked');

        if($options['spam_data']['bayes'] == "1")
            $bayes_support->setAttribute('checked','checked');

        if($options['spam_data']['spam_report'] == "1")
            $spam_report->setAttribute('checked','checked');


        if($options['spam_data']['use_bayes_rules'] == "1")
            $use_bayes_rules->setAttribute('checked','checked');

        if($options['spam_data']['bayes_auto_learn_on_error'] == "1")
            $bayes_auto_learn_on_error->setAttribute('checked','checked');

        if($options['spam_data']['bayes_auto_learn'] == "1")
            $bayes_auto_learn->setAttribute('checked','checked');


        $bayes_auto_learn_threshold_nonspam->setAttribute('value', $options['spam_data']['bayes_auto_learn_threshold_nonspam']);
        $bayes_auto_learn_threshold_spam->setAttribute('value', $options['spam_data']['bayes_auto_learn_threshold_spam']);
        $rewrite_header->setAttribute('value', $options['spam_data']['rewrite_header']);


        $this->add($spam_scores);
        $this->add($pyzor_support);
        $this->add($razor_support);
        $this->add($skip_rbl);
        $this->add($bayes_support);
        $this->add($spam_report);

        $this->add($use_bayes_rules);
        $this->add($bayes_auto_learn_on_error);
        $this->add($bayes_auto_learn);
        $this->add($bayes_auto_learn_threshold_nonspam);
        $this->add($bayes_auto_learn_threshold_spam);
        $this->add($rewrite_header);

    }

    /**
     * @param null $options
     */
    private function blacklistSettingsElements($options = null)
    {
        if($options['action'] == "edit") {
            // Blacklist Rule
            $this->add(new Text('blacklist_rule', array(
                'id' => 'blacklist_rule',
                'name' => 'blacklist_rule',
                'value' => $options['rule']['rule'],
            )));
        }
        else {
            // Blacklist Rule
            $this->add(new Text('blacklist_rule', array(
                'id' => 'blacklist_rule',
                'name' => 'blacklist_rule',
                'value' => '',
            )));
        }

    }

    private function whitelistSettingsElements($options = null)
    {
        if($options['action'] == "edit") {
            // White Rule
            $this->add(new Text('whitelist_rule', array(
                'id' => 'whitelist_rule',
                'name' => 'whitelist_rule',
                'value' => $options['rule']['rule'],
            )));
        }
        else {
            // White Rule
            $this->add(new Text('whitelist_rule', array(
                'id' => 'whitelist_rule',
                'name' => 'whitelist_rule',
                'value' => '',
            )));
        }
    }

    private function policydSettingsElements($options = null)
    {
        $min_servers = new Text('min_servers', array(
            'id' => 'min_servers',
            'name' => 'min_servers',
        ));

        $min_spare_servers = new Text('min_spare_servers', array(
            'id' => 'min_spare_servers',
            'name' => 'min_spare_servers',
        ));

        $max_spare_servers = new Text('max_spare_servers', array(
            'id' => 'max_spare_servers',
            'name' => 'max_spare_servers',
        ));

        $max_servers = new Text('max_servers', array(
            'id' => 'max_servers',
            'name' => 'max_servers',
        ));

        $max_requests = new Text('max_requests', array(
            'id' => 'max_requests',
            'name' => 'max_requests',
        ));

        $timeout_busy = new Text('timeout_busy', array(
            'id' => 'timeout_busy',
            'name' => 'timeout_busy',
        ));

        $bypass_timeout = new Text('bypass_timeout', array(
            'id' => 'bypass_timeout',
            'name' => 'bypass_timeout',
        ));

        $log_level = new Select('log_level',[
            "0" => __("Only Errors"),
            "1" => __("Warning & Errors"),
            "2" => __("Notices, Warnings, Errors"),
            "3" => __("Info, Notices, Warnings, Errors"),
            "4" => __("Debugging"),
        ], [
            'id' => 'log_level',
            'name' => 'log_level',
        ]);

        $log_detail = new Select('log_detail',[
            "modules" => "Module Running Info",
            "tracking" => "Tracking Information",
            "policies" => "Policy Resolution",
            "protocols" => "Protocols Info",
            "bizanga" => "Bizanga",
            "cache" => "Cache",
        ], [
            'id' => 'log_detail',
            'name' => 'log_detail[]',
            'multiple' => 'multiple',
        ]);

        $min_servers->setAttribute("value", $options["policy"]["min_servers"]);
        $min_spare_servers->setAttribute("value", $options["policy"]["min_spare_servers"]);
        $max_spare_servers->setAttribute("value", $options["policy"]["max_spare_servers"]);
        $max_servers->setAttribute("value", $options["policy"]["max_servers"]);
        $max_requests->setAttribute("value", $options["policy"]["max_requests"]);

        $timeout_busy->setAttribute("value", $options["policy"]["timeout_busy"]);
        $bypass_timeout->setAttribute("value", $options["policy"]["bypass_timeout"]);
        $log_level->setDefault($options["policy"]["log_level"]);
        $log_detail->setDefault(explode(",", $options["policy"]["log_detail"]));

        $this->add($min_servers);
        $this->add($min_spare_servers);
        $this->add($max_spare_servers);
        $this->add($max_servers);
        $this->add($max_requests);
        $this->add($timeout_busy);
        $this->add($bypass_timeout);
        $this->add($log_level);
        $this->add($log_detail);
    }
}