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
 */namespace FPanel\Environments;

use Phalcon\Mvc\User\Component;

class Messages extends Component
{
    private $verify_status;
    private $activate_status = false;
    private $verify_message;
    private $activate_message;

    public function getMessages()
    {
        $error_message = "";
        if(!empty($this->flashSession->getMessages(null,false))) {
            foreach ($this->flashSession->getMessages(null, true) as $type => $messages) {
                if($type == "error") {
                    $error_message = '<div class="row"><div class="col-md-12"> <div class="alert alert-danger show alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
                }
                else if($type == "success") {
                    $error_message = '<div class="row"><div class="col-md-12"> <div class="alert alert-success show alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
                }
                else if($type == "warning") {
                    $error_message = '<div class="row"><div class="col-md-12"> <div class="alert alert-warning show alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
                }

                foreach ($messages as $message) {
                    $message_arr = json_decode($message, true);
                    if(json_last_error() == JSON_ERROR_NONE) {
                        foreach ($message_arr as $item => $value) {
                            $error_message .= $item . ": $value<br />";
                        }
                    }
                    else {
                        $error_message .= $message . "<br />";
                    }
                }

                $error_message .= '</div></div></div>';
                echo $error_message;
            }
        }
    }

    public function getMessage($field, bool $remove = false): ?string
    {
        if(!empty($this->flashSession->getMessages("error",$remove))) {
            $errors = $this->flashSession->getMessages("error", $remove);
            if(!empty($errors)) {
                $errors_arr = json_decode($errors[0], true);
                if(!empty($errors_arr)) {
                    if(array_key_exists($field, $errors_arr)) {
                        $error_message = '<div class="row"><div class="col-md-12"> <div class="alert alert-danger show alert-dismissable"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
                        $error_message .= $errors_arr[$field] . "<br />";
                        $error_message .= '</div></div></div>';
                        return $error_message;
                    }
                }
            }
        }
        return null;
    }

    public function getSuccessMessage()
    {
        if(!empty($this->flashSession->getMessages("success",false))) {
            $errors = $this->flashSession->getMessages("success", true);
            $error_message = '<div class="row"><div class="col-md-12"> <div class="alert alert-success alert-dismissible show"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
            $error_message .= $errors[0];
            $error_message .= '</div></div></div>';
            echo $error_message;
        }
    }

    public function getSiteMessages(): array
    {
        $message_result = [];

        if(!empty($this->flashSession->getMessages(null,false))) {
            foreach ($this->flashSession->getMessages() as $type => $messages) {
                $message_result[$type] = [
                    'type' => $type,
                    'messages' => []
                ];

                foreach ($messages as $message) {
                    $message_result[$type]['messages'][] = $message;
                }
            }
        }
        return $message_result;
    }

    public function hasMessage($type = null): bool
    {
        return !empty($this->flashSession->getMessages($type, false));
    }

    public function setVerify($verify_status, $verify_message)
    {
        $this->verify_status = $verify_status;
        $this->verify_message = $verify_message;
    }

    public function getVerify()
    {
        if($this->session->has("identity")) {
            if(!$this->verify_status) {
                echo $this->verify_message;
            }
        }
    }

    public function setActivate($activate_status, $activate_message)
    {
        $this->activate_status = $activate_status;
        $this->activate_message = $activate_message;
    }

    public function getActivate()
    {
        if($this->session->has("identity")) {
            if(!$this->activate_status) {
                echo $this->activate_message;
            }
        }
    }
}