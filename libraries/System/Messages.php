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
 */namespace FPanel\System;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\Messages as SystemMessages;

class Messages extends Library
{
    public function messageList(): array
    {
        $datatable = new Datatable();
        $delete_button = $datatable->buttons('messages/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        $edit_button = $datatable->buttons('messages/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');

        $datatable->select(array('messages.id',
            'messages.title',
            'messages.description',
            'messages.created_date',
            'messages.expire_date',
            'messages.type',
            'messages.status')
        )
            ->from("messages")
            ->addColumn('actions', $edit_button.$delete_button,'messages.id')
            ->searchColumn(array(
                'messages.title',
                'messages.description',
                'messages.type',
            ));
        $datatable->editColumn('messages.title','<a href="'. 'messages/edit/$1">$2</a>', 'messages.id, messages.title');


        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            if ($value[6] == '1'){
                $data['aaData'][$key][6] = '<span class="label label-success"> '. __('Active') .' </span>';
            }
            else {
                $data['aaData'][$key][6] = '<span class="label label-danger"> '. __('Passive') .' </span>';
            }
            $data['aaData'][$key][3] = $this->date->getOnlyDate($value[3], 'd/m/Y');
            $data['aaData'][$key][4] = $this->date->getOnlyDate($value[4],'d/m/Y');

            switch($value[5]) {
                case 0:
                    $data['aaData'][$key][5] = '<span class="label label-info"> '. __('Announcement') .' </span>';
                    break;
                case 1:
                    $data['aaData'][$key][5] = '<span class="label label-danger"> '. __('Important') .' </span>';
                    break;
                case 2:
                    $data['aaData'][$key][5] = '<span class="label label-primary"> '. __('Welcome') .' </span>';
                    break;
            }

            unset($data['aaData'][$key][0]);
            $data['aaData'][$key] = array_values($data['aaData'][$key]);

        }
        return $data;
    }

    public function createMessage(): bool
    {
        $this->validation->Required(array(
            array(
                'field' => 'title',
                'item' => $this->getPost("title"),
                'message' => __('Title is required')
            ),
            array(
                'field' => 'expire_date',
                'item' => $this->getPost("expire_date"),
                'message' => __('Expire Time is required')
            ),
            array(
                'field' => 'type',
                'item' => $this->getPost("type"),
                'message' => __('Message Type is required')
            ),
            array(
                'field' => 'message_content',
                'item' => $this->getPost("message"),
                'message' => __('Message is required')
            )
        ));
        $this->validation->Numeric($this->getPost("expire_date"),'expire_date')
            ->InclusionIn($this->getPost("type"),'type',array('announcement','important','welcome'),__('Please select a type'));
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        $created_date = $this->date->dateTime();
        if($this->getPost("status") != ON) {
            $status = PASSIVE;
        }
        else {
            $status = ACTIVE;
        }
        $date = $this->env->expireDateForDay($this->date->getTimeStamp($created_date), $this->getPost("expire_date"));
        $message = new SystemMessages();
        $assign = array(
            'title' => $this->getPost("title"),
            'message' => $this->getPost("message"),
            'created_date' => $created_date,
            'expire_date' => $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date']),
            'type' => $this->getPost("type"),
            'description' => $this->getPost("description"),
            'status' => $status,
        );
        $message->assign($assign);
        if (!$message->create()) {
            foreach ($message->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Messages->createMessage",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function editMessage(): bool
    {
        $this->validation->Required(array(
            array(
                'field' => 'title',
                'item' => $this->getPost("title"),
                'message' => __('Title is required')
            ),
            array(
                'field' => 'expire_date',
                'item' => $this->getPost("expire_date"),
                'message' => __('Expire Time is required')
            ),
            array(
                'field' => 'type',
                'item' => $this->getPost("type"),
                'message' => __('Message Type is required')
            ),
            array(
                'field' => 'message_content',
                'item' => $this->getPost("message"),
                'message' => __('Message is required')
            )
        ));
        $this->validation->Numeric($this->getPost("expire_date"),'expire_date')
            ->InclusionIn($this->getPost("type"),'type',array('announcement','important','welcome'),__('Please select a type'));
        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }


        $id = $this->getPost("id");
        $message = SystemMessages::findFirstById($id);


        if($this->getPost("title") != null) {
            $message->title = $this->getPost("title");
        }
        if($this->getPost("message") != null) {
            $message->message = $this->getPost("message");
        }
        if($this->getPost("type") != null) {
            $message->type = $this->getPost("type");
        }
        if($this->getPost("description") != null) {
            $message->description = $this->getPost("description");
        }
        if($this->getPost("status") != ON) {
            $status = PASSIVE;
        }
        else {
            $status = ACTIVE;
        }
        $message->status = $status;

        if($this->getPost("expire_date") != null) {
            $date = $this->env->expireDateForDay($this->date->getTimeStamp($message->created_date), $this->getPost("expire_date"));
            $message->expire_date = $this->date->tsToDate($date['expire_date']);
        }

        if (!$message->update()) {
            foreach ($message->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Messages->editMessage",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }



        return true;
    }

    public function deleteMessage(): bool
    {
        $id = $this->getPost("id");
        $message = SystemMessages::findFirstById($id);
        if($message) {
            if(!$message->delete()) {
                foreach($message->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Messages->deleteMessage",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

}
