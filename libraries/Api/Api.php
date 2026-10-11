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
 */namespace FPanel\Api;

use Phalcon\Mvc\User\Component;

class Api extends Component
{
    private $request_data;

    private $account_type;
    private $account_id;

    private $module;

    public $error = array();

    public function setRequestData($request_data): Api
    {
        $this->request_data = $request_data;
        return $this;
    }

    public function setUserId($user_id): Api
    {
        $this->account_id = $user_id;
        return $this;
    }

    public function setUserType($type): Api
    {
        $this->account_type = $type;
        return $this;
    }

    public function setModule($module): Api
    {
        $this->module = $module;
        return $this;
    }

    public function execute()
    {
        $api = new Builder($this->account_id, $this->account_type, $this->request_data);
        return $api->builder($this->module);
    }
}