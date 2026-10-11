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
use FPanel\Environments\Url;
use Phalcon\Config\Adapter\Ini;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application;

class Router
{
    /**
     * @param Phalcon\DI\FactoryDefault $di
     * @param Phalcon\Mvc\Application $application
     */
    public function load(FactoryDefault $di, Application $application)
    {
        $router = new Phalcon\Mvc\Router(false);

        $router->setDefaultModule('Main');
        $router->setDefaultController('main');
//        $router->setDefaultAction('index');

        // Example: dashboard/domain/show/12
//        $router->add('/:module/:controller/:action/:params', array(
//            'module'     => 1,
//            'controller' => 2,
//            'action'     => 3,
//            'params'     => 4
//        ))->setName('default');

        $config = new Ini(ROOTPATH . "/config/config.ini");

        $uri = new Url();


        if ((strpos($_SERVER['REQUEST_URI'],'?') !== false)){
            $new_link = preg_replace("/\?.*/", "", $_SERVER['REQUEST_URI']);
        }
        else {
            $new_link = $_SERVER['REQUEST_URI'];
        }
        $url_raw = ltrim($new_link,"/");


        if(strpos(trim($url_raw), trim($config->api->address)) !== false) {
            $url_clear = $url_raw;
        }
        else {
            $url_clear = $uri->url_decrypt($url_raw);

            if($config->telegram->status == "1") {
                if ($url_raw == "telegram/" . $config->telegram->api_key) {
                    $url_clear = $url_raw;
                }
            }
        }


        $_SERVER['REQUEST_URI'] = "/" . $url_clear;
        $router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
//        echo $url_clear;
//        exit(0);
        foreach ($application->getModules() as $module) {
            if(!file_exists($module['path'])) {
                print_r("Module doesn't exists: " . $module['path']);
                exit;
            }

            $routesClassName = str_replace('Module', 'Routes', $module['className']);
            if (class_exists($routesClassName)) {
                /**
                 * @var $routesClass \Phalcon\Mvc\Router
                 */
                $routesClass = new $routesClassName();
                $router      = $routesClass->add($router);
            }
            else {
                print_r($routesClassName .' not registered');
                exit;
            }
        }

        $di->set('router', $router);
    }
}