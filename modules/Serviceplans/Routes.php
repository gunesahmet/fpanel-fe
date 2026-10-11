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
namespace Serviceplans;

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
        $serviceplans = new Group(array(
            'module'     => 'Serviceplans',
            'controller' => 'serviceplans'
        ));

        $serviceplans->setPrefix('/serviceplans');

        $route_list = array(
            array('','index','Serviceplans'),
            array('/create','create','Create Serviceplans'),
            array('/edit/{id}','edit','Edit Serviceplans'),
            array('/delete/{id}','delete','Delete Serviceplans'),
            array('/get','get',''),
            array('/multiple','multiple','Delete Serviceplans'),
        );

        foreach ($route_list as $url) {

            $serviceplans->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $serviceplans->add($url['0'].'/{type}/{client_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($serviceplans);

        return $router;

    }

}