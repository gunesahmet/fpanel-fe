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
 */namespace FPanel\Auth;

use FPanel\Environments\Environments;
use FPanel\Exceptions\AuthException;
use FPanel\Models\Domains;
use FPanel\Models\FailedLogins;
use FPanel\Models\Mails;
use FPanel\Models\SuccessLogins;
use FPanel\Models\Support;
use FPanel\Models\Users;
use Phalcon\Mvc\User\Component;

/**
 * Class Auth
 * @package FPanel\Auth
 */
class Auth extends Component
{
    private $error_messages = [];

    /**
     * @throws AuthException
     */
    public function check($credentials): bool
    {
        $current_ip = $this->request->getClientAddress();

        if(!$this->panel_installed) {
            $username = "admin";
            $password = "setup";
            if(($username == $credentials['username']) && ($password == $credentials['password'])) {
                $this->session->set('identity', array(
                    'id'        => $this->env->createUuid(),
                    'name'      => "admin",
                    'username'  => "admin",
                    'type'      => ROOT_USER,
                    'parent_id' => "0",
                    'installation' => true,
                    'key'       => $this->getCsrfKey(),
                    'token'     => $this->getCsrfToken(),
                ));
                return true;
            }
            else {
                throw new AuthException(__('Wrong username/password combination'));
            }
        }
        else {
            if((isset($this->config->recaptcha->status)) && ($this->config->recaptcha->status  == "1")) {
                $recaptcha = new Recaptcha();
                $result = $recaptcha->validate();
                if(!$result) {
                    $error = __("Recaptcha verification failed. Please refresh page and try again.");
                    throw new AuthException($error);
                }
            }

            if($credentials['username'] == null) {
                throw new AuthException(__("Username required"));
            }
            if($credentials['password'] == null) {
                throw new AuthException(__('Password is required'));
            }

            if (!filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)) {
                $this->user($credentials, $current_ip);
            }
            else {
                $this->email($credentials);
            }
        }
        return true;
    }

    /**
     * @throws AuthException
     */
    private function user($credentials, $current_ip)
    {
        $user = Users::findFirstByUsername($credentials['username']);

        if(!$user) {
            $this->registerUserThrottling();
            throw new AuthException(__('Wrong username/password combination'));
        }

        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            $this->registerUserThrottling();
            throw new AuthException(__('Wrong username/password combination'));
        }
        else {
//            $this->checkUserFlags($user->status, $user->status_msg);
            $this->saveLogin($user->id, $this->session->getId());

            $user_data = array(
                'id' => $user->id,
                'username' => $user->username,
                'type' => $user->type,
                'parent_id' => $user->parent,
                'limit' => array(),
                'key' => $this->getCsrfKey(),
                'token' => $this->getCsrfToken(),
                'last_login_ip' => $user->last_login_ip,
                'last_login' => $user->last_login
            );

            $user->assign(array(
                'last_login' => $this->date->dateTime(),
                'last_login_ip' => $current_ip,
            ));

            if(!$user->update()) {
                $error_messages = [];
                foreach($user->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "Auth.php",
                    "Error" => "User update failed",
                    "Data" => $error_messages
                ]);
                throw new AuthException(__('User update failed'));
            }

            if(($user->parent != '0')) {
                $parent = Users::findFirstById($user->parent);
                if($parent) {
                    $user_data['parent'] = array(
                        'id' => $parent->id,
                        'type' => $parent->type,
                        'limit' => array(),
                        'parent' => array(),
                    );
                    if($parent->parent != "0") {
                        $top_parent = Users::findFirstById($parent->parent);
                        if($top_parent) {
                            if($top_parent->type != ROOT_USER) {
                                $user_data['parent']['parent'] = array(
                                    'id' => $top_parent->id,
                                    'type' => $top_parent->type,
                                    'limit' => array(),
                                );
                            }
                        }
                    }
                }
            }
            else {
                $this->checkSupportUser($user->type, $user->username);
                $user_data['parent_type'] = ROOT_USER;
            }

            $this->session->set('identity', $user_data);
            $this->session->set('session_time',time());

            if(!$this->fcookies->has("language")) {
                $this->fcookies->set("language", "en_US", time() + (86400 * 365));
                $this->fcookies->set("logged_time", time(), time() + (86400 * 365));
            }
        }

    }

    /**
     * @throws AuthException
     */
    private function email($credentials)
    {
        $exploded = explode('@', $credentials['username']);
        $domain_name = $exploded[1];
        $domains = Domains::findFirst("name = '$domain_name'");
        if($domains) {
            if($domains->status != ACTIVE) {
                $this->registerUserThrottling();
                throw new AuthException(__('Domain status is passive. Please contact with system administrator.'));
            }

            $emails = Mails::findFirst("mail_name = '$exploded[0]' AND domain_id = '$domains->id'");
            if($emails) {
                if($credentials['password'] == trim($this->crypto->bdecode($emails->password))) {
                    $user_data = array(
                        'id'        => $emails->id,
                        'username'  => $emails->mail_name,
                        'type'      => EMAIL_USER,
                        'domain_id' => $emails->domain_id,
                        'key'       => $this->getCsrfKey(),
                        'token'     => $this->getCsrfToken(),
                    );
                    $this->session->set('identity', $user_data);
                    $this->session->set('session_time',time());

                    if(!$this->fcookies->has("language")) {
                        $this->fcookies->set("language", "en_US", time() + (86400 * 365));
                        $this->fcookies->set("logged_time", time(), time() + (86400 * 365));
                    }

                }
                else {
                    $this->registerUserThrottling();
                    throw new AuthException(__('Wrong username/password combination'));
                }
            }
            else {
                $this->registerUserThrottling();
                throw new AuthException(__('Wrong username/password combination'));
            }
        }
        else {
            $this->registerUserThrottling();
            throw new AuthException(__('Wrong username/password combination'));
        }
    }

    /**
     * @throws AuthException
     */
    public function saveLogin($user_id, $session): bool
    {
        $session_search = SuccessLogins::findFirst("session = '$session'");
        $ip_address = $this->request->getClientAddress();
        $failed_logins = FailedLogins::find("session_id = '$session' AND ip_address = '$ip_address'");
        if($failed_logins) {
            if(!$failed_logins->delete()) {
                return false;
            }
        }
        if($session_search) {
            $session_search->assign(array(
                'session_id' => $session,
                'ip_address' => $this->request->getClientAddress(),
                'logged_time' => $this->date->dateTime()
            ));

            if(!$session_search->update()) {
                $error_messages = [];
                foreach($session_search->getMessages() as $message) {
                    $error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "User update failed",
                    "Error" => "User update failed",
                    "Data" => $error_messages
                ]);
                throw new AuthException($error_messages[0]);
            }
        }
        else {
            $successLogin = new SuccessLogins();
            $successLogin->assign(array(
                'user_id'       => $user_id,
                'ip_address'    => $this->request->getClientAddress(),
                'logged_time'   => $this->date->dateTime(),
                'session'       => $session,
            ));
            if (!$successLogin->create()) {
                $messages = $successLogin->getMessages();
                throw new AuthException($messages[0]);
            }
        }
        return true;
    }

    public function registerUserThrottling(): bool
    {
        $failed_login = new FailedLogins();
        $failed_login->assign([
            "session_id" => $this->session->getId(),
            "ip_address" => $this->request->getClientAddress(),
        ]);
        if (!$failed_login->create()) {
            foreach ($failed_login->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }
        $this->log->info([
            $this->request->getClientAddress()
        ]);
        $attempts = FailedLogins::count(
            array(
                "ip_address" => $this->request->getClientAddress(),
                "session_id" => $this->session->getId()
            ));

        if((!isset($this->config->recaptcha->status)) || ($this->config->recaptcha->status  != "1")) {
            $captcha_attempts = "3";
        }
        else {
            $captcha_attempts = $this->config->recaptcha->attempts;
        }

        for ($i = 1; $i < $attempts; $i++) {
            if($attempts >= $captcha_attempts) {
                $this->view->setVar("captcha",true);
                if($this->config->recaptcha->status  == "1") {
                    $this->session->set('captcha',true);
                }
                break;
            }
        }
        return true;
    }

    public function checkUserFlags($status, $status_message)
    {
//        if ($status != '1') {
//            if($status != '-1')
//                throw new Exception(__('This user is inactive') . "\n" . $status_message);
//        }
    }

    public function remove()
    {
        // @TODO
        $this->redis->select(0);
        $session_id = $this->session->getId();
        $this->redis->del("PHPREDIS_SESSION:".$session_id);
        if(!file_exists(ROOTPATH . '/.firstboot')) {
            $success_logins = SuccessLogins::findFirst("session = '$session_id'");
            if($success_logins) {
                $success_logins->delete();
            }
        }
        $this->session->destroy();
        $this->response->redirect("/",true);
    }

    public function getUsername()
    {
        $identity = $this->session->get('identity');
        return $identity['username'];
    }

    public function getUserId()
    {
        $identity = $this->session->get('identity');
        return $identity['id'];
    }

    public function getIdentity()
    {
        return $this->session->get('identity');
    }

    public function getParentId()
    {
        $identity = $this->session->get('identity');
        return $identity['parent_id'];
    }

    public function getUserToken()
    {
        $identity = $this->session->get('identity');
        return $identity['token'];
    }

    public function getUserType()
    {
        $identity = $this->session->get('identity');
        return $identity['type'];
    }

    public function getUserStatus()
    {
        $identity = $this->session->get('identity');
        return $identity['status'];
    }

    public function checkAuth(): bool
    {
        $env = new Environments();
        $status = false;
        if(!$this->session->has('identity')) {
            if($this->fcookies->has("ENCODED_SESS")) {
                if($this->fcookies->get("ENCODED_SESS") != null) {
                    $sess_arr = json_decode($this->fcookies->get("ENCODED_SESS"), true);
                    if(json_last_error() == JSON_ERROR_NONE) {
                        $this->redis->select(0);
                        $session_data = $this->redis->get("PHPREDIS_SESSION:".$sess_arr["session_id"]);
                        $decoded_sess_data = $env->unserializeSession($session_data);
                        if(isset($decoded_sess_data["identity"])) {
                            if($decoded_sess_data["identity"] != null) {
                                $this->session->set('identity', $decoded_sess_data["identity"]);
                                $this->encodeCookie();
                                $status = true;
                            }
                        }
                    }
                }
            }
        }
        else {
            if($this->session->get('identity') == null) {
                if($this->fcookies->has("ENCODED_SESS")) {
                    if($this->fcookies->get("ENCODED_SESS") != null) {
                        $sess_arr = json_decode($this->fcookies->get("ENCODED_SESS"), true);
                        if(json_last_error() == JSON_ERROR_NONE) {
                            $this->redis->select(0);
                            $session_data = $this->redis->get("PHPREDIS_SESSION:".$sess_arr["session_id"]);
                            $decoded_sess_data = $env->unserializeSession($session_data);
                            if($decoded_sess_data["identity"] != null) {
                                $this->session->set('identity', $decoded_sess_data["identity"]);
                                $this->encodeCookie();
                                $status = true;
                            }
                        }
                    }
                }
            }
            else {
                $status = true;
            }
        }

        if(!$status) {
            return false;
        }
        else {
            $this->encodeCookie();
        }

        return true;
    }

    private function checkSupportUser($user_type, $username)
    {
        if($user_type == SUPPORT_USER) {
            $support =  Support::findFirst("user = '$username'");
            if($support) {
                $support->assign(array(
                    'connecting_status' => '1'
                ));
                if(!$support->update()) {
                    $error_messages = [];
                    foreach($support->getMessages() as $message) {
                        $error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "File" => "Support User update failed",
                        "Error" => "Support User update failed",
                        "Data" => $error_messages
                    ]);
                }
            }
        }
    }


    private function encodeCookie()
    {
        $data = array(
            'session_id' => $this->session->getId(),
            'username' => $this->getUsername(),
            'user_id' => $this->getUserId(),
            'token' => $this->getUserToken(),
        );
        $status = false;
        foreach($this->fcookies->get() as $name => $cookie) {
            if($name == "ENCODED_SESS") {
                $status = true;
                $this->fcookies->set($name, json_encode($data), time() + (86400 * 365));
            }
        }

        if(!$status) {
            $this->fcookies->set("ENCODED_SESS", json_encode($data), time() + (86400 * 365));
        }
    }

    public function setCsrfKey()
    {
        $this->session->set('$FP/CSRF/KEY$', $this->env->generateCsrf());
    }

    public function setCsrfToken()
    {
        $this->session->set('$FP/CSRF$', $this->env->generateCsrf());
    }

    public function getCsrfKey()
    {
        return $this->session->get('$FP/CSRF/KEY$');
    }

    public function getCsrfToken()
    {
        return $this->session->get('$FP/CSRF$');
    }
}