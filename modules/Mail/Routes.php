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
namespace Mail;

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
        $mail = new Group(array(
            'module'     => 'Mail',
            'controller' => 'mail'
        ));

        $mail->setPrefix('/mail');

        $route_list = array(
            //array('link','method','name')
            array('','index','Domain Mail'),
            array('/mailsettings','mailsettings','Domain Mail Settings'),

            array('/accounts','accounts','Domain Mails'),
            array('/multiple/{id_type}','multiple','Remove Domain Mail'),
            array('/create','create','Create Domain Mail'),
            array('/edit/{id}','edit','Edit Domain Mail'),
            array('/delete/{id}','delete','Delete Domain Mail'),

            array('/alias/{mail_id}','alias','Domain Mail Alias'),
            array('/createalias/{mail_id}','createalias','Create Domain Mail Alias'),
            array('/editalias/{alias_id}/{mail_id}','editalias','Edit Domain Mail Alias'),
            array('/deletealias/{alias_id}/{mail_id}','deletealias','Delete Domain Mail Alias'),
        );

        foreach ($route_list as $url) {
            $mail->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);

            $mail->add($url['0'].'/{type}/{domain_id}', array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        $router->mount($mail);

        return $router;

    }

}