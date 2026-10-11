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
namespace ClientBackup;

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
            'module'     => 'ClientBackup',
            'controller' => 'clientbackup'
        ));

        $backup->setPrefix('/backupclient');

        $route_list = array(
            //array('link','method','name')
            array('','index','Client Backup'),
            array('/create','create','Create Client Backup'),
            array('/restore/{id}','restore','Restore Client Backup'),
            array('/delete/{id}','delete','Delete Client Backup'),
            array('/download/{id}','download','Download Client Backup'),
            array('/status/{id}','status','Client Backup'),
            array('/cancel/{id}','cancel','Delete Client Backup'),
            array('/progress/{restore_id}','progress','Client Backup'),
            array('/upload','upload','Upload Client Backup'),
            array('/repo','repo','Client Backup Repo'),
            array('/repo/create','createrepo','Create Client Backup Repo'),
            array('/repo/edit/{id}','editrepo','Edit Client Backup Repo'),
            array('/repo/delete/{id}','deleterepo','Delete Client Backup Repo'),
            array('/storage','storage','Client Backup Storage'),
            array('/storage/create','addstorage','Create Client Backup Storage'),
            array('/storage/edit/{id}','editstorage','Edit Client Backup Storage'),
            array('/storage/delete/{id}','deletestorage','Delete Client Backup Storage'),
        );

        foreach ($route_list as $url) {

            $backup->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $backup->add($url['0'].'/{type}/{client_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }


        $router->mount($backup);

        return $router;

    }

}