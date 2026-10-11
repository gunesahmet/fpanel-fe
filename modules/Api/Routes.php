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
 */namespace Api;

use Phalcon\Config\Adapter\Ini;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group;

class Routes
{
    /**
     * @param Router $router
     * @return Router
     */
    public function add(Router $router): Router
    {
        $config = new Ini(ROOTPATH . "/config/config.ini");

        if($config->api->status == "1") {
            $api_link = $config->api->address;
            $api = $this->api($api_link);
            $router->mount($api);

            $system = $this->system($api_link);
            $router->mount($system);

            $serviceplan = $this->serviceplan($api_link);
            $router->mount($serviceplan);

            $domain = $this->domain($api_link);
            $domain_dns = $this->domainDns($api_link);
            $domain_alias = $this->domainAlias($api_link);
            $domain_cron = $this->domainCron($api_link);
            $pro_dir = $this->proDir($api_link);
            $subdomain = $this->subdomain($api_link);
            $domain_mail = $this->domainMail($api_link);
            $domain_database = $this->domainDatabase($api_link);
            $router->mount($domain);
            $router->mount($domain_dns);
            $router->mount($domain_alias);
            $router->mount($domain_cron);
            $router->mount($pro_dir);
            $router->mount($subdomain);
            $router->mount($domain_mail);
            $router->mount($domain_database);

            $client = $this->client($api_link);
            $router->mount($client);
        }
        return $router;
    }

    private function api($api_link): Group
    {
        $api = new Group(array(
            'module'     => 'Api',
            'controller' => 'api',
        ));
        $api->setPrefix("/$api_link");


        $api->addPost("/login", array(
            'action' => "login",
        ))->setName("API Access");


        $api->addPost("/health", array(
            'action' => "health",
        ))->setName("API Health Check");
        return $api;
    }

