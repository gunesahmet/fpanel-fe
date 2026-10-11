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
use FPanel\Environments\Parser;
use FPanel\Environments\Smtp;
use FPanel\Library\Library;
use FPanel\Models\IpAddress;
use FPanel\Models\SystemSettings;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use FPanel\Process\Admin;

/**
 * Class Session
 * @package FPanel\Session
 */
class Support extends Library
{
    public $parent_id;
    public $user_id;
    public $user_type;
    protected $request_user;
    protected $request_user_email;
    protected $subject;
    protected $support_message;
    protected $support_user;
    protected $support_user_password;
    protected $created_date;
    protected $ip_addresses;
    protected $default_ip_addresses;
    protected $disable_logins;
    protected $message_body;
    protected $update_support_status;
    protected $support_status;
    use Templates;

    public function supportList(): array
    {
        $datatable = new Datatable();

        $connected_support = \FPanel\Models\Support::findFirst("connecting_status = '1'");
        $conn_state = false;
        if($connected_support) {
            $conn_state = true;
        }
        $data = $datatable->select(array('support.id',
            'support.status',
            'support.title',
            'support.created_date',
            'support.closed_date',
            'support.user_msg',
            'support.connecting_status',
            'support.disable_clients_logins'))
            ->from("support")
            ->generate()
            ->execute()
            ->result();


        foreach ($data['aaData'] as $key => $value) {

            if($conn_state == true)
                $support = $value[2];
            else
                $support = '<a data-id="nested" href="' . $this->uri->url_encrypt('support/edit/' . $value[0]) . '">' . $value[2] . '</a>';

            if($value[1] == ACTIVE) {
                $status = '<span class="label label-success"> '. __('Active') .' </span>';
            }
            else {
                $status = '<span class="label label-danger"> '. __('Closed') .' </span>';
            }

            $created_date = $value[3];
            if($value[4] != null) {
                $closed_date = $value[4];
            }
            else {
                $closed_date = __('Not closed');
            }

            $closed_message = $value[5];

            if($value[6] == "1") {
                $connect_status = '<span class="label label-primary"> '. __('Connected') .' </span>';
            }
            else {
                $connect_status = '<span class="label label-danger"> '. __('Not Connected') .' </span>';
            }

            if($value[7] == "1") {
                $disable_logins = '<span class="label label-danger"> '. __('Not Allowed') .' </span>';
            }
            else {
                $disable_logins = '<span class="label label-info"> '. __('Allowed') .' </span>';
            }


            $data['aaData'][$key][0] = $support;
            $data['aaData'][$key][1] = $status;
            $data['aaData'][$key][2] = $created_date;
            $data['aaData'][$key][3] = $closed_date;
            $data['aaData'][$key][4] = $closed_message;
            $data['aaData'][$key][5] = $connect_status;
            $data['aaData'][$key][6] = $disable_logins;
        }
        return $data;
    }

