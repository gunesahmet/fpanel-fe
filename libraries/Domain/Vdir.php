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
 */namespace FPanel\Domain;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\Domains;
use FPanel\Models\VirtualDirectory;
use FPanel\Process\Vhost;

class Vdir extends Library
{
    public function vdirList($domain_id, $nested = false): array
    {
        $datatable = new Datatable();

        if($nested) {
            $delete_button = $datatable->buttons('vdir/delete/$1/domain/'.$domain_id,'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('vdir/edit/$1/domain/'.$domain_id,'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('vdir/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('vdir/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array('virtual_directory.id','virtual_directory.name','virtual_directory.path','virtual_directory.app'))
            ->from("virtual_directory")
            ->where("virtual_directory.domain_id = '$domain_id'");
        if($nested == true) {
            $datatable->editColumn('virtual_directory.name', '<a href="'.'vdir/edit/$1/domain/'.$domain_id.'">$2</a>', 'virtual_directory.id,virtual_directory.name');
        }
        else {
            $datatable->editColumn('virtual_directory.name', '<a href="'.'vdir/edit/$1">$2</a>', 'virtual_directory.id,virtual_directory.name');
        }

        $data = $datatable->addColumn('actions', $edit_button.$delete_button,'virtual_directory.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = '<input class="checkbox-blue" type="checkbox" name="vdirs[]" value="'. $value[0] .'" />';
            if($value[3] == "1")
                $data['aaData'][$key][3] = '<span class="label label-info"> '. __('Application') .' </span>';
            else
                $data['aaData'][$key][3] = '<span class="label label-warning"> '. __('Virtual Directory') .' </span>';
        }

        return $data;
    }

    public function saveVdirSettings(): bool
    {
        if($this->validate() == false)
            return false;

        $default_document_files = "";
        foreach($this->getPost("default_document_order") as $value) {
            if($default_document_files == null) {
                $default_document_files = $value;
            }
            else {
                $default_document_files = $default_document_files.','.$value;
            }
        }

        $directory_browse = "true";
        if($this->getPost("directory_browse") != ON) {
            $directory_browse = "false";
        }

        $logging = "true";
        if($this->getPost("logging") != ON) {
            $logging = "false";
        }

        $parent_path = "true";
        if($this->getPost("parent_path") != ON) {
            $parent_path = "false";
        }

        $execute_in_mta = "true";
        if($this->getPost("execute_in_mta") != ON) {
            $execute_in_mta = "false";
        }

        $use_default_document = "true";
        if($this->getPost("use_default_document") != ON) {
            $use_default_document = "false";
        }

        $allow_anonymous = "true";
        if($this->getPost("allow_anonymous") != ON) {
            $allow_anonymous = "false";
        }

        $client_debugging = "true";
        if($this->getPost("client_debugging") != ON) {
            $client_debugging = "false";
        }

        $server_debugging = "true";
        if($this->getPost("server_debugging") != ON) {
            $server_debugging = "false";
        }


        $access_policy = null;
        foreach($this->getPost("accessPolicy") as $policy) {
            if($access_policy == null)
                $access_policy = $policy;
            else
                $access_policy = $access_policy . '#' . $policy;
        }

        if($access_policy == null)
            $access_policy = "None";


        $params = array(
            'domain' => $this->getPost("domain_name"),
            'prefix' => $this->getPost("prefix"),
            'virtual-dir-name' => $this->getPost("vdir_name"),
            'dir-browsing' => $directory_browse,
            'log-visits' => $logging,
            'parent-path' => $parent_path,
            'exec-mta' => $execute_in_mta,
            'doc-status' => $use_default_document,
            'def-file' => $default_document_files,
            'anony-auth-status' => $allow_anonymous,
            'buff-limit' => $this->getPost("buffer_limit"),
            'req-ent-allowed' => $this->getPost("entity_size"),
            'appallow-client-debug' => $client_debugging,
            'appallow-debug' => $server_debugging,
            'acc-policy' => $access_policy,
        );

        $vhostmng = new Vhost();
        $vhostmng->savevDirSettings($params);

        return true;
    }

    public function createVirtualDirectory(): bool
    {
        if($this->validate('virtual_dir') == false)
            return false;

        $app = "1";
        if($this->getPost("create_application") != ON)
            $app = "0";


        $domain_id = $this->getPost("domain_id");
        $dir_name = $this->getPost("dir_name");
        $path = $this->getPost("physical_path");
        $virtual_directory = new VirtualDirectory();
        $assign = array(
            "id"            => $this->env->createUuid(),
            'domain_id' => $domain_id,
            'name' => $dir_name,
            'path' => $path,
            'app' => $app,
        );

        $virtual_directory->assign($assign);

        if(!$virtual_directory->create()) {
            foreach ($virtual_directory->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Vdir->createVirtualDirectory",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            $this->createVdirCurl();
            return true;
        }
    }

    private function createVdirCurl()
    {
        $access_policy = null;
        foreach($this->getPost("accessPolicy") as $policy) {
            if($access_policy == null)
                $access_policy = $policy;
            else
                $access_policy = $access_policy . '#' . $policy;
        }

        $client_debugging = "true";
        if($this->getPost("client_debugging") != ON) {
            $client_debugging = "false";
        }
        $server_debugging = "true";
        if($this->getPost("server_debugging") != ON) {
            $server_debugging = "false";
        }
        $domain_name = $this->getPost("domain_name");
        $domains = Domains::findFirst("name = '$domain_name'");

        $params = array(
            'domain' => $domain_name,
            'prefix' => $domains->prefix,
            'virtual-dir-name' => $this->getPost("dir_name"),
            'physical-path' => $this->getPost("physical_path"),
            'acc-policy' => $access_policy,
            'appallow-client-debug' => $client_debugging,
            'appallow-debug' => $server_debugging,
            'buff-limit' => $this->getPost("buffer_limit"),
            'req-ent-allowed' => $this->getPost("entity_size")
        );

        if($this->getPost("create_application") != ON) {
            $params['virtual-dir-mode'] = "vdir";
        }
        else {
            $params['virtual-dir-mode'] = "app";
        }

        $vhostmng = new Vhost();
        $vhostmng->createVdir($params);
    }

    public function updateVdirCurl(): bool
    {
        $access_policy = null;
        foreach($this->getPost("accessPolicy") as $policy) {
            if($access_policy == null)
                $access_policy = $policy;
            else
                $access_policy = $access_policy . '#' . $policy;
        }

        $client_debugging = "true";
        if($this->getPost("client_debugging") != ON) {
            $client_debugging = "false";
        }
        $server_debugging = "true";
        if($this->getPost("server_debugging") != ON) {
            $server_debugging = "false";
        }
        $domain_name = $this->getPost("domain_name");
        $domains = Domains::findFirst("name = '$domain_name'");

        $params = array(
            'domain' => $domain_name,
            'prefix' => $domains->prefix,
            'virtual-dir-name' => $this->getPost("dir_name"),
            'physical-path' => $this->getPost("physical_path"),
            'acc-policy' => $access_policy,
            'appallow-client-debug' => $client_debugging,
            'appallow-debug' => $server_debugging,
            'buff-limit' => $this->getPost("buffer_limit"),
            'req-ent-allowed' => $this->getPost("entity_size")
        );

        $vhostmng = new Vhost();
        $vhostmng->updateVdir($params);

        return true;

    }

    public function deleteVdir(): bool
    {
        $id = $this->getPost("id");
        $domain_id = $this->getPost("domain_id");
        $vdir = VirtualDirectory::findFirst("id = '$id' AND domain_id = '$domain_id'");
        if($vdir) {
            if($vdir->app == "0")
                $app = "vdir";
            else
                $app = "app";
            $vdir_name = $vdir->name;
            $this->setPost("dir_name", $vdir_name);
            $this->setPost("type", $app);

            if(!$vdir->delete()) {
                foreach($vdir->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Vdir->deleteVdir",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
            $this->removeVdirCurl();
            return true;
        }
        else {
            return false;
        }
    }

    public function deleteAll($domain_id): bool
    {
        $vdirs = VirtualDirectory::find("domain_id = '$domain_id'");
        if(!empty($vdirs)) {
            if(!$vdirs->delete()) {
                foreach($vdirs->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Vdir->deleteAll",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
            return true;
        }
        else {
            return false;
        }
    }

    private function removeVdirCurl()
    {
        $domain_name = $this->getPost("domain_name");
        $domains = Domains::findFirst("name = '$domain_name'");
        $params = array(
            'domain' => $domain_name,
            'prefix' => $domains->prefix,
            'virtual-dir-name' => $this->getPost("dir_name"),
            'virtual-dir-mode' => $this->getPost("type"),
        );
        $vhostmng = new Vhost();
        $vhostmng->removeVdir($params);
    }

    private function validate($setting_type = "general"): bool
    {
        $required = array(
            array(
                'field' => 'buffer_limit',
                'item' => $this->getPost("buffer_limit"),
                'message' => __('Buffer Limit is required')
            ),
            array(
                'field' => 'entity_size',
                'item' => $this->getPost("entity_size"),
                'message' => __('Entity Size is required')
            ),
        );
        if($setting_type == "virtual_dir") {
            $required[] = array(
                'field' => 'dir_name',
                'item' => $this->getPost("dir_name"),
                'message' => __('Directory name is required')
            );
            $required[] = array(
                'field' => 'physical_path',
                'item' => $this->getPost("physical_path"),
                'message' => __('Physical path is required')
            );
        }

        $this->validation->Required($required)
            ->Numeric($this->getPost("entity_size"),'entity_size',__('Please enter valid size'))
            ->Numeric($this->getPost("buffer_limit"),'buffer_limit',__('Please enter valid buffer size'));

        if($setting_type == "general") {
            foreach($this->getPost("default_document_order") as $document)
                $this->validation->WithoutSpace($document,'default_document_order',__('Please enter valid document name'));
        }

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}