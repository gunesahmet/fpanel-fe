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
 */namespace FPanel\Api\Templates;

use FPanel\Environments\QueryBuilder;
use FPanel\Models\Users;
use FPanel\System\Serviceplans;
use Phalcon\Mvc\User\Component;

class Templates extends Component
{
    private $error_messages = [];

    public function serviceplans($process, $params, $client_id)
    {
        $users = Users::findFirstById($client_id);
        switch($process) {
            case "list":
                $result = $this->templateList($client_id);
                break;
            case "create":
                $result = $this->templateCreate($params, $client_id, $users->type);
                break;
            case "update":
                $result = $this->templateUpdate($params, $client_id, $users->type);
                break;
            case "delete":
                $result = $this->templateDelete($params, $client_id, $users->type);
                break;
            default:
                $result = array('Process required');
                break;
        }
        return $result;
    }
    private function templateList($client_id): array
    {
        $qbuilder = new QueryBuilder();
        $template_list = $qbuilder->select([
            "serviceplans.name",
            "serviceplans.is_default",
            "users.username as user",
            "serviceplans.expire_time",
            "serviceplans.client_limit",
            "serviceplans.domain_limit",
            "serviceplans.disk_limit",
            "serviceplans.traffic_limit",
            "serviceplans.subdomain_limit",
            "serviceplans.domain_alias_limit",
            "serviceplans.email_box_limit",
            "serviceplans.email_box_size_limit",
            "serviceplans.email_forward_limit",
            "serviceplans.email_groups_limit",
            "serviceplans.email_autoresponder_limit",
            "serviceplans.email_lists_limit",
            "serviceplans.database_limit",
            "serviceplans.database_size_limit",
            "serviceplans.template_limit",
            "serviceplans.access_panel",
            "serviceplans.man_client",
            "serviceplans.man_domain",
            "serviceplans.man_subdomain",
            "serviceplans.man_domain_alias",
            "serviceplans.man_email_alias",
            "serviceplans.man_hosting_settings",
            "serviceplans.man_task",
            "serviceplans.man_dns",
            "serviceplans.man_mailing_lists",
            "serviceplans.man_spam_filters",
            "serviceplans.man_backup",
            "serviceplans.man_email",
            "serviceplans.man_database",
            "serviceplans.man_protect_dir",
            "serviceplans.man_ssl",
            "serviceplans.man_template",
            "serviceplans.overuse",
            "serviceplans.php",
            "serviceplans.php_version",
            "serviceplans.cgi",
            "serviceplans.perl",
            "serviceplans.python",
            "serviceplans.fast_cgi",
            "serviceplans.statistic_support",
            "serviceplans.statistic_type",
            "serviceplans.asp",
            "serviceplans.aspnet",
            "serviceplans.aspnet_core",
            "serviceplans.aspnet_version",
            "serviceplans.dedicated_pool",
            "serviceplans.max_cpu",
            "serviceplans.application_pool_mode",
            "serviceplans.created_date",
        ])
            ->from("serviceplans")
            ->innerJoin("users", "users.id = serviceplans.client_id")
            ->where([
                'conditions' => "serviceplans.client_id = ?1",
                'bind' => [1 => $client_id]
            ])
            ->find();

        foreach ($template_list as $k => $v) {
            $template_list[$k]["php_version"] = PHP_VERSIONS[$v["php_version"]];
            $template_list[$k]["statistic_type"] = STATISTICS[$v["statistic_type"]];
            $template_list[$k]["aspnet_version"] = NET_VERSIONS[$v["aspnet_version"]];
            $template_list[$k]["application_pool_mode"] = POOL_MODE[$v["application_pool_mode"]];
            $template_list[$k]["is_default"] = $v["is_default"] == "1";
            $template_list[$k]["access_panel"] = $v["access_panel"] == "1";
            $template_list[$k]["man_client"] = $v["man_client"] == "1";
            $template_list[$k]["man_domain"] = $v["man_domain"] == "1";
            $template_list[$k]["man_subdomain"] = $v["man_subdomain"] == "1";
            $template_list[$k]["man_domain_alias"] = $v["man_domain_alias"] == "1";
            $template_list[$k]["man_email_alias"] = $v["man_email_alias"] == "1";
            $template_list[$k]["man_hosting_settings"] = $v["man_hosting_settings"] == "1";
            $template_list[$k]["man_task"] = $v["man_task"] == "1";
            $template_list[$k]["man_dns"] = $v["man_dns"] == "1";
            $template_list[$k]["man_mailing_lists"] = $v["man_mailing_lists"] == "1";
            $template_list[$k]["man_spam_filters"] = $v["man_spam_filters"] == "1";
            $template_list[$k]["man_backup"] = $v["man_backup"] == "1";
            $template_list[$k]["man_email"] = $v["man_email"] == "1";
            $template_list[$k]["man_database"] = $v["man_database"] == "1";
            $template_list[$k]["man_protect_dir"] = $v["man_protect_dir"] == "1";
            $template_list[$k]["man_ssl"] = $v["man_ssl"] == "1";
            $template_list[$k]["man_template"] = $v["man_template"] == "1";
            $template_list[$k]["overuse"] = $v["overuse"] == "1";
            $template_list[$k]["php"] = $v["php"] == "1";
            $template_list[$k]["cgi"] = $v["cgi"] == "1";
            $template_list[$k]["perl"] = $v["perl"] == "1";
            $template_list[$k]["python"] = $v["python"] == "1";
            $template_list[$k]["fast_cgi"] = $v["fast_cgi"] == "1";
            $template_list[$k]["statistic_support"] = $v["statistic_support"] == "1";
            $template_list[$k]["asp"] = $v["asp"] == "1";
            $template_list[$k]["aspnet"] = $v["aspnet"] == "1";
            $template_list[$k]["aspnet_core"] = $v["aspnet_core"] == "1";
            $template_list[$k]["dedicated_pool"] = $v["dedicated_pool"] == "1";
        }

        $template_list['status'] = "1";
        return $template_list;
    }

    private function templateCreate($params, $client_id, $client_type)
    {
        $requeired_keys = [
            "name",
            "is_default",
            "expire_time",
            "client_limit",
            "domain_limit",
            "disk_limit",
            "traffic_limit",
            "subdomain_limit",
            "domain_alias_limit",
            "email_box_limit",
            "email_box_size_limit",
            "email_forward_limit",
            "email_groups_limit",
            "email_autoresponder_limit",
            "email_lists_limit",
            "database_limit",
            "database_size_limit",
            "template_limit",
            "access_panel",
            "man_client",
            "man_domain",
            "man_subdomain",
            "man_domain_alias",
            "man_email_alias",
            "man_hosting_settings",
            "man_task",
            "man_dns",
            "man_mailing_lists",
            "man_spam_filters",
            "man_backup",
            "man_email",
            "man_database",
            "man_protect_dir",
            "man_ssl",
            "man_template",
            "overuse",
            "php",
            "php_version",
//            "cgi",
            "perl",
            "python",
//            "fast_cgi",
            "statistic_support",
            "statistic_type",
            "asp",
            "aspnet",
            "aspnet_core",
            "aspnet_version",
            "dedicated_pool",
            "max_cpu",
            "application_pool_mode",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        if(!$this->env->checkServiceplanOwner($params['name'], $client_id)) {
            $this->error_messages['name'] = "You dont have access for this serviceplan or serviceplan doesn't exists.";
            return $this->error_messages;
        }

        $serviceplan = new Serviceplans();
        $serviceplan->user_type = $client_type;
        $serviceplan->user_id = $client_id;
        $on_off_statuses = [
            "is_default",
            "access_panel",
            "man_client",
            "man_domain",
            "man_subdomain",
            "man_domain_alias",
            "man_email_alias",
            "man_hosting_settings",
            "man_task",
            "man_dns",
            "man_mailing_lists",
            "man_spam_filters",
            "man_backup",
            "man_email",
            "man_database",
            "man_protect_dir",
            "man_ssl",
            "man_template",
            "overuse",
            "php",
//            "cgi",
            "perl",
            "python",
//            "fast_cgi",
            "statistic_support",
            "asp",
            "aspnet",
            "aspnet_core",
            "dedicated_pool",
        ];
        $post_data = [
            "serviceplan_name" => $params["name"],
            "expire_time_limit" => $params["expire_time"],
            "client_limit" => $params["client_limit"],
            "domain_limit" => $params["domain_limit"],
            "disk_limit" => $params["disk_limit"],
            "traffic_limit" => $params["traffic_limit"],
            "subdomain_limit" => $params["subdomain_limit"],
            "domain_alias_limit" => $params["domain_alias_limit"],
            "email_box_limit" => $params["email_box_limit"],
            "email_box_size_limit" => $params["email_box_size_limit"],
            "email_forward_limit" => $params["email_forward_limit"],
            "email_groups_limit" => $params["email_groups_limit"],
            "email_autoresponder_limit" => $params["email_autoresponder_limit"],
            "email_lists_limit" => $params["email_lists_limit"],
            "database_limit" => $params["database_limit"],
            "database_size_limit" => $params["database_size_limit"],
            "template_limit" => $params["template_limit"],

            "php_version" => $this->env->getPhpVersion($params["php_version"], true),
            "max_cpu" => $params["max_cpu"],
            "statistic_type" => $this->env->getStatisticsType($params["statistic_type"], true),
            "aspnet_version" => $this->env->getNetVersion($params["aspnet_version"], true),
            "application_pool_mode" => $this->env->getApplicationMode($params["application_pool_mode"], true),

            "cgi" => OFF,
            "fast_cgi" => OFF,
        ];

        foreach ($on_off_statuses as $key) {
            if($params[$key]) {
                $post_data[$key] = ON;
            }
            else {
                $post_data[$key] = OFF;
            }
        }
        $serviceplan->setPosts($post_data);
        if(!$serviceplan->save()) {
//            $this->debug->debug($serviceplan->getMessages(), false);
            return $serviceplan->getMessages();
        }
        return true;
    }

    private function templateUpdate($params, $client_id, $client_type)
    {
        $requeired_keys = [
            "name",
            "is_default",
            "expire_time",
            "client_limit",
            "domain_limit",
            "disk_limit",
            "traffic_limit",
            "subdomain_limit",
            "domain_alias_limit",
            "email_box_limit",
            "email_box_size_limit",
            "email_forward_limit",
            "email_groups_limit",
            "email_autoresponder_limit",
            "email_lists_limit",
            "database_limit",
            "database_size_limit",
            "template_limit",
            "access_panel",
            "man_client",
            "man_domain",
            "man_subdomain",
            "man_domain_alias",
            "man_email_alias",
            "man_hosting_settings",
            "man_task",
            "man_dns",
            "man_mailing_lists",
            "man_spam_filters",
            "man_backup",
            "man_email",
            "man_database",
            "man_protect_dir",
            "man_ssl",
            "man_template",
            "overuse",
            "php",
            "php_version",
//            "cgi",
            "perl",
            "python",
//            "fast_cgi",
            "statistic_support",
            "statistic_type",
            "asp",
            "aspnet",
            "aspnet_core",
            "aspnet_version",
            "dedicated_pool",
            "max_cpu",
            "application_pool_mode",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        if(!$this->env->checkServiceplanOwner($params['name'], $client_id)) {
            $this->error_messages['name'] = "You dont have access for this serviceplan or serviceplan doesn't exists.";
            return $this->error_messages;
        }

        $sp_name = $params["name"];
        $existed_sp = \FPanel\Models\Serviceplans::findFirst("name = '$sp_name'");
        if(!$existed_sp) {
            return ["name" => "Serviceplan doesn't exists"];
        }

        $serviceplan = new Serviceplans();
        $serviceplan->user_type = $client_type;
        $serviceplan->user_id = $client_id;
        $on_off_statuses = [
            "is_default",
            "access_panel",
            "man_client",
            "man_domain",
            "man_subdomain",
            "man_domain_alias",
            "man_email_alias",
            "man_hosting_settings",
            "man_task",
            "man_dns",
            "man_mailing_lists",
            "man_spam_filters",
            "man_backup",
            "man_email",
            "man_database",
            "man_protect_dir",
            "man_ssl",
            "man_template",
            "overuse",
            "php",
//            "cgi",
            "perl",
            "python",
//            "fast_cgi",
            "statistic_support",
            "asp",
            "aspnet",
            "aspnet_core",
            "dedicated_pool",
        ];
        $post_data = [
            "serviceplan_name" => $params["name"],
            "expire_time_limit" => $params["expire_time"],
            "client_limit" => $params["client_limit"],
            "domain_limit" => $params["domain_limit"],
            "disk_limit" => $params["disk_limit"],
            "traffic_limit" => $params["traffic_limit"],
            "subdomain_limit" => $params["subdomain_limit"],
            "domain_alias_limit" => $params["domain_alias_limit"],
            "email_box_limit" => $params["email_box_limit"],
            "email_box_size_limit" => $params["email_box_size_limit"],
            "email_forward_limit" => $params["email_forward_limit"],
            "email_groups_limit" => $params["email_groups_limit"],
            "email_autoresponder_limit" => $params["email_autoresponder_limit"],
            "email_lists_limit" => $params["email_lists_limit"],
            "database_limit" => $params["database_limit"],
            "database_size_limit" => $params["database_size_limit"],
            "template_limit" => $params["template_limit"],

            "php_version" => $this->env->getPhpVersion($params["php_version"], true),
            "max_cpu" => $params["max_cpu"],
            "statistic_type" => $this->env->getStatisticsType($params["statistic_type"], true),
            "aspnet_version" => $this->env->getNetVersion($params["aspnet_version"], true),
            "application_pool_mode" => $this->env->getApplicationMode($params["application_pool_mode"], true),

            "cgi" => OFF,
            "fast_cgi" => OFF,
        ];

        foreach ($on_off_statuses as $key) {
            if($params[$key]) {
                $post_data[$key] = ON;
            }
            else {
                $post_data[$key] = OFF;
            }
        }
        $serviceplan->setPosts($post_data);
        if(!$serviceplan->update($existed_sp->id)) {
            return $serviceplan->getMessages();
        }
        return true;
    }

    private function templateDelete($params, $client_id, $client_type)
    {
        $requeired_keys = [
            "name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        if(!$this->env->checkServiceplanOwner($params['name'], $client_id)) {
            $this->error_messages['name'] = "You dont have access for this serviceplan or serviceplan doesn't exists.";
            return $this->error_messages;
        }
        $serviceplan = new Serviceplans();
        $serviceplan->user_type = $client_type;
        $serviceplan->user_id = $client_id;
        $sp_name = $params["name"];
        $existed_sp = \FPanel\Models\Serviceplans::findFirst("name = '$sp_name'");
        if(!$existed_sp) {
            return ["name" => "Serviceplan doesn't exists"];
        }
        if(!$serviceplan->delete($existed_sp->id)) {
            return $serviceplan->getMessages();
        }
        return true;
    }
}