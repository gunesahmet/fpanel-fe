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
 */namespace FPanel\Log;

use Phalcon\Mvc\User\Component;

class FB extends Component
{
    public static function setLogToInsightConsole($console)
    {
        FPHP::getInstance(true)->setLogToInsightConsole($console);
    }

    public static function setEnabled($enabled)
    {
        FPHP::getInstance(true)->setEnabled($enabled);
    }

    public static function getEnabled()
    {
        return FPHP::getInstance(true)->getEnabled();
    }

    public static function setObjectFilter($class, $filter)
    {
        FPHP::getInstance(true)->setObjectFilter($class, $filter);
    }

    public static function setOptions($options)
    {
        FPHP::getInstance(true)->setOptions($options);
    }

    public static function getOptions()
    {
        return FPHP::getInstance(true)->getOptions();
    }

    public static function send()
    {
        $args = func_get_args();
        return call_user_func_array(array(FPHP::getInstance(true), 'fb'), $args);
    }

    public static function group($name, $options=null)
    {
        return FPHP::getInstance(true)->group($name, $options);
    }

    public static function groupEnd()
    {
        return self::send(null, null, FPHP::GROUP_END);
    }

    public static function log($object, $label=null)
    {
        return self::send($object, $label, FPHP::LOG);
    }

    public static function info($object, $label=null)
    {
        return self::send($object, $label, FPHP::INFO);
    }

    public static function warn($object, $label=null)
    {
        return self::send($object, $label, FPHP::WARN);
    }

    public static function error($object, $label=null)
    {
        return self::send($object, $label, FPHP::ERROR);
    }

    public static function dump($key, $variable)
    {
        return self::send($variable, $key, FPHP::DUMP);
    }

    public static function trace($label)
    {
        return self::send($label, FPHP::TRACE);
    }

    public static function table($label, $table)
    {
        return self::send($table, $label, FPHP::TABLE);
    }

}