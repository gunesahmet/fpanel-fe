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

class Pid
{

	protected $_isCreated;

	protected $_isRemoved;

	protected static $_instance;

	protected $_fp;

	public $_pidFile;

	/**
	 * @param $file
	 */
	protected function __construct($file)
	{
		$this->_pidFile = $file;
		$this->_isCreated = false;
		$this->_isRemoved = false;
		$this->_fp = null;
	}

	/**
	 * @param $file
	 * @param string $dir
	 * @return Pid
	 */
	public static function singleton($file, string $dir = '/tmp'): Pid
    {
		if (empty(self::$_instance)) {
			self::$_instance = new Pid($dir . '/' . $file);	
		}
		return self::$_instance;
	}

	/**
	 * @return bool
	 */
	public function remove(): bool
    {
		if ($this->_isCreated) {
			fclose($this->_fp);
			if (unlink($this->_pidFile)) {
				return $this->_isRemoved = true;
			}
			else {
				return false;
			}
		}
		return $this->_isRemoved = true;
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function create(): bool
    {
		if ($this->exists()) {
			throw new \Exception('Pid File exists/Instance of script is already running');
		}

		if (is_writable($this->_pidFile)) {
			throw new \Exception('Unable to write to this file');
		}

		if ($this->_fp = fopen($this->_pidFile, "x")) {
			if (!flock($this->_fp, LOCK_EX | LOCK_NB)) {
				fclose($this->_fp);
				return false;
			}
		}
		else {
			return false;
		}

		$this->_isCreated = true;
		return true;
	}

	/**
	 * @return bool
	 */
	public function exists(): bool
    {
		return file_exists($this->_pidFile);
	}

	/**
	 * @return bool
	 */
	public function created(): bool
    {
		return $this->_isCreated;
	}

	/**
	 * @return bool
	 */
	public function removed(): bool
    {
		return $this->_isRemoved;
	}

	/**
	 * @return mixed
	 */
	public function getFileName()
	{
		return $this->_pidFile;
	}
}