    public function createSupport(): bool
    {
        $username = $this->getPost("username");
        $password = $this->getPost("password");
        $request_user = $this->getPost("request_user");
        $request_user_email = $this->getPost("request_user_email");

        $support_message = $this->getPost("support_message");
        $support_status = $this->getPost("support_status");
        $disable_logins = $this->getPost("disable_logins");
        $support_title = $this->getPost("title");

        $created_date = time();

        if($this->validate() == false)
            return false;

        $support_user_id = $this->createSystemUser($username, $password);

        $send_status = PASSIVE;
        if($support_status != ON) {
            $support_status = PASSIVE;
        }
        else {
            $support_status = ACTIVE;
            $send_status = ACTIVE;
        }

        if($disable_logins != ON) {
            $disable_logins = PASSIVE;
        }
        else {
            $disable_logins = ACTIVE;
        }

        if($support_status == ACTIVE) {
            $this->request_user = $this->getPost("request_user");
            $this->request_user_email = $request_user_email;
            $this->subject = $this->getPost("title");
            $this->support_user = $username;
            $this->created_date = $created_date;
            $this->support_user_password = $password;
            $this->support_message = $support_message;
            $this->update_support_status = "First Time";
            $this->support_status = "Active";

            if($this->config->application->type == CLOUD) {
                $server_ip = IpAddress::find("type = '4'");
                if(!empty($server_ip)) {
                    foreach ($server_ip as $ip) {
                        if($this->default_ip_addresses == null) {
                            $this->default_ip_addresses = $ip->ip_address;
                        }
                        else {
                            $this->default_ip_addresses .= ', '. $ip->ip_address;
                        }
                    }
                }
                else {
                    $this->error_messages["content"] = __("Cloud ips can't get");
                    return false;
                }
            }
            $server_ip = IpAddress::find();
            $server_ip_list = "";
            if(!empty($server_ip)) {
                foreach ($server_ip as $ip) {
                    if($server_ip_list == null) {
                        $server_ip_list = $ip->ip_address;
                    }
                    else {
                        $server_ip_list .= ', '. $ip->ip_address;
                    }
                }
            }
            else {
                $this->error_messages["content"] = __("Server ips can't get");
                return false;
            }

            if($disable_logins == PASSIVE) {
                $this->disable_logins = "Disable";
            }
            else {
                $this->disable_logins = "Allowed";
            }
            $this->ip_addresses = $server_ip_list;
        }

        $support = new \FPanel\Models\Support();

        $assign = array(
            'request_user' => $request_user,
            'request_user_email' => $request_user_email,
            'status' => $support_status,
            'title' => $support_title,
            'description' => $support_message,
            'user'   => $username,
            'password' => $password,
            'connecting_status' => PASSIVE,
            'disable_clients_logins' => $disable_logins
        );
        $support->assign($assign);
        if (!$support->create()) {
            foreach ($support->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Support->createSupport",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            if($support_status == ACTIVE) {
                $created_id = $support->id;
                $result = $this->createSupportCurl();
                if(trim($result) != null) {
                    $this->error_messages['Error'] = $result;
                    $support = \FPanel\Models\Support::findFirstById($created_id);
                    if($support) {
                        if(!$support->delete()) {
                            foreach($support->getMessages() as $title => $message) {
                                $this->error_messages[$title] = $message;
                            }
                            return false;
                        }
                        if($support_user_id != false) {
                            $this->support_user = $username;
                            $this->removeSupportUser($support_user_id);
                            $this->removeSupportcurl();
                        }
                    }
                    return false;
                }
                else {
                    $this->requestEmailTemplate();
                    $status =  $this->sendEmail($send_status);
                    if(!$status) {
                        $this->removeSupportUser($support_user_id);
                    }
                }
            }
        }
        return true;
    }

