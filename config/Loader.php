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
use FPanel\Exceptions\InvalidObjectsException;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher;

function sanitize_output($buffer)
{
    $search = array(
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    );
    $replace = array(
        '>',
        '<',
        '\\1',
        ''
    );
    return preg_replace($search, $replace, $buffer);
}
/**
 * Class Loader
 */
class Loader
{
    /**
     * @var Phalcon\Mvc\Application
     */
    private $application;

    /**
     * @throws Exception
     */
    public function init()
    {
        require_once ROOTPATH."/config/Modules.php";
        require_once ROOTPATH."/config/Router.php";
        require_once ROOTPATH."/config/Namespaces.php";
        require_once ROOTPATH."/config/Events.php";
        require_once ROOTPATH."/config/Objects.php";
        require_once ROOTPATH."/config/Connections.php";

        $di = new Phalcon\DI\FactoryDefault();
        $dispatcher = new Dispatcher();
        $eventsManager = new Manager();
        $objects = new Objects();
        $connections = new Connections();
        $namespaces = new Namespaces();
        $module = new Modules();
        $router = new Router();
        $events = new Events();

        $loader = new Phalcon\Loader();

        $namespaces->load($loader);

        try {
            $objects->setObjects($di);
        }
        catch(Exception $e) {
            throw new InvalidObjectsException("\n<span>Invalid SetObject</span>\nMessage: ".$e->getMessage()."\nLine Number: " . $e->getLine() ."\n");
        }

        $this->application = new Phalcon\Mvc\Application();

        $module->load($this->application);


        $router->load($di, $this->application);
        try {
            $connections->loadServices($di);
        }
        catch(Exception $e) {
            throw new InvalidObjectsException("\n<span>Invalid SetObject</span>\nMessage: ".$e->getMessage()."\nLine Number: " . $e->getLine() ."\n");
        }


        if(PRODUCTION_STATUS == "DEBUG") {
            $events->load($eventsManager, $di);
        }

        $dispatcher->setEventsManager($eventsManager);

        $di->setShared('dispatcher', $dispatcher);

        $this->application->setDI($di);

    }

    function run()
    {
        echo sanitize_output($this->application->handle()->getContent());
    }
}
