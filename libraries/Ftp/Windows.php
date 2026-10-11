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
 */namespace FPanel\Ftp;

use FPanel\Models\Domains;
use FPanel\Process\Permission;

trait Windows
{
    public function getWindowsFilePermission($file_name, $domain_name): array
    {
        $file_name = str_replace('/','\\', $file_name);
        $domains = Domains::findFirst("name = '$domain_name'");
        $params = array(
            "domain" => $domain_name,
            "file" => $file_name,
            "prefix" => $domains->prefix
        );
        $permissionmng = new Permission();
        $permissionmng->setServerId($domains->web_server_id);
        $perms = $permissionmng->getPermission($params);

        $perms = trim($perms);

        $user_perms = array();

        $array = preg_split("/((\r?\n)|(\r\n?))/", $perms);

        foreach($array as $line){
            $line = trim($line);
            if($line != null) {
                $line_arr = explode(':', $line);
                $user_perms[] = array(
                    'user' => $line_arr[0],
                    'perm' => $line_arr[1]
                );
            }
        }

        $user_permissions = array();
        for ($i = 0; $i < count($user_perms); $i++) {
            $alloweds = array();
            $denieds = array();
            $disabled = false;

            if(strpos($user_perms[$i]['user'],'Administrator') !== false) {
                $disabled = true;
            }
            if(strpos($user_perms[$i]['user'],'Administrators') !== false) {
                $disabled = true;
            }
            if(strpos($user_perms[$i]['user'],'SYSTEM') !== false) {
                $disabled = true;
            }
            if(strpos($user_perms[$i]['user'],'Users') !== false) {
                $disabled = true;
            }

            if($this->checkUser($user_permissions, $user_perms[$i]['user']) == true) {
                $alloweds = $user_permissions[$user_perms[$i]['user']]['allowed'];
                $denieds = $user_permissions[$user_perms[$i]['user']]['denied'];

            }
            $allowed_permission = "";
            $denied_permission  = "";
            if(strpos($user_perms[$i]['perm'],'Allow') !== false) {
                $allowed_permission = str_replace('Allow','', $user_perms[$i]['perm']);
            }
            else  {
                $denied_permission = str_replace('Deny','', $user_perms[$i]['perm']);
            }
            foreach(explode(',',trim($allowed_permission)) as $perms ) {
                if(trim($perms) != null)
                    $alloweds[] = trim($perms);
            }
            foreach(explode(',',trim($denied_permission)) as $perms ) {
                if(trim($perms) != null)
                    $denieds[] = trim($perms);
            }
            $user_permissions[$user_perms[$i]['user']] = array(
                'name' => $user_perms[$i]['user'],
                'allowed' => $alloweds,
                'denied' => $denieds,
                'disabled' => $disabled
            );
        }
        $list = array();
        foreach($user_permissions as $user) {
            $list[] = $user;
        }

        return $list;
    }

    private function checkUser($user_list, $user_name): bool
    {
        foreach($user_list as $user => $values) {
            if($user == $user_name) {
                return true;
            }
        }
        return false;
    }

    public function setWindowsFilePermission($file_name, $domain_name, $permissions): bool
    {
        $file_name = str_replace('/','\\\\', $file_name);
        $domains = Domains::findFirst("name = '$domain_name'");
        $params = array(
            "domain" => $domain_name,
            "file" => $file_name,
            "prefix" => $domains->prefix
        );
        $permissionmng = new Permission();
        $permissionmng->setServerId($domains->web_server_id);
        $perms = $permissionmng->getPermission($params);

        $perms = trim($perms);

        $array = preg_split("/((\r?\n)|(\r\n?))/", $perms);

        $status = false;

        foreach($permissions as $perm) {
            $perm_user = explode('\\', $perm['name']);
            $username = end($perm_user);

            $status = $this->setPermission($file_name, $domain_name, $perm['permission'], $username, $array, $domains);
        }
        return $status;
    }