    public function updateSupport(): bool
    {
        $id = $this->getPost("id");
        $request_user = $this->getPost("request_user");
        $request_user_email = $this->getPost("request_user_email");
        $support_message = $this->getPost("support_message");
        $support_status = $this->getPost("support_status");
        $disable_logins = $this->getPost("disable_logins");

        if($support_message == null) {
            $this->error_messages['support_message'] = __('Support message is required');
            return false;
        }

        if($this->validateEmail($request_user_email) == false)
            return false;

        if($support_status != ON) {
            $support_status = PASSIVE;
            $user_message = "Closed by ".$request_user;
            $closed_date = $this->date->dateTime();
        }
        else {
            $support_status = ACTIVE;
            $user_message = "";
            $closed_date = null;
        }
        if($disable_logins != ON) {
            $disable_logins = PASSIVE;
        }
        else {
            $disable_logins = ACTIVE;
        }

        $support = \FPanel\Models\Support::findFirstById($id);
        if($support) {
            $support->status = $support_status;
            $support->request_user_email = $request_user_email;
            $support->request_user = $request_user;
            $support->user_msg = $user_message;
            $support->description = $support_message;

            if($closed_date != null) {
                $support->closed_date = $closed_date;
            }

            if (!$support->update()) {
                foreach ($support->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Support->updateSupport",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $this->error_messages['support'] = __('Support request not found');
            return false;
        }

        if($support_status == ACTIVE) {
            $support_status = "Active";
            $send_status = UPDATED;
        }
        else {
            $this->removeSystemUser($support->user);
            $this->support_user = $support->user;
            $this->removeSupportcurl();
            $support_status = "Closed";
            $send_status = PASSIVE;
        }

        $this->request_user = $request_user;
        $this->request_user_email = $request_user_email;
        $this->subject = $support->title;
        $this->support_user = $support->user;
        $this->created_date = $support->created_date;
        $this->support_user_password = $support->password;
        $this->support_message = $support_message;
        $this->update_support_status = "Updated by ".$request_user;
        $this->support_status = $support_status;



        if($this->config->application->type == CLOUD) {
            $server_ip = IpAddress::find("type = '4'");
            if(!empty($server_ip)) {
                foreach ($server_ip as $ip) {
                    if($this->default_ip_addresses == null) {
                        $this->default_ip_addresses = $ip->ip_address;
                    }
                    else {
                        $this->default_ip_addresses .= ', '. $ip->ip_address;
                    }
                }
            }
            else {
                $this->error_messages["content"] = __("Cloud ips can't get");
                return false;
            }
        }
        $server_ip = IpAddress::find();
        $server_ip_list = "";
        if(!empty($server_ip)) {
            foreach ($server_ip as $ip) {
                if($server_ip_list == null) {
                    $server_ip_list = $ip->ip_address;
                }
                else {
                    $server_ip_list .= ', '. $ip->ip_address;
                }
            }
        }
        else {
            $this->error_messages["content"] = __("Server ips can't get");
            return false;
        }
        if($disable_logins == PASSIVE) {
            $this->disable_logins = "Disable";
        }
        else {
            $this->disable_logins = "Allowed";
        }
        $this->ip_addresses = $server_ip_list;
        $this->requestEmailTemplate();
        return $this->sendEmail($send_status);
    }

    public function validatePassword($password): bool
    {
        if($password != null) {
            if (preg_match('/\s/', $password)) {
                $this->error_messages['password'] = __('Password is not valid');
                return false;
            }
            else {
                if(strlen($password) < 5) {
                    $this->error_messages['password'] = __('Password is not valid. Password must be minimum 5 characters');
                    return false;
                }
                else {
                    if(strlen($password) > 12) {
                        $this->error_messages['password'] = __('Password is not valid. Password must be maximum 12 characters');
                        return false;
                    }
                }
            }
        }
        else {
            $this->error_messages['password'] = __('Password is required');
            return false;
        }
        return true;
    }

    private function validateEmail($email): bool
    {
        if($email != null) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error_messages['request_user_email'] = __('Invalid email format');
                return false;
            }
        }
        else {
            $this->error_messages['request_user_email'] = __('Email Address is required');
            return false;
        }
        return true;
    }

