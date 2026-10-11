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
use FPanel\Process\Php;

class PhpSettings extends Library
{
    public function saveSettings($domain_id): bool
    {
        $domains = Domains::findFirstById($domain_id);
        if(!$domains) {
            $this->error_messages["domain_name"] = __("Domain not found");
            return false;
        }
        if(!$this->validate()) {
            return false;
        }

        $extensions = implode(",", $this->getPost("extensions"));

        $display_errors = $this->getPost("display_errors", OFF) == "on" ?  "On" : "Off";
        $display_startup_errors = $this->getPost("display_startup_errors", "off") == "on" ?  "On" : "Off";
        $error_reporting = $this->getPost("error_reporting", "E_ALL & ~E_DEPRECATED & ~E_STRICT");
        $log_errors = $this->getPost("log_errors", "off") == "on" ?  "On" : "Off";
        $ignore_repeated_errors = $this->getPost("ignore_repeated_errors", "off") == "on" ?  "On" : "Off";
        $log_errors_max_len = $this->getPost("log_errors_max_len", "1024");
        $html_errors = $this->getPost("html_errors", "off") == "on" ?  "On" : "Off";
        $error_log = $this->getPost("error_log"); // Path

        $short_open_tag = $this->getPost("short_open_tag", "off") == "on" ?  "On" : "Off";
        $output_buffering = $this->getPost("output_buffering", "4096") ;
        $precision = $this->getPost("precision", "14") ;
        $ignore_user_abort = $this->getPost("ignore_user_abort", "off") == "on" ?  "On" : "Off";
        $default_charset = $this->getPost("default_charset", "default") ;
        $date_timezone = $this->getPost("date_timezone", "default") ; // date.timezone
        $bcmath_scale = $this->getPost("bcmath_scale", "0") ; //bcmath.scale
        $allow_url_fopen = $this->getPost("allow_url_fopen", "off") == "on" ?  "On" : "Off";
        $auto_globals_jit = $this->getPost("auto_globals_jit", "off") == "on" ?  "On" : "Off";

        $memory_limit = $this->getPost("memory_limit", "128") ; //
        $max_input_time = $this->getPost("max_input_time", "60") ; //
        $max_execution_time = $this->getPost("max_execution_time", "30") ; //
        $post_max_size = $this->getPost("post_max_size", "8") ; //
        $upload_max_filesize = $this->getPost("upload_max_filesize", "2") ; //
        $max_file_uploads = $this->getPost("max_file_uploads", "20") ; //


        $session_save_handler = $this->getPost("session_save_handler") ; // session.save_handler
        $session_save_path = $this->getPost("session_save_path") ; // session.save_path
        $session_use_cookies = $this->getPost("session_use_cookies", "0") == "on" ?  "1" : "0"; // session.use_cookies
        $session_name = $this->getPost("session_name") ; // session.name
        $session_auto_start = $this->getPost("session_auto_start", "0") == "on" ?  "1" : "0"; // session.auto_start
        $session_cookie_lifetime = $this->getPost("session_cookie_lifetime", "0"); // session.cookie_lifetime
        $session_cookie_path = $this->getPost("session_cookie_path", "/"); // session.cookie_path
        $session_cookie_domain = $this->getPost("session_cookie_domain"); // session.cookie_path
        $session_cache_expire = $this->getPost("session_cache_expire"); // session.cache_expire


        $phpmng = new Php();
        $phpmng->save([
            "domain" => $this->getPost("domain_name"),
            "phpver" => $this->getPost("phpver"),

            "display_errors" => $display_errors,
            "display_startup_errors" => $display_startup_errors,
            "error_reporting" => $error_reporting,
            "log_errors" => $log_errors,
            "log_errors_max_len" => $log_errors_max_len,
            "ignore_repeated_errors" => $ignore_repeated_errors,
            "html_errors" => $html_errors,
            "error_log" => $error_log,

            "extensions" => $extensions,

            "short_open_tag" => $short_open_tag,
            "output_buffering" => $output_buffering,
            "precision" => $precision,
            "ignore_user_abort" => $ignore_user_abort,
            "default_charset" => $default_charset,
            "date_timezone" => $date_timezone,
            "bcmath_scale" => $bcmath_scale,
            "allow_url_fopen" => $allow_url_fopen,
            "auto_globals_jit" => $auto_globals_jit,
            "memory_limit" => $memory_limit,
            "max_input_time" => $max_input_time,
            "max_execution_time" => $max_execution_time,
            "post_max_size" => $post_max_size,
            "upload_max_filesize" => $upload_max_filesize,
            "max_file_uploads" => $max_file_uploads,

            "session_save_handler" => $session_save_handler,
            "session_save_path" => $session_save_path,
            "session_use_cookies" => $session_use_cookies,
            "session_name" => $session_name,
            "session_auto_start" => $session_auto_start,
            "session_cookie_lifetime" => $session_cookie_lifetime,
            "session_cookie_path" => $session_cookie_path,
            "session_cookie_domain" => $session_cookie_domain,
            "session_cache_expire" => $session_cache_expire,
        ]);
        return true;
    }

