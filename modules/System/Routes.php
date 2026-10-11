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
 */
namespace System;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group;

class Routes
{
    /**
     * @param Router $router
     * @return Router
     */
    public function add(Router $router): Router
    {
        $system = new Group(array(
            'module'     => 'System',
            'controller' => 'system'
        ));

        $system->setPrefix('/system');

        $system->add('', array(
            'action' => 'index',
        ))->setName('System Dashboard');

        $system->add('/notify', array(
            'action' => 'notify',
        ))->setName('System Notify');

        $system->add('/advanced-settings', array(
            'action' => 'advancedsettings',
        ))->setName('System Advanced Settings');

        $system->add('/passwordsettings', array(
            'action' => 'passwordsettings',
        ))->setName('System Password Settings');

        $system->add('/certificate', array(
            'action' => 'certificate',
        ))->setName('System Certificate Settings');

        $system->add('/recaptchasettings', array(
            'action' => 'recaptchasettings',
        ))->setName('System Captcha Settings');

        $system->add('/addnewadmin', array(
            'action' => 'addnewadmin',
        ))->setName('Create System User');

        $router->mount($system);

        return $router;

    }

}