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
 */namespace ServerPHP\Controller;

use FPanel\Forms\ServerPhpSettingsForm;
use FPanel\Globals\Globals;
use FPanel\Models\Servers;
use FPanel\WebServer\PhpSettings;
use Main\Controller\ControllerBase;

/**
 * Class ServerphpController
 * @package ServerPHP\Controller
 */
class ServerphpController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;
    protected $system_data;

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
                    $domain = Servers::findFirstById($server_id);
                    if(!$domain) {
                        $this->view->setVar('error_content',__('Server not found'));
                        $this->response->setStatusCode(404, __('Server not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $server_data = $domain->toArray();
                        $this->server_id = $server_id;
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
    }


    public function settingsAction()
    {

        // @TODO Phpsettings server save işlemi tamamlanacak. Sunucuda php yüklü olup olmadığı kontrol edilecek
        // @TODO Sistemdeki yüklü php modüllerinin listesi ve ayarları ekrana basılacak ve ayar yaptırılabilecek
        $this->tag->appendTitle(__('PHP Settings'));
        $this->view->pick('webserver/php');
        if(isset($this->server_id)) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $redirect_url = $this->uri->url_encrypt('serverphp/settings/server/'.$server_id);
            $post_url = $this->uri->url_encrypt('serverphp/settings/server/'.$server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt('serverphp/settings');
            $post_url = $this->uri->url_encrypt('serverphp/settings');
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Web Server PHP Settings'),'','');

        $options = array(
            'action'    => 'edit',
//            'server_ini'  => $server_ini,
        );

        $this->view->form = new ServerPhpSettingsForm(null, $options);

        if($this->request->isPost()){
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $php = new PhpSettings();
            $status = $php->update($server_id);
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($php->getMessages() as $title => $message) {
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
                    $this->flashSession->success(__('PHP Server settings updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }

        return true;

    }

}