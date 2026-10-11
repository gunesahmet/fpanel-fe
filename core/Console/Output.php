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
namespace FPanel\Console;

class Output
{

	/**
	 * @const COLOR_RED
	 */
	const COLOR_RED = "\e[0;31m";

	/**
	 * @const COLOR_YELLOW
	 */
	const COLOR_YELLOW = "\e[1;33m";

	/**
	 * @const COLOR_GREEN
	 */
	const COLOR_GREEN = "\e[0;32m";

	/**
	 * @const COLOR_BLUE
	 */
	const COLOR_BLUE = "\e[0;34m";

	/**
	 * @const COLOR_NONE original shell color
	 */
	const COLOR_NONE = "\e[0m";


	/**
	 * @var string output sent to standard error
	 */
	protected static $_stderr;

	/**
	 * @var string output sent to standard output
	 */
	protected static $_stdout;


	/**
	 * @param $msg
	 */
	public static function stderr($msg)
	{
		fwrite(STDERR, $msg . PHP_EOL);
		self::$_stderr .= $msg . PHP_EOL;
	}


	/**
	 * @param $msg
	 */
	public static function stdout($msg)
	{
		fwrite(STDOUT, $msg . PHP_EOL);
		self::$_stdout .= $msg . PHP_EOL;
	}


	/**
	 * @return string
	 */
	public static function getStdout(): string
    {
		return self::$_stdout;
	}


	/**
	 * @return string
	 */
	public static function getStderr(): string
    {
		return self::$_stderr;
	}

	/**
	 *
	 */
    public static function clear()
	{
//        unset(self::$_stderr);
        self::$_stderr = '';        
//        unset(self::$_stdout);
        self::$_stdout = '';        
    }
}
