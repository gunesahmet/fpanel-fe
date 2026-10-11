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
 * Class TasksForm
 * @package FPanel\Forms
 */
class TasksForm extends Form
{

	public function initialize($entity = null, $options)
	{
		$this->formElements($options);
	}

	/**
	 * @param $options
	 */
	private function formElements($options)
	{
		// Status
		$task_status = new Check('task_status', array(
			'id' => 'task_status',
			'name' => 'task_status',
		));

		$php_script = new Check('php_script', array(
			'id' => 'php_script',
			'name' => 'php_script',
		));

        $minute_array = array(
            '*/5'   => __('Every %s% Minutes',['s' => '5']). '(*/5)',
            '*/10'   => __('Every %s% Minutes',['s' => '10']). '(*/10)',
            '*/15'   => __('Every %s% Minutes',['s' => '15']). '(*/15)',
            '*/30'   => __('Every %s% Minutes',['s' => '30']). '(*/30)',
            '0 '     => '0',
        );
        for ($i=1; $i<=59; $i++) {
            $minute_array[$i] = $i;
        }


        $day_array = array(
            '*'   => __('Every Day').' (*)',
            '*/2'   => __('Every in 2 Day').' (*/2)',
            '*/5'   => __('Every in 5 Day').' (*/5)',
        );
        for ($i=1; $i<=31; $i++) {
            $day_array[$i] = $i;
        }

        $minute = new Text('minute', array(
            'id' => 'minute',
            'name' => 'minute',
        ));
        $hour = new Text('hour', array(
            'id' => 'hour',
            'name' => 'hour',
        ));
        $day = new Text('day', array(
            'id' => 'day',
            'name' => 'day',
        ));
        $month = new Text('month', array(
            'id' => 'month',
            'name' => 'month',
        ));
        $week = new Text('week', array(
            'id' => 'week',
            'name' => 'week',
        ));
        $php_command = new Text('php_command', array(
            'id' => 'php_command',
            'name' => 'php_command',
        ));
        $command = new Text('command', array(
            'id' => 'command',
            'name' => 'command',
        ));
        $description = new Text('description', array(
            'id' => 'description',
            'name' => 'description',
        ));

        $minute_select = new Select('minute_select', $minute_array,array('id' => 'minute_select',
            'name' => 'minute_select',
        ));

        $hour_select = new Select('hour_select',array(
            '*'     => __('Every Hour'). ' (*) ',
            '*/2'   => __('Every').' 2 ' .__('Hours'). '(*/2)',
            '*/3'   => __('Every').' 3 ' .__('Hours'). '(*/3)',
            '*/4'   => __('Every').' 4 ' .__('Hours'). '(*/4)',
            '*/5'   => __('Every').' 5 ' .__('Hours'). '(*/5)',
            '*/6'   => __('Every').' 6 ' .__('Hours'). '(*/6)',
            '*/12'  => __('Every').' 12 '.__('Hours'). '(*/12)',
            '0 '     => __('Once at').' 00:00 (0)',
            '1'     => __('Once at').' 01:00 (1)',
            '2'     => __('Once at').' 02:00 (2)',
            '3'     => __('Once at').' 03:00 (3)',
            '4'     => __('Once at').' 04:00 (4)',
            '5'     => __('Once at').' 05:00 (5)',
            '6'     => __('Once at').' 06:00 (6)',
            '7'     => __('Once at').' 07:00 (7)',
            '8'     => __('Once at').' 08:00 (8)',
            '9'     => __('Once at').' 09:00 (9)',
            '10'    => __('Once at').' 10:00 (10)',
            '11'    => __('Once at').' 11:00 (11)',
            '12'    => __('Once at').' 12:00 (12)',
            '13'    => __('Once at').' 13:00 (13)',
            '14'    => __('Once at').' 14:00 (14)',
            '15'    => __('Once at').' 15:00 (15)',
            '16'    => __('Once at').' 16:00 (16)',
            '17'    => __('Once at').' 17:00 (17)',
            '18'    => __('Once at').' 18:00 (18)',
            '19'    => __('Once at').' 19:00 (19)',
            '20'    => __('Once at').' 20:00 (20)',
            '21'    => __('Once at').' 21:00 (21)',
            '22'    => __('Once at').' 22:00 (22)',
            '23'    => __('Once at').' 23:00 (23)',
        ),array('id' => 'hour_select',
            'name' => 'hour_select',
        ));

        $day_select = new Select('day_select', $day_array,array('id' => 'day_select',
            'name' => 'day_select',
        ));

        $month_select = new Select('month_select',array(
            '*'     => __('Every Month'). ' (*)',
            '*/2'     => __('Every in'). ' 2 '.__('Month').' (*/2)',
            '*/3'     => __('Every in'). ' 3 '.__('Month').' (*/3)',
            'jan'   => __('January'),
            'feb'   => __('Febuary'),
            'mar'   => __('March'),
            'apr'   => __('April'),
            'may'   => __('May'),
            'jun'   => __('June'),
            'jul'   => __('July'),
            'aug'   => __('August'),
            'sep'   => __('September'),
            'oct'   => __('October'),
            'nov'   => __('November'),
            'dec'   => __('December')
        ),array('id' => 'month_select',
            'name' => 'month_select',
        ));

        $week_select = new Select('week_select',array(
            '*'     => __('Every Week'). ' (*)',
            '*/2'     => __('Every in'). ' 2 '.__('Week').' (*/2)',
            '*/3'     => __('Every in'). ' 3 '.__('Week').' (*/3)',
            'mon'   => __('Monday'),
            'tue'   => __('Tuesday'),
            'wed'   => __('Wednesday'),
            'thu'   => __('Thursday'),
            'fri'   => __('Friday'),
            'sat'   => __('Saturday'),
            'sun'   => __('Sunday'),
        ),array('id' => 'week_select',
            'name' => 'week_select',
        ));


        if($options['action'] == "edit") {
            if($options['task_data']['status'] == ACTIVE) {
                $task_status->setAttribute('checked','checked');
            }
            if($options['task_data']['php_script'] == ACTIVE) {
                $php_script->setAttribute('checked','checked');
            }
            $minute->setAttribute('value', $options['task_data']['minute']);
            $minute_select->setDefault($options['task_data']['minute']);

            $hour->setAttribute('value', $options['task_data']['hour']);
            $hour_select->setDefault($options['task_data']['hour']);

            $day->setAttribute('value', $options['task_data']['day']);
            $day_select->setDefault($options['task_data']['day']);

            $month->setAttribute('value', $options['task_data']['month']);
            $month_select->setDefault($options['task_data']['month']);

            $week->setAttribute('value', $options['task_data']['week']);
            $week_select->setDefault($options['task_data']['week']);

            $php_command->setAttribute('value', $options['task_data']['command']);
            $command->setAttribute('value', $options['task_data']['command']);
            $description->setAttribute('value', $options['task_data']['description']);

        }
        else {
            $default = array(
                'minute'    => "*/5",
                'hour'    => '*',
                'day'    => '*',
                'week'    => '*',
                'month'    => '*',
            );
            $task_status->setAttribute('checked','checked');
            $php_script->setAttribute('checked','checked');

            $minute->setAttribute('value', $default['minute']);
            $minute_select->setDefault($default['minute']);

            $hour->setAttribute('value', $default['hour']);
            $hour_select->setDefault($default['hour']);

            $day->setAttribute('value', $default['day']);
            $day_select->setDefault($default['day']);

            $month->setAttribute('value', $default['month']);
            $month_select->setDefault($default['month']);

            $week->setAttribute('value', $default['week']);
            $week_select->setDefault($default['week']);
        }

        $this->add($task_status);
        $this->add($php_script);
        $this->add($minute);
        $this->add($minute_select);
        $this->add($hour);
        $this->add($hour_select);
        $this->add($day);
        $this->add($day_select);
        $this->add($month);
        $this->add($month_select);
        $this->add($week);
        $this->add($week_select);
        $this->add($php_command);
        $this->add($command);
        $this->add($description);

	}

}