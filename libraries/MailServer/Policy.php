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
 */namespace FPanel\MailServer;

use FPanel\Environments\Datatable;
use FPanel\Models\PolicySettings;
use FPanel\Process\MailServer;

/**
 * Class Policy
 * @package FPanel\Server
 */
trait Policy
{
    public function policyLists($server_id): array
    {
        $datatable = new Datatable();
        if($server_id != "1") {
            $delete_button = $datatable->buttons("mailserver/deletepolicy/$1/server/$server_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'), __('Delete'));
            $edit_button = $datatable->buttons("mailserver/editpolicy/$1/server/$server_id",'modal',array('i' => 'fa fa-edit','a' => 'text-info'), __('Edit'), "edit_modal", null, "edit-policy");
        }
        else {
            $delete_button = $datatable->buttons("mailserver/deletepolicy/$1",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'), __('Delete'));
            $edit_button = $datatable->buttons("mailserver/editpolicy/$1",'modal', array('i' => 'fa fa-edit','a' => 'text-info'), __('Edit'), "edit_modal", null,"edit-policy");
        }

        $data = $datatable->select([
            "system_policy_settings.name",
            "system_policy_settings.priority",
            "system_policy_settings.description",
            "system_policy_settings.disabled",
            "system_policy_settings.id",
        ])
            ->from("system_policy_settings")
            ->where("system_policy_settings.server_id = '$server_id'")
            ->searchColumn([
                "system_policy_settings.name",
                "system_policy_settings.priority",
                "system_policy_settings.disabled",
            ])
            ->addColumn('actions', $edit_button.$delete_button,'system_policy_settings.id')
            ->unsetColumns('id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value) {
            if($value[3] == "1") {
                $data['aaData'][$key][3] = '<span class="text-danger">'. __("Disable") .'</span>';
            }
            else {
                $data['aaData'][$key][3] = '<span class="text-success">'. __("Active") .'</span>';
            }
            $policy_id = $value[4];
            $data["aaData"][$key][4] = $value[5];
            unset($value[5]);
            $data["aaData"][$key][5] = [
                "name" => $value[0],
                "priority" => $value[1],
                "description" => $value[2],
                "status" => $value[3] == "1" ? "disabled" : "enabled",
                "members" => $this->uri->url_encrypt("mailserver/policymember/$policy_id" . ($server_id != "1" ? "/server/$server_id" : "")),
                "add_members" => $this->uri->url_encrypt("mailserver/addpolicymember/$policy_id" . ($server_id != "1" ? "/server/$server_id" : "")),
                "quotas" => $this->uri->url_encrypt("mailserver/quotas/$policy_id" . ($server_id != "1" ? "/server/$server_id" : "")),
                "add_quotas" => $this->uri->url_encrypt("mailserver/addquotas/$policy_id" . ($server_id != "1" ? "/server/$server_id" : "")),
            ];
        }

        return $data;
    }

    public function addPolicy(): bool
    {
        if(!$this->policyValidate())
            return false;
        $name = $this->getPost("name");
        $policy = PolicySettings::findFirst("name = '$name'");
        if($policy) {
            $this->error_messages["name"] = __("Policy name is already exists");
            return false;
        }

        $mailserver = new MailServer();
        $params = [
            "min-servers"       => $this->getPost("min_servers"),
            "min-spare-servers" => $this->getPost("min_spare_servers"),
            "max-spare-servers" => $this->getPost("max_spare_servers"),
            "max-servers"       => $this->getPost("max_servers"),
            "max-requests"      => $this->getPost("max_requests"),
            "log-level"         => $this->getPost("log_level"),
            "log-detail"        => join(",", $this->getPost("log_detail")),
            "timeout-busy"      => $this->getPost("timeout_busy"),
            "bypass-timeout"    => $this->getPost("bypass_timeout"),
        ];

        $mailserver->setServerId($this->getPost("server_id"));
        $mailserver->addPolicy($params);


        $policies = new PolicySettings();
        $policies->assign([
            "name"          => $this->getPost("name"),
            "description"   => $this->getPost("description"),
            "priority"      => $this->getPost("priority"),
            "is_disabled"   => $this->getPost("status") == "enabled" ? "0" : "1",
            "server_id"     => $this->getPost("server_id"),
        ]);

        if(!$policies->create()) {
            foreach ($policies->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }
        return true;
    }

    public function updatePolicy($policy_id): bool
    {
        if(!$this->policyValidate())
            return false;
        $name = $this->getPost("name");

        $policy = PolicySettings::findFirstById($policy_id);
        if(trim($policy->name) != trim($name)) {
            $policy_check = PolicySettings::findFirst("name = '$name'");
            if($policy_check) {
                $this->error_messages["name"] = __("Policy name is already exists");
                return false;
            }
        }
        if(!$policy) {
            $this->error_messages["name"] = __("Policy not found");
            return false;
        }

        $mailserver = new MailServer();
        $params = [
            "min-servers"       => $this->getPost("min_servers"),
            "min-spare-servers" => $this->getPost("min_spare_servers"),
            "max-spare-servers" => $this->getPost("max_spare_servers"),
            "max-servers"       => $this->getPost("max_servers"),
            "max-requests"      => $this->getPost("max_requests"),
            "log-level"         => $this->getPost("log_level"),
            "log-detail"        => join(",", $this->getPost("log_detail")),
            "timeout-busy"      => $this->getPost("timeout_busy"),
            "bypass-timeout"    => $this->getPost("bypass_timeout"),
        ];

        $mailserver->setServerId($this->getPost("server_id"));
        $mailserver->updatePolicy($params);

        $policy->assign([
            "name"          => $this->getPost("name"),
            "description"   => $this->getPost("description"),
            "priority"      => $this->getPost("priority"),
            "is_disabled"   => $this->getPost("status") == "enabled" ? "0" : "1",
        ]);

        if(!$policy->create()) {
            foreach ($policy->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }


        return true;
    }

    public function deletePolicy($policy_id): bool
    {
        $policy = PolicySettings::findFirstById($policy_id);
        if(!$policy) {
            $this->error_messages["message"] = __("Policy not found");
            return false;
        }

        $mailserver = new MailServer();
        $params = [
            "min-servers"       => $this->getPost("min_servers"),
            "min-spare-servers" => $this->getPost("min_spare_servers"),
            "max-spare-servers" => $this->getPost("max_spare_servers"),
            "max-servers"       => $this->getPost("max_servers"),
            "max-requests"      => $this->getPost("max_requests"),
            "log-level"         => $this->getPost("log_level"),
            "log-detail"        => join(",", $this->getPost("log_detail")),
            "timeout-busy"      => $this->getPost("timeout_busy"),
            "bypass-timeout"    => $this->getPost("bypass_timeout"),
        ];

        $mailserver->setServerId($this->getPost("server_id"));
        $mailserver->deletePolicy($params);

//        $policy_data = $policy->toArray();
        $policy->delete();

        return true;
    }

    private function policyValidate(): bool
    {
        $required = array(
            array(
                'field' => 'name',
                'item' => $this->getPost("name"),
                'message' => __('Name is required')
            ),
            array(
                'field' => 'priority',
                'item' => $this->getPost("priority"),
                'message' => __('Priority is required')
            ),
            array(
                'field' => 'status',
                'item' => $this->getPost("status"),
                'message' => __('Status is required')
            ),
        );

        $this->validation->Required($required)
            ->Numeric($this->getPost("priority"),'priority');

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    public function savePolicySettings(): bool
    {
        $required = array(
            array(
                'field' => 'min_servers',
                'item' => $this->getPost("min_servers"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'min_spare_servers',
                'item' => $this->getPost("min_spare_servers"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'max_spare_servers',
                'item' => $this->getPost("max_spare_servers"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'max_servers',
                'item' => $this->getPost("max_servers"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'max_requests',
                'item' => $this->getPost("max_requests"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'timeout_busy',
                'item' => $this->getPost("timeout_busy"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'bypass_timeout',
                'item' => $this->getPost("bypass_timeout"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'log_level',
                'item' => $this->getPost("log_level"),
                'message' => __('This field is required')
            ),
            array(
                'field' => 'log_detail',
                'item' => $this->getPost("log_detail"),
                'message' => __('This field is required')
            ),
        );

        $this->validation->Required($required)
            ->Numeric($this->getPost("min_servers"),'min_servers')
            ->Numeric($this->getPost("min_spare_servers"),'min_spare_servers')
            ->Numeric($this->getPost("max_spare_servers"),'max_spare_servers')
            ->Numeric($this->getPost("max_servers"),'max_servers')
            ->Numeric($this->getPost("max_requests"),'max_requests')
            ->Numeric($this->getPost("timeout_busy"),'timeout_busy')
            ->Numeric($this->getPost("bypass_timeout"),'bypass_timeout')
            ->Numeric($this->getPost("log_level"),'log_level')
            ->InclusionIn($this->getPost("log_level"),'log_level', [0,1,2,3,4]);

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        $mailserver = new MailServer();
        $params = [
            "min-servers"       => $this->getPost("min_servers"),
            "min-spare-servers" => $this->getPost("min_spare_servers"),
            "max-spare-servers" => $this->getPost("max_spare_servers"),
            "max-servers"       => $this->getPost("max_servers"),
            "max-requests"      => $this->getPost("max_requests"),
            "log-level"         => $this->getPost("log_level"),
            "log-detail"        => join(",", $this->getPost("log_detail")),
            "timeout-busy"      => $this->getPost("timeout_busy"),
            "bypass-timeout"    => $this->getPost("bypass_timeout"),
        ];

        $mailserver->setServerId($this->getPost("server_id"));
        $mailserver->savePolicyConf($params);
        return true;
    }

    public function getPolicySettings()
    {
        $mailserver = new MailServer();
//        $params = array(
//        );
        return json_decode($mailserver->getPolicyConf(), true);
    }
}
