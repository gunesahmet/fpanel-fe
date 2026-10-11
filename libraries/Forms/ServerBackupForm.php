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
 * Class ServerBackupForm
 * @package FPanel\Forms
 */
class ServerBackupForm extends Form
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
            $this->domainItems($options);
            $this->serverSettingItems();
        }

    }

    /**
     * backupFormElements
     */
    private function backupFormElements()
    {
        // Directory Path
        $this->add(new Text("backup_name", array(
            "id" => "backup_name",
            "name" => "backup_name",
        )));

        $this->add(new TextArea("description", array(
            "id" => "description",
            "name" => "description",
            "value" => __("Server Backup") . "\n" . __("Creation Date") . ": " . $this->date->getOnlyDate("now") ,
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

        $clients = new Select("client_list", $this->clientAndDomains(), array(
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

        $this->add(new Check("restore_server_settings", array(
            "id" => "restore_server_settings",
            "name" => "restore_server_settings",
        )));


        $this->add(new Check("restore_clients", array(
            "id" => "restore_clients",
            "name" => "restore_clients",
        )));

        $this->add(new Check("restore_domains", array(
            "id" => "restore_domains",
            "name" => "restore_domains",
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

    private function domainItems($options)
    {
        $selected_domains = $options["config"]["selected_domains"];
        $users = $options["config"]["users"];

        $domains = new Select("domain_list", $this->extractDomains($users), [
            "data-selectable" => __("Available Domains"),
            "data-selection" => __("Selected Domains"),
            "id" => "domain_list",
            "name" => "domain_list[]",
            "multiple" => "multiple"
        ]);

        $this->add($domains);

    }

    private function extractDomains($users): array
    {
        $domain_list = [];
        $this->extractDomainsFromAdmins($users["admins"], $domain_list);
        $this->extractDomainsFromResellers($users["resellers"], $domain_list);
        $this->extractDomainsFromClients($users["clients"], $domain_list);
        $this->extractDomainsFromRoot($users["domains"], $domain_list);
        return $domain_list;
    }

    private function extractDomainsFromAdmins($users, &$domain_list = [])
    {
        foreach($users as $admin_name => $user_data) {
            $domain_list[$admin_name] = [];
            if(!empty($user_data["domains"])) {
                foreach ($user_data["domains"] as $domain) {
                    $domain_list[$admin_name][$domain] = $domain;
                }
                if(!empty($user_data["resellers"])) {
                    $this->extractDomainsFromResellers($user_data["resellers"], $domain_list);
                }
            }
        }
    }

    private function extractDomainsFromResellers($users, &$domain_list = [])
    {
        foreach($users as $reseller_name => $user_data) {
            $domain_list[$reseller_name] = [];
            if(!empty($user_data["domains"])) {
                foreach ($user_data["domains"] as $domain) {
                    $domain_list[$reseller_name][$domain] = $domain;
                }

                if(!empty($user_data["clients"])) {
                    $this->extractDomainsFromClients($user_data["clients"], $domain_list);
                }
            }
        }
    }

    private function extractDomainsFromClients($users, &$domain_list = [])
    {
        foreach($users as $client_name => $domains) {
            $domain_list[$client_name] = [];
            if(!empty($domains["domains"])) {
                foreach ($domains["domains"] as $domain) {
                    $domain_list[$client_name][$domain] = $domain;
                }
            }
        }
    }

    private function extractDomainsFromRoot($domains, &$domain_list = [])
    {
        $domain_list["admin"] = [];
        foreach($domains as $domain) {
            $domain_list["admin"][$domain] = $domain;
        }
    }

    private function serverSettingItems()
    {

        $this->add(new Check("restore_cronjobs", array(
            "id" => "restore_cronjobs",
            "name" => "restore_cronjobs",
        )));

        $this->add(new Check("restore_blacklists", array(
            "id" => "restore_blacklists",
            "name" => "restore_blacklists",
        )));

        $this->add(new Check("restore_mail_server_settings", array(
            "id" => "restore_mail_server_settings",
            "name" => "restore_mail_server_settings",
        )));

        $this->add(new Check("restore_dns_server", array(
            "id" => "restore_dns_server",
            "name" => "restore_dns_server",
        )));

        $this->add(new Check("restore_spam_settings", array(
            "id" => "restore_spam_settings",
            "name" => "restore_spam_settings",
        )));

        $this->add(new Check("restore_spam_list", array(
            "id" => "restore_spam_list",
            "name" => "restore_spam_list",
        )));

    }

    private function clientAndDomains(): array
    {
        $client_list = array();
        $clients = Users::find("type = '0'");
        // Admin
        if($clients) {
            foreach($clients as $client) {
                $client_childs = Users::find("parent = '$client->id' AND type not like '4'");
                $client_list[$client->username] = array();
                $client_domains = Domains::find("client_id = '$client->id'");
                if($client_domains) {
                    foreach($client_domains as $client_domain) {
                        $client_list[$client->username][$client_domain->id] = $client_domain->name;
                    }
                }
                // Reseller veya Client
                if($client_childs) {
                    foreach($client_childs as $client_child) {
                        $client_list[$client->username][$client_child->username] = array();
                        $client_child_childs = Users::find("parent = '$client_child->id' AND type not like '4'");
                        $client_child_domains = Domains::find("client_id = '$client_child->id'");
                        if($client_child_domains) {
                            foreach($client_child_domains as $client_child_domain) {
                                $client_list[$client->username][$client_child->username][$client_child_domain->id] = $client_child_domain->name;
                            }
                        }
                        // Client
                        if($client_child_childs) {
                            foreach($client_child_childs as $client_child_child) {
                                $client_child_child_domains = Domains::find("client_id = '$client_child_child->id'");
                                if($client_child_child_domains) {
                                    foreach($client_child_child_domains as $client_child_child_domain) {
                                        $client_list[$client->username][$client_child->username][$client_child_child->username][$client_child_child_domain->id] = $client_child_child_domain->name;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $client_list;
    }
}