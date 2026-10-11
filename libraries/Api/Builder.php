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
 */namespace FPanel\Api;

use FPanel\Api\Client\Client;
use FPanel\Api\Dashboard\Dashboard;
use FPanel\Api\Domain\Database;
use FPanel\Api\Domain\DNS;
use FPanel\Api\Domain\Domain;
use FPanel\Api\Domain\DomainAlias;
use FPanel\Api\Domain\Mail;
use FPanel\Api\Domain\Prodir;
use FPanel\Api\Domain\Resource;
use FPanel\Api\Domain\Subdomain;
use FPanel\Api\Domain\Task;
use FPanel\Api\System\DatabaseServer;
use FPanel\Api\System\DnsServer;
use FPanel\Api\System\MailServer;
use FPanel\Api\System\Network;
use FPanel\Api\System\Servers;
use FPanel\Api\System\WebServer;
use FPanel\Api\Templates\Templates;
use FPanel\Models\ClientStatistics;
use FPanel\Models\Domains;
use FPanel\Models\Users;
use FPanel\Process\Admin;
use Phalcon\Mvc\User\Component;

class Builder extends Component
{
    private $client_id;
    private $client_type;
    private $request_data = array();

    public function __construct($account_id, $account_type, $request_data)
    {
        $this->client_id = $account_id;
        $this->client_type = $account_type;
        $this->request_data = $request_data;
    }

    public function builder($module)
    {

        switch($module["name"]) {
            case "domain":
                $result = $this->domain($module["action"]);
                break;
            case "domain_dns":
                $result = $this->domainDns($module["action"]);
                break;
            case "subdomain":
                $result = $this->subdomain($module["action"]);
                break;
            case "domain_database":
                $result = $this->domainDatabase($module["action"]);
                break;
            case "domain_alias":
                $result = $this->domainAlias($module["action"]);
                break;
            case "protect_dir":
                $result = $this->protectDirectory($module["action"]);
                break;
            case "domain_mail":
                $result = $this->domainMail($module["action"]);
                break;
            case "domain_task":
                $result = $this->domainTask($module["action"]);
                break;

            case "system":
                $result = $this->system($module["action"], $module["sub"]);
                break;


            case "client":
                $result = $this->client($module["action"]);
                break;
            case "serviceplan":
                $result = $this->template($module["action"]); // TODO: Test et api dokumanina kaydet
                break;

            default:
                $result = array("process" => 'Processs is required');
                break;
        }

        return $result;
    }

    private function client($process)
    {
        $client = new Client();
        switch($process) {
            case "create":
                $total_clients = ClientStatistics::count("id > 0");
                $result = $client->clientCreate($this->request_data, $this->client_id);
                break;
            case "update":
                $result = $client->clientUpdate($this->request_data, $this->client_id);
                break;
            case "delete":
                $result = $client->clientDelete($this->request_data, $this->client_id);
                break;
            case "list":
                $result = $client->clientList($this->request_data, $this->client_id);
                break;
            default:
                $result = array('Process required');
                break;
        }
        return $result;
    }

    private function domain($process)
    {
        $domain = new Domain();
        $resource = new Resource();
        switch($process) {
            case "list":
                $result = $domain->domainList($this->client_id);
                break;
            case "create":
                $result = $domain->domainCreate($this->request_data, $this->client_id);
                break;
            case "delete":
                $result = $domain->domainDelete($this->request_data, $this->client_id);
                break;
            case "update":
                $result = $domain->domainUpdate($this->request_data, $this->client_id);
                break;
            case "webhosting":
                $result = $domain->webHostingSettings($this->request_data, $this->client_id);
                break;
            case "resource":
                $result = $resource->show($this->request_data, $this->client_id);
                break;
            default:
                $result = array("process" => 'Processs is required');
                break;
        }
        return $result;
    }

    private function subdomain($process)
    {
        $subdomains = new Subdomain();
        return $subdomains->subdomains($process, $this->request_data, $this->client_id);
    }

    private function domainDatabase($process)
    {
        $databases = new Database();
        return $databases->database($process, $this->request_data, $this->client_id);
    }

    private function domainDns($process)
    {
        $dns = new DNS();
        return $dns->dns($process, $this->request_data, $this->client_id);
    }

    private function domainAlias($process)
    {
        $domain_alias = new DomainAlias();
        return $domain_alias->alias($process, $this->request_data, $this->client_id);
    }

    private function protectDirectory($process)
    {
        $prodir = new Prodir();
        return $prodir->directory($process, $this->request_data, $this->client_id);
    }

    private function domainMail($process)
    {
        $mail = new Mail();
        //@TODO: Mail settings update yazılacak
        return $mail->mail($process, $this->request_data, $this->client_id);
    }

    private function domainTask($process)
    {
        $task = new Task();
        return $task->tasks($process, $this->request_data, $this->client_id);
    }

    private function system($action, $sub)
    {
        if(!$this->checkAdmin($this->client_id)) {
            return array(
                'username' => 'User not found'
            );
        }

        $webserver = new WebServer();
        $database = new DatabaseServer();
        $mail = new MailServer();
        $dns = new DnsServer();
        $network = new Network();
        $servers = new Servers();
        switch($action) {
            case "server":
                $result = $servers->init($sub, $this->request_data);
                break;
            case "webserver":
                $result = $webserver->init($sub, $this->request_data);
                break;
            case "database":
                $result = $database->init($sub, $this->request_data); // TODO: Test et api dokumanina kaydet
                break;
            case "mail":
                $result = $mail->init($sub, $this->request_data); // TODO: Test et api dokumanina kaydet
                break;
            case "dns":
                $result = $dns->init($sub, $this->request_data); // TODO: Test et api dokumanina kaydet
                break;
            case "network":
                $result = $network->init($sub, $this->request_data); // TODO: Test et api dokumanina kaydet
                break;
            default:
                $result = array("process" => 'Processs is required');
                break;
        }
        return $result;

    }

    private function checkAdmin($client_id): bool
    {
        $user = Users::findFirstById($client_id);
        if($user) {
            if(($user->type != ROOT_USER) && ($user->type != ADMIN_USER)) {
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    private function template($process)
    {
        $templates = new Templates();
        return $templates->serviceplans($process, $this->request_data, $this->client_id);
    }
}