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

use FPanel\Translate\Translate;

class Validation
{
    public $message = array();
    public $error_status = false;
    public $translate;

    use General,
        Permissions,
        Domain,
        Blacklist,
        Limits,
        User,
        Database,
        DNS,
        Alias,
        DatabaseServers,
        Serviceplans,
        Subdomains,
        Protectdir,
        Mail,
        Task,
        DnsServer,
        Time,
        Network;

    public function __construct()
    {
        $this->translate = new Translate();
    }

    public function Validate(): bool
    {
        if($this->error_status) {
            return false;
        }
        else {
            return true;
        }
    }

    public function getMessage(): array
    {
        return $this->message;
    }
}


