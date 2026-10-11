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
 */namespace Telegram\Controller;

use Exception;
use FPanel\Forms\TelegramForm;
use FPanel\Globals\Globals;
use FPanel\Models\Servers;
use FPanel\Models\SystemSettings;
use FPanel\System\Telegram;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class TelegramController
 * @package Telegram\Controller
 */
class TelegramController extends ControllerBase
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
        if($this->dispatcher->getActionName() != "api") {
            parent::initialize();
            if(!$this->session_status)
                return;
            $user_data = $this->acl->run('system');
            $this->user_data = $user_data;
            $this->user_id = $user_data['user']['info']['user_id'];
            $this->user_type = $user_data['user']['info']['user_type'];

            $this->view->setVar('system_items', $user_data);

            $this->system_data = $user_data['system_data'];

            $params = $this->dispatcher->getParams();

            $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
            if($this->system_data["system_type"] == CLOUD) {
                $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
            }
            else {
                $this->breadcrumbs->add(__('System Dashboard'),'system',' ');
            }

            if(array_key_exists('type', $params)) {
                switch($params['type']) {
                    case "server":
                        $server_id = $params['server_id'];
                        $servers = Servers::findFirstById($server_id);
                        if(!$servers) {
                            $this->tag->appendTitle(__('Error 404 Server Not Found') );
                            $this->response->setStatusCode(404, 'Not Found');
                            $this->view->setVar('error_content',__('Unreconized url'));
                            $this->view->pick("errors/404");
                            return true;
                        }
                        else {
                            $server_data = $servers->toArray();
                            $this->server_id = $server_id;
                            $this->view->setVar('server_id', $server_id);
                            $this->breadcrumbs->add(__('Server List'),'system',' ');
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
    }

    public function indexAction()
    {
        $this->tag->appendTitle( __('Telegram Users'));
        $this->breadcrumbs->add(__('Telegram Users'),'','');
        $th = [__('FTP Users'),__('Status'),__('Action')];

        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $telegram = new Telegram();
            $this->response->setJsonContent($telegram->userList())->send();
        }
    }

    /**
     * @return bool|ResponseInterface
     */
    public function settingsAction()
    {
        $this->tag->appendTitle( __('Telegram Settings'));
        $this->breadcrumbs->add(__('Telegram Users'),"telegram",' ');
        $this->breadcrumbs->add(__('Telegram Settings'),"",'');

        $this->view->setVar('post_url', $this->uri->url_encrypt('telegram'));

        $system_settings = SystemSettings::findFirst("key = 'certificate'");
//        $default_ipaddress = SystemSettings::findFirst("key = 'default_ip_address'");

//        $url_prefix = "http://".$default_ipaddress->value . ":8443/telegram/";
        if(!$system_settings) {
            $this->response->setStatusCode(404, 'Not Found');
            $this->view->setVar('error_content',__('You must be set SSL Certificate before use telegram. Telegram does not allow systems without SSL.'));
            $this->view->pick("errors/404");
            return true;
        }

        $ssl_params = json_decode($system_settings->value, true);

        if(isset($ssl_params["activate"]) && ($ssl_params["activate"] == true)) {
            $hostname = $ssl_params["hostname"];
            $url_prefix = "https://".$hostname . ":8443/telegram/";
        }
        else {
            $this->response->setStatusCode(404, 'Not Found');
            $this->view->setVar('error_content',__('You must be set SSL Certificate before use telegram. Telegram does not allow systems without SSL.'));
            $this->view->pick("errors/404");
            return true;
        }

        $telegram_model = \FPanel\Models\Telegram::findFirst();
        if(!$telegram_model) {
            $telegram_data = [];
        }
        else {
            $telegram_data = $telegram_model->toArray();
        }

        $options = [
            "data" => $telegram_data
        ];

        $this->view->form = new TelegramForm(null, $options);

        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode!');
                if($this->request->isAjax())
                    return $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->error(json_encode([$messages['messages']]));
                }
            }
            $telegram = new Telegram();
            $telegram->setPost("url_prefix", $url_prefix);
            $status = $telegram->saveSettings();
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($telegram->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Update successful'));
                    return $this->response->redirect($this->uri->url_encrypt('telegram'),true);
                }
            }
        }
        return true;
    }

    /**
     * @return ResponseInterface|void
     */
    public function activateAction()
    {
        $this->view->disable();
        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true) {
                $messages = Globals::messages();
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Update successful');

                $telegram = new Telegram();
                $status = $telegram->activation();
                if(!$status) {

                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Activate failed');
                    $messages['errors'] = [];
                    foreach ($telegram->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }


                if ($this->request->isAjax()) {

                    $this->response->setJsonContent($messages)->send();
                }
                else {
                    if ($status == true) {
                        $this->flashSession->success(__('Activate successful'));
                        return $this->response->redirect($this->uri->url_encrypt('telegram'),true);
                    }
                }
            }
        }
    }

    /**
     * @param $chat_id
     * @return ResponseInterface|void
     */
    public function deactivateAction($chat_id)
    {
        $this->view->disable();
        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true) {
                $messages = Globals::messages();
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Update successful');

                $telegram = new Telegram();
                if($status = $telegram->deactivate($chat_id) == false) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Activate failed');
                    $messages['errors'] = [];

                    foreach ($telegram->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
                if ($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
                else {
                    if ($status == true) {
                        $this->flashSession->success(__('Activate successful'));
                        return $this->response->redirect($this->uri->url_encrypt('telegram'),true);
                    }
                }
            }
        }
    }

    /**
     *
     */
    public function apiAction()
    {

        try {
            $telegram = new Telegram();
            $telegram->init();
        }
        catch (Exception $e) {
        }
        $this->view->disable();
    }
}