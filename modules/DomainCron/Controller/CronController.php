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
 */
namespace DomainCron\Controller;

use FPanel\Domain\Cronjobs;
use FPanel\Domain\Services;
use FPanel\Forms\TasksForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use FPanel\Models\ScheduledTasks;
use FPanel\Models\Servers;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class CronController
 * @package DomainCron\Controller
 */
class CronController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $js_code;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('task');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        $params = $this->dispatcher->getParams();

        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "domain":
                    $domain_id = $params['domain_id'];
                    $domain = Domains::findFirstById($domain_id);
                    if(!$domain) {
                        $this->view->setVar('error_content',__('Domain not found'));
                        $this->response->setStatusCode(404, __('Domain not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($domain_id,DOMAIN_USER);
                        $domain_data = $domain->toArray();
                        $this->domain_id = $domain_id;
                        $web_server_id = $domain->web_server_id;
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$domain_id,' ');
                        $this->domain_data = $domain_data;
                        $this->view->setVar('client_type',DOMAIN_USER);
                        $client_menu = $this->sidebar->clientMenu(DOMAIN_USER, $domain_id);
                        $this->view->setVar("client_menu", $client_menu);
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        else {
            $domain_id = $user_data['user']['domain_info']['domain_id'];
            $web_server_id = $user_data['user']['domain_info']['web_server_id'];
        }
        $servers = Servers::findFirstById($web_server_id);
        $this->view->setVar("server_type", $this->env->getOs($servers->os));


        if(isset($this->domain_id)) {
            $redirect_url = $this->uri->url_encrypt("dashboard/domain/".$domain_id);
        }
        else {
            $redirect_url = $this->uri->url_encrypt("dashboard");
        }

        $domain_services = new Services();
        $domain_services->checkDomainService($domain_id,"web", $redirect_url);
    }

    /**
     * index
     */
    public function indexAction()
    {
        $this->view->pick("domaincron/index");
        $this->tag->appendTitle(__('Cronjob List'));
        $data = $this->user_data;
        $this->breadcrumbs->add(__('Cronjob List'),'','');


        if($this->request->isPost() && $this->request->isAjax()) {
            $this->view->disable();
            $nested = false;
            $cronjobs = new Cronjobs();
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $nested = true;
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
            }

            $this->response->setJsonContent($cronjobs->listDomainTasks($domain_id,$nested))->send();

        }
    }

    /**
     * create
     */
    public function createAction()
    {
        $this->view->pick("domaincron/create");
        $this->tag->appendTitle(__('Create Cronjob'));
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $ftp_user_id = $this->domain_data['ftp_user_id'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Cronjob List'),'tasks/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('tasks/domain/'.$domain_id);
            $server_id = $this->domain_data['web_server_id'];
            $post_url = $this->uri->url_encrypt('tasks/create/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $ftp_user_id = $data['user']['domain_info']['ftp_user_id'];
            $this->breadcrumbs->add(__('Cronjob List'),'tasks',' ');
            $redirect_url = $this->uri->url_encrypt('tasks');
            $server_id = $this->user_data['user']['domain_info']['web_server_id'];
            $post_url = $this->uri->url_encrypt('tasks/create');
        }
        $this->view->setVar('post_url', $post_url);


        $this->breadcrumbs->add(__('Create Cronjob'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $options = array(
            'action'  => 'create',
        );

        $this->view->form = new TasksForm(null, $options);
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                // Task Status
                'ftp_user_id'           => $ftp_user_id,
                'domain_id'             => $domain_id,
                'domain_name'           => $domain_name,
                'status'                => $this->request->getPost('task_status'),
                'minute'                => trim($this->request->getPost('minute')),
                'hour'                  => trim($this->request->getPost('hour')),
                'day'                   => trim($this->request->getPost('day')),
                'week'                  => trim($this->request->getPost('week')),
                'month'                 => trim($this->request->getPost('month')),
                'command'               => trim($this->request->getPost('command')),
                'php_command'           => trim($this->request->getPost('php_command')),
                'description'           => trim($this->request->getPost('description')),
                'php_script'            => trim($this->request->getPost('php_script')),
                'server_id'             => $server_id
            );
            $task = new Cronjobs();
            $task->setPosts($post_data);
            $task_save_status = $task->saveDomain();
            if(!$task_save_status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($task->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($task_save_status) {
                    $this->flashSession->success(__('Cronjob is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $task_id
     * @return bool|ResponseInterface
     */
    public function editAction($task_id)
    {
        $this->view->pick("domaincron/edit");
        $this->tag->appendTitle(__('Edit Cronjob'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $ftp_user_id = $this->domain_data['ftp_user_id'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Cronjob List'),'tasks/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('tasks/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt("tasks/edit/$task_id/domain/$domain_id");
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $ftp_user_id = $data['user']['domain_info']['ftp_user_id'];
            $this->breadcrumbs->add(__('Cronjob List'),'tasks',' ');
            $redirect_url = $this->uri->url_encrypt('tasks');
            $post_url = $this->uri->url_encrypt("tasks/edit/$task_id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Cronjob'),'','');

        $this->view->setVar('domain_name', $domain_name);

        if($task_id != null) {
            $task = ScheduledTasks::findFirst(array("id='$task_id'", "domain_id" => "$domain_id"));
            $this->view->setVar('task_id', $task_id);

            if($task) {
                $task_data = $task->toArray();
            }
            else {
                $this->view->setVar('error_content',__('Task not found'));
                $this->response->setStatusCode(404, __('Task not found'));
                $this->view->pick("errors/404");
                return true;
            }
        }
        else {
            $this->view->setVar('error_content',__('Task not found'));
            $this->response->setStatusCode(404, __('Task not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $options = array(
            'action'  => 'edit',
            'task_data' => $task_data,
        );
        $this->view->form = new TasksForm(null, $options);

        $this->view->setVar("task_data", $task_data);
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                // Task Status
                'ftp_user_id'        => $ftp_user_id,
                'task_id'        => $task_id,
                'domain_name'        => $domain_name,
                'domain_id'        => $domain_id,
                'php_script'        => $this->request->getPost('php_script'),
                'status'        => $this->request->getPost('task_status'),
                'minute'        => $this->request->getPost('minute'),
                'hour'          => $this->request->getPost('hour'),
                'day'      => $this->request->getPost('day'),
                'week'    => $this->request->getPost('week'),
                'month'  => $this->request->getPost('month'),
                'php_command'       => $this->request->getPost('php_command'),
                'command'       => $this->request->getPost('command'),
                'description'       => $this->request->getPost('description'),
                'old-timestamp'       => $task_data['timestamp'],
            );
            $task = new Cronjobs();
            $task->setPosts($post_data);
            $task_update_status = $task->updateDomain();
            if(!$task_update_status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($task->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($task_update_status) {
                    $this->flashSession->success(__('Cronjob is updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $task_id
     */
    public function deleteAction($task_id)
    {
        $this->view->disable();
        if ($this->request->isAjax() && $this->request->isPost()) {
            $messages = $this->delete($task_id);
            $this->response->setJsonContent($messages)->send();
        }
    }

    /**
     * @param $task_id
     * @return array
     */
    private function delete($task_id): array
    {
        $data = $this->user_data;
//        $url = $this->uri->url_encrypt("tasks");
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $ftp_user_id = $this->domain_data['ftp_user_id'];
//            $url = $this->uri->url_encrypt('tasks/domain/'.$domain_id);
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $this->user_data['user']['domain_info']['domain_id'];
            $ftp_user_id = $data['user']['domain_info']['ftp_user_id'];
        }

        $task = new Cronjobs();
        $task->setPosts(array(
            'ftp_user_id'        => $ftp_user_id,
            'domain_name' => $domain_name,
            'domain_id' => $domain_id,
            'task_id' => $task_id,
        ));
        $task_status = $task->deleteDomain();

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        if (!$task_status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Action failed');
        }

        return $messages;
    }

    public function multipleAction()
    {
        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if($this->request->getPost('id') == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select at least one');
            }
            else if($this->request->getPost("action") == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select an action');
            }
            else {
                switch($action) {
                    case "delete":
                        $messages = $this->delete($id);
                        break;
                    default:
                        $messages['type'] = 'error';
                        $messages['title'] = __('Failed');
                        $messages['messages'] = __('Action not found');
                        break;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
    }
}