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
namespace Server;

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
        $server = new Group(array(
            'module'     => 'Server',
            'controller' => 'server'
        ));

//        $server->setPrefix('');

        $route_list = array(
            //array('link','method','name')
            array('/server','index','Server List'),
            array('/server/dashboard/server/{server_id}','dashboard','Server List'),
            array('/server/settings','settings','Server Settings'),
            array('/server/logsettings','logsettings','Server Log Settings'),
            array('/server/timesettings','timesettings','Server Time Settings'),
            array('/server/optimize','optimize','Server Management'),
            array('/server/reboot','reboot','Server Management'),
            array('/server/showlogs','showlogs','View Server Logs'),
            array('/server/resource','resource','View Server Resources'),

            // Cloud Parts
            array('/server/create','create','Add Server'),
            array('/server/installation','installation','Add Server'),
            array('/server/save','save','Add Server'),
            array('/server/edit/{id}','edit','Edit Server'),
            array('/server/delete/{id}','delete','Delete Server'),
        );





        foreach ($route_list as $url) {
            $server->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $server->add($url['0'].'/{type}/{server_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($server);


        return $router;

    }

}