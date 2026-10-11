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
 */namespace FPanel\MailServer;

use FPanel\Library\Library;
use FPanel\Process\Mail as MailMng;
use FPanel\Process\Mail as MailProcess;
use FPanel\Process\MailServer as MailServerProcess;

/**
 * Class Mail
 * @package FPanel\Server
 */
class Mail extends Library
{
    use Postfix, MailEnable, HMail;

    public function saveMailSettings(bool $default, $server_id): bool
    {
        $status = false;
        if($this->getPost("server_type") == POSTFIX) {
            $status = $this->savePostfixSettings($default, $server_id);
        }
        else if($this->getPost("server_type") == MAILENABLE) {
            $status = $this->saveMailEnableSettings($default, $server_id);
        }
        else if($this->getPost("server_type") == HMAIL) {
            $status = $this->saveHmailSettings($default, $server_id);
        }
        if($status)
            $this->generalSettingsCurl($server_id);

        return $status;
    }

    public function updateMailSettings($server_id): bool
    {
        $status = false;
        if($this->getPost("server_type") == POSTFIX) {
            $status = $this->savePostfixSettings(false, $server_id);
        }
        else if($this->getPost("server_type") == MAILENABLE) {
            $status = $this->saveMailEnableSettings(false, $server_id);
        }
        else if($this->getPost("server_type") == HMAIL) {
            $status = $this->saveHMailSettings(false, $server_id);
        }

        if($status)
            $this->generalSettingsCurl($server_id);

        return $status;
    }

    protected function generalSettingsCurl($server_id)
    {
        if($this->getPost("server_type") == POSTFIX) {
            $this->postfixCommand($server_id);
        }
        else if($this->getPost("server_type") == MAILENABLE) {
            $this->mailenableCommand($server_id);
        }
        else if($this->getPost("server_type") == HMAIL) {
            $this->hmailCommand($server_id);
        }
    }

    public function queueList(string $server_id, $system_type): array
    {
        $mailqueue = new MailMng();
        if($system_type == CLOUD) {
            $mailqueue->setServerId($server_id);
        }
        $params = array(
            'start' => $this->request->getPost('iDisplayStart',null,'0'),
            'end' => abs($this->request->getPost('iDisplayStart',null,'0')+$this->request->getPost('iDisplayLength',null,'25')),
        );
        $queue = $mailqueue->queue($params);

        if($this->env->isJson($queue)) {
            $queue = json_decode($queue,true);
        }
        else {
            $queue = array();
        }

        $queue_list = array();

        if(!empty($queue)) {
            foreach($queue as $mail) {
                if(isset($this->server_id))
                    $action_buttons = '<a data-id="confirm" data-url="'. $this->uri->url_encrypt('mailserver/removequeue/'.$mail['queue_id'].'/server/'.$server_id) .'" class="text text-danger"><i class="fa fa-times"></i> '. __('Delete') .'</a>';
                else
                    $action_buttons = '<a data-id="confirm" data-url="' . $this->uri->url_encrypt('mailserver/removequeue/'.$mail['queue_id']) . '" class="text text-danger"><i class="fa fa-times"></i> '. __('Delete') .'</a>';

                if(PANEL_OS_TYPE == WINDOWS) {
                    $arrival_time = $this->date->getTimeStamp($mail["arrival_time"]);
                }
                else {
                    $arrival_time = $mail["arrival_time"];
                }
                $age = $this->env->secondsToTime(abs(time() - $arrival_time));
                $subject = $mail['subject'];
                $size = $this->env->convert($mail["message_size"]);
                $date = $this->env->convertDate($arrival_time);

                $result =  array(
                    '0' => '<input type="checkbox" class="checkbox-blue" id="'.$mail['queue_id'].'" value="'.$mail['queue_id'].'" name="queue[]">', // input
                    '1' => $mail['queue_name'], // Sender
                    '2' => $mail['sender'], // Sender
                    '3' => $subject, // Subject
                    '4' => $date, // Date
                    '5' => $age, // Age
                    '6' => $size,
                    '7' => $action_buttons,
                    '8' => $mail['recipients'], // Recipient
                );

                $queue_list['aaData'][] = $result;
            }
        }
        else {
            $queue_list['iTotalRecords'] = "0";
            $queue_list['iTotalDisplayRecords'] = "0";
            $queue_list['aaData'] = array();
        }

        return $queue_list;
    }

    public function clearQueue($server_id, $system_type)
    {
        $mailqueue = new MailProcess();
        if($system_type == CLOUD) {
            $mailqueue->setServerId($server_id);
        }
        $mailqueue->clearQueue();
    }

    public function removeQueueMsg($server_id, $system_type, $id)
    {
        $mailqueue = new MailProcess();
        if($system_type == CLOUD) {
            $mailqueue->setServerId($server_id);
        }
        $mailqueue->removeQueueMsg($id);
    }

    public function retryQueue($server_id, $system_type)
    {
        $mailqueue = new MailProcess();
        if($system_type == CLOUD) {
            $mailqueue->setServerId($server_id);
        }
        $mailqueue->retryQueue();
    }

    public function retrieve($server_id, $system_type)
    {
        $mailsrvmng = new MailServerProcess();
        if($system_type == CLOUD) {
            $mailsrvmng->setServerId($server_id);
        }
        return $mailsrvmng->retrieve();
    }
}
