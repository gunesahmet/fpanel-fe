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
 */namespace FPanel\Server;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\ScheduledTasks;
use FPanel\Process\Cron as CronManager;

/**
 * Class Cronjobs
 * @package FPanel\Server
 */
class Cronjobs extends Library
{
    protected $ftpuser;
    protected $job;
    protected $minute;
    protected $hour;
    protected $day;
    protected $week;
    protected $month;
    protected $timestamp;

    public function listServerTasks($server_id = null, $system_type = null): array
    {
        $datatable = new Datatable();
        if($system_type == CLOUD) {
            $delete_button = $datatable->buttons("cron/delete/$1/server/$server_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("cron/edit/$1/server/$server_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('cron/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('cron/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array(
            'scheduled_tasks.id as task_id',
            'scheduled_tasks.command',
            'scheduled_tasks.description',
            'scheduled_tasks.status',
            'scheduled_tasks.id'
        ))
            ->from("scheduled_tasks")
            ->where("scheduled_tasks.server_id = '$server_id'")
            ->andWhere("type = '1'")
            ->searchColumn(array('scheduled_tasks.command'))
            ->addColumn('actions', $edit_button.$delete_button,'scheduled_tasks.id');

        if($system_type == CLOUD) {
            $datatable->where("`scheduled_tasks`.server_id = '$server_id'");
            $datatable->editColumn('scheduled_tasks.command','<a href="'. "cron/edit/$1/server/$server_id\">$2</a>", 'scheduled_tasks.id, scheduled_tasks.command');
        }
        else {
            $datatable->editColumn('scheduled_tasks.command','<a href="'. 'cron/edit/$1">$2</a>', 'scheduled_tasks.id, scheduled_tasks.command');
        }

        $data = $datatable->unsetColumns('scheduled_tasks.id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            if ($value[3] == '1'){
                $data['aaData'][$key][3] = '<span class="label label-success"> '. __('Active') .' </span>';
            }
            else{
                $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Passive') .' </span>';
            }
            $data['aaData'][$key][4] = $value[5];
            unset($data['aaData'][$key][0]);
            unset($data['aaData'][$key][5]);
            $data['aaData'][$key] = array_values($data['aaData'][$key]);
        }

        return $data;

    }

    public function saveServer(): bool
    {
        $required = array(
            array(
                'field' => 'minute',
                'item'  => $this->getPost("minute"),
                'message'   => __('Minute is required')
            ),
            array(
                'field' => 'hour',
                'item'  => $this->getPost("hour"),
                'message'   => __('Hour is required')
            ),
            array(
                'field' => 'day',
                'item'  => $this->getPost("day"),
                'message'   => __('Day is required')
            ),
            array(
                'field' => 'week',
                'item'  => $this->getPost("week"),
                'message'   => __('Week is required')
            ),
            array(
                'field' => 'month',
                'item'  => $this->getPost("month"),
                'message'   => __('Month is required')
            ),
            array(
                'field' => 'command',
                'item'  => $this->getPost("command"),
                'message'   => __('Command is required')
            ),
        );
        $status = $this->validation->Required($required)
            ->CronMinuteHourDay($this->getPost("minute"),'minute')
            ->CronMinuteHourDay($this->getPost("hour"),'hour',__('Please enter valid hour'))
            ->CronMinuteHourDay($this->getPost("day"),'day',__('Please enter valid day'))
            ->CronWeek($this->getPost("week"),'week',__('Please enter valid week'))
            ->CronMonth($this->getPost("month"),'month',__('Please enter valid month'))
            ->Validate();

        if(!$status) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        if($this->getPost("status") == ON) {
            $status = ACTIVE;
            $this->ftpuser = "root";
        }
        else {
            $status = PASSIVE;
        }
        $this->minute = $this->getPost("minute");
        $this->timestamp = time();
        $task = new ScheduledTasks();
        $task->assign(array(
            "id"            => $this->env->createUuid(),
            'minute'        => $this->minute,
            'hour'          => $this->getPost("hour"),
            'day'           => $this->getPost("day"),
            'week'          => $this->getPost("week"),
            'month'         => $this->getPost("month"),
            'command'       => $this->getPost("command"),
            'description'   => $this->getPost("description"),
            'timestamp'     => $this->timestamp,
            'status'        => $status,
            'type'          => SERVER_CRON,
            'server_id'     => $this->getPost("server_id"),
        ));
        if(!$task->create()) {
            foreach ($task->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        if($status == "1") {
            $this->createServerTaskCurl($this->getPost("server_id"));
        }
        return true;
    }

    public function updateServer(): bool
    {
        $required = array(
            array(
                'field' => 'minute',
                'item'  => $this->getPost("minute"),
                'message'   => __('Minute is required')
            ),
            array(
                'field' => 'hour',
                'item'  => $this->getPost("hour"),
                'message'   => __('Hour is required')
            ),
            array(
                'field' => 'day',
                'item'  => $this->getPost("day"),
                'message'   => __('Day is required')
            ),
            array(
                'field' => 'week',
                'item'  => $this->getPost("week"),
                'message'   => __('Week is required')
            ),
            array(
                'field' => 'month',
                'item'  => $this->getPost("month"),
                'message'   => __('Month is required')
            ),
            array(
                'field' => 'command',
                'item'  => $this->getPost("command"),
                'message'   => __('Command is required')
            ),
        );
        $this->validation->Required($required)
            ->CronMinuteHourDay($this->getPost("minute"),'minute')
            ->CronMinuteHourDay($this->getPost("hour"),'hour',__('Please enter valid hour'))
            ->CronMinuteHourDay($this->getPost("day"),'day',__('Please enter valid day'))
            ->CronWeek($this->getPost("week"),'week',__('Please enter valid week'))
            ->CronMonth($this->getPost("month"),'month',__('Please enter valid month'));

        if(!$this->validation->Validate()) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        $task_id = $this->getPost("task_id");
        if($this->getPost("status") == ON) {
            $status = ACTIVE;
            $this->ftpuser = "root";
        }
        else {
            $status = PASSIVE;
        }
        $task = ScheduledTasks::findFirstById($task_id);

        if($task) {
            $this->timestamp = $task->timestamp;
            $this->job = $this->getPost("command");
            $this->minute = $this->getPost("minute");
            $this->hour = $this->getPost("hour");
            $this->day = $this->getPost("day");
            $this->week = $this->getPost("week");
            $this->month = $this->getPost("month");
        }
        $timestamp = $task->timestamp;
        $task->assign(array(
            'minute'        => $this->getPost("minute"),
            'hour'          => $this->getPost("hour"),
            'day'           => $this->getPost("day"),
            'month'         => $this->getPost("month"),
            'week'          => $this->getPost("week"),
            'command'       => $this->getPost("command"),
            'description'   => $this->getPost("description"),
            'timestamp'     => $timestamp,
            'status'        => $status,
        ));
        if(!$task->update()) {
            foreach ($task->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        if($status == ACTIVE) {
            $this->removeServerTaskCurl($this->getPost("server_id"), $timestamp);
            $this->createServerTaskCurl($this->getPost("server_id"));
        }
        return true;
    }

    public function deleteServer(): bool
    {
        $task_id = $this->getPost("task_id");
        $server_id = $this->getPost("server_id");
        $task = ScheduledTasks::findFirst("id = '$task_id' AND server_id = '$server_id'");

        if($task) {
            $this->timestamp = $task->timestamp;
            $this->ftpuser = "root";
            if(!$task->delete()) {
                foreach($task->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Cronjobs->deleteServer",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            $this->removeServerTaskCurl($server_id, $task->timestamp);
            return true;
        }
        else {
            return false;
        }
    }

    private function createServerTaskCurl($server_id)
    {
        $cron = new CronManager();
        $cron->setServerId($server_id);
        $params = array(
            'user'      => "root",
            'job'       =>  $this->crypto->staticEncode($this->getPost("command")),
            'minute'    => $this->minute,
            'hour'      => $this->getPost("hour"),
            'day'       => $this->getPost("day"),
            'week'      => $this->getPost("week"),
            'month'     => $this->getPost("month"),
            'timestamp' => $this->timestamp,
        );
        $cron->create($params);
    }

    private function removeServerTaskCurl($server_id, $timestamp)
    {
        $cron = new CronManager();
        $cron->setServerId($server_id);

        $params = array(
            'user'      => "root",
            'timestamp' => $timestamp,
        );
        $cron->remove($params);
    }
}