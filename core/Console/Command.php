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

class Command
{

	/**
	 * @var string command executed
	 */
	public $command;

	/**
	 * @var string file that executed command
	 */
	public $file;

	/**
	 * @var int on which line was command executed
	 */
	public $line;

	/**
	 * @var bool was command successful
	 */
	public $success;

	/**
	 * @var string  standard output from command
	 */
	public $stdout;

	/**
	 * @var string  error output from command
	 */
	public $stderr;

	/**
	 * @var string time taken for command to execute
	 */
	public $time;

	/**
	 * @var int	command status/return result
	 */
	public $result_code;


	/**
	 * echo out command to table row display
	 */
	public function toRow()
	{
		echo "<tr><td>{$this->command}</td><td>{$this->file}</td><td>{$this->line}</td></tr>";
	}
}
