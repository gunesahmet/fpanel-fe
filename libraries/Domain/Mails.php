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

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\ClientLimitsPermissions;
use FPanel\Models\Domains;
use FPanel\Models\DomainServices;
use FPanel\Models\MailAlias;
use FPanel\Models\Mails as MailModel;
use FPanel\Models\MailSettings;
use FPanel\Process\Mail as MailManager;
use FPanel\Process\Mail as Mailmng;

/**
 * Class Mails
 * @package FPanel\Mails
 */
class Mails extends Library
{
    public $domain_name;
    protected $change_password = false;
    protected $overuse = PASSIVE;
    protected $option_data = array(
        'forward' => "off",
        'forward-addr' => "",
        'autoresponder' => "off",
    );

    public function mailSettings(): bool
    {
        try {
            $mail_settings = new MailSettings();
            $domain_id = $this->getPost("domain_id");
            $mail_settings->assign(array(
                'domain_id'         => $domain_id,
                'nonexistend_user'  => $this->getPost("nonexistend_user"),
                'mail_forward_addr' => "",
                'webmail'           => $this->getPost("webmail"),
                'spamfilter'        => $this->getPost("spamfilter"),
                'server_id'         => $this->getPost("server_id"),
            ));

            if(!$mail_settings->create()) {
                foreach ($mail_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Mails->mailSettings Domains",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $domains = Domains::findFirstById($domain_id);
                $this->setPost("domain_name", $domains->name);
                $this->mailSettingCurl();
                return true;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->mailSettings",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function updateMailSettings(): bool
    {
        try {
            $domain_id = $this->getPost("domain_id");

            if($this->getPost("forward_to_address") == ON) {
                $required = array(
                    array(
                        'field' => 'mail_address',
                        'item'  => $this->getPost("mail_address"),
                        'message'   => __('Mail Address is required')
                    )
                );

                $this->validation->Required($required);
                $status = $this->validation->Email($this->getPost("mail_address").'@'.$this->getPost("domain_name"),'mail_address',__('Please enter valid email address'))->Validate();
                if(!$status) {
                    $this->error_messages = $this->validation->getMessage();
                    return false;
                }

                $fwd_addr = $this->getPost("mail_address");
                $mails = MailModel::findFirst("domain_id = '$domain_id' AND mail_name = '$fwd_addr'");
                if(!$mails) {
                    $this->error_messages = array('mail_address' => __('Please create this mail for use'));
                    return false;
                }
            }

            $mail_settings = MailSettings::findFirst("domain_id = '$domain_id'");

            if($this->getPost("reject_incoming") == ON) {
                $nonexistend_user = REJECT;
            }
            else {
                $nonexistend_user = FORWARD;
            }

            $mail_settings->nonexistend_user = $nonexistend_user;

            if(!$mail_settings->update()) {
                foreach ($mail_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Mails->updateMailSettings",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            if(($nonexistend_user == FORWARD) && ($this->getPost("mail_address") != null)) {
                $mail_settings->mail_forward_addr =  $this->getPost("mail_address");

                if(!$mail_settings->update()) {
                    foreach ($mail_settings->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Mails->updateMailSettings",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }
            $this->mailSettingCurl();
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->updateMailSettings",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function createMailAccount($restore = false): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            if(!$restore) {
                $required = array(
                    array(
                        'field' => 'mail_account',
                        'item'  => $this->getPost("mail_account"),
                        'message' => __('Mail Address is required')
                    ),
                    array(
                        'field' => 'password',
                        'item'  => $this->getPost("password"),
                        'message' => __('Password is required')
                    )
                );

                if($this->getPost("unlimited_quota") != ON) {
                    $required[] = array(
                        'field' => 'mail_quota',
                        'item'  => $this->getPost("mailbox_quota"),
                        'message' => __('Quota is required')
                    );
                }

                $this->validation->Required($required)
                    ->Email($this->getPost("mail_account").'@'.$this->getPost("domain_name"),'mail_account',__('Please enter valid email address'))
                    ->Password($this->getPost("password"),'password',__('Please enter valid password'))
                    ->CheckBlacklist($this->getPost("mail_account"),'mail_account','mail_account',false,__('This username can\'t be use.'));

                if($this->getPost("unlimited_quota") != ON) {
                    $this->validation->Numeric($this->getPost("mailbox_quota"),'mail_quota',__('Please enter valid quota'));
                }

                $this->validation->UniqueMailAddress($this->getPost("mail_account"), $this->getPost("domain_id"),'mail_account');

                $status = $this->validation->Validate();
                if(!$status) {
                    $this->error_messages = $this->validation->getMessage();
                    return false;
                }
            }

            $domain_id = $this->getPost("domain_id");
            $client_limit_permissions = ClientLimitsPermissions::findFirst("domain_id = '$domain_id'");
            if($client_limit_permissions) {
                $domain_limit_data = $client_limit_permissions->toArray();
                $this->overuse = $domain_limit_data['overuse'];
            }

            $mail_account = new MailModel();
            if($this->getPost("unlimited_quota") == ON) {
                $mail_box_quota = UNLIMITED;
            }
            else {
                $mail_box_quota = $this->getPost("mailbox_quota");
            }
            $password = $this->crypto->bencode($this->getPost("password"));
            $status = "1";
            if ($this->has("status") && ($this->getPost("status") == OFF)) {
                $status = "0";
            }
            $mail_account->assign(array(
                "id"                => $this->env->createUuid(),
                'mail_name'         => $this->getPost("mail_account"),
                'password'          => $password,
                'domain_id'         => $this->getPost("domain_id"),
                'forward'           => PASSIVE,
                'forward_addr'      => NULL,
                'mail_group'        => PASSIVE,
                'autoresponder'     => PASSIVE,
                'mailbox_quota'     => $mail_box_quota,
                'status'            => $status,
            ));

            if(!$mail_account->create()) {
                foreach ($mail_account->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Mails->createMailAccount",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $this->createMailAccountCurl();
                return true;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->createMailAccount",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function updateMailAccount(): bool
    {
        try {
            $required = array();
            if($this->getPost("password") != null) {
                $required[] = array(
                    'field' => 'password',
                    'item'  => $this->getPost("password"),
                    'message' => __('Password is required')
                );

                $this->validation->Password($this->getPost("password"),'password',__('Please enter valid password'));
            }


            if($this->getPost("unlimited_quota") != ON) {
                $required[] = array(
                    'field' => 'mail_quota',
                    'item'  => $this->getPost("mailbox_quota"),
                    'message' => __('Quota is required')
                );
            }

            if($this->getPost("forward") == ON) {
                $required[] = array(
                    'field' => 'forward_to_address',
                    'item'  => $this->getPost("forward_addr"),
                    'message' => __('Forward mail address is required')
                );
                $this->validation->Email($this->getPost("forward_addr"),'forward_to_address',__('Please enter valid email address'));
                if($this->getPost("forward_addr") == $this->getPost("old_address").'@'.$this->getPost("domain_name")) {
                    $this->error_messages = array('forward_to_address' => __('Forward address is can not be same with mail address'));
                    return false;
                }
            }

            if(!empty($required)) {
                $this->validation->Required($required);
            }


            if($this->getPost("unlimited_quota") != ON) {
                $this->validation->Numeric($this->getPost("mailbox_quota"),'mail_quota',__('Please enter valid quota'));
            }

            $status = $this->validation->Validate();
            if(!$status) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }


            $domain_id = $this->getPost("domain_id");
            $client_limit_permissions = ClientLimitsPermissions::findFirst("domain_id = '$domain_id'");
            if($client_limit_permissions) {
                $domain_limit_data = $client_limit_permissions->toArray();
                $this->overuse = $domain_limit_data['overuse'];
            }

            $mail_id = $this->getPost("mail_id");
            $mail_account = MailModel::findFirstById($mail_id);

            if(!$mail_account) {
                return false;
            }

            $this->option_data["old-forward-addr"] = $mail_account->forward_addr;

            $update_data = [];

            $mailbox_quota = $this->getPost("mailbox_quota");
            if($this->getPost("unlimited_quota") == ON) {
                $mailbox_quota = UNLIMITED;
            }
            $update_data['mailbox_quota'] = $mailbox_quota;

            if($this->getPost("forward") != ON) {
                $update_data['forward'] = PASSIVE;
                $update_data['forward_addr'] = "";
            }
            else {
                $update_data['forward'] = ACTIVE;
                $update_data['forward_addr'] = $this->getPost("forward_addr");
                $this->option_data['forward'] = ON;
                $this->option_data['forward-addr'] = $this->getPost("forward_addr");
            }

            if($this->getPost("password") != null) {
                $password = $this->crypto->bencode($this->getPost("password"));
                $update_data['password'] = $password;
                $mail_account_password = trim($this->crypto->bdecode($mail_account->password));
                if($mail_account_password != $this->getPost("password")) {
                    $this->change_password = true;
                }
            }

            if($this->getPost("autoresponder") != ON) {
                $update_data['autoresponder'] = PASSIVE;
            }
            else {
                $update_data['autoresponder'] = ON;
                $this->option_data['autoresponder'] = ON;
            }

            if($this->getPost("status") != ON) {
                $update_data['status'] = "0";
                $this->option_data['status'] = PASSIVE;
            }
            else {
                $update_data['status'] = ACTIVE;
                $this->option_data['status'] = ACTIVE;
            }

            $mail_account->assign($update_data);

            if(!$mail_account->update()) {
                foreach ($mail_account->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Mails->updateMailAccount",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            $this->updateMailAccountCurl();
            $this->updateMailSettingsCurl();
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->updateMailAccount",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function deleteMailAccount(): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            $mail_id = $this->getPost("mail_id");
            $mail = MailModel::findFirstById($mail_id);
            if($mail) {
                $this->setPost("mail_account", $mail->mail_name);
                $mail_alias = MailAlias::find("mail_id = '$mail_id'");

                if($mail_alias) {
                    $mail_alias_data = $mail_alias->toArray();

                    if(!$mail_alias->delete()) {
                        foreach($mail_alias->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        return false;
                    }
                    if(!$mail->delete()) {
                        foreach($mail->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        $this->log->error([
                            "Module" => "Mails->deleteMailAccount",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    foreach($mail_alias_data as $value) {
                        $this->setPost("alias_name", $value['alias_name']);
                        $this->removeMailAliasCurl();
                    }
                }
                $this->removeMailAccountCurl();
                return true;
            }
            else {
                return false;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->deleteMailAccount",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function createMailAlias($restore = false): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            if(!$restore) {
                $status = $this->validation->Required(array(array('field' => 'mail_alias', 'item' => $this->getPost("alias_name"),__('Mail Address is required'))))
                    ->Email($this->getPost("alias_name").'@'.$this->getPost("domain_name"),'mail_alias',__('Please enter valid email address'))
                    ->UniqueMailAddress($this->getPost("alias_name"), $this->getPost("domain_id"),'mail_alias', __("Please enter unique mail address"))
                    ->Validate();

                if(!$status) {
                    $this->error_messages =  $this->validation->getMessage();
                    return false;
                }
            }

            $mail_alias = new MailAlias();
            $mail_alias->assign(array(
                "id"                => $this->env->createUuid(),
                'alias_name'        => $this->getPost("alias_name"),
                'mail_id'           => $this->getPost("mail_id"),
            ));

            if(!$mail_alias->create()) {
                foreach ($mail_alias->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Mails->createMailAlias",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $this->createMailAliasCurl();
                return true;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->createMailAlias",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function updateMailAlias(): bool
    {
        try {
            $alias_id = $this->getPost("alias_id");

            $status = $this->validation->Required(array(array('field' => 'mail_alias', 'item' => $this->getPost("alias_name"),__('Mail Address is required'))))
                ->Email($this->getPost("alias_name").'@'.$this->getPost("domain_name"),'mail_alias',__('Please enter valid email address'))
                ->UniqueMailAddress($this->getPost("alias_name"), $this->getPost("domain_id"),'mail_alias',null,true, $this->getPost("old_aliasname"))
                ->Validate();

            if(!$status) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }

            $mail_alias = MailAlias::findFirstById($alias_id);

            if($this->getPost("alias_name") != null) {
                $mail_alias->assign(array(
                    'id'                => $alias_id,
                    'alias_name'        => $this->getPost("alias_name"),
                ));
            }
            else {
                $this->error_messages['mail_alias'] = __('Alias name is required');
                return false;
            }

            if(!$mail_alias->update()) {
                foreach ($mail_alias->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Mails->updateMailAlias",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $this->removeMailAliasCurl();
                $this->createMailAliasCurl();
                return true;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->updateMailAlias",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function deleteMailAlias(): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            $domain_id = $this->getPost("domain_id");
            $alias_id = $this->getPost("alias_id");
            $mail_id = $this->getPost("mail_id");

            $mail = MailModel::findFirst(array("id='$mail_id'", "domain_id" => "$domain_id"));
            if($mail) {
                $this->setPost("mail_account", $mail->mail_name);
                $mail_alias = MailAlias::findFirst(array("id='$alias_id'", "mail_id" => "$mail_id"));
                if($mail_alias) {
                    $this->setPost("alias_name", $mail_alias->alias_name);
                    if(!$mail_alias->delete()) {
                        foreach($mail_alias->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        $this->log->error([
                            "Module" => "Mails->deleteMailAlias",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    $this->removeMailAliasCurl();
                }
                return true;
            }
            else {
                return false;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->deleteMailAlias",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return false;
        }
    }

    public function listAccount($domain_id = null, $nested = null): array
    {
        try {
            $datatable = new Datatable();
            if($nested) {
                $delete_button = $datatable->buttons("mail/delete/$1/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $edit_button = $datatable->buttons("mail/edit/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
                $user_button = $datatable->buttons("mail/alias/$1/domain/$domain_id",'normal',array('i' => 'fa fa-user','a' => 'text-primary'),__('Aliases'),null,'data-id="nested"');
            }
            else {
                $delete_button = $datatable->buttons('mail/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $edit_button = $datatable->buttons('mail/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
                $user_button = $datatable->buttons("mail/alias/$1",'normal',array('i' => 'fa fa-user','a' => 'text-primary'),__('Aliases'),null,'data-id="nested"');
            }

            $datatable->select(array(
                'mails.id as mail_id',
                'mails.mail_name',
                'mails.mailbox_quota',
                'mails.forward_addr',
                '(SELECT COUNT(*) FROM mail_alias WHERE mail_alias.mail_id = mails.id) as mail_alias_count',
                'mails.status',
                'domains.name',
                'mails.id',
                'mails.forward'
            ))
                ->from("mails")
                ->innerJoin("domains","domains.id = mails.domain_id")
                ->where("mails.domain_id = '$domain_id'")
                ->searchColumn(array('mails.mail_name','mails.forward_addr'))
                ->addColumn('actions', $edit_button.$delete_button.$user_button,'mails.id')
                ->editColumn('mail_id','<input type="checkbox" class="checkbox-blue" value="$1" name="mails[]">','mail_id');

            if($nested) {
                $datatable->editColumn('mails.mail_name','<a href="'. 'mail/edit/$1/domain/'.$domain_id.'">$2'.'@'.'$3</a>', 'mails.id, mails.mail_name,domains.name');
            }
            else {
                $datatable->editColumn('mails.mail_name','<a href="'. 'mail/edit/$1">$2'.'@'.'$3</a>', 'mails.id, mails.mail_name,domains.name');
            }

            $data = $datatable->unsetColumns('domains.name')
                ->unsetColumns('mails.forward')
                ->unsetColumns('mails.id')
                ->generate()
                ->execute()
                ->result();

            foreach($data['aaData'] as $key => $value){
                if ($value[2] == UNLIMITED){
                    $data['aaData'][$key][2] = '<span class="label label-success"> '. __(ucfirst(UNLIMITED)) .' </span>';
                }
                else {
                    $data['aaData'][$key][2] = $value[2]. ' MB';
                }


                if ($value[3] == null){
                    $data['aaData'][$key][3] = '<span class="label label-info"> '. __('None') .' </span>';
                }
                if($value[5] == PASSIVE)
                    $data['aaData'][$key][5] = '<span class="badge badge-danger">'. __(STATUSES[PASSIVE]) .'</span>';
                else
                    $data['aaData'][$key][5] = '<span class="badge badge-success">'. __(STATUSES[ACTIVE]) .'</span>';

                $data['aaData'][$key][6] = $value[9];
                unset($data['aaData'][$key][7]);
                unset($data['aaData'][$key][8]);
                unset($data['aaData'][$key][9]);
            }
            return $data;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->listAccount",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return [];
        }
    }

    public function listAlias($mail_id, $domain_id = null, $nested = null): array
    {
        try {
            $datatable = new Datatable();
            if($nested) {
                $delete_button = $datatable->buttons('mail/deletealias/$1/$2/domain/'.$domain_id,'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $edit_button = $datatable->buttons('mail/editalias/$1/$2/domain/'.$domain_id,'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
            }
            else {
                $delete_button = $datatable->buttons('mail/deletealias/$1/$2','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $edit_button = $datatable->buttons('mail/editalias/$1/$2','normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
            }

            $datatable->select(array('mail_alias.id as alias_id','mail_alias.alias_name','mail_alias.id','domains.name','mails.id'))
                ->from("mail_alias")
                ->innerJoin("mails","mails.id = mail_alias.mail_id")
                ->innerJoin("domains","domains.id = mails.domain_id")
                ->where("mails.domain_id = '$domain_id'")
                ->andWhere("mail_alias.mail_id = '$mail_id'")
                ->searchColumn(array('mail_alias.alias_name'))
                ->editColumn('alias_id','<input type="checkbox" class="checkbox-blue" value="$1" name="aliases[]">','alias_id')
                ->addColumn('actions', $edit_button.$delete_button,'mail_alias.id,mails.id');

            if($nested) {
                $datatable->editColumn('mail_alias.alias_name','<a href="'. 'mail/editalias/$1/'.$mail_id.'/domain/'.$domain_id.'">$2'.'@'.'$3</a>', 'mail_alias.id, mail_alias.alias_name,domains.name');
            }
            else {
                $datatable->editColumn('mail_alias.alias_name','<a href="'. 'mail/editalias/$1/'.$mail_id.'">$2'.'@'.'$3</a>', 'mail_alias.id, mail_alias.alias_name,domains.name');
            }


            $data = $datatable->unsetColumns('mail_alias.id')
                ->unsetColumns('domains.name')
                ->unsetColumns('mails.id')
                ->generate()
                ->execute()
                ->result();

            foreach($data['aaData'] as $key => $value) {
                $data['aaData'][$key][2] = $value[5];
                unset($data['aaData'][$key][3]);
                unset($data['aaData'][$key][4]);
                unset($data['aaData'][$key][5]);
            }

            return $data;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Mails->listAlias",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
//            $this->db->rollback();
            return [];
        }
    }

    private function mailSettingCurl()
    {
        try {
            $mail = new MailManager();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $mail->setServerId($domain->mail_server_id);
            $params = array(
                'domain' => $this->getPost("domain_name"),
            );
            if($this->getPost("forward_to_address") == ON) {
                $params['options'] = "forward";

                if($this->getPost("mail_address") != null) {
                    $params['mail-addr'] = $this->getPost("mail_address").'@'.$this->getPost("domain_name");
                }
                else {
                    $params['options'] = "forward";
                }

            }
            else if($this->getPost("reject_incoming") == ON) {
                $params['options'] = "reject";
            }
            else {
                $params['options'] = "default";
            }
            $mail->domainSettings($params);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function createMailAccountCurl()
    {
        try {
            $mail = new MailManager();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $mail->setServerId($domain->mail_server_id);
            $params = array(
                'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
                'passwd' => $this->getPost("password"),
            );

            if($this->overuse == ACTIVE) {
                $params['quota'] = UNLIMITED;
            }
            else {
                $params['quota'] = $this->getPost("mailbox_quota");
            }

            if($this->getPost("status") == ON) {
                $params['status'] = ON;
            }
            else {
                $params['status'] = OFF;
            }
            $mail->createMail($params);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function updateMailAccountCurl()
    {
        try {
            $mail = new MailManager();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $mail->setServerId($domain->mail_server_id);
            $params = array(
                'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
            );

            if($this->overuse == ACTIVE) {
                $params['quota'] = UNLIMITED;
            }
            else {
                if($this->getPost("unlimited_quota") == ON) {
                    $params['quota'] = UNLIMITED;
                }
                else {
                    $params['quota'] = $this->getPost("mailbox_quota");
                }
            }
            if($this->change_password) {
                $params['new-passwd'] = $this->getPost("password");
            }

            $mail->updateMail($params);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function removeMailAccountCurl()
    {
        try {
            $mail = new MailManager();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $mail->setServerId($domain->mail_server_id);
            $params = array(
                'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
            );

            $mail->removeMail($params);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function createMailAliasCurl()
    {
        try {
            $mail = new MailManager();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $mail->setServerId($domain->mail_server_id);
            $params = array(
                'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
                'mail-alias'    => $this->getPost("alias_name").'@'.$this->getPost("domain_name"),
            );
            $mail->createAlias($params);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function removeMailAliasCurl()
    {
        try {
            $mail = new MailManager();

            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $mail->setServerId($domain->mail_server_id);
            $params = array(
                'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
                'mail-alias'    => $this->getPost("alias_name").'@'.$this->getPost("domain_name"),
            );
            $mail->removeAlias($params);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function updateMailSettingsCurl()
    {
        try {
            $mail = new MailManager();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $mail->setServerId($domain->mail_server_id);

            if(isset($this->option_data['old-forward-addr']) && $this->option_data['old-forward-addr'] != null) {
                $params = array(
                    'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
                    'mail-forward' => $this->option_data['old-forward-addr']
                );
                $mail->removeForward($params);

            }
            if($this->option_data['forward'] == "on") {
                if(isset($this->option_data['forward-addr']) && $this->option_data['forward-addr'] != null) {
                    $params = array(
                        'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
                        'mail-forward' => $this->option_data['forward-addr']
                    );
                    $mail->createForward($params);
                }
            }

            if($this->option_data['autoresponder'] == ON) {
                $params = array(
                    'mail-addr' => $this->getPost("mail_account").'@'.$this->getPost("domain_name"),
                    'autoresponder' => ON
                );
                $mail->mailAddressSettingsAutoResponder($params);
            }
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function changeMailSettings($mail_service_status, $domain_id): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            $services = DomainServices::findFirst("domain_id = '$domain_id'");
            $domain = Domains::findFirstById($domain_id);
            $mailmng = new Mailmng();
            $mailmng->setServerId($domain->mail_server_id);
            $params = array(
                'domain' => $domain->name
            );

            if($mail_service_status == ACTIVE) {
                $mailmng->unsuspendDomain($params);
            }
            else {
                $mailmng->suspendDomain($params);
            }
            $services->mail = $mail_service_status;
            if (!$services->update()) {
                foreach ($services->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Mails->changeMailSettings DomainServices",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            return true;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}