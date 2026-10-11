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
 */namespace Webserver\Controller;

use FPanel\Forms\WebServerForm;
use FPanel\Globals\Globals;
use FPanel\Models\Servers;
use FPanel\WebServer\WebServer;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class WebserverController
 * @package Webserver\Controller
 */
class WebserverController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;
    private $system_os_type;
    private $system_data;

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
                        $this->system_packages = json_decode($server_data["type"], true)["system_packages"];
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

        $this->system_os_type = $system_os_type;
        $this->view->setVar('system_os_type', $system_os_type);
    }

    /**
     * webSetting
     */
    public function webSettingAction()
    {
        $this->breadcrumbs->add(__('Web Server Settings'),'','');

        $this->view->pick('webserver/web');

        $this->tag->appendTitle(__('Web Server Settings'));

        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("webserver/web/server/$server_id");
            $post_url = $this->uri->url_encrypt("webserver/web/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("webserver/web");
            $post_url = $this->uri->url_encrypt("webserver/web");
        }

        $this->view->setVar('post_url', $post_url);

        if($this->system_os_type == LINUX) {
            if($this->system_packages["HTTPD_SERVER"] == "apache") {
                $webserver_type = "apache";
            }
            else
                $webserver_type = "nginx";
        }
        else {
            $webserver_type = "iis";
        }

        $this->view->setVar('web_server_type', $webserver_type);
        $webserver_setting = new WebServer();
        $settings = $webserver_setting->getWebServerSettings($server_id);

        if(!is_array($settings)) {
            $this->view->setVar("error_content", $settings);
            $this->response->setStatusCode(500, __("Response failed please check error message."));
            $this->view->pick("errors/500");
            return true;
        }

        $options = array(
            'page'    => 'settings',
            'action'    => 'edit',
            'settings' => $settings,
            'webserver_type' => $webserver_type
        );

        $this->view->form = new WebServerForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $webserver_setting = new WebServer();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode');
                if($this->request->isAjax())
                    return $this->response->setJsonContent($messages)->send();
                else
                    $this->flashSession->warning($messages['messages']);
            }

            $webserver_setting->setPost("webserver_type", $webserver_type);
            $status = $webserver_setting->updateWebServerSettings($server_id);

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($webserver_setting->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax()) {
                $this->response->setJsonContent($messages)->send();
            }
            else {
                if($status == true) {
                    $this->flashSession->success(__('Settings updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * modules
     */
    public function modulesAction()
    {
        $this->view->pick('webserver/modules');
        $this->tag->appendTitle(__('Web Server Modules'));

        if($this->system_data['system_type'] == CLOUD) {
            $server = Servers::findFirstById($this->server_id);
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $post_url = $this->uri->url_encrypt("webserver/modules/server/$server_id");
            $panel_os_type = (strpos(strtolower($server->os), WINDOWS) !== false ? WINDOWS : LINUX);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $post_url = $this->uri->url_encrypt("webserver/modules");
            $panel_os_type = PANEL_OS_TYPE;
        }

        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Web Server Modules'),'','');


        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $web_server = new WebServer();
            $data = $web_server->extraModList($server_id,false, $panel_os_type);

            $this->response->setJsonContent($data)->send();

        }
    }

    /**
     * mimeTypes
     */
    public function mimeTypesAction()
    {
        $this->view->pick('webserver/mimetype');

        $this->tag->appendTitle(__('Mime Types'));

        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $post_url = $this->uri->url_encrypt("webserver/mimetypes/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $post_url = $this->uri->url_encrypt("webserver/mimetypes");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Mime Types'),'','');


        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $web_server = new WebServer();
            $data = $web_server->mimeTypeList($server_id,false, $this->system_os_type);
            $this->response->setJsonContent($data)->send();
        }
    }

    /**
     * addMime
     */
    public function addMimeAction()
    {
        $this->view->pick('webserver/addmime');

        $this->tag->appendTitle(__('Add Mime Type'));

        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $redirect_url = $this->uri->url_encrypt("webserver/mimetypes/server/".$server_id);
            $post_url = $this->uri->url_encrypt("webserver/addmime/server/".$server_id);
            $this->breadcrumbs->add(__('Mime Types'),"webserver/mimetypes/server/".$server_id,' ');
        }
        else {
            $this->breadcrumbs->add(__('Mime Types'),'webserver/mimetypes',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("webserver/mimetypes");
            $post_url = $this->uri->url_encrypt("webserver/addmime");
        }

        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Add Mime Type'),'','');

        

        $options = array(
            'page'    => 'mime',
            'action'    => 'create',
        );
        $this->view->form = new WebServerForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $post_data = array(
                'file_extension' => $this->request->getPost('file_extension'),
                'mimetype_octet' => $this->request->getPost('mimetype_octet'),
                'server_id' => $server_id
            );
            $web_server = new WebServer();
            $web_server->setPosts($post_data);
            $status = $web_server->addMime($server_id);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach($web_server->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax()) {
                $this->response->setJsonContent($messages)->send();
            }
            else {
                if($status == true) {
                    $this->flashSession->success(__('Mime Type added'));
                    return $this->response->redirect($redirect_url,true);
                }
            }

        }


        return true;
    }

    /**
     * @param $file_extension
     * @return ResponseInterface|void
     */
    public function removeMimeAction($file_extension)
    {
        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if(demo == true) {
                return $this->response->setJsonContent($messages)->send();
            }

            if($this->system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }

            $mime = new WebServer();
            $mime->setPosts(array(
                'file_extension'        => str_replace(' ','#',base64_decode($file_extension)),
                'server_id' => $server_id,
            ));
            $status = $mime->removeMime($server_id);
            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
            }
            else {
                $messages['messages'] = __('Mime type removed');
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    /**
     * editModule
     */
    public function editmoduleAction()
    {
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        $this->view->disable();
        if(demo == true) {
            $messages['messages'] = __('Sorry, this feature is disabled in demo mode');
            return $this->response->setJsonContent($messages)->send();
        }
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            if($this->system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }
            $params = $this->dispatcher->getParams();
//
            if($params['process'] == "activate") {
                $messages['messages'] = __('Module is actived');
                $action = "on";
            }
            else {
                $messages['type'] = 'warning';
                $messages['messages'] = __('Module is deactived');
                $action = "off";
            }

            $module = $params['module_name'];

            $webserver = new WebServer();
            $status = $webserver->updateModule($module, $action, $server_id);

            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
            }

            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
        return true;
    }

}