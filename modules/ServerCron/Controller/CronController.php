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
 */namespace ServerCron\Controller;

use FPanel\Server\Cronjobs;
use FPanel\Forms\TasksForm;
use FPanel\Globals\Globals;
use FPanel\Models\ScheduledTasks;
use FPanel\Models\Servers;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class CronController
 * @package ServerCron\Controller
 */
class CronController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;

    /**
     * @return bool|void
     */
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
        $params = $this->dispatcher->getParams();

        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "server":
                    $server_id = $params['server_id'];
                    $server = Servers::findFirstById($server_id);
                    if(!$server) {
                        $this->view->setVar('error_content',__('Server not found'));
                        $this->response->setStatusCode(404, __('Server not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $server_data = $server->toArray();
                        $this->server_id = $server_id;
                        $this->view->setVar('server_id', $server_id);
                        $this->breadcrumbs->add($server_data['name'],'server/dashboard/server/'.$server_id,' ');
                        $this->server_data = $server_data;
                    }
                    break;
                default:
                    $this->response->setStatusCode(404, 'Not Found');
                    $this->view->setVar('error_content',__('Unreconized url'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }

    }

    /**
     * index
     */
    public function indexAction()
    {
        $this->view->pick("servercron/index");
        $this->tag->appendTitle( __('Cronjob List'));

        $create_url = $this->uri->url_encrypt("cron/create");
        $list_url = $this->uri->url_encrypt("cron");
        if($this->system_data['system_type'] == CLOUD) {
            $this->view->setVar("server_id", $this->server_id);
            $create_url = $this->uri->url_encrypt("cron/create/server/$this->server_id");
            $list_url = $this->uri->url_encrypt("cron/server/$this->server_id");
        }

        $this->view->setVar("create_url", $create_url);
        $this->view->setVar("list_url", $list_url);
        $this->breadcrumbs->add(__('Cronjob List'),'','');

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $this->view->disable();
            $cronlist = new Cronjobs();
            $this->response->setJsonContent($cronlist->listServerTasks($this->server_id, $this->system_data['system_type']))->send();
        }
    }

    public function createAction()
    {
        $this->view->pick("servercron/create");
        $this->tag->appendTitle( __('Create Cronjob'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Cronjob List'),'cron/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt('cron/server/'.$server_id);
            $post_url = $this->uri->url_encrypt('cron/create/server/'.$server_id);
        }
        else {
            $redirect_url = $this->uri->url_encrypt('cron');
            $this->breadcrumbs->add(__('Cronjob List'),'cron',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $post_url = $this->uri->url_encrypt('cron/create');
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Create Scheduled Task'),'','');


        $options = array(
            'action'  => 'create',
        );
        $this->view->form = new TasksForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $post_data = array(
                // Task Status
                'status'        => $this->request->getPost('task_status'),
                'minute'        => $this->request->getPost('minute'),
                'hour'          => $this->request->getPost('hour'),
                'day'      => $this->request->getPost('day'),
                'week'    => $this->request->getPost('week'),
                'month'  => $this->request->getPost('month'),
                'command'       => $this->request->getPost('command'),
                'description'       => $this->request->getPost('description'),
                'server_id'       => $server_id,
            );
            $task = new Cronjobs();
            $task->setPosts($post_data);
            $status = $task->saveServer();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($task->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Cronjob is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function editAction($id)
    {
        $this->view->pick("servercron/edit");
        $this->tag->appendTitle( __('Edit Cronjob'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Cronjob List'),'cron/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("cron/server/$server_id");
            $post_url = $this->uri->url_encrypt("cron/edit/$id/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $this->breadcrumbs->add(__('Cronjob List'),'cron',' ');
            $redirect_url = $this->uri->url_encrypt("cron");
            $post_url = $this->uri->url_encrypt("cron/edit/$id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Scheduled Task'),'','');


        if($id != null) {
            $task = ScheduledTasks::findFirst(array("id='$id'", "server_id" => "$server_id"));
            $this->view->setVar('task_id', $id);

            if($task) {
                $task_data = $task->toArray();
            }
            else {
                $this->tag->appendTitle( __('Error 404 Scheduled Task Not Found') );
                $this->view->setVar("error_content", __('Error 404 Scheduled Task Not Found') );
                $this->response->setStatusCode(404, 'Not Found');
                $this->view->pick("errors/404");
                return true;
            }
        }
        else {
            $this->tag->appendTitle( __('Error 404 Scheduled Task Not Found') );
            $this->response->setStatusCode(404, 'Not Found');
            $this->view->setVar('error_content',__('Scheduled task not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $options = array(
            'action'  => 'edit',
            'task_data' => $task_data,
        );
        $this->view->form = new TasksForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $post_data = array(
                // Task Status
                'task_id'        => $id,
                'server_id'        => $server_id,
                'status'        => $this->request->getPost('task_status'),
                'minute'        => $this->request->getPost('minute'),
                'hour'          => $this->request->getPost('hour'),
                'day'      => $this->request->getPost('day'),
                'week'    => $this->request->getPost('week'),
                'month'  => $this->request->getPost('month'),
                'command'       => $this->request->getPost('command'),
                'description'       => $this->request->getPost('description'),
            );
            $task = new Cronjobs();
            $task->setPosts($post_data);
            $status = $task->updateServer();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($task->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Cronjob is updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    /**
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost()) && ($this->request->isAjax())) {
            $system_data = $this->system_data;
            if($system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }
            $task = new Cronjobs();
            $task->setPosts(array(
                'server_id' => $server_id,
                'task_id' => $id,
            ));
            $task_status = $task->deleteServer();
            $messages = Globals::messages();
            if ($task_status == true) {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Delete successful');
            }
            else {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Delete failed');
            }
            $this->response->setJsonContent($messages)->send();

        }
    }

}