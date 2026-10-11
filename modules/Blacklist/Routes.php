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
namespace Blacklist;

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
        $blacklist = new Group(array(
            'module'     => 'Blacklist',
            'controller' => 'blacklist'
        ));

        $blacklist->setPrefix('/blacklist');

        $route_list = array(
            //array('link','method','name')
            array('','index','Blacklist Listing'),
            array('/ftpusers','ftpusers','Blacklist Listing'),
            array('/domains','domains','Blacklist Listing'),
            array('/dbusers','dbusers','Blacklist Listing'),
            array('/emailusers','emailusers','Blacklist Listing'),
            array('/addemailuser','addemailuser','Blacklist Add'),
            array('/adddbuser','adddbuser','Blacklist Add'),
            array('/adddomain','adddomain','Blacklist Add'),
            array('/addftpuser','addftpuser','Blacklist Add'),
            array('/edit/{id}','edit','Blacklist Edit'),
            array('/delete/{id}','delete','Blacklist Delete'),
        );

        foreach ($route_list as $url) {
            $blacklist->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $blacklist->add($url['0'].'/{type}/{server_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($blacklist);

        return $router;

    }

}