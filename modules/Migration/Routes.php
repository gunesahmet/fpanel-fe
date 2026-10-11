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
namespace Migration;

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
        $migration = new Group(array(
            'module'     => 'Migration',
            'controller' => 'migration'
        ));


        $route_list = array(
            //array('link','method','name')
            array('','index','Migration'),
            array('/create','create','Create Migration'),
            array('/edit/{id}','edit','Edit Migration'),
            array('/delete/{id}','delete','Delete Migration'),
            array('/start/{id}','start','Start Migration'),
            array('/status/{id}','status','Migration'),
            array('/progress/{id}','progress','Migration'),
        );
        $migration->setPrefix('/migration');

        foreach ($route_list as $url) {
            $migration->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $migration->add($url['0'].'/{type}/{server_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        $router->mount($migration);

        return $router;

    }

}