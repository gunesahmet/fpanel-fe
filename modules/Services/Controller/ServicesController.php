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
 */namespace Services\Controller;

use FPanel\Globals\Globals;
use FPanel\Models\Servers;
use FPanel\Server\Services;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class ServicesController
 * @package Services\Controller
 */
class ServicesController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;
    protected $packages = array();
    /**
     * @var Services
     */
    private $services;
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

        $this->services = new Services();

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
                        if(strpos(strtolower($server->os),WINDOWS) !== false) {
                            $this->server_os_type = WINDOWS;
                        }
                        else {
                            $this->server_os_type = LINUX;
                        }
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
        else {
            $this->server_os_type = PANEL_OS_TYPE;
        }
    }

    public function managementAction()
    {
        $this->view->pick('server/services');
        $system_data = $this->system_data;

        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->breadcrumbs->add(__('Service Management'),'','');

        $this->tag->appendTitle( __('Service Management'));

        $services = $this->services->servicesList($server_id, $this->server_os_type);

        $this->view->setVar('services', $services);
    }

    /**
     * @param $service
     * @return bool|ResponseInterface
     */
    public function startAction($service)
    {
        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("services/management/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("services/management");
        }

        if($this->request->isAjax() == true)
            $this->view->disable();

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        if(demo == true) {
            $messages['type'] = 'warning';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('This feature disabled in demo mode!');

            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
        }
        else {
            $this->services->start($server_id, $this->crypto->decryptSession($service));
        }

        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            $this->flashSession->success(__('Selected service is starting'));
            return $this->response->redirect($redirect_url,true);
        }
        return true;
    }

    /**
     * @param $service
     */
    public function stopAction($service)
    {
        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("services/management/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("services/management");
        }

        if($this->request->isAjax() == true)
            $this->view->disable();

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        if(demo == true) {

            $messages['type'] = 'warning';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('This feature disabled in demo mode!');
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
        }
        else {
            $this->services->stop($server_id, $this->crypto->decryptSession($service));
        }

        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            $this->flashSession->success(__('Selected service is stopping'));
            $this->response->redirect($redirect_url,true);
        }
    }

    /**
     * @param $service
     * @return ResponseInterface|void
     */
    public function restartAction($service)
    {
        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("services/management/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("services/management");
        }

        if($this->request->isAjax() == true)
            $this->view->disable();
        $messages = Globals::messages();
        $messages['type'] = 'info';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Restarting successful');

        if(demo == true) {
            $messages['type'] = 'warning';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('This feature disabled in demo mode!');
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
        }
        else {
            $this->services->restart($server_id, $this->crypto->decryptSession($service));
        }

        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            $this->flashSession->success(__('Selected service is restarting'));
            return $this->response->redirect($redirect_url,true);
        }
    }
}