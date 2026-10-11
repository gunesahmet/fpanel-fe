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
namespace ServerBackup;

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
        $backup = new Group(array(
            'module'     => 'ServerBackup',
            'controller' => 'serverbackup'
        ));

        $backup->setPrefix('/serverbackup');

        $route_list = array(
            //array('link','method','name')
            array('','index','Server Backup'),
            array('/create','create','Create Server Backup'),
            array('/restore/{id}','restore','Restore Server Backup'),
            array('/delete/{id}','delete','Delete Server Backup'),
            array('/cancel/{id}','delete','Delete Server Backup'),
            array('/download/{id}','download','Download Server Backup'),
            array('/status/{id}','status','Server Backup'),
            array('/progress/{restore_id}','progress','Server Backup'),
            array('/upload','upload','Upload Server Backup'),
            array('/repo','repo','Server Backup Repo'),
            array('/repo/create','createrepo','Create Server Backup Repo'),
            array('/repo/edit/{id}','editrepo','Edit Server Backup Repo'),
            array('/repo/delete/{id}','deleterepo','Delete Server Backup Repo'),
            array('/storage','storage','Server Backup Storage'),
            array('/storage/create','addstorage','Create Server Backup Storage'),
            array('/storage/edit/{id}','editstorage','Edit Server Backup Storage'),
            array('/storage/delete/{id}','deletestorage','Delete Server Backup Storage'),
        );

        foreach ($route_list as $url) {
            $backup->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $backup->add($url['0'].'/{type}/{server_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($backup);

        return $router;

    }

}