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
namespace Update;

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
        $system_time = new Group(array(
            'module'     => 'Update',
            'controller' => 'update'
        ));

        $system_time->setPrefix('/update');

        $route_list = array(
            //array('link','method','name')
            array('','index','Update Settings'),
            array('/settings','settings','Update Settings'),
            array('/close','close','Update Settings'),
            array('/force','force','Update Settings'),
            array('/ignore','ignore','Update Settings'),
        );

        foreach ($route_list as $url) {
            $system_time->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $system_time->add($url['0'].'/{type}/{server_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($system_time);

        return $router;

    }

}