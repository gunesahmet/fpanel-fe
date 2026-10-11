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
 */namespace Mail\Controller;

use FPanel\Domain\Mails;
use FPanel\Domain\Services;
use FPanel\Forms\MailForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use FPanel\Models\MailAlias;
use FPanel\Models\Mails as Mail;
use FPanel\Models\MailSettings;
use Main\Controller\ControllerBase;

/**
 * Class MailController
 * @package Mail\Controller
 */
class MailController extends ControllerBase
{
    use \Mail\Controller\MailAlias;

    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_name;
    protected $domain_data;
    protected $user_id;
    protected $user_type;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('email');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        $params = $this->dispatcher->getParams();

        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "domain":
                    $domain_id = $params['domain_id'];
                    $domain = Domains::findFirstById($domain_id);
                    if(!$domain) {
                        $this->view->setVar('error_content',__('Domain not found'));
                        $this->response->setStatusCode(404, __('Domain not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        if(($this->auth->getUserType() != ADMIN_USER) && ($this->auth->getUserType() != ROOT_USER))
                            $this->acl->checkParent($domain_id,DOMAIN_USER);
                        $domain_data = $domain->toArray();
                        $this->domain_id = $domain_id;
                        $this->domain_name = $domain_data['name'];
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$domain_id,' ');
                        $this->domain_data = $domain_data;
                        $this->view->setVar('client_type', DOMAIN_USER);
                        $client_menu = $this->sidebar->clientMenu(DOMAIN_USER, $domain_id);
                        $this->view->setVar("client_menu", $client_menu);
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
            $domain_id = $user_data['user']['domain_info']['domain_id'];

        }

        if(isset($this->domain_id)) {
            $redirect_url = $this->uri->url_encrypt("dashboard/domain/".$domain_id);
        }
        else {
            $redirect_url = $this->uri->url_encrypt("dashboard");
        }

        $domain_services = new Services();
        $domain_services->checkDomainService($domain_id,"mail", $redirect_url);
        return true;
    }

