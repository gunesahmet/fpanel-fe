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
 */namespace FPanel\Library;

use FPanel\Globals\Globals;
use FPanel\Models\SuccessLogins;
use Phalcon\Mvc\User\Component;

/**
 * Class Library
 * @package FPanel\Library
 */
abstract class Library extends Component
{
    protected $error_messages   = [];
    protected $debug_messages   = [];
    private   $post             = [];
    protected $active_controller;
    protected $active_action;
    protected $active_module;
    protected $cached;
    protected $demo = false;

    public function __construct()
    {
        if((php_sapi_name() != 'cli' ) && ($this->dispatcher->getControllerName() != "cli") &&
            ($this->dispatcher->getModuleName() != "Cli") &&
            ($this->dispatcher->getModuleName() != "Api")) {
            $this->active_controller = $this->dispatcher->getControllerName();
            $this->active_action = $this->dispatcher->getActionName();
            $this->active_module = $this->dispatcher->getModuleName();
            $this->post = $this->env->getRawPost();
        }
    }

    public function has($element): bool
    {
        if(isset($this->post[$element])) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->error_messages;
    }
    public function getDebugs(): array
    {
        return $this->debug_messages;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setPost($key, $value): Library
    {
        if(!is_array($value))
            $this->post[$key] = $this->filter->sanitize($value, "striptags");
        else {
            $this->post[$key] = $this->stripTags($value);
        }
        return $this;
    }

    /**
     * @param $posts
     * @return $this
     */
    public function setPosts($posts): Library
    {
        $this->post = [];
        foreach ($posts as $key => $value) {
            if(is_string($value)) {
                $this->post[$key] = $this->filter->sanitize($value, "striptags");
            }
            else {
                $this->post[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * @param null $element
     * @param null $default_value
     * @return array|mixed|null
     */
    public function getPost($element = null, $default_value = null)
    {
        if($element != null) {
            if($default_value != null) {
                if(!isset($this->post[$element]) || ($this->post[$element] == null)) {
                    return $default_value;
                }
            }
            else {
                if(!isset($this->post[$element]) || ($this->post[$element] == null)) {
                    return null;
                }
            }
            if(is_array($this->post[$element])) {
                foreach ($this->post[$element] as $key => $value) {
                    if(is_string($value))
                        $this->post[$element][$key] = trim($value);
                    else
                        $this->post[$element][$key] = $value;
                }
            }
            else {
                if(isset($this->post[$element])) {
                    return trim($this->post[$element]);
                }
                else
                    return null;
            }
            return $this->post[$element] ?? null;
        }
        else {
            return $this->post;
        }
    }

    public function checkCsrf()
    {
        $this->checkSessions();
        if($this->panel_installed) {
            if($this->request->isPost()) {
                if(!$this->env->checkCsrf()) {
                    if($this->request->isAjax()) {
                        $this->view->disable();
                        header('Content-Type: application/json');
                        $this->error_messages = Globals::messages();
                        $this->error_messages["type"] = "error";
                        $this->error_messages["login"] = true;
                        $this->error_messages["status"] = false;
                        $this->error_messages["title"] = __("Request has been failed");
                        $this->error_messages["messages"] = __("Your request has been failed. Please reload your browser.");
                        $this->error_messages["request_id"] = $this->request_id;
                        $this->error_messages["redirect_url"] = "/";
                        echo print_r(json_encode($this->error_messages), true);
                        exit(0);
                    }
                    else {
                        $this->auth->remove();
                        $this->flashSession->error(json_encode([__("Your request has been failed.")]));
                        header("Location: /");
                    }
                }
            }
        }
    }

    private function checkSessions()
    {
        if(!file_exists(ROOTPATH . '/.firstboot')) {
            $keys = $this->redis->keys("PHPREDIS_SESSION:*");
            $success_logins = SuccessLogins::find();
            $session_timeout = $this->config->session->timeout;
            $keep_session_keys = [];
            if(!empty($keys)) {
                foreach ($keys as $key) {
                    /**
                     * @var $session SuccessLogins
                     */
                    foreach($success_logins as $success_login) {
                        if("PHPREDIS_SESSION:".$success_login->session == $key) {
                            $keep_session_keys[] = $key;
                        }
                    }
                }
            }


            foreach ($keys as $key) {
                if(in_array($key, $keep_session_keys)) {
                    continue;
                }
                $sess_data = $this->env->unserializeSession($this->redis->get($key));

                if(array_key_exists("identity", $sess_data)) {
                    $this->redis->del($key);
                }
            }

            foreach ($success_logins as $session) {
                foreach ($keep_session_keys as $keep_session_key) {
                    if($session->session == $keep_session_key) {
                        $logged_time_difference = abs(time() - $session->created_date);
                        if($session_timeout != "0") {
                            if($logged_time_difference >= $session_timeout) {
                                $session->delete();
                                if(REDIS_STATUS == true) {
                                    $this->redis->del($keep_session_key);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function stripTags(array $value): array
    {
        foreach ($value as $k => $v) {
            if(!is_array($v)) {
                $value[$k] = $this->filter->sanitize($v, "striptags");
            }
            else {
                $value[$k] = $this->stripTags($v);
            }
        }
        return $value;
    }
}