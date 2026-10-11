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
use Phalcon\DI\FactoryDefault;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher;

class Events
{
    /**
     * @param Manager $events_manager
     * @param FactoryDefault $di
     * @throws Exception
     */
    public function load(Manager $events_manager, FactoryDefault $di)
    {
        $events_manager->attach("dispatch", function ($event, $dispatcher, $exception) use ($di) {
            /**
             * @var $event Event
             */
            if ($event->getType() == 'beforeNotFoundAction') {
                /**
                 * @var Dispatcher $dispatcher
                 */
                $action_name = $dispatcher->getActionName();
                throw new Exception("\n<span>Action: <b>$action_name</b> Not found</span>\n");
            }

            if ($event->getType() == 'beforeException') {
                /**
                 * @var $exception \Phalcon\Exception
                 */
                switch ($exception->getCode()) {
                    case \Phalcon\Dispatcher::EXCEPTION_NO_DI:
                        throw new Exception("\n<span>Invalid Load</span>\nMessage: ".$exception->getMessage()."\n\nFile: ".$exception->getFile()."\nLine Number: " . $exception->getLine() ."\n");
                    case \Phalcon\Dispatcher::EXCEPTION_INVALID_PARAMS:
                        throw new Exception("\n<span>Invalid Parameters</span>\nMessage: ".$exception->getMessage()."\nLine Number: " . $exception->getLine() ."\n");
                    case \Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                        throw new Exception("\n<span>Message: ". $exception->getMessage() ."</span>\n<span>Line: ".$exception->getLine()."</span>");
                    case \Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        throw new Exception("\n<span>Invalid Action</span>\nMessage: ".$exception->getMessage()."\nLine Number: " . $exception->getLine() ."\n");
                    case \Phalcon\Dispatcher::EXCEPTION_INVALID_HANDLER:
                        throw new Exception("\n<span>Invalid Handlers</span>\nMessage: ".$exception->getMessage()."\nLine Number: " . $exception->getLine() ."\n");
                    case \Phalcon\Dispatcher::EXCEPTION_CYCLIC_ROUTING:
                        throw new Exception("\n<span>Route cyclic</span>\nMessage: ".$exception->getMessage()."\nLine Number: " . $exception->getLine() ."\n");
                }
            }
        });
    }
}