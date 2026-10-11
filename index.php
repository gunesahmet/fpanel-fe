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
 */use FPanel\Translate\Translate;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Debug;
set_error_handler('exceptions_error_handler');

$extensions = [
    "imap",
    "phalcon",
    "ionCube Loader",
    "PDO",
    "pdo_sqlite",
    "pdo_sqlsrv",
    "redis",
    "sqlsrv",
];
foreach ($extensions as $extension) {
    if(!extension_loaded($extension)) {
        echo '<style>pre {background: #fdf5ce none repeat scroll 0 0 !important;border-collapse: collapse !important;}pre{padding:10px} pre span{color:#da0024  !important}</style>';
        echo "<pre>";
        echo "Extension: $extension isn't load";
        echo "\n";
        echo "</pre>";
        exit(255);
    }
}

/**
 * @throws ErrorException
 */
function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
}

ini_set('short_open_tag', "on");
ini_set("memory_limit","2048M");

const ROOTPATH = __DIR__;
$status = "PRODUCTION";

if(file_exists(ROOTPATH . "/.debug")) {
    $status = "DEBUG";
}

const PRODUCTION_STATUS = "PRODUCTION";
require __DIR__ . '/config/Constants.php';
require __DIR__ . '/config/Define.php';

$constants = new Constants();
$constants->load();

$defines = new Define();
$defines->defines();

$redis_status = true;

try {
    $redis = new \Redis();
    if(!$redis->connect("127.0.0.1","8879",2)) {
        $redis_status = false;
    }
    if(!$redis->auth(PANEL_SQL_PASS)) {
        $redis_status = false;
    }
}
catch(\Exception $e) {
    $redis_status = false;
}

define('REDIS_STATUS', $redis_status);

if(PRODUCTION_STATUS !== "DEBUG") {
    ini_set('display_errors', true);
    if(PANEL_OS_TYPE == WINDOWS) {
        set_error_handler("customError",E_ALL);
    }
}
else {
    ini_set('display_errors', false);
    set_error_handler("customError",E_ALL);
}

function customError($errno, $err_str, $script, $line)
{
    $date = date('m/d/Y H:i:s a', time());
    echo "<pre>";
    $data = [
        'date' => $date,
        'code' => $errno,
        'message' => $err_str,
        'file' => $script,
        'line' => $line,
    ];
    echo print_r($data,true);
    exit(0);
}

if(!is_dir('logs')) {
    try {
        mkdir('logs');
    }
    catch (\Exception $e) {}
}
if(!is_dir('tmp')) {
    try {
        mkdir('tmp');
    }
    catch (\Exception $e) {}
}

function executeWithoutErrorHandler(callable $function): void
{
    set_error_handler(function () {});
    try {
        $function();
    }
    finally {
        restore_error_handler();
    }
}

if(php_sapi_name() != 'cli' ) {
    require __DIR__. '/core/Translate/Translate.php';
    require __DIR__ . '/config/Loader.php';
    $loader = new \Loader();

    if(PRODUCTION_STATUS == "DEBUG") {
        $debug = new Debug();
        $debug->setShowFiles(true);
        $debug->_uri = "https://fpanel.net/debug/";
        $debug->listen();
        try {
            $loader->init();
        }
        catch (\Exception $e) {
            echo '<style>pre {background: #fdf5ce none repeat scroll 0 0 !important;border-collapse: collapse !important;}pre{padding:10px} pre span{color:#da0024  !important}</style>';
            echo "<pre>";
            echo $e->getMessage();
            echo "\n";
            echo $e->getFile();
            echo "\n";
            echo $e->getLine();
            echo "</pre>";
            exit(255);
        }

        $ini = new Ini(__DIR__.'/config/config.ini');
        if(isset($ini->application->timezone))
            date_default_timezone_set($ini->application->timezone);

        function setTranslate(): Translate
        {
            $translate = new Translate();
            $translate->initPath();
            return $translate;
        }
        $translate = setTranslate();
        function __($str, $bind = null)
        {
            global $translate;
            return $translate->loadCustomTranslate($str, $bind);
        }
        $loader->run();
    }
    else {
        try {
            error_reporting(-1);
            $loader->init();
            function setTranslate() {
                $translate = new Translate();
                $translate->initPath();
                return $translate;
            }
            $translate = setTranslate();
            function __($str, $bind = null)
            {
                global $translate;
                return $translate->loadCustomTranslate($str, $bind);
            }
            $loader->run();
        }
        catch (\Exception $e) {
            echo '<style>pre {background: #fdf5ce none repeat scroll 0 0 !important;border-collapse: collapse !important;}pre{padding:10px} pre span{color:#da0024  !important}</style>';
            echo "<pre>";
            $content = file_get_contents("php://input");
            echo print_r("Content: " . $content, true);
            echo "\n";
            echo $e->getFile();
            echo "\n";
            echo $e->getMessage();
            echo "\n";
            echo $e->getLine();
            echo "</pre>";
            exit(255);
        }
    }
    exit(0);
}
