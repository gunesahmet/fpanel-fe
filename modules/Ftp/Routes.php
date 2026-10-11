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
namespace Ftp;

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
        $ftp = new Group(array(
            'module'     => 'Ftp',
            'controller' => 'ftp'
        ));

        $ftp->setPrefix('/ftp');

        $route_list = array(
            //array('link','method','name')
            array('','index','FTP Server Settings'),
            array('/settings','settings','FTP Server Settings'),
            array('/sessions','sessions','FTP Sessions'),
            array('/multiple','multiple','FTP Sessions'),
            array('/kill/{id}','kill','FTP Sessions'),
        );

        foreach ($route_list as $url) {
            $ftp->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $ftp->add($url['0'].'/{type}/{server_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($ftp);

        return $router;

    }

}