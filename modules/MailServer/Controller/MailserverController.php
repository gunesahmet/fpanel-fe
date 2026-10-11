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
 */namespace MailServer\Controller;

use FPanel\Forms\MailServerForm;
use FPanel\Globals\Globals;
use FPanel\MailServer\Mail as MailServer;
use FPanel\Models\MailServerSettings;
use FPanel\Models\Servers;
use FPanel\Models\SpamSettings;
use FPanel\Process\MailServer as MailServerProcess;
use Main\Controller\ControllerBase;

/**
 * Class MailserverController
 * @package MailServer\Controller
 */
class MailserverController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;
    protected $mail_server;

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
        $servers = Servers::findFirst("type = 'main'");
        $server_id = $servers->id;
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
                    $this->view->setVar('error_content',__('Server not found'));
                    $this->response->setStatusCode(404, __('Server not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }

        $generalsettings_link = $this->uri->url_encrypt("mailserver/generalsettings" . ((isset($server_id) && $server_id != "1") ? "/server/$server_id" : ""));
        $spamsettings_link = $this->uri->url_encrypt("mailserver/spamsettings". ((isset($server_id) && $server_id != "1") ? "/server/$server_id" : ""));
        $blacklist_link = $this->uri->url_encrypt("mailserver/blacklist". ((isset($server_id) && $server_id != "1") ? "/server/$server_id" : ""));
        $whitelist_link = $this->uri->url_encrypt("mailserver/whitelist". ((isset($server_id) && $server_id != "1") ? "/server/$server_id" : ""));
        $antivirus_link = $this->uri->url_encrypt("mailserver/antivirus". ((isset($server_id) && $server_id != "1") ? "/server/$server_id" : ""));
        $smtp_link = $this->uri->url_encrypt("mailserver/smtp". ((isset($server_id) && $server_id != "1") ? "/server/$server_id" : ""));
        $policyd_link = $this->uri->url_encrypt("mailserver/policyd". ((isset($server_id) && $server_id != "1") ? "/server/$server_id" : ""));

        $this->view->setVar("generalsettings_link", $generalsettings_link);
        $this->view->setVar("spamsettings_link", $spamsettings_link);
        $this->view->setVar("blacklist_link", $blacklist_link);
        $this->view->setVar("whitelist_link", $whitelist_link);
        $this->view->setVar("antivirus_link", $antivirus_link);
        $this->view->setVar("smtp_link", $smtp_link);
        $this->view->setVar("policyd_link", $policyd_link);

        $this->mail_server = MailServerSettings::findFirst("server_id = '$server_id'");

        if(!$this->mail_server) {
            $this->view->setVar('error_content',__('Mail Server not found'));
            $this->response->setStatusCode(404, __('Mail Server not found'));
            $this->view->pick("errors/404");
            return false;
        }
        else {
            $this->view->setVar("installed_mail", $this->mail_server->server_type);
        }
    }

    /**
     * General Settings
     */
    public function generalsettingsAction()
    {
        $this->tag->appendTitle(__('Mail Server General Settings'));

        if(!$this->mail_server) {
            $this->view->setVar('error_content',__('Mail Server not found'));
            $this->response->setStatusCode(404, __('Mail Server not found'));
            $this->view->pick("errors/404");
            return true;
        }
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $redirect_url = $this->uri->url_encrypt('mailserver/generalsettings/server/'.$server_id);
            $post_url = $this->uri->url_encrypt('mailserver/generalsettings/server/'.$server_id);
            $validate_url = $this->uri->url_encrypt('mailserver/retrieve/server/'.$server_id);
            $this->view->setVar("policy_save_url", $this->uri->url_encrypt("mailserver/savepolicysettings/server/$server_id"));
        }
        else {
            if(!isset($this->system_packages["SMTP_SERVER"]) || (!$this->system_packages["SMTP_SERVER"] == "hmail" || !$this->system_packages["SMTP_SERVER"] == "mailenable")) {
                $this->view->setVar('error_content',__('Mail Service not installed'));
                $this->response->setStatusCode(404, 'Not Found');
                $this->view->pick("errors/404");
                return true;
            }
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt('mailserver/generalsettings');
            $post_url = $this->uri->url_encrypt('mailserver/generalsettings');
            $validate_url = $this->uri->url_encrypt('mailserver/retrieve');
            $this->view->setVar("policy_save_url", $this->uri->url_encrypt("mailserver/savepolicysettings"));
        }

        $this->view->setVar('post_url', $post_url);
        $this->view->setVar('validate_url', $validate_url);

        $this->breadcrumbs->add(__('Mail Server General Settings'),'','');

        $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");

        $mail_data = json_decode($this->mail_server->data, true);
        $mail_data["spam_status"] = $spam_settings ? $spam_settings->status : false;
        $options = array(
            'part'              => 'general',
            'server_type'       => $this->mail_server->server_type,
            'mail_server_data'  => $mail_data,
        );


        $this->view->form = new MailServerForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $mail_server_settings = new MailServer();
            $mail_server_settings->setPost("server_id", $server_id);
            $mail_server_settings->setPost("server_type", $this->mail_server->server_type);
//
            $status = $mail_server_settings->updateMailSettings($server_id);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($mail_server_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status == true) {
                    $this->flashSession->success(__('Mail Server settings updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function smtpAction()
    {
        $this->tag->appendTitle(__('SMTP Queue'));

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->breadcrumbs->add(__('SMTP Queue'),'','');

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true) ) {
            $this->view->disable();
            $mail_server_settings = new MailServer();
            $result = $mail_server_settings->queueList($server_id, $this->system_data["system_type"]);
            $this->response->setJsonContent($result)->send();
        }
    }

    public function clearAction()
    {
        $this->view->disable();
        if($this->request->isPost() == true) {
            $mail_server_settings  = new MailServer();
            if($this->system_data["system_type"] == CLOUD) {
                $server_id = $this->server_id;
                $this->view->setVar("server_id", $this->server_id);
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }

            $mail_server_settings->clearQueue($server_id, $this->system_data["system_type"]);

            $redirect_url = $this->uri->url_encrypt("mailserver/smtp");
            if($this->system_data["system_type"] == CLOUD) {
                $redirect_url = $this->uri->url_encrypt("mailserver/smtp/server/$this->server_id");
            }
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Clearing successful');
            $messages['redirect_url'] = $redirect_url;

            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    public function removeQueueAction($id)
    {
        $this->view->disable();
        if($this->request->isPost() == true) {
            $mail_server_settings  = new MailServer();
            if($this->system_data["system_type"] == CLOUD) {
                $server_id = $this->server_id;
                $this->view->setVar("server_id", $this->server_id);
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }
            $mail_server_settings->removeQueueMsg($server_id, $this->system_data["system_type"], $id);
            $redirect_url = $this->uri->url_encrypt("mailserver/smtp");
            if($this->system_data["system_type"] == CLOUD) {
                $redirect_url = $this->uri->url_encrypt("mailserver/smtp/server/$this->server_id");
            }
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Message deleted');
            $messages['redirect_url'] = $redirect_url;

            $this->response->setJsonContent($messages)->send();
            exit;
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }


    public function multipleQueueAction()
    {
        $mail_server_settings  = new MailServer();
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }
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
                    case "removequeue":
                        $mail_server_settings->removeQueueMsg($server_id, $this->system_data["system_type"], $id);
                        $messages['messages'] = __('Messages deleted');
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

    public function flushAction()
    {
        $mail_server_settings  = new MailServer();
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }
        $this->view->disable();
        if($this->request->isPost() == true) {
            $redirect_url = $this->uri->url_encrypt("mailserver/smtp");
            if($this->system_data["system_type"] == CLOUD) {
                $redirect_url = $this->uri->url_encrypt("mailserver/smtp/server/$this->server_id");
            }
            $mail_server_settings->retryQueue($server_id, $this->system_data["system_type"]);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Queued messages is retrying');
            $messages['redirect_url'] = $redirect_url;


            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    public function retrieveAction()
    {
        $redirect_url = $this->uri->url_encrypt("mailserver/generalsettings");
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("mailserver/generalsettings/server/$this->server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }
        $system_mail_server = MailServerSettings::findFirst("server_id = '$server_id'");

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['redirect_url'] = $redirect_url;
        $messages['messages'] = __('Retrieved data from mail server');
        $this->view->disable();

        if(!$system_mail_server) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Retrieving failed');
        }
        else {
            $mail_server_settings  = new MailServer();
            if($this->system_data["system_type"] == CLOUD) {
                $server_id = $this->server_id;
                $this->view->setVar("server_id", $this->server_id);
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }

            $result = $mail_server_settings->retrieve($server_id, $this->system_data["system_type"]);
            $mail_settings = new MailServer();
            $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");

            $post_data = json_decode($result, true);
            $post_data["server_type"] = $system_mail_server->server_type;
            $post_data["server_id"] = $server_id;
            $post_data["spam_filter_status"] = $spam_settings->status == ACTIVE ? ON : OFF;

            $mail_settings->setPosts($post_data);
            $status = $mail_settings->saveMailSettings(true, $server_id);

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Retrieving failed');
                $messages['errors'] = [];

                foreach ($mail_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
        }

        $this->response->setJsonContent($messages)->send();
    }
}