    private function validate(): bool
    {
        $required = [
            [
                "field" => "error_reporting",
                "item" => $this->getPost("error_reporting"),
            ],
            [
                "field" => "log_errors_max_len",
                "item" => $this->getPost("log_errors_max_len"),
            ],
            [
                "field" => "output_buffering",
                "item" => $this->getPost("output_buffering"),
            ],
            [
                "field" => "precision",
                "item" => $this->getPost("precision"),
            ],
            [
                "field" => "bcmath_scale",
                "item" => $this->getPost("bcmath_scale"),
            ],
            [
                "field" => "memory_limit",
                "item" => $this->getPost("memory_limit"),
            ],
            [
                "field" => "max_input_time",
                "item" => $this->getPost("max_input_time"),
            ],
            [
                "field" => "max_execution_time",
                "item" => $this->getPost("max_execution_time"),
            ],
            [
                "field" => "post_max_size",
                "item" => $this->getPost("post_max_size"),
            ],
            [
                "field" => "upload_max_filesize",
                "item" => $this->getPost("upload_max_filesize"),
            ],
            [
                "field" => "session_cookie_lifetime",
                "item" => $this->getPost("session_cookie_lifetime"),
            ],
            [
                "field" => "session_cache_expire",
                "item" => $this->getPost("session_cache_expire"),
            ],
        ];
        $this->validation->Required($required);
        $this->validation->Numeric($this->getPost("log_errors_max_len"), "log_errors_max_len");
        $this->validation->Numeric($this->getPost("output_buffering"), "output_buffering");
        $this->validation->Numeric($this->getPost("precision"), "precision");
        $this->validation->Numeric($this->getPost("bcmath_scale"), "bcmath_scale");
        $this->validation->Numeric($this->getPost("memory_limit"), "memory_limit");
        $this->validation->Numeric($this->getPost("max_input_time"), "max_input_time");
        $this->validation->Numeric($this->getPost("max_execution_time"), "max_execution_time");
        $this->validation->Numeric($this->getPost("post_max_size"), "post_max_size");
        $this->validation->Numeric($this->getPost("upload_max_filesize"), "upload_max_filesize");
        $this->validation->Numeric($this->getPost("session_cookie_lifetime"), "session_cookie_lifetime");
        $this->validation->Numeric($this->getPost("session_cache_expire"), "session_cache_expire");
        $this->validation->Numeric($this->getPost("max_file_uploads"), "max_file_uploads");
        $available_modules = [];
        foreach (explode(",", $this->getPost("installed_extensions")) as $item) {
            if($item != null) {
                $available_modules[] = $item;
            }
        }
        foreach ($this->getPost("extensions") as $ext) {
            $this->validation->InclusionIn($ext, "extensions", $available_modules);
        }

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}