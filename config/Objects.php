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
use FPanel\Environments\Date;
use FPanel\Environments\Environments;
use FPanel\Environments\QueryBuilder;
use FPanel\Log\FPHP;
use FPanel\Session\Cookie;
use Phalcon\Config\Adapter\Ini;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;

class Objects
{
    /**
     * @param $di FactoryDefault
     * @throws Exception
     */
    public function setObjects(FactoryDefault $di)
    {
        $redis = new Redis();
        $debug = new FPanel\Log\Debugger();
        $logger = new FPanel\Log\Logger();
        $fcookies = new Cookie();
        $socket = new \FPanel\Socket\Socket();
        $date = new Date();
        $env = new Environments();
        $qbuilder = new QueryBuilder();
        $request_id = $env->createUuid();
        $config = new Ini(ROOTPATH . "/config/config.ini");

        $di->setShared('request_id', function () use ($request_id) {
            return $request_id;
        });

        if(REDIS_STATUS) {
            try {
                $redis->connect("127.0.0.1",8879,0);
                $redis->auth(PANEL_SQL_PASS);
            }
            catch(Exception $e) {
                throw new Exception("\n<span>Invalid Load</span>\nMessage: ".$e->getMessage()."\nLine Number: " . $e->getLine() ."\n");
            }
        }

        $di->setShared('session', function () use ($config) {
            if(REDIS_STATUS) {
                $session = new FPanel\Session\Redis([
                    "type" => "tcp",
                    "host" => "127.0.0.1:8879",
                    "timeout" => $config->session->timeout,
                    "name" => md5("fpanel-sessions"),
                    "auth" => PANEL_SQL_PASS
                ]);
            }
            else {
                $session = new Phalcon\Session\Adapter\Files();
            }

            if (!$session->isStarted()) {
                $session->start();
            }
            return $session;
        });

        if(!file_exists(PANEL_DIR . "/etc/.sys.packages")) {
            throw new Exception("Unable to load system packages");
        }

        $packages = [];
        $fn = fopen(PANEL_DIR . "/etc/.sys.packages","r");
        while(!feof($fn))  {
            $result = fgets($fn);
            if(!empty($result)) {
                if(empty($result[1])) {
                    continue;
                }
                if(!isset($result[1])) {
                    continue;
                }
                $result = explode("=", trim($result));
                $packages[$result[0]] = trim($result[1]);
            }
        }
        fclose($fn);





        $di->set('modelsManager', function () {
            return new Phalcon\Mvc\Model\Manager();
        });

        $di->setShared('view', function() use ($config) {
            $view = new View();
            $view->setViewsDir(ROOTPATH . "/templates/". $config->application->template);
            $view->registerEngines(array(
                '.php' => 'Phalcon\Mvc\View\Engine\Php'
            ));
            return $view;
        });

        $alerts = [
            'success' => 'alert alert-success alert-dismissable fade in',
            'notice' => 'alert alert-info alert-dismissable fade in',
            'warning' => 'alert alert-warning alert-dismissable fade in',
            'error' => 'alert alert-danger alert-dismissable fade in'
        ];

        $di->set('flash', function() use ($alerts) {
            return new Phalcon\Flash\Session($alerts);
        });

        $di->set('flashDirect', function() use ($alerts) {
            return new Phalcon\Flash\Direct($alerts);
        });

        $di->set('url', function() {
            $url = new Phalcon\Mvc\Url();
            $url->setBaseUri('/');

            $url->setBasePath('/');
            return $url;
        });

        $di->set('crypt', function () {
            $crypt = new Phalcon\Crypt();
            $crypt->setCipher("aes-256-cfb");
            $crypt->setKey(CRYPT_KEY);
            return $crypt;
        });

        $di->set('fcookies', function() use ($fcookies){
            return $fcookies;
        });

        $di->set('crypto', function () {
            return new FPanel\Environments\Crypto();
        });

        $di->set('auth', function() {
            return new FPanel\Auth\Auth();
        });


        $di->set('message', function() {
            return new FPanel\Environments\Messages();
        });

        $di->setShared("env", function() use ($env) {
            return $env;
        });

        $di->set('debug', function() use ($debug) {
            return $debug;
        });

        $di->set('queryBuilder', function() use($qbuilder) {
            return $qbuilder;
        });

        $di->set('log', function() use ($logger) {
            return $logger;
        });

        $di->set('fb', function () {
            return FPHP::getInstance(true);
        });


        $di->set('translate', function() {
            return new FPanel\Translate\Translate();
        });

        $di->set('uri', function() {
            return new FPanel\Environments\Url();
        });

        $di->set('env', function() {
            return new FPanel\Environments\Environments();
        });

        $di->set('panel_installed', function() {
            if(file_exists(ROOTPATH . '/.firstboot')) {
                return false;
            }
            return true;
        });

        $di->set('redis', function() use ($redis) {
            return $redis;
        });

        $di->set('acl', function() {
            return new FPanel\Acl\Acl();
        });

        $di->set('socket', function() use ($socket) {
            return $socket;
        });

        $di->set('breadcrumbs', function() {
            return new FPanel\Menu\Breadcrumbs();
        });

        $di->set('sidebar', function() {
            return new FPanel\Menu\Sidebar();
        });

        $di->set('desktop', function() {
            return new FPanel\Menu\Desktop();
        });

        $di->set('validation', function() {
            return new FPanel\Validations\Validation();
        });

        $di->set('config', function() use ($config) {
            return $config;
        });

        $di->set('api_link',function() use ($config) {
            return $config->api->address;
        });

        $di->set('portal_link',function() use ($config) {
            return "https://fpanel.net/wiki/errors";
        });

        $di->set('api_status', function() use ($config) {
            if($config->api->status == 1)
                return true;
            else
                return false;
        });

        $di->set('api_user', function() use ($config) {
            return $config->api->user;
        });

        $di->set('date', function() use ($date) {
            return $date;
        });

        $di->set('api_passwd', function() use ($config) {
            return $config->api->pass;
        });


        $di->set("system_packages", function () use ($packages) {
            return $packages;
        });

    }
}