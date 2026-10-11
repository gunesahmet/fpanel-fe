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

trait Blacklist
{
    public function CheckBlacklist($element, $field, $type, $like = false, $message = null):Validation
    {
        try {
            $conditions = "type = ?1 AND rule LIKE ?2";
            if(!$like) {
                $parameters = array(1 => $type,2 => $element);
            }
            else {
                $parameters = array(1 => $type,2 => $element.'%');
            }

            $blacklist = \FPanel\Models\Blacklist::findFirst([
                $conditions,
                "bind" => $parameters
            ]);

            if($blacklist) {
                $this->error_status = true;
                if($message == null) {
                    $this->message[$field] = __("This rule can not use. Please enter different rule");
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