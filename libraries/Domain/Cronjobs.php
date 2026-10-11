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
 */namespace FPanel\Domain;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\ScheduledTasks;
use FPanel\Models\Servers;
use FPanel\Process\Cron as CronManager;

/**
 * Class Cronjobs
 * @package FPanel\Cronjobs
 */
class Cronjobs extends Library
{
    protected $ftpuser;
    protected $ftpuser_passwd;
    protected $job;
    protected $minute;
    protected $hour;
    protected $day;
    protected $week;
    protected $month;
    protected $timestamp;
    public $task_id;

    public function saveDomain(): bool
    {
        try {
            $php_script = "0";
            if($this->getPost("php_script") == ON) {
                $php_script = "1";
                $this->setPost("command", $this->getPost("php_command"));
            }

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
                $status = "1";
                $ftp_users = FtpUsers::findFirstById($this->getPost("ftp_user_id"));
                if($ftp_users) {
                    $this->ftpuser = $ftp_users->username;
                    $this->ftpuser_passwd = $this->crypto->bdecode($ftp_users->password);
                    $this->timestamp = time();
                    $this->job = $this->getPost("command");
                    $this->minute = $this->getPost("minute");
                    $this->hour = $this->getPost("hour");
                    $this->day = $this->getPost("day");
                    $this->week = $this->getPost("week");
                    $this->month = $this->getPost("month");
                }
                else {
                    $this->error_messages['ftpuser'] = __('Ftp user not found');
                    return false;
                }
            }
            else {
                $status = "0";
            }
            $command =  $this->getPost("command");
            $parameters = array(
                1 => $command,
                2 => $php_script,
                3 => $this->getPost("domain_id")
            );
            $ctask = ScheduledTasks::findFirst([
                "command = ?1 AND php_script = ?2 and domain_id = ?3",
                "bind" => $parameters
            ]);
            if(!$ctask) {
                $task = new ScheduledTasks();
                $task->assign(array(
                    "id"        => $this->env->createUuid(),
                    'minute'    => $this->minute,
                    'hour'    => $this->getPost("hour"),
                    'day'    => $this->getPost("day"),
                    'week'    => $this->getPost("week"),
                    'month'    => $this->getPost("month"),
                    'command'    => $command,
                    'php_script'    => $php_script,
                    'description'    => $this->getPost("description"),
                    'timestamp'    => $this->timestamp,
                    'status'    => $status,
                    'domain_id'    => $this->getPost("domain_id"),
                    'server_id'    => $this->getPost("server_id"),
                    'type'    => 'domain'
                ));
                if(!$task->create()) {
                    foreach ($task->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Cronjobs->saveDomain",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
                if($status == "1") {
                    $this->task_id = $task->id;
                    $this->createDomainTaskCommand();
                }
                return true;
            }
            else {
                $this->error_messages['command'] = __('This task already exists.');
            }
            return false;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Cronjobs->saveDomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function updateDomain(): bool
    {
        try {
            $php_script = "0";
            if($this->getPost("php_script") == "on") {
                $php_script = "1";
                $this->setPost("command", $this->getPost("php_command"));
            }

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


            $task_id = $this->getPost("task_id");
            if($this->getPost("status") == ON) {
                $status = "1";
                $ftp_users = FtpUsers::findFirstById($this->getPost("ftp_user_id"));
                if($ftp_users) {
                    $this->ftpuser = $ftp_users->username;
                    $this->ftpuser_passwd = $this->crypto->bdecode($ftp_users->password);
                }
                else {
                    $this->error_messages['ftpuser'] = __('Ftp user not found');
                    return false;
                }
            }
            else {
                $status = "0";
            }

            $task = ScheduledTasks::findFirstById($task_id);

            if($task) {
                $this->timestamp = $this->getPost("old-timestamp");
                $this->job = $this->getPost("command");
                $this->minute = $this->getPost("minute");
                $this->hour = $this->getPost("hour");
                $this->day = $this->getPost("day");
                $this->week = $this->getPost("week");
                $this->month = $this->getPost("month");
            }
            $task->assign(array(
                'minute'    => $this->getPost("minute"),
                'hour'    => $this->getPost("hour"),
                'day'    => $this->getPost("day"),
                'month'    => $this->getPost("month"),
                'week'    => $this->getPost("week"),
                'command'    => $this->getPost("command"),
                'description'    => $this->getPost("description"),
                'timestamp'    => $this->getPost("old-timestamp"),
                'php_script'    => $php_script,
                'status'    => $status,
                'domain_id'    => $this->getPost("domain_id")
            ));
            if(!$task->update()) {
                foreach ($task->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Cronjobs->updateDomain",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            if($status == "1") {
                $this->removeDomainTaskCommand($this->getPost("old-timestamp"));
                $this->createDomainTaskCommand();
            }
            return true;
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Cronjobs->saveDomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function deleteDomain(): bool
    {
        try {
            $task_id = $this->getPost("task_id");
            $domain_id = $this->getPost("domain_id");
            $task = ScheduledTasks::findFirst(array("id='$task_id'", "domain_id" => "$domain_id"));

            if($task) {
                $timestamp = $task->timestamp;
                $ftp_users = FtpUsers::findFirstById($this->getPost("ftp_user_id"));
                if($ftp_users) {
                    $this->ftpuser = $ftp_users->username;
                }
                else {
                    $this->error_messages['ftpuser'] = __('Ftp user not found');
                    return false;
                }

                if(!$task->delete()) {
                    foreach($task->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    $this->log->error([
                        "Module" => "Cronjobs->deleteDomain",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
                $this->removeDomainTaskCommand($timestamp);
                return true;
            }
            else {
                return false;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "Cronjobs->saveDomain",
                "data" => $this->getPost(),
                "exception" => $e->getMessage(),
            ]);
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function deleteAll(FtpUsers $ftp_user, Domains $domains): bool
    {
        $task = ScheduledTasks::find("domain_id = '$domains->id'");
        if(!empty($task)) {
            if($ftp_user) {
                $this->removeAllCurl($ftp_user->username, $domains->web_server_id);
            }
            if(!$task->delete()) {
                foreach($task->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Cronjobs->deleteAll",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    public function listDomainTasks($domain_id = null, $nested = false): array
    {
        $datatable = new Datatable();
        if($nested) {
            $delete_button = $datatable->buttons("tasks/delete/$1/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("tasks/edit/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('tasks/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('tasks/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select(array(
            'scheduled_tasks.id as task_id',
            'scheduled_tasks.command',
            'scheduled_tasks.description',
            'scheduled_tasks.status',
            'scheduled_tasks.id',
            'scheduled_tasks.php_script',
        ))
            ->from("scheduled_tasks")
            ->where("scheduled_tasks.domain_id = '$domain_id'")
            ->andWhere("type = 'domain'")
            ->searchColumn(array('scheduled_tasks.command'))
            ->editColumn('task_id','<input type="checkbox" value="$1" class="checkbox-blue" name="tasks[]">', 'task_id')
            ->addColumn('actions', $edit_button.$delete_button,'scheduled_tasks.id');

        if($nested) {
            $datatable->editColumn('scheduled_tasks.command','<a href="'. "tasks/edit/$1/domain/$domain_id\">$2</a>", 'scheduled_tasks.id, scheduled_tasks.command');
        }
        else {
            $datatable->editColumn('scheduled_tasks.command','<a href="'. 'tasks/edit/$1">$2</a>', 'scheduled_tasks.id, scheduled_tasks.command');
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

            if ($value[5] == '1'){
                $data['aaData'][$key][4] = '<span class="label label-success"> '. __('PHP') .' </span>';
            }
            else{
                $data['aaData'][$key][4] = '<span class="label label-danger"> '. __('Command') .' </span>';
            }
            $data['aaData'][$key][5] = $value[6];
            unset($data['aaData'][$key][6]);
        }

        return $data;
    }

    private function createDomainTaskCommand()
    {
        $cron = new CronManager();
        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $cron->setServerId($domain->web_server_id);

        if (($this->has("server_id")) && ($this->getPost("server_id") != "1")) {
            $cron->setServerId($this->getPost("server_id"));
        }
        $server = Servers::findFirstById($domain->web_server_id);
        $panel_os_type = $this->env->getOs($server->os);

        if($panel_os_type != WINDOWS) {
            if($this->getPost("php_script") == "on") {
                $command = "php -q " . VHOST_DIR . "/$domain->name/vhost/httpdoc/" . $this->getPost("command");
            }
            else {
                $command = "/usr/local/fpanel/core/sbin/fsh --user=" . $this->ftpuser . " --domain=".$domain->name . " --command=".$this->crypto->staticEncode($this->getPost("command"));
            }
            $cron->makeJail([
                "domain" => $domain->name
            ]);
        }
        else {
            $command = $this->getPost("command");
        }

        $params = array(
            'user'      => $this->ftpuser,
            'job'       => $this->crypto->staticEncode($command),
            'minute'    => $this->minute,
            'hour'      => $this->getPost("hour"),
            'day'       => $this->getPost("day"),
            'week'      => $this->getPost("week"),
            'month'     => $this->getPost("month"),
            "domain"    => $domain->name,
            'prefix'    => $domain->prefix,
            'timestamp' => $this->timestamp,
        );
        if($panel_os_type == WINDOWS) {
            $params["password"] = $this->crypto->staticEncode($this->ftpuser_passwd);
        }
        $cron->create($params);
    }

    private function removeDomainTaskCommand($timestamp)
    {
        $cron = new CronManager();
        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $cron->setServerId($domain->web_server_id);
        $params = array(
            'user' => $this->ftpuser,
            'timestamp'    => $timestamp,
        );
        $cron->remove($params);
    }

    private function removeAllCurl(string $ftp_user, string $server_id)
    {
        $cron = new CronManager();
        $cron->setServerId($server_id);
        $params = array(
            "user" => $ftp_user,
        );
        $cron->removeUser($params);
    }
}