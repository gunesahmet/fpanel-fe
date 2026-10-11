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
namespace Users;

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
        $users = new Group(array(
            'module'     => 'Users',
            'controller' => 'users'
        ));

        $users->setPrefix('/users');

        $route_list = array(
            //array('link','method','name')
            array('','index','Client & Reseller'),
            array('/create','create','Create Client & Reseller'),
            array('/edit/{id}','edit','Edit Client & Reseller'),
            array('/delete/{id}','delete','Delete Client & Reseller'),
            array('/suspend/{id}/{status}','suspend','Suspend Client & Reseller'),
            array('/multiple','multiple','Delete Client & Reseller'),
            array('/change/{id}','change','Client & Reseller'),
        );

        foreach ($route_list as $url) {
            $users->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $users->add($url['0'].'/{type}/{client_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($users);

        return $router;

    }

}