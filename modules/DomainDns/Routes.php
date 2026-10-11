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
namespace DomainDns;

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
        $dns = new Group(array(
            'module'     => 'DomainDns',
            'controller' => 'domaindns'
        ));

        $dns->setPrefix('/dns');

        $route_list = array(
            //array('link','method','name')
            array('','index','Domain DNS'),
            array('/create','create','Create Domain DNS'),
            array('/restore','restore','Restore Domain DNS'),
            array('/remove/multiple','removemultiple','Delete Domain DNS'),
            array('/edit/{id}','edit','Edit Domain DNS'),
            array('/delete/{id}','delete','Delete Domain DNS'),
        );

        foreach ($route_list as $url) {
            $dns->add($url[0], array(
                'action' => $url[1],
            ))->setName($url[2]);

            $dns->add($url[0].'/{type}/{domain_id}', array(
                'action' => $url[1],
            ))->setName($url[2]);
        }

        $router->mount($dns);

        return $router;

    }

}