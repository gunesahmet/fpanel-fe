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
namespace Cli;

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

        $cli = new Group(array(
            'module'     => 'Cli',
            'controller' => 'cli'
        ));

        $cli->setPrefix('/cli');


        $route_list = array(
            //array('link','method','name')
            array('','index','cli'),
        );

        foreach ($route_list as $url) {
            $cli->add($url['0'], array(
                'action' => $url['1'],
            ))->via(
                [
                    'POST',
                    'GET',
                ]
            )->setName($url['2']);

        }

        $router->mount($cli);

        return $router;

    }

}