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
namespace Blacklist\Controller;

use FPanel\Forms\BlacklistForm;
use FPanel\Globals\Globals;
use FPanel\Models\Blacklist;
use FPanel\System\Blacklists;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class BlacklistController
 * @package Blacklist\Controller
 */
class BlacklistController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;

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
    }

    public function indexAction()
    {
        $this->view->pick('blacklist/index');
    }

    public function ftpusersAction()
    {
        $this->breadcrumbs->add(__('Blacklist FTP Users'),'','');
        $this->tag->appendTitle( __('Blacklist Management (FTP Users)'));
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $server_list = new Blacklists();
            $this->response->setJsonContent($server_list->blacklists('ftp_user'))->send();
        }
    }

    public function domainsAction()
    {
        $this->breadcrumbs->add(__('Blacklist Domain Name'),'','');
        $this->tag->appendTitle( __('Blacklist Management (Domain Names)'));
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $server_list = new Blacklists();
            $this->response->setJsonContent($server_list->blacklists('domain_name'))->send();
        }
    }

    public function dbusersAction()
    {
        $this->breadcrumbs->add(__('Blacklist Database Users'),'','');
        $this->tag->appendTitle( __('Blacklist Management (Database Users)'));
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $server_list = new Blacklists();
            $this->response->setJsonContent($server_list->blacklists('db_username'))->send();
        }
    }

    public function emailUsersAction()
    {
        $this->breadcrumbs->add(__('Blacklist Email Users'),'','');
        $this->tag->appendTitle( __('Blacklist Management (Email Users)'));
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $server_list = new Blacklists();
            $this->response->setJsonContent($server_list->blacklists('mail_account'))->send();
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function addFtpUserAction()
    {
        $this->breadcrumbs->add(__('Blacklist FTP Users'),'blacklist/ftpusers',' ');
        $this->breadcrumbs->add(__('Blacklist Add Ftp User'),'','');


        $this->tag->appendTitle( __('Add New FTP User'));
        $options = array(
            'action'    => 'create',
            'page'    => 'ftpuser'
        );

        $this->view->setVar('post_url', $this->uri->url_encrypt("blacklist/addftpuser"));

        $this->view->form = new BlacklistForm(null, $options);
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('FTP User Rule added');


            $blacklist = new Blacklists();
            $status = $blacklist->addRule('ftp_user');
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to add');
                $messages['errors'] = [];

                foreach ($blacklist->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('FTP User Rule saved'));
                    return $this->response->redirect($this->uri->url_encrypt("blacklist/ftpusers"),true);
                }
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function addEmailUserAction()
    {
        $this->breadcrumbs->add(__('Blacklist Email Users'),'blacklist/emailusers',' ');
        $this->breadcrumbs->add(__('Blacklist Add Email User'),'','');

        $this->view->setVar('post_url', $this->uri->url_encrypt("blacklist/addemailuser"));

        $this->tag->appendTitle( __('Add New Email User'));
        $options = array(
            'action'    => 'create',
            'page'    => 'ftpuser'
        );
        $this->view->form = new BlacklistForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Rule added');

            $blacklist = new Blacklists();
            $status = $blacklist->addRule('mail_account');
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to add');
                $messages['errors'] = [];

                foreach ($blacklist->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Email User Rule saved'));
                    return $this->response->redirect($this->uri->url_encrypt("blacklist/emailusers"),true);
                }
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function addDbUserAction()
    {
        $this->breadcrumbs->add(__('Blacklist Database Users'),'blacklist/dbusers',' ');
        $this->breadcrumbs->add(__('Blacklist Add Database User'),'','');

        $this->view->setVar('post_url', $this->uri->url_encrypt("blacklist/adddbuser"));

        $this->tag->appendTitle( __('Add New Database User'));
        $options = array(
            'action'    => 'create',
        );
        $this->view->form = new BlacklistForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Rule added');

            $blacklist = new Blacklists();
            $status = $blacklist->addRule('db_username');
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to add');
                $messages['errors'] = [];

                foreach ($blacklist->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Database User Rule saved'));
                    return $this->response->redirect($this->uri->url_encrypt("blacklist/dbusers"),true);
                }
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function addDomainAction()
    {
        $this->breadcrumbs->add(__('Blacklist Domain Names'),'blacklist/domains',' ');
        $this->breadcrumbs->add(__('Blacklist Add Domain Name'),'','');

        $this->view->setVar('post_url', $this->uri->url_encrypt("blacklist/adddomain"));

        $this->tag->appendTitle( __('Add New Domain Name'));
        $options = array(
            'action'    => 'create',
            'page'    => 'ftpuser'
        );
        $this->view->form = new BlacklistForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Rule added');

            $blacklist = new Blacklists();
            $status = $blacklist->addRule('domain_name');
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to add');
                $messages['errors'] = [];

                foreach ($blacklist->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Domain Name Rule saved'));
                    return $this->response->redirect($this->uri->url_encrypt("blacklist/domains"),true);
                }
            }
        }
    }

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function editAction($id)
    {
        $this->view->setVar("blacklist_id", $id);
        $this->tag->appendTitle( __('Edit Blacklist'));
        $blacklists = Blacklist::findFirstById($id);
        if(!$blacklists) {
            $this->view->setVar('error_content',__('Blacklist not found'));
            $this->response->setStatusCode(404, __('Blacklist not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $this->view->setVar('post_url', $this->uri->url_encrypt("blacklist/edit/$id"));

        $blacklist_data = $blacklists->toArray();
        $this->view->setVar('page', $blacklist_data['type']);
        $options = array(
            'action'    => 'edit',
            'rule'  => $blacklist_data['rule'],
        );

        $redirect_url = $this->uri->url_encrypt('blacklist/ftpusers');
        switch($blacklist_data['type']) {
            case "ftp_user":
                $this->breadcrumbs->add(__('Blacklist FTP Users'),'blacklist/ftpusers',' ');
                $this->breadcrumbs->add(__('Blacklist Edit FTP User'),'','');
                $redirect_url = $this->uri->url_encrypt('blacklist/ftpusers');
                break;
            case "db_username":
                $this->breadcrumbs->add(__('Blacklist Database Users'),'blacklist/dbusers',' ');
                $this->breadcrumbs->add(__('Blacklist Edit Database User'),'','');
                $redirect_url = $this->uri->url_encrypt('blacklist/dbusers');
                break;
            case "mail_account":
                $this->breadcrumbs->add(__('Blacklist Email Users'),'blacklist/emailusers',' ');
                $this->breadcrumbs->add(__('Blacklist Edit Email User'),'','');
                $redirect_url = $this->uri->url_encrypt('blacklist/emailusers');
                break;
            case "domain_name":
                $this->breadcrumbs->add(__('Blacklist Domain Name'),'blacklist/domains',' ');
                $this->breadcrumbs->add(__('Blacklist Edit Domain Name'),'','');
                $redirect_url = $this->uri->url_encrypt('blacklist/domains');
                break;
        }

        $this->view->form = new BlacklistForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Rule updated');

            $blacklist = new Blacklists();
            $blacklist->setPost("id", $id);
            $blacklist->setPost("type", $blacklist_data['type']);
            $blacklist->setPost("old_rule", $blacklist_data['rule']);
            $status = $blacklist->editRule();
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to edit');
                $messages['errors'] = [];

                foreach ($blacklist->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Blacklist Rule updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    public function deleteAction($id)
    {
        if($this->request->isAjax() == true)
            $this->view->disable();

        if($this->request->isPost() == true) {

            $blacklist = new Blacklists();
            $delete_blacklist = $blacklist->deleteRule($id);
            $messages = Globals::messages();
            if ($delete_blacklist == true) {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Rule added');
            }
            else {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to delete');
            }
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'));
        }
    }

}