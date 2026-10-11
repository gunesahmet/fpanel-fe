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
 */namespace Api\Controller;

use Exception;
use FPanel\Api\Api;
use FPanel\Auth\ApiAuth;
use FPanel\Environments\XMLParser;
use FPanel\Jwt\Exception\JwtException;
use Main\Controller\ControllerBase;
use Phalcon\Config\Adapter\Ini;

class ApiController extends ControllerBase
{
    /**
     * @var array
     */
    protected $error = [];

    /**
     * @var int
     */
    protected $error_code;

    /**
     * @var array
     */
    protected $request_data;

    /**
     * @var string
     */
    protected $request_type;

    /**
     * @var ApiAuth
     */
    protected $api_auth;

    /**
     * @var Api
     */
    protected $fapi;

    /**
     * init
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->view->disable();

        $request = $this->request->getRawBody();
        $config = new Ini(ROOTPATH . "/config/config.ini");
        if ($config->api->status == null) {
            $this->response->setStatusCode(404, "Not Found")->sendHeaders();
            exit(255);
        }

        if ($request == null) {
            $this->response->setStatusCode(400, "Missing request body")->sendHeaders();
            exit(255);
        }

        if ($this->env->isJson($request))
            $this->request_type = JSON;
        else if ($this->env->isXml($request))
            $this->request_type = XML;
        else {
            $this->response->setStatusCode(406, "Not Acceptable")->sendHeaders();
            exit(255);
        }

        switch($this->request_type) {
            case JSON:
                $this->request_data = json_decode($request,true);
                break;
            case XML:
                $this->request_data = json_decode(json_encode((array) simplexml_load_string($request)), 1);
                break;
            default:
                $this->error['data'] = "Invalid post data";
                break;
        }


        if (json_last_error() != JSON_ERROR_NONE) {
            $this->response->setStatusCode(406, "Not Acceptable")->sendHeaders();
            exit(255);
        }

        $this->api_auth = new ApiAuth();
        $this->fapi = new Api();
    }

    /**
     * @throws Exception
     */
    public function healthAction()
    {
        $result = [
            'status' => true,
            'message' => [
                "ping" => "pong"
            ],
        ];

        if ($this->request_type == XML) {
            $this->response->setHeader('Content-Type', 'application/xml');
            $xml = XMLParser::createXML('result', $result);
            print_r($xml->saveXML());
            exit;
        } else {
            $result = array('result' => $result);
            $this->response->setHeader('Content-Type', 'application/json');
            return $this->response->setJsonContent($result)->send();
        }
    }

    /**
     * @return false|void
     * @throws JwtException
     * @throws Exception
     */
    public function loginAction()
    {
        $request = $this->request->getRawBody();
        $this->log->apiInfo([
            'client_address' => $this->request->getClientAddress(),
            'request' => $request,
        ]);

        $parameters = ['apiusername', 'apipassword', 'username', 'password'];

        foreach ($parameters as $api_part) {
            if (empty($this->request_data[$api_part])) {
                $this->error[$api_part] = "This param is required";
                return false;
            }
        }

        if ($this->request_data["apiusername"] != $this->config->api->user) {
            $this->error_code = 9001;
            return false;
        }

        if ($this->request_data["apipassword"] != trim($this->crypto->bdecode($this->config->api->pass))) {
            $this->error_code = 9002;
            return false;
        }
        $api_auth = new ApiAuth();

        $token = $api_auth->check([
            "username" => $this->request_data["username"],
            "password" => $this->request_data["password"],
        ]);

        $this->log->apiInfo([
            'request_id' => $this->request_id,
            'client_address' => $this->request->getClientAddress(),
            'apiusername' => $this->request_data["apiusername"],
            'apipassword' => $this->request_data["apipassword"],
            'username' => $this->request_data["username"],
            'password' => "********",
            'process' => $this->request_data["process"] ?? "login",
        ]);


        if ($token === false) {
            $this->error["username"] = $api_auth->message;
        }

        $result = array(
            'status' => true,
            'message' => "Operation completed successful",
            "token" => $token
        );


        if ($token === false) {
            $result = array(
                'status' => false,
                'code' => $this->error_code,
                'message' => $this->error,
                "token" => null
            );
        }

        if ($this->request_type == XML) {
            $this->response->setHeader('Content-Type', 'application/xml');
            $xml = XMLParser::createXML('result', $result);
            print_r($xml->saveXML());
            exit;
        } else {
            $result = array('result' => $result);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setJsonContent($result)->send();
        }
    }



    protected function checkAuth()
    {
        $status = $this->api_auth->checkToken();

        if (!$status) {
            $result = array(
                'status' => false,
                'message' => [
                    "token" => "Authorization failed"
                ],
            );
            $this->returnResult($result);
        }
    }

    protected function response($response)
    {
        if ($response == "Success") {
            $result = array(
                'status' => true,
                'message' => "Operation completed successful",
            );
            $this->returnResult($result);
        }
        else {
            if ((!empty($response['status'])) && ($response['status']) == "1") {
                unset($response['status']);
                $result['status'] = true;
            } else {
                $result['status'] = false;
            }
            $result['message'] = $response;
            $this->returnResult($result);
            exit;
        }
    }

    private function returnResult($result): void
    {
        if ($this->request_type == XML) {
            $this->response->setHeader('Content-Type', 'application/xml');
            $xml = XMLParser::createXML('result', $result);
            print_r($xml->saveXML());
        } else {
            $result = array('result' => $result);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setJsonContent($result)->send();
        }
        exit(0);
    }
}