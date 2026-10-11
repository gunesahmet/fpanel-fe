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
 */namespace FPanel\Api\System;

use FPanel\Environments\QueryBuilder;
use FPanel\MailServer\Mail;
use FPanel\MailServer\SpamSettings;
use FPanel\Models\SpamList;
use Phalcon\Mvc\User\Component;

class MailServer extends Component
{
    public function init($process, $params)
    {
        switch($process[2]) {
            case "general-settings":
                return $this->generalSettings($params);
            case "spam-settings":
                return $this->spamSettings($params);
            case "blacklists":
                return $this->blackLists($process, $params);
            case "whitelists":
                return $this->whiteLists($process, $params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function generalSettings($params)
    {
        $error_messages = array();
        $mail_server_settings = new Mail();
        $post_data = array(
            'max_message_size' => $params['max_message_size'],
            'queue_life_time' => $params['queue_life_time'],
            'bounce_queue_lifetime' => $params['bounce_queue_lifetime'],
            'policyd_spy_timelimit' => $params['policyd_spy_timelimit'],
            'concurrency_in_que' => $params['concurrency_in_que'],
            'max_recipients' => $params['max_recipients'],
            'submission_port' => $params['submission_port'],
            'delete_double_msg' => $params['delete_double_msg'], // soft-bounce
            'imap_auth_with_smtp' => $params['imap_with_smtp'],
            'smtp_auth' => $params['smtp_auth'],
            'spf_protection' => $params['spf_protection'],
            'server_id' => $params['server_id'],
        );
        $mail_server_settings->setPosts($post_data);
        $mail_setting_update = $mail_server_settings->updateMailSettings($params['server_id']);
        if($mail_setting_update == true) {
            return "Success";
        }
        else {
            foreach ($mail_server_settings->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function spamSettings($params)
    {
        $error_messages = array();

        $post_data = array(
            'server_id' => $params['server_id'],
            'status' => $params['status'],
            'scores' => $params['scores'],
            'pyzor' => $params['pyzor'],
            'razor' => $params['razor'],
            'skip_rbl' => $params['skip_rbl'],
            'bayes' => $params['bayes'],
            'spam_report' => $params['spam_report'],

            'use_bayes_rules' => $params['use_bayes_rules'],
            'bayes_auto_learn_on_error' => $params['bayes_auto_learn_on_error'],
            'bayes_auto_learn' => $params['bayes_auto_learn'],
            'bayes_auto_learn_threshold_nonspam' => $params['bayes_auto_learn_threshold_nonspam'],
            'bayes_auto_learn_threshold_spam' => $params['bayes_auto_learn_threshold_spam'],
            'rewrite_header' => $params['rewrite_header'],
        );

        $spam_settings = new SpamSettings();

        $spam_settings->setPosts($post_data);

        $status = $spam_settings->updateSpamSettings();
        if(!$status) {
            foreach ($spam_settings->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }

    public function blackLists($process, $params)
    {
        switch($process[3]) {
            case "list":
                return $this->blackListsList($params);
            case "create":
                return $this->blackListsCreate($params);
            case "remove":
                return $this->blackListsRemove($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function whiteLists($process, $params)
    {
        switch($process[3]) {
            case "list":
                return $this->whiteListsList($params);
            case "create":
                return $this->whiteListsCreate($params);
            case "remove":
                return $this->whiteListsRemove($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    private function blackListsList($params): array
    {
        $qbuilder = new QueryBuilder();
        $data = $qbuilder->select([
            "spam_list.rule",
        ])
            ->from("spam_list")
            ->where([
                'conditions' => "spam_list.server_id = ?1",
                'bind' => [1 => $params['server_id']]
            ])
            ->andWhere([
                'conditions' => "spam_list.type = ?1",
                'bind' => [1 => "black"]
            ])
            ->find();

        $data['status'] = "1";
        return $data;
    }

    private function whiteListsList($params): array
    {
        $qbuilder = new QueryBuilder();
        $data = $qbuilder->select([
            "spam_list.rule",
        ])
            ->from("spam_list")
            ->where([
                'conditions' => "spam_list.server_id = ?1",
                'bind' => [1 => $params['server_id']]
            ])
            ->andWhere([
                'conditions' => "spam_list.type = ?1",
                'bind' => [1 => "white"]
            ])
            ->find();
        $data['status'] = "1";
        return $data;
    }

    private function blackListsCreate($params)
    {
        $error_messages = array();
        $spamlists = new SpamSettings();
        $status = $spamlists->createList('black', $params['server_id'], $params['rule']);
        if($status) {
            foreach ($spamlists->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }

    private function whiteListsCreate($params)
    {
        $error_messages = array();
        $spamlists = new SpamSettings();
        $status = $spamlists->createList('white', $params['server_id'], $params['rule']);
        if(!$status) {
            foreach ($spamlists->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }

    private function blackListsRemove($params)
    {
        $error_messages = array();
        $rule = SpamList::findFirst("rule = '$params[rule]' AND type = 'black' AND server_id = '$params[server_id]'");
        if(!$rule) {
            return $error_messages['rule'] = "Rule not found";
        }

        $spamlists = new SpamSettings();
        $status = $spamlists->deleteList("black", $params['server_id'], $rule->id);


        if(!$status) {
            foreach ($spamlists->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }

    private function whiteListsRemove($params)
    {
        $error_messages = array();
        $rule = SpamList::findFirst("rule = '$params[rule]' AND type = 'white' AND server_id = '$params[server_id]'");
        if(!$rule) {
            return $error_messages['oldrule'] = "Rule not found";
        }

        $spamlists = new SpamSettings();
        $status = $spamlists->deleteList("white",$params['server_id'], $rule->id);

        if(!$status) {
            foreach ($spamlists->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }
}