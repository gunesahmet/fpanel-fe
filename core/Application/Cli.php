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
namespace FPanel\Application;

use ConsoleLoader;
use FPanel\Console\Exception;
use FPanel\Console\Output as Output;
use FPanel\Console\Pid;
use FPanel\Environments\Crypto;
use FPanel\Environments\Date;
use FPanel\Environments\Environments;
use FPanel\Interfaces\IRun as IRun;
use FPanel\Log\Debugger;
use FPanel\Socket\Socket;
use FPanel\Validations\Validation;
use Phalcon\CLI\Console;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Crypt;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Events\Manager;
use Phalcon\Loader;
use Redis;

class Cli extends Console implements IRun
{
	/**
	 * @const
	 */
	const ERROR_SINGLE = 999;

	/**
	 * @var string filename that will store the pid
	 */
	protected $_pidFile;

	/**
	 * @var string directory path that will contain the pid file
	 */
	protected $_pidDir = '/tmp';

	/**
	 * @var bool	if debug mode is on or off
	 */
	protected $_isDebug;

	/**
	 * @var bool 	whether to record tasks by inserting into the database or not
	 */
	protected $_isRecording;

	/**
	 * @var
	 */
	protected $_argc;

	/**
	 * @var
	 */
	protected $_argv;

	/**
	 * @var
	 */
	protected $_isSingleInstance;

	/**
	 * Task for cli handler to run
	 */

	protected $_task;
	/**
	 * Action for cli handler to run
	 */
	protected $_action;

	/**
	 * Parameters to be passed to Task
	 * @var
	 */
	protected $_params;

	/**
	 * Task Id from the database
	 * @var
	 */
	protected $_taskId;

	/**
	 * @var
	 */
	protected $_mode;

	/**
	 * @var
	 */
	protected $_stdout;

	/**
	 * @var
	 */
	protected $_stderr;

    /**
     * @param string $pidDir
     */
	public function __construct(string $pidDir = '/tmp')
	{
		$this->_pidDir = $pidDir;
		$this->_stderr = $this->_stdout = '';
		$this->_isSingleInstance = $this->_isRecording = false;
		$this->_task = $this->_action = null;
		$this->_params = array();
		$this->_taskId = null;
	}

    /**
     * @throws \Exception
     */
	public function setConfig()
	{
        $redis = new Redis();
        $config = new Ini(ROOTPATH . "/config/config.ini");

		$di = new \Phalcon\DI\FactoryDefault\CLI();
        $di->setShared('config', function () use ($config) {
            return $config;
        });
        $di->set('socket', function() {
            return new Socket();
        });
        $di->set('date', function() {
            return new Date();
        });
        $di->set('env', function() {
            return new Environments();
        });
        $di->set('validation', function() {
            return new Validation();
        });
        $di->set('debug', function() {
            return new Debugger();
        });
        $di->set('crypt', function () {
            $crypt = new Crypt();
            $crypt->setKey(CRYPT_KEY);
            return $crypt;
        });
        $di->set('crypto', function () {
            return new Crypto();
        });

        $di->set('db', function() use ($di) {
			$creds = array(
                'host' => '127.0.0.1',
                'username' => USER_SQL_ADMIN,
                'password' => PANEL_SQL_PASS,
                'dbname' => DATABASE_NAME,
                'port' => 3306
			);
            return new Mysql($creds);
		});

        if(REDIS_STATUS == true) {
            try {
                $redis->connect("127.0.0.1",8879,0);
//                !$redis->connect(ROOTPATH . "/../var/run/fredis.sock");
                $redis->auth(PANEL_SQL_PASS);
            }
            catch(\Exception $e) {
                throw new \Exception("Redis Connection failed");
            }
        }

        $di->set('redis', function() use ($redis) {
            return $redis;
        });

		$this->setDI($di);
	}

	/**
	 * @param $file
	 * @throws \Exception
	 */
	public function setAutoload($file)
	{
		if (!file_exists($file)) {
			throw new \Exception('Unable to load ConsoleLoader');
		}

		// Set dir to be used inside include file
		include_once $file;
        $console_loader = new ConsoleLoader();

		$loader = new Loader();
		$loader->registerNamespaces($console_loader->namespace())->register();
	}

	/**
	 * @param $argv
	 * @param $argc
	 */
	public function setArgs($argv, $argc)
	{
		$this->_argv = $argv;
		$this->_argc = $argc;
	}

	/**
	 * @param Manager $events
	 */
	public function setEvents(Manager $events)
	{
		$this->setEventsManager($events);
	}

    /**
     * @return int
     */
	public function run(): int
    {
		try {
			$exit = 0;

			// Check on stupid mistakes
			$this->preTaskCheck($this->_argc);

			$this->determineTask($this->_argv);

			$this->checkProcessInstance();


			$args['task'] = 'FPanel\\Tasks' . "\\" . $this->_task;

			$args['action'] = !empty($this->_action) ? $this->_action : 'main';

			if (!empty($this->_params)) {
				$args['params'] = $this->_params;
			}

			// Kick off Task
			$this->handle($args);
			$this->removeProcessInstance();
		}
		catch(Exception $e) {
			$exit = 3;
			$this->handleException($e);
		}
        catch(\Exception $e) {
			$exit = 1;
			$this->handleException($e);
		}

		return $exit;
	}

