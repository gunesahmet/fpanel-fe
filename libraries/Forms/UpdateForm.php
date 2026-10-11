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
 * Class SystemTimeForm
 * @package FPanel\Forms
 */
class UpdateForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
        $this->statisticsFormElements($options);
    }

    /**
     * @param $options
     */
    private function formElements($options)
    {
        $auto_update = new Check('auto_update',array(
            'id' => 'auto_update',
            'name' => 'auto_update',
        ));

        $random_time = new Check('random_time', array(
            'id' => 'random_time',
            'name' => 'random_time',
        ));

        $update_time = new Text('update_time',array(
            'id' => 'update_time',
            'name' => 'update_time',
            'value' => $options['settings']['update_time']
        ));

        $update_server = new Text('update_server',array(
            'id' => 'update_server',
            'name' => 'update_server',
//            'value' => $options['settings']['update_server'],
        ));

        if($options['settings']['auto_update'] == ACTIVE)
            $auto_update->setAttribute('checked','checked');

        if($options['settings']['random_time'] == ACTIVE) {
            $random_time->setAttribute('checked','checked');
            $update_time->setAttribute('disabled','disabled');
        }

        $this->add($update_server);
        $this->add($random_time);
        $this->add($auto_update);
        $this->add($update_time);
    }

    private function statisticsFormElements($options)
    {
        $statistics_random = new Check('statistics_random', array(
            'id' => 'statistics_random',
            'name' => 'statistics_random',
        ));

        $statistics_time = new Text('statistics_time',array(
            'id' => 'statistics_time',
            'name' => 'statistics_time',
            'value' => $options['settings']['statistics_time']
        ));

        if($options['settings']["statistics_random"] == ACTIVE) {
            $statistics_random->setAttribute('checked','checked');
            $statistics_time->setAttribute('disabled','disabled');
        }

        $this->add($statistics_random);
        $this->add($statistics_time);
    }


}