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
 */namespace FPanel\Domain;

use FPanel\Library\Library;
use FPanel\Models\Domains;
use FPanel\Process\Vhost;

class AspNet extends Library
{
    public function getAspNetSettingsDomain($domain_name, $domain_id)
    {
        $domains = Domains::findFirstById($domain_id);
        if($domains) {
            $vhostmng = new Vhost();
            $vhostmng->setServerId($domains->web_server_id);
            $params = array(
                'domain' => $domain_name,
                'setting-type' => "aspnet"
            );
            $aspnet_settings = $vhostmng->aspnetSettings($params);
            return json_decode(json_encode((array) simplexml_load_string($aspnet_settings)), 1);
        }
        return [];
    }

    public function getCustomErrorList($data, $domain_id = null): array
    {
        $conn_strings = $data['customErrors']['customErrorList'];
        $list = array();
        if(!empty($conn_strings)) {
            foreach($conn_strings as $value) {
                if($value['redirect'] == null) {
                    $redirect = "";
                }
                else {
                    $redirect = $value['redirect'];
                }
                if($domain_id == null)
                    $action = '<a data-id="confirm" data-url="' . $this->uri->url_encrypt("aspnet/deleterr/". base64_encode($value['statusCode'])) . '" data-toggle="tooltip" data-title="'. __('Delete').'" data-placement="top" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
                else
                    $action = '<a data-id="confirm" data-url="' . $this->uri->url_encrypt("aspnet/deleterr/". base64_encode($value['statusCode']).'/domain/'.$domain_id) . '" data-toggle="tooltip" data-title="'. __('Delete').'" data-placement="top" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';


                $list[] = array(
                    '0' => $value['statusCode'],
                    '1' => $redirect,
                    '2' => $action,
                );
            }
        }
        return $list;
    }

    public function getConnStringList($data, $domain_id = null): array
    {
        $conn_strings = $data['connectionStrings'];

        $list = array();
        foreach($conn_strings as $value) {
            if($value['connectionString'] == null) {
                $connectionString = "";
            }
            else {
                $connectionString = $value['connectionString'];
            }
            if($value['providerName'] == null) {
                $providerName = "";
            }
            else {
                $providerName = $value['providerName'];
            }
            if($domain_id == null)
                $action = '<a data-id="confirm" data-url="' . $this->uri->url_encrypt('aspnet/deleteconn/'. base64_encode($value['name'])) . '" data-toggle="tooltip" data-title="'. __('Delete').'" data-placement="top" id="modals-bootbox-confirm" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';
            else
                $action = '<a data-id="confirm" data-url="' . $this->uri->url_encrypt('aspnet/deleteconn/'. base64_encode($value['name']).'/domain/'.$domain_id) . '" data-toggle="tooltip" data-title="'. __('Delete').'" data-placement="top" id="modals-bootbox-confirm" class="btn btn-xs btn-danger"><i class="fa fa-times"></i></a>';

            $list[] = array(
                '0' => $value['name'],
                '1' => $connectionString,
                '2' => $providerName,
                '3' => $action
            );
        }


        return $list;
    }

    public function saveSettings($domain_id): bool
    {
        $domains = Domains::findFirst($domain_id);
        $vhostmng = new Vhost();
        $vhostmng->setServerId($domains->web_server_id);
        $params = array(
            'domain' => $this->getPost("domain_name"),
            'request-encode' => $this->getPost("requestEncoding"),
            'response-encode' => $this->getPost("responseEncoding"),
            'file-encode' => $this->getPost("fileEncoding"),
            'culture' => $this->getPost("culture"),
            'ui-culture' => $this->getPost("ui_culture"),
            'con-string-name' => $this->getPost("connStrName"),
            'con-string' => $this->getPost("connectionString"),
            'con-string-prov' => $this->getPost("connectionStringProvider"),
//            'default-lang' => "",
//            'com-debug' => "",
//            'trust-level' => "",
//            'sess-auth' => "",
//            'sess-timeout' => "",
//            'status-code' => "",
//            'status-url' => "",
//            'error-mode' => "",
        );

        switch ($this->getPost("customErrMode")) {
            case "On":
                $custom_error_mode = "On";
                break;
            case "RemoteOnly":
                $custom_error_mode = "RemoteOnly";
                break;
            default:
                $custom_error_mode = "Off";
                break;
        }
        $params["error-mode"] = $custom_error_mode;

        switch($this->getPost("defaultLang")) {
            case "c#":
            case "vb":
                $defaultLang = $this->getPost("defaultLang");
                break;
            default:
                $this->error_messages['defaultLang'] = __('Please select Default Language');
                return false;
        }
        $params["default-lang"] = $defaultLang;

        if($this->getPost("switchDebug") != ON) {
            $switch_debug = "false";
        }
        else {
            $switch_debug = "true";
        }
        $params["com-debug"] = $switch_debug;

        if($this->getPost("is_admin")) {
            switch($this->getPost("trust_level")) {
                case "Full":
                case "High":
                case "Medium":
                case "Low":
                case "Minimal":
                    $trust_level = $this->getPost("trust_level");
                    break;
                default:
                    $this->error_messages['trust_level'] = __('Please select Trust Level');
                    return false;
            }
            $params["trust-level"] = $trust_level;
        }

        switch($this->getPost("auth_mode")) {
            case "0":
            case "1":
            case "2":
            case "3":
            case "4":
                $auth_mode = $this->getPost("auth_mode");
                break;
            default:
                $this->error_messages['auth_mode'] = __('Please select Session Auth Mode');
                return false;
        }
        $params["sess-auth"] = $auth_mode;

        if(!is_numeric($this->getPost("sessionTimeOut"))) {
            $this->error_messages['sessionTimeOut'] = __('Please enter valid time');
            return false;
        }
        else {
            if($this->getPost("sessionTimeOut") > 1439) {
                $this->error_messages['sessionTimeOut'] = __('Session timeout can not be higher than 24 hours max value (1439 minutes)');
                return false;
            }
            $session_time_out = floor($this->getPost("sessionTimeOut") / 60).":".($this->getPost("sessionTimeOut") % 60);
            $params["sess-timeout"] = $session_time_out;
        }

        if($this->getPost("customErrCode") != null) {
            if(!is_numeric($this->getPost("customErrCode"))) {
                $this->error_messages['customErrCode'] = __('Please enter valid Status Code');
                return false;
            }
            $params["status-code"] = $this->getPost("customErrCode");
            $params["status-url"] = $this->getPost("customErrUrl");
        }
        $vhostmng->saveAspNetSettings($params);
        return true;
    }

    public function deleteConnectionString($connection_string, $domain_name, $domain_id)
    {
        $domains = Domains::findFirst($domain_id);
        $vhostmng = new Vhost();
        $vhostmng->setServerId($domains->web_server_id);
        $params = array(
            'con-string-name' => $connection_string,
            'domain' => $domain_name
        );

        return $vhostmng->removeConnStr($params);
    }

    public function deleteCustomError($status_code, $domain_name, $domain_id)
    {
        $domains = Domains::findFirst($domain_id);
        $vhostmng = new Vhost();
        $vhostmng->setServerId($domains->web_server_id);
        $params = array(
            'status-code' => $status_code,
            'domain' => $domain_name
        );

        return $vhostmng->removeCustomError($params);
    }

}