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

trait Limits
{
    public function CheckLimit($user_type, $limits, $used_data, $field, $type, $message = null):Validation
    {
        $status = false;
        try {
            if($user_type != 0) {
                switch($type) {
                    case "domain":
                        if(!$this->compare($limits['domain_limit'], $used_data['domain'])) {
                            $status = true;
                        }
                        break;
                    case "client":
                        if(!$this->compare($limits['client_limit'], $used_data['client'])) {
                            $status = true;
                        }
                        break;
                    case "database":
                        if(!$this->compare($limits['database_limit'], $used_data['database'])) {
                            $status = true;
                        }
                        break;
                    case "subdomain":
                        if(!$this->compare($limits['subdomain_limit'], $used_data['subdomain'])) {
                            $status = true;
                        }
                        break;
                    case "email":
                        if(!$this->compare($limits['email_limit'], $used_data['email'])) {
                            $status = true;
                        }
                        break;
                    case "domain_alias":
                        if(!$this->compare($limits['domain_alias_limit'], $used_data['domain_alias'])) {
                            $status = true;
                        }
                        break;
                    case "email_forward":
                        if(!$this->compare($limits['email_forward_limit'], $used_data['email_forward'])) {
                            $status = true;
                        }
                        break;
                    case "email_group":
                        if(!$this->compare($limits['email_group_limit'], $used_data['email_group'])) {
                            $status = true;
                        }
                        break;
                    case "auto_responder":
                        if(!$this->compare($limits['auto_responder_limit'], $used_data['auto_responder'])) {
                            $status = true;
                        }
                        break;
                    case "email_list":
                        if(!$this->compare($limits['email_list_limit'], $used_data['email_list'])) {
                            $status = true;
                        }
                        break;
                }
            }

            if($status) {
                $this->error_status = true;
                if($message == null) {
                    $this->message[$field] = __('Limit is exceeded');
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

    public function CheckParentLimit($parent_id, $message = null)
    {
        //@TODO
    }

    private function compare($limit, $used): bool
    {
        if($limit > $used) {
            return false;
        }
        return true;
    }

}