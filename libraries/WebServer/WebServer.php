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
 */namespace FPanel\WebServer;

use FPanel\Library\Library;
use FPanel\Models\WebServer as SystemWebServer;
use FPanel\Process\WebServer as Webservermng;

/**
 * Class WebServer
 * @package FPanel\WebServer
 */
class WebServer extends Library
{
    use Apache, Nginx, IIS;

    public function updateGeneralSettings($restore = false): bool
    {
        if($restore == false) {
            $required = array();
            if($this->getPost("php_status") == true) {
                $required[] = array(
                    'field' => 'php_extension',
                    'item' => $this->getPost("php_extension"),
                    'message' => __('PHP extension is required')
                );
            }
            if($this->getPost("perl_status") == true) {
                $required[] = array(
                    'field' => 'perl_extension',
                    'item' => $this->getPost("perl_extension"),
                    'message' => __('Perl extension is required')
                );
            }
            if($this->getPost("python_status") == true) {
                $required[] = array(
                    'field' => 'python_extension',
                    'item' => $this->getPost("python_extension"),
                    'message' => __('Python extension is required')
                );
            }

            if(!empty($required))
                $this->validation->Required($required);

            $validation = $this->validation->Validate();

            if($validation == false) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }

        }

        $php_support = "1";
        if($this->getPost("php_status") == false)
            $php_support = "0";

        $perl_support = "1";
        if($this->getPost("perl_status") == false)
            $perl_support = "0";

        $python_support = "1";
        if($this->getPost("python_status") == false)
            $python_support = "0";


        $php_56 = "1";
        if($this->getPost("php_56") == false)
            $php_56 = "0";

        $php_72 = "1";
        if($this->getPost("php_72") == false)
            $php_72 = "0";

        $php_74 = "1";
        if($this->getPost("php_74") == false)
            $php_74 = "0";

        $server_id = $this->getPost("server_id");
        $system_web_server = SystemWebServer::findFirst("server_id = '$server_id'");
        if($system_web_server) {
            $system_web_server->assign(array(
                'php_status'    => $php_support,
                'php_56'        => $php_56,
                'php_72'        => $php_72,
                'php_74'        => $php_74,
                'perl_status'   => $perl_support,
                'python_status' => $python_support,
            ));
            if(!$system_web_server->update()) {
                foreach ($system_web_server->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "WebServer->updateGeneralSettings",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $this->error_messages['system'] = __('Module not found');
            return false;
        }
        return true;
    }

    public function extraModList($server_id, $api = false, $server_os_type = LINUX)
    {
        $web_server = new Webservermng();
        $server_link = "";
        $web_server->setServerId($server_id);
        if($server_id != "1") {
            $server_link = "/server/$server_id";
        }

        $modules_list = $web_server->listModules();
        if($server_os_type == WINDOWS) {
//            $this->debug->debug($modules_list, false);
            $data = json_decode(json_encode((array) simplexml_load_string($modules_list)), true);
        }
        else {
            $data = json_decode($modules_list, true);
        }
        if (json_last_error() != JSON_ERROR_NONE) {
            return false;
        }

        $mod_list = array();

        if($api == false) {
            if($server_os_type == WINDOWS) {
                foreach($data as $value) {
                    if($value['name'] != null) {
                        $name = $value['name'];
                        $image = $value['image'];
                        $preCondition = $value['preCondition'];

                        if($image == null) {
                            $image = "";
                        }
                        if($preCondition == null) {
                            $preCondition = "";
                        }

                        $mod_list[] = array(
                            '0' => $name,
                            '1' => $image,
                            '2' => $preCondition,
                        );
                    }
                }
            }
            else {
                foreach($data as $key => $value) {

                    $module = $key;

                    if(($key == "perl_handler") || ($key == "php5_handler") || ($key == "python_handler"))
                        continue;


                    ///editmodule/{module_name}/{process}
                    if($value == "on") {
                        $status = '<span class="label label-success"> '. __('On'). '</span>';
                        $action_button = '<a data-url="'.$this->uri->url_encrypt('webserver/editmodule/'.$key.'/deactivate'.$server_link).'" data-id="confirm" class="text-danger">'. __('De Activate') .'</a>';
                    }
                    else if($value == "off") {
                        $status = '<span class="label label-warning"> '. __('Off'). '</span>';
                        $action_button = '<a data-url="'.$this->uri->url_encrypt('webserver/editmodule/'.$key.'/activate'.$server_link).'" data-id="confirm" class="text-success">'. __('Activate') .'</a>';
                    }
                    else {
                        $status = '<span class="label label-danger"> '. __('Not Installed'). '</span>';
                        $action_button = '';
                    }
                    $mod_list[] = array(
                        '0' => $module,
                        '1' => $status,
                        '2' => $action_button
                    );
                }
            }

            return array (
                'iTotalRecords'        => count($mod_list),
                'iTotalDisplayRecords' => count($mod_list),
                'aaData'               => $mod_list,
            );
        }
        else {
            return $data;
        }

    }

    public function getWebServerSettings($server_id)
    {
        $wersrvmng = new Webservermng();
        $wersrvmng->setServerId($server_id);
        $data = $wersrvmng->serverSettings();
        $result = json_decode($data, true);

        if(json_last_error() == JSON_ERROR_NONE) {
            return $result;
        }

        return $data;
    }

    public function updateWebServerSettings($server_id): bool
    {
        switch ($this->getPost("webserver_type")) {
            case "apache":
                $status = $this->saveApache($server_id);
                break;
            case "nginx":
                $status = $this->saveNginx($server_id);
                break;
            case "iis":
                $status = $this->saveIis($server_id);
                break;
            default:
                $status = false;
                $this->web_error_messages["server_type"] = __("Invalid server type");
        }
        $this->error_messages = $this->web_error_messages;
        return $status;
    }

    public function mimeTypeList($server_id, $api, $panel_os_type)
    {
        $web_server = new Webservermng();

        $server_link = "";
        $web_server->setServerId($server_id);
        if($server_id != "1") {
            $server_link = "/server/$server_id";
        }
        $data = $web_server->listMimeTypes();

        if($panel_os_type == WINDOWS) {
            $xml = simplexml_load_string(base64_decode($data), "SimpleXMLElement", LIBXML_NOCDATA);
            $data = json_encode($xml);
            $data = json_decode($data,true);
            $data = array(
                'mimetypes' => $data
            );
        }
        else {
            $data = json_decode(base64_decode($data),true);
        }

        $aaData = array();

        if($api == false) {
            if($panel_os_type == WINDOWS) {
                foreach($data['mimetypes'] as $value) {
                    if($value['fileExtension'] != null) {
                        $extension = $value['fileExtension'];
                        $octet = $value['mimeType'];
                        $action = '<a data-url="'. $this->uri->url_encrypt('webserver/removemime/'.base64_encode($extension).$server_link).'" data-id="confirm" class="text-danger">'. __('Delete') .'</a>';

                        if($octet == null) {
                            $octet = "";
                        }

                        $aaData[] = array(
                            '0' => $extension,
                            '1' => $octet,
                            '2' => $action
                        );
                    }
                }
            }
            else {
                foreach($data as $key => $value) {
                    if($value != null) {
                        $extension = $value;
                        $octet = $key;
                        $action = '<a data-url="'. $this->uri->url_encrypt('webserver/removemime/'.base64_encode($extension).$server_link).'" data-id="confirm" class="text-danger">'. __('Delete') .'</a>';

                        if($octet == null) {
                            $octet = "";
                        }

                        $aaData[] = array(
                            '0' => $extension,
                            '1' => $octet,
                            '2' => $action
                        );
                    }
                }
            }
            return array(
                'iTotalRecords'        => count($aaData),
                'iTotalDisplayRecords' => count($aaData),
                'aaData'               => $aaData,
            );
        }
        else {
            return $data;
        }

    }

    public function addMime($server_id): bool
    {
        $this->validation->Required(array(
            array(
                'field' => 'file_extension',
                'item' => $this->getPost("file_extension"),
                'message' => __('File Extension is required')
            ),
            array(
                'field' => 'mimetype_octet',
                'item' => $this->getPost("mimetype_octet"),
                'message' => __('File Application Type is required')
            ),
        ));

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }


        $process = new Webservermng();
        $process->setServerId($server_id);
        $params = array(
            'file-ext' => $this->getPost("file_extension"),
            'file-ext-octet' => $this->getPost("mimetype_octet"),
        );
        $process->addMimeType($params);

        return true;

    }

    public function removeMime($server_id): bool
    {

        if($this->getPost("file_extension") == null) {
            $this->error_messages['file_extension'] = __('File Extension is required');
            return false;
        }

        $process = new Webservermng();
        $process->setServerId($server_id);

        $params = array(
            'file-ext' => $this->getPost("file_extension"),
        );
        $process->removeMimeType($params);

        return true;

    }

    public function updateModule($module, $action, $server_id): bool
    {
        $websrvmng = new Webservermng();
        $websrvmng->setServerId($server_id);
        $params = array(
            $module => $action,
        );

        $websrvmng->updateModules($params);
        return true;
    }

}