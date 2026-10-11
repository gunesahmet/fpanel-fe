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
 */namespace FPanel\Api\Domain;

use FPanel\Domain\Cronjobs;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\Domains;
use FPanel\Models\ScheduledTasks;
use Phalcon\Mvc\User\Component;

class Task extends Component
{
    private $error_messages = [];

    public function tasks($process, $params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        if(!$this->env->checkDomainOwner($params['domain_name'], $client_id)) {
            $this->error_messages['domain_name'] = "You dont have access for this domain";
            return $this->error_messages;
        }
        switch($process) {
            case "list":
                return $this->taskList($params);
            case "create":
                return $this->create($params);
            case "delete":
                return $this->delete($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function taskList($params): array
    {
        $qbuilder = new QueryBuilder();
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        $result = $qbuilder->select([
            "scheduled_tasks.command",
            "scheduled_tasks.description",
            "scheduled_tasks.status",
            "scheduled_tasks.php_script",
            "scheduled_tasks.timestamp",
        ])
            ->from('scheduled_tasks')
            ->where([
                'conditions' => "scheduled_tasks.domain_id = ?1",
                'bind' => [1 => $domains->id]
            ])
            ->find();

        foreach ($result as $k => $v) {
            $result[$k]["status"] = $v["status"] ? STATUSES[ACTIVE] : STATUSES[PASSIVE];
            $result[$k]["php_script"] = $v["php_script"] == "1";
        }

        return $result;
    }

    public function create($params)
    {
        $requeired_keys = [
            "status",
            "minute",
            "hour",
            "day",
            "week",
            "month",
            "is_php",
            "command",
            "description",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        $post_data = array(
            // Task Status
            'ftp_user_id'           => $domains->ftp_user_id,
            'domain_id'             => $domains->id,
            'domain_name'           => $domain_name,
            'status'                => $params['status'] ? ON : OFF,
            'minute'                => $params['minute'],
            'hour'                  => $params['hour'],
            'day'                   => $params['day'],
            'week'                  => $params['week'],
            'month'                 => $params['month'],
            'command'               => $params['command'],
            'php_command'           => $params['command'],
            'description'           => isset($params['description']) ? trim($params['description']) : "",
            'server_id'             => $domains->web_server_id,
            'php_script'            => $params['is_php'] ? ON : OFF,
        );
        $task = new Cronjobs();
        $task->setPosts($post_data);
        $task_save_status = $task->saveDomain();
        if($task_save_status) {
            return "Success";
        }
        else {
            foreach ($task->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    public function delete($params)
    {
        $requeired_keys = [
            "timestamp",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }
        $timestamp = $params["timestamp"];

        $tasks = ScheduledTasks::findFirst("domain_id = '$domains->id' AND timestamp = '$timestamp'");

        if(!$tasks) {
            return [
                "command" => __("Task not found"),
            ];
        }

        $task = new Cronjobs();
        $task->setPosts(array(
            'ftp_user_id'  => $domains->ftp_user_id,
            'domain_name' => $domain_name,
            'domain_id' => $domains->id,
            'task_id' => $tasks->id,
        ));
        $task_status = $task->deleteDomain();

        if($task_status) {
            return "Success";
        }
        return $task->getMessages();
    }
}