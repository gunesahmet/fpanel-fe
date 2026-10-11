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
use FPanel\Models\ProtectDirectory;
use FPanel\Models\ProtectDirectoryUser;
use FPanel\Models\Subdomains;

/**
 * Class Protectdir
 * @package FPanel\Protectdir
 */
class Protectdir extends Library
{
    public $domain_name;
    public $subdomain_name = null;
    protected $pro_dir_data = array();
    protected $pro_dir_user_data = array();
    protected $old_pro_dir;

    public function saveProtectdir(bool $restore = false)
    {
        $this->db->begin();
        try {
            if(!$restore) {
                if(!$this->saveDirValidation()) {
                    return false;
                }
            }

            $protect_directory = new ProtectDirectory();
            $domain_id = $this->getPost("domain_id");

            if($this->getPost("subdomain_id") == "0") {
                $subdomain_id = "0";
            }
            else {
                $subdomain_id = $this->getPost("subdomain_id");
                $subdomain = Subdomains::findFirstById($subdomain_id);
                if($subdomain) {
                    $subdomain_data = $subdomain->toArray();
                    $this->subdomain_name = $subdomain_data['subdomain_name'];
                }
            }

            $protect_directory->assign(array(
                "id"            => $this->env->createUuid(),
                'domain_id'     => $domain_id,
                'path'          => $this->getPost("path"),
                'header'        => $this->getPost("header"),
                'cgi_bin'       => $this->getPost("cgi_bin"),
                'subdomain_id'  => $subdomain_id,
            ));
            if(!$protect_directory->create()) {
                foreach ($protect_directory->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Protectdir->saveProtectdir",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
            else {
                $this->createProDirCurl();
                if(!empty($protect_directory->id)) {
                    $this->db->commit();
                    return $protect_directory->id;
                }
            }
            $this->db->rollback();
            return false;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Protectdir->saveProtectdir",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            $this->db->rollback();
            return false;
        }
    }

    public function saveProtectDirUser(): bool
    {
        try {
            $validation = $this->saveDirUserValidation();
            if(!$validation) {
                return false;
            }

            $password = $this->crypto->bencode($this->getPost("password"));

            $protect_dir_user = new ProtectDirectoryUser();

            $protect_dir_user->assign(array(
                "id"              => $this->env->createUuid(),
                'username'        => $this->getPost("username"),
                'password'        => $password,
                'protect_dir_id'  => $this->getPost("protect_dir_id"),
            ));

            if(!$protect_dir_user->create()) {
                foreach ($protect_dir_user->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Protectdir->saveProtectDirUser",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $pro_dir_id = $this->getPost("protect_dir_id");
                $protect_dir = ProtectDirectory::findFirstById($pro_dir_id);
                if($protect_dir) {
                    $this->pro_dir_data = $protect_dir->toArray();
                    if($this->pro_dir_data['subdomain_id'] != null) {
                        $subdomain_id = $this->pro_dir_data['subdomain_id'];
                        $subdomain = Subdomains::findFirstById($subdomain_id);
                        if($subdomain) {
                            $subdomain_data = $subdomain->toArray();
                            $this->subdomain_name = $subdomain_data['subdomain_name'];
                        }
                    }
                    $this->createProDirUserCurl();
                    return true;
                }
                else {
                    return false;
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Protectdir->saveProtectDirUser",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function deleteProtectDir(): bool
    {
        try {
            $protect_dir_id = $this->getPost("dir_id");
            $domain_id = $this->getPost("domain_id");
            $protect_dir = ProtectDirectory::findFirst("id = '$protect_dir_id' AND domain_id = '$domain_id'");
            if(!$protect_dir) {
                $this->error_messages["content"] = __("Protect directory not found");
                return false;
            }

            $protect_dir_users = ProtectDirectoryUser::find("protect_dir_id = '$protect_dir_id' and protect_dir_id = '$protect_dir_id'");
            if(!$protect_dir_users) {
                $this->error_messages["content"] = __("Protect directory not found");
                return false;
            }

            $data = array(
                'subdomain_name' => '',
                'username' => '',
                'path' => $protect_dir->path,
                'domain_id' => $protect_dir->domain_id
            );
            if($protect_dir->subdomain_id != "0") {
                $subdomain = Subdomains::findFirstById($protect_dir->subdomain_id);
                if($subdomain) {
                    $data['subdomain_name'] = $subdomain->subdomain_name;
                }
            }

            foreach($protect_dir_users as $pro_user) {
                $data['username'] = $pro_user->username;
                $this->removeProDirUserCurl($data);
            }

            if(!$protect_dir_users->delete()) {
                foreach($protect_dir_users->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Protectdir->deleteProtectDir ProtectDirectoryUser",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }

            if(!$protect_dir->delete()) {
                foreach($protect_dir->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Protectdir->deleteProtectDir ProtectDirectory",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
            }
            $this->removeProDirCurl($data);
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Protectdir->deleteProtectDir",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function editProtectDir(): bool
    {
        $this->db->begin();
        try {
            if(!$this->updateDirValidation()) {
                return false;
            }

            $protect_dir = ProtectDirectory::findFirstById($this->getPost("dir_id"));
            if(!$protect_dir) {
                $this->error_messages["content"] = __("Directory not found");
                return false;
            }

            if($this->getPost("path") != null) {
                $protect_dir_data = $protect_dir->toArray();
                $subdomain_id = $protect_dir_data['subdomain_id'];
                if($subdomain_id != null) {
                    $subdomain = Subdomains::findFirstById($subdomain_id);
                    if($subdomain) {
                        $subdomain_data = $subdomain->toArray();
                        $this->subdomain_name = $subdomain_data['subdomain_name'];
                    }
                }
                $this->old_pro_dir = $protect_dir_data['path'];
                $protect_dir->path = $this->getPost("path");
                if(!$protect_dir->update()) {
                    foreach ($protect_dir->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Protectdir->editProtectDir ProtectDirectory",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    $this->db->rollback();
                    return false;
                }
            }
            else {
                $this->db->rollback();
                return false;
            }
            if($this->getPost("header") != null) {
                $protect_dir->header = $this->getPost("header");
                if(!$protect_dir->update()) {
                    foreach ($protect_dir->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Protectdir->editProtectDir ProtectDirectory2",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    $this->db->rollback();
                    return false;
                }
            }
            $this->updateProDirCurl();
            $this->db->commit();
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Protectdir->editProtectDir",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            $this->db->rollback();
            return false;
        }
    }

    public function editUser(): bool
    {
        try {
            $validation = $this->updateDirUserValidation();
            if(!$validation) {
                return false;
            }
            $protect_dir_user = ProtectDirectoryUser::findFirstById($this->getPost("user_id"));
            if(!$protect_dir_user) {
                $this->error_messages["content"] = __("User not found");
                return false;
            }
            $pro_dir_data = $this->getPost("pro_dir_data");

            $subdomain_id = $pro_dir_data['subdomain_id'];
            if($subdomain_id != null) {
                $subdomain = Subdomains::findFirstById($subdomain_id);
                if($subdomain) {
                    $subdomain_data = $subdomain->toArray();
                    $this->subdomain_name = $subdomain_data['subdomain_name'];
                }
            }

            if($this->getPost("username") != null) {
                $protect_dir_user->username = $this->getPost("username");
                if(!$protect_dir_user->update()) {
                    foreach ($protect_dir_user->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Protectdir->editUser ProtectDirectoryUser",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }

            if($this->getPost("password") != null) {
                $password = $this->crypto->bencode($this->getPost("password"));

                $protect_dir_user->password = $password;
                if(!$protect_dir_user->update()) {
                    foreach ($protect_dir_user->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Protectdir->editUser ProtectDirectoryUser2",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
            }

            $this->updateProDirUserCurl();
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Protectdir->editUser",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function deleteUser(): bool
    {
        try {
            $user_id = $this->getPost("user_id");
            $dir_id = $this->getPost("dir_id");
            $domain_id = $this->getPost("domain_id");

            $pro_user = ProtectDirectoryUser::findFirstById($user_id);
            if(!$pro_user) {
                $this->error_messages["content"] = __("User not found");
                return false;
            }
            $pro_user_data = $pro_user->toArray();
            $this->pro_dir_user_data = $pro_user_data;
            if($pro_user_data['protect_dir_id'] == $dir_id) {
                $pro_dir = ProtectDirectory::findFirst("id = '$dir_id' AND domain_id = '$domain_id'");
                if(!$pro_dir) {
                    return false;
                }
                if($pro_dir->subdomain_id != "0") {
                    $subdomain = Subdomains::findFirstById($pro_dir->subdomain_id);
                    $subdomain_name = $subdomain->subdomain_name;
                }
                else {
                    $subdomain_name = "";
                }

                $pro_dir_data = $pro_dir->toArray();
                $this->pro_dir_data = $pro_dir_data;
                if($pro_dir_data['domain_id'] == $domain_id) {

                    if(!$pro_user->delete()) {
                        foreach($pro_user->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        $this->log->error([
                            "Module" => "Protectdir->deleteUser ProtectDirectoryUser2",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                    }
                    $this->removeProDirUserCurl(array(
                        'path' => $pro_dir->path,
                        'username' => $pro_user_data['username'],
                        'subdomain_name' => $subdomain_name,
                        'domain_id' => $domain_id,
                    ));
                    return true;
                }
            }
            return false;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Protectdir->deleteUser",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function listProtectDir($domain_id = null, $nested = false): array
    {
        $datatable = new Datatable();
        if($nested) {
            $delete_button = $datatable->buttons("protectdir/delete/$1/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("protectdir/edit/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
            $user_button = $datatable->buttons("protectdir/protectuser/$1/domain/$domain_id",'normal',array('i' => 'fa fa-user','a' => 'text-primary'),__('Users'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('protectdir/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('protectdir/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
            $user_button = $datatable->buttons("protectdir/protectuser/$1",'normal',array('i' => 'fa fa-user','a' => 'text-primary'),__('Users'),null,'data-id="nested"');
        }

        $datatable->select(array('protect_directory.id','protect_directory.path','protect_directory.header','domains.name','subdomains.full_name'))
            ->from("domains")
            ->innerJoin('protect_directory','domains.id = protect_directory.domain_id')
            ->leftJoin('subdomains','protect_directory.subdomain_id = subdomains.id')
            ->where("domains.id = '$domain_id'")
            ->searchColumn(array('protect_directory.path','domains.name','subdomains.full_name'))
            ->addColumn('actions', $edit_button.$delete_button.$user_button,'protect_directory.id');

        if($nested) {
            $datatable->editColumn('protect_directory.path','<a href="'. "protectdir/edit/$1/domain/$domain_id\">$2</a>", 'protect_directory.id, protect_directory.path');
        }
        else {
            $datatable->editColumn('protect_directory.path','<a href="'. 'protectdir/edit/$1">$2</a>', 'protect_directory.id, protect_directory.path');
        }

        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = '<input type="checkbox" name="pro_dirs[]" class="checkbox-blue" value="'. $value[0] .'">';

            if ($value[4] == null){
                $data['aaData'][$key][3] = $value[3];
            }
            else {
                $data['aaData'][$key][3] = $data['aaData'][$key][4];
            }
            $data['aaData'][$key][4] = $value[5];
            unset($data['aaData'][$key][5]);
        }
        return $data;
    }

    public function listDirUsers($dir_id, $domain_id = null, $nested = false): array
    {
        $datatable = new Datatable();
        if($nested) {
            $delete_button = $datatable->buttons("protectdir/deleteuser/$1/dir/$dir_id/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("protectdir/edituser/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons("protectdir/deleteuser/$1/dir/$dir_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("protectdir/edituser/$1",'normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array('protect_directory_users.id as pro_user_id','protect_directory_users.username', 'protect_directory_users.id', 'protect_directory.id'))
            ->from("domains")
            ->innerJoin('protect_directory','domains.id = protect_directory.domain_id')
            ->innerJoin('protect_directory_users','protect_directory.id = protect_directory_users.protect_dir_id')
            ->where("domains.id = '$domain_id'")
            ->andWhere("protect_directory.id = '$dir_id'")
            ->searchColumn(array('protect_directory_users.username'))
            ->addColumn('actions', $edit_button.$delete_button,'protect_directory_users.id');

        if($nested) {
            $datatable->editColumn('protect_directory_users.username','<a href="'. "protectdir/edituser/$1/domain/$domain_id\">$2</a>", 'protect_directory_users.id, protect_directory_users.username');
        }
        else {
            $datatable->editColumn('protect_directory_users.username','<a href="'. 'protectdir/edituser/$1">$2</a>', 'protect_directory_users.id, protect_directory_users.username');
        }
        $datatable->editColumn('pro_user_id','<input type="checkbox" class="checkbox-blue" value="$1" name="prousers[]">', 'pro_user_id');

        $data = $datatable->unsetColumns('protect_directory.id')->unsetColumns('protect_directory_users.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value) {
            $data['aaData'][$key][2] = $value[4];
            unset($data['aaData'][$key][3]);
            unset($data['aaData'][$key][4]);
        }
        return $data;
    }

    public function createProDirCurl()
    {
        $pro_dir = new \FPanel\Process\ProtectDirectory();
        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $pro_dir->setServerId($domain->web_server_id);

        $params = array(
            'domain' => $this->domain_name,
            'prefix' => $domain->prefix,
            'text' => $this->getPost("header"),
            'dir' => $this->getPost("path"),
        );
        if($this->subdomain_name != null) {
            $params['subdomain'] = $this->subdomain_name;
        }
        $pro_dir->createDirectory($params);
    }

    public function createProDirUserCurl()
    {
        $pro_dir = new \FPanel\Process\ProtectDirectory();
        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $pro_dir->setServerId($domain->web_server_id);

        $params = array(
            'domain' => $this->domain_name,
            'prefix' => $domain->prefix,
            'user' => $this->getPost("username"),
            'passwd' => $this->getPost("password"),
            'dir' => $this->getPost("path"),
        );

        if($this->subdomain_name != null) {
            $params['subdomain'] = $this->subdomain_name;
        }
        $pro_dir->createUser($params);
    }

    public function updateProDirCurl()
    {
        $pro_dir = new \FPanel\Process\ProtectDirectory();
        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $pro_dir->setServerId($domain->web_server_id);

        $params = array(
            'domain' => $this->getPost("domain_name"),
            'prefix' => $domain->prefix,
            'text' => $this->getPost("header"),
            'dir' => $this->getPost("path"),
            'old-dir' => $this->old_pro_dir,
        );

        if($this->subdomain_name != null) {
            $params['subdomain'] = $this->subdomain_name;
        }
        $pro_dir->updateDirectory($params);
    }

    public function updateProDirUserCurl()
    {
        $pro_dir = new \FPanel\Process\ProtectDirectory();
        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $pro_dir->setServerId($domain->web_server_id);

        $params = array(
            'domain' => $this->domain_name,
            'prefix' => $domain->prefix,
            'dir' => $this->getPost("pro_dir_data")['path'],
            'user' => $this->getPost("username"),
            'passwd' => $this->getPost("password"),
        );
        if($this->subdomain_name != null) {
            $params['subdomain'] = $this->subdomain_name;
        }
        $pro_dir->updateUser($params);
    }

    public function removeProDirCurl($data)
    {
        $pro_dir = new \FPanel\Process\ProtectDirectory();
        $domain = Domains::findFirst("name = '$this->domain_name'");
        $pro_dir->setServerId($domain->web_server_id);
        $params = array(
            'domain' => $this->domain_name,
            'prefix' => $domain->prefix,
            'dir' => $data['path'],
        );
        $params['subdomain'] = $data['subdomain_name'];
        $pro_dir->removeDirectory($params);
    }

    public function removeProDirUserCurl($data): bool
    {
        $pro_dir = new \FPanel\Process\ProtectDirectory();
        $domain_id = $data['domain_id'];
        $domain = Domains::findFirstById($domain_id);
        if(!$domain) {
            $this->error_messages["dir_id"] = __("Protect directory not found");
            return false;
        }
        $pro_dir->setServerId($domain->web_server_id);

        $params = array(
            'domain' => $domain->name,
            'prefix' => $domain->prefix,
            'dir' => $data['path'],
            'user' => $data['username'],
        );
        $params['subdomain'] = $data['subdomain_name'];
        $pro_dir->removeUser($params);
        return true;
    }

    private function saveDirValidation(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("path"),
                'field' => 'directory_path',
                'message' => __('Directory Path is required'),
            ),
            array(
                'item' => $this->getPost("subdomain_id"),
                'field' => 'domain_name',
                'message' => __('Please select domain'),
            ),
        );

        $this->validation->Required($required);

        if($this->getPost("subdomain_id") != "0") {
            $sub_list = array();
            foreach($this->getPost("subdomain_list") as $domains) {
                $sub_list[] = $domains['id'];
            }
            $this->validation->InclusionIn($this->getPost("subdomain_id"),'domain_name', $sub_list,__('Please select valid domain or subdomain'));
        }

        $status = $this->validation->Directory($this->getPost("path"),'directory_path',__('Please enter valid directory name'))
            ->UniquePath($this->getPost("path"), $this->getPost("domain_id"), $this->getPost("subdomain_id"),'directory_path')
            ->Validate();
        if(!$status) {
            $this->error_messages =  $this->validation->getMessage();
            return false;
        }
        else {
            return true;
        }
    }

    private function saveDirUserValidation(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("username"),
                'field' => 'username',
                'message' => __('Username is required'),
            ),
            array(
                'item' => $this->getPost("password"),
                'field' => 'password',
                'message' => __('Password is required'),
            ),
        );

        $status = $this->validation->Required($required)
            ->Username($this->getPost("username"),'username')
            ->Password($this->getPost("password"),'password')
            ->UniqueProUsername($this->getPost("username"), $this->getPost("protect_dir_id"),'username')
            ->Validate();


        if(!$status) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        else {
            return true;
        }
    }

    private function updateDirValidation(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("path"),
                'field' => 'directory_path',
                'message' => __('Directory Path is required'),
            ),
        );

        $this->validation->Required($required);

        $status = $this->validation->Directory($this->getPost("path"),'directory_path',__('Please enter valid directory name'))
            ->UniquePath($this->getPost("path"), $this->getPost("domain_id"), $this->getPost("subdomain_id"),'directory_path',null,true, $this->getPost("old_path"))
            ->Validate();
        if(!$status) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        else {
            return true;
        }
    }

    private function updateDirUserValidation(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("username"),
                'field' => 'username',
                'message' => __('Username is required'),
            ),
            array(
                'item' => $this->getPost("password"),
                'field' => 'password',
                'message' => __('Password is required'),
            ),
        );

        $status = $this->validation->Required($required)
            ->Username($this->getPost("username"),'username')
            ->Password($this->getPost("password"),'password')
            ->UniqueProUsername($this->getPost("username"), $this->getPost("protect_dir_id"),'username',null,true, $this->getPost("old_username"))
            ->Validate();


        if(!$status) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        else {
            return true;
        }
    }
}