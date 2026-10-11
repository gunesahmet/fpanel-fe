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
namespace SystemMessages;

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
        $messages = new Group(array(
            'module'     => 'SystemMessages',
            'controller' => 'messages'
        ));

        $messages->setPrefix('/messages');

        $messages->add('', array(
            'action' => 'index',
        ))->setName('System Messages');

        $messages->add('/create', array(
            'action' => 'create',
        ))->setName('Create System Message');

        $messages->add('/edit/{id}', array(
            'action' => 'edit',
        ))->setName('Edit System Message');

        $messages->add('/delete/{id}', array(
            'action' => 'delete',
        ))->setName('Delete System Message');

        $router->mount($messages);

        return $router;

    }

}