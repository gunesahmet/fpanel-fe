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
namespace Telegram;

use Phalcon\Config\Adapter\Ini;
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
        $telegram = new Group(array(
            'module'     => 'Telegram',
            'controller' => 'telegram'
        ));
        $config = new Ini(ROOTPATH . "/config/config.ini");

        $telegram->setPrefix('/telegram');

        $route_list = array(
            array('','index','Telegram Settings'),
            array('/settings','settings','Telegram Settings'),
            array('/activation','activate','Telegram Settings'),
            array('/deactivate/{chat_id}','deactivate','Telegram Settings'),
        );

        foreach ($route_list as $url) {
            $telegram->add($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        if($config->telegram->status == "1") {
            $api_key = $config->telegram->api_key;
            $telegram->add("/$api_key", array(
                'action' => "api",
            ))->setName("telegram/api");
        }


        $router->mount($telegram);
        return $router;

    }

}