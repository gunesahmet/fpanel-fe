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

use FPanel\Library\Library;
use FPanel\Models\ClientLimitsPermissions;

/**
 * Class LimitsPermission
 * @package FPanel\LimitsPermission
 */
class LimitsPermission extends Library
{
    public $domain_id;
    public $user_id;

    public function save($restore = false): bool
    {
        if(!$restore) {
            if(!$this->validate()) {
                return false;
            }
        }

        $overuse = $this->getPost("overuse");

        if ($overuse != ACTIVE)
            $overuse = false;

        $post_perm_data = array(
            'access_panel',
            'man_client',
            'man_domain',
            'man_subdomain',
            'man_domain_alias',
            'man_email_alias',
            'man_hosting_settings',
            'man_task',
            'man_dns',
            'man_ssl',
            'man_mailing_lists',
            'man_spam_filters',
            'man_backup',
            'man_database',
            'man_email',
            'man_protect_dir',
            'man_template',
        );

        $permissions = array();
        foreach ($post_perm_data as $data_perm) {
            $post_data = $this->getPost($data_perm);
            $permissions[$data_perm] = ALLOWED;
            if ($post_data == OFF)
                $permissions[$data_perm] = NOT_ALLOWED;
        }

        $client_limits_permissions = new ClientLimitsPermissions();

        $disk_limit = $this->getPost("disk_limit");
        $traffic_limit = $this->getPost("traffic_limit");
        $email_box_size_limit = $this->getPost("email_box_size_limit");
        $database_size_limit = $this->getPost("database_size_limit");
        $max_cpu = $this->getPost("max_cpu");
        $application_pool_mode = $this->getPost("application_pool_mode");

        if ($disk_limit != UNLIMITED)
            $disk_limit = $this->getPost("disk_limit")*1024*1024;

        if($traffic_limit != UNLIMITED)
            $traffic_limit = $this->getPost("traffic_limit")*1024*1024;

        if ($email_box_size_limit != UNLIMITED)
            $email_box_size_limit = $this->getPost("email_box_size_limit")*1024*1024;

        if ($database_size_limit != UNLIMITED)
            $database_size_limit = $this->getPost("database_size_limit")*1024*1024;

        if(($this->getPost("max_cpu") == null) || !is_numeric($this->getPost("max_cpu")))
            $max_cpu = "0";

        if(($this->getPost("application_pool_mode") == null) || !in_array($this->getPost("application_pool_mode"), [CLASSIC, INTEGRATED]))
            $application_pool_mode = INTEGRATED;

        if($this->getPost("web_statistics_type") == null)
            $this->setPost("web_statistics_type", "none");


        $assigned = array(
            'client_id' => $this->user_id,
            'domain_id' => $this->domain_id,
            'client_limit' => $this->getPost("client_limit"),
            'domain_limit' => $this->getPost("domain_limit"),
            'disk_limit' => $disk_limit,
            'traffic_limit' => $traffic_limit,
            'subdomain_limit' => $this->getPost("subdomain_limit"),
            'domain_alias_limit' => $this->getPost("domain_alias_limit"),
            'email_box_limit' => $this->getPost("email_box_limit"),
            'email_box_size_limit' => $email_box_size_limit,
            'email_forward_limit' => $this->getPost("email_forward_limit"),
            'email_groups_limit' => $this->getPost("email_groups_limit"),
            'email_autoresponder_limit' => $this->getPost("email_autoresponder_limit"),
            'email_lists_limit' => $this->getPost("email_lists_limit"),
            'database_limit' => $this->getPost("database_limit"),
            'database_size_limit' => $database_size_limit,
            'template_limit' => $this->getPost("template_limit"),
            'overuse' => $overuse,
            // Permissions
            'access_panel' => $permissions['access_panel'],
            'man_client' => $this->getPost("man_client") == ON ? ALLOWED : NOT_ALLOWED,
            'man_domain' => $this->getPost("man_domain") == ON ? ALLOWED : NOT_ALLOWED,
            'man_subdomain' => $permissions['man_subdomain'],
            'man_domain_alias' => $permissions['man_domain_alias'],
            'man_email_alias' => $permissions['man_email_alias'],
            'man_hosting_settings' => $permissions['man_hosting_settings'],
            'man_task' => $permissions['man_task'],
            'man_dns' => $permissions['man_dns'],
            'man_ssl' => $permissions['man_ssl'],
            'man_mailing_lists' => $permissions['man_mailing_lists'],
            'man_spam_filters' => $permissions['man_spam_filters'],
            'man_backup' => $permissions['man_backup'],
            'man_database' => $permissions['man_database'],
            'man_email' => $permissions['man_email'],
            'man_protect_dir' => $permissions['man_protect_dir'],
            'man_template' => $permissions['man_template'],
            // Domain Parameters
            'php' => $this->getPost("php") == ON ? 1 : 0,
            'php_version' => $this->getPost("php_version"),
            'cgi' => $this->getPost("cgi") == ON ? 1 : 0,
            'perl' => $this->getPost("perl") == ON ? 1 : 0,
            'python' => $this->getPost("python") == ON ? 1 : 0,
            'fast_cgi' => $this->getPost("fast_cgi") == ON ? 1 : 0,
            'statistic_support' => $this->getPost("web_statistics") == ON ? 1 : 0,
            'statistic_type' => $this->getPost("web_statistics_type"),
            'asp' => $this->getPost("asp") == ON ? 1 : 0,
            'aspnet_core' => $this->getPost("aspnet_core") == ON ? 1 : 0,
            'aspnet' => $this->getPost("aspnet") == ON ? 1 : 0,
            'aspnet_version' => $this->getPost("aspnet_version"),
            'dedicated_pool' => $this->getPost("dedicated_pool") == ON ? 1 : 0,
            'max_cpu' => $max_cpu,
            'application_pool_mode' => $application_pool_mode,
            'ssl' => $this->getPost("ssl") == ON ? 1 : 0,
        );
 
        $client_limits_permissions->assign($assigned);

        if ($client_limits_permissions->create()) {
            return true;
        } else {
            foreach ($client_limits_permissions->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "LimitsPermission->save",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
    }

    public function delete($user_id)
    {
        $users_limits = ClientLimitsPermissions::findFirst("client_id = '$user_id'");
        if ($users_limits) {
            if(!$users_limits->delete()) {
                foreach($users_limits->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "LimitsPermission->delete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
        }
    }

    public function update($user_id): bool
    {
        try {
            if(!$this->validate())
                return false;
            $overuse = $this->getPost("overuse");

            if ($overuse == ON)
                $overuse = ACTIVE;
            else
                $overuse = PASSIVE;

            $post_perm_data = array(
                'access_panel',
                'man_client',
                'man_domain',
                'man_subdomain',
                'man_domain_alias',
                'man_email_alias',
                'man_hosting_settings',
                'man_task',
                'man_dns',
                'man_ssl',
                'man_mailing_lists',
                'man_spam_filters',
                'man_backup',
                'man_database',
                'man_email',
                'man_protect_dir',
                'man_template',
            );

            $permissions = array();
            foreach ($post_perm_data as $data_perm) {
                $post_data = $this->getPost($data_perm);
                $permissions[$data_perm] = ALLOWED;
                if ($post_data == OFF)
                    $permissions[$data_perm] = NOT_ALLOWED;
            }

            $client_limits_permissions = ClientLimitsPermissions::findFirst("client_id = '$user_id'");

            $disk_limit = $this->getPost("disk_limit");
            $traffic_limit = $this->getPost("traffic_limit");
            $email_box_size_limit = $this->getPost("email_box_size_limit");
            $database_size_limit = $this->getPost("database_size_limit");

            if ($disk_limit != UNLIMITED)
                $disk_limit = $this->getPost("disk_limit")*1024*1024;

            if($traffic_limit != UNLIMITED)
                $traffic_limit = $this->getPost("traffic_limit")*1024*1024;

            if ($email_box_size_limit != UNLIMITED)
                $email_box_size_limit = $this->getPost("email_box_size_limit")*1024*1024;

            if ($database_size_limit != UNLIMITED)
                $database_size_limit = $this->getPost("database_size_limit")*1024*1024;


            $client_limits_permissions->assign(array(
                // Limits
                'client_limit' => $this->getPost("client_limit"),
                'domain_limit' => $this->getPost("domain_limit"),
                'disk_limit' => $disk_limit,
                'traffic_limit' => $traffic_limit,
                'subdomain_limit' => $this->getPost("subdomain_limit"),
                'domain_alias_limit' => $this->getPost("domain_alias_limit"),
                'email_box_limit' => $this->getPost("email_box_limit"),
                'email_box_size_limit' => $email_box_size_limit,
                'email_forward_limit' => $this->getPost("email_forward_limit"),
                'email_groups_limit' => $this->getPost("email_groups_limit"),
                'email_autoresponder_limit' => $this->getPost("email_autoresponder_limit"),
                'email_lists_limit' => $this->getPost("email_lists_limit"),
                'database_limit' => $this->getPost("database_limit"),
                'database_size_limit' => $database_size_limit,
                'template_limit' => $this->getPost("template_limit"),
                'overuse' => $overuse,
                // Permissions
                'access_panel' => $permissions['access_panel'],
                'man_client' => $permissions['man_client'],
                'man_domain' => $permissions['man_domain'],
                'man_subdomain' => $permissions['man_subdomain'],
                'man_domain_alias' => $permissions['man_domain_alias'],
                'man_email_alias' => $permissions['man_email_alias'],
                'man_hosting_settings' => $permissions['man_hosting_settings'],
                'man_task' => $permissions['man_task'],
                'man_dns' => $permissions['man_dns'],
                'man_ssl' => $permissions['man_ssl'],
                'man_mailing_lists' => $permissions['man_mailing_lists'],
                'man_spam_filters' => $permissions['man_spam_filters'],
                'man_backup' => $permissions['man_backup'],
                'man_database' => $permissions['man_database'],
                'man_email' => $permissions['man_email'],
                'man_protect_dir' => $permissions['man_protect_dir'],
                'man_template' => $permissions['man_template'],
            ));

            if ($client_limits_permissions->update()) {
                return true;
            }
            else {
                foreach ($client_limits_permissions->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "LimitsPermission->update",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "System->updateDomainStatistic",
                "data" => $this->getPost(),
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function validate(): bool
    {
        if(!$this->validation->Limits($this->getPost())
                ->Permissions($this->getPost())
                ->Validate()) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;

    }
}