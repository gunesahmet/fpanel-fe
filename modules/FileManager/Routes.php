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
namespace FileManager;

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
        $tasks = new Group(array(
            'module'     => 'FileManager',
            'controller' => 'filemanager'
        ));

        $tasks->setPrefix('/filemanager');

        $route_list = array(
            //array('link','method','name')
            array('','index','FileManager'),
            array('/createfolder','createfolder','FileManager'),
            array('/createfile','createfile','FileManager'),
            array('/editfile','editfile','FileManager'),
            array('/savefile','savefile','FileManager'),
            array('/rename','rename','FileManager'),
            array('/delete','delete','FileManager'),
            array('/move','move','FileManager'),
            array('/permission','permission','FileManager'),
            array('/getpermission','getpermission','FileManager'),
            array('/getfiles','getfiles','FileManager'),
            array('/upload','upload','FileManager'),
            array('/download/{file_name}','download','FileManager'),
            array('/cancel','cancel','FileManager'),
        );

        foreach ($route_list as $url) {
            $tasks->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $tasks->add($url['0'].'/{type}/{domain_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        $router->mount($tasks);

        return $router;

    }

}