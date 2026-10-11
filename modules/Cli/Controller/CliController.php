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
namespace Cli\Controller;

use Exception;
use FPanel\Common\Base;
use FPanel\Models\Servers;
use FPanel\Models\SystemSettings;
use Phalcon\Http\ResponseInterface;
use Phalcon\Mvc\Controller;

/**
 * Class CliController
 * @package Cli\Controller
 */
class CliController extends Controller
{
    protected $username;
    protected $password;
    protected $action;
    protected $module;
    protected $data;
    protected $post_data;

    /**
     * @return ResponseInterface|void
     * @throws Exception
     */
    public function initialize()
    {
        $this->view->disable();
        $post_data = $this->request->getRawBody();
        $data = json_decode($post_data, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            return $this->response->redirect("/");
        }

        if (!isset($data["username"]) || !isset($data["password"]) || !isset($data["action"]) || !isset($data["module"]) || !isset($data["data"])) {
            echo print_r("Missed data", true);
            exit(0);
        }

        $this->post_data = $data;
        $this->logging();
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->action = $data['action'];
        $this->module = $data['module'];
        $this->data = $data['data'];
        $client_addr = $this->request->getClientAddress();
        $client_addresses = $this->config->clients->address;

        if (!in_array($client_addr, (array)$client_addresses)) {
            return $this->response->redirect("/");
        }
        else {
            if ($client_addr == "127.0.0.1")
                $server = Servers::findFirst("type = 'main'");
            else
                $server = Servers::findFirst("ip_address = '$client_addr'");

            if (!$server) {
                throw new Exception("\n<span>Server: <b> $client_addr</b> Not found</span>\n");
            }
            $server_data = json_decode($this->crypto->bdecode($server->data), true);

            if (json_last_error() != JSON_ERROR_NONE) {
                return $this->response->redirect("/");
            }
            else {
                $cli_username = $server_data["key"];
                $cli_password = $server_data["iv"];
            }
        }

        if (file_exists(ROOTPATH . '/.migration')) {
            $content = file_get_contents(ROOTPATH . '/.migration');
            $ip_addr = trim($content);
            if ($ip_addr != $this->request->getClientAddress())
                return $this->response->redirect("/");
        }

        if (($this->username != $cli_username) || ($this->password != $cli_password)) {
            return $this->response->redirect("/");
        }

        $panel_data = SystemSettings::findFirst("key = 'system_data'")->toArray();
        $panel_settings = json_decode($panel_data['value'], true);
        define('PANEL_TYPE', trim($panel_settings['system_type']));
    }

    /**
     * @return bool|ResponseInterface|void
     */
    public function indexAction()
    {
        $backend = new Base();

        switch ($this->module) {
            case "list":
                $result = $backend->action($this->action)->data($this->data)->getList();
                return $this->response->setJsonContent($result)->send();
            case "changeip":
                break;
            case "backup":
                $backend->action($this->action)->data($this->data)->backupRestore();
                break;
            case "migration":
                $result = $backend->action($this->action)->data($this->data)->migration();
                return $this->response->setJsonContent($result)->send();
            case "statistics":
                $result = $backend->action($this->action)->statistics($this->data, $this->username, $this->password);
                return $this->response->setJsonContent($result)->send();
            case "update":
                $backend->action($this->action)->update($this->data);
                break;
            case "config":
                $result = $backend->action($this->action)->data($this->data)->getConfig();
                return $this->response->setJsonContent($result)->send();
            case "reset":
                $backend->reset();
                break;
            case "subdomain":
                $result = $backend->action($this->action)->subdomain($this->data);
                if (is_array($result)) {
                    return $this->response->setJsonContent($result)->send();
                }
                else {
                    echo $result;
                }
                break;
            case "mail":
                $result = $backend->action($this->action)->mail($this->data);
                if (is_array($result)) {
                    return $this->response->setJsonContent($result)->send();
                }
                else {
                    echo $result;
                }
                break;
            case "database":
                $result = $backend->action($this->action)->database($this->data);
                if (is_array($result)) {
                    return $this->response->setJsonContent($result)->send();
                }
                else {
                    echo $result;
                }
                break;
            case "mail-alias":
                $result = $backend->action($this->action)->mailAlias($this->data);
                if (is_array($result)) {
                    return $this->response->setJsonContent($result)->send();
                }
                else {
                    echo $result;
                }
                break;
            case "domain-alias":
                $result = $backend->action($this->action)->domainAlias($this->data);
                if (is_array($result)) {
                    return $this->response->setJsonContent($result)->send();
                }
                else {
                    echo $result;
                }
                break;
            default:
                echo "There is no setting any module";
                return false;
        }
    }

    private function logging()
    {
        $client_ip = $this->request->getClientAddress();
        $referrer = $this->request->getHTTPReferer();
        $url = $this->request->getURI();
        $method = $this->request->getMethod();
        $browser_agent = $this->request->getUserAgent();
        if ((strpos($browser_agent, 'bot') === false)
            && (strpos($browser_agent, 'spider') === false)
            && (strpos($browser_agent, 'agent') === false)) {
            foreach ($this->post_data as $key => $value) {
                if (strpos($key, "password") !== false) {
                    $this->post_data[$key] = "*********";
                }
                if (strpos($key, "secret") !== false) {
                    $this->post_data[$key] = "*********";
                }
            }

            $data = array(
                'client_address' => $client_ip,
                'referrer' => $referrer,
                'url' => $this->uri->encode($url),
                'method' => $method,
                'browser' => $browser_agent,
                'data' => $this->post_data
            );

            if (strpos($url, "favicon.ico") === false) {
                $this->log->info($data);
            }
        }
    }

}