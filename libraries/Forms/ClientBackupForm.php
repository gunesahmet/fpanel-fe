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

use FPanel\Models\Domains;
use FPanel\Models\Users;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;

/**
 * Class ClientBackupForm
 * @package FPanel\Forms
 */
class ClientBackupForm extends Form
{
    public function initialize($entity = null, $options)
    {
        if($options["action"] == "backup") {
            $this->backupFormElements($options);
        }
        else {
            $this->restoreFormElements();
            $this->selectedDomains($options);
        }
    }

    private function backupFormElements($options)
    {
        // Directory Path
        $this->add(new Text("backup_name", array(
            "id" => "backup_name",
            "name" => "backup_name",
        )));

        $this->add(new TextArea("description", array(
            "id" => "description",
            "name" => "description",
            "value" => __("Account Backup") . "\n" . __("Creation Date") . ": " . $this->date->getOnlyDate("d-m-Y") ,
        )));

        // Backup Location (Server )
        $this->add(new Select("backup_repo",array(
            "server" => __("Server"),
            "ftp" => __("FTP"),
            "storage" => __("Cloud Storage"),
        ), array(
            "id" => "backup_repo",
            "name" => "backup_repo",
        )));

        // Send Mail
        $this->add(new Check("send_mail", array(
            "id" => "send_mail",
            "name" => "send_mail",
        )));

        // Email Address
        $this->add(new Text("email_address", array(
            "id" => "email_address",
            "name" => "email_address",
            "disabled" => "disabled",
        )));

        // Content
        // All Content
        $this->add(new Check("include_all", array(
            "id" => "include_all",
            "name" => "content",
        )));

        // Include Domain Content
        // www Content
        $this->add(new Check("www_content", array(
            "id" => "www_content",
            "name" => "www_content",
        )));

        // Subdomain Content
        $this->add(new Check("subdomain_content", array(
            "id" => "subdomain_content",
            "name" => "subdomain_content",
        )));

        // All Content
        $this->add(new Check("all_content", array(
            "id" => "all_content",
            "name" => "content",
        )));

        // Databases
        $this->add(new Check("database_content", array(
            "id" => "database_content",
            "name" => "database_content",
        )));

        // DNS
        $this->add(new Check("dns_content", array(
            "id" => "dns_content",
            "name" => "dns_content",
        )));

        // Mail
        $this->add(new Check("mail_content", array(
            "id" => "mail_content",
            "name" => "mail_content",
        )));

        $clients = new Select("client_list", $this->clientAndDomains($options), array(
            "data-selectable" => __("Available"),
            "data-selection" => __("Selected"),
            "id" => "client_list",
            "name" => "client_list[]",
            "multiple" => "multiple"
        ));
        $this->add($clients);
    }

    private function restoreFormElements()
    {
        $this->add(new Check("restore_all", array(
            "id" => "restore_all",
            "name" => "restore_all",
        )));

        $this->add(new Check("restore_domain_ftproot", array(
            "id" => "restore_domain_ftproot",
            "name" => "restore_domain_ftproot",
        )));

        $this->add(new Check("restore_subdomains", array(
            "id" => "restore_subdomains",
            "name" => "restore_subdomains",
        )));

        $this->add(new Check("restore_databases", array(
            "id" => "restore_databases",
            "name" => "restore_databases",
        )));

        $this->add(new Check("restore_dns", array(
            "id" => "restore_dns",
            "name" => "restore_dns",
        )));

        $this->add(new Check("restore_mails", array(
            "id" => "restore_mails",
            "name" => "restore_mails",
        )));
    }

    private function clientAndDomains($options): array
    {
        $client_list = array();
        $client_id = $options["client_id"];
        $client_username = $options["client_username"];
        $client_childs = Users::find("parent = '$client_id' AND type not like '4'");
        $client_list[$client_username] = array();
        $client_domains = Domains::find("client_id = '$client_id'");
        if($client_domains) {
            foreach($client_domains as $client_domain) {
                $client_list[$client_username][$client_domain->id] = $client_domain->name;
            }
        }
        // Reseller veya Client
        if($client_childs) {
            foreach($client_childs as $client_child) {
                $client_list[$client_username][$client_child->username] = array();
                $client_child_childs = Users::find("parent = '$client_child->id' AND type not like '4'");
                $client_child_domains = Domains::find("client_id = '$client_child->id'");
                if($client_child_domains) {
                    foreach($client_child_domains as $client_child_domain) {
                        $client_list[$client_username][$client_child->username][$client_child_domain->id] = $client_child_domain->name;
                    }
                }
                // Client
                if($client_child_childs) {
                    foreach($client_child_childs as $client_child_child) {
                        $client_child_child_domains = Domains::find("client_id = '$client_child_child->id'");
                        if($client_child_child_domains) {
                            foreach($client_child_child_domains as $client_child_child_domain) {
                                $client_list[$client_username][$client_child->username][$client_child_child->username][$client_child_child_domain->id] = $client_child_child_domain->name;
                            }
                        }
                    }
                }

            }
        }
        return $client_list;
    }

    private function selectedDomains($options)
    {
        $config = $options["config"];
        $domains = $config["domains"];
        $childs = $config["users"];

        $owned_domain_list = array();
        if(!empty($domains)) {
            foreach($domains as $domain) {
                $owned_domain_list[$domain] = $domain;
            }
        }


        $child_list = array();
        if(!empty($childs)) {
            foreach($childs as $name => $child_domains) {
                if(!empty($child_domains)) {
                    foreach ($child_domains as $child_domain) {
                        $child_list[$name][$child_domain] = $child_domain;
                    }
                }
            }
        }

        $list = array(
            $options["client_name"] => $owned_domain_list,
        );
        $list = array_merge($list, $child_list);

        $clients = new Select("domain_list", $list, array(
            "data-selectable" => __("Available"),
            "data-selection" => __("Selected"),
            "id" => "domain_list",
            "name" => "domain_list[]",
            "multiple" => "multiple"
        ));
        $this->add($clients);
    }
}