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
 */namespace Support\Controller;

use FPanel\Forms\SupportForm;
use FPanel\Globals\Globals;
use FPanel\Models\Support;
use FPanel\Models\SystemSettings;
use FPanel\System\Support as SupportLibrary;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class SupportController
 * @package Support\Controller
 */
class SupportController extends ControllerBase
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
        $support = Support::findFirst("status = '1'");
        $status = false;
        if($support)
            $status = true;
        $this->view->setVar('opened_support', $status);
    }

    public function indexAction()
    {
        $this->tag->appendTitle( __('Help & Support'));
        if($this->user_type == SUPPORT_USER) {
            $this->view->setVar('support_user',true);
        }
        else {
            $this->view->setVar('support_user',false);
        }


        $this->breadcrumbs->add(__('Help & Support'),'','');

        $this->checkSupport();
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $this->view->disable();
            $supports = new SupportLibrary();
            $this->response->setJsonContent($supports->supportList())->send();
        }
    }

    /**
     * @return bool|ResponseInterface|void
     */
    public function createAction()
    {
        $this->tag->appendTitle( __('Request Support'));

        $redirect_url = $this->uri->url_encrypt('support');

        $this->breadcrumbs->add(__('Help & Support'),'support',' ');

        $this->breadcrumbs->add(__('Request Support'),'','');

        $this->view->setVar('post_url', $this->uri->url_encrypt("support/create"));
        $notifications = SystemSettings::findFirst("key = 'notyfy'");
        $notify_status = false;
        $notify_data = [];
        if($notifications) {
            $notify_data = json_decode($notifications->value, true);
            if(!empty($notify_data) && isset($notify_data["host"])) {
                $notify_status = true;
            }
        }

        if(!$notify_status) {
            $this->view->setVar('error_content',__('Before opening a support request, you should set the %notification_settings%.', ["notification_settings" => '<a href="' . $this->uri->url_encrypt("system/notify") . '">' . __("Notification Settings") . '</a>']));
            $this->response->setStatusCode(403, __('You can not request support'));
            $this->view->pick("errors/403");
            return true;
        }
        $support = Support::findFirst("status = '1'");
        if($support) {
            $this->view->setVar('error_content',__('You have already opened a support request'));
            $this->response->setStatusCode(403, __('You have already opened a support request'));
            $this->view->pick("errors/403");
            return true;
        }
        $this->checkOpenedSupport();

        $options = array(
            'action'    => 'create'
        );
        $this->view->form = new SupportForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');
            $messages['redirect_url'] = $redirect_url;

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode!');

                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode([$messages['messages']]));
                else
                    return $this->response->setJsonContent($messages)->send();

                return true;
            }
            $support = new SupportLibrary();
            $support->setPost("request_user", $this->user_data['user']['info']['username']);

            $notifications = SystemSettings::findFirst("key = 'notyfy'");
            $notify_status = false;
            $notify_data = [];
            if($notifications) {
                $notify_data = json_decode($notifications->value, true);
                if(!empty($notify_data) && isset($notify_data["host"])) {
                    $notify_status = true;
                }
            }

            if(!$notify_status) {
                $this->view->setVar('error_content',__('Before opening a support request, you should set the %notification_settings%.', ["notification_settings" => '<a href="' . $this->uri->url_encrypt("system/notify") . '">' . __("Notification Settings") . '</a>']));
                $this->response->setStatusCode(403, __('You can not request support'));
                $this->view->pick("errors/403");
                return true;
            }

            $support->setPost("notify_settings", $notify_data);
            $status = $support->createSupport();

            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($support->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }

                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Support created'));
                    $this->response->redirect($redirect_url,true);
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
        $this->tag->appendTitle( __('Update Support'));

        $redirect_url = $this->uri->url_encrypt('support');

        $this->view->setVar('id', $id);
        $this->breadcrumbs->add(__('Help & Support'),'support',' ');

        $this->breadcrumbs->add(__('Update Support'),'','');

        $this->view->setVar('post_url', $this->uri->url_encrypt("support/edit/$id"));


        $this->checkOpenedSupport($id);
        $support_data = Support::findFirstById($id);
        if($support_data) {
            $support_data = $support_data->toArray();
        }

        $notifications = SystemSettings::findFirst("key = 'notyfy'");
        $notify_status = false;
        $notify_data = [];
        if($notifications) {
            $notify_data = json_decode($notifications->value, true);
            if(!empty($notify_data) && isset($notify_data["host"])) {
                $notify_status = true;
            }
        }

        if(!$notify_status) {
            $this->view->setVar('error_content',__('Before opening a support request, you should set the %notification_settings%.', ["notification_settings" => '<a href="' . $this->uri->url_encrypt("system/notify") . '">' . __("Notification Settings") . '</a>']));
            $this->response->setStatusCode(403, __('You can not request support'));
            $this->view->pick("errors/403");
            return true;
        }

        $options = array(
            'action'    => 'edit',
            'request_user_email' => $support_data['request_user_email'],
            'username' => $support_data['user'],
            'password' => $support_data['password'],
            'title' => $support_data['title'],
            'support_message' => $support_data['description'],
            'support_status' => $support_data['status'],
            'disable_logins' => $support_data['disable_clients_logins'],
        );
        $this->view->form = new SupportForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');
            $messages['redirect_url'] = $redirect_url;

            $support = new SupportLibrary();
            $support->setPost("id", $id);
            $support->setPost("request_user", $this->user_data['user']['info']['username']);



            $support->setPost("notify_settings", $notify_data);

            $status = $support->updateSupport();

            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($support->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Support updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    /**
     * @return bool|ResponseInterface|void
     */
    public function supportuserAction()
    {
        if($this->user_type != SUPPORT_USER) {
            $this->view->setVar('error_content',__('URL not found'));
            $this->response->setStatusCode(404, __('URL not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $this->view->pick('support/edit');

        $this->tag->appendTitle( __('Support User Area'));

        $redirect_url = $this->uri->url_encrypt('support');
        $this->view->setVar('post_url', $this->uri->url_encrypt("support/supportuser"));

        $this->breadcrumbs->add(__('Help & Support'),'support',' ');

        $this->breadcrumbs->add(__('Support User Area'),'','');

        
        $options = array(
            'action'    => 'support_user',
        );
        if(($this->request->isPost() == true)) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $support_close = $this->request->getPost('support_status',null,OFF);
            $post_data = array(
                'user_email' => $this->request->getPost('user_email'),
                'support_status' => $support_close,
                'disable_logins'    => false,
                'user_message' => $this->request->getPost('user_message'),
            );
            $new_support = new SupportLibrary();
            $new_support->setPosts($post_data);
            $new_support_status = $new_support->closeSupport();
            if( $new_support_status == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($new_support->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            else {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Action successful');
            }
            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($new_support_status == true) {
                    $this->flashSession->success(__('Support updated'));
                    if($support_close == ON) {
                        $this->session->destroy();
                        $this->response->redirect('/',true);
                    }
                    else {
                        return $this->response->redirect($redirect_url,true);
                    }

                }
            }
        }
        $this->view->form = new SupportForm(null, $options);
    }

    /**
     * @param $id
     */
    private function checkOpenedSupport($id = null)
    {
        $connected_support = Support::findFirst("connecting_status = '1'");
        if($connected_support) {
            $this->response->redirect($this->uri->url_encrypt('support'),true);
        }
    }

    /**
     * checksupport
     */
    private function checkSupport()
    {
        $supports = Support::findFirst("connecting_status = '1'");

        if($supports) {
            $support_status = true;
        }
        else {
            $support_status = false;
        }

        $this->view->setVar('support_status', $support_status);
    }
}