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
namespace Packages;

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
        $packages = new Group(array(
            'module'     => 'Packages',
            'controller' => 'packages'
        ));

        $packages->setPrefix('/packages');

        $route_list = array(
            //array('link','method','name')
            array('','index','System Packages'),
            array('/install/{component_id}/{confirm}','install','System Packages Management'),
            array('/remove/{component_id}','remove','System Packages Management'),
            array('/updatesourcetree','updatesourcetree','System Packages Management'),
            array('/sourcelists','sourcelists','System Packages Management'),
            array('/createsourcelist','createsourcelist','System Packages Management'),
            array('/updatesourcelist','updatesourcelist','System Packages Management'),
            array('/removesourcelist','removesourcelist','System Packages Management'),
            array('/initsourcelist','initsourcelist','System Packages Management'),
        );


        foreach ($route_list as $url) {
            $packages->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $packages->add($url['0'].'/{type}/{server_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($packages);


        return $router;

    }

}