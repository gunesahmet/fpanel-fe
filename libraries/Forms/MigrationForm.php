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
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;


/**
 * Class MigrationForm
 * @package FPanel\Forms
 */
class MigrationForm extends Form
{

    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        switch($options['page']) {
            case "create":
                $this->createFormElements($options);
                break;
            case "start":
                $this->createConfigs($options);
        }
    }

    /**
     * @param $options
     */
    private function createFormElements($options)
    {
        $migration_type = new Select('migration_type', array(
                'fpanel' => __('FPanel'),
                'plesk' => __('Plesk Panel'),
                'cpanel' => __('CPanel'),
            ), array(
                'id' => 'migration_type',
                'name' => 'migration_type'
            ));

        $system = new Select('system', array(
            WINDOWS => __('Windows'),
            LINUX => __('Linux'),
        ), array(
            'id' => 'system',
            'name' => 'system'
        ));

        $ip_address = new Text('ip_address',array(
            'id' => 'ip_address',
            'name' => 'ip_address'
        ));

        $username = new Text('username',array(
            'id' => 'username',
            'name' => 'username',
        ));

        $password = new Password('password',array(
            'id' => 'password',
            'name' => 'password',
        ));

        $panel_uname = new Text('panel_uname',array(
            'id' => 'panel_uname',
            'name' => 'panel_uname',
        ));

        $panel_upass = new Password('panel_upass',array(
            'id' => 'panel_upass',
            'name' => 'panel_upass',
        ));

        $description = new TextArea('description',array(
            'id' => 'description',
            'name' => 'description',
        ));

        if($options['action'] == "edit") {
            $migration_data = $options['migration_data'];
            $migration_type->setDefault($migration_data['server_type']);
            $system->setDefault($migration_data['system']);
            $ip_address->setAttribute('value', $migration_data['ip_address']);
            $username->setAttribute('value', $migration_data['root']);
            $password->setAttribute('value', $migration_data['root_pass']);
            $panel_uname->setAttribute('value', $migration_data['admin']);
            $panel_upass->setAttribute('value', $migration_data['admin_pass']);
            $description->setAttribute('value', $migration_data['description']);
        }

        $this->add($migration_type);
        $this->add($system);
        $this->add($ip_address);
        $this->add($username);
        $this->add($password);
        $this->add($panel_uname);
        $this->add($panel_upass);
        $this->add($description);
    }

    /**
     * @param $options
     */
    private function createConfigs($options)
    {
         $skip_global_settings = new Check('skip_global_settings',array(
            'id' => 'skip_global_settings',
            'name' => 'skip_global_settings',
        ));

        $skip_domain_datas = new Check('skip_domain_datas',array(
            'id' => 'skip_domain_datas',
            'name' => 'skip_domain_datas',
        ));

        $replace_exists_accounts = new Check('replace_exists_accounts',array(
            'id' => 'replace_exists_accounts',
            'name' => 'replace_exists_accounts',
        ));

        $replace_exists_domains = new Check('replace_exists_domains',array(
            'id' => 'replace_exists_domains',
            'name' => 'replace_exists_domains',
        ));

        $this->add($skip_global_settings);
        $this->add($skip_domain_datas);
        $this->add($replace_exists_accounts);
        $this->add($replace_exists_domains);

    }


}