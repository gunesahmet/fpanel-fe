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
 */namespace FPanel\Api\System;

use FPanel\Models\Servers;
use Phalcon\Mvc\User\Component;

class WebServer extends Component
{
    public function init($process, $params)
    {
        switch($process[2]) {
            case "general-settings":
                return $this->general_settings($params);
            case "modules":
                return $this->modules($process, $params);
            case "mime":
                return $this->mime($process, $params);
            case "webserver-settings":
                return $this->webserverSettings($params);
            case "php":
                return $this->php($process); // TODO: Test et api dokumanina kaydet
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function general_settings($params)
    {
        $error_messages = array();
        $post_data = array(
            'php_status' => $params['php'],
            'php_extension' => $params['php_extension'],
            'php_56' => $params['php_56'],
            'perl_status' => $params['perl'],
            'perl_extension' => $params['perl_extension'],
            'python_status' => $params['python'],
            'python_extension' => $params['python_extension'],
            'server_id' => "1"
        );
        $web_server = new \FPanel\WebServer\WebServer();
        $web_server->setPosts($post_data);
        $status = $web_server->updateGeneralSettings();
        if($status == true) {
            return "Success";
        }
        else {
            foreach ($web_server->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function modules($process, $params)
    {
       switch($process[3]) {
           case "list":
               return $this->moduleList($params);
           case "status":
               return $this->moduleChangeStatus($params);
           default:
               return array("process" => 'Processs is required');
       }
    }

    public function moduleList($params)
    {
        $server = Servers::findFirst("name = '$params[server_name]'");
        if(!$server) {
            return [
                "server_name" => __("Server not found")
            ];
        }
        $web_server = new \FPanel\WebServer\WebServer();
        $data = $web_server->extraModList($server->id,true, $server->os);
        if(!empty($data["status"])) {
            $data['web_status'] = $data['status'];
        }
        $data['status'] = true;

        return $data;
    }

    public function moduleChangeStatus($params)
    {
        $error_messages = array();
        $web_server = new \FPanel\WebServer\WebServer();
        if((!isset($params['module'])) || (!isset($params['action'])) || (!isset($params['server_name']))) {
            return [
                "data" => __("Missing data")
            ];
        }
        $server = Servers::findFirst("name = '$params[server_name]'");
        if(!$server) {
            return [
                "server_name" => __("Server not found")
            ];
        }
        $status = $web_server->updateModule($params['module'], $params['action'], $server->id);

        if ($status == true) {
            return "Success";
        }
        else {
            foreach ($web_server->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function mime($process, $params)
    {
        switch($process[3]) {
            case "list":
                return $this->mimeList($params);
            case "add":
                return $this->addMime($params);
            case "remove":
                return $this->removeMime($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function mimeList($params)
    {
        $web_server = new \FPanel\WebServer\WebServer();

        if(!isset($params['server_name'])) {
            return [
                "data" => __("Missing data")
            ];
        }
        $server = Servers::findFirst("name = '$params[server_name]'");
        if(!$server) {
            return [
                "server_name" => __("Server not found")
            ];
        }
        $data = $web_server->mimeTypeList($server->id,true, $server->os);

        $data['status'] = true;
        return $data;
    }

    public function addMime($params)
    {
        $web_server = new \FPanel\WebServer\WebServer();
        if((!isset($params['mimetype_octet'])) || (!isset($params['file_extension'])) || (!isset($params['server_name']))) {
            return [
                "server_name" => __("Missing data")
            ];
        }
        $server = Servers::findFirst("name = '$params[server_name]'");
        if(!$server) {
            return [
                "server_name" => __("Server not found")
            ];
        }
        $post_data = array(
            'file_extension' => $params['file_extension'],
            'mimetype_octet' => $params['mimetype_octet'],
            'server_id' => $server->id
        );
        $web_server->setPosts($post_data);
        $status = $web_server->addMime($server->id);

        if ($status == true) {
            return "Success";
        }
        else {
            $error_messages = array();
            foreach ($web_server->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function removeMime($params)
    {
        $web_server = new \FPanel\WebServer\WebServer();
        if((!isset($params['file_extension'])) || (!isset($params['server_name']))) {
            return [
                "server_name" => __("Missing data")
            ];
        }
        $server = Servers::findFirst("name = '$params[server_name]'");
        if(!$server) {
            return [
                "server_name" => __("Server not found")
            ];
        }
        $web_server->setPosts(array(
            'file_extension'        => $params['file_extension'],
            'server_id' => $server->id
        ));
        $status = $web_server->removeMime($server->id);

        if ($status == true) {
            return "Success";
        }
        else {
            $error_messages = array();
            foreach ($web_server->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function webserverSettings($params)
    {
        $web_server = new \FPanel\WebServer\WebServer();
        if((!isset($params['server_name']))) {
            return [
                "server_name" => __("Missing data")
            ];
        }
        $server = Servers::findFirst("name = '$params[server_name]'");
        if(!$server) {
            return [
                "server_name" => __("Server not found")
            ];
        }

        if($server->os == WINDOWS) {
            $post_data = array(
                'ftp_messages' => (isset($params['ftp_messages']) ? $params['ftp_messages'] : ""),
                'hidden_segments' => (isset($params['hidden_segments']) ? $params['hidden_segments'] : ""),
                'dynamic_compression' => (isset($params['dynamic_compression']) ? $params['dynamic_compression'] : ""),
                'static_compression' => (isset($params['static_compression']) ? $params['static_compression'] : ""),
                'compress_file_size_status' => (isset($params['compress_file_size_status']) ? $params['compress_file_size_status'] : ""),
                'compress_file_size' => (isset($params['compress_file_size']) ? $params['compress_file_size'] : ""),
                'cache_directory' => (isset($params['cache_directory']) ? $params['cache_directory'] : ""),
                'per_application_pool_status' => (isset($params['per_application_pool_status']) ? $params['per_application_pool_status'] : ""),
                'per_application_pool' => (isset($params['per_application_pool']) ? $params['per_application_pool'] : ""),
            );
        }
        else {
            $post_data = array(
                'webserver_type' => (isset($params['webserver_type']) ? $params['webserver_type'] : ""),

                // For Apache
                'keep_alive' => (isset($params['keep_alive']) ? $params['keep_alive'] : "false"),
                'keepalive_timeout' => (isset($params['keepalive_timeout']) ? $params['keepalive_timeout'] : ""),
                'timeout' => (isset($params['timeout']) ? $params['timeout'] : ""),
                'max_keepalive_req' => (isset($params['max_keepalive_req']) ? $params['max_keepalive_req'] : ""),
                'start_server' => (isset($params['start_server']) ? $params['start_server'] : ""),
                'min_spare_server' => (isset($params['min_spare_server']) ? $params['min_spare_server'] : ""),
                'max_client' => (isset($params['max_client']) ? $params['max_client'] : ""),
                'max_req_per_child' => (isset($params['max_req_per_child']) ? $params['max_req_per_child'] : ""),

                // For NGINX
                'worker_processes' => (isset($params['worker_processes']) ? $params['worker_processes'] : ""),
                'worker_connections' => (isset($params['worker_connections']) ? $params['worker_connections'] : ""),
                'multi_accept' => (isset($params['multi_accept']) ? $params['multi_accept'] : ""),
                'sendfile' => (isset($params['sendfile']) ? $params['sendfile'] : ""),
                'tcp_nopush' => (isset($params['tcp_nopush']) ? $params['tcp_nopush'] : ""),
                'tcp_nodelay' => (isset($params['tcp_nodelay']) ? $params['tcp_nodelay'] : ""),
                'types_hash_max_size' => (isset($params['types_hash_max_size']) ? $params['types_hash_max_size'] : ""),
                'server_tokens' => (isset($params['server_tokens']) ? $params['server_tokens'] : ""),
                'server_names_hash_bucket_size' => (isset($params['server_names_hash_bucket_size']) ? $params['server_names_hash_bucket_size'] : ""),
                'server_name_in_redirect' => (isset($params['server_name_in_redirect']) ? $params['server_name_in_redirect'] : ""),
                'gzip' => (isset($params['gzip']) ? $params['gzip'] : ""),
                'gzip_disable' => (isset($params['gzip_disable']) ? $params['gzip_disable'] : ""),
                'gzip_vary' => (isset($params['gzip_vary']) ? $params['gzip_vary'] : ""),
                'gzip_proxied' => (isset($params['gzip_proxied']) ? $params['gzip_proxied'] : ""),
                'gzip_comp_level' => (isset($params['gzip_comp_level']) ? $params['gzip_comp_level'] : ""),
                'gzip_buffers' => (isset($params['gzip_buffers']) ? $params['gzip_buffers'] : ""),
                'gzip_http_version' => (isset($params['gzip_http_version']) ? $params['gzip_http_version'] : ""),
                'gzip_types' => (isset($params['gzip_types']) ? $params['gzip_types'] : ""),
            );
        }

        $web_server->setPosts($post_data);
        // Server type gonder
        $status = $web_server->updateWebServerSettings($server->id);
        if ($status == true) {
            return "Success";
        }
        else {
            $error_messages = array();
            foreach ($web_server->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
    }

    public function php($process): array
    {
        switch($process[3]) {
            case "update":
            case "list":
                break;
            default:
                return array("process" => 'Processs is required');
        }
        return array("process" => 'Processs is required');
    }
}