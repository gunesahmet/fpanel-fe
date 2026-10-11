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
namespace Ftp\Controller;

use FPanel\Forms\FtpForm;
use FPanel\Globals\Globals;
use FPanel\Models\ServerFtpSettings;
use FPanel\Models\Servers;
use FPanel\Server\FtpServer;
use Main\Controller\ControllerBase;

/**
 * Class FtpController
 * @package Ftp\Controller
 */
class FtpController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;
    protected $packages = array();
    protected $services = array();
    protected $server_os_type;

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
                        $system_os_type = (strpos(strtolower($server->os), WINDOWS) !== false ? WINDOWS : LINUX);
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
        else {
            $system_os_type = PANEL_OS_TYPE;
        }
        $this->server_os_type = $system_os_type;

        return true;
    }

    public function settingsAction()
    {
        $this->view->pick('ftp/settings');
        $system_data = $this->system_data;

        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $redirect_url = $this->uri->url_encrypt("ftp/settings/server/$server_id");
            $post_url = $this->uri->url_encrypt("ftp/settings/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("ftp/settings");
            $post_url = $this->uri->url_encrypt("ftp/settings");
        }
        $this->view->setVar("post_url", $post_url);

        if($this->server_os_type == LINUX)
            $ftp_type = "proftpd";
        else
            $ftp_type = "iis";

        $this->breadcrumbs->add(__('FTP Settings'),'','');
        $this->tag->appendTitle( __('FTP Settings'));

        $ftp_settings = ServerFtpSettings::findFirst("server_id = '$server_id'");

        $options = array(
            'data' => json_decode($ftp_settings->params,true)
        );
        $form = new FtpForm(null, $options);
        $this->view->form = $form;

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $ftp = new FtpServer();
            $post = array(
                'server_id' => $server_id,
                'server_type' => $this->server_os_type,
                'ftp_type' => $ftp_type,
                'timeout_idle' => $this->request->getPost('timeout_idle'),
                'timeout_no_transfer' => $this->request->getPost('timeout_no_transfer'),
                'maxinstances' => $this->request->getPost('maxinstances'),
                'max_clients' => $this->request->getPost('max_clients'),
                'welcome_message' => $this->request->getPost('welcome_message'),
            );
            $ftp->setPosts($post);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $status = $ftp->save();


            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach($ftp->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                return $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('FTP Settings saved'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function sessionsAction()
    {
        $this->view->pick('ftp/sessions');
        $system_data = $this->system_data;

        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
        }
        else {
            $server_id = "1";
        }

        $this->breadcrumbs->add(__('FTP Sessions'),'','');


        $this->tag->appendTitle( __('FTP Sessions'));

        $this->view->setVar('server_os', $this->server_os_type);
        if($this->request->isPost()) {
            if($this->request->isAjax()) {
                $ftp = new FtpServer();
                return $this->response->setJsonContent($ftp->activeSessions($server_id))->send();
            }
        }
        return true;
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
            $messages['messages'] = __('Adding successful');

            if($this->request->getPost('id') == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select a session');
                return $this->response->setJsonContent($messages)->send();
            }
            else if($this->request->getPost("action") == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Please select an action');
                return $this->response->setJsonContent($messages)->send();
            }
            else {
                switch($action) {
                    case "kill":
                        $this->kill($id);
                        break;
                    default:
                        $messages['type'] = 'error';
                        $messages['title'] = __('Failed');
                        $messages['messages'] = __('Action not found');
                        return $this->response->setJsonContent($messages)->send();
                }
            }

        }
        else {
            return $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
        return true;
    }

    public function killAction($id)
    {
        $this->view->disable();
        $system_data = $this->system_data;

        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $redirect_url = $this->uri->url_encrypt("ftp/sessions/server/$server_id");
        }
        else {
            $server_id = "1";
            $redirect_url = $this->uri->url_encrypt("ftp/sessions");
        }


        if($this->request->isPost()) {
            $ftp = new FtpServer();
            $ftp->setPosts(array(
                'server_id' => $server_id
            ));
            $status = $ftp->killSession($id);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('FTP Session killed');

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('FTP Session is not found.');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            else {
                if ($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->success(__('FTP session is killed'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function kill($id)
    {
        $this->view->disable();
        $system_data = $this->system_data;

        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $redirect_url = $this->uri->url_encrypt("ftp/sessions/server/$server_id");
        }
        else {
            $server_id = "1";
            $redirect_url = $this->uri->url_encrypt("ftp/sessions");
        }

        if($this->request->isPost()) {
            $ftp = new FtpServer();
            $ftp->setPosts(array(
                'server_id' => $server_id
            ));
            $status = $ftp->killSession($id);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach($ftp->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            else {
                if ($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->success(__('FTP session is killed'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }
}