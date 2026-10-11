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
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class ResourceManagementForm
 * @package FPanel\Forms
 */
class ResourceManagementForm extends Form
{
    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        $this->serviceplanLimitsElements($options);

        $this->serviceplanPermissionElements($options);

    }

    /**
     * @param $options
     */
    public function serviceplanLimitsElements($options)
    {
        $domain_data = $options['domain_data'];

        $overuse = new Check("overuse", array(
            'id' => 'overuse',
            'name' => 'overuse',
        ));

        if((isset($domain_data['overuse'])) && ($domain_data['overuse']== ACTIVE)) {
            $overuse->setAttribute('checked','checked');
        }
        if($options['action'] != "edit") {
            $overuse->setAttribute('disabled','disabled');
        }
        $this->add($overuse);

        $disk_limit = new Text("disk_limit", array(
            'id' => "disk_limit",
            'name' => "disk_limit",
        ));
        $traffic_limit = new Text("traffic_limit", array(
            'id' => "traffic_limit",
            'name' => "traffic_limit",
        ));
        $subdomain_limit = new Text("subdomain_limit", array(
            'id' => "subdomain_limit",
            'name' => "subdomain_limit",
        ));
        $domain_alias_limit = new Text("domain_alias_limit", array(
            'id' => "domain_alias_limit",
            'name' => "domain_alias_limit",
        ));
        $email_box_size_limit = new Text("email_box_size_limit", array(
            'id' => "email_box_size_limit",
            'name' => "email_box_size_limit",
        ));
        $email_box_limit = new Text("email_box_limit", array(
            'id' => "email_box_limit",
            'name' => "email_box_limit",
        ));
        $postgresql_limit = new Text("postgresql_limit", array(
            'id' => "postgresql_limit",
            'name' => "postgresql_limit",
        ));
        $mssql_limit = new Text("mssql_limit", array(
            'id' => "mssql_limit",
            'name' => "mssql_limit",
        ));
        $mysql_limit = new Text("mysql_limit", array(
            'id' => "mysql_limit",
            'name' => "mysql_limit",
        ));
        $database_limit = new Text("database_limit", array(
            'id' => "database_limit",
            'name' => "database_limit",
        ));
        $database_size_limit = new Text("database_size_limit", array(
            'id' => "database_size_limit",
            'name' => "database_size_limit",
        ));
        $expire_time_limit = new Text ('expire_time_limit', array(
            'id' => 'expire_time_limit',
            'name' => 'expire_time_limit',
        ));

        $disk = new Check("disk", array(
            'id' => "disk",
            'name' => "disk_limit",
            'value' => UNLIMITED
        ));
        $traffic = new Check("traffic", array(
            'id' => "traffic",
            'name' => "traffic_limit",
            'value' => UNLIMITED
        ));
        $subdomain = new Check("subdomain", array(
            'id' => "subdomain",
            'name' => "subdomain_limit",
            'value' => UNLIMITED
        ));
        $domain_alias = new Check("domain_alias", array(
            'id' => "domain_alias",
            'name' => "domain_alias_limit",
            'value' => UNLIMITED
        ));
        $email_box = new Check("email_box", array(
            'id' => "email_box",
            'name' => "email_box_limit",
            'value' => UNLIMITED
        ));
        $email_box_size = new Check("email_box_size", array(
            'id' => "email_box_size",
            'name' => "email_box_size_limit",
            'value' => UNLIMITED
        ));
        $postgresql = new Check("postgresql", array(
            'id' => "postgresql",
            'name' => "postgresql_limit",
            'value' => UNLIMITED
        ));
        $mssql = new Check("mssql", array(
            'id' => "mssql",
            'name' => "mssql_limit",
            'value' => UNLIMITED
        ));
        $mysql = new Check("mysql", array(
            'id' => "mysql",
            'name' => "mysql_limit",
            'value' => UNLIMITED
        ));
        $database = new Check("database", array(
            'id' => "database",
            'name' => "database_limit",
            'value' => UNLIMITED
        ));
        $database_size = new Check("database_size", array(
            'id' => "database_size",
            'name' => "database_size_limit",
            'value' => UNLIMITED
        ));
        $expire_time = new Check('expire_time', array(
            'id' => 'expire_time',
            'name' => 'expire_time_limit',
            'value' => UNLIMITED,
        ));

        if($options['action'] == "edit") {

            // Disk
            if($domain_data['disk_limit'] == UNLIMITED) {
                $disk->setAttribute("checked","checked");
                $disk_limit->setAttribute('disabled',"disabled");
            }
            else {
                $disk_limit->setAttribute('value', $domain_data["disk_limit"]/1024/1024);
            }

            // Traffic
            if($domain_data['traffic_limit'] == UNLIMITED) {
                $traffic->setAttribute("checked","checked");
                $traffic_limit->setAttribute('disabled',"disabled");
            }
            else {
                $traffic_limit->setAttribute('value', $domain_data["traffic_limit"]/1024/1024);
            }

            // Subdomain
            if($domain_data['subdomain_limit'] == UNLIMITED) {
                $subdomain->setAttribute("checked","checked");
                $subdomain_limit->setAttribute('disabled',"disabled");
            }
            else {
                $subdomain_limit->setAttribute('value', $domain_data["subdomain_limit"]);
            }

            // Domain Alias
            if($domain_data['domain_alias_limit'] == UNLIMITED) {
                $domain_alias->setAttribute("checked","checked");
                $domain_alias_limit->setAttribute('disabled',"disabled");
            }
            else {
                $domain_alias_limit->setAttribute('value', $domain_data["domain_alias_limit"]);
            }

            // Email Box
            if($domain_data['email_box_limit'] == UNLIMITED) {
                $email_box->setAttribute("checked","checked");
                $email_box_limit->setAttribute('disabled',"disabled");
            }
            else {
                $email_box_limit->setAttribute('value', $domain_data["email_box_limit"]);
            }

            // Email Box Size
            if($domain_data['email_box_size_limit'] == UNLIMITED) {
                $email_box_size->setAttribute("checked","checked");
                $email_box_size_limit->setAttribute('disabled',"disabled");
            }
            else {
                $email_box_size_limit->setAttribute('value', $domain_data["email_box_size_limit"]/1024/1024);
            }


            // Database
            if($domain_data['database_limit'] == UNLIMITED) {
                $database->setAttribute("checked","checked");
                $database_limit->setAttribute('disabled',"disabled");

            }
            else {
                $database_limit->setAttribute('value', $domain_data["database_limit"]);
            }

            // Database Size
            if($domain_data['database_size_limit'] == UNLIMITED) {
                $database_size->setAttribute("checked","checked");
                $database_size_limit->setAttribute('disabled',"disabled");

            }
            else {
                $database_size_limit->setAttribute('value', $domain_data["database_size_limit"]/1024/1024);
            }

            if(!is_numeric($domain_data['expire_date'])) {
                $expire_time->setAttribute('checked','checked');
                $expire_time_limit->setAttribute('disabled','disabled');
            }
            else {
                $expire_date = $domain_data['expire_date'];
                $created_date = $domain_data['created_date'];
                $year = abs($created_date - $expire_date)/60/60/24/365;
                $expire_time_limit->setAttribute('value', $year);
            }
        }

        $this->add($disk_limit);
        $this->add($traffic_limit);
        $this->add($subdomain_limit);
        $this->add($domain_alias_limit);
        $this->add($email_box_limit);
        $this->add($email_box_size_limit);
        $this->add($postgresql_limit);
        $this->add($mssql_limit);
        $this->add($mysql_limit);
        $this->add($database_limit);
        $this->add($database_size_limit);
        $this->add($expire_time_limit);

        $this->add($disk);
        $this->add($traffic);
        $this->add($subdomain);
        $this->add($domain_alias);
        $this->add($email_box);
        $this->add($email_box_size);
        $this->add($postgresql);
        $this->add($mssql);
        $this->add($mysql);
        $this->add($database);
        $this->add($database_size);
        $this->add($expire_time);
    }

    /**
     * @param $options
     */
    public function serviceplanPermissionElements($options)
    {
        $permission_items = array(
            'access_panel' => 'access_panel',
            'man_subdomain' => 'access_subdomain',
            'man_domain_alias' => 'access_domain_alias',
            'man_email_alias' => 'access_email_alias',
            'man_hosting_settings' => 'access_hosting_settings',
            'man_task' => 'access_task',
            'man_dns' => 'access_dns',
            'man_ssl' => 'access_ssl',
            'man_backup' => 'access_backup',
            'man_database' => 'access_database',
            'man_email' => 'access_email',
            'man_protect_dir' => 'access_protect_dir',
        );

        $domain_data = $options['domain_data'];

        foreach ($permission_items as $key => $value) {
            if($options['action'] == "edit") {
                if((isset($domain_data[$key])) && ($domain_data[$key] == ALLOWED)) {

                    $this->add(new Check(
                        $value,
                        array(
                            'name' => $value,
                            'id' => $value,
                            'checked' => 'checked',
                        )
                    ));
                }
                else {

                    $this->add(new Check(
                        $value,
                        array(
                            'name' => $value,
                            'id' => $value,
                        )
                    ));
                }
            }
            else {
                if((isset($domain_data[$key])) && ($domain_data[$key] == ALLOWED)) {
                    $this->add(new Check(
                        $value,
                        array(
                            'name' => $value,
                            'id' => $value,
                            'checked' => 'checked',
                            'disabled' => 'disabled'
                        )
                    ));
                }
                else {
                    $this->add(new Check(
                        $value,
                        array(
                            'name' => $value,
                            'id' => $value,
                            'disabled' => 'disabled'
                        )
                    ));
                }
            }
        }

    }

}