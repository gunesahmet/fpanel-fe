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
use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\Models\ClientLimitsPermissions;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\Subdomains;
use FPanel\Models\Users;
use FPanel\Process\Subdirectory;
use FPanel\Process\Subuser;
use FPanel\Process\Subvhost;

/**
 * Class Subdomain
 * @package FPanel\DomainSettings
 */
class Subdomain extends Library
{

    public $permission;
    public $ftp_user_id;
    public $domain_name;
    public $ftp_username;
    private $portal_link = PORTAL_URL;

    public function subdomainList($domain_id = null, $nested = false): array
    {
        $datatable = new Datatable();
        if(($nested == true)) {
            $delete_button = $datatable->buttons("subdomain/delete/$1/domain/$domain_id","ajax",array("i" => "fa fa-times","a" => "text-danger"),__("Delete"));
            $edit_button = $datatable->buttons("subdomain/edit/$1/domain/$domain_id","normal",array("i" => "fa fa-edit","a" => "text-info"),__("Edit"),null,"data-id='nested'");
        }
        else {
            $delete_button = $datatable->buttons("subdomain/delete/$1","ajax",array("i" => "fa fa-times","a" => "text-danger"),__("Delete"));
            $edit_button = $datatable->buttons("subdomain/edit/$1","normal",array("i" => "fa fa-edit","a" => "text-info"),__("Edit"),null,"data-id='nested'");
        }

        $datatable->select(array(
            "subdomains.id",
            "subdomains.full_name",
            "subdomains.created_date",
            "subdomains.ftpuser_type",
            "ftp_users.username as ftpname",
            "subdomains.status"
        ))
            ->from("subdomains")
            ->innerJoin("domains","domains.id = subdomains.domain_id")
            ->leftJoin("ftp_users","ftp_users.id = subdomains.ftpuser_id")
            ->where("subdomains.domain_id = '$domain_id'")
            ->searchColumn(array("subdomains.full_name","ftp_users.username as ftpname"))
            ->addColumn("actions", $edit_button.$delete_button,"subdomains.id");

        if(($nested == true)) {
            $datatable->editColumn("subdomains.full_name",'<a href="'. 'subdomain/edit/$1/domain/' . $domain_id. '">$2</a>', "subdomains.id, subdomains.full_name");
        }
        else {
            $datatable->editColumn("subdomains.full_name",'<a href="'. 'subdomain/edit/$1">$2</a>', "subdomains.id, subdomains.full_name");
        }

        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data["aaData"] as $key => $value){
            $data["aaData"][$key][0] = '<input class="checkbox-blue" type="checkbox" value="'. $value[0] .'" name="subdomains[]">';
            if ($value[5] == ACTIVE) {
                $data["aaData"][$key][5] = '<span class="label label-success"> '. __("Active") ." </span>";
            }
            else{
                $data["aaData"][$key][5] = '<span class="label label-danger"> '. __("Passive") ." </span>";
            }

            if ($value[3] == MAIN){
                $data["aaData"][$key][3] = '<span class="label label-primary"> '. __("Main") ." </span>";
            }
            else{
                $data["aaData"][$key][3] = '<span class="label label-primary"> '. __("Native") ." </span>";
            }

            if (($value[2] == "0") || ($value[2] == null)){
                $data["aaData"][$key][2] = __("Unknown");
            }
            else {
                $data["aaData"][$key][2] = $this->date->getOnlyDate($value[2], "d/m/Y");
            }

        }

        return $data;
    }

    public function createSubdomain($restore = false): bool
    {
        if(!$this->validate())
            return false;
        $this->db->begin();
        try {
            $this->createFTPUser();
            $domain_id = $this->getPost("domain_id");
            $domain_parent_id = $this->getPost("domain_parent");
            $domain_parent = Users::findFirstById($domain_parent_id);
            $domain_permission = ClientLimitsPermissions::findFirst("domain_id = '$domain_id'")->toArray();
            if($domain_parent->type != ROOT_USER) {
                $domain_parent_permission = ClientLimitsPermissions::findFirst("client_id = '$domain_parent_id'")->toArray();
            }
            else {
                $domain_parent_permission = array(
                    "php"               => ACTIVE,
                    "cgi"               => ACTIVE,
                    "perl"              => ACTIVE,
                    "python"            => ACTIVE,
                    "asp"               => ACTIVE,
                    "aspnet"            => ACTIVE,
                    "aspnet_core"       => ACTIVE,
                );
            }

            $subdomain = new Subdomains();

            $data = array(
                "php",
                "cgi",
                "perl",
                "python",
                "asp",
                "aspnet",
                "aspnet_core",
            );

            foreach ($data as $key) {
                if($this->getPost($key) == ON) {
                    if($domain_parent_permission[$key] == PASSIVE) {
                        $this->setPost($key, OFF);
                    }
                    else {
                        if ($domain_permission[$key] == PASSIVE) {
                            $this->setPost($key, OFF);
                        }
                    }
                }
            }

            $ipaddress = $this->getPost("ipaddr_id");

            $sub_php_version = "";
            if($this->getPost("php") == ON) {
                $sub_php_version = $this->getPost("php_version");
            }
            $sub_aspnet_version = "";
            if($this->getPost("aspnet") == ON) {
                $sub_aspnet_version = $this->getPost("aspnet_version");
            }

            //@TODO Max CPU değeri set edilecek
            $sub_max_cpu = "0";

            // FTP User Type
            if ($this->getPost("use_native_ftp") == OFF) {
                $ftp_user_type = MAIN;
                $ftp_user_id = $this->getPost("domain_ftp_user_id");
            }
            else {
                $ftp_user_type = NATIVE;
                $ftp_user_id = $this->ftp_user_id;
            }

            $assign = array(
                "id"                        => $this->env->createUuid(),
                "domain_id"                 => $this->getPost("domain_id"),
                "subdomain_name"            => $this->getPost("subdomain_name"),
                "ipaddr_id"                 => $ipaddress,
                "full_name"                 => $this->getPost("full_name"),
                "ftpuser_type"              => $ftp_user_type,
                "ftpuser_id"                => $ftp_user_id,
                "ssl"                       => PASSIVE,
                "php_version"               => $sub_php_version,
                "fast_cgi"                  => PASSIVE,
                "status"                    => $this->getPost("status") == ON ? ACTIVE : PASSIVE,
                "php"                       => $this->getPost("php") == ON ? ACTIVE : PASSIVE,
                "cgi"                       => $this->getPost("cgi") == ON ? ACTIVE : PASSIVE,
                "perl"                      => $this->getPost("perl") == ON ? ACTIVE : PASSIVE,
                "python"                    => $this->getPost("python") == ON ? ACTIVE : PASSIVE,
                "asp"                       => $this->getPost("asp") == ON ? ACTIVE : PASSIVE,
                "aspnet_core"               => $this->getPost("aspnet_core") == ON ? ACTIVE : PASSIVE,
                "aspnet"                    => $this->getPost("aspnet") == ON ? ACTIVE : PASSIVE,
                "aspnet_version"            => $sub_aspnet_version,
                "dedicated_pool"            => PASSIVE,
                "max_cpu"                   => $sub_max_cpu,
            );
            $subdomain->assign($assign);

            if (!$subdomain->create()) {
                foreach ($subdomain->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Subdomain->createSubdomain",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                $this->db->rollback();
                return false;
            }
            else {
                if(!$restore) {
                    $dns = new NamedService();
                    $dns_post_data = array(
                        // Değişkenler
                        "update"   => false,
                        "domain_id"   => $domain_id,
                        "domain_name"   => $this->getPost("domain_name"),
                        "time_stamp"   => time(),
                        "type"   => "A",

                        // A
                        "a_host" => $this->getPost("subdomain_name"),
                        "a_value" => $this->getPost("ipaddress"),
                        "a_opt" => "",

                        "old_host" => "",
                        "old_type" => "",
                        "old_value" => "",
                    );
                    $dns->setPosts($dns_post_data);
                    $dns_record_status = $dns->createDNSRecords();
                    $dns->initCurl($domain_id);

                    if ($dns_record_status) {
                        $result = $this->sendCreateSubdomainCurl();
                        if(!$result) {
                            $this->log->error([
                                "Module" => "Subdomain->sendCreateSubdomainCurl",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                            $this->db->rollback();
                            return false;
                        }
                        else {
                            $this->db->commit();
                            return true;
                        }
                    }
                    else {
                        $this->log->error([
                            "Module" => "Subdomain",
                            "Action" => "Create",
                            "Message" => "Dns Record cant create",
                            "Result" => "Dns record create failed",
                            "Errors" => $this->getMessages(),
                        ]);
                        $this->db->rollback();
                        return false;
                    }
                }
                else {
                    $result = $this->sendCreateSubdomainCurl();
                    if(!$result) {
                        $this->log->error([
                            "Module" => "Subdomain",
                            "Action" => "Create",
                            "Message" => "Subdomain cant create only curl",
                            "Errors" => $this->getMessages(),
                        ]);
                        $this->db->rollback();
                        return false;
                    }
                    else {
                        $this->db->commit();
                        return true;
                    }
                }
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->createSubdomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            $this->db->rollback();
            return false;
        }
    }

    public function updateFTPUser($ftp_user_id): bool
    {
        $this->db->begin();
        try {
            // Not:Bu kısıma düşüyorsa sistem FTP User type daha öncede Native demek oluyor.
            $ftp_user = FtpUsers::findFirstById($ftp_user_id);
            $ftp_pass = trim($this->crypto->bdecode($ftp_user->password));
            $ftp_user_data = $ftp_user->toArray();

            $this->setPost("new_ftp_username", "");
            $this->setPost("old_ftp_username", $ftp_user_data["username"]);

            if($ftp_user_data["username"] != $this->getPost("ftpname")) {
                $ftp_user->username = $this->getPost("ftpname");
                if (!$ftp_user->update()) {
                    foreach ($ftp_user->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Subdomain->updateFTPUser",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
                $this->setPost("new_ftp_username", $this->getPost("ftpname"));
            }

            if($this->getPost("password") != null) {
                if(trim($this->crypto->bdecode($ftp_user_data["password"])) != $this->getPost("password")) {
                    $password = $this->crypto->bencode($this->getPost("password"));
                    $ftp_user->password = $password;
                    if (!$ftp_user->update()) {
                        foreach ($ftp_user->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "Subdomain->updateFTPUser2",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    $ftp_pass = $this->getPost("password");
                }
            }

            $domains_ftp_users = FtpUsers::findFirstById($this->getPost("domain_ftp_user_id"));
            $subusermng = new Subuser();
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $subusermng->setServerId($domain->web_server_id);

            $params = array(
                "sub-user" => $this->getPost("old_ftp_username"),
                "user" => $domains_ftp_users->username,
                "domain" => $this->getPost("domain_name"),
                "prefix" => $domain->prefix,
                "subdomain" => $this->getPost("subdomain_name"),
                "passwd" => $ftp_pass,
            );
            if($this->getPost("new_ftp_username") != null) {
                $params["new-sub-user"] = $this->getPost("new_ftp_username");
            }
            $subusermng->update($params);
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->createSubdomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            $this->db->rollback();
            return false;
        }
    }

    public function updateSubdomain(): bool
    {
        $this->db->begin();
        try {
            if(!$this->validate(true)) {
                return false;
            }
            $subdomain_data = $this->getPost("subdomain_data");

            $ftp_user_type = $subdomain_data["ftpuser_type"];
            $ftp_user_id = $subdomain_data["ftpuser_id"];

            if($this->getPost("use_native_ftp") == ON)
                $this->setPost("ftp-type", NATIVE);
            else
                $this->setPost("ftp-type", MAIN);

            if($ftp_user_type == MAIN) {
                if($this->getPost("use_native_ftp") == ON) {
                    $ftp_user = $this->createFTPUser();
                    if (!is_numeric($ftp_user)) {
                        $this->db->rollback();
                        return false;
                    }
                }
            }
            else {
                if($this->getPost("use_native_ftp") == ON) {
                    $ftp_user = $this->updateFTPUser($ftp_user_id);
                    if (!$ftp_user) {
                        $this->db->rollback();
                        return false;
                    }
                }
                else if($this->getPost("use_native_ftp") == ON) {
                    $ftp_user = $this->deleteFtpUser($ftp_user_id, $this->getSubdomain());
                    if (!$ftp_user) {
                        $this->db->rollback();
                        return false;
                    }
                }
            }

            $subdomain_update_status = $this->update();
            if(!$subdomain_update_status) {
                $this->db->rollback();
                return false;
            }
            if($this->sendUpdateSubdomainCurl()) {
                $this->db->commit();
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->updateSubdomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            $this->db->rollback();
            return false;
        }
    }

    public function deleteSubdomain($domain_id): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists("Users"))
                    $this->redis->del("Users");
            }
            $subdomain = $this->getSubdomain();
            if($subdomain) {
                $dns = new NamedService();
                $dns->deleteDNSRecords($subdomain->full_name, $domain_id);
                $this->removeSubdomainCurl($subdomain);
                if(!$subdomain->delete()) {
                    foreach($subdomain->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    $this->log->error([
                        "Module" => "Subdomain->delete Subdomains",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
                $this->deleteFtpUser($subdomain->ftpuser_id, $subdomain);
                return true;
            }
            else {
                $this->error_messages["subdomain"] = __("Subdomain not found");
                return false;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->updateSubdomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    private function sendCreateSubdomainCurl(): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists("Users"))
                    $this->redis->del("Users");
            }

            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $subdirmng = new Subdirectory();
            $subdirmng->setServerId($domain->web_server_id);
            $subvhostmng = new Subvhost();
            $subvhostmng->setServerId($domain->web_server_id);

            $parameters = array(
                "domain" => $this->getPost("domain_name"),
                "prefix" => $domain->prefix,
                "subdomain" => $this->getPost("subdomain_name"),
                "ipaddr" => $this->getPost("ipaddress"),
                "user" => $this->getPost("domain_ftpuser"),
                "passwd" => $this->getPost("password"),
                "sub-user" => $this->getPost("ftpname"),
            );

            if($this->getPost("use_native_ftp") == ON) {
                $parameters["ftp-type"] = NATIVE;
            }
            else {
                $parameters["ftp-type"] = MAIN;
            }

            $parameters["stats"] = "";

            // PHP ayarlari
            if((!empty($this->getPost("php"))) && ($this->getPost("php") == ON)) {
                $parameters["php"] = "on";
                $parameters["php-ver"] = $this->getPost("php_version");
            }

            // ASP Ayarlari
            if(!empty($this->getPost("asp"))) {
                if($this->getPost("asp") == ON)
                    $parameters["asp"] = "on";
            }

            // ASP.NET CORE
            if((!empty($this->getPost("aspnet_core"))) && ($this->getPost("aspnet_core") == ON)) {
                $parameters["asp-net-core"] = "on";
            }

            // ASP.NET
            if((!empty($this->getPost("aspnet"))) && ($this->getPost("aspnet") == ON)) {
                $parameters["aspnet"] = "on";
                if(!empty($this->getPost("aspnet_version"))) {
                    $parameters["aspnet-ver"] = $this->getPost("aspnet_version");
                }
            }

            if((!empty($this->getPost("cgi"))) && ($this->getPost("cgi") == ON)) {
                $parameters["cgi"] = "on";
            }
            if((!empty($this->getPost("perl"))) && ($this->getPost("perl") == ON)) {
                $parameters["perl"] = "on";
            }
            if((!empty($this->getPost("python"))) && ($this->getPost("python") == ON)) {
                $parameters["python"] = "on";
            }

            if((!empty($this->getPost("dedicated_pool"))) && ($this->getPost("dedicated_pool") == ON)) {
                $parameters["appool"] = "on";
                $parameters["limit"] = $this->getPost("max_cpu");
            }
            else {
                $parameters["appool"] = "off";
            }

            if($this->getPost("use_native_ftp") == ON) {
                $parameters["sub-user"] = $this->getPost("domain_ftpuser");
            }

            $status = $subdirmng->create($parameters);

            if(strpos($status,"Err")) {
                $result = "";
                $error_msg = str_replace("Success"," ", $status);
                $error_msg = explode(" ", $error_msg);

                foreach($error_msg as $msg) {
                    $msg = trim($msg);
                    if($msg != null) {
                        if($result == null) {
                            $result = "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                        }
                        else {
                            $result = $result . " " . "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                        }
                    }
                }
                $this->error_messages["contents"] = $result;
                return false;
            }
            else {
                $status = $subvhostmng->create($parameters);
                if(strpos($status,"Err")) {
                    $result = "";
                    $error_msg = str_replace("Success"," ", $status);
                    $error_msg = explode(" ", $error_msg);

                    foreach($error_msg as $msg) {
                        $msg = trim($msg);
                        if($msg != null) {
                            if($result == null) {
                                $result = "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                            }
                            else {
                                $result = $result . " " . "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                            }
                        }
                    }
                    $this->error_messages["contents"] = $result;
                    return false;
                }
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->sendCreateSubdomainCurl",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    private function sendUpdateSubdomainCurl(): bool
    {
        try {
            $domain = Domains::findFirstById($this->getPost("domain_id"));
            $subdirmng = new Subdirectory();
            $subdirmng->setServerId($domain->web_server_id);
            $subvhostmng = new Subvhost();
            $subvhostmng->setServerId($domain->web_server_id);

            $parameters = array(
                "domain" => $this->getPost("domain_name"),
                "prefix" => $domain->prefix,
                "subdomain" => $this->getPost("subdomain_name"),
                "ipaddr" => $this->getPost("ipaddress"),
            );

            // PHP ayarlari
            if((!empty($this->getPost("php"))) && ($this->getPost("php") == ON)) {
                $parameters["php"] = "on";
                $parameters["php-ver"] = PHP_VERSIONS[$this->getPost("php_version", PHP56)];
            }
            // ASP Ayarlari
            if(!empty($this->getPost("asp"))) {
                if($this->getPost("asp") == ON)
                    $parameters["asp"] = "on";
            }
            // ASP.NET CORE
            if((!empty($this->getPost("aspnet_core"))) && ($this->getPost("aspnet_core") == ON)) {
                $parameters["asp-net-core"] = "on";
            }
            // ASP.NET
            if((!empty($this->getPost("aspnet"))) && ($this->getPost("aspnet") == ON)) {
                $parameters["aspnet"] = "on";
                if(!empty($this->getPost("aspnet_version"))) {
                    $parameters["aspnet-ver"] = NET_VERSIONS[$this->getPost("aspnet_version")];
                }
            }
            if((!empty($this->getPost("cgi"))) && ($this->getPost("cgi") == ON)) {
                $parameters["cgi"] = "on";
            }
            if((!empty($this->getPost("perl"))) && ($this->getPost("perl") == ON)) {
                $parameters["perl"] = "on";
            }
            if((!empty($this->getPost("python"))) && ($this->getPost("python") == ON)) {
                $parameters["python"] = "on";
            }
            if((!empty($this->getPost("dedicated_pool"))) && ($this->getPost("dedicated_pool") == ON)) {
                $parameters["appool"] = "domain";
                $parameters["limit"] = $this->getPost("max_cpu");
            }

            $subdomain_id = $this->getPost("subdomain_data")["id"];

            $ftp_users = FtpUsers::findFirstById($this->getPost("domain_ftp_user_id"));
            $parameters["user"] = $ftp_users->username;

            $qbuilder = new QueryBuilder();
            $sub_data = $qbuilder->select([
                "ftp_users.username as ftpname",
                "subdomains.ftpuser_type",
            ])
                ->from("subdomains")
                ->innerJoin("ftp_users","ftp_users.id = subdomains.ftpuser_id")
                ->where([
                    "conditions" => "subdomains.id = ?1",
                    "bind" => [1 => $subdomain_id]
                ])
                ->findFirst();

            $parameters["sub-user"] = $sub_data["ftpname"];
            $parameters["ftp-type"] = FTP_USER_TYPES[$sub_data["ftpuser_type"]];

            $status = $subdirmng->update($parameters);

            if(strpos($status,"Err")) {
                $result = "";
                $error_msg = str_replace("Success"," ", $status);
                $error_msg = explode(" ", $error_msg);

                foreach($error_msg as $msg) {
                    $msg = trim($msg);
                    if($msg != null) {
                        if($result == null) {
                            $result = "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                        }
                        else {
                            $result = $result . " " . "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                        }
                    }
                }
                $this->error_messages["contents"] = $result;
                return false;
            }
            else {
                $status = $subvhostmng->update($parameters);
                if(strpos($status,"Err")) {
                    $result = "";
                    $error_msg = str_replace("Success"," ", $status);
                    $error_msg = explode(" ", $error_msg);

                    foreach($error_msg as $msg) {
                        $msg = trim($msg);
                        if($msg != null) {
                            if($result == null) {
                                $result = "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                            }
                            else {
                                $result = $result . " " . "<a href='$this->portal_link/#$msg' target='_blank'>$msg</a>";
                            }
                        }
                    }
                    $this->error_messages["contents"] = $result;
                    return false;
                }
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->updateSubdomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    private function removeSubdomainCurl(Subdomains $subdomain): void
    {
        try {
            $domain = $subdomain->getDomains();
            if(!$domain) {
                $this->error_messages["content"] = __("Domain not found");
                return;
            }

            $subvhostmng = new Subvhost();
            $subdirectory = new Subdirectory();
            $subvhostmng->setServerId($domain->web_server_id);
            $subdirectory->setServerId($domain->web_server_id);


            $subvhost_params = array(
                "subdomain" => $subdomain->subdomain_name,
                "domain" => $domain->name,
                "prefix" => $domain->prefix,
            );

            $subdir_params = array(
                "subdomain" => $subdomain->subdomain_name,
                "domain" => $domain->name,
                "prefix" => $domain->prefix,
                "ftp-type" => FTP_USER_TYPES[$subdomain->ftpuser_type],
                "sub-user" => $subdomain->getFtpUsers()->username,
            );

            $subvhostmng->remove($subvhost_params);
            $subdirectory->remove($subdir_params);
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->sendCreateSubdomainCurl",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
        }
    }

    private function validate($update = false): bool
    {

        $required = array();
        // Genel Kontroller
        $required[] = array(
            "field" => "subdomain_name",
            "item" => $this->getPost("subdomain_name"),
            "message" => __("Subdomain is required.")
        );

        // FTP Type is Native
        if ($this->getPost("use_native_ftp") == ON) {

            $required[] = array(
                "field" => "username",
                "item" => $this->getPost("ftpname"),
                "message" => __("Username is required")
            );

            if(!$update) {
                $required[] = array(
                    "field" => "password",
                    "item" => $this->getPost("password"),
                    "message" => __("Password is required")
                );
            }
        }


        $this->validation->Required($required);

        if(PANEL_OS_TYPE == WINDOWS) {
            // PHP & PHP Version
            if($this->getPost("php") == ON) {
                $required[] = array(
                    "field" => "php_version",
                    "item" => $this->getPost("php_version"),
                    "message" => __("PHP Version is required")
                );
                $this->validation->Required($required);

                if($this->getPost("php_version") != null) {
                    $this->validation->InclusionIn($this->getPost("php_version"),"php_version",array(PHP56, PHP72, PHP74));
                }

            }

            // ASP.NET & ASP.NET Version
            if($this->getPost("aspnet") == ON) {
                $required[] = array(
                    "field" => "aspnet_version",
                    "item" => $this->getPost("aspnet_version"),
                    "message" => __("ASP.NET Version is required")
                );
                $this->validation->Required($required);

                if($this->getPost("aspnet_version") != null) {
                    $this->validation->InclusionIn($this->getPost("aspnet_version"),"aspnet_version",array(v40, v20));
                }
            }

        }
        if ($this->getPost("use_native_ftp") == ON) {
            $this->validation->FtpUser($this->getPost("ftpname"),"username");
            if(!$update) {
                if($this->getPost("password") != null)
                    $this->validation->Password($this->getPost("password"),"password");
            }
        }


        if(!$update) {
            $this->validation->Domain($this->getPost("subdomain_name").".".$this->getPost("domain_name"),"subdomain_name");
            $this->validation->UniqueSubDomain($this->getPost("subdomain_name"), $this->getPost("domain_id"),"subdomain_name");
            $old_ftpname = null;
        }
        else {
            $old_ftpname = $this->getPost("old_ftpname");
        }


        if($this->getPost("use_native_ftp") == ON) {
            $this->validation->CheckBlackList($this->getPost("ftpname"),"username","ftp_user",false,__("This ftp username can\"t be use."))
                ->UniqueFtpUser($this->getPost("ftpname"),"username",null, $update, $old_ftpname);
        }


        // Validate & Return Messages
        $result = $this->validation->Validate();
        if(!$result) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    private function createFTPUser()
    {
        try {
            if ($this->getPost("use_native_ftp") == ON) {
                $ftp_user = new FtpUsers();

                $password = $this->crypto->bencode($this->getPost("password"));
                $ftp_user->assign(array(
                    "id"            => $this->env->createUuid(),
                    "username" => $this->getPost("ftpname"),
                    "password" => $password
                ));

                if (!$ftp_user->create()) {
                    foreach ($ftp_user->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Subdomain->createFTPUser",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
                else {
                    $subusermng = new Subuser();
                    $domain = Domains::findFirstById($this->getPost("domain_id"));
                    $subusermng->setServerId($domain->web_server_id);

                    $params = array(
                        "sub-user" => $this->getPost("ftpname"),
                        "prefix" => $domain->prefix,
                        "user" => $this->getPost("domain_ftpuser"),
                        "domain" => $this->getPost("domain_name"),
                        "subdomain" => $this->getPost("subdomain_name"),
                        "passwd" => $this->getPost("password"),

                    );
                    $subusermng->create($params);
                    $this->ftp_user_id = $ftp_user->id;
                    return $ftp_user->id;
                }
            }
            else {
                return $this->getPost("domain_ftp_user_id");
            }
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function update(): bool
    {
        $domain_id = $this->getPost("domain_id");
        $subdomain_data = $this->getPost("subdomain_data");
        $domain_parent_id = $subdomain_data["domain_parent"];

        $domain_permission = ClientLimitsPermissions::findFirst("domain_id = '$domain_id'")->toArray();
        $domain_parent = Users::findFirstById($domain_parent_id);
        if($domain_parent->type != ROOT_USER) {
            $domain_parent_permission = ClientLimitsPermissions::findFirst("client_id = '$domain_parent_id'")->toArray();
        }
        else {
            $domain_parent_permission = array(
                "php"               => ACTIVE,
                "cgi"               => ACTIVE,
                "perl"              => ACTIVE,
                "python"            => ACTIVE,
                "asp"               => ACTIVE,
                "aspnet"            => ACTIVE,
                "aspnet_core"       => ACTIVE,
            );
        }

        $data = array(
            "php",
            "cgi",
            "perl",
            "python",
            "asp",
            "aspnet",
            "aspnet_core",
        );

        foreach ($data as $value) {
            if($domain_parent_permission[$value] == OFF) {
                $this->setPost($value, OFF);
            }
            else {
                if ($domain_permission[$value] == OFF) {
                    $this->setPost($value, OFF);
                }
                else {
                    if ($this->getPost($value) != ON) {
                        $this->setPost($value, OFF);
                    }
                }
            }
        }

        // TODO:Kontrol edilecek domaine ait mi değil mi diye

        // PHP Version
        $sub_php_version = "";
        if($domain_parent_permission["php"] == ACTIVE) {
            if($domain_permission["php"] == ACTIVE) {
                $sub_php_version = $this->getPost("php_version", PHP56);
            }
        }

        // ASP.NET Version
        $sub_aspnet_version = v40;
        if($domain_parent_permission["aspnet"] == ACTIVE) {
            if($domain_permission["aspnet"] == ACTIVE) {
                $sub_aspnet_version = $this->getPost("aspnet_version", v40);
            }
        }

        // FTP User

        if($subdomain_data["ftpuser_type"] == MAIN) {
            $ftp_user_type = MAIN;
            $ftp_user_id = $this->getPost("domain_ftp_user_id");
            if ($this->getPost("use_native_ftp") == ON) {
                $ftp_user_type = NATIVE;
                $ftpname = $this->getPost("ftpname");
                $ftpusers = FtpUsers::findFirst("username = '$ftpname'");
                if(!$ftpusers) {
                    $this->error_messages["ftpname"] = __("Main Ftp user not found");
                    return false;
                }
                $ftp_user_id = $ftpusers->id;
            }
        }
        else {
            $ftp_user_type = NATIVE;
            $ftp_user_id = $subdomain_data["ftpuser_id"];
            if ($this->getPost("use_native_ftp") != ON) {
                $ftp_user_type = MAIN;
                $ftp_user_id = $this->getPost("domain_ftp_user_id");
            }
        }


        // Dedicated Pool
        $sub_dedicated_pool = PASSIVE;
        $sub_max_cpu = "0";
        if($this->getPost("dedicated_pool") == ON) {
            $sub_max_cpu = $this->getPost("max_cpu", 0);
        }

        $subdomain = Subdomains::findFirstById($subdomain_data["id"]);

        $assign = array(
            "subdomain_name"            => $this->getPost("subdomain_name"),
            "full_name"                 => $this->getPost("full_name"),
            "ftpuser_type"              => $ftp_user_type,
            "ftpuser_id"                => $ftp_user_id,
            "status"                    => $this->getPost("status") == ON ? ACTIVE : PASSIVE,
            "php"                       => $this->getPost("php") == ON ? ACTIVE : PASSIVE,
            "cgi"                       => $this->getPost("cgi") == ON ? ACTIVE : PASSIVE,
            "perl"                      => $this->getPost("perl") == ON ? ACTIVE : PASSIVE,
            "python"                    => $this->getPost("python") == ON ? ACTIVE : PASSIVE,
            "asp"                       => $this->getPost("asp") == ON ? ACTIVE : PASSIVE,
            "aspnet"                    => $this->getPost("aspnet") == ON ? ACTIVE : PASSIVE,
            "aspnet_core"               => $this->getPost("aspnet_core") == ON ? ACTIVE : PASSIVE,
            "php_version"               => $sub_php_version,
            "aspnet_version"            => $sub_aspnet_version,
            "dedicated_pool"            => $sub_dedicated_pool,
            "max_cpu"                   => $sub_max_cpu,
        );
        $subdomain->assign($assign);
        if (!$subdomain->update()) {
            foreach ($subdomain->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Subdomain->update Subdomains",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    private function getSubdomain()
    {
        $subdomain_id = $this->getPost("id");
        $domain_id = $this->getPost("domain_id");
        return Subdomains::findFirst("id = '$subdomain_id' AND domain_id = '$domain_id'");
    }

    private function deleteFtpUser($ftp_user_id, Subdomains $subdomain): bool
    {
        try {
            if($subdomain->ftpuser_type == NATIVE) {
                $ftpusers = FtpUsers::findFirstById($ftp_user_id);
                if($ftpusers) {
                    $this->ftp_username = $ftpusers->username;

                    $subusermng = new Subuser();
                    $subusermng->setServerId($subdomain->getDomains()->web_server_id);
                    $params = array(
                        "sub-user" => $ftpusers->username,
                    );
                    $subusermng->remove($params);
                    if(!$ftpusers->delete()) {
                        foreach($ftpusers->getMessages() as $title => $message) {
                            $this->error_messages[$title] = $message;
                        }
                        $this->log->error([
                            "Module" => "Subdomain->deleteFtpUser",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                }
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Subdomain->deleteFtpUser",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }
}