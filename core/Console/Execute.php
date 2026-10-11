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

class Execute
{
	/**
	 * @var object
	 */
	protected static $_instance;

	/**
	 * @var array
	 */
	protected $_command;

	/**
	 * constructor to initialize class
	 */
	private function __construct() {
		$this->_command = array();
	}

	/**
	 *
	 */
	public static function singleton()
	{
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * execute a command
	 *
	 * @param string $cmd
	 * @param string $file
	 * @param int $line
	 * @param string|null $stdout string
	 * @param string|null $stderr Stderr result
     * @param string|null $stdin Readable resource stream or string to be passed to proc STDIN
	 *
	 * @return true|int return exit code of command
	 */
	public function execute(string $cmd, string $file, int $line, string &$stdout = null, string &$stderr = null, string $stdin = null)
	{
        $outFile = tempnam(".", "cli");
        $errFile = tempnam(".", "cli");

        $descriptor = array(0 => array("pipe", "r"),
            1 => array("file", $outFile, "w"),
            2 => array("file", $errFile, "w")
        );

		$start = microtime(true);

        $proc = proc_open($cmd, $descriptor, $pipes);
        if (!is_resource($proc)) {
            $result =  255;
			if($result == 255) {

			}
        }
		else {
            if($stdin){
                if(is_resource($stdin))
                    stream_copy_to_stream($stdin, $pipes[0]);
                else
                    fwrite($pipes[0], $stdin);
            }
            fclose($pipes[0]);
            $return = proc_close($proc);
        }
		$end = microtime(true);

        // Get Output
        $stdout = implode("", file($outFile));
        $stderr = implode("", file($errFile));

        // Remove temp files
        unlink($outFile);
        unlink($errFile);

		$command = new Command;
		$command->command = $cmd;
		$command->file = $file;
		$command->line = $line;
		$command->result_code = $return;
		$command->success = $return == 0;
		$command->stdout = $stdout;
        $command->stderr = $stderr;
		$command->time = ($end - $start);

		$this->_command[] = $command;

		return $return == 0 ? true : $return;
	}


	/**
	 * @return array
	 */
	public function getCommands(): array
    {
		return $this->_command;
	}


    /**
     * @return bool|string
     */
	public function __toString() {
		if (PHP_SAPI == 'cli')
			return print_r($this->_command, true);
		else
			return "<pre>" . print_r($this->_command, true) . "</pre>";
	}
}
