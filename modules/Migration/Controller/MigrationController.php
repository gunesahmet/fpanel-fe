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
namespace Migration\Controller;

use FPanel\Forms\MigrationForm;
use FPanel\Globals\Globals;
use FPanel\Models\Servers;
use FPanel\Server\Migration;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;


/**
 * Class MigrationController
 * @package Migration\Controller
 */
class MigrationController extends ControllerBase
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
                        $this->breadcrumbs->add(__('Server List'),'system',' ');
                        $this->breadcrumbs->add($server_data['name'],'system/dashboard/server/'.$server_id,' ');
                        $this->server_data = $server_data;
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('Server not found'));
                    $this->response->setStatusCode(404, __('Server not found'));
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
        $this->tag->appendTitle( __('Migration Management'));
        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/dashboard/server/'.$server_id,' ');
        }
        else {
            $server_id = "1";
        }

        $this->breadcrumbs->add(__('Migration Management'),'','');


        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)){
            $this->view->disable();
            $migration = new Migration();
            $this->response->setJsonContent($migration->migrationlist($server_id))->send();
        }
    }

    /**
     * create
     */
    public function createAction()
    {
        $this->tag->appendTitle( __('Create Migration Connection'));

        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/dashboard/server/'.$server_id,' ');
            $this->breadcrumbs->add(__('Migration Management'),'migration/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("migration/server/".$server_id);
        }
        else {
            $server_id = "1";
            $this->breadcrumbs->add(__('Migration Management'),'migration',' ');
            $redirect_url = $this->uri->url_encrypt("migration");
        }

        $options = array(
            'action'    => 'create',
            'page'      => 'create'
        );

        $this->breadcrumbs->add(__('Create Migration'),'','');

        $this->view->form = new MigrationForm(null, $options);

        if ($this->request->isPost() == true){
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $migration = new Migration();
            $migration->setPosts(array(
                'migration_type' => $this->request->getPost('migration_type'),
                'ip_address' => $this->request->getPost('ip_address'),
                'port' => $this->request->getPost('port'),
                'system' => $this->request->getPost('system'),
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'panel_uname' => $this->request->getPost('panel_uname'),
                'panel_upass' => $this->request->getPost('panel_upass'),
                'description' => $this->request->getPost('description'),
            ));
            $status = $migration->create($server_id);
            if($status === false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($migration->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Migration is saved'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }

        return true;
    }

    /**
     * @param $id
     * @return bool|ResponseInterface
     */
    public function editAction($id)
    {
        $this->tag->appendTitle( __('Edit Migration Connection'));


        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/dashboard/server/'.$server_id,' ');
            $this->breadcrumbs->add(__('Migration Management'),'migration/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("migration/server/".$server_id);
        }
        else {
            $server_id = "1";
            $this->breadcrumbs->add(__('Migration Management'),'migration',' ');
            $redirect_url = $this->uri->url_encrypt("migration");
        }

        $migration = \FPanel\Models\Migration::findFirstById($id);
        if(!$migration) {
            $this->view->setVar('error_content',__('Migration not found'));
            $this->response->setStatusCode(404, __('Migration not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $this->breadcrumbs->add(__('Create Migration'),'','');

        $options = array(
            'action'    => 'edit',
            'page'      => 'create',
            'migration_data' => $migration->toArray()
        );


        $this->view->form = new MigrationForm(null, $options);

        if ($this->request->isPost() == true){
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $migration = new Migration();
            $migration->setPosts(array(
                'id' => $id,
                'migration_type' => $this->request->getPost('migration_type'),
                'ip_address' => $this->request->getPost('ip_address'),
                'port' => $this->request->getPost('port'),
                'system' => $this->request->getPost('system'),
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'panel_uname' => $this->request->getPost('panel_uname'),
                'panel_upass' => $this->request->getPost('panel_upass'),
                'description' => $this->request->getPost('description'),
            ));
            $status = $migration->update($server_id);
            if($status === false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($migration->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Migration is updated'));
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
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)){
            $this->view->disable();
            $migration = new Migration();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Delete successful');

            if($migration->delete($id) == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Migration not found');

            }


            $this->response->setJsonContent($messages)->send();
        }
    }

    /**
     * @param $id
     * @return bool|ResponseInterface
     */
    public function startAction($id)
    {
        $this->tag->appendTitle( __('Migration Start'));
        $this->view->setVar("id", $id);
        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/dashboard/server/'.$server_id,' ');
            $this->breadcrumbs->add(__('Migration Management'),'migration/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("migration/status/". $id ."/server/".$server_id);
        }
        else {
//            $server_id = "1";
            $this->breadcrumbs->add(__('Migration Management'),'migration',' ');
            $redirect_url = $this->uri->url_encrypt("migration/status/". $id);
        }

        $this->breadcrumbs->add(__('Migration Start'),'','');


        $mig = \FPanel\Models\Migration::findFirstById($id);

        if(!$mig) {
            $this->view->setVar('error_content',__('Migration not found'));
            $this->response->setStatusCode(404, __('Migration not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $migration_data = $mig->toArray();

        $migration = new Migration();

        $enabled = $migration->enableMigration($migration_data);

        if(strpos($enabled,'error') == true) {
            $this->view->setVar('error_content',__('Migration not found'));
            $this->response->setStatusCode(404, __('Migration not found'));
            $this->view->pick("errors/404");
            return true;
        }
        else {
            if(strpos($enabled,"Err") !== false) {
                $this->view->setVar('error_content',__('Migration failed %error_code%',array('error_code' => trim($enabled))));
                $this->response->setStatusCode(404, __('Migration failed %error_code%',array('error_code' => trim($enabled))));
                $this->view->pick("errors/404");
                return true;
            }
            else {
                $conf_data = explode(' ', $enabled);
                $remote_user = trim($conf_data[0]);
                $remote_pass = trim($conf_data[1]);
            }
        }

        $remote_data = $this->connect($migration_data['ip_address'], $remote_user, $remote_pass, $id);


        $this->view->setVar("remote_data", $remote_data);

        $options = array(
            'action'    => 'start',
            'page'      => 'start'
        );

        $this->view->form = new MigrationForm(null, $options);

        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Starting successful');

            $migration->setPosts(array(
                'selected_resellers' => explode(',', $this->request->getPost('remote_resellers')),
                'selected_clients' => explode(',', $this->request->getPost('remote_clients')),
                'selected_domains' => explode(',', $this->request->getPost('remote_domains')),
                'skip_global_settings' => $this->request->getPost('skip_global_settings'),
                'skip_domain_datas' => $this->request->getPost('skip_domain_datas'),
                'replace_exists_accounts' => $this->request->getPost('replace_exists_accounts'),
                'replace_exists_domains' => $this->request->getPost('replace_exists_domains'),
                'remote_user' => $remote_user,
                'remote_pass' => $remote_pass,
                'ip_address' => $migration_data['ip_address'],
                'root'      => $migration_data['root'],
                'root_pass' => $migration_data['root_pass'],
                'server_type' => $migration_data['server_type'],
                'system' => $migration_data['system'],
            ));

            $status = $migration->start($id);
            if($status === false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Migration starting failed');
                $messages['errors'] = [];
                foreach ($migration->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $id
     */
    public function statusAction($id)
    {
        $this->tag->appendTitle( __('Migration Status'));
        $this->view->setVar('id', $id);

        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/dashboard/server/'.$server_id,' ');
            $this->breadcrumbs->add(__('Migration Management'),'migration/server/'.$server_id,' ');
            $prog_url = $this->uri->url_encrypt("migration/progress/".$id."/server/".$server_id);
        }
        else {
//            $server_id = "1";
            $this->breadcrumbs->add(__('Migration Management'),'migration',' ');
            $prog_url = $this->uri->url_encrypt("migration/progress/".$id);
        }
        $this->view->setVar('prog_url', $prog_url);

        $this->breadcrumbs->add(__('Migration Status'),'','');


        if(!file_exists(ROOTPATH."/tmp/.migration.config")) {
            $this->view->setVar('error_content',__('Migration failed create config'));
            $this->response->setStatusCode(404, __('Migration failed create config'));
            $this->view->pick("errors/404");
        }
        else {
            $config = $this->env->readConfig(ROOTPATH."/tmp/.migration.config");
            $this->view->setVar('configs', $config);
            $this->view->setVar("resellers", $config['resellers']);
            $this->view->setVar("clients", $config['clients']);
            $this->view->setVar("domains", $config['domains']);
            $this->view->setVar("progress", $config['progress']);
        }
    }

    public function progressAction()
    {
        $this->view->disable();
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {

            if(isset($this->server_id)) {
                $server_id = $this->server_id;
                $this->view->setVar("server_id", $server_id);
                $redirect_url = $this->uri->url_encrypt("migration/server/". $server_id);
            }
            else {
//                $server_id = "1";
//                $this->view->setVar("server_id", $server_id);
                $redirect_url = $this->uri->url_encrypt("migration");
            }

            if(file_exists(ROOTPATH."/tmp/.migration.config")) {
                $progress = $this->env->readConfig(ROOTPATH."/tmp/.migration.config");
                $progress['url'] = $redirect_url;
                unset($progress['remote_pass']);
                unset($progress['remote_user']);
                unset($progress['root']);
                unset($progress['root_pass']);
                unset($progress['ip_address']);
                unset($progress['options']);
                unset($progress['server_type']);
                unset($progress['system']);
                unset($progress['migration_id']);
                $this->response->setJsonContent($progress)->send();

            }
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    /**
     * @param $ip_address
     * @param $username
     * @param $password
     * @param $id
     * @return array
     */
    private function connect($ip_address, $username, $password, $id): array
    {

        $data = array(
            'username' => $username,
            'password' => $password,
            'module'    => 'migration',
            'action'  => 'get-data',
            'data'     => array(
                'status'    => '',
                'migration_id'  => $id,
                'progress'  => '',
            )
        );

        return $this->env->remote($ip_address, $data);

    }
}