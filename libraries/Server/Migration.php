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
 */namespace FPanel\Server;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;

class Migration extends Library
{
    public function migrationlist($server_id): array
    {

        $mig_status = \FPanel\Models\Migration::findFirst("status LIKE '1'");

        $datatable = new Datatable();

        if($server_id != "1") {
            $delete_button = $datatable->buttons("migration/delete/$1/server/$server_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("migration/edit/$1/server/$server_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
            $start_button = $datatable->buttons("migration/start/$1/server/$server_id",'normal',array('i' => 'fa fa-exchange','a' => 'text-warning'),__('Start'),null,'data-id="nested"');
            $status_button = $datatable->buttons("migration/status/$1/server/$server_id",'normal',array('i' => 'fa fa-spinner','a' => 'text-info'),__('Status'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('migration/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('migration/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
            $start_button = $datatable->buttons('migration/start/$1','normal',array('i' => 'fa fa-exchange','a' => 'text-warning'),__('Start'),null,'data-id="nested"');
            $status_button = $datatable->buttons('migration/status/$1','normal',array('i' => 'fa fa-spinner','a' => 'text-info'),__('Status'),null,'data-id="nested"');
        }

        $datatable->select(array('migration.id',
            'migration.server_type',
            'migration.ip_address',
            'migration.description',
            'migration.created_date',
            'migration.last_migration_date',
            'migration.status'))
            ->from("migration")
            ->where("migration.server_id = '$server_id'");

        if($mig_status) {
            $datatable->addColumn('actions', $status_button,'migration.id');
        }
        else {
            $datatable->addColumn('actions', $edit_button.$delete_button.$start_button,'migration.id');
        }

        $datatable->searchColumn(array('migration.server_type','migration.description','migration.status'));
        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){

            switch($value[1]) {
                case "fpanel":
                    $data['aaData'][$key][0] = '<span class="label label-info"> Fpanel </span>';
                    break;
                case "plesk":
                    $data['aaData'][$key][0] = '<span class="label label-info"> Plesk </span>';
                    break;
                case "cpanel":
                    $data['aaData'][$key][0] = '<span class="label label-info"> Cpanel </span>';
                    break;
            }
            $data['aaData'][$key][1] = $value[2];
            $data['aaData'][$key][2] = $value[3];
            $data['aaData'][$key][3] = $this->env->convertDate($value[4],null,false);
            if($value[5] != null)
                $data['aaData'][$key][4] = $this->env->convertDate($value[5],null,false);
            else
                $data['aaData'][$key][4] = "";


            // Status
            switch($value[6]) {
                case "0":
                    $data['aaData'][$key][5] = '<span class="label label-warning"> '. __('Not Started') .' </span>';
                    break;
                case "1":
                    $data['aaData'][$key][5] = '<span class="label label-success"> '. __('Processing') .' </span>';
                    break;
                case "2":
//                    $data['aaData'][$key][3] = '<span class="label label-important"> '. _('_Restoring_') .' </span>';
                    break;
            }

            $data['aaData'][$key][6] = $value[7];


        }
        return $data;
    }

    public function create($server_id): bool
    {

        if($this->validate() == false)
            return false;


        $connection_status = $this->connection();
        if (strpos(trim($connection_status),'Err') !== false) {
            $this->error_messages['content'] = __('Server connection error. Please check your field');
            return false;
        }

        $migration = new \FPanel\Models\Migration();
        $migration->assign(array(
            'server_type' => $this->getPost("migration_type"),
            'ip_address' => $this->getPost("ip_address"),
            'system' => $this->getPost("system"),
            'root'      => $this->getPost("username"),
            'root_pass'      => $this->getPost("password"),
            'admin'      => $this->getPost("panel_uname"),
            'admin_pass'      => $this->getPost("panel_upass"),
            'description'      => $this->getPost("description"),
            'status'     => "0",
            'server_id'     => $server_id
        ));

        if(!$migration->create()) {
            foreach ($migration->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        return true;
    }

    public function update($server_id): bool
    {
        if($this->validate() == false)
            return false;

        $connection_status = $this->connection();
        if (strpos(trim($connection_status),'Err') !== false) {
            $this->error_messages['content'] = __('Server connection error. Please check your field');
            return false;
        }

        $migration = \FPanel\Models\Migration::findFirstById($this->getPost("id"));
        if(!$migration) {
            $this->error_messages['content'] = __('Migration record not found');
            return false;
        }
        $migration->assign(array(
            'server_type' => $this->getPost("migration_type"),
            'ip_address' => $this->getPost("ip_address"),
            'system' => $this->getPost("system"),
            'root'      => $this->getPost("username"),
            'root_pass'      => $this->getPost("password"),
            'admin'      => $this->getPost("panel_uname"),
            'admin_pass'      => $this->getPost("panel_upass"),
            'description'      => $this->getPost("description"),
            'server_id'      => $server_id,
            'status'     => "0",
        ));

        if(!$migration->update()) {
            foreach ($migration->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        return true;
    }

    public function delete($id): bool
    {
        $migration = \FPanel\Models\Migration::findFirstById($id);
        if($migration) {
            if(!$migration->delete()) {
                foreach($migration->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    private function connection()
    {
        $params = array(
            'user'  => $this->getPost("username"),
            'passwd'  => $this->getPost("password"),
            'ipaddr'  => $this->getPost("ip_address"),
            'system'  => $this->getPost("system"),
        );
        $migration = new \FPanel\Process\Migration();
        return $migration->connection($params);
    }

    public function start($id): bool
    {
        $options = array(
            'skip_global_settings' => $this->getPost("skip_global_settings"),
            'skip_domain_datas' => $this->getPost("skip_domain_datas"),
            'replace_exists_accounts' => $this->getPost("replace_exists_accounts"),
            'replace_exists_domains' => $this->getPost("replace_exists_domains"),
        );

        $selected_resellers = array();
        $selected_clients = array();
        $selected_domains = array();

        if(!empty($this->getPost("selected_resellers"))) {
            foreach($this->getPost("selected_resellers") as $reseller) {
                if($reseller != null)
                    $selected_resellers[$reseller] = false;
            }
        }
        if(!empty($this->getPost("selected_clients"))) {
            foreach($this->getPost("selected_clients") as $client) {
                if($client != null)
                    $selected_clients[$client] = false;
            }
        }
        if(!empty($this->getPost("selected_domains"))) {
            foreach($this->getPost("selected_domains") as $domain) {
                if($domain != null)
                    $selected_domains[$domain] = false;
            }
        }

        $migration = \FPanel\Models\Migration::findFirstById($id);
        if($migration) {
            $migration_data = $migration->toArray();
            $migration->assign(array(
                'status' => '1',
                'last_migration_date' => $this->date->dateTime()
            ));
            if (!$migration->update()) {
                foreach ($migration->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                return false;
            }
        }
        else {
            foreach ($migration->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        $config_data = array(
            'migration_id' => $id,
            'remote_user' => $this->getPost("remote_user"),
            'remote_pass' => $this->getPost("remote_pass"),
            'domains' => $selected_domains,
            'clients'   => $selected_clients,
            'resellers' => $selected_resellers,
            'options'   => $options,
            'ip_address' => $this->getPost("ip_address"),
            'root'      => $this->getPost("root"),
            'root_pass' => $this->getPost("root_pass"),
            'server_type' => $this->getPost("server_type"),
            'system' => $this->getPost("system"),
            'progress' => "0",
        );


        $this->env->writeConfig(ROOTPATH . '/tmp',".migration.config", $config_data);

        $this->startMigration($migration_data);
        return true;
    }

    public function enableMigration($data)
    {
        $params = array(
            'user'  => $data['root'],
            'passwd'  => $data['root_pass'],
            'ipaddr'  => $data['ip_address'],
            'system'  => $data['system'],
        );
        $migration = new \FPanel\Process\Migration();
        return $migration->enable($params);
    }

    private function startMigration($data)
    {
        $params = array(
            'migration-id' => $data['id'],
        );
        $migration = new \FPanel\Process\Migration();
        $migration->start($params);
    }

    private function validate(): bool
    {
        $required = array(
            array(
                'item'   => $this->getPost("migration_type"),
                'field'  => 'migration_type',
                'message' => __('Please select a type')
            ),
            array(
                'item'   => $this->getPost("ip_address"),
                'field'  => 'ip_address',
                'message' => __('IP Address is required')
            ),
            array(
                'item'   => $this->getPost("username"),
                'field'  => 'username',
                'message' => __('Username is required')
            ),
            array(
                'item'   => $this->getPost("password"),
                'field'  => 'password',
                'message' => __('Password is required')
            ),
            array(
                'item'   => $this->getPost("panel_uname"),
                'field'  => 'panel_uname',
                'message' => __('Panel Username required')
            ),
            array(
                'item'   => $this->getPost("panel_upass"),
                'field'  => 'panel_upass',
                'message' => __('Panel User Password required')
            )
        );

        $this->validation->Required($required)
            ->Ip($this->getPost("ip_address"),'ip_address')
            ->InclusionIn($this->getPost("migration_type"),'migration_type',array('fpanel','plesk','cpanel'));
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

}