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
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class ProtectdirForm
 * @package FPanel\Forms
 */
class BackupForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
	public function initialize($entity = null, $options)
	{

        if($options["action"] == "backup") {
            $this->backupFormElements();
        }
        else {
            $this->restoreFormElements();
        }

	}

	private function backupFormElements()
	{
		// Directory Path
		$this->add(new Text("backup_name", array(
			"id" => "backup_name",
			"name" => "backup_name",
		)));

        $this->add(new Text("description", array(
            "id" => "description",
            "name" => "description",
        )));

		$this->add(new Select("backup_repo",array(
			"server" => __("Server"),
			"ftp" => __("FTP"),
			"storage" => __("Cloud Storage"),
		), array(
			"id" => "backup_repo",
			"name" => "backup_repo",
		)));


        // Split Size
        $this->add(new Select("split_size",array(
            "512" => "512 MB",
            "1024" => "1024 MB",
            "2048" => "2048 MB",
        ), array(
            "id" => "split_size",
            "name" => "split_size",
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
		// Domain Settings
		$this->add(new Check("domain_settings", array(
			"id" => "domain_settings",
			"name" => "domain_settings",
		)));

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

		// DNS
		$this->add(new Check("mail_content", array(
			"id" => "mail_content",
			"name" => "mail_content",
		)));



	}

    private function restoreFormElements()
    {
        $this->add(new Check("restore_domain_configs", array(
            "id" => "restore_domain_configs",
            "name" => "restore_domain_configs",
        )));

        $this->add(new Check("restore_dns", array(
            "id" => "restore_dns",
            "name" => "restore_dns",
        )));

        $this->add(new Check("restore_httpdoc", array(
            "id" => "restore_httpdoc",
            "name" => "restore_httpdoc",
        )));

        $this->add(new Check("restore_subdomains", array(
            "id" => "restore_subdomains",
            "name" => "restore_subdomains",
        )));

        $this->add(new Check("restore_databases", array(
            "id" => "restore_databases",
            "name" => "restore_databases",
        )));

        $this->add(new Check("restore_mails", array(
            "id" => "restore_mails",
            "name" => "restore_mails",
        )));
    }
}