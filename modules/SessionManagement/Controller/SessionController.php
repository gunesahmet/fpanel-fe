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
 */namespace SessionManagement\Controller;

use FPanel\Forms\SessionForm;
use FPanel\Globals\Globals;
use FPanel\System\Session;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class SessionController
 * @package SessionManagement\Controller
 */
class SessionController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;

    /**
     * init
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
    }

    public function indexAction()
    {
        $this->tag->appendTitle( __('Session Management'));

        $this->breadcrumbs->add(__('Session Management'),'','');

        $url = $this->uri->url_encrypt("session/deleteall");
        $this->view->setVar("remove_url", $url);
        if($this->request->isPost() && $this->request->isAjax()) {
            $this->view->disable();
            $session = new Session();
            $this->response->setJsonContent($session->sessionList())->send();
        }
    }

    /**
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        if ($this->request->isAjax() && $this->request->isPost()) {
            $this->view->disable();
            if(demo) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('This feature not running in demo mode!');
                $this->response->setJsonContent($messages)->send();
            }
            $session = new Session();
            $status = $session->sessionDelete($id);

            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('action failed');
            }
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'));
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function settingsAction()
    {
        $this->tag->appendTitle( __('System Access Settings'));
        $this->breadcrumbs->add(__('Session List'),'session',' ');

        $this->breadcrumbs->add(__('System Access Settings'),'','');


        $this->view->setVar('post_url', $this->uri->url_encrypt('session/settings'));
        $session = new Session();

        $get_access_rules = $session->getRules();

        if(!empty($get_access_rules['allowed_ip'])) {
            $allowed_ip = $get_access_rules['allowed_ip'];
        }
        else {
            $allowed_ip = array();
        }

        if(!empty($get_access_rules['blocked_ip'])) {
            $blocked_ip = $get_access_rules['blocked_ip'];
        }
        else {
            $blocked_ip = array();
        }
        $options = array(
            'page' => 'settings',
            'session_time' => $get_access_rules['timeout'],
            'allow_ip' => $get_access_rules['allow_ip'],
            'block_ip' => $get_access_rules['block_ip'],
            'allowed_ip_addresses' => $allowed_ip,
            'blocked_ip_addresses' => $blocked_ip,
        );
        $this->view->form = new SessionForm(null, $options);

        if ($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            if(demo) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode!');
                if($this->request->isAjax())
                    return $this->response->setJsonContent($messages)->send();

                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode([$messages['messages']]));

            }
            $post_data = array(
                'session_time' => $this->request->getPost('session_time',null,"0"),
                'allow_ip_address' => $this->request->getPost('allow_ip_address',null,OFF),
                'block_ip_address' => $this->request->getPost('block_ip_address',null,OFF),
                'allowed_ip_addresses' => $this->request->getPost('allowed_ip_addresses'),
                'blocked_ip_addresses' => $this->request->getPost('blocked_ip_addresses'),
            );
            $session->setPosts($post_data);
            $status = $session->saveRules();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($session->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Update successful'));
                    $this->response->redirect($this->uri->url_encrypt('session'),true);
                }
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function deleteallAction()
    {
        $this->view->disable();
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');
        if(demo) {
            $messages['type'] = 'warning';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Sorry, this feature is disabled in demo mode!');
            return $this->response->setJsonContent($messages)->send();
        }
        $ids = $this->request->getPost("id");
        $session = new Session();
        $status = $session->sessionDelete($ids);

        if (!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Action failed');
        }
        $this->response->setJsonContent($messages)->send();
    }
}