    private function system($api_link): Group
    {
        $system = new Group(array(
            'module'     => 'Api',
            'controller' => 'system'
        ));
        $system->setPrefix("/$api_link/system");

        $system_routes = [
            array('/advanced-settings/{id}','advancedSettings','Api System Advanced Settings'),

            array('/blacklist/{id}','blacklist','Api Blacklist Management'),

            array('/servers/{id}','servers','Server Management'),

            array('/server/backup/{id}','backup','Api Server Backup Management'),

            array('/server/cron/{id}','cron','Api Server Cronjob Management'),

            array('/server/database/{id}','database','Api Database Server Management'),

            array('/server/dns/{id}','dns','Api DNS Server Management'),

            array('/server/ftp/{id}','ftp','Api Ftp Server Management'),

            array('/server/mail/{id}','mail','Api Mail Server Management'),

            array('/server/network/{id}','network','Api Network Management'),

            array('/server/service/{id}','service','Api Service Management'),

            array('/server/webserver/{id}','webserver','Api Web Server Management'),
        ];

        foreach ($system_routes as $url) {
            $system->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $system;
    }

    private function domain($api_link): Group
    {
        $domain = new Group(array(
            'module'     => 'Api',
            'controller' => 'domain'
        ));
        $domain->setPrefix("/$api_link/domain");

        $domain_routes = [
            array('/list','index','Api Domain Listing'),
            array('/create','create','Api Domain Create'),
            array('/update','update','Api Domain Update'),
            array('/delete','delete','Api Domain Delete'),
            array('/webhosting','webhosting','Api Domain Webhosting Settings'),
            array('/resources','resources','Api Domain Show Resources'),
        ];
        foreach ($domain_routes as $url) {
            $domain->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }
        return $domain;
    }

    private function client($api_link): Group
    {
        $client = new Group(array(
            'module'     => 'Api',
            'controller' => 'client'
        ));
        $client->setPrefix("/$api_link/client");

        $client_routes = [
            array('/list','index','Api Client Listing'),
            array('/create','create','Api Client Create'),
            array('/update','update','Api Client Update'),
            array('/delete','delete','Api Client Delete'),
        ];
        foreach ($client_routes as $url) {
            $client->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }
        return $client;
    }

    private function serviceplan($api_link): Group
    {
        $serviceplan = new Group(array(
            'module'     => 'Api',
            'controller' => 'serviceplan'
        ));
        $serviceplan->setPrefix("/$api_link/serviceplan");
        $serviceplan_routes = [
            array('/list','index','Api Serviceplan Listing'),
            array('/create','create','Api Serviceplan Create'),
            array('/update','update','Api Serviceplan Update'),
            array('/delete','delete','Api Serviceplan Delete'),
        ];
        foreach ($serviceplan_routes as $url) {
            $serviceplan->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $serviceplan;
    }

    private function domainDns($api_link): Group
    {
        $domain_dns = new Group(array(
            'module'     => 'Api',
            'controller' => 'domaindns'
        ));
        $domain_dns->setPrefix("/$api_link/domain/dns");
        $domain_dns_routes = [
            array('/list','index','Api Domain DNS Listing'),
            array('/create','create','Api Domain DNS Create'),
            array('/update','update','Api Domain DNS Update'),
            array('/delete','delete','Api Domain DNS Delete'),
        ];
        foreach ($domain_dns_routes as $url) {
            $domain_dns->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $domain_dns;
    }

    private function domainAlias($api_link): Group
    {
        $domain_alias = new Group(array(
            'module'     => 'Api',
            'controller' => 'domainalias'
        ));
        $domain_alias->setPrefix("/$api_link/domain/alias");
        $domain_alias_routes = [
            array('/list','index','Api Domain Alias Listing'),
            array('/create','create','Api Domain Alias Create'),
            array('/update','update','Api Domain Alias Update'),
            array('/delete','delete','Api Domain Alias Delete'),
        ];
        foreach ($domain_alias_routes as $url) {
            $domain_alias->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $domain_alias;
    }

    private function domainCron($api_link): Group
    {
        $domain_cron = new Group(array(
            'module'     => 'Api',
            'controller' => 'domaincron'
        ));
        $domain_cron->setPrefix("/$api_link/domain/cron");

        $domain_cron_routes = [
            array('/list','index','Api Domain Crons Listing'),
            array('/create','create','Api Domain Cron Create'),
            array('/delete','delete','Api Domain Cron Delete'),
        ];
        foreach ($domain_cron_routes as $url) {
            $domain_cron->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $domain_cron;
    }

    private function proDir($api_link): Group
    {
        $pro_dir = new Group(array(
            'module'     => 'Api',
            'controller' => 'protectdir'
        ));
        $pro_dir->setPrefix("/$api_link/domain/prodir");

        $pro_dir_routes = [
            array('/list','index','Api Domain Protect Directory Listing'),
            array('/create','create','Api Domain Protect Directory Create'),
            array('/update','update','Api Domain Protect Directory Update'),
            array('/delete','delete','Api Domain Protect Directory Delete'),
            array('/user/create','userCreate','Api Domain Protect Directory User Create'),
            array('/user/update','userUpdate','Api Domain Protect Directory User Update'),
            array('/user/delete','userDelete','Api Domain Protect Directory User Delete'),
        ];
        foreach ($pro_dir_routes as $url) {
            $pro_dir->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $pro_dir;
    }

    private function subdomain($api_link): Group
    {
        $subdomain = new Group(array(
            'module'     => 'Api',
            'controller' => 'subdomain'
        ));
        $subdomain->setPrefix("/$api_link/domain/subdomain");

        $subdomain_routes = [
            array('/list','index','Api Subdomain Listing'),
            array('/create','create','Api Subdomain Create'),
            array('/update','update','Api Subdomain Update'),
            array('/delete','delete','Api Subdomain Delete'),
        ];
        foreach ($subdomain_routes as $url) {
            $subdomain->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $subdomain;
    }

    private function domainMail($api_link): Group
    {
        $domain_mail = new Group(array(
            'module'     => 'Api',
            'controller' => 'domainmail'
        ));
        $domain_mail->setPrefix("/$api_link/domain/mail");

        $domain_mail_routes = [
            array('/list','index','Api Domain Mail Listing'),
            array('/create','create','Api Domain Mail Create'),
            array('/update','update','Api Domain Mail Update'),
            array('/delete','delete','Api Domain Mail Delete'),

            array('/alias/create','createAlias','Api Domain Mail Alias Create'),
            array('/alias/update','updateAlias','Api Domain Mail Alias Update'),
            array('/alias/delete','deleteAlias','Api Domain Mail Alias Delete'),
        ];
        foreach ($domain_mail_routes as $url) {
            $domain_mail->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $domain_mail;
    }

    private function domainDatabase($api_link): Group
    {
        $domain_mail = new Group(array(
            'module'     => 'Api',
            'controller' => 'domaindatabase'
        ));
        $domain_mail->setPrefix("/$api_link/domain/database");

        $domain_mail_routes = [
            array('/list','index','Api Domain Database Listing'),
            array('/create','create','Api Domain Database Create'),
            array('/update','update','Api Domain Database Update'),
            array('/delete','delete','Api Domain Database Delete'),
        ];
        foreach ($domain_mail_routes as $url) {
            $domain_mail->addPost($url['0'], array(
                'action' => $url['1'],
            ))->setName($url['2']);
        }

        return $domain_mail;
    }

}
