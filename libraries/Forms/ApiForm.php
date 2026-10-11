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

use FPanel\Models\Users;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class ApiForm
 * @package FPanel\Forms
 */
class ApiForm extends Form
{
    public function initialize($entity = null, $options)
    {
        $this->formElements();
        $this->userElements($options);
    }

    private function formElements()
    {
        $api_status = new Check('api_status', array(
            'id' => 'api_status',
            'name' => 'api_status',
        ));

        if($this->api_status == true) {
            $api_status->setAttribute('checked','checked');
        }
        $this->add($api_status);

        $this->add(new Text('api_url', array(
            'id' => 'api_url',
            'name' => 'api_url',
            'value' => $this->api_link
        )));
        $this->add(new Text('username', array(
            'id' => 'username',
            'name' => 'username',
            'value' => $this->api_user
        )));
        $password = trim($this->api_passwd);
        $this->add(new Password('password', array(
            'id' => 'password',
            'name' => 'password',
        )));
    }

    private function userElements($options)
    {
        if(!empty($options['user_list'])) {

            $user_list = new Select('user_list', $options['user_list'], array(
                'data-selectable' => __('Available Users'),
                'data-selection' => __('Selected Users'),
                'id' => 'multiselect-optgroup',
                'name' => 'user_list[]',
                'multiple' => 'multiple'
            ));

            $active_users = Users::find("api_status = '1'");
            $a_users = array();
            foreach($active_users->toArray() as $active_user) {
                $a_users[] = $active_user['id'];
            }
            $user_list->setDefault($a_users);
            $this->add($user_list);
        }
    }
}