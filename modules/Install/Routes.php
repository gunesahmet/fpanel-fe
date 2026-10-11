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
 */namespace Install;

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
        $index = new Group(array(
            'module'     => 'Install',
            'controller' => 'install'
        ));

        $route_list = array(
            array(
                'link' => '/install',
                'controller' => 'install',
                'action' => 'index',
                'name' => ''
            ),
            array(
                'link' => '/install/licence',
                'controller' => 'install',
                'action' => 'licence',
                'name' => ''
            ),
            array(
                'link' => '/install/serverinfo',
                'controller' => 'install',
                'action' => 'serverinfo',
                'name' => ''
            ),
            array(
                'link' => '/install/generalinfo',
                'controller' => 'install',
                'action' => 'generalinfo',
                'name' => ''
            ),
            array(
                'link' => '/install/finish',
                'controller' => 'install',
                'action' => 'finish',
                'name' => ''
            ),
        );
        foreach($route_list as $url) {
            $index->add($url['link'],
                array(
                    'controller' => $url['controller'],
                    'action' => $url['action'],
                )
            )->setName($url['name']);
        }
        $router->mount($index);


        return $router;

    }

}