	/**
     * @throws Exception
	 * @throws \Exception
	 */
	public function checkProcessInstance()
	{

		// Single Instance
		if ($this->isSingleInstance()) {

			$file = sprintf('%s-%s.pid', $this->_task, $this->_action);
			$pid = Pid::singleton($file);

			// Default
			$this->_pidFile = $file;

			if ($pid->exists()) {
				throw new \Exception('Instance of task is already running', self::ERROR_SINGLE);
			}
			else {
				// Create PID File
				/*
				$fp = fopen($this->_pidFile, 'r+');
				if (!flock($fp, LOCK_EX | LOCK_NB)) {
					fclose($fp);
					throw new \exceptions\System('unable to create pid file', $desc);
				}*/

				if ($pid->create()) {
					if ($this->isDebug()) {
						Output::stdout("[DEBUG] Created Pid File: " . $pid->getFileName());
					}
				} else {
					$desc = '';
					throw new Exception('unable to create pid file', $desc);
				}

				/*if (!file_put_contents($this->_pidFile, getmypid())) {
					$desc = '';
					throw new \exceptions\System('unable to create pid file', $desc);
				}*/

			}
		}
	}

    /**
     * @return bool
     */
	public function removeProcessInstance(): bool
    {
		if ($this->isSingleInstance()) {
			$pid = Pid::singleton('');
			if ($pid->created() && !$pid->removed()) {
				if ($result = $pid->remove()) {
					if ($this->isDebug()) {
						Output::stdout("[DEBUG] Removed Pid File: " . $pid->getFileName());
					}
				}
				else {
					$msg = Output::COLOR_RED . "[ERROR]" . Output::COLOR_NONE . " Failed to remove Pid File: $this->_pidFile";
					Output::stderr($msg);
				}
				return $result;
			}
		}

		return true;
	}

    /**
     * @param $flags
     * @throws Exception
     */
	protected function determineTask($flags)
	{

		// Since first argument is the name so script executing (pop it off the list)
		array_shift($flags);

		if (is_array($flags) && !empty($flags)) {
			foreach($flags as $flag) {
				if (empty($this->_task) && !$this->isFlag($flag)) {
					$this->_task = $flag;
				}
				else if (empty($this->_action) && !$this->isFlag($flag)) {
					$this->_action = $flag;
				}
				else if (!$this->isFlag($flag)) {
					$this->_params[] = $flag;
				}
			}
		}
		else {
			throw new Exception('Unable to determine task/action/params');
		}
	}

    /**
     * @param $mode
     */
	public function setMode($mode)
	{
		$this->_mode = $mode;
	}

    /**
     * @param $dir
     */
	public function setPidDir($dir)
	{
		$this->_pidDir = $dir;
	}

	/**
	 * @param $argc
	 * @throws \Exception
	 */
	protected function preTaskCheck($argc)
	{
		// Make sure task is added
		if ($argc < 2) {
			throw new \Exception('Please enter module name');
		}
	}

	/**
	 * sets debug mode
	 *
	 * @param bool $debug
	 */
	public function setDebug(bool $debug)
	{
		$this->_isDebug = $debug;
		if ($debug) {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
		}
	}

	public function isDebug(): bool
    {
		return $this->_isDebug;
	}

	/**
	 * Set Application to record results to database
	 *
	 * @param bool
	 */
	public function setRecording($record)
	{
		$this->_isRecording = $record;
	}

	/**
	 * Determine if application is recording output (stdout and stderr) to database
	 *
	 * @return bool
	 */
	public function isRecording(): bool
    {
		return $this->_isRecording;
	}

	/**
	 * Set Single Instance Flag
	 *
	 * @param bool
	 */
	public function setSingleInstance($single)
	{
		$this->_isSingleInstance = $single;
	}

	/**
	 * determine if only a single instance is allowed to run
	 *
	 * @return bool
	 */
	public function isSingleInstance(): bool
    {
		return $this->_isSingleInstance;
	}

	/**
	 * @param \Exception $e
     */
	protected function handleException(\Exception $e)
	{

		// Remove Process Instance
		if ($e->getCode() != self::ERROR_SINGLE) {
			$this->removeProcessInstance();
		}

		// Update Failure


        if(PRODUCTION_STATUS == "DEBUG") {
			$sub = '%s[ERROR]%s %s file: %s line: %d';
			$msg = sprintf($sub, Output::COLOR_RED, Output::COLOR_NONE, $e->getMessage(), $e->getFile(), $e->getLine());
		}
		else {
			$message = preg_replace("/'Fpanel.*/", "", $e->getMessage());
			$sub = '%s[ERROR]%s %s';
			$msg = sprintf($sub, Output::COLOR_RED, Output::COLOR_NONE, $message,null,null);
		}

		// Let user that ran this know it failed
		Output::stderr($msg);
	}

	protected function isFlag($flag): bool
    {
		return substr(trim($flag), 2) == '--';
	}

	public function getPidFile(): string
    {
		return $this->_pidFile;
	}

	public function getTaskId()
	{
		return $this->_taskId;
	}
}
