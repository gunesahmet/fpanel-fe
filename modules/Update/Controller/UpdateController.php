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
 */namespace Update\Controller;

use FPanel\Forms\UpdateForm;
use FPanel\Globals\Globals;
use FPanel\Models\Servers;
use FPanel\Models\Settings;
use FPanel\System\Update;
use FPanel\Update\Initialize;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;


/**
 * Class UpdateController
 * @package Update\Controller
 */
class UpdateController extends ControllerBase
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
                    if($server_id == "1") {
                        $server = Servers::findFirst("type = 'main'");
                    }
                    else {
                        $server = Servers::findFirstById($server_id);
                    }
                    if(!$server) {
                        $this->view->setVar('error_content',__('Server not found'));
                        $this->response->setStatusCode(404, __('Server not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $server_data = $server->toArray();
                        $this->server_id = $server->id;
                        $this->view->setVar('server_id', $server_id);
                        $this->breadcrumbs->add(__('Server List'),'system',' ');
                        $this->breadcrumbs->add($server_data['name'],'system/server/'.$server_id,' ');
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
        else {
            $server = Servers::findFirst("type = 'main'");
            $server_data = $server->toArray();
            $this->server_data = $server_data;
            $this->server_id = $server->id;
        }
    }

    /**
     * index
     */
    public function indexAction()
    {
//        $update_list = [
//            [
//                "package_name" => "dbmyadmin",
//                "packages" => [
//                    [
//                        "version" => "4.4.15",
//                        "date" => "2020-06-21",
//                        "description" => "Fixed somtthings",
//                    ]
//                ]
//            ],
//        ];

        $this->view->pick('system/update');

        $this->tag->appendTitle( __('Update History'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }


        $settings = Settings::findFirst("server_id = '$server_id'");
        if($settings)
            $setting_data = $settings->toArray();
        else
            $setting_data = array('auto_update' => '0','update_time' => '','update_server' => '','random_time' => '');


        $setting_data["statistics_random"] = $this->config->statistics->random;
        $setting_data["statistics_time"] = $this->config->statistics->time;

        $options = array(
            'settings' => $setting_data
        );
        $form = new UpdateForm(null, $options);
        $this->view->form = $form;


        $this->breadcrumbs->add(__('Update History'),'','');

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $this->view->disable();
            $update = new Update();
            $this->response->setJsonContent($update->updateList($server_id))->send();
        }

    }

    /**
     * @return bool|ResponseInterface
     */
    public function settingsAction()
    {
        $this->view->disable();
        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt('update/server/'.$server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt('update');
        }

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true) {
                $this->view->disable();
            }

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $update = new Update();
            $post_data = array(
                'auto_update' => $this->request->getPost('auto_update',null,OFF),
                'update_time' => $this->request->getPost('update_time'),
//                'update_server' => $this->request->getPost('update_server'),
                'random_time' => $this->request->getPost('random_time',null,OFF),
                'statistics_random' => $this->request->getPost('statistics_random',null,OFF),
                'statistics_time' => $this->request->getPost('statistics_time'),
                'system_data' => $system_data
            );
            $update->setPosts($post_data);
            $status = $update->settingsUpdate($server_id);

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($update->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));

            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Settings is updated'));
                    return $this->response->redirect($redirect_url,true);
                }
                else {
                    $this->response->redirect($this->uri->url_encrypt('update'),true);
                }
            }
        }
        return true;
    }

    /**
     * @return ResponseInterface|void
     */
    public function closeAction()
    {
        $this->view->disable();
        if($this->request->isPost() == true) {
            if(file_exists(ROOTPATH.'/.update'))
                unlink(ROOTPATH.'/.update');
        }
        else {
            return $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    public function forceAction()
    {
        $this->view->disable();
        $update = new Initialize();
        $update->init();
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Update successful');
        $messages['redirect_url'] = $this->uri->url_encrypt("logout", true);
        $this->response->setJsonContent($messages)->send();
    }
}