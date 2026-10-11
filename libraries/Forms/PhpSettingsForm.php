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
 * Class PhpSettingsForm
 * @package FPanel\Forms
 */
class PhpSettingsForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
    }

    private function formElements($options)
    {
        $php_data = $options["data"];
        $extension_data = [];
        foreach ($php_data["PHP"]["extension"] as $ext) {
            $extension_data[$ext] = $ext;
        }

        $available_extensons = [];
        foreach (explode(",", $options["available_modules"]) as $available_module) {
            if($available_module != null)
                $available_extensons[$available_module] = $available_module;
        }

        $time_zone = $this->env->php_timezone();
        $display_errors = new Check('display_errors', [
            'id' => 'display_errors',
            'name' => 'display_errors',
        ]);
        $display_startup_errors = new Check('display_startup_errors', [
            'id' => 'display_startup_errors',
            'name' => 'display_startup_errors',
        ]);
        $error_reporting = new Text('error_reporting', [
            'id' => 'error_reporting',
            'name' => 'error_reporting',
        ]);
        $log_errors = new Check('log_errors', [
            'id' => 'log_errors',
            'name' => 'log_errors',
        ]);
        $log_errors_max_len = new Text('log_errors_max_len', [
            'id' => 'log_errors_max_len',
            'name' => 'log_errors_max_len',
        ]);
        $ignore_repeated_errors = new Check('ignore_repeated_errors', [
            'id' => 'ignore_repeated_errors',
            'name' => 'ignore_repeated_errors',
        ]);
        $precision = new Text('precision', [
            'id' => 'precision',
            'name' => 'precision',
        ]);
        $html_errors = new Check('html_errors', [
            'id' => 'html_errors',
            'name' => 'html_errors',
        ]);
        $error_log = new Text('error_log', [
            'id' => 'error_log',
            'name' => 'error_log',
        ]);

        $extensions = new Select('extensions', $available_extensons, [
            'id' => 'extensions',
            'name' => 'extensions[]',
            'multiple' => 'multiple',
        ]);
        $extensions->setDefault($extension_data);

        $short_open_tag = new Check('short_open_tag', [
            'id' => 'short_open_tag',
            'name' => 'short_open_tag',
        ]);
        $output_buffering = new Text('output_buffering', [
            'id' => 'output_buffering',
            'name' => 'output_buffering',
        ]);
        $ignore_user_abort = new Check('ignore_user_abort', [
            'id' => 'ignore_user_abort',
            'name' => 'ignore_user_abort',
        ]);
        $default_charset = new Text('default_charset', [
            'id' => 'default_charset',
            'name' => 'default_charset',
        ]);
        $date_timezone = new Select('date_timezone', $time_zone, [
            'id' => 'date_timezone',
            'name' => 'date_timezone',
        ]);
        $bcmath_scale = new Text('bcmath_scale', [
            'id' => 'bcmath_scale',
            'name' => 'bcmath_scale',
        ]);
        $allow_url_fopen = new Check('allow_url_fopen', [
            'id' => 'allow_url_fopen',
            'name' => 'allow_url_fopen',
        ]);

        $auto_globals_jit = new Check('auto_globals_jit', [
            'id' => 'auto_globals_jit',
            'name' => 'auto_globals_jit',
        ]);
        $memory_limit = new Text('memory_limit', [
            'id' => 'memory_limit',
            'name' => 'memory_limit',
        ]);
        $max_input_time = new Text('max_input_time', [
            'id' => 'max_input_time',
            'name' => 'max_input_time',
        ]);
        $max_execution_time = new Text('max_execution_time', [
            'id' => 'max_execution_time',
            'name' => 'max_execution_time',
        ]);
        $post_max_size = new Text('post_max_size', [
            'id' => 'post_max_size',
            'name' => 'post_max_size',
        ]);
        $upload_max_filesize = new Text('upload_max_filesize', [
            'id' => 'upload_max_filesize',
            'name' => 'upload_max_filesize',
        ]);
        $max_file_uploads = new Text('max_file_uploads', [
            'id' => 'max_file_uploads',
            'name' => 'max_file_uploads',
        ]);



        $session_save_handler = new Text('session_save_handler', [
            'id' => 'session_save_handler',
            'name' => 'session_save_handler',
        ]);
        $session_save_path = new Text('session_save_path', [
            'id' => 'session_save_path',
            'name' => 'session_save_path',
        ]);
        $session_use_cookies = new Check('session_use_cookies', [
            'id' => 'session_use_cookies',
            'name' => 'session_use_cookies',
        ]);
        $session_name = new Text('session_name', [
            'id' => 'session_name',
            'name' => 'session_name',
        ]);
        $session_auto_start = new Check('session_auto_start', [
            'id' => 'session_auto_start',
            'name' => 'session_auto_start',
        ]);
        $session_cookie_lifetime = new Text('session_cookie_lifetime', [
            'id' => 'session_cookie_lifetime',
            'name' => 'session_cookie_lifetime',
        ]);
        $session_cookie_path = new Text('session_cookie_path', [
            'id' => 'session_cookie_path',
            'name' => 'session_cookie_path',
        ]);
        $session_cookie_domain = new Text('session_cookie_domain', [
            'id' => 'session_cookie_domain',
            'name' => 'session_cookie_domain',
        ]);
        $session_cache_expire = new Text('session_cache_expire', [
            'id' => 'session_cache_expire',
            'name' => 'session_cache_expire',
        ]);


        if(isset($php_data["PHP"]["display_errors"]) && (strtolower($php_data["PHP"]["display_errors"]) == "on")) {
            $display_errors->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["display_startup_errors"]) && (strtolower($php_data["PHP"]["display_startup_errors"]) == "on")) {
            $display_startup_errors->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["error_reporting"]) && (strtolower($php_data["PHP"]["error_reporting"]) != null)) {
            $error_reporting->setAttribute("value", $php_data["PHP"]["error_reporting"]);
        }
        if(isset($php_data["PHP"]["log_errors"]) && (strtolower($php_data["PHP"]["log_errors"]) == "on")) {
            $log_errors->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["log_errors_max_len"]) && (strtolower($php_data["PHP"]["log_errors_max_len"]) != null)) {
            $log_errors_max_len->setAttribute("value", $php_data["PHP"]["log_errors_max_len"]);
        }
        if(isset($php_data["PHP"]["ignore_repeated_errors"]) && (strtolower($php_data["PHP"]["ignore_repeated_errors"]) == "on")) {
            $ignore_repeated_errors->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["precision"]) && (strtolower($php_data["PHP"]["precision"]) != null)) {
            $precision->setAttribute("value", $php_data["PHP"]["precision"]);
        }
        if(isset($php_data["PHP"]["html_errors"]) && (strtolower($php_data["PHP"]["html_errors"]) == "on")) {
            $html_errors->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["error_log"]) && (strtolower($php_data["PHP"]["error_log"]) != null)) {
            $error_log->setAttribute("value", $php_data["PHP"]["error_log"]);
        }
        else {
            $error_log->setAttribute("placeholder", __("Default"));
        }
        if(isset($php_data["PHP"]["short_open_tag"]) && (strtolower($php_data["PHP"]["short_open_tag"]) == "on")) {
            $short_open_tag->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["output_buffering"]) && (strtolower($php_data["PHP"]["output_buffering"]) != null)) {
            $output_buffering->setAttribute("value", $php_data["PHP"]["output_buffering"]);
        }
        if(isset($php_data["PHP"]["ignore_user_abort"]) && (strtolower($php_data["PHP"]["ignore_user_abort"]) == "on")) {
            $ignore_user_abort->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["default_charset"]) && (strtolower($php_data["PHP"]["default_charset"]) != null)) {
            $default_charset->setAttribute("value", $php_data["PHP"]["default_charset"]);
        }
        else {
            $default_charset->setAttribute("placeholder", __("Default"));
        }
        if(isset($php_data["Date"]["date.timezone"]) && (strtolower($php_data["Date"]["date.timezone"]) != null)) {
            $date_timezone->setDefault($php_data["Date"]["date.timezone"]);
        }
        if(isset($php_data["bcmath"]["bcmath.scale"]) && (strtolower($php_data["bcmath"]["bcmath.scale"]) != null)) {
            $bcmath_scale->setAttribute("value", $php_data["bcmath"]["bcmath.scale"]);
        }
        if(isset($php_data["Session"]["session.save_handler"]) && (strtolower($php_data["Session"]["session.save_handler"]) != null)) {
            $session_save_handler->setAttribute("value", $php_data["Session"]["session.save_handler"]);
        }
        if(isset($php_data["Session"]["session.save_path"]) && (strtolower($php_data["Session"]["session.save_path"]) != null)) {
            $session_save_path->setAttribute("value", $php_data["Session"]["session.save_path"]);
        }
        if(isset($php_data["Session"]["session.use_cookies"]) && (strtolower($php_data["Session"]["session.use_cookies"]) == "on")) {
            $ignore_user_abort->setAttribute("checked", "checked");
        }
        if(isset($php_data["Session"]["session.name"]) && (strtolower($php_data["Session"]["session.name"]) != null)) {
            $session_name->setAttribute("value", $php_data["Session"]["session.name"]);
        }
        if(isset($php_data["Session"]["session.auto_start"]) && (strtolower($php_data["Session"]["session.auto_start"]) == "1")) {
            $session_auto_start->setAttribute("checked", "checked");
        }
        if(isset($php_data["Session"]["session.cookie_lifetime"]) && (strtolower($php_data["Session"]["session.cookie_lifetime"]) != null)) {
            $session_cookie_lifetime->setAttribute("value", $php_data["Session"]["session.cookie_lifetime"]);
        }
        if(isset($php_data["Session"]["session.cookie_path"]) && (strtolower($php_data["Session"]["session.cookie_path"]) != null)) {
            $session_cookie_path->setAttribute("value", $php_data["Session"]["session.cookie_path"]);
        }
        if(isset($php_data["Session"]["session.cookie_domain"]) && (strtolower($php_data["Session"]["session.cookie_domain"]) != null)) {
            $session_cookie_domain->setAttribute("value", $php_data["Session"]["session.cookie_domain"]);
        }
        if(isset($php_data["Session"]["session.cache_expire"]) && (strtolower($php_data["Session"]["session.cache_expire"]) != null)) {
            $session_cache_expire->setAttribute("value", $php_data["Session"]["session.cache_expire"]);
        }

        if(isset($php_data["PHP"]["auto_globals_jit"]) && (strtolower($php_data["PHP"]["auto_globals_jit"]) == "on")) {
            $auto_globals_jit->setAttribute("checked", "checked");
        }
        if(isset($php_data["PHP"]["memory_limit"]) && (strtolower($php_data["PHP"]["memory_limit"]) != null)) {
            $memory_limit->setAttribute("value", (int) $php_data["PHP"]["memory_limit"]);
        }
        if(isset($php_data["PHP"]["max_input_time"]) && (strtolower($php_data["PHP"]["max_input_time"]) != null)) {
            $max_input_time->setAttribute("value", (int) $php_data["PHP"]["max_input_time"]);
        }
        if(isset($php_data["PHP"]["max_execution_time"]) && (strtolower($php_data["PHP"]["max_execution_time"]) != null)) {
            $max_execution_time->setAttribute("value", (int) $php_data["PHP"]["max_execution_time"]);
        }
        if(isset($php_data["PHP"]["post_max_size"]) && (strtolower($php_data["PHP"]["post_max_size"]) != null)) {
            $post_max_size->setAttribute("value", (int) $php_data["PHP"]["post_max_size"]);
        }
        if(isset($php_data["PHP"]["upload_max_filesize"]) && (strtolower($php_data["PHP"]["upload_max_filesize"]) != null)) {
            $upload_max_filesize->setAttribute("value", (int) $php_data["PHP"]["upload_max_filesize"]);
        }
        if(isset($php_data["PHP"]["max_file_uploads"]) && (strtolower($php_data["PHP"]["max_file_uploads"]) != null)) {
            $max_file_uploads->setAttribute("value", (int) $php_data["PHP"]["max_file_uploads"]);
        }
        if(isset($php_data["PHP"]["allow_url_fopen"]) && (strtolower($php_data["PHP"]["allow_url_fopen"]) == "on")) {
            $allow_url_fopen->setAttribute("checked", "checked");
        }

        $this->add($display_errors);
        $this->add($display_startup_errors);
        $this->add($error_reporting);
        $this->add($log_errors);
        $this->add($log_errors_max_len);
        $this->add($ignore_repeated_errors);
        $this->add($precision);
        $this->add($html_errors);
        $this->add($error_log);

        $this->add($extensions);

        $this->add($short_open_tag);
        $this->add($output_buffering);
        $this->add($ignore_user_abort);
        $this->add($default_charset);
        $this->add($date_timezone);
        $this->add($bcmath_scale);


        $this->add($auto_globals_jit);
        $this->add($memory_limit);
        $this->add($max_input_time);
        $this->add($max_execution_time);
        $this->add($post_max_size);
        $this->add($upload_max_filesize);
        $this->add($max_file_uploads);
        $this->add($allow_url_fopen);


        $this->add($session_save_handler);
        $this->add($session_save_path);
        $this->add($session_use_cookies);
        $this->add($session_name);
        $this->add($session_auto_start);
        $this->add($session_cookie_lifetime);
        $this->add($session_cookie_path);
        $this->add($session_cookie_domain);
        $this->add($session_cache_expire);

    }
}