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

use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class DatabaseForm
 * @package FPanel\Forms
 */
class DatabaseForm extends Form
{
	/**
	 * @param null $entity
	 * @param $options
	 */
	public function initialize($entity = null, $options = null)
	{

        $this->databaseFormElements($options);
        $this->databaseServers($options);
	}

    /**
     * @param null $options
     */
    private function databaseFormElements($options = null)
	{

        // Database Name
        $db_name_form = new Text('database_name', array(
            'id' => 'database_name',
            'name' => 'database_name',
        ));

		// Database User Name
        $db_username = new Text('database_username', array(
            'id' => 'database_username',
            'name' => 'database_username',
        ));

        $db_user_password = new Password('database_password', array(
            'id' => 'password',
            'name' => 'password',
        ));

        $add_remote_host = new Text('add_remote_host', array(
            'id' => 'add_remote_host',
            'name' => 'add_remote_host',
        ));

        if($options['action'] == "edit") {
            $database_data = $options['db_data'];
            $db_name_form->setAttribute('value', $database_data['name']);
            $db_name_form->setAttribute('readonly', 'true');
            $db_username->setAttribute('value', $database_data['login']);

            if(isset($options['remote_connection']) && $options['remote_connection'] == "on") {
                $remote_hosts = array();
                if(!empty($options['remote_hosts'])) {
                    $remote_hosts = array();

                    foreach($options['remote_hosts'] as $host) {
                        $remote_hosts[$host] = $host;
                    }
                }
                $remote_hosts = new Select('remote_hosts', $remote_hosts, array(
                    'id' => 'remote_hosts',
                    'name' => 'remote_hosts[]',
                    'multiple' => 'multiple',
                ));
            }
            else {
                $remote_hosts = new Select('remote_hosts',array(), array(
                    'id' => 'remote_hosts',
                    'name' => 'remote_hosts[]',
                    'multiple' => 'multiple',
                ));
                $remote_hosts->setAttribute('disabled','disabled');
                $add_remote_host->setAttribute('disabled','disabled');
            }
            $this->add($remote_hosts);

        }
        else {
            $db_username->setAttribute('value', \Phalcon\Text::random(\Phalcon\Text::RANDOM_ALPHA,3).\Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM,5));
            $db_user_password->setAttribute('value', \Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM));
        }

        $this->add($db_name_form);
        $this->add($db_username);
        $this->add($db_user_password);
        $this->add($add_remote_host);

	}

    public function databaseServers($options = null)
    {
        if($options['action'] == "edit") {
            $database_data = $options['db_data'];
        }

        $database_server_list = $options['database_server_list'];

        $database_types = array();
        $mssql_db_servers = array();
        $mysql_db_servers = array();
        $pgsql_db_servers = array();

//        $this->debug->debug($database_server_list, false);
        foreach ($database_server_list as $value) {
            if (($value['last_error'] == 'no_error') || ($value['last_error'] == null) || ($value['last_error'] == 0)) {
                $database_types[]= $value['type'];
                switch($value['type']) {
                    case "mysql":
                        $mysql_db_servers[$value['id']] = $value['host'].':'.$value['port'];
                        break;
                    case "mssql":
                        $mssql_db_servers[$value['id']] = $value['host'].':'.$value['port'];
                        break;
                    case "pgsql":
                        $pgsql_db_servers[$value['id']] = $value['host'].':'.$value['port'];
                        break;
                }
            }
        }

        $database_type_list = array();
        foreach ($database_types as $value) {
            if ( $value == "mysql") {
                $database_type_list['mysql'] = "MySQL";
            }
            if ( $value == "pgsql") {
                $database_type_list['pgsql'] = "PostgreSQL";
            }
            if ( $value == "mssql") {
                $database_type_list['mssql'] = "Microsoft SQL Server";
            }
            if ( $value == "mongodb") {
                $database_type_list['mongodb'] = "MongoDB";
            }
        }

        // MySQL Database Server
        $mysql_database_server = new Select(
            "mysql_database_server",
            $mysql_db_servers,
            array(
                'id' => 'database_server',
                'name' => 'database_server',
            ));
        if($options['action'] == "edit") {
            $mysql_database_server->setDefault($database_data['database_server_id']);
            $mysql_database_server->setAttribute('disabled', 'disabled');
        }

        // PostgreSQL Database Server
        $pgsql_database_server = new Select(
            "pgsql_database_server",
            $pgsql_db_servers,
            array(
                'id' => 'database_server',
                'name' => 'database_server',
            ));
        if($options['action'] == "edit") {
            $pgsql_database_server->setDefault($database_data['database_server_id']);
            $pgsql_database_server->setAttribute('disabled', 'disabled');
        }

        $mssql_database_server = new Select(
            "mssql_database_server",
            $mssql_db_servers,
            array(
                'id' => 'database_server',
                'name' => 'database_server',
            ));
        if($options['action'] == "edit") {
            $mssql_database_server->setDefault($database_data['database_server_id']);
            $mssql_database_server->setAttribute('disabled', 'disabled');
        }


        // Database Type
        $database_type = new Select(
            "database_server_type",
            $database_type_list,
            array(
                'id' => 'database_server_type',
                'name' => 'database_server_type',
            ));
        if($options['action'] == "edit") {
            $database_type->setDefault(DB_TYPES_ARR[$database_data['type']]);
            $database_type->setAttribute('disabled', 'disabled');
        }

        $this->add($mysql_database_server);
        $this->add($mssql_database_server);
        $this->add($pgsql_database_server);
        $this->add($database_type);
    }
}