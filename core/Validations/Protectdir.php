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
 */namespace FPanel\Validations;

use FPanel\Models\ProtectDirectory;
use FPanel\Models\ProtectDirectoryUser;

trait Protectdir
{
    public function UniqueProUsername($username, $pro_id, $field, $message = null, $udpate = false, $old_username = null): Validation
    {
        try {
            $users = false;
            if(!$this->error_status) {
                if(!$udpate) {
                    $users = ProtectDirectoryUser::findFirst("username = '$username' AND protect_dir_id = '$pro_id'");
                }
                else {
                    if($username != $old_username) {
                        $users = ProtectDirectoryUser::findFirst("username = '$username' AND protect_dir_id = '$pro_id'");
                    }
                }
                if($users) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Username must be unique');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function UniquePath($path, $domain_id, $subdomain_id, $field, $message = null, $update = false, $old_dirname = null): Validation
    {
        try {
            if(!$this->error_status) {
                if(!$update) {
                    if($subdomain_id == null) {
                        $pro_dir = ProtectDirectory::findFirst("path = '$path' AND domain_id = '$domain_id'");
                    }
                    else {
                        $pro_dir = ProtectDirectory::findFirst("path = '$path' AND domain_id = '$domain_id' AND subdomain_id = '$subdomain_id'");
                    }
                    if($pro_dir) {
                        $this->error_status = true;
                        if($message == null) {
                            $this->message[$field] = __('Path must be unique');
                        }
                        else {
                            $this->message[$field] = $message;
                        }
                    }
                }
                else {
                    if($old_dirname != $path) {
                        $pro_dir = ProtectDirectory::findFirst("path = '$path' AND domain_id = '$domain_id'");
                        if($pro_dir) {
                            $this->error_status = true;
                            if($message == null) {
                                $this->message[$field] = __('Path must be unique');
                            }
                            else {
                                $this->message[$field] = $message;
                            }
                        }
                    }
                }
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }
}
