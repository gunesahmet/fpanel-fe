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
 */namespace FPanel\Backup;

use FPanel\Environments\Datatable;
use FPanel\Models\Domains;
use FPanel\Models\FtpRepo;
use FPanel\Models\ServerFtpRepo;
use FPanel\Models\Users;

trait Repo
{
    public function listRepo($domain_id, $nested = false): array
    {
        $domains = Domains::findFirstById($domain_id);
        $users = Users::findFirst("username = '$domains->name'");
        $datatable = new Datatable();
        if($nested == true) {
            $delete_button = $datatable->buttons('backup/repo/delete/$1/domain/'.$domain_id,'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backup/repo/edit/$1/domain/'.$domain_id,'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('backup/repo/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backup/repo/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array('ftp_repo.id','ftp_repo.ftp_server','ftp_repo.ftp_user','ftp_repo.status'))
            ->from("ftp_repo")
            ->where("ftp_repo.client_id = '$users->id'")
            ->addColumn('actions', $edit_button.$delete_button,'ftp_repo.id');

        if($nested == true) {
            $datatable->editColumn('ftp_repo.ftp_server','<a href="'. "backup/repo/edit/$1/domain/$domain_id\">$2</a>", 'ftp_repo.id, ftp_repo.ftp_server');
        }
        else {
            $datatable->editColumn('ftp_repo.ftp_server','<a href="'. 'backup/repo/edit/$1">$2</a>', 'ftp_repo.id, ftp_repo.ftp_server');
        }

        $data = $datatable->unsetColumns('ftp_repo.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = $value[1];
            $data['aaData'][$key][1] = $value[2];
            switch($value[3]) {
                case "on":
                    $data['aaData'][$key][2] = '<span class="label label-success"> '. __('Active'). '</span>';
                    break;
                default:
                    $data['aaData'][$key][2] = '<span class="label label-danger"> '. __('Passive'). ' </span>';
                    break;
            }

            $data['aaData'][$key][3] = $value[4];


        }
        return $data;

    }

    public function listServerRepo(): array
    {

        $datatable = new Datatable();
        $delete_button = $datatable->buttons('serverbackup/repo/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        $edit_button = $datatable->buttons('serverbackup/repo/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');

        $datatable->select(array('server_ftp_repo.id','server_ftp_repo.ftp_server','server_ftp_repo.ftp_user','server_ftp_repo.status'))
            ->from("server_ftp_repo")
            ->addColumn('actions', $edit_button.$delete_button,'server_ftp_repo.id');

        $datatable->editColumn('server_ftp_repo.ftp_server','<a href="'. 'serverbackup/repo/edit/$1">$2</a>', 'server_ftp_repo.id, server_ftp_repo.ftp_server');


        $data = $datatable->searchColumn(array('server_ftp_repo.ftp_server'))
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = $value[1];
            $data['aaData'][$key][1] = $value[2];
            switch($value[3]) {
                case "on":
                    $data['aaData'][$key][2] = '<span class="label label-success"> '. __('Active'). '</span>';
                    break;
                default:
                    $data['aaData'][$key][2] = '<span class="label label-danger"> '. __('Passive'). ' </span>';
                    break;
            }

            $data['aaData'][$key][3] = $value[4];


        }
        return $data;
    }

    public function listClientRepo($client_id, $nested = false): array
    {

        $datatable = new Datatable();
        if($nested == true) {
            $delete_button = $datatable->buttons('backupclient/repo/delete/$1/client/'.$client_id,'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backupclient/repo/edit/$1/client/'.$client_id,'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('backupclient/repo/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backupclient/repo/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array('ftp_repo.id','ftp_repo.ftp_server','ftp_repo.ftp_user','ftp_repo.status'))
            ->from("ftp_repo")
            ->where("ftp_repo.client_id = '$client_id'")
            ->addColumn('actions', $edit_button.$delete_button,'ftp_repo.id');

        if($nested == true) {
            $datatable->editColumn('ftp_repo.ftp_server','<a href="'. "backupclient/repo/edit/$1/client/$client_id\">$2</a>", 'ftp_repo.id, ftp_repo.ftp_server');
        }
        else {
            $datatable->editColumn('ftp_repo.ftp_server','<a href="'. 'backupclient/repo/edit/$1">$2</a>', 'ftp_repo.id, ftp_repo.ftp_server');
        }

        $data = $datatable->unsetColumns('ftp_repo.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = $value[1];
            $data['aaData'][$key][1] = $value[2];
            switch($value[3]) {
                case "on":
                    $data['aaData'][$key][2] = '<span class="label label-success"> '. __('Active'). '</span>';
                    break;
                default:
                    $data['aaData'][$key][2] = '<span class="label label-danger"> '. __('Passive'). ' </span>';
                    break;
            }

            $data['aaData'][$key][3] = $value[4];


        }
        return $data;
    }


    public function createRepo(): bool
    {
        if($this->repoValidation() == false)
            return false;

        if($this->ftpConnectionTest() == false)
            return false;

        $domain_name = $this->getPost("domain_name");
        $user = Users::findFirst("username = '$domain_name'");
        $status = "off";
        if((!empty($this->getPost("status"))) && ($this->getPost("status") == "on"))
            $status = "on";
        $ftp_repo = new FtpRepo();
        $ftp_repo->assign(array(
            "id" => $this->env->createUuid(),
            'client_id' => $user->id,
            'ftp_server' => $this->getPost("ftp_server"),
            'ftp_user' =>  $this->getPost("ftp_user"),
            'ftp_folder' =>  $this->getPost("ftp_folder"),
            'ftp_pass' => $this->crypto->bencode($this->getPost("ftp_pass")),
            'status' => $status,
        ));


        if (!$ftp_repo->create()) {
            foreach ($ftp_repo->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Repo.php",
                "Error" => "FtpRepo create failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }

    public function createServerRepo(): bool
    {
        if($this->repoValidation() == false)
            return false;

        if($this->ftpConnectionTest() == false)
            return false;

        $status = "off";
        if((!empty($this->getPost("status"))) && ($this->getPost("status") == "on"))
            $status = "on";
        $ftp_repo = new ServerFtpRepo();
        $ftp_repo->assign(array(
            'ftp_server' => $this->getPost("ftp_server"),
            'ftp_user' =>  $this->getPost("ftp_user"),
            'ftp_folder' =>  $this->getPost("ftp_folder"),
            'ftp_pass' => $this->crypto->bencode($this->getPost("ftp_pass")),
            'status' => $status,
        ));

        if (!$ftp_repo->create()) {
            foreach ($ftp_repo->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Repo.php",
                "Error" => "FtpRepo create failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }

    public function createClientRepo(): bool
    {
        if($this->repoValidation() == false)
            return false;

        if($this->ftpConnectionTest() == false)
            return false;

        $status = "off";
        if((!empty($this->getPost("status"))) && ($this->getPost("status") == "on"))
            $status = "on";
        $ftp_repo = new FtpRepo();
        $ftp_repo->assign(array(
            "id" => $this->env->createUuid(),
            'client_id' => $this->getPost("client_id"),
            'ftp_server' => $this->getPost("ftp_server"),
            'ftp_user' =>  $this->getPost("ftp_user"),
            'ftp_folder' =>  $this->getPost("ftp_folder"),
            'ftp_pass' => $this->crypto->bencode($this->getPost("ftp_pass")),
            'status' => $status,
        ));

        if (!$ftp_repo->create()) {
            foreach ($ftp_repo->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Repo.php",
                "Error" => "FtpRepo create failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }


    public function editRepo($id): bool
    {
        if($this->repoValidation(true) == false)
            return false;

        if($this->ftpConnectionTest() == false)
            return false;

        $status = "off";
        if((!empty($this->getPost("status"))) && ($this->getPost("status") == "on"))
            $status = "on";
        $domain_name = $this->getPost("domain_name");
        $user = Users::findFirst("username = '$domain_name'");
        $ftp_repo = FtpRepo::findFirst("client_id = '$user->id' AND id = '$id'");
        if(!$ftp_repo) {
            $this->error_messages['content'] = __('Ftp Repo was not found');
            return false;
        }

        $assign = array(
            'ftp_server' => $this->getPost("ftp_server"),
            'ftp_user' =>  $this->getPost("ftp_user"),
            'ftp_folder' =>  $this->getPost("ftp_folder"),
            'status' => $status,
        );

        if($this->getPost("ftp_pass") != null)
            $assign['ftp_pass'] = $this->crypto->bencode($this->getPost("ftp_pass"));

        $ftp_repo->assign($assign);

        if (!$ftp_repo->update()) {
            foreach ($ftp_repo->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Repo.php",
                "Error" => "FtpRepo update failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }

    public function editServerRepo($id): bool
    {
        if($this->repoValidation(true) == false)
            return false;

        if($this->ftpConnectionTest() == false)
            return false;

        $status = "off";
        if((!empty($this->getPost("status"))) && ($this->getPost("status") == "on"))
            $status = "on";

        $ftp_repo = ServerFtpRepo::findFirst("id = '$id'");
        if(!$ftp_repo) {
            $this->error_messages['content'] = __('Ftp Repo was not found');
            return false;
        }

        $assign = array(
            'ftp_server' => $this->getPost("ftp_server"),
            'ftp_user' =>  $this->getPost("ftp_user"),
            'ftp_folder' =>  $this->getPost("ftp_folder"),
            'status' => $status,
        );

        if($this->getPost("ftp_pass") != null)
            $assign['ftp_pass'] = $this->crypto->bencode($this->getPost("ftp_pass"));

        $ftp_repo->assign($assign);

        if (!$ftp_repo->update()) {
            foreach ($ftp_repo->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Repo.php",
                "Error" => "FtpRepo update failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }

    public function editClientRepo($id): bool
    {
        if($this->repoValidation(true) == false)
            return false;

        if($this->ftpConnectionTest() == false)
            return false;

        $status = "off";
        if((!empty($this->getPost("status"))) && ($this->getPost("status") == "on"))
            $status = "on";
        $client_id = $this->getPost("client_id");
        $ftp_repo = FtpRepo::findFirst("client_id = '$client_id' AND id = '$id'");
        if(!$ftp_repo) {
            $this->error_messages['content'] = __('Ftp Repo was not found');
            return false;
        }

        $assign = array(
            'ftp_server' => $this->getPost("ftp_server"),
            'ftp_user' =>  $this->getPost("ftp_user"),
            'ftp_folder' =>  $this->getPost("ftp_folder"),
            'status' => $status,
        );

        if($this->getPost("ftp_pass") != null)
            $assign['ftp_pass'] = $this->crypto->bencode($this->getPost("ftp_pass"));

        $ftp_repo->assign($assign);

        if (!$ftp_repo->update()) {
            foreach ($ftp_repo->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Repo.php",
                "Error" => "FtpRepo update failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }


    public function deleteRepo($domain_name, $id): bool
    {
        $user = Users::findFirst("username = '$domain_name'");
        $ftp_repo = FtpRepo::findFirst("client_id = '$user->id' AND id = '$id'");
        if(!$ftp_repo) {
            $this->error_messages['content'] = __('Ftp Repo was not found');
            return false;
        }

        if(!$ftp_repo->delete()) {
            foreach($ftp_repo->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    public function deleteServerRepo($id): bool
    {
        $ftp_repo = ServerFtpRepo::findFirstById($id);
        if(!$ftp_repo) {
            $this->error_messages['content'] = __('Ftp Repo was not found');
            return false;
        }
        if(!$ftp_repo->delete()) {
            foreach($ftp_repo->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    public function deleteClientRepo($client_id, $id): bool
    {
        $ftp_repo = FtpRepo::findFirst("client_id = '$client_id' AND id = '$id'");
        if(!$ftp_repo) {
            $this->error_messages['content'] = __('Ftp Repo was not found');
            return false;
        }
        if(!$ftp_repo->delete()) {
            foreach($ftp_repo->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }
}