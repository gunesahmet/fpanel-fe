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
 */namespace Api\Controller;

use Exception;

class DomainController extends ApiController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->checkAuth();
        $this->fapi->setRequestData($this->request_data)
            ->setUserId($this->api_auth->getUserId())
            ->setUserType($this->api_auth->getUsername());
    }


    public function indexAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain",
            "action" => "list",
        ])
            ->execute();

       $this->response($response);
    }

    public function createAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain",
            "action" => "create",
        ])
            ->execute();

        $this->response($response);
    }

    public function updateAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain",
            "action" => "update",
        ])
            ->execute();

        $this->response($response);
    }

    public function deleteAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain",
            "action" => "delete",
        ])
            ->execute();
        $this->response($response);
    }

    public function webhostingAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain",
            "action" => "webhosting",
        ])
            ->execute();
        $this->response($response);
    }

    public function resourcesAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain",
            "action" => "resource",
        ])
            ->execute();
        $this->response($response);
    }
}