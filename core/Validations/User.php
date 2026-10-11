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

use FPanel\Models\Users;

trait User
{
    public function UniqueUser($username, $field, $message = null, $update = false, $old_username = null):Validation
    {
        try {
            if(!$this->error_status) {
                if(!$update) {
                    $user = Users::findFirst("username = '$username'");
                    if($user) {
                        $this->error_status = true;
                        if($message == null) {
                            $this->message[$field] = __('Username must be unique');
                        }
                        else {
                            $this->message[$field] = $message;
                        }
                    }
                }
                else {
                    if(trim($old_username) != trim($username)) {
                        $user = Users::findFirst("username = '$username'");
                        if($user) {
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
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }

        return $this;
    }
}