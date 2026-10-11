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

use FPanel\Models\SystemPackages;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class DatabaseServerForm
 * @package FPanel\Forms
 */
class DatabaseServerForm extends Form
{
    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        switch($options['page']) {
            case "database":
                $this->formElements($options);
                break;
            case "settings":
                $this->settingsFormElements($options);
                break;
        }
    }

    private function formElements($options = null)
    {

        // Host
        $db_host = new Text('db_host', array(
            'id' => 'db_host',
            'name' => 'db_host',
        ));

        // DB Port
        $db_port = new Text('db_port', array(
            'id' => 'db_port',
            'name' => 'db_port',
        ));

        // Type
        $types = array(
            'mysql' => 'MySQL',
            'pgsql' => 'PostgreSQL',
            'mssql' => 'Microsoft SQL Server',
        );

        if(PANEL_OS_TYPE == LINUX) {
            $packages = SystemPackages::findFirst("package_name = 'unixodbc'");
            if(!$packages) {
                unset($types['mssql']);
            }
        }

        $db_type = new Select('db_type',
            $types,
            array(
                'id' => 'db_type',
                'name' => 'db_type',
            ));

        // DB Username
        $username = new Text('username', array(
            'id' => 'username',
            'name' => 'username',
        ));

        // DB Password
        $password = new Password('password', array(
            'id' => 'password',
            'name' => 'password',
        ));

        $db_server_name = new Text('db_server_name', array(
            'id' => 'db_server_name',
            'name' => 'db_server_name',
        ));

        $this->add($db_host);
        $this->add($db_port);
        $this->add($db_type);
        $this->add($username);
        $this->add($password);
        $this->add($db_server_name);

        if($options['action'] == "edit") {
            $db_host->setAttribute('value', $options['db_host']);
            $db_port->setAttribute('value', $options['db_port']);
            $db_type->setDefault($options['db_type']);
            $username->setAttribute('value', $options['db_username']);
            $db_server_name->setAttribute('value', $options['db_server_name']);
        }
    }

    private function settingsFormElements($options = null)
    {
        switch($options['database_type']) {
            case "postgresql":
            case "mssql":
                break;
            case "mysql":
                $this->mysqlElements($options);
                break;
        }
    }

    private function mysqlElements($options)
    {
        $mysql_8 = false;
        if(strpos($this->system_packages["DATABASE_SERVERS"], "mysql") !== false) {
            if(PANEL_OS_TYPE == LINUX) {
                $package = SystemPackages::findFirst("package_name = 'mysql-community-server'");
                if(strpos($package->version, "8.") !== false) {
                    $mysql_8 = true;
                }
            }
            else {
                $package = SystemPackages::findFirst("package_name LIKE 'MySQL Server%'");
                if(strpos($package->version, "8.") !== false) {
                    $mysql_8 = true;
                }
            }
        }
        $allow_remote_connection = new Select('allow_remote_connection',array(
            'on' => 'On',
            'off' => 'Off'
        ),array(
            'id' => 'allow_remote_connection',
            'name' => 'allow_remote_connection',
        ));
        $buffer_pool_size = new Text('buffer_pool_size',array(
            'id' => 'buffer_pool_size',
            'name' => 'buffer_pool_size',
        ));
        $log_file_size = new Text('log_file_size',array(
            'id' => 'log_file_size',
            'name' => 'log_file_size',
        ));
        $file_per_table = new Select('file_per_table',array(
            'default' => __('Default'),
            'on' => 'On',
            'off' => 'Off',
        ),array(
            'id' => 'file_per_table',
            'name' => 'file_per_table',
        ));

        if($options['server_os'] == LINUX) {
            $flush_methods = array(
                'default' => __('Default'),
                'fsync' => 'fsync',
                'O_DSYNC' => 'O_DSYNC',
                'O_DIRECT' => 'O_DIRECT',
                'littlesync' => 'littlesync',
                'nosync' => 'nosync',
            );
        }
        else {
            $flush_methods = array(
                'default' => __('Default'),
                'async_unbuffered' => 'async_unbuffered',
                'normal' => 'normal',
                'unbuffered' => 'unbuffered',
            );
        }
        $flush_method = new Select('flush_method', $flush_methods,array(
            'id' => 'flush_method',
            'name' => 'flush_method',
        ));

        // Logging
        $log_error = new Text('log_error',array(
            'id' => 'log_error',
            'name' => 'log_error',
        ));
        $slow_query_log = new Select('slow_query_log',array(
            'default' => __('Default'),
            'on' => 'On',
            'off' => 'Off',
        ),array(
            'id' => 'slow_query_log',
            'name' => 'slow_query_log',
        ));
        $slow_query_log_file = new Text('slow_query_log_file',array(
            'id' => 'slow_query_log_file',
            'name' => 'slow_query_log_file',
        ));

        // OTHER Settings
        $tmp_table_size = new Text('tmp_table_size',array(
            'id' => 'tmp_table_size',
            'name' => 'tmp_table_size',
        ));
        $max_heap_table_size = new Text('max_heap_table_size',array(
            'id' => 'max_heap_table_size',
            'name' => 'max_heap_table_size',
        ));
        $query_cache_type = new Select('query_cache_type',array(
            'default' => __('Default'),
            'on' => __('On'),
            'off' => __('Off'),
            'demand' => __('Demand'),
        ),array(
            'id' => 'query_cache_type',
            'name' => 'query_cache_type',
        ));
        $query_cache_size = new Text('query_cache_size',array(
            'id' => 'query_cache_size',
            'name' => 'query_cache_size',
        ));
        $max_connections = new Text('max_connections',array(
            'id' => 'max_connections',
            'name' => 'max_connections',
        ));
        $thread_cache_size = new Text('thread_cache_size',array(
            'id' => 'thread_cache_size',
            'name' => 'thread_cache_size',
        ));
        $table_open_cache = new Text('table_open_cache',array(
            'id' => 'table_open_cache',
            'name' => 'table_open_cache',
        ));
        $open_files_limit = new Text('open_files_limit',array(
            'id' => 'open_files_limit',
            'name' => 'open_files_limit',
        ));

        $data = $options['data'];
        $allow_remote_connection->setDefault($data['allow_remote_connection']);
        $file_per_table->setDefault($data['file_per_table']);
        $flush_method->setDefault($data['flush_method']);
        $slow_query_log->setDefault($data['slow_query_log']);
        $query_cache_type->setDefault($data['query_cache_type']);

        $log_error->setAttribute('value', $data['log_error']);

        if($mysql_8) {
            $query_cache_type->setAttribute("disabled", "disabled");
            $query_cache_size->setAttribute("disabled", "disabled");
        }
        if($data['buffer_pool_size'] == "default") {
            $buffer_pool_size->setAttribute('placeholder',__('Default'));
        }
        else {
            $buffer_pool_size->setAttribute('value', $data['buffer_pool_size']);
        }
        if($data['log_file_size'] == "default") {
            $log_file_size->setAttribute('placeholder',__('Default'));
        }
        else {
            $log_file_size->setAttribute('value', $data['log_file_size']);
        }
        if($data['file_per_table'] == "default") {
            $file_per_table->setAttribute('placeholder',__('Default'));
        }
        else {
            $file_per_table->setAttribute('value', $data['file_per_table']);
        }

        if($data['slow_query_log'] == "on") {
            if(($data['slow_query_log_file'] == "default") || ($data['slow_query_log_file'] == null)) {
                $slow_query_log_file->setAttribute('placeholder',__('Default'));
            }
            else {
                $slow_query_log_file->setAttribute('value', $data['slow_query_log_file']);
            }
        }
        else {
            $slow_query_log_file->setAttribute('disabled','disabled');
        }

        if($data['tmp_table_size'] == "default") {
            $tmp_table_size->setAttribute('placeholder',__('Default'));
        }
        else {
            $tmp_table_size->setAttribute('value', $data['tmp_table_size']);
        }
        if($data['max_heap_table_size'] == "default") {
            $max_heap_table_size->setAttribute('placeholder',__('Default'));
        }
        else {
            $max_heap_table_size->setAttribute('value', $data['max_heap_table_size']);
        }
        if($data['query_cache_size'] == "default") {
            $query_cache_size->setAttribute('placeholder',__('Default'));
        }
        else {
            $query_cache_size->setAttribute('value', $data['query_cache_size']);
        }
        if($data['max_connections'] == "default") {
            $max_connections->setAttribute('placeholder',__('Default'));
        }
        else {
            $max_connections->setAttribute('value', $data['max_connections']);
        }
        if($data['thread_cache_size'] == "default") {
            $thread_cache_size->setAttribute('placeholder',__('Default'));
        }
        else {
            $thread_cache_size->setAttribute('value', $data['thread_cache_size']);
        }
        if($data['table_open_cache'] == "default") {
            $table_open_cache->setAttribute('placeholder',__('Default'));
        }
        else {
            $table_open_cache->setAttribute('value', $data['table_open_cache']);
        }
        if($data['open_files_limit'] == "default") {
            $open_files_limit->setAttribute('placeholder',__('Default'));
        }
        else {
            $open_files_limit->setAttribute('value', $data['open_files_limit']);
        }


        $this->add($allow_remote_connection);
        $this->add($buffer_pool_size);
        $this->add($log_file_size);
        $this->add($file_per_table);
        $this->add($flush_method);

        $this->add($log_error);
        $this->add($slow_query_log);
        $this->add($slow_query_log_file);


        $this->add($tmp_table_size);
        $this->add($max_heap_table_size);
        $this->add($query_cache_type);
        $this->add($query_cache_size);
        $this->add($max_connections);
        $this->add($thread_cache_size);
        $this->add($table_open_cache);
        $this->add($open_files_limit);
    }
}