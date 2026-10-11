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

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\SpamList;
use FPanel\Models\SpamSettings as SpamSettingsDb;
use FPanel\Process\MailServer;

class SpamSettings extends Library
{
    public function spamlists($server_id, $type): array
    {
        $datatable = new Datatable();
        if($server_id != "1") {
            $delete_button = $datatable->buttons("mailserver/delete$type/$1/server/$server_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        }
        else {
            $delete_button = $datatable->buttons("mailserver/delete$type/$1",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        }

        $datatable->select([
            'spam_list.id as rule_id',
            'spam_list.rule','spam_list.id'
        ])
            ->from("spam_list")
            ->where("spam_list.server_id = '$server_id'")
            ->andWhere("spam_list.type = '$type'")
            ->searchColumn(array('spam_list.rule'))
            ->addColumn('actions', $delete_button,'spam_list.id')
            ->editColumn('rule_id','<input type="checkbox" class="checkbox-blue" value="$1" name="lists[]">','rule_id');


        $data = $datatable->unsetColumns('spam_list.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value) {
            $data['aaData'][$key][0] = $value[0];
            $data['aaData'][$key][1] = $value[1];
            $data['aaData'][$key][2] = $value[3];
            unset($data['aaData'][$key][3]);
        }

        return $data;
    }

    public function updateSpamSettings(): bool
    {
        $pyzor = "0";
        $razor = "0";
        $skip_rbl = "0";
        $bayes = "0";
        $spam_report = "0";
        $server_id = $this->getPost("server_id");

        $use_bayes_rules = "0";
        $bayes_auto_learn_on_error = "0";
        $bayes_auto_learn = "0";
        $bayes_auto_learn_threshold_nonspam = "0.05";
        $bayes_auto_learn_threshold_spam = "11.0";
        $rewrite_header = $this->getPost("rewrite_header", "[***** SPAM _SCORE_ *****]");

        if($this->getPost("pyzor") == "on") {
            $pyzor = "1";
        }
        if($this->getPost("razor") == "on") {
            $razor = "1";
        }
        if($this->getPost("skip_rbl") == "on") {
            $skip_rbl = "1";
        }
        if($this->getPost("bayes") == "on") {
            $bayes = "1";
        }
        if($this->getPost("spam_report") == "on") {
            $spam_report = "1";
        }

        if($this->getPost("use_bayes_rules") == "on") {
            $use_bayes_rules = "1";
        }
        if($this->getPost("bayes_auto_learn_on_error") == "on") {
            $bayes_auto_learn_on_error = "1";
        }
        if($this->getPost("bayes_auto_learn") == "on") {
            $bayes_auto_learn = "1";
        }
        if($this->getPost("bayes_auto_learn_threshold_nonspam") == "on") {
            $bayes_auto_learn_threshold_nonspam = "1";
        }
        if($this->getPost("bayes_auto_learn_threshold_spam") == "on") {
            $bayes_auto_learn_threshold_spam = "1";
        }
        if($this->getPost("rewrite_header") == null) {
            $rewrite_header = "[***** SPAM _SCORE_ *****]";
        }

        if(is_numeric($this->getPost("scores"))) {
            if(is_float($this->getPost("scores"))) {
                if($this->getPost("scores") > 5) {
                    $this->error_messages['spam_scores'] = __('Score must be max 5.0');
                    return false;
                }
                $scores = $this->getPost("scores");
            }
            else {
                if($this->getPost("scores") > 5) {
                    $this->error_messages['spam_scores'] = __('Score must be max 5.0');
                    return false;
                }
                else {
                    $scores = $this->getPost("scores");
                    if(strlen($this->getPost("scores")) < 2) {
                        $scores = $scores.'.0';
                    }
                }
            }
        }
        else {
            $this->error_messages['spam_scores'] = __('Please enter valid scores');
            return false;
        }


        $spam_settings = SpamSettingsDb::findFirst("server_id = '$server_id'");

        $spam_settings->assign([
            'scores'        => $scores,
            'pyzor'         => $pyzor,
            'razor'         => $razor,
            'skip_rbl'      => $skip_rbl,
            'bayes'         => $bayes,
            'spam_report'   => $spam_report,

            'use_bayes_rules'           => $use_bayes_rules,
            'bayes_auto_learn_on_error' => $bayes_auto_learn_on_error,
            'bayes_auto_learn'          => $bayes_auto_learn,
            'bayes_auto_learn_threshold_nonspam' => $bayes_auto_learn_threshold_nonspam,
            'bayes_auto_learn_threshold_spam' => $bayes_auto_learn_threshold_spam,
            'rewrite_header' => $rewrite_header,
        ]);

        if(!$spam_settings->update()) {
            foreach ($spam_settings->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "SpamSettings->updateSpamSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        $this->spamSettingsCurl($server_id);
        return true;
    }

    public function createList(string $type, string $server_id, string $rule): bool
    {
        $required = array(array(
            'field' => $type.'list_rule',
            'item' => $rule,
            'message' => __('Rule is required')
        ));

        if($this->validation->Required($required)->Validate() == false) {
            $this->error_messages =  $this->validation->getMessage();
            return false;
        }

        $chk = SpamList::findFirst("rule = '$rule' AND server_id = '$server_id'");
        if($chk) {
            $this->error_messages[$type.'list_rule'] = __('Rule must be unique');
            return false;
        }

        $spam_list = new SpamList();
        $spam_list->assign(array(
            'rule'      => $rule,
            'type'      => $type,
            'server_id' => $server_id,
        ));
        if(!$spam_list->create()) {
            foreach($spam_list->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            $this->log->error([
                "Module" => "SpamSettings->createList",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            $rules = SpamList::pluck("id", "rule",["type = '$type'"]);

            if($type == "black") {
                $this->initBlackListRuleCommand(implode("#", $rules), $server_id);
            }
            else if($type == "white") {
                $this->initWhiteListRuleCommand(implode("#", $rules), $server_id);
            }
        }
        return true;
    }

    public function deleteList($type, $server_id, $id): bool
    {
        $list = SpamList::findFirst("id = '$id' AND server_id = '$server_id' AND type = '$type'");
        if($list) {
            if(!$list->delete()) {
                foreach($list->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "SpamSettings->deleteList",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $rules = SpamList::pluck("id", "rule",["type = '$type'"]);
                if($type == "black") {
                    $this->initBlackListRuleCommand(implode("#", $rules), $server_id);
                }
                else if($type == "white") {
                    $this->initWhiteListRuleCommand(implode("#", $rules), $server_id);
                }
            }
            return true;
        }
        else {
            return false;
        }
    }

    public function saveDefaultSpamSettings(int $spam_status, string $server_id, int $installed = PASSIVE): bool
    {
        $scores = "5.0";
        $pyzor = "1";
        $razor = "1";
        $skip_rbl = "1";
        $bayes = "1";
        $spam_report = "1";

        $use_bayes_rules = "1";
        $bayes_auto_learn_on_error = "1";
        $bayes_auto_learn = "1";
        $bayes_auto_learn_threshold_nonspam = "0.05";
        $bayes_auto_learn_threshold_spam = "11.0";
        $rewrite_header = $this->getPost("rewrite_header", "[***** SPAM _SCORE_ *****]");

        $spam_settings = SpamSettingsDb::findFirst("server_id = '$server_id'");
        if(!$spam_settings)
            $spam_settings = new SpamSettingsDb();

        $assign = array(
            'status' => $spam_status,
            'scores' => $scores,
            'pyzor' => $pyzor,
            'razor' => $razor,
            'skip_rbl' => $skip_rbl,
            'bayes' => $bayes,
            'spam_report' => $spam_report,

            'use_bayes_rules' => $use_bayes_rules,
            'bayes_auto_learn_on_error' => $bayes_auto_learn_on_error,
            'bayes_auto_learn' => $bayes_auto_learn,
            'bayes_auto_learn_threshold_nonspam' => $bayes_auto_learn_threshold_nonspam,
            'bayes_auto_learn_threshold_spam' => $bayes_auto_learn_threshold_spam,
            'rewrite_header' => $rewrite_header,

            'server_id' => $server_id,
            'installed' => $installed,
        );

        $spam_settings->assign($assign);
        if(!$spam_settings->create()) {
            foreach($spam_settings->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            $this->log->error([
                "Module" => "SpamSettings->saveDefaultSpamSettings",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function spamSettingsCurl($server_id)
    {
        $mailsrvmng = new MailServer();
        $mailsrvmng->setServerId($server_id);
        $scores = $this->getPost("scores");
        $pyzor = "off";
        $razor = "off";
        $skip_rbl = "off";
        $bayes = "off";
        $spam_report = "off";

        $use_bayes_rules = "off";
        $bayes_auto_learn_on_error = "off";
        $bayes_auto_learn = "off";
        $rewrite_header = "[***** SPAM _SCORE_ *****]";
        $bayes_auto_learn_threshold_nonspam = $this->getPost("bayes_auto_learn_threshold_nonspam");
        $bayes_auto_learn_threshold_spam = $this->getPost("bayes_auto_learn_threshold_spam");

        if($this->getPost("pyzor") == "on") {
            $pyzor = "on";
        }
        if($this->getPost("razor") == "on") {
            $razor = "on";
        }
        if($this->getPost("skip_rbl") == "on") {
            $skip_rbl = "on";
        }
        if($this->getPost("bayes") == "on") {
            $bayes = "on";
        }
        if($this->getPost("spam_report") == "on") {
            $spam_report = "on";
        }
        if($this->getPost("use_bayes_rules") == "on") {
            $use_bayes_rules = "on";
        }
        if($this->getPost("bayes_auto_learn_on_error") == "on") {
            $bayes_auto_learn_on_error = "on";
        }
        if($this->getPost("bayes_auto_learn") == "on") {
            $bayes_auto_learn = "on";
        }
        if($this->getPost("rewrite_header") != null) {
            $rewrite_header = $this->getPost("rewrite_header");
        }

        if(strlen($this->getPost("scores")) == "1") {
            $scores = $scores . ".0";
        }

        $params = array(
            'score' => $scores,
            'bayes' => $bayes,
            'rbl-check' => $skip_rbl,
            'razor' => $razor,
            'pyzor' => $pyzor,
            'report' => $spam_report,

            "use-bayes" => $use_bayes_rules,
            "bayes-auto-learn" => $bayes_auto_learn,
            "bayes-auto-learn-err" => $bayes_auto_learn_on_error,
            "bayes-ts-nospam" => $bayes_auto_learn_threshold_nonspam,
            "bayes-ts-spam" => $bayes_auto_learn_threshold_spam,
            "rewrite-header" => $rewrite_header,
        );

        $mailsrvmng->spam($params);

    }

    public function initBlackListRuleCommand($rules, $server_id)
    {
        $mailsrvmng = new MailServer();
        $mailsrvmng->setServerId($server_id);
        $params = array(
            "list" => $rules
        );
        $mailsrvmng->initBlackList($params);

    }

    public function initWhiteListRuleCommand($rules, $server_id)
    {
        $mailsrvmng = new MailServer();
        $mailsrvmng->setServerId($server_id);
        $params = array(
            "list" => $rules
        );
        $mailsrvmng->initWhiteList($params);
    }

}