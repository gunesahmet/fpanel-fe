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
namespace Database;

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
        $database = new Group(array(
            'module'     => 'Database',
            'controller' => 'database'
        ));

        $database->setPrefix('/database');

        $route_list = array(
            //array('link','method','name')
            array('','index','Database'),
            array('/create','create','Create Database'),
            array('/multiple','multiple','Delete Database'),
            array('/remote','remote','Database Remote Connection Management'),
            array('/remote/delete/{id}','deleteremote','Delete Database Remote Connection'),
            array('/remote/add','addremote','Create Database Remote Connection'),
            array('/edit/{id}','edit','Edit Database'),
            array('/delete/{id}','delete','Delete Database'),
            array('/dbAdmin/{id}','dbAdmin','Database DBAdmin'),
        );

        foreach ($route_list as $url) {
            $database->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $database->add($url['0'].'/{type}/{domain_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        $router->mount($database);

        return $router;

    }

}