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
 * Class VdirForm
 * @package FPanel\Forms
 */
class VdirForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {

        if($options["page"] != "create") {
            $this->formElements($options);
        }
        else {
            $this->createFormElements($options);
        }

    }


    /**
     * @param $options
     */
    private function formElements($options)
    {
        $virtual_dir_settings = $options["vdir_settings"];
        $selected_handlers = $this->findHandlers($virtual_dir_settings["handlers"]);

        // Virtual Directory
        $this->add(new Text("directoryPath" , array(
            "id" => "directoryPath",
            "name" => "directoryPath",
            "value" => "/httpdoc",
            "disabled"  => "disabled"
        )));

        // $handlers = array("1" => "Read", "2" => "Write","4" => "Execute", "16" => "Source", "512" => "Script" , "1024" => "NoRemoteWrite", "4096" => "NoRemoteRead","8192" =>  "NoRemoteExecute", "16384" => "NoRemoteScript");


        $accessPolicy = new Select("accessPolicy",array(
            "Read" => __("Read"),
            "Write" => __("Write"),
            "Execute" => __("Execute"),
            "Source" => __("Source"),
            "Script" => __("Script"),
            "NoRemoteRead" => __("No Remote Read"),
            "NoRemoteWrite" => __("No Remote Write"),
            "NoRemoteExecute" => __("No Remote Execute"),
            "NoRemoteScript" => __("No Remote Script"),
        ),array(
            "id" => "accessPolicy",
            "name" => "accessPolicy[]",
            "multiple" => "multiple",
            "data-selectable" => __("Available"),
            "data-selection" => __("Selected"),
        ));


        $selected_policy = array();
        if(array_key_exists("1", $selected_handlers)) {
            $selected_policy[] = "Read";
        }
        if(array_key_exists("2", $selected_handlers)) {
            $selected_policy[] = "Write";
        }
        if(array_key_exists("4", $selected_handlers)) {
            $selected_policy[] = "Execute";
        }
        if(array_key_exists("16", $selected_handlers)) {
            $selected_policy[] = "Source";
        }
        if(array_key_exists("512", $selected_handlers)) {
            $selected_policy[] = "Script";
        }
        if(array_key_exists("1024", $selected_handlers)) {
            $selected_policy[] = "NoRemoteWrite";
        }
        if(array_key_exists("4096", $selected_handlers)) {
            $selected_policy[] = "NoRemoteRead";
        }
        if(array_key_exists("8192", $selected_handlers)) {
            $selected_policy[] = "NoRemoteExecute";
        }
        if(array_key_exists("16384", $selected_handlers)) {
            $selected_policy[] = "NoRemoteScript";
        }
        $accessPolicy->setDefault($selected_policy);
        $this->add($accessPolicy);



        $directory_browse = new Check("directory_browse", array(
            "id" => "directory_browse",
            "name" => "directory_browse",
        ));
        if($virtual_dir_settings["directoryBrowse"] == "True") {
            $directory_browse->setAttribute("checked","checked");
        }
        $this->add($directory_browse);

        $createapplication = new Check("createapplication", array(
            "id" => "createapplication",
            "name" => "createapplication",
            "checked" => "checked",
            "disabled" => "disabled",
        ));

        $this->add($createapplication);



        $logging = new Check("logging", array(
            "id" => "logging",
            "name" => "logging",
        ));
        if($virtual_dir_settings["httpLogging"] == "True") {
            $logging->setAttribute("checked","checked");
        }
        $this->add($logging);


        // Application Settings
        $execute_in_mta = new Check("execute_in_mta", array(
            "id" => "execute_in_mta",
            "name" => "execute_in_mta",
        ));
        if($virtual_dir_settings["executeInMta"] == "True") {
            $execute_in_mta->setAttribute("checked","checked");
        }
        $this->add($execute_in_mta);


        // Default Documents
        $use_default_document = new Check("use_default_document", array(
            "id" => "use_default_document",
            "name" => "use_default_document",
        ));
        if($virtual_dir_settings["defaultDocumentStatus"] == "True") {
            $use_default_document->setAttribute("checked","checked");
        }
        $this->add($use_default_document);

        $this->add(new Text("default_document_add" , array(
            "id" => "default_document_add",
            "name" => "default_document_add",
        )));

        $gettin_def_docs = explode(",", $virtual_dir_settings["defaultDocuments"]);
        $default_docuements = array();
        foreach($gettin_def_docs as $value) {
            $default_docuements[$value] = $value;
        }
        $default_docuement_order = new Select("default_document_order",
            $default_docuements,
        array(
            "id" => "default_document_order",
            "name" => "default_document_order[]",
            "multiple" => "multiple",
            "size" => "10",
        ));
        $default_docuement_order->setDefault($default_docuements);
        $this->add($default_docuement_order);

        // Directory security
        $allow_anonymous = new Check("allow_anonymous", array(
            "id" => "allow_anonymous",
            "name" => "allow_anonymous",
        ));

        if($virtual_dir_settings["anonymousAuthentication"] == "True") {
            $allow_anonymous->setAttribute("checked","checked");
        }
        $this->add($allow_anonymous);

        // Asp Settings
        $parent_path = new Check("parent_path", array(
            "id" => "parent_path",
            "name" => "parent_path",
        ));
        if($virtual_dir_settings["enableParentPaths"] == "True") {
            $parent_path->setAttribute("checked","checked");
        }
        $this->add($parent_path);

        $asp_buffer_limit = $virtual_dir_settings["bufferingLimit"];
        $this->add(new Text("buffer_limit" , array(
            "id" => "buffer_limit",
            "name" => "buffer_limit",
            "value"  => $asp_buffer_limit
        )));

        $entity_size_req = $virtual_dir_settings["maxRequestEntityAllowed"];
        $this->add(new Text("entity_size" , array(
            "id" => "entity_size",
            "name" => "entity_size",
            "value"  => $entity_size_req
        )));

        $server_debugging = new Check("server_debugging", array(
            "id" => "server_debugging",
            "name" => "server_debugging",
        ));
        $client_debugging = new Check("client_debugging", array(
            "id" => "client_debugging",
            "name" => "client_debugging",
        ));

        if($virtual_dir_settings["appAllowDebugging"] == "True") {
            $server_debugging->setAttribute("checked","checked");
        }
        if($virtual_dir_settings["appAllowClientDebug"] == "True") {
            $client_debugging->setAttribute("checked","checked");
        }
        $this->add($server_debugging);
        $this->add($client_debugging);

    }

    public function findHandlers($target): array
    {
        $array_handlers = array("1" => "Read", "2" => "Write","4" => "Execute", "16" => "Source", "512" => "Script" , "1024" => "NoRemoteWrite", "4096" => "NoRemoteRead","8192" =>  "NoRemoteExecute", "16384" => "NoRemoteScript");

        $output = array("1", "2", "4","16","512","1024","4096","8192","16384");
        $power_set = $this->arrayPowerSet($output);

        foreach ($power_set as $value) {
            if(!empty($value)) {
                $result = 0;
                $result_array = array();
                for ($i = 0; $i < count($value); $i++) {
                    $result_array[$value[$i]] = $array_handlers[$value[$i]];
                    $result += $value[$i];
                }
                if($result == $target) {
                    return $result_array;
                }
            }
        }
        return [];
    }

    public function arrayPowerSet($array): array
    {
        $results = array(array( ));

        foreach ($array as $element)
            foreach ($results as $combination)
                array_push($results, array_merge(array($element), $combination));

        return $results;
    }

    public function createFormElements($options)
    {
        $dir_name = new Text("dir_name" , array(
            "id" => "dir_name",
            "name" => "dir_name",
        ));

        $physical_path = new Text("physical_path" , array(
            "id" => "physical_path",
            "name" => "physical_path",
        ));


        $accessPolicy = new Select("accessPolicy",array(
            "Read" => __("Read"),
            "Write" => __("Write"),
            "Execute" => __("Execute"),
            "Source" => __("Source"),
            "Script" => __("Script"),
            "NoRemoteRead" => __("No Remote Read"),
            "NoRemoteWrite" => __("No Remote Write"),
            "NoRemoteExecute" => __("No Remote Execute"),
            "NoRemoteScript" => __("No Remote Script"),
        ),array(
            "id" => "accessPolicy",
            "name" => "accessPolicy[]",
            "multiple" => "multiple"
        ));


        $create_application = new Check("create_application" , array(
            "id" => "create_application",
            "name" => "create_application",
        ));

        $max_size_asp_buffer = new Text("buffer_limit" , array(
            "id" => "buffer_limit",
            "name" => "buffer_limit",
            "value" => "4194304"
        ));

        $max_size_entity_body = new Text("entity_size" , array(
            "id" => "entity_size",
            "name" => "entity_size",
            "value" => "200000"
        ));

        $server_side_debug = new Check("server_debugging" , array(
            "id" => "server_debugging",
            "name" => "server_debugging",
        ));

        $client_side_debug = new Check("client_debugging" , array(
            "id" => "client_debugging",
            "name" => "client_debugging",
        ));

        if($options["action"] != "edit") {
            $selected_policy = array("Read","Write");
            $accessPolicy->setDefault($selected_policy);
        }
        else {
            $virtual_dir_settings = $options["vdir_settings"]["virtualDirectorySettings"];
            $selected_handlers = $this->findHandlers($virtual_dir_settings["handlers"]);

            $selected_policy = array();
            if(array_key_exists("1", $selected_handlers)) {
                $selected_policy[] = "Read";
            }
            if(array_key_exists("2", $selected_handlers)) {
                $selected_policy[] = "Write";
            }
            if(array_key_exists("4", $selected_handlers)) {
                $selected_policy[] = "Execute";
            }
            if(array_key_exists("16", $selected_handlers)) {
                $selected_policy[] = "Source";
            }
            if(array_key_exists("512", $selected_handlers)) {
                $selected_policy[] = "Script";
            }
            if(array_key_exists("1024", $selected_handlers)) {
                $selected_policy[] = "NoRemoteWrite";
            }
            if(array_key_exists("4096", $selected_handlers)) {
                $selected_policy[] = "NoRemoteRead";
            }
            if(array_key_exists("8192", $selected_handlers)) {
                $selected_policy[] = "NoRemoteExecute";
            }
            if(array_key_exists("16384", $selected_handlers)) {
                $selected_policy[] = "NoRemoteScript";
            }
            $accessPolicy->setDefault($selected_policy);


            $dir_name->setAttribute("value", $options["name"]);
            $dir_name->setAttribute("disabled","disabled");

            $physical_path->setAttribute("value", $options["path"]);
            $physical_path->setAttribute("disabled","disabled");

            $create_application->setAttribute("disabled","disabled");

            if($options["app"] == ACTIVE) {
                $create_application->setAttribute("checked","checked");
            }

            if($virtual_dir_settings["appAllowDebugging"] == "True") {
                $server_side_debug->setAttribute("checked","checked");
            }
            if($virtual_dir_settings["appAllowClientDebug"] == "True") {
                $client_side_debug->setAttribute("checked","checked");
            }

            $max_size_asp_buffer->setAttribute("value", $virtual_dir_settings["bufferingLimit"]);
            $max_size_entity_body->setAttribute("value", $virtual_dir_settings["maxRequestEntityAllowed"]);
        }
        $this->add($dir_name);
        $this->add($physical_path);
        $this->add($accessPolicy);
        $this->add($create_application);
        $this->add($max_size_asp_buffer);
        $this->add($max_size_entity_body);
        $this->add($server_side_debug);
        $this->add($client_side_debug);
    }

}

