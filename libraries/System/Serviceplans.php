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

use FPanel\Environments\Datatable;
use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;

class Serviceplans extends Library
{
    public $user_id;    // Login olan user"in idsi
    public $user_type;  // Login olan user"in type"i

    public function save(): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists("Users"))
                $this->redis->del("Users");
        }
        if($this->has("is_default") || ($this->getPost("is_default") != ON) || ($this->getPost("is_default") != OFF)) {
            $this->error_messages["is_default"] = __("Value is must be valid");
        }

        if(!$this->validations(false, null)) {
            return false;
        }

        $serviceplan = new \FPanel\Models\Serviceplans();
        $assign = $this->convert();

//        $this->debug->debug($assign, false);
        $assign["id"] = $this->env->createUuid();
        $assign["client_id"] = $this->user_id;

        $serviceplan->assign($assign);

        if (!$serviceplan->create()) {
            foreach ($serviceplan->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Serviceplans->save",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function update($sp_id): bool
    {
        $user_id = $this->user_id;
        $serviceplan = \FPanel\Models\Serviceplans::findFirst("client_id = '$user_id' and id = '$sp_id'");

        if(!$serviceplan) {
            $this->error_messages["serviceplan_name"] = __("This Serviceplan does not exist");
            return false;
        }

        if(!$this->validations(true, $serviceplan)) {
            return false;
        }

        $assign = $this->convert();
        $serviceplan->assign($assign);

        if (!$serviceplan->update()) {
            foreach ($serviceplan->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Serviceplans->update",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

        if(!empty($this->error_messages)) {
            return false;
        }
        else {
            return true;
        }

    }

    public function delete($id): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists("Users"))
                $this->redis->del("Users");
        }
        $user_id = $this->user_id;
        $serviceplan = \FPanel\Models\Serviceplans::find("client_id = '$user_id'");

        if($serviceplan) {
            $serviceplan_data = $serviceplan->toArray();
            $sp_list = array();
            foreach ($serviceplan_data as $key) {
                $sp_list[$key["id"]] = $key["id"];
            }
            if(!in_array($id, $sp_list)) {
                return false;
            }
        }

        $serviceplan = \FPanel\Models\Serviceplans::findFirstById($id);
        if (array_key_exists("id", $serviceplan->toArray())) {
            if (!$serviceplan->delete()) {
                foreach ($serviceplan->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Serviceplans->delete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        else {
            return false;
        }

    }

    public function deleteClient($user_id): bool
    {
        $serviceplans = \FPanel\Models\Serviceplans::find("client_id = '$user_id'");
        if(!empty($serviceplans)) {
            if (!$serviceplans->delete()) {
                foreach ($serviceplans->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Serviceplans->delete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    public function convert(): array
    {
        $data = array();
        $data["name"] = $this->getPost("serviceplan_name");

        $data["expire_time"] = $this->getPost("expire_time_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("expire_time_limit", UNLIMITED);
        $data["subdomain_limit"] = $this->getPost("subdomain_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("subdomain_limit", UNLIMITED);
        $data["domain_alias_limit"] = $this->getPost("domain_alias_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("domain_alias_limit", UNLIMITED);
        $data["email_box_limit"] = $this->getPost("email_box_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("email_box_limit", UNLIMITED);
//        $data["email_box_size_limit"] = $this->getPost("email_box_size_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("email_box_size_limit", UNLIMITED);
        $data["email_forward_limit"] = $this->getPost("email_forward_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("email_forward_limit", UNLIMITED);
        $data["email_groups_limit"] = $this->getPost("email_groups_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("email_groups_limit", UNLIMITED);
        $data["email_autoresponder_limit"] = $this->getPost("email_autoresponder_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("email_autoresponder_limit", UNLIMITED);
        $data["email_lists_limit"] = $this->getPost("email_lists_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("email_lists_limit", UNLIMITED);
        $data["database_limit"] = $this->getPost("database_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("database_limit", UNLIMITED);
//        $data["database_size_limit"] = $this->getPost("database_size_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("database_size_limit", UNLIMITED);
        $data["template_limit"] = $this->getPost("template_limit", UNLIMITED) == UNLIMITED ? UNLIMITED : $this->getPost("template_limit", UNLIMITED);

        $data["is_default"] = $this->getPost("is_default", OFF) == ON ? ACTIVE : PASSIVE;
        $data["overuse"] = $this->getPost("overuse", OFF) == ON ? ACTIVE : PASSIVE;
        $data["access_panel"] = $this->getPost("access_panel", OFF) == ON ? ACTIVE : PASSIVE;

        $data["man_hosting_settings"] = $this->getPost("man_hosting_settings", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_subdomain"] = $this->getPost("man_subdomain", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_database"] = $this->getPost("man_database", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_domain_alias"] = $this->getPost("man_domain_alias", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_email"] = $this->getPost("man_email", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_email_alias"] = $this->getPost("man_email_alias", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_task"] = $this->getPost("man_task", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_dns"] = $this->getPost("man_dns", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_ssl"] = $this->getPost("man_ssl", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_backup"] = $this->getPost("man_backup", OFF) == ON ? ACTIVE : PASSIVE;
        $data["man_protect_dir"] = $this->getPost("man_protect_dir", OFF) == ON ? ACTIVE : PASSIVE;

//        $data["man_mailing_lists"] = $this->getPost("man_mailing_lists", OFF) == ON ? ACTIVE : PASSIVE;
//        $data["man_spam_filters"] = $this->getPost("man_spam_filters", OFF) == ON ? ACTIVE : PASSIVE;

        $data["php"] = $this->getPost("php", OFF) == ON ? ACTIVE : PASSIVE;
        $data["php_version"] = $this->getPost("php_version", PHP56);
        $data["asp"] = $this->getPost("asp", OFF) == ON ? ACTIVE : PASSIVE;
        $data["aspnet_core"] = $this->getPost("aspnet_core", OFF) == ON ? ACTIVE : PASSIVE;
        $data["aspnet"] = $this->getPost("aspnet", OFF) == ON ? ACTIVE : PASSIVE;
        $data["aspnet_version"] = $this->has("aspnet_version") ? ($this->getPost("aspnet_version") == v40 ? v40 : v20) : v40;
        $data["application_pool_mode"] = $this->has("application_pool_mode") ? ($this->getPost("application_pool_mode") == INTEGRATED ? INTEGRATED : CLASSIC) : INTEGRATED;
        $data["perl"] = $this->getPost("perl", OFF) == ON ? ACTIVE : PASSIVE;
        $data["python"] = $this->getPost("python", OFF) == ON ? ACTIVE : PASSIVE;
        $data["statistic_support"] = $this->getPost("statistic_support", OFF) == ON ? ACTIVE : PASSIVE;
        $data["statistic_type"] = !$this->has("statistic_type") ? NO_STATS : ($this->getPost("statistic_type") == AWSTATS ? AWSTATS : WEBALIZER);
        $data["dedicated_pool"] = $this->getPost("dedicated_pool", OFF) == ON ? ACTIVE : PASSIVE;
        $data["max_cpu"] = is_numeric($this->getPost("max_cpu", 100)) ? $this->getPost("max_cpu", 100) : 100;

//        $data["cgi"] = $this->getPost("cgi", OFF) == ON ? ACTIVE : PASSIVE;
//        $data["fast_cgi"] = $this->getPost("fast_cgi", OFF) == ON ? ACTIVE : PASSIVE;

        $data["disk_limit"] = $this->getPost("disk_limit", UNLIMITED);
        if ($data["disk_limit"] != UNLIMITED) {
            $data["disk_limit"] =  $this->getPost("disk_limit")*1024*1024;
        }

        $data["traffic_limit"] =  $this->getPost("traffic_limit", UNLIMITED);
        if($data["traffic_limit"] != UNLIMITED) {
            $data["traffic_limit"] =  $this->getPost("traffic_limit")*1024*1024;
        }

        $data["email_box_size_limit"] = $this->getPost("email_box_size_limit");
        if ($data["email_box_size_limit"] != UNLIMITED) {
            $data["email_box_size_limit"] = $this->getPost("email_box_size_limit")*1024*1024;
        }

        $data["database_size_limit"] = $this->getPost("database_size_limit");
        if ($data["database_size_limit"] != UNLIMITED) {
            $data["database_size_limit"] = $this->getPost("database_size_limit")*1024*1024;
        }

        switch($this->user_type) {
            case ROOT_USER:
            case ADMIN_USER:
                $data["client_limit"] = $this->getPost("client_limit");
                $data["domain_limit"] = $this->getPost("domain_limit");
                $data["template_limit"] = $this->getPost("template_limit");
                $data["man_client"] = $this->getPost("man_client", OFF) == ON ? ALLOWED : NOT_ALLOWED;
                $data["man_template"] = $this->getPost("man_template", OFF) == ON ? ALLOWED : NOT_ALLOWED;
                $data["man_domain"] = $this->getPost("man_domain",OFF) == ON ? ALLOWED : NOT_ALLOWED;
                break;
            case RESELLER_USER:
                $data["client_limit"] = "0";
                $data["domain_limit"] = $this->getPost("domain_limit");
                $data["template_limit"] = $this->getPost("template_limit");
                $data["man_client"] = NOT_ALLOWED;
                $data["man_template"] = $this->getPost("man_template", OFF) == ON ? ALLOWED : NOT_ALLOWED;
                $data["man_domain"] = $this->getPost("man_domain", OFF) == ON ? ALLOWED : NOT_ALLOWED;
                break;
            case CLIENT_USER:
                $data["client_limit"] = "0";
                $data["domain_limit"] = "0";
                $data["template_limit"] = "0";
                $data["man_client"] = NOT_ALLOWED;
                $data["man_template"] = NOT_ALLOWED;
                $data["man_domain"] = NOT_ALLOWED;
                break;
        }
        return $data;
    }

    public function checkDefaultTemplateAndUniqueName(): bool
    {
        $qbuilder = new QueryBuilder();
        $serviceplan = $qbuilder->select([
            "is_default",
        ])
            ->from("serviceplans")
            ->where([
                "conditions" => "serviceplans.client_id = ?1",
                "bind" => [1 => $this->user_id]
            ])
            ->andWhere([
                "conditions" => "serviceplans.is_default = ?1",
                "bind" => [1 => ON]
            ])
            ->findFirst();
        if(!empty($serviceplan))
            return false;
        return true;
    }

    public function templateList($user_id, $nested = null): array
    {
        $datatable = new Datatable();

        if($nested) {
            $delete_button = $datatable->buttons("serviceplans/delete/$1/client/".$user_id,"ajax",array("i" => "fa fa-times","a" => "text-danger"),__("Delete"));
            $edit_button = $datatable->buttons("serviceplans/edit/$1/client/".$user_id,"normal",array("i" => "fa fa-edit","a" => "text-info"),__("Edit"),null,"data-id='nested'");
        }
        else {
            $delete_button = $datatable->buttons("serviceplans/delete/$1","ajax",array("i" => "fa fa-times","a" => "text-danger"),__("Delete"));
            $edit_button = $datatable->buttons("serviceplans/edit/$1","normal",array("i" => "fa fa-edit","a" => "text-info"),__("Edit"),null,"data-id='nested'");
        }


        $datatable->select(array(
            "serviceplans.id",
            "serviceplans.name",
            "users.username",
            "serviceplans.is_default",
            "serviceplans.overuse",
            "serviceplans.client_id",
        ))
            ->from("serviceplans")
            ->innerJoin("users","serviceplans.client_id = users.id")
            ->searchColumn(array("serviceplans.name"))
            ->orderableColumn(array("serviceplans.name","serviceplans.is_default","serviceplans.overuse","users.name"))
            ->addColumn("actions", $edit_button.$delete_button,"serviceplans.id");
        if($nested) {
            $datatable->where("serviceplans.client_id = '$user_id'");
        }
        if($nested) {
            $datatable->editColumn("serviceplans.name",'<a href="'. 'serviceplans/edit/$1/client/'. $user_id .'">$2</a>', "serviceplans.id, serviceplans.name");
        }
        else {
            $datatable->editColumn("serviceplans.name",'<a href="'. 'serviceplans/edit/$1">$2</a>', "serviceplans.id, serviceplans.name");
        }

        $data = $datatable->unsetColumns("serviceplans.client_id")
            ->generate()
            ->execute()
            ->result();

        foreach($data["aaData"] as $key => $value){
            $data["aaData"][$key][0] = "<input type='checkbox' class='checkbox-blue' value='". $value[0] ."' name='serviceplans[]'>";

            if ($value[3]){
                $data["aaData"][$key][3] = '<span class="label label-primary"> '. __("Default") .' </span>';
            }
            else {
                $data["aaData"][$key][3] = "";
            }

            if($value[4]) {
                $data["aaData"][$key][4] = '<span class="label label-primary"> '. __("On") .' </span>';
            }
            else {
                $data["aaData"][$key][4] = '<span class="label label-danger"> '. __("Off") .' </span>';
            }
            $data["aaData"][$key][5] = $value[6];
        }
        return $data;

    }

    private function validations(bool $update, \FPanel\Models\Serviceplans $serviceplan = null): bool
    {
        $required = array(
            array(
                "item" => $this->getPost("serviceplan_name"),
                "field" => "serviceplan_name",
                "message" => __("Please enter a name")
            ),
            array(
                "item" => $this->getPost("disk_limit"),
                "field" => "disk",
                "message" => __("Disk limit is required")
            ),
            array(
                "item" => $this->getPost("traffic_limit"),
                "field" => "traffic",
                "message" => __("Traffic limit is required")
            ),
            array(
                "item" => $this->getPost("subdomain_limit"),
                "field" => "subdomain",
                "message" => __("Subdomain limit is required")
            ),
            array(
                "item" => $this->getPost("email_box_limit"),
                "field" => "email_box",
                "message" => __("Email box limit is required")
            ),
            array(
                "item" => $this->getPost("domain_alias_limit"),
                "field" => "domain_alias",
                "message" => __("Domain alias limit is required")
            ),
            array(
                "item" => $this->getPost("email_box_size_limit"),
                "field" => "email_box_size",
                "message" => __("Email Box Size limit is required")
            ),
//            array(
//                "item" => $this->getPost("email_forward_limit"),
//                "field" => "email_forward",
//                "message" => __("Email Forward limit is required")
//            ),
//            array(
//                "item" => $this->getPost("email_groups_limit"),
//                "field" => "email_groups",
//                "message" => __("Email Group limit is required")
//            ),
//            array(
//                "item" => $this->getPost("email_autoresponder_limit"),
//                "field" => "email_autoresponder",
//                "message" => __("Email Autoresponder limit is required")
//            ),
//            array(
//                "item" => $this->getPost("email_lists_limit"),
//                "field" => "email_list",
//                "message" => __("Email List limit is required")
//            ),
            array(
                "item" => $this->getPost("database_limit"),
                "field" => "database",
                "message" => __("Database limit is required")
            ),
            array(
                "item" => $this->getPost("database_size_limit"),
                "field" => "database_size",
                "message" => __("Database size limit is required")
            ),
            array(
                "item" => $this->getPost("expire_time_limit"),
                "field" => "expire_time_limit",
                "message" => __("Expire time limit is required")
            ),
        );

        if($this->user_type < 2) {
            $required[] = array(
                "item" => $this->getPost("client_limit"),
                "field" => "clients",
                "message" => __("Client limit is required")
            );
            $required[] = array(
                "item" => $this->getPost("domain_limit"),
                "field" => "domain",
                "message" => __("Domain limit is required")
            );
        }
        if($this->user_type < 3) {
            $required[] = array(
                "item" => $this->getPost("domain_limit"),
                "field" => "domain",
                "message" => __("Domain limit is required")
            );
        }

        $this->validation->Required($required);

        foreach($required as $item) {
            if($item["field"] != "serviceplan_name") {
                if($item["item"] != UNLIMITED) {
                    $this->validation->Numeric($item["item"], $item["field"],__("Please enter valid limit"));
                }
            }
        }

        if($this->getPost("expire_time_limit") != UNLIMITED)
            $this->validation->MinMax($this->getPost("expire_time_limit"),"expire_time_limit",1,10,__("Please enter time limit 1-10 between."));

        $this->validation->ServicePlanName($this->getPost("serviceplan_name"), "serviceplan_name", __("Please enter valid name"));

        if($this->getPost("statistic_support", OFF) == ON) {
            $this->validation->InclusionIn($this->getPost("statistic_type"),"web_statistics_type",array(AWSTATS, WEBALIZER),__("Please choose valid statistics type"));
        }

        if(PANEL_OS_TYPE == WINDOWS) {
            if($this->getPost("php") == ON) {
                $this->validation->InclusionIn($this->getPost("php_version"),"php_version",array(PHP56, PHP72, PHP74),__("Please choose valid version"));
            }
            if($this->getPost("dedicated_pool") == ON) {
                $this->validation->Numeric($this->getPost("max_cpu"),"max_cpu_usage",__("CPU Usage must be numeric"));
                $this->validation->Between($this->getPost("max_cpu"),"max_cpu_usage","1","99",__("CPU Usage must 1-99"));
            }
            if($this->getPost("aspnet") == ON) {
                $this->validation->InclusionIn($this->getPost("aspnet_version"),"asp_net_version", [v20, v40],__("Please choose valid version"));
                $this->validation->InclusionIn($this->getPost("application_pool_mode"),"application_pool_mode",array(INTEGRATED, CLASSIC),__("Please choose valid mode"));
            }
        }

        if(!$update) {
            $this->validation->UniqueServiceplan($this->getPost("serviceplan_name"), $this->user_id,"serviceplan_name",__("Serviceplan name must be unique"));
            if($this->getPost("is_default") == ON)
                $this->validation->UniqueDefault($this->user_id,"is_default");
        }
        else {
            $this->validation->UniqueServiceplan($this->getPost("serviceplan_name"), $this->user_id,"serviceplan_name", __("Serviceplan name must be unique"),true, $serviceplan->name);
            if($this->getPost("is_default") == ON)
                if($serviceplan->is_default == PASSIVE)
                    $this->validation->UniqueDefault($this->user_id,"is_default");
        }

        $status = $this->validation->Validate();

        if(!$status) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        else {
            return true;
        }
    }
}