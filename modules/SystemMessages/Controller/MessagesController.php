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
 */namespace SystemMessages\Controller;

use FPanel\Forms\MessageForm;
use FPanel\Globals\Globals;
use FPanel\Models\Messages as SystemMessages;
use FPanel\Models\Servers;
use FPanel\System\Messages;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class MessagesController
 * @package SystemMessages\Controller
 */
class MessagesController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;

    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('system');
        $this->user_data = $user_data;
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];

        $this->system_data = $user_data['system_data'];

        $this->view->setVar('system_items', $user_data);

        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        if($this->system_data["system_type"] == CLOUD) {
            $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
        }
        else {
            $this->breadcrumbs->add(__('System Dashboard'),'system',' ');
        }
    }
    /**
     * index
     */
    public function indexAction()
    {
        $this->breadcrumbs->add(__('System Messages'),'','');

        $data = $this->acl->run('system');
        $this->view->setVar("system_items", $data);
        $this->tag->appendTitle( __('System Messages List'));

        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $server_list = new Messages();
            $this->response->setJsonContent($server_list->messageList())->send();
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function createAction()
    {
        $this->breadcrumbs->add(__('System Messages'),'messages',' ');

        $this->breadcrumbs->add(__('Create System Message'),'','');

        $this->view->setVar('post_url', $this->uri->url_encrypt("messages/create"));

        $this->tag->appendTitle( __('Create New Message'));
        $options = array(
            'action' => 'create'
        );
        $this->view->form = new MessageForm(null, $options);

        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $post_data = array(
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'message' => $this->request->getPost('message_content'),
                'expire_date' => $this->request->getPost('expire_date'),
                'type' => $this->request->getPost('type'),
                'status' => $this->request->getPost('status'),
            );
            $message = new Messages();
            $message->setPosts($post_data);
            $status = $message->createMessage();
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($message->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('New Message saved'));
                    return $this->response->redirect($this->uri->url_encrypt("messages"),true);
                }
            }
        }
    }

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function editAction($id)
    {
        $this->breadcrumbs->add(__('System Messages'),'messages',' ');

        $this->breadcrumbs->add(__('Edit System Message'),'','');


        $this->view->setVar('post_url', $this->uri->url_encrypt("messages/edit/$id"));

        $this->tag->appendTitle( __('Edit System Message'));
        $this->view->setVar('id', $id);
        $message = SystemMessages::findFirstById($id);
        if($message) {
            $message_data = $message->toArray();
        }
        else {
            $this->view->setVar('error_content',__('Message not found'));
            $this->response->setStatusCode(404, __('Message not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $expire_date = abs($this->date->getTimeStamp($message_data['expire_date']) - $this->date->getTimeStamp($message_data['created_date']))/60/60/24;
        $options = array(
            'action'    => 'edit',
            'title'    => $message_data['title'],
            'description' => $message_data['description'],
            'message' => $message_data['message'],
            'type' => $message_data['type'],
            'status' => $message_data['status'],
            'expire_date' => $expire_date,
        );
        $this->view->form = new MessageForm(null, $options);

        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $post_data = array(
                'id'    => $id,
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'message' => $this->request->getPost('message_content'),
                'expire_date' => $this->request->getPost('expire_date'),
                'type' => $this->request->getPost('type'),
                'status' => $this->request->getPost('status'),
            );
            $message = new Messages();
            $message->setPosts($post_data);
            $status = $message->editMessage();
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($message->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Message updated'));
                    return $this->response->redirect($this->uri->url_encrypt("messages"),true);
                }
            }
        }
    }

    /**
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->acl->run('system');
        $this->view->disable();
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $post_data = array(
                'id'            => $id,
            );
            $message = new Messages();
            $message->setPosts($post_data);
            $message_status = $message->deleteMessage();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if ($message_status == false) {

                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
            }

            $this->response->setJsonContent($messages)->send();
        }
    }
}