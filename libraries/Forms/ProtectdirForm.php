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
 * Class ProtectdirForm
 * @package FPanel\Forms
 */
class ProtectdirForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
	{
        if($options['page'] == "protectdir")
            $this->protectDirFormElements($options);
        else
            $this->userFormElements($options);
	}

    /**
     * @param $options
     */
    private function protectDirFormElements($options)
	{
        $domains = array($options['domain_name']);
        foreach ($options['domain_list'] as $value) {
            $domains[$value['id']] = $value['full_name'];
        }
        // Domains
        $domains = new Select(
            'domain_name',
            $domains,
            array(
                'id' => 'domain_name',
                'name' => 'domain_name',
            )
        );
        // Directory Path
        $path = new Text('directory_path', array(
            'id' => 'directory_path',
            'name' => 'directory_path',
        ));
        // Title of Header
        $header= new Text('title_of_header', array(
            'id' => 'title_of_header',
            'name' => 'title_of_header',
        ));

        if($options['action'] != "edit") {
            // Directory Path
            $this->add(new Text('directory_path', array(
                'id' => 'directory_path',
                'name' => 'directory_path',
            )));
            // Title of Header
            $this->add(new Text('title_of_header', array(
                'id' => 'title_of_header',
                'name' => 'title_of_header',
            )));

        }
        else {
            $path->setAttribute("value", $options['path']);
            $header->setAttribute("value", $options['header']);
            if($options['selected_domain'] != null)
                $domains->setDefault($options['selected_domain']);
            $domains->setAttribute('disabled','disabled');
        }
        $this->add($domains);
        $this->add($path);
        $this->add($header);
	}

    /**
     * @param $options
     */
    private function userFormElements($options)
    {
        $directory = new Text('directory', array(
            'id' => 'directory',
            'name' => 'directory',
            'class' => 'form-control form-control-sm',
            'disabled' => 'disabled',
            'value' => $options['path'],
        ));
        $username = new Text('username', array(
            'id' => 'username',
            'name' => 'username',
        ));
        $password = new Password('password', array(
            'id' => 'password',
            'name' => 'password',
        ));

        if($options['action'] == "edit") {
            // Directory
            $directory->setAttribute('value', $options['path']);
            $username->setAttribute('value', $options['username']);
//            $password->setAttribute('value', $options['password']);
        }

        $this->add($directory);
        $this->add($username);
        $this->add($password);
    }

}