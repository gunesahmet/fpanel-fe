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
 */namespace MailServer\Controller;

/**
 * Class AntivirusController
 * @package MailServer\Controller
 */
class AntivirusController extends MailserverController
{
    public function antivirusAction()
    {
        $this->view->setVar('error_content',__('Antivirus service not installed'));
        $this->response->setStatusCode(404, __('Antivirus service not installed'));
        $this->view->pick("errors/404");
    }
}