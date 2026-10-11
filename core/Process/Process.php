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
 */namespace FPanel\Process;

use Phalcon\Mvc\User\Component;

/**
 * Class Process
 * @package FPanel\Process
 */
abstract class Process extends Component
{
    protected $server_id = null;
    protected $cloud = false;

    public function setServerId($server_id)
    {
        if($server_id != "1")
            $this->cloud = true;
        $this->server_id = $server_id;
    }
}