    private function removeSupportUser($support_user_id)
    {
        $user_data = Userdata::findFirst("user_id = '$support_user_id'");
        if(!$user_data->delete()) {
            foreach($user_data->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        $user = Users::findFirstById($support_user_id);
        if(!$user->delete()) {
            foreach($user->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
    }

    public function sendEmail($support_status = PASSIVE): bool
    {
        $smtp = new Smtp();
        $smtp->host = $this->getPost("notify_settings")["host"];
        $smtp->port = $this->getPost("notify_settings")["port"];
        $smtp->username = $this->getPost("notify_settings")["email"];
        $smtp->password = $this->crypto->bdecode($this->getPost("notify_settings")["password"]);
        $smtp->from = array(
            $this->getPost("request_user_email") => $this->getPost("notify_settings")["from_fullname"],
        );

        $smtp->recipients[] = array(
            'address' => SUPPORT_MAIL,
            'name' => 'Fpanel Software Support'
        );
        if($support_status == ACTIVE) // Yeni talep
            $smtp->subject = "Support Request";
        else if($support_status == PASSIVE) // Talep iptali
            $smtp->subject = "Support Request Canceled";
        else if($support_status == UPDATED) // Talep guncellemesi
            $smtp->subject = "Support Request Updated";
        else if($support_status == CLOSED) // Talep guncellemesi
            $smtp->subject = "Support Request Closed";
        $smtp->message = $this->message_body;

        $status = $smtp->Send();
        if(!$status) {
            $this->error_messages["content"] = $smtp->getErrorMessage();
            $this->debug->debug($smtp->getErrorMessage(), false);
            return false;
        }
        return true;
    }


    public function createSupportCurl()
    {
        $adminmng = new Admin();
        $params = array(
            'user' => $this->support_user,
            'passwd' => $this->support_user_password
        );
        return $adminmng->createSupport($params);
    }

    public function removeSupportcurl()
    {
        $adminmng = new Admin();
        $params = array(
            'user' => $this->support_user,
        );
        return $adminmng->removeSupport($params);
    }

    public function createSystemUser($username, $password)
    {
        $user = new Users();
        $user->assign(array(
            "id"            => $this->env->createUuid(),
            'username'      => $username,
            'password'      => $this->security->hash($password),
            'type'          => SUPPORT_USER,
            'must_change_password '   => PASSIVE,
            'status'        => ACTIVE,
            'parent'        => '0',
            'interface'     => "default",
            'language'      => "en_US",
            'api_status'    => PASSIVE,
        ));
        if(!$user->create()) {
            foreach ($user->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Support->createSystemUser",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            $user_data = new Userdata();
            $user_data->assign([
                'fullname'  => "Support User",
                'email'     => "support@fpanel.net",
                'user_id' => $user->id
            ]);
            if(!$user_data->create()) {
                foreach ($user_data->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Support->createSystemUser userdata",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return $user->id;
        }
    }

    public function removeSystemUser($username): bool
    {
        $user = Users::findFirst("username = '$username'");
        if($user) {
            $user_data = Userdata::findFirst("user_id = '$user->id'");
            if(!$user_data->delete()) {
                foreach($user_data->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Support->removeSystemUser userdata",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
            if(!$user->delete()) {
                foreach($user->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Support->removeSystemUser",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
        return true;
    }

    public function closeSupport(): bool
    {
        if($this->getPost("user_message") == null) {
            $this->error_messages['user_message'] = __('User message can not be null');
            return false;
        }
        $support = \FPanel\Models\Support::findFirst("status = '1'");
        if($support) {
            $this->support_user = $support->user;

            if($this->getPost("support_status") == ON) {
                $support->assign(array(
                    'user_msg' => $this->getPost("user_message"),
                    'status' => PASSIVE,
                    'connecting_status' => PASSIVE,
                    'close_date' => $this->date->dateTime()
                ));

                if (!$support->update()) {
                    foreach ($support->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Support->closeSupport",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
                $this->removeSystemUser($this->support_user);
                $this->removeSupportcurl();

                $ini = new Parser(ROOTPATH.'/config/config.ini');
                $ini->data['support']['status'] = "\"false\"";
                $ini->data['support']['logins'] = "\"false\"";
                $ini->write();

                if(REDIS_STATUS == true) {
                    if($this->redis->exists('Users'))
                        $this->redis->del("Users");
                }

                $this->support_status = CLOSED;

            }

            $notifications = SystemSettings::findFirst("key = 'notyfy'");
            $notify_data = [];
            if($notifications) {
                $notify_data = json_decode($notifications->value, true);
                if(empty($notify_data) || isset($notify_data["host"])) {
                    return true;
                }
            }
            $smtp = new Smtp();

            $smtp->from = array(
                $support->request_user_email => $notify_data["from_fullname"],
            );

            $smtp->recipients[] = array(
                'address' => $this->getPost("user_email"),
                'name' => $this->getPost("user_email")
            );

            $smtp->subject = "Support Request Response";
            $this->support_status = 'Updated';
            $this->support_message = $this->getPost("user_message");

            $this->responseEmailTemplate();
            $smtp->message = $this->message_body;

            $status = $smtp->Send();
            if($status == true)
                return true;
            else {
                $this->error_messages['message'] = __('Email can not send');
                return false;
            }
        }
        return true;
    }

    private function validate(): bool
    {
        $required = array(
            array(
                'field' => 'password',
                'item' => $this->getPost("password"),
                'message' => __('Password is required')
            ),
            array(
                'field' => 'support_message',
                'item' => $this->getPost("support_message"),
                'message' => __('Message is required')
            ),
            array(
                'field' => 'title',
                'item' => $this->getPost("title"),
                'message' => __('Message title is required')
            ),
            array(
                'field' => 'username',
                'item' => $this->getPost("username"),
                'message' => __('Username is required')
            ),
            array(
                'field' => 'username',
                'item' => $this->getPost("username"),
                'message' => __('Username is required')
            ),
        );
        $this->validation->Required($required);

        $this->validation->Password($this->getPost("password"),'password')
            ->Username($this->getPost("username"),'username')
            ->Email($this->getPost("request_user_email"),'request_user_email');

        if($this->validation->Validate() == false) {
            foreach ( $this->validation->getMessage() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }
}