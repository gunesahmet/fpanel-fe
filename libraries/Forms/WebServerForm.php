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
 */namespace FPanel\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class WebServerForm
 * @package FPanel\Forms
 */
class WebServerForm extends Form
{
    protected $perl_status = false;
    protected $python_status = false;

    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        switch($options["page"]) {
            case "default":
                $this->mainModules($options);
                break;
            case "mime":
                $this->mimeTypes();
                break;
            case "settings":
                $this->settings($options);
                break;
        }
    }

    /**
     * @param null $options
     */
    private function mainModules($options = null)
    {
        $installed_pkg = $options["installed_pkg"];
        $main_modules = $options["main_modules"];

        // SSL Support
        $ssl_support = new Check("ssl_support", array(
            "id" => "ssl_support",
            "name" => "ssl_support",
            "checked" => "checked",
            "disabled" => "disabled"
        ));

        $php_support = new Check("php_support", array(
            "id" => "php_support",
            "name" => "php_support",
        ));

        $php_extension = new Text("php_extension", array(
            "id" => "php_extension",
            "name" => "php_extension",
        ));

        $perl_support = new Check("perl_support", array(
            "id" => "perl_support",
            "name" => "perl_support",
        ));

        $perl_extension = new Text("perl_extension", array(
            "id" => "perl_extension",
            "name" => "perl_extension",
        ));

        $python_support = new Check("python_support", array(
            "id" => "python_support",
            "name" => "python_support",
        ));

        $python_extension = new Text("python_extension", array(
            "id" => "python_extension",
            "name" => "python_extension",
        ));

        $mod_rewrite_support = new Check("rewrite_support", array(
            "id" => "rewrite_support",
            "name" => "rewrite_support",
            "checked" => "checked",
        ));

        $this->pkgStatus($installed_pkg);

        if($main_modules["php_status"] == ACTIVE) {
            $php_support->setAttribute("checked","checked");
            $php_extension->setAttribute("value", $main_modules["php_extension"]);
        }
        else {
            $php_extension->setAttribute("disabled","disabled");
        }

        if($main_modules["perl_status"] == ACTIVE) {
            $perl_support->setAttribute("checked","checked");
            $perl_extension->setAttribute("value", $main_modules["perl_extension"]);
        }
        else {
            $perl_extension->setAttribute("disabled","disabled");
        }

        if($main_modules["python_status"] == ACTIVE) {
            $python_support->setAttribute("checked","checked");
            $python_extension->setAttribute("value", $main_modules["python_extension"]);
        }
        else {
            $python_extension->setAttribute("disabled","disabled");
        }

        $this->add($php_support);
        $this->add($php_extension);
        $this->add($python_support);
        $this->add($python_extension);
        $this->add($perl_support);
        $this->add($perl_extension);

        $this->add($mod_rewrite_support);
        $this->add($ssl_support);

    }

    private function settingsElementsIis($options) {

        $dynamic_compression = new Check("dynamic_compression", array(
            "id" => "dynamic_compression",
            "name" => "dynamic_compression",
        ));

        $static_compression = new Check("static_compression", array(
            "id" => "static_compression",
            "name" => "static_compression",
        ));

        $compress_file_size = new Text("compress_file_size", array(
            "id" => "compress_file_size",
            "name" => "compress_file_size",
        ));

        $per_application_pool_status = new Check("per_application_pool_status", array(
            "id" => "per_application_pool_status",
            "name" => "per_application_pool_status",
        ));

        $per_application_pool = new Text("per_application_pool", array(
            "id" => "per_application_pool",
            "name" => "per_application_pool",
        ));

        $settings = $options["settings"];

        if($settings["doStaticCompression"] != "False") {
            $static_compression->setAttribute("checked","checked");
        }
        if($settings["doDynamicCompression"] != "False") {
            $dynamic_compression->setAttribute("checked","checked");
        }

        $compress_file_size->setAttribute("value", $settings["minFileSizeForComp"]);


        if($settings["doDiskSpaceLimiting"] == "True") {
            $per_application_pool_status->setAttribute("checked","checked");
            $per_application_pool->setAttribute("value", $settings["maxDiskSpaceUsage"]);
        }
        else {
            $per_application_pool->setAttribute("disabled","disabled");
        }

        $this->add($dynamic_compression);
        $this->add($static_compression);
        $this->add($compress_file_size);
        $this->add($per_application_pool_status);
        $this->add($per_application_pool);

    }

    private function pkgStatus($installed_packages)
    {
        foreach ($installed_packages as $packages) {
            if($packages["package_name"] == "python") {
                if($packages["status"] == "installed") {
                    $this->python_status = true;
                }
            }
            if($packages["package_name"] == "perl") {
                if($packages["status"] == "installed") {
                    $this->perl_status = true;
                }
            }
        }
    }

    private function settings($options)
    {
        if($options["webserver_type"] == "apache") {
            $this->apache($options);
        }
        else if($options["webserver_type"] == "nginx") {
            $this->nginx($options);
        }
        else {
            $this->settingsElementsIis($options);
        }
    }

    private function mimeTypes()
    {
        $file_extension = new Text("file_extension", array(
            "id" => "file_extension",
            "name" => "file_extension",
        ));

        $mimetype_octet = new Text("mimetype_octet", array(
            "id" => "mimetype_octet",
            "name" => "mimetype_octet",
        ));

        $this->add($file_extension);
        $this->add($mimetype_octet);

    }

    private function apache($options)
    {
        // Keep Alive
        // Default On (Debian)
        $keep_alive = new Check("keep_alive", array(
            "id" => "keep_alive",
            "name" => "keep_alive",
        ));

        // KeepAlive Timeout
        // Default 300 seconds
        $keepalive_timeout = new Text("keepalive_timeout", array(
            "id" => "keepalive_timeout",
            "name" => "keepalive_timeout",
        ));


        // Timeout
        // Default 300 Seconds
        $timeout = new Text("timeout", array(
            "id" => "timeout",
            "name" => "timeout",
            "value" => "300",
        ));

        // MaxKeepAliveRequests 100
        $max_keepalive_req = new Text("max_keepalive_req", array(
            "id" => "max_keepalive_req",
            "name" => "max_keepalive_req",
            "value" => "100",
        ));

        // Start Server
        // Default 5
        $start_server = new Text("start_server", array(
            "id" => "start_server",
            "name" => "start_server",
            "value" => "5",
        ));

        // Min Spare Server
        // Default 5
        $min_spare_server = new Text("min_spare_server", array(
            "id" => "min_spare_server",
            "name" => "min_spare_server",
            "value" => "5",
        ));

        // Max Spare Server
        // Default 10
        $max_spare_server = new Text("max_spare_server", array(
            "id" => "max_spare_server",
            "name" => "max_spare_server",
            "value" => "10",
        ));

        // Max Client
        // Default 150
        $max_client = new Text("max_client", array(
            "id" => "max_client",
            "name" => "max_client",
            "value" => "150",
        ));


        // Max Requests Per Child
        // Default 0
        $max_req_per_child = new Text("max_req_per_child", array(
            "id" => "max_req_per_child",
            "name" => "max_req_per_child",
            "value" => "0",
        ));

        $settings = $options["settings"];

        if((isset($settings["keep_alive"])) && ($settings["keep_alive"] != null))
            if(strtolower($settings["keep_alive"]) == ON)
                $keep_alive->setAttribute("checked","checked");

        if((isset($settings["keep_alive_timeout"])) && ($settings["keep_alive_timeout"] != null))
            $keepalive_timeout->setAttribute("value", $settings["keep_alive_timeout"]);

        if((isset($settings["server_timeout"])) && ($settings["server_timeout"] != null))
            $timeout->setAttribute("value", $settings["server_timeout"]);

        if((isset($settings["max_keep_alive_requests"])) && ($settings["max_keep_alive_requests"] != null))
            $max_keepalive_req->setAttribute("value", $settings["max_keep_alive_requests"]);

        if((isset($settings["start_server"])) && ($settings["start_server"] != null))
            $start_server->setAttribute("value", $settings["start_server"]);

        if((isset($settings["min_spare_servers"])) && ($settings["min_spare_servers"] != null))
            $min_spare_server->setAttribute("value", $settings["min_spare_servers"]);

        if((isset($settings["max_clients"])) && ($settings["max_clients"] != null))
            $max_client->setAttribute("value", $settings["max_clients"]);

        if((isset($settings["max_requests_per_childs"])) && ($settings["max_requests_per_childs"] != null))
            $max_req_per_child->setAttribute("value", $settings["max_requests_per_childs"]);


        $this->add($keep_alive);
        $this->add($keepalive_timeout);
        $this->add($timeout);
        $this->add($max_keepalive_req);
        $this->add($start_server);
        $this->add($min_spare_server);
        $this->add($max_spare_server);
        $this->add($max_client);
        $this->add($max_req_per_child);
    }

    private function nginx($options)
    {
        $settings = $options["settings"];


        $worker_processes = new Text("worker_processes", array(
            "id" => "worker_processes",
            "name" => "worker_processes",
            "placeholder" => "4"
        ));
        if(!empty($settings["worker_processes"]))
            $worker_processes->setAttribute("value", $settings["worker_processes"]);

        $worker_connections = new Text("worker_connections", array(
            "id" => "worker_connections",
            "name" => "worker_connections",
            "placeholder" => "768"
        ));
        if(!empty($settings["worker_connections"])) {
            $worker_connections->setAttribute("value", $settings["worker_connections"]);
        }


        $multi_accept = new Check("multi_accept", array(
            "id" => "multi_accept",
            "name" => "multi_accept",
        ));
        if((strtolower($settings["multi_accept"]) == "on") || (trim($settings["multi_accept"]) == null))
            $multi_accept->setAttribute("checked","checked");

        $sendfile = new Check("sendfile", array(
            "id" => "sendfile",
            "name" => "sendfile",
        ));
        if((strtolower($settings["sendfile"]) == "on") || (trim($settings["sendfile"]) == null))
            $sendfile->setAttribute("checked","checked");

        $tcp_nopush = new Check("tcp_nopush", array(
            "id" => "tcp_nopush",
            "name" => "tcp_nopush",
        ));
        if((strtolower($settings["tcp_nopush"]) == "on") || (trim($settings["tcp_nopush"]) == null))
            $tcp_nopush->setAttribute("checked","checked");


        $tcp_nodelay = new Check("tcp_nodelay", array(
            "id" => "tcp_nodelay",
            "name" => "tcp_nodelay",
        ));
        if((strtolower($settings["tcp_nodelay"]) == "on") || (trim($settings["tcp_nodelay"]) == null))
            $tcp_nodelay->setAttribute("checked","checked");


        $keepalive_timeout = new Text("keepalive_timeout", array(
            "id" => "keepalive_timeout",
            "name" => "keepalive_timeout",
            "placeholder" => "65"
        ));
        if((isset($settings["keepalive_timeout"])) && ($settings["keepalive_timeout"] != null))
            $keepalive_timeout->setAttribute("value", $settings["keepalive_timeout"]);


        $types_hash_max_size = new Text("types_hash_max_size", array(
            "id" => "types_hash_max_size",
            "name" => "types_hash_max_size",
            "placeholder" => "2048"
        ));
        if((isset($settings["types_hash_max_size"])) && ($settings["types_hash_max_size"] != null))
            $types_hash_max_size->setAttribute("value", $settings["types_hash_max_size"]);


        $server_tokens = new Check("server_tokens", array(
            "id" => "server_tokens",
            "name" => "server_tokens",
        ));
        if((isset($settings["server_tokens"])) && ($settings["server_tokens"] != null))
            if(strtolower($settings["server_tokens"]) == "on")
                $server_tokens->setAttribute("checked","checked");

        $server_names_hash_bucket_size = new Text("server_names_hash_bucket_size", array(
            "id" => "server_names_hash_bucket_size",
            "name" => "server_names_hash_bucket_size",
            "placeholder" => "64"
        ));
        if((isset($settings["server_names_hash_bucket_size"])) && ($settings["server_names_hash_bucket_size"] != null))
            $server_names_hash_bucket_size->setAttribute("value", $settings["server_names_hash_bucket_size"]);

        $server_name_in_redirect = new Check("server_name_in_redirect", array(
            "id" => "server_name_in_redirect",
            "name" => "server_name_in_redirect",
        ));
        if((isset($settings["server_name_in_redirect"])) && ($settings["server_name_in_redirect"] != null))
            if(strtolower($settings["server_name_in_redirect"]) == "on")
                $server_name_in_redirect->setAttribute("checked","checked");

        $gzip = new Check("gzip", array(
            "id" => "gzip",
            "name" => "gzip",
        ));
        if((strtolower($settings["gzip"]) == "on") || trim($settings["gzip"]) == null) {
            $gzip->setAttribute("checked","checked");
        }

        $gzip_disable = new Text("gzip_disable", array(
            "id" => "gzip_disable",
            "name" => "gzip_disable",
        ));
        if((isset($settings["gzip_disable"])) && ($settings["gzip_disable"] != null))
            $gzip_disable->setAttribute("value", $settings["gzip_disable"]);

        $gzip_vary = new Check("gzip_vary", array(
            "id" => "gzip_vary",
            "name" => "gzip_vary",
        ));
        if(strtolower($settings["gzip_vary"]) == "on"  || trim($settings["gzip_vary"]) == null)
            $gzip_vary->setAttribute("checked","checked");

        $gzip_proxied = new Select("gzip_proxied",array("off" => "off",
                                                        "expired" => "expired",
                                                        "no-cache" => "no-cache",
                                                        "no-store" => "no-store",
                                                        "private" => "private",
                                                        "no_last_modified" => "no_last_modified",
                                                        "no_etag" => "no_etag",
                                                        "auth" => "auth",
                                                        "any" => "any"), array(
            "id" => "gzip_proxied",
            "name" => "gzip_proxied",
        ));
        if((isset($settings["gzip_proxied"])) && ($settings["gzip_proxied"] != null))
            $gzip_proxied->setDefault($settings["gzip_proxied"]);
        else
            $gzip_proxied->setDefault("any");

        $gzip_comp_level = new Select("gzip_comp_level", [
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5",
            "6" => "6",
            "7" => "7",
            "8" => "8",
            "9" => "9",
        ], array(
            "id" => "gzip_comp_level",
            "name" => "gzip_comp_level",
        ));
        if((isset($settings["gzip_comp_level"])) && ($settings["gzip_comp_level"] != null))
            $gzip_comp_level->setDefault($settings["gzip_comp_level"]);
        else
            $gzip_comp_level->setDefault("6");


        $gzip_buffers = new Text("gzip_buffers", array(
            "id" => "gzip_buffers",
            "name" => "gzip_buffers",
            "placeholder" => "16 8k"
        ));
        if((isset($settings["gzip_buffers"])) && ($settings["gzip_buffers"] != null))
            $gzip_buffers->setAttribute("value", $settings["gzip_buffers"]);


        $gzip_http_version = new Select("gzip_http_version",[
            "1.0" => "1.0",
            "1.1" => "1.1"
        ], array(
            "id" => "gzip_http_version",
            "name" => "gzip_http_version",
        ));
        if((isset($settings["gzip_http_version"])) && ($settings["gzip_http_version"] != null))
            $gzip_http_version->setDefault($settings["gzip_http_version"]);
        else
            $gzip_http_version->setDefault("1.1");


        $gzip_types = new Text("gzip_types", array(
            "id" => "gzip_types",
            "name" => "gzip_types",
        ));
        if((isset($settings["gzip_types"])) && ($settings["gzip_types"] != null))
            $gzip_types->setAttribute("value", $settings["gzip_types"]);

        $this->add($worker_processes);
        $this->add($worker_connections);
        $this->add($multi_accept);
        $this->add($sendfile);
        $this->add($tcp_nopush);
        $this->add($tcp_nodelay);
        $this->add($keepalive_timeout);
        $this->add($types_hash_max_size);
        $this->add($server_tokens);
        $this->add($server_names_hash_bucket_size);
        $this->add($server_name_in_redirect);
        $this->add($gzip);
        $this->add($gzip_disable);
        $this->add($gzip_vary);
        $this->add($gzip_proxied);
        $this->add($gzip_comp_level);
        $this->add($gzip_buffers);
        $this->add($gzip_http_version);
        $this->add($gzip_types);

    }
}