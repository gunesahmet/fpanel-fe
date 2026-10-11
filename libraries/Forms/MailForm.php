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
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class MailForm
 * @package FPanel\Forms
 */
class MailForm extends Form
{
	/**
	 * @param null $entity
	 * @param $options
	 */
	public function initialize($entity = null, $options)
	{

        if($options["page"] == "settings") {
            $this->settingsFormElements($options);
        }
        else if($options["page"] == "mailaccount") {
            $this->mailAddressFormElements($options);
        }
        else if($options["page"] == "alias") {
            $this->mailAliasFormElements($options);
        }
	}

	/**
	 * @param $options
	 */
	private function settingsFormElements($options)
	{
        $mail_settings = $options["mail_settings"];
        // Forward to Address
        $forward_to_address = new Check("forward_to_address", array(
            "id" => "forward_to_address",
            "name" => "forward_to_address",
        ));
        // Reject
        $reject_incoming = new Check("reject_incoming", array(
            "id" => "reject_incoming",
            "name" => "reject_incoming",
        ));
        // Mail Address
        $mail_address = new Text("mail_address", array(
            "id" => "mail_address",
            "name" => "mail_address",
            "value" => $mail_settings["mail_forward_addr"]
        ));

        $webmail = new Select("webmail",
            array(
                "roundcube" => "RoundCube",
                "atmail" => "Atmail",
            ),
            array(
                "id" => "webmail",
                "name" => "webmail",
            ));
        $webmail->setDefault($mail_settings["webmail"]);
        if($mail_settings["nonexistend_user"] == FORWARD) {
            $forward_to_address->setAttribute("checked","checked");
        }
        else {
            $reject_incoming->setAttribute("checked","checked");
            $mail_address->setAttribute("disabled","disabled");
        }
        $this->add($webmail);
        $this->add($forward_to_address);
        $this->add($reject_incoming);
        $this->add($mail_address);
	}

    /**
     * @param $options
     */
    private function mailAddressFormElements($options)
    {
        // Mail Account
        $mail_account = new Text("mail_account", array(
            "id"    => "mail_account",
            "name"  => "mail_account",
        ));

        // Password
        $mail_password = new Password("mail_password", array(
            "id"    => "password",
            "name"  => "password",
        ));

        // Mail Quota
        $mail_quota = new Text("mail_quota", array(
            "id"    => "mail_quota",
            "name"  => "mail_quota",
        ));

        // Unlimited
        $unlimited_quota = new Check("unlimited_quota", array(
            "id" => "unlimited_quota",
            "name" => "unlimited_quota",
        ));

        // Autoresponder
        $autoresponder = new Check("autoresponder", array(
            "id" => "autoresponder",
            "name" => "autoresponder",
        ));

        // Forwarding
        $forwarding = new Check("forwarding", array(
            "id" => "forwarding",
            "name" => "forwarding",
        ));

        // Forward Address
        $forward_to_address = new Text("forward_to_address", array(
            "id" => "forward_to_address",
            "name" => "forward_to_address",
        ));



        if($options["action"] == "edit") {
            $mail_account->setAttribute("value", $options["mail_name"]);
//            $mail_password->setAttribute("value", $options["password"]);
            $mail_account->setAttribute("disabled","disabled");
            if($options["forward"] == true) {
                $forward_to_address->setAttribute("value", $options["forward_addr"]);
                $forwarding->setAttribute("checked","checked");
            }
            else {
                $forward_to_address->setAttribute("disabled","disabled");
            }

            if($options["autoresponder"] == true) {
                $autoresponder->setAttribute("checked","checked");
            }
            $this->add($autoresponder);

            $this->add($forwarding);
            $this->add($forward_to_address);

            if($options["mailbox_quota"] == UNLIMITED) {
                $unlimited_quota->setAttribute("checked","checked");
                $mail_quota->setAttribute("disabled","disabled");
            }
            else {
                $mail_quota->setAttribute("value", $options["mailbox_quota"]);
            }
        }

        $this->add($mail_account);
        $this->add($mail_password);

        $this->add($mail_quota);
        $this->add($unlimited_quota);

    }

    private function mailAliasFormElements($options)
    {
        // Mail Address
        $mail_alias = new Text("mail_alias", array(
            "id" => "mail_alias",
            "name" => "mail_alias",
        ));

        if($options["action"] == "edit") {
            $mail_alias->setAttribute("value", $options["alias_name"]);
        }
        $this->add($mail_alias);

    }
}