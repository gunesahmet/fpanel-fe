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

use FPanel\Models\DnsRecords;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\Subdomains;
use FPanel\Models\Users;

trait Domain
{
    public function UniqueDomain($domain_name, $field, $message = null): Validation
    {
        try {
            if(!$this->error_status) {
                $domain = Domains::findFirst("name = '$domain_name'");
                if($domain) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Domain name must be unique');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                    return $this;
                }


                $dns_records = DnsRecords::findFirst("value = '$domain_name' OR host = '$domain_name'");

                if($dns_records) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Domain name allready opened as DNS Record. Please contact with system administrator');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                    return $this;
                }

                $subdomains = Subdomains::findFirst("full_name = '$domain_name'");
                if($subdomains) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Domain name allready opened as Subdomain. Please contact with system administrator');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                    return $this;
                }
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function UniqueFtpUser($ftp_user, $field, $message = null, $update = false, $old_ftp_user = null): Validation
    {
        try {
            if(!$this->error_status) {
                if(!$update) {
                    $ftpuser = FtpUsers::findFirst("username = '$ftp_user'");
                    if($ftpuser) {
                        $this->error_status = true;
                        if($message == null) {
                            $this->message[$field] = __('Ftpuser name must be unique');
                        }
                        else {
                            $this->message[$field] = $message;
                        }
                    }
                }
                else {
                    if($old_ftp_user != $ftp_user) {
                        $ftpuser = FtpUsers::findFirst("username = '$ftp_user'");
                        if($ftpuser) {
                            $this->error_status = true;
                            if($message == null) {
                                $this->message[$field] = __('Ftpuser name must be unique');
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

    public function UniqueDomainUser($domain, $field, $message = null): Validation
    {
        try {
            if(!$this->error_status) {
                $user = Users::findFirst("username = '$domain'");
                if($user) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Domain name must be unique');
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
