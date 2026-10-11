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

use FPanel\Models\DnsTemplate;

trait DnsServer
{
    public function CheckDnsUniqueServer(array $records, string $server_id, $update = false): Validation
    {
        if($this->error_status) {
            return $this;
        }
        try {
            $status = false;


            $field = $records['field'];
            $type = $records['type'];
            $value = $records['value'];
            $host = $records['host'];
            $old_value = $records['old_value'];
            $old_host = $records['old_host'];


            $record = false;
            if(!$update) {
                if(($type == "A") || ($type == "CNAME")) {
                    $record = DnsTemplate::findFirst("type = '$type' AND host = '$host' AND server_id = '$server_id'");
                }
                else if(($type == "MX") || ($type == "PTR") || ($type == "TXT") || ($type == "NS")) {
                    $record = DnsTemplate::findFirst("type = '$type' AND value = '$value' AND server_id = '$server_id'");
                }
            }
            else {
                if(($type == "A") || ($type == "CNAME")) {
                    if($old_host != $host)
                        $record = DnsTemplate::findFirst("type = '$type' AND host = '$host' AND server_id = '$server_id'");
                }
                else if(($type == "MX") || ($type == "PTR") || ($type == "TXT") || ($type == "NS")) {
                    if($old_value != $value)
                        $record = DnsTemplate::findFirst("type = '$type' AND value = '$value' AND server_id = '$server_id'");
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
        catch (\Exception $e) {
            $this->error_status = true;
            $this->message[$field] = $e->getMessage();
        }

        return $this;
    }
}
