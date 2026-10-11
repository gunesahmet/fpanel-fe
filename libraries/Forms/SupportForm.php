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

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;


/**
 * Class SupportForm
 * @package FPanel\Forms
 */
class SupportForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {

        if($options["action"] != "support_user") {
            $this->formElements($options);
        }
        else {
            $this->supportUserElements();
        }

    }

    /**
     * @param $options
     */
    private function formElements($options)
    {
        $request_user = new Text("request_user_email", array(
            "id" => "request_user_email",
            "name" => "request_user_email",
        ));

        $support_status = new Check("support_status", array(
            "id" => "support_status",
            "name" => "support_status",
        ));

        $disable_logins = new Check("disable_logins", array(
            "id" => "disable_logins",
            "name" => "disable_logins",
        ));

        $username = new Text("username", array(
            "id" => "username",
            "name" => "username",
        ));

        $password = new Password("password", array(
            "id" => "password",
            "name" => "password",
        ));

        $title = new Text("title", array(
            "id" => "title",
            "name" => "title",
        ));

        $message = new TextArea("support_message", array(
            "id" => "support_message",
            "name" => "support_message",
            "rows" => "5",
        ));

        if($options["action"] == "edit") {
            if($options["support_status"] == ACTIVE ) {
                $support_status->setAttribute("checked","checked");
            }
            if($options["disable_logins"] == ACTIVE ) {
                $disable_logins->setAttribute("checked","checked");
            }

            $request_user->setAttribute("value", $options["request_user_email"]);
            $username->setAttribute("value", $options["username"]);
            $password->setAttribute("value", $options["password"]);
            $username->setAttribute("disabled","disabled");
            $password->setAttribute("disabled","disabled");
            $title->setAttribute("value", $options["title"]);
            $message->setAttribute("value", $options["support_message"]);
        }
        else {
            $username->setAttribute("value", \Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM));
            $password->setAttribute("value", \Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM));
            $username->setAttribute("readonly","true");
        }

        $this->add($request_user);
        $this->add($support_status);
        $this->add($disable_logins);
        $this->add($username);
        $this->add($password);
        $this->add($title);
        $this->add($message);


    }

    private function supportUserElements()
    {
        $user_message = new TextArea("user_message", array(
            "id" => "user_message",
            "name" => "user_message",
            "rows" => "5",
        ));

        $support_status = new Check("support_status", array(
            "id" => "support_status",
            "name" => "support_status",
        ));

        $user_email = new Text("user_email", array(
            "id" => "user_email",
            "name" => "user_email",
        ));

        $this->add($user_email);
        $this->add($user_message);
        $this->add($support_status);
    }
}