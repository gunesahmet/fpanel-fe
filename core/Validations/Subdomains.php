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
use FPanel\Models\Domains;

trait Subdomains
{
    public function UniqueSubDomain($subdomain, $domain_id, $field, $message = null): Validation
    {
        try {
            if(!$this->error_status) {
                $subdomains = \FPanel\Models\Subdomains::findFirst("domain_id = '$domain_id' AND subdomain_name = '$subdomain'");
                if($subdomains) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Subdomain name must be unique');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }

                $domains = Domains::findFirstById($domain_id);
                $dns_records = DnsTemplate::findFirst("type = 'A' AND host='$subdomain.$domains->name'");
                if($dns_records) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Allready have a dns record for this subdomain. Please remove dns record and try again create');
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
