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

trait Serviceplans
{
    public function UniqueServiceplan($name, $user_id, $field, $message = null, $update = false, $old_name = null): Validation
    {
        try {
            if(!$this->error_status) {
                if(!$update) {
                    $sp = \FPanel\Models\Serviceplans::findFirst("name = '$name' AND client_id = '$user_id'");
                    if($sp) {
                        $this->error_status = true;
                        if($message == null) {
                            $this->message[$field] = __('This serviceplan already exists');
                        }
                        else {
                            $this->message[$field] = $message;
                        }
                    }
                }
                else {
                    if($name != $old_name) {
                        $sp = \FPanel\Models\Serviceplans::findFirst("name = '$name' AND client_id = '$user_id'");
                        if($sp) {
                            $this->error_status = true;
                            if($message == null) {
                                $this->message[$field] = __('This serviceplan already exists');
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

    public function UniqueDefault($user_id, $field, $message = null): Validation
    {
        try {
            if(!$this->error_status) {
                $sp = \FPanel\Models\Serviceplans::findFirst("is_default = '1' AND client_id = '$user_id'");
                if($sp) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('You cannot select more than one default.');
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

    public function ServicePlanName($name, $field, $message = null): Validation
    {
        if(!$this->error_status) {
            if(strpos("'", $name) > 0 OR strpos('"', $name) > 0 OR strpos('`', $name) > 0){
                $this->error_status = true;
                if($message == null) {
                    $this->message[$field] = __('Please dont use single or double quote');
                }
                else {
                    $this->message[$field] = $message;
                }
            }
        }
        return $this;
    }
}
