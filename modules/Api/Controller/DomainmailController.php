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

class DomainmailController extends ApiController
{
    /**
     * init
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->checkAuth();
        $this->fapi->setRequestData($this->request_data)
            ->setUserId($this->api_auth->getUserId())
            ->setUserType($this->api_auth->getUsername());
    }


    /**
     * @throws Exception
     */
    public function indexAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain_mail",
            "action" => "list",
        ])
            ->execute();
       $this->response($response);
    }

    /**
     * @throws Exception
     */
    public function createAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain_mail",
            "action" => "create",
        ])
            ->execute();
        $this->response($response);
    }

    /**
     * @throws Exception
     */
    public function updateAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain_mail",
            "action" => "update",
        ])
            ->execute();
        $this->response($response);
    }

    /**
     * @throws Exception
     */
    public function deleteAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain_mail",
            "action" => "delete",
        ])
            ->execute();
        $this->response($response);
    }

    /**
     * @throws Exception
     */
    public function createAliasAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain_mail",
            "action" => "create_alias",
        ])
            ->execute();

        $this->response($response);
    }

    /**
     * @throws Exception
     */
    public function updateAliasAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain_mail",
            "action" => "update_alias",
        ])
            ->execute();

        $this->response($response);
    }

    /**
     * @throws Exception
     */
    public function deleteAliasAction(): void
    {
        $response = $this->fapi->setModule([
            "name" => "domain_mail",
            "action" => "delete_alias",
        ])
            ->execute();
        $this->response($response);
    }
}