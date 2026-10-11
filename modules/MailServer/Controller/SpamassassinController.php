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
use FPanel\MailServer\SpamSettings as ServerSpamSettings;
use FPanel\Models\Servers;
use FPanel\Models\SpamSettings;
use FPanel\Models\SystemPackages;
use FPanel\Process\MailServer;

/**
 * Class SpamassassinController
 * @package MailServer\Controller
 */
class SpamassassinController extends MailserverController
{
    /**
     * Spam Settings
     */
    public function spamsettingsAction()
    {
        $this->view->pick("mailserver/spamsettings");
        $this->tag->appendTitle(__('Mail Server Spam Settings'));
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $redirect_url = $this->uri->url_encrypt('mailserver/spamsettings/server/'.$server_id);
            $post_url = $this->uri->url_encrypt('mailserver/spamsettings/server/'.$server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt('mailserver/spamsettings');
            $post_url = $this->uri->url_encrypt('mailserver/spamsettings');
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Mail Server Spam Settings'),'','');

        $installed = false;

        $system_pkg = $this->queryBuilder->select([
            "*"
        ])
            ->from("system_packages")
            ->where([
                "conditions" => "system_packages.server_id = ?1",
                "bind" => [1 => $server_id]
            ])
            ->andWhere([
                "conditions" => "system_packages.package_name LIKE ?1",
                "bind" => [1 => "spamassassin%"]
            ])
        ->findFirst();
        if($system_pkg) {
            $installed = true;
        }

        $status = false;
        $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");
        if($spam_settings)
            if($spam_settings->status == ACTIVE) {
                $status = true;
            }

        if($installed == false) {
            $this->view->setVar('error_content',__('Spam Service not installed'));
            $this->response->setStatusCode(404, 'Not Found');
            $this->view->pick("errors/404");
            return true;
        }
        else {
            if(!$status) {
                $this->view->setVar('error_content',__('Spam Service is not enabled. You have to enable from  %general_settings% section.',
                    [ "general_settings" => "<a class='text-info' href='". $this->uri->url_encrypt("mailserver/generalsettings/server/$server_id") ."'>" . __("General Settings") . "</a>"]));
                $this->response->setStatusCode(406, 'Forbidden');
                $this->view->pick("errors/406");
                return true;
            }
        }

        $options = array(
            'part'    => 'spam',
            'spam_data' => $spam_settings->toArray()
        );

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $post_data = array(
                'server_id' => $server_id,
                'status' => $this->request->getPost('spam_filter_status',null,"off"),
                'scores' => $this->request->getPost('spam_scores',null,"5.0"),
                'pyzor' => $this->request->getPost('pyzor_support',null,"off"),
                'razor' => $this->request->getPost('razor_support',null,"off"),
                'skip_rbl' => $this->request->getPost('skip_rbl',null,"off"),
                'bayes' => $this->request->getPost('bayes_support',null,"off"),
                'spam_report' => $this->request->getPost('spam_report',null,"off"),

                'use_bayes_rules' => $this->request->getPost('use_bayes_rules',null,"off"),
                'bayes_auto_learn_on_error' => $this->request->getPost('bayes_auto_learn_on_error',null,"off"),
                'bayes_auto_learn' => $this->request->getPost('bayes_auto_learn',null,"off"),
                'bayes_auto_learn_threshold_nonspam' => $this->request->getPost('bayes_auto_learn_threshold_nonspam',null,"off"),
                'bayes_auto_learn_threshold_spam' => $this->request->getPost('bayes_auto_learn_threshold_spam',null,"off"),
                'rewrite_header' => $this->request->getPost('rewrite_header'),
            );

            $spam_settings = new ServerSpamSettings();

            $spam_settings->setPosts($post_data);

            $status = $spam_settings->updateSpamSettings();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($spam_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status == true) {
                    $this->flashSession->success(__('Spam settings updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }

        }

        $this->view->form = new MailServerForm(null, $options);
        return true;
    }

    /**
     * @return bool|void
     */
    public function blacklistAction()
    {
        $this->tag->appendTitle(__('Mail Server Black List'));
        $this->view->pick("mailserver/blacklist");

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->breadcrumbs->add(__('Mail Server Blacklist Settings'),'','');

        $spam_status = false;
        $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");
        if($spam_settings)
            if($spam_settings->status == "1")
                $spam_status = true;

        if($spam_status == false) {
            $this->view->setVar('error_content',__('Spam service not installed'));
            $this->response->setStatusCode(404, __('Spam service not installed'));
            $this->view->pick("errors/404");
            return true;
        }

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true) ) {
            $this->view->disable();
            $spam_settings = new ServerSpamSettings();
            $this->response->setJsonContent($spam_settings->spamlists($server_id,"black"))->send();
        }
    }

    /**
     * @return bool|void
     */
    public function whitelistAction()
    {
        $this->tag->appendTitle(__('Mail Server Whitelist'));
        $this->view->pick("mailserver/whitelist");

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->breadcrumbs->add(__('Mail Server Whitelist Settings'),'','');

        $spam_status = false;
        $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");
        if($spam_settings)
            if($spam_settings->status == "1")
                $spam_status = true;

        if($spam_status == false) {
            $this->view->setVar('error_content',__('Spam service not installed'));
            $this->response->setStatusCode(404, __('Spam service not installed'));
            $this->view->pick("errors/404");
            return true;
        }

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true) ) {
            $this->view->disable();
            $spam_settings = new ServerSpamSettings();
            $this->response->setJsonContent($spam_settings->spamlists($server_id,"white"))->send();
        }
    }

    /**
     * Black List Create
     */
    public function blacklistcreateAction()
    {

        $this->tag->appendTitle(__('Mail Server Create Blacklist Rule'));

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/server/'.$server_id,' ');
            $this->breadcrumbs->add(__('Mail Server Blacklist Settings'),'mailserver/blacklist/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("mailserver/blacklist/server/".$server_id);
            $post_url = $this->uri->url_encrypt("mailserver/blacklistcreate/server/".$server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $this->breadcrumbs->add(__('Mail Server Blacklist Settings'),'mailserver/blacklist',' ');
            $redirect_url = $this->uri->url_encrypt("mailserver/blacklist");
            $post_url = $this->uri->url_encrypt("mailserver/blacklistcreate");
        }

        $spam_status = false;
        $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");
        if($spam_settings)
            if($spam_settings->status == "1")
                $spam_status = true;

        if($spam_status == false) {
            $this->view->setVar('error_content',__('Spam service not installed'));
            $this->response->setStatusCode(404, __('Spam service not installed'));
            $this->view->pick("errors/404");
            return true;
        }

        $this->view->setVar('spam_status', true);

        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Mail Server Create Blacklist Rule'),'','');


        $options = array(
            'part'    => 'blacklist',
            'action' => 'create'
        );
        $this->view->form = new MailServerForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

             $spamlists = new ServerSpamSettings();
            $status = $spamlists->createList('black', $server_id, $this->request->getPost('blacklist_rule'));
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($spamlists->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Black List rule created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }

        }
        return true;
    }

    /**
     * White List Create
     */
    public function whitelistcreateAction()
    {
        $this->tag->appendTitle(__('Mail Server White List Create'));

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/server/'.$server_id,' ');
            $this->breadcrumbs->add(__('Mail Server Whitelists'),'mailserver/whitelist/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("mailserver/whitelist/server/".$server_id);
            $post_url = $this->uri->url_encrypt("mailserver/whitelistcreate/server/".$server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $this->breadcrumbs->add(__('Mail Server Whitelists'),'mailserver/whitelist',' ');
            $redirect_url = $this->uri->url_encrypt("mailserver/whitelist");
            $post_url = $this->uri->url_encrypt("mailserver/whitelistcreate");
        }

        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Mail Server Create Whitelist Rule'),'','');

        $spam_status = false;
        $spam_settings = SpamSettings::findFirst("server_id = '$server_id'");
        if($spam_settings)
            if($spam_settings->status == "1")
                $spam_status = true;

        if($spam_status == false) {
            $this->view->setVar('error_content',__('Spam service not installed'));
            $this->response->setStatusCode(404, __('Spam service not installed'));
            $this->view->pick("errors/404");
            return true;
        }

        $options = array(
            'part'    => 'whitelist',
            'action' => 'create'
        );
        $this->view->form = new MailServerForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $spamlists = new ServerSpamSettings();
            $status = $spamlists->createList('white', $server_id, $this->request->getPost('whitelist_rule'));
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($spamlists->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('White List rule created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }

        }
        return true;
    }

    /**
     * Black List Delete
     * @param $id
     */
    public function deleteblackAction($id)
    {
        $this->view->disable();

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {

            $messages = $this->delete($id,"black");
            $this->response->setJsonContent($messages)->send();

        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"));
        }

    }

    /**
     * Black List Delete
     * @param $id
     */
    public function deletewhiteAction($id)
    {
        $this->view->disable();

        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $dataRules = $this->delete($id,"white");
            $this->response->setJsonContent($dataRules)->send();

        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"));
        }
    }

    /**
     * @param $id
     * @param $type
     * @return array
     */
    protected function delete($id, $type): array
    {
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $spam_lists = new ServerSpamSettings();
        $status = $spam_lists->deleteList($type, $server_id, $id);
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Delete successful');

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Delete failed');
        }

        return $messages;
    }

    /**
     * @param $type
     */
    public function multipleAction($type)
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
                        $messages = $this->delete($id, $type);
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

    public function initspamAction()
    {
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $redirect_url = $this->uri->url_encrypt("mailserver/spamsettings/server/$this->server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("mailserver/spamsettings");
        }


        $this->view->disable();
        $mailsrvmng = new MailServer();
        $mailsrvmng->setServerId($server_id);
        $mailsrvmng->initSpam();

        $spam_settings = new ServerSpamSettings();
        $spam_settings->saveDefaultSpamSettings(ACTIVE, $server_id, ACTIVE);
        $this->response->redirect($redirect_url);
    }
}