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
 */namespace FPanel\Menu;

use FPanel\Library\Library;

/**
 * Class Dashboard
 * @package FPanel\Dashboard
 */
class Dashboard extends Library
{
    public $domain_data;
    public $client_data;

    public function create($user_data, $client_type = null, $client_id = null, $nested = null): array
    {
        if($this->fcookies->has("template"))
            $template = $this->fcookies->get("template");
        else
            $template = "default";

        if($template == "desktop")
            $builder = new Builder();
        else
            $builder = new BuilderDefault();

        $builder->domain_data = $this->domain_data;
        $builder->client_data = $this->client_data;

        return $builder->build($user_data, $client_type, $client_id, $nested);
    }
}

