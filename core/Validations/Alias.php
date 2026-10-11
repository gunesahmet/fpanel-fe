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

use Exception;
use FPanel\Models\DomainAlias;
use FPanel\Models\Domains;

trait Alias
{
    public function UniqueAlias($alias_name, $field, $message = null, $update = false, $old_alias = null ) :Validation
    {
        try {
            if(!$this->error_status) {
                if(!$update) {
                    $domain = Domains::findFirst("name = '$alias_name'");
                    if($domain) {
                        $this->error_status = true;
                        if($message == null) {
                            $this->message[$field] = __('Alias name must be unique');
                        }
                        else {
                            $this->message[$field] = $message;
                        }
                    }

                    $alias = DomainAlias::findFirst("alias_name = '$alias_name'");
                    if($alias) {
                        $this->error_status = true;
                        if($message == null) {
                            $this->message[$field] = __('Alias name must be unique');
                        }
                        else {
                            $this->message[$field] = $message;
                        }
                    }
                }
                else {
                    if($alias_name != $old_alias) {
                        $domain = Domains::findFirst("name = '$alias_name'");
                        if($domain) {
                            $this->error_status = true;
                            if($message == null) {
                                $this->message[$field] = __('Alias name must be unique');
                            }
                            else {
                                $this->message[$field] = $message;
                            }
                        }

                        $alias = DomainAlias::findFirst("alias_name = '$alias_name'");
                        if($alias) {
                            $this->error_status = true;
                            if($message == null) {
                                $this->message[$field] = __('Alias name must be unique');
                            }
                            else {
                                $this->message[$field] = $message;
                            }
                        }
                    }
                }

            }
        }
        catch (Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }
}
