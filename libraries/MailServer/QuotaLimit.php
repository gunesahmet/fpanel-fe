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

/**
 * Class QuotaLimit
 * @package FPanel\Server
 */
trait QuotaLimit
{
    public function quotaLimitLists($policy_id, $server_id): array
    {
        $datatable = new Datatable();
        if($server_id != "1") {
            $delete_button = $datatable->buttons("mailserver/deletepolicymember/$policy_id/$1/server/$server_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'), __('Delete'));
            $edit_button = $datatable->buttons("mailserver/editpolicymember/$policy_id/$1/server/$server_id",'modal',array('i' => 'fa fa-edit','a' => 'text-info'), __('Edit'), "edit_modal", null, "edit-policy");
        }
        else {
            $delete_button = $datatable->buttons("mailserver/deletepolicymember/$policy_id/$1",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'), __('Delete'));
            $edit_button = $datatable->buttons("mailserver/editpolicymember/$policy_id/$1",'modal', array('i' => 'fa fa-edit','a' => 'text-info'), __('Edit'), "edit_modal", null,"edit-policy");
        }

        $data = $datatable->select([
            "system_policy_members.source",
            "system_policy_members.destination",
            "system_policy_members.disabled",
            "system_policy_members.id",
        ])
            ->from("system_policy_members")
            ->where("system_policy_members.server_id = '$server_id'")
            ->andWhere("system_policy_members.policy_id = '$policy_id'")
            ->searchColumn([
                "system_policy_members.source",
                "system_policy_members.destination",
                "system_policy_members.disabled",
            ])
            ->addColumn('actions', $edit_button.$delete_button,'system_policy_members.id')
            ->unsetColumns('id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value) {
            if($value[2] == "1") {
                $data['aaData'][$key][2] = '<span class="text-danger">'. __("Disable") .'</span>';
            }
            else {
                $data['aaData'][$key][2] = '<span class="text-success">'. __("Active") .'</span>';
            }
            $data["aaData"][$key][3] = $value[4];
            unset($value[4]);
            $data["aaData"][$key][4] = [
                "source" => $value[0],
                "destination" => $value[1],
                "status" => $value[2] == "1" ? "disabled" : "enabled",
            ];
        }
        return $data;
    }

    public function addQuotaLimit()
    {

    }

    public function updateQuotaLimit()
    {

    }

    public function deleteQuotaLimit()
    {

    }

    private function quotaLimitValidate(): bool
    {

        return true;
    }
}
