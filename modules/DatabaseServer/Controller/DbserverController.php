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
 */namespace DatabaseServer\Controller;

use FPanel\Forms\DatabaseServerForm;
use FPanel\Globals\Globals;
use FPanel\Models\DatabaseServer;
use FPanel\Models\DatabaseServerSettings;
use FPanel\Models\Servers;
use FPanel\DatabaseServer\DatabaseServer as DbServer;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class DbserverController
 * @package DatabaseServer\Controller
 */
class DbserverController extends ControllerBase
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
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
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
        $this->tag->appendTitle(__('Database Server List'));

        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->breadcrumbs->add(__('Database Server'),'','');

        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)){
            $this->view->disable();
            $db_list = new DbServer();
            $this->response->setJsonContent($db_list->databaseServerList($server_id))->send();
        }
    }

    /**
     * @return bool|ResponseInterface
     */
    public function createAction()
    {
        $this->tag->appendTitle(__('Add New Database Server'));

        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Database Server List'),'dbserver/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt('dbserver/server/'.$server_id);
            $post_url = $this->uri->url_encrypt('dbserver/create/server/'.$server_id);
        }
        else {
            $this->breadcrumbs->add(__('Database Server List'),'dbserver',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt('dbserver');
            $post_url = $this->uri->url_encrypt('dbserver/create');
        }

        $this->view->setVar("post_url", $post_url);

        $this->breadcrumbs->add(__('Add Database Server'),'','');


        $options = array(
            'page' => 'database',
            'action' => 'create'
        );
        $this->view->form = new DatabaseServerForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $post_data = array(
                'db_server_name'       => $this->request->getPost('db_server_name'),
                'host'                  => $this->request->getPost('db_host'),
                'password'              => $this->request->getPost('password'),
                'port'                  => $this->request->getPost('db_port'),
                'type'                  => $this->request->getPost('db_type'),
                'username'              => $this->request->getPost('username'),
                'server_id'             => $server_id,
                'status'                => '1',
            );
            $server = new DbServer();
            $server->setPosts($post_data);
            $status = $server->addDatabaseServer();
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($server->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Database Server Saved'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;

    }

    /**
     * @param $db_srv_id
     * @return bool|ResponseInterface
     */
    public function editAction($db_srv_id)
    {

        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Database Server List'),'dbserver/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt('dbserver/server/'.$server_id);
            $post_url = $this->uri->url_encrypt("dbserver/edit/$db_srv_id/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Database Server List'),'dbserver',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt('dbserver');
            $post_url = $this->uri->url_encrypt("dbserver/edit/$db_srv_id");
        }
        $this->view->setVar("post_url", $post_url);

        $this->breadcrumbs->add(__('Edit Database Server'),'','');


        $this->view->setVar("db_srv_id", $db_srv_id);
        $this->tag->appendTitle(__('Edit Database Server'));
        $server_data = DatabaseServer::findFirstById($db_srv_id)->toArray();
        $options = array(
            'page' => 'database',
            'action' => 'edit',
            'db_type'   => $server_data['type'],
            'db_host'   => $server_data['host'],
            'db_port'   => $server_data['port'],
            'db_username'   => $server_data['username'],
            'db_password'   => $server_data['password'],
            'db_server_name'   => $server_data['db_server_name'],
        );

        $this->view->form = new DatabaseServerForm(null, $options);
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $post_data = array(
                'db_server_id'          => $db_srv_id,
                'db_server_name'        => $this->request->getPost('db_server_name'),
                'host'                  => $this->request->getPost('db_host'),
                'password'              => $this->request->getPost('password'),
                'port'                  => $this->request->getPost('db_port'),
                'type'                  => $this->request->getPost('db_type'),
                'username'              => $this->request->getPost('username'),
                'server_id'             => $server_id,
                'status'                => '1',
            );
            $server = new DbServer();
            $server->setPosts($post_data);
            $status = $server->updateDatabaseServer();
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($server->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }


            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Database Server updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if(($this->request->isAjax()) && ($this->request->isPost())) {

            $db_server = new DbServer();
            $db_server->setPosts(array(
                'id'    => $id,
            ));
            $status = $db_server->deleteDatabaseServer();
            $messages = Globals::messages();

            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Delete failed');
            }
            else {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Delete successful');
            }
            $this->response->setJsonContent($messages)->send();
        }
    }

    public function databasesAction()
    {
        $this->tag->appendTitle(__('Database List'));

        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $list_url = $this->uri->url_encrypt('dbserver/databases/server/'.$server_id);
            $this->breadcrumbs->add(__('Database Server List'),'dbserver/server/'.$server_id,' ');
        }
        else {
            $this->breadcrumbs->add(__('Database Server List'),'dbserver',' ');
            $list_url = $this->uri->url_encrypt('dbserver/databases');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->view->setVar("list_url", $list_url);
        $this->breadcrumbs->add(__('Database List'),'','');


        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)){
            $this->view->disable();
            $db_list = new DbServer();
            $this->response->setJsonContent($db_list->databaseList($server_id))->send();
        }
    }

    /**
     * @param $id
     * @return bool|ResponseInterface
     */
    public function settingsAction($id)
    {
        $this->tag->appendTitle(__('Database Server Settings'));

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Database Server List'),'dbserver/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt('dbserver/server/'.$server_id);
            $post_url = $this->uri->url_encrypt("dbserver/settings/$id/server/$server_id");

            $servers = Servers::findFirstById($server_id);
            if(strpos(strtolower($servers->os),WINDOWS) !== false) {
                $server_os = WINDOWS;
            }
            else {
                $server_os = LINUX;
            }
        }
        else {
            $this->breadcrumbs->add(__('Database Server List'),'dbserver',' ');
            $redirect_url = $this->uri->url_encrypt('dbserver');
            $post_url = $this->uri->url_encrypt("dbserver/settings/$id");
            $server_os = PANEL_OS_TYPE;
        }

        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Database Server Settings'),'','');


        $database = DatabaseServer::findFirstById($id);
        if(!$database) {
            $this->view->setVar('error_content',__('Database server not found'));
            $this->response->setStatusCode(404, __('Database server not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $database_server_settings = DatabaseServerSettings::findFirst("database_server_id = '$database->id'");

        if(!$database_server_settings) {
            $this->view->setVar('error_content',__('Database server settings not found'));
            $this->response->setStatusCode(404, __('Database server settings not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $db_settings = json_decode($database_server_settings->params,true);
        if(empty($db_settings)) {
            $this->view->setVar('error_content',__('Database server settings not yet implemented'));
            $this->response->setStatusCode(404, __('Database server settings not yet implemented'));
            $this->view->pick("errors/404");
            return true;
        }


        $options = array(
            'page' => 'settings',
            'database_type' => $database->type,
            'server_os' => $server_os,
            'data' => $db_settings,
        );

        $this->view->form = new DatabaseServerForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $server = new DbServer();
            $status = $server->settings($id);
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($server->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Database Server settings saved'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

}