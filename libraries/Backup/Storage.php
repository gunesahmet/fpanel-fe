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

use Exception;
use FPanel\Environments\Datatable;
use FPanel\Models\CloudStorage;
use FPanel\Process\BackupRestore;

trait Storage
{
    public function listServerStorages(): array
    {
        $datatable = new Datatable();
        $delete_button = $datatable->buttons('serverbackup/storage/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        $edit_button = $datatable->buttons('serverbackup/storage/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');

        $datatable->select(array('cloud_storage.id','cloud_storage.name','cloud_storage.type','cloud_storage.status'))
            ->from("cloud_storage")
            ->addColumn('actions', $edit_button.$delete_button,'cloud_storage.id');

        $datatable->editColumn('cloud_storage.name','<a href="'. 'serverbackup/storage/edit/$1">$2</a>', 'cloud_storage.id, cloud_storage.name');


        $data = $datatable->unsetColumns('cloud_storage.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = $value[1];
            switch($value[2]) {
                case "dropbox":
                    $data['aaData'][$key][1] = "Dropbox";
                    break;
                case "gdrive":
                    $data['aaData'][$key][1] = "Google Drive";
                    break;
                default:
                    $data['aaData'][$key][1] = __('Unknown');
                    break;
            }

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

    public function listClientStorages($client_id, $nested = false): array
    {
        $datatable = new Datatable();
        if($nested == true) {
            $delete_button = $datatable->buttons('backupclient/storage/delete/$1/client/'.$client_id,'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backupclient/storage/edit/$1/client/'.$client_id,'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('backupclient/storage/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backupclient/storage/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array(
            'cloud_storage.id',
            'cloud_storage.name',
            'cloud_storage.type',
            'cloud_storage.status'
        ))
            ->from("cloud_storage")
            ->where("cloud_storage.client_id = '$client_id'")
            ->addColumn('actions', $edit_button.$delete_button,'cloud_storage.id');

        if($nested == true) {
            $datatable->editColumn('cloud_storage.name','<a href="'. "backupclient/storage/edit/$1/client/$client_id\">$2</a>", 'cloud_storage.id, cloud_storage.name');
        }
        else {
            $datatable->editColumn('cloud_storage.name','<a href="'. 'backupclient/storage/edit/$1">$2</a>', 'cloud_storage.id, cloud_storage.name');
        }

        $data = $datatable->unsetColumns('cloud_storage.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = $value[1];
            switch($value[2]) {
                case "dropbox":
                    $data['aaData'][$key][1] = "Dropbox";
                    break;
                case "gdrive":
                    $data['aaData'][$key][1] = "Google Drive";
                    break;
                default:
                    $data['aaData'][$key][1] = __('Unknown');
                    break;
            }

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

    public function listDomainStorages($client_id, $domain_id, $nested = false): array
    {
        $datatable = new Datatable();
        if($nested == true) {
            $delete_button = $datatable->buttons('backup/storage/delete/$1/domain/'.$domain_id,'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backup/storage/edit/$1/domain/'.$domain_id,'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('backup/storage/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('backup/storage/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array('cloud_storage.id','cloud_storage.name','cloud_storage.type','cloud_storage.status'))
            ->from("cloud_storage")
            ->where("cloud_storage.client_id = '$client_id'")
            ->addColumn('actions', $edit_button.$delete_button,'cloud_storage.id');

        if($nested == true) {
            $datatable->editColumn('cloud_storage.name','<a href="'. "backup/storage/edit/$1/domain/$domain_id\">$2</a>", 'cloud_storage.id, cloud_storage.name');
        }
        else {
            $datatable->editColumn('cloud_storage.name','<a href="'. 'backup/storage/edit/$1">$2</a>', 'cloud_storage.id, cloud_storage.name');
        }

        $data = $datatable->unsetColumns('cloud_storage.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = $value[1];
            switch($value[2]) {
                case "dropbox":
                    $data['aaData'][$key][1] = "Dropbox";
                    break;
                case "gdrive":
                    $data['aaData'][$key][1] = "Google Drive";
                    break;
                default:
                    $data['aaData'][$key][1] = __('Unknown');
                    break;
            }

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

    public function createStorage($client_id): bool
    {
        if($this->storageValidation() == false)
            return false;

        if($this->storageConnectionTest() == false)
            return false;
        $storage = new CloudStorage();
        $params = array(
            'folder' => '/'.$this->getPost("folder"),
        );

        if($this->getPost("type") == "gdrive") {
            if($this->has("json_data") && $this->getPost("json_data") != null) {
                $json_data = json_decode($this->getPost("json_data"), true);
                if(json_last_error() != JSON_ERROR_NONE) {
                    $this->error_messages["json_data"] = __("Incorrect json data please enter other fields or paste correct data");
                    return false;
                }
                if(!isset($json_data["project_id"]) || !isset($json_data["private_key"]) || !isset($json_data["private_key_id"]) || !isset($json_data["client_email"])
                    || !isset($json_data["client_id"]) || !isset($json_data["client_cert_url"]) || !isset($json_data["client_cert_url"])) {
                    $this->error_messages["json_data"] = __("Incorrect json data please enter other fields or paste correct data");
                    return false;
                }
                if(empty($json_data["project_id"]) || empty($json_data["private_key"]) || empty($json_data["private_key_id"]) || empty($json_data["client_email"])
                    || empty($json_data["client_id"]) || empty($json_data["client_cert_url"])) {
                    $this->error_messages["json_data"] = __("Incorrect json data please enter other fields or paste correct data");
                    return false;
                }
                $params["project_id"] =  $json_data["project_id"];
                $params["private_key"] =  $json_data["private_key"];
                $params["private_key_id"] =  $json_data["private_key_id"];
                $params["client_email"] =  $json_data["client_email"];
                $params["client_id"] =  $json_data["client_id"];
                $params["client_cert_url"] =  $json_data["client_cert_url"];
            }
            else {
                $params["project_id"] =  $this->getPost("project_id");
                $params["private_key"] =  $this->getPost("private_key");
                $params["private_key_id"] =  $this->getPost("private_key_id");
                $params["client_email"] =  $this->getPost("client_email");
                $params["client_id"] =  $this->getPost("client_id");
                $params["client_cert_url"] =  $this->getPost("client_cert_url");
            }
        }
        else if($this->getPost("type") == "dropbox") {
            $params["access_token"] = $this->getPost("access_token");
        }
        $storage->assign(array(
            "id" => $this->env->createUuid(),
            'name' => $this->getPost("name"),
            'client_id' => $client_id,
            'type' => $this->getPost("type"),
            'status' => $this->getPost("status"),
            'params' => $this->crypto->staticEncode(json_encode($params))
        ));

        if (!$storage->create()) {
            foreach ($storage->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Storage.php",
                "Error" => "CloudStorage create failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }

    public function editStorage($id, $client_id): bool
    {
        if($this->storageValidation(true) == false)
            return false;

        if($this->storageConnectionTest() == false)
            return false;
        $storage = CloudStorage::findFirst("id = '$id' AND client_id = '$client_id'");
        if($storage) {
            $params = array(
                'access_token' => $this->getPost("access_token"),
                "project_id" => $this->getPost("project_id"),
                "private_key" => $this->getPost("private_key"),
                "private_key_id" => $this->getPost("private_key_id"),
                "client_email" => $this->getPost("client_email"),
                "client_id" => $this->getPost("client_id"),
                "client_cert_url" => $this->getPost("client_cert_url"),
                'folder' => $this->getPost("folder"),
            );
            $storage->assign(array(
                'name' => $this->getPost("name"),
                'status' => $this->getPost("status"),
                'params' => $this->crypto->staticEncode(json_encode($params))
            ));

            if (!$storage->update()) {
                foreach ($storage->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "Storage.php",
                    "Error" => "CloudStorage update failed",
                    "Data" => $this->error_messages
                ]);
                return false;
            }
            return true;

        }
        else {
            $this->error_messages['content'] = __('Storage was not found');
            return false;
        }

    }

    public function deleteStorage($id, $client_id): bool
    {
        $storage = CloudStorage::findFirst("id = '$id' AND client_id = '$client_id'");
        if(!$storage) {
            $this->error_messages['content'] = __('Storage was not found');
            return false;
        }
        if(!$storage->delete()) {
            foreach($storage->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    public function deleteAllStorage($client_id): bool
    {
        $storage = CloudStorage::find("client_id = '$client_id'");
        if(!$storage) {
            $this->error_messages['content'] = __('Storage was not found');
            return false;
        }
        if(!$storage->delete()) {
            foreach($storage->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }
        return true;
    }

    public function repoValidation($update = false): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("ftp_user"),
                'field' => 'ftp_user',
                'message' => __('FTP User is required'),
            ),
            array(
                'item' => $this->getPost("ftp_server"),
                'field' => 'ftp_server',
                'message' => __('FTP Server Address is required'),
            ),
            array(
                'item' => $this->getPost("ftp_folder"),
                'field' => 'ftp_folder',
                'message' => __('FTP Folder is required'),
            ),
        );

        if($update == false) {
            $required[] = array(
                'item' => $this->getPost("ftp_pass"),
                'field' => 'ftp_pass',
                'message' => __('Password is required'),
            );
        }
        $this->validation->Required($required);
        $this->validation->IpOrDomain($this->getPost("ftp_server"),'ftp_server')
            ->Username($this->getPost("ftp_user"),'ftp_user')
            ->Directory($this->getPost("ftp_folder"),'ftp_folder');

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;

    }

    public function ftpConnectionTest(): bool
    {
        $con = @ftp_connect($this->getPost("ftp_server"),21,10);
        if(!$con) {
            $this->error_messages['content'] = __('Connection failed');
            return false;
        }
        if (@ftp_login($con, $this->getPost("ftp_user"), $this->getPost("ftp_pass"))) {
            $this->error_messages['content'] = __('Connection success');
            return true;
        }
        else {
            $this->error_messages['content'] = __('Wrong username/password');
            return false;
        }
    }

    public function storageValidation($update = false): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("name"),
                'field' => 'storage_name',
                'message' => __('Storage Name is required'),
            ),
        );

        if($update == false) {
            $required[] =             array(
                'item' => $this->getPost("type"),
                'field' => 'storages',
                'message' => __('Storage type is required'),
            );
        }

        switch($this->getPost("type")) {
            case "dropbox":
                $required[] = array(
                    'item' => $this->getPost("access_token"),
                    'field' => 'access_token',
                    'message' => __('Application token is required'),
                );
                break;
            case "gdrive":
                $required[] = array(
                    'item' => $this->getPost("gdrive_client_id"),
                    'field' => 'gdrive_client_id',
                    'message' => __('Google client id is required'),
                );
                $required[] = array(
                    'item' => $this->getPost("gdrive_secret_key"),
                    'field' => 'gdrive_secret_key',
                    'message' => __('Google Secret key is required'),
                );
                break;
        }
        $this->validation->Required($required);


        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    public function storageConnectionTest(): bool
    {
        if($this->getPost("type") == "dropbox") {
            return $this->dropboxTest();
        }
        else if($this->getPost("type") == "gdrive") {
            return $this->gdriveTest();
        }
        return false;
    }

    private function dropboxTest(): bool
    {
        $temp = tempnam("tmp", "FOO");

        $file = fopen($temp, "w");
        fwrite($file, "Temporary file create by Fpanel");
        fclose($file);

        $access_token = $this->getPost("access_token");
//        $application_secret = $this->getPost("application_secret");
        $project = 'Fpanel Dropbox Connector';
        $project_folder = '/'.$this->getPost("folder");

        try {
            $bk = new Dropbox($access_token, $project, $project_folder);
            $bk->upload($temp);
            if(file_exists($temp))
                $this->env->unlink($temp);
        }
        catch(Exception $e) {
            if(strpos($e->getMessage(),"Invalid OAuth2")) {
                $this->error_messages['content'] = __('Invalid token key');
            }
            if(strpos($e->getMessage(),"SSL certificate")) {
                $this->error_messages['content'] = __('SSL certificate problem');
            }
            if(file_exists($temp))
                $this->env->unlink($temp);

            return false;
        }

//        $bk->deleteFile($temp);
        return true;
    }

    private function gdriveTest(): bool
    {
        $backupmng = new BackupRestore();
        $result = $backupmng->gDriveTest([
            "server-addr" => $this->request->getServerAddress(),
            "project-id" => $this->getPost("project_id"),
            "private-key" => $this->getPost("private_key"),
            "private-key-id" => $this->getPost("private_key_id"),
            "client-email" => $this->getPost("client_email"),
            "client-id" => $this->getPost("client_id"),
            "client-cert-url" => $this->getPost("client_cert_url"),
            "dest-path" => $this->getPost("folder"),
        ]);

        if(strpos($result, "FolderID:") === false) {
            return false;
        }
        return true;
    }
}