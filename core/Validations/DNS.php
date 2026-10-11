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
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;

trait DNS
{
    public function CheckRecord($type, $record, $field, $message = null): Validation
    {
        try {
            if (!$this->error_status) {
                switch($type) {
                    case "CNAME":
                    case "NS":
                        $status = preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \. )*  #Subdomains
   							[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?            #Domain
   							\. [a-z]{2,6} $                               #Top-level domain
							/ix', $record);
                        break;
                    case "A":
                        $status = true;
                        if($record != null)
                            $status = preg_match('/^[a-z0-9A-Z-.]{1,120}$/', $record) && preg_match('/\w/', $record);
                        break;
                    default:
                        return $this;
                }

                if(!$status) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Please enter a valid record');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }
            }
        }
        catch (Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function CheckRecordTemplate($type, $record, $field, $message = null): Validation
    {
        try {
            if (!$this->error_status) {
                switch($type) {
                    case "NS":
                        $status = preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \. )*  #Subdomains
   							[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?            #Domain
   							\. [a-z]{2,6} $                               #Top-level domain
							/ix', $record);

                        if(!$status) {
                            $status_rec = strpos($record, '[domain]');
                            if ($status_rec === false) {
                                $status = false;
                            } else {
                                $status = true;
                            }
                        }
                        break;
                    case "A":
                        $status = true;
                        if($record != null)
                            $status = preg_match('/^[a-z0-9A-Z-.]{1,120}$/', $record) && preg_match('/\w/', $record);
                        break;
                    case "CNAME":
                        $status = preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \. )*  #Subdomains
   							[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?            #Domain
   							\. [a-z]{2,6} $                               #Top-level domain
							/ix', $record);
                        break;
                    default:
                        return $this;
                }

                if(!$status) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Please enter a valid record');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }
            }
        }
        catch (Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function CheckDnsUnique($records = array(), $domain_id  = null, $update = false): Validation
    {
        try {
            $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
            if($dns_zone) {
                $dns_zone_id = $dns_zone->id;
                $status = false;
                $field = $records['field'];
                $type = $records['type'];
                $value = $records['value'];
                $host = $records['host'];
                $old_value = $records['old_value'];
                $old_host = $records['old_host'];
                $record = false;
                if(!$update) {
                    if(($type == "CNAME")) {
                        $record = DnsRecords::findFirst("type = 'CNAME' AND type = 'A' AND dns_zone_id = '$dns_zone_id' OR host = '$host'");
                    }
                    else if(($type == "A")) {
                        $record = DnsRecords::findFirst("type = 'CNAME' AND dns_zone_id = '$dns_zone_id' AND host = '$host'");
                    }
                    else if(($type == "MX") || ($type == "PTR") || ($type == "TXT") || ($type == "NS")) {
                        $record = DnsRecords::findFirst("type = '$type' AND value = '$value' AND dns_zone_id = '$dns_zone_id'");
                    }
                }
                else {
                    if(($type == "CNAME")) {
                        if($old_host != $host) {
                            $record = DnsRecords::findFirst("type = 'CNAME' AND type = 'A' AND dns_zone_id = '$dns_zone_id' OR host = '$host'");
                        }
                    }
                    else if(($type == "A")) {
                        if($old_host != $host) {
                            $record = DnsRecords::findFirst("type = 'A' AND dns_zone_id = '$dns_zone_id' AND host = '$host'");
                        }
                    }
                    else if(($type == "MX") || ($type == "PTR") || ($type == "TXT") || ($type == "NS")) {
                        if($old_value != $value) {
                            $record = DnsRecords::findFirst("type = '$type' AND value = '$value' AND dns_zone_id = '$dns_zone_id'");
                        }
                    }

                }
                if($record) {
                    $status = true;
                }

                if($status) {
                    $this->error_status = true;
                    $this->message[$field] = __('This record is allready exist');
                }
            }
            else {
                $this->error_status = true;
                $this->message["content"] = __('DNS Zone not found');
            }
        }
        catch (Exception $e) {
            $this->message["exception"] = $e->getMessage();
        }
        return $this;
    }

    public function TXTSyntax($record, $field, $message = null): Validation
    {
        $status = preg_match('/^[a-z0-9A-Z_:\/\\\.=?~-\s]{'.'2' .',255}$/', "$record");
        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid record');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }
}