    public function mailsettingsAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Mail Settings'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_name;
            $this->view->setVar('domain_id', $domain_id);
            $redirect_url = $this->uri->url_encrypt("mail/mailsettings/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("mail/mailsettings/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->view->setVar('user_id', $domain_id);
            $redirect_url = $this->uri->url_encrypt("mail/mailsettings");
            $post_url = $this->uri->url_encrypt("mail/mailsettings");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Mail Settings'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $mail = MailSettings::findFirst("domain_id = '$domain_id'");
        if(!$mail) {
            $this->view->setVar('error_content',__('Mail Settings not found'));
            $this->response->setStatusCode(404, __('Mail Settings not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $mail_settings_data = $mail->toArray();
        $options = array(
            'action'    => 'edit',
            'page'      => 'settings',
            'domain_id'      => $domain_id,
            'domain_name'      => $domain_name,
            'mail_settings'      => $mail_settings_data,
        );
        $this->view->form = new MailForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $mail_settings = new Mails();
            $post_data = array(
                'domain_name'             => $domain_name,
                'domain_id'             => $domain_id,
                'forward_to_address'    => $this->request->getPost('forward_to_address',null,OFF),
                'mail_address'          => $this->request->getPost('mail_address'),
                'reject_incoming'       => $this->request->getPost('reject_incoming',null,OFF),
            );
            $mail_settings->domain_name = $domain_name;
            $mail_settings->setPosts($post_data);
            $status = $mail_settings->updateMailSettings();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($mail_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status == true) {
                    $this->flashSession->success(__('Mail settings updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function accountsAction()
    {
        if($this->user_data['user']['remaining']['domainemail'] < "1")
            $active_class = "disabled";
        else
            $active_class = "";

        $this->view->setVar('active_class', $active_class);


        $this->tag->appendTitle(__('Mail Account List'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $this->view->setVar('domain_id', $domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $this->view->setVar('user_id', $domain_id);
        }

        $this->breadcrumbs->add(__('Mail Account List'),'','');


        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $subdomains = new Mails();
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $this->response->setJsonContent($subdomains->listAccount($domain_id,true))->send();
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $this->response->setJsonContent($subdomains->listAccount($domain_id))->send();
            }
        }

    }

    public function createAction()
    {
        $this->tag->appendTitle(__('Create Mail Account'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_name;
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Mail Account List'),'mail/accounts/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('mail/accounts/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt('mail/create/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Mail Account List'),'mail/accounts',' ');
            $redirect_url = $this->uri->url_encrypt('mail/accounts');
            $post_url = $this->uri->url_encrypt('mail/create');
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Create Mail Account'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $options = array(
            'action'    => 'create',
            'page'      => 'mailaccount',
            'domain_id'      => $domain_id
        );
        $this->view->form = new MailForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'domain_name'       => $domain_name,
                'domain_id'         => $domain_id,
                'mail_account'      => $this->request->getPost('mail_account'),
                'password'          => $this->request->getPost('password'),
                'mailbox_quota'     => $this->request->getPost('mail_quota'),
                'unlimited_quota'   => $this->request->getPost('unlimited_quota',null,OFF),
                'status'            => $this->request->getPost('status',null,OFF),
            );
            $mail_account = new Mails();
            $mail_account->setPosts($post_data);
            $mail_account_save = $mail_account->createMailAccount();
            if($mail_account_save == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];
                foreach ($mail_account->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($mail_account_save == true) {
                    $this->flashSession->success(__('Mail account created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $mail_id
     * @return bool|void
     */
    public function editAction($mail_id)
    {
        $this->tag->appendTitle(__('Edit Mail Account'));

        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_name;
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Mail Account List'),'mail/accounts/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt('mail/accounts/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt("mail/edit/$mail_id/domain/$domain_id");
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Mail Account List'),'mail/accounts',' ');
            $redirect_url = $this->uri->url_encrypt('mail/accounts');
            $post_url = $this->uri->url_encrypt("mail/edit/$mail_id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Mail Account'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar("mail_id", $mail_id);

        $mail = Mail::findFirstById($mail_id);
        if($mail) {
            $mail_data = $mail->toArray();
            $password = trim($this->crypto->bdecode($mail_data['password']));
        }
        else {
            $this->view->setVar('error_content',__('Mail Account not found'));
            $this->response->setStatusCode(404, __('Mail Account not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $options = array(
            'action'            => 'edit',
            'page'              => 'mailaccount',
            'domain_id'         => $domain_id,
            'mail_name'         => $mail_data['mail_name'],
            'mailbox_quota'     => $mail_data['mailbox_quota'],
            'forward'           => $mail_data['forward'],
            'forward_addr'      => $mail_data['forward_addr'],
            'autoresponder'     => $mail_data['autoresponder'],
            'password'          => $password,
        );
        $this->view->form = new MailForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'mail_id'           => $mail_id,
                'domain_name'       => $domain_name,
                'domain_id'         => $domain_id,
                'mail_account'      => $mail_data['mail_name'],
                'password'          => $this->request->getPost('password'),
                'mailbox_quota'     => $this->request->getPost('mail_quota'),
                'unlimited_quota'   => $this->request->getPost('unlimited_quota',null,OFF),
                'forward'           => $this->request->getPost('forwarding',null,OFF),
                'autoresponder'     => $this->request->getPost('autoresponder',null,OFF),
                'forward_addr'      => $this->request->getPost("forward_to_address"),
                'old_address'       => $mail_data['mail_name'],
                'status'          => $this->request->getPost('status',null,OFF),
            );
            $mail_account = new Mails();
            $mail_account->setPosts($post_data);
            $mail_account_update = $mail_account->updateMailAccount();
            if($mail_account_update == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($mail_account->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($mail_account_update == true) {
                    $this->flashSession->success(__('Mail account updated'));
                    $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    public function deleteAction($mail_id)
    {
        $this->view->disable();
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
             $messages = $this->delete($mail_id);
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    private function delete($mail_id): array
    {
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_name;
            $this->view->setVar('domain_id', $domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->view->setVar('user_id', $domain_id);
        }

        $mail = new Mails();
        $mail->setPosts(array(
            'domain_name' => $domain_name,
            'domain_id' => $domain_id,
            'mail_id' => $mail_id,
        ));
        $mail_status = $mail->deleteMailAccount();

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        if ($mail_status == false) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Mail account not found');
        }

        return $messages;
    }

    private function deleteAlias($alias_id, $mail_id): array
    {
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
            $domain_id = $this->domain_id;
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $this->user_data['user']['domain_info']['domain_id'];
        }

        $mail = new Mails();
        $mail->setPosts(array(
            'domain_name' => $domain_name,
            'domain_id' => $domain_id,
            'mail_id' => $mail_id,
            'alias_id' => $alias_id,
        ));
        $mail_status = $mail->deleteMailAlias();
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        if ($mail_status == false) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Mail Alias not found');
        }
        return $messages;
    }

    public function multipleAction($type)
    {
        $data = $this->user_data;
        $domain_id = $this->domain_id ?? $data['user']['domain_info']['domain_id'];

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
                $messages['messages'] = __('Select an alias');
            }
            else if($this->request->getPost("action") == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select an action');
            }
            else {
                switch($action) {
                    case "delete":
                        if($type == "alias") {
                            $messages['type'] = 'error';
                            $messages['title'] = __('Failed');
                            $messages['messages'] = __('Mail Alias not found');
                            $alias = MailAlias::findFirstById($id);
                            if($alias) {
                                $mail = Mail::findFirst("id = '$alias->mail_id' AND domain_id = '$domain_id'");
                                if($mail) {
                                    $messages = $this->deleteAlias($id, $mail->id);
                                }
                            }
                        }
                        else {
                            $messages = $this->delete($id);
                        }
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
}