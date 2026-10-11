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

use FPanel\Models\Databases;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseUsers;


trait Database
{
    public function UniqueDatabaseName($dbname, $field, $db_type, $domain_id, $message = null): Validation
    {
        if($this->error_status) {
            return $this;
        }
        try {
            $domain = Databases::findFirst("name = '$dbname' and type = '$db_type' and domain_id = '$domain_id'");
            if($domain) {
                $this->error_status = true;
                if($message == null) {
                    $this->message[$field] =  __('Database name must be unique');
                }
                else {
                    $this->message[$field] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function UniqueDatabaseUsername($username, $field, $message = null, $update = false, $old_username = null): Validation
    {
        if($this->error_status) {
            return $this;
        }
        try {
            if(!$update) {
                $user = DatabaseUsers::findFirst("login = '$username'");
                if($user) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] =  __('Database Username must be unique');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }
            }
            else {
                if(trim($old_username) != trim($username)) {

                    $user = DatabaseUsers::findFirst("login = '$username'");
                    if($user) {
                        $this->error_status = true;
                        if($message == null) {
                            $this->message[$field] =  __('Database Username must be unique');
                        }
                        else {
                            $this->message[$field] = $message;
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

    public function CheckDatabaseServers($dbserver_id, $field, $message = null): Validation
    {
        if($this->error_status) {
            return $this;
        }
        try {
            $dbserver = DatabaseServer::findFirstById($dbserver_id);
            if(!$dbserver) {
                $this->error_status = true;
                if($message == null) {
                    $this->message[$field] =  __('Database server not found');
                }
                else {
                    $this->message[$field] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }

        return $this;
    }

}