    private function setPermission($file_name, $domain_name, $permissions, $username, $array, Domains $domains): bool
    {
        $blocked_users = ['Administrator','Administrators','SYSTEM','Users']; // IUSR
        foreach($blocked_users as $blocked) {
            if(strtolower($username) == strtolower($blocked))
                return true;
        }

        $params = array(
            'user' => $username,
            "domain" => $domain_name,
            "file" => $file_name,
            "prefix" => $domains->prefix
        );
        $status = false;


        foreach($array as $line){
            $line = trim($line);
            if($line != null) {
                $line_arr = explode(':', $line);
                if(strpos($line_arr[0], $username) !== false) {
                    $status = true;
                }
            }
        }

        if(!$status) {
            $this->error_messages['content'] = __('User not found');
            return false;
        }


        if(array_key_exists('FullControl', $permissions)) {
            $params['permission'] = 'F';
            if($permissions['FullControl'] == "deny") {
                $params['deny'] = "on";
                $params['allow'] = "off";
            }
            else{
                $params['allow'] = "on";
                $params['deny'] = "off";
            }
            $this->setPermissionCurl($params, $domains->web_server_id);
            return true;
        }
        else {
            if(array_key_exists('Modify', $permissions)) {
                $params['permission'] = 'M';
                if($permissions['Modify'] == "deny") {
                    $params['deny'] = "on";
                    $params['allow'] = "off";
                }
                else{
                    $params['allow'] = "on";
                    $params['deny'] = "off";
                }
                $this->setPermissionCurl($params, $domains->web_server_id);
                return true;
            }
            else {
                if(array_key_exists('ReadAndExecute', $permissions)) {
                    if($permissions['ReadAndExecute'] == "deny") {
                        if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "deny") {
                            $params['permission'] = 'RXW';
                            $params['deny'] = "on";
                            $params['allow'] = "off";
                        }
                        else {
                            $params['permission'] = 'RX';
                            $params['deny'] = "on";
                            $params['allow'] = "off";
                            $this->setPermissionCurl($params, $domains->web_server_id);
                            if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "allow") {
                                $params['permission'] = 'W';
                                $params['deny'] = "off";
                                $params['allow'] = "on";
                            }
                        }
                    }
                    else {
                        if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "allow") {
                            $params['permission'] = 'RXW';
                            $params['deny'] = "off";
                            $params['allow'] = "on";
                        }
                        else {
                            $params['permission'] = 'RX';
                            $params['deny'] = "off";
                            $params['allow'] = "on";
                            $this->setPermissionCurl($params, $domains->web_server_id);
                            if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "deny") {

                                $params['permission'] = 'W';
                                $params['deny'] = "on";
                                $params['allow'] = "off";
                            }
                        }
                    }
                    $this->setPermissionCurl($params, $domains->web_server_id);
                    return true;


                }
                else {
                    if(array_key_exists('Read', $permissions)) {
                        if($permissions['Read'] == "deny") {
                            if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "deny") {
                                $params['permission'] = 'RW';
                            }
                            else {
                                if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "allow") {
                                    $params['permission'] = 'W';
                                    $params['deny'] = "off";
                                    $params['allow'] = "on";
                                    $this->setPermissionCurl($params, $domains->web_server_id);
                                }
                                $params['permission'] = 'R';
                            }
                            $params['deny'] = "on";
                            $params['allow'] = "off";
                            $this->setPermissionCurl($params, $domains->web_server_id);
                            return true;
                        }
                        else if($permissions['Read'] == "allow") {
                            if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "allow") {
                                $params['permission'] = 'RW';
                                $params['deny'] = "off";
                                $params['allow'] = "on";
                            }
                            else {
                                $params['permission'] = 'R';
                                $params['deny'] = "off";
                                $params['allow'] = "on";
                                $this->setPermissionCurl($params, $domains->web_server_id);
                                if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "deny") {

                                    $params['permission'] = 'W';
                                    $params['deny'] = "on";
                                    $params['allow'] = "off";
                                }
                            }
                            $this->setPermissionCurl($params, $domains->web_server_id);
                            return true;
                        }
                    }
                    else {
                        if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "allow") {
                            $params['permission'] = 'W';
                            $params['deny'] = "off";
                            $params['allow'] = "on";
                            $this->setPermissionCurl($params, $domains->web_server_id);
                            return true;
                        }
                        else {
                            if((array_key_exists('Write', $permissions)) && $permissions['Write'] == "deny") {
                                $params['permission'] = 'W';
                                $params['deny'] = "on";
                                $params['allow'] = "off";
                                $this->setPermissionCurl($params, $domains->web_server_id);
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function setPermissionCurl($params, $web_server_id)
    {
        $permissionmng = new Permission();
        $permissionmng->setServerId($web_server_id);
        return $permissionmng->setPermission($params);
    }
}