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

use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;


/**
 * Class FtpForm
 * @package FPanel\Forms
 */
class FtpForm extends Form
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
        $timeout_idle = new Text('timeout_idle',array(
            'id' => 'timeout_idle',
            'name' => 'timeout_idle',
        ));

        $timeout_no_transfer = new Text('timeout_no_transfer',array(
            'id' => 'timeout_no_transfer',
            'name' => 'timeout_no_transfer',
        ));

        $maxinstances = new Text('maxinstances',array(
            'id' => 'maxinstances',
            'name' => 'maxinstances',
        ));

        $max_clients = new Text('max_clients',array(
            'id' => 'max_clients',
            'name' => 'max_clients',
        ));

        $welcome_message = new Text('welcome_message',array(
            'id' => 'welcome_message',
            'name' => 'welcome_message',
        ));

        $welcome_msg = (isset($options['data']['welcome_message']) == true ? $options['data']['welcome_message'] : '');
        $timeout_idle->setAttribute('value', $options['data']['timeout_idle']);
        $timeout_no_transfer->setAttribute('value', $options['data']['timeout_no_transfer']);
        $maxinstances->setAttribute('value', $options['data']['maxinstances']);
        $max_clients->setAttribute('value', $options['data']['max_clients']);
        $welcome_message->setAttribute('value', $welcome_msg);

        $this->add($timeout_idle);
        $this->add($timeout_no_transfer);
        $this->add($maxinstances);
        $this->add($welcome_message);
        $this->add($max_clients);

    }


}