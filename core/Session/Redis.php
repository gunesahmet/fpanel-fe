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
 */namespace FPanel\Session;

use Phalcon\Session\Adapter;
use Phalcon\Session\AdapterInterface;
use Phalcon\Session\Exception;

class Redis extends Adapter implements AdapterInterface
{

    /**
     * @throws Exception
     */
    public function __construct($options = null)
    {
        try {
            if (!isset($options['host'])) {
                throw new Exception("The parameter 'save_path' is required");
            }

            $type = $options['type'];
            $host = $options['host'];
            $timeout = $options['timeout'];

            ini_set('session.save_handler', 'redis');


            ini_set('session.save_path', sprintf("%s://%s?auth=%s", $type, $host, PANEL_SQL_PASS));
//            ini_set('session.save_path', $host );

            if ($timeout == "0") {
                ini_set('session.gc_maxlifetime', 60 * 60 * 30);
            } else {
                ini_set('session.gc_maxlifetime', $timeout);
            }

            if (isset($options['name'])) {
                ini_set('session.name', $options['name']);
            }
            parent::__construct($options);
        }
        catch(\Exception $e) {
            throw new Exception("\n<span>Invalid Connection</span>\nMessage: Redis closed \nLine Number: " . $e->getLine() ."\n");
        }
    }
}