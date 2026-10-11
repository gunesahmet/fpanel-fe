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
use FPanel\Library\Library;
use FPanel\Models\Blacklist;

/**
 * Class Blacklists
 * @package FPanel\System
 */
class Blacklists extends Library
{
    public function blacklists($type): array
    {
        $datatable = new Datatable();

        $delete_button = $datatable->buttons("blacklist/delete/$1",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        $edit_button = $datatable->buttons("blacklist/edit/$1",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        $data = $datatable
            ->select(array('id as rule_id','id','rule'))
            ->from("blacklist")
            ->where("type = '$type'")
            ->searchColumn(array('rule'))
            ->editColumn('rule', '<a href="blacklist/edit/$1">$2</a>', 'id,rule')
            ->addColumn('actions', $edit_button.$delete_button,'id')
            ->editColumn('rule_id','<input type="checkbox" id="$1" name="rule_chk[]">','rule_id')
            ->unsetColumns('id')
            ->searchColumn(array('rule'))
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][1] = $value[2];
            $data['aaData'][$key][2] = $value[3];
            unset($data['aaData'][$key][3]);
            unset($data['aaData'][$key][0]);
            $data['aaData'][$key] = array_values($data['aaData'][$key]);
        }

        return $data;
    }

    public function addRule($type): bool
    {
        $this->validation->Required(array(
            array(
                'item' => $this->getPost("rule"),
                'field' => 'rule',
                'message' => __('Rule is required')

            )
        ));
        $this->validation->CheckBlacklist($this->getPost("rule"),'rule', $type);

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        $blacklist = new Blacklist();

        $blacklist->assign(array(
            'type'  => $type,
            'rule'  => $this->getPost("rule"),
            'status'    => ACTIVE,
        ));

        if (!$blacklist->create()) {
            foreach ($blacklist->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Blacklists->addRule",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function editRule(): bool
    {
        $this->validation->Required(array(
            array(
                'item' => $this->getPost("rule"),
                'field' => 'rule',
                'message' => __('Rule is required')
            )
        ));

        if($this->getPost("rule") != $this->getPost("old_rule"))
            $this->validation->CheckBlacklist($this->getPost("rule"),'rule', $this->getPost("type"));
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }


        $blacklist_id = $this->getPost("id");
        $blacklist = Blacklist::findFirstById($blacklist_id);

        if($this->getPost("rule") != "null") {
            $blacklist->rule = $this->getPost("rule");
            if (!$blacklist->update()) {
                foreach ($blacklist->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Blacklists->editRule",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    public function deleteRule($id): bool
    {
        $blacklist = Blacklist::findFirstById($id);
        if($blacklist) {
            if (!$blacklist->delete()) {
                foreach ($blacklist->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Blacklists->deleteRule",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    public function defaultRulesSettingsSave(): bool
    {
        $blacklist_array = array(
            array('type' => 'ftp_user','rule' => 'SQLserver*', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'admin', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Administrator', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Administrators', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'apache', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Backup', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Certificate', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Cryptographic', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'dbadmin-pga', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'dbadmin-pma', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Distributed', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Event', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'ftpadmin', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Guest', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Guests', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'IIS_IUSRS', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'IUSER', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Network', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'nobody', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Performance', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Power', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Print', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Remote', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Replicator', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'root', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'Users', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'webmail-at', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'webmail-rc', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'www-data', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'system', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'sa', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'admin', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'nfsnobody', 'status' => 'on'),
            array('type' => 'ftp_user','rule' => 'nogroup', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'SQLserver]*', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'admin', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Administrator', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Administrators', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'apache', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Backup', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Certificate', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Cryptographic', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'dbadmin-pga', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'dbadmin-pma', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Distributed', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Event', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'ftpadmin', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Guest', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Guests', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'IIS_IUSRS', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'IUSER', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Network', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'nobody', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Performance', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Power', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Print', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Remote', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Replicator', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'root', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'Users', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'webmail-at', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'webmail-rc', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'www-data', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'system', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'sa', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'admin', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'nfsnobody', 'status' => 'on'),
            array('type' => 'support_users','rule' => 'nogroup', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'root', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'admin', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'admin', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'wm_rc', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'wm_at', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'Administrators', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'Users', 'status' => 'on'),
            array('type' => 'db_username','rule' => 'sa', 'status' => 'on'),
            array('type' => 'mail_account','rule' => 'root', 'status' => 'on'),
            array('type' => 'mail_account','rule' => 'postmaster', 'status' => 'on'),
            array('type' => 'mail_account','rule' => 'mailer-daemon', 'status' => 'on'),
        );

        foreach ($blacklist_array as $rule) {
            $blacklist = new Blacklist();
            $blacklist->assign(array(
                'type'      => $rule['type'],
                'rule'      => $rule['rule'],
                'status'    => '1'
            ));
            if (!$blacklist->create()) {
                foreach ($blacklist->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Blacklists->defaultRulesSettingsSave",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }

        return true;

    }

}