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
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;


/**
 * Class ServerForm
 * @package FPanel\Forms
 */
class ServerForm extends Form
{
    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        $this->createFormElements($options);
    }

    /**
     * @param null $options
     */
    private function createFormElements($options = null)
    {

        $agent_status = new Check("agent_status", array(
            "id" => "agent_status",
            "name" => "agent_status",
        ));
        $os_type = new Select("server_os",array(
            WINDOWS  => __("Windows Server"),
            LINUX  => __("Linux"),
        ),array(
            "id" => "server_os",
            "name" => "server_os",
        ));

        if($options["action"] == "edit") {
            $os_type->setDefault($options["server_os"]);
        }
        else {
            $agent_status->setAttribute("checked","checked");
        }

        $ip_address = new Text("ip_address", array(
            "id" => "ip_address",
            "name" => "ip_address",
        ));

        $username = new Text("username", array(
            "id" => "username",
            "name" => "username",
        ));

        $password = new Password("password", array(
            "id" => "password",
            "name" => "password",
        ));

        $hostname = new Text("hostname", array(
            "id" => "hostname",
            "name" => "hostname",
        ));
        $this->serverPackages();
        $this->confirmFormElements($options);
        if($options["server_os"] == LINUX) {
            $username->setAttribute("value", "root");
        }
        else {
            $username->setAttribute("value", "administrator");
        }
        $username->setAttribute("readonly", true);

        if($options["action"] == "edit") {
            $os_type->setDefault($options["server_os"]);
            $hostname->setAttribute("value", $options["server_name"]);
            $ip_address->setAttribute("value", $options["ip_address"]);
        }
        else {
            $this->add($username);
            $this->add($password);
            $this->add($agent_status);
        }

//        $os_type->setDefault(WINDOWS);
//        $username->setAttribute("value", "administrator");
//        $ip_address->setAttribute("value", "192.168.139.93");
//        $password->setAttribute("value", "Z6humank4");
//        $hostname->setAttribute("value", "windows.test.fpanel.net");


        $this->add($hostname);
        $this->add($ip_address);
        $this->add($os_type);

    }

    private function serverPackages()
    {
        $win_pkgs = [
            "apache",
            "nginx",
            "iis",
            "proftpd",

            "perl",
            "python",
            "php56",
            "php72",
            "php74",

            "bind",
            "msdns",

            "mssql12",
            "mssql16",


            "mysql",
            "mariadb",
            "mysql57",
            "mysql80",
            "postgresql",

            "postfix",
            "dovecot",
            "hmail",
            "mailenable",
            "spamassassin",

            "roundcube",
            "phpmyadmin",
            "mylittleadmin",
            "awstats",
            "webalizer",
        ];
        foreach ($win_pkgs as $win_pkg) {
            if($win_pkg == "mysql80") {
                $this->add(new Check($win_pkg, array(
                    "id" => $win_pkg,
                    "name" => $win_pkg,
                    "checked" => "checked"
                )));
            }
            else {
                $this->add(new Check($win_pkg, array(
                    "id" => $win_pkg,
                    "name" => $win_pkg,
                )));
            }

        }
    }

    private function confirmFormElements($options)
    {
        $server_name = new Text("server_name", array(
            "id" => "server_name",
            "name" => "server_name",
        ));

        $description = new TextArea("description", array(
            "id" => "description",
            "name" => "description",
        ));


        $domain_limit = new Text("domain_limit", array(
            "id" => "domain_limit",
            "name" => "domain_limit",
        ));


        $database_limit = new Text("database_limit", array(
            "id" => "database_limit",
            "name" => "database_limit",
        ));


        $mail_limit = new Text("mail_limit", array(
            "id" => "mail_limit",
            "name" => "mail_limit",
        ));

        if($options["action"] == "edit") {
            $description->setAttribute("value", $options["description"]);
            $options["domain_limit"] == "unlimited" ? $domain_limit->setAttribute("value", "") : $domain_limit->setAttribute("value", $options["domain_limit"]);
            $options["database_limit"] == "unlimited" ? $database_limit->setAttribute("value", "") : $database_limit->setAttribute("value", $options["database_limit"]);
            $options["mail_limit"] == "unlimited" ? $mail_limit->setAttribute("value", "") : $mail_limit->setAttribute("value", $options["mail_limit"]);
        }
        else {
            $domain_limit->setAttribute("value", 0);
            $database_limit->setAttribute("value", 0);
            $mail_limit->setAttribute("value", 0);
        }

        $this->add($server_name);
        $this->add($description);
        $this->add($domain_limit);
        $this->add($database_limit);
        $this->add($mail_limit);
    }
}