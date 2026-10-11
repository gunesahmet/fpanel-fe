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
 * Class DomainaliasForm
 * @package FPanel\Forms
 */
class DomainaliasForm extends Form
{
    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
	{
		$this->formElements($options);

	}

    /**
     * @param null $options
     */
    private function formElements($options = null)
	{

        if(!empty($options)) {
            if($options['action'] == "edit") {
                // Domain Alias Name
                $this->add(new Text('alias_name', array(
                    'id' => 'alias_name',
                    'name' => 'alias_name',
                    'value' => $options['dom_alias_data']['alias_name'],
                )));

                // Activate Web Service
                if ($options['dom_alias_data']['web'] == true) {
                    $this->add(new Check('activate_web_service', array(
                        'id' => 'activate_web_service',
                        'name' => 'activate_web_service',
                        'checked'   => 'checked',
                    )));
                }
                else {
                    $this->add(new Check('activate_web_service', array(
                        'id' => 'activate_web_service',
                        'name' => 'activate_web_service',
                    )));
                }

                // Activate Mail Service
                if ($options['dom_alias_data']['mail'] == true) {
                    $this->add(new Check('activate_mail_service', array(
                        'id' => 'activate_mail_service',
                        'name' => 'activate_mail_service',
                        'checked'   => 'checked',
                    )));
                }
                else {
                    $this->add(new Check('activate_mail_service', array(
                        'id' => 'activate_mail_service',
                        'name' => 'activate_mail_service',
                    )));
                }

            }
        }
        else {
            // Domain Alias Name
            $this->add(new Text('alias_name', array(
                'id' => 'alias_name',
                'name' => 'alias_name',
            )));

            // Activate Web Service
            $this->add(new Check('activate_web_service', array(
                'id' => 'activate_web_service',
                'name' => 'activate_web_service',
                'checked' => 'checked',
            )));

            // Activate Mail Service
            $this->add(new Check('activate_mail_service', array(
                'id' => 'activate_mail_service',
                'name' => 'activate_mail_service',
            )));
        }


	}


}