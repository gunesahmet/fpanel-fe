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

use FPanel\Models\MailAlias;
use FPanel\Models\Mails;

trait Mail
{
    public function UniqueMailAddress($email_address, $domain_id, $field, $message = null, $update = false, $old_email = null): Validation
    {
        try {
            $def_message = "";
            if(!$this->error_status) {
                $status = false;
                if(!$update) {
                    $domain_mails = Mails::find("domain_id = '$domain_id'");
                    $mail = Mails::findFirst("mail_name = '$email_address' AND domain_id = '$domain_id'");
                    if($mail) {
                        $status = true;
                        $def_message = __('Email address already used by another account');
                    }

                    if($domain_mails) {
                        foreach($domain_mails as $mail_addr) {
                            $mail_id = $mail_addr->id;
                            $mail_alias = MailAlias::findFirst("alias_name = '$email_address' AND mail_id = '$mail_id'");
                            if($mail_alias) {
                                $status = true;
                                $def_message = __('Email address already used by another account as Alias');
                            }
                        }
                    }
                }
                else {
                    if($email_address != $old_email) {
                        $domain_mails = Mails::find("domain_id = '$domain_id'");
                        $mail = Mails::findFirst("mail_name = '$email_address' AND domain_id = '$domain_id'");
                        if($mail) {
                            $status = true;
                            $def_message = __('Email address already used by another account');
                        }

                        if($domain_mails) {
                            foreach($domain_mails as $mail_addr) {
                                $mail_id = $mail_addr->id;
                                $mail_alias = MailAlias::findFirst("alias_name = '$email_address' AND mail_id = '$mail_id'");
                                if($mail_alias) {
                                    $status = true;
                                    $def_message = __('Email address already used by another account as Alias');
                                }
                            }
                        }
                    }
                }

                if($status) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = $def_message;
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
}
