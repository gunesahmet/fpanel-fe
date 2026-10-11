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
namespace Mail\Controller;

use FPanel\Domain\Mails;
use FPanel\Forms\MailForm;
use FPanel\Globals\Globals;
use FPanel\Models\Mails as Mail;
use Phalcon\Http\ResponseInterface;

trait MailAlias
{
    public function aliasAction($mail_id)
    {
        $this->tag->appendTitle(__('Mail Alias List'));
        $data = $this->user_data;

        $mails = Mail::findFirstById($mail_id);
        if (!$mails) {
            $this->view->setVar('error_content', __('Mail Account not found'));
            $this->response->setStatusCode(404, __('Mail Account not found'));
            $this->view->pick("errors/404");
            return true;
        }

        if (isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $this->breadcrumbs->add(__('Mail Account List'), 'mail/accounts/domain/' . $this->domain_id, ' ');
            $nested = true;
        } else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $this->breadcrumbs->add(__('Mail Account List'), 'mail/accounts', ' ');
            $nested = false;
        }

        $this->breadcrumbs->add(__('Mail Alias List'), '', '');

        $this->view->setVar('mail_id', $mail_id);

        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true)
                $this->view->disable();
            $mail = new Mails();
            return $this->response->setJsonContent($mail->listAlias($mail_id, $domain_id, $nested))->send();
        }
        return true;
    }

    /**
     * @param $mail_id
     * @return bool|ResponseInterface|void
     */
    public function createaliasAction($mail_id)
    {
        $this->tag->appendTitle(__('Create Mail Alias'));
        $data = $this->user_data;

        $domain_id = $this->domain_id ?? $data['user']['domain_info']['domain_id'];

        $mail = Mail::findFirst("id = '$mail_id' AND domain_id ='$domain_id'");
        if ($mail) {
            $mail_data = $mail->toArray();
            $mail_name = $mail_data['mail_name'];
        } else {
            $this->view->setVar('error_content', __('Mail Account not found'));
            $this->response->setStatusCode(404, __('Mail Account not found'));
            $this->view->pick("errors/404");
            return true;
        }

        if (isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_name;
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Mail Account List'), 'mail/accounts/domain/' . $domain_id, ' ');
            $this->breadcrumbs->add($mail_name . '@' . $domain_name, 'mail/edit/' . $mail_id . '/domain/' . $domain_id, ' ');
            $this->breadcrumbs->add(__('Mail Alias List'), "mail/alias/$mail_id/domain/$domain_id", ' ');
            $redirect_url = $this->uri->url_encrypt("mail/alias/$mail_id/domain/$domain_id");
            $post_url = $this->uri->url_encrypt("mail/createalias/$mail_id/domain/$domain_id");
        } else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Mail Account List'), 'mail/accounts', ' ');
            $this->breadcrumbs->add($mail_name . '@' . $domain_name, 'mail/edit/' . $mail_id, ' ');
            $this->breadcrumbs->add(__('Mail Alias List'), "mail/alias/$mail_id", ' ');
            $redirect_url = $this->uri->url_encrypt("mail/alias/$mail_id");
            $post_url = $this->uri->url_encrypt("mail/createalias/$mail_id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Create Mail Alias'), '', '');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('mail_id', $mail_id);

        $options = array(
            'action' => 'create',
            'page' => 'alias',
            'domain_id' => $domain_id
        );

        $this->view->form = new MailForm(null, $options);
        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'mail_account' => $mail_name,
                'domain_name' => $domain_name,
                'domain_id' => $domain_id,
                'mail_id' => $mail_id,
                'alias_name' => $this->request->getPost('mail_alias')
            );
            $mail_alias = new Mails();
            $mail_alias->setPosts($post_data);
            $mail_alias_save = $mail_alias->createMailAlias();
            if ($mail_alias_save == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];
                foreach ($mail_alias->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if (!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($mail_alias_save == true) {
                    $this->flashSession->success(__('Mail Alias Created'));
                    return $this->response->redirect($redirect_url, true);
                }
            }
        }
    }

    public function editaliasAction($alias_id, $mail_id)
    {
        $this->tag->appendTitle(__('Edit Mail Alias'));
        $data = $this->user_data;

        $domain_id = $this->domain_id ?? $data['user']['domain_info']['domain_id'];

        $mail = Mail::findFirst("id = '$mail_id' AND domain_id ='$domain_id'");
        if ($mail) {
            $mail_data = $mail->toArray();
            $mail_name = $mail_data['mail_name'];
        } else {
            $this->view->setVar('error_content', __('Mail Account not found'));
            $this->response->setStatusCode(404, __('Mail Account not found'));
            $this->view->pick("errors/404");
            return true;
        }

        if (isset($this->domain_id)) {
            $domain_name = $this->domain_name;
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Mail Account List'), 'mail/accounts/domain/' . $domain_id, ' ');
            $this->breadcrumbs->add($mail_name . '@' . $domain_name, 'mail/edit/' . $mail_id . '/domain/' . $domain_id, ' ');
            $this->breadcrumbs->add(__('Mail Alias List'), "mail/alias/$mail_id/domain/$domain_id", ' ');
            $redirect_url = $this->uri->url_encrypt("mail/alias/$mail_id/domain/$domain_id");
            $post_url = $this->uri->url_encrypt("mail/editalias/$alias_id/$mail_id/domain/$domain_id");
        } else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Mail Account List'), 'mail/accounts', ' ');
            $this->breadcrumbs->add($mail_name . '@' . $domain_name, 'mail/edit/' . $mail_id, ' ');
            $this->breadcrumbs->add(__('Mail Alias List'), "mail/alias/$mail_id", ' ');
            $redirect_url = $this->uri->url_encrypt("mail/alias/$mail_id");
            $post_url = $this->uri->url_encrypt("mail/editalias/$alias_id/$mail_id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Mail Alias'), '', '');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('mail_id', $mail_id);
        $this->view->setVar('alias_id', $alias_id);

        $mail_alias = \FPanel\Models\MailAlias::findFirstById($alias_id);
        if (!$mail_alias) {
            $this->view->setVar('error_content', __('Mail Alias not found'));
            $this->response->setStatusCode(404, __('Mail Alias not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $mail_alias_data = $mail_alias->toArray();

        $options = array(
            'action' => 'edit',
            'page' => 'alias',
            'domain_id' => $domain_id,
            'alias_name' => $mail_alias_data['alias_name'],
        );


        if ($this->request->isPost() == true) {
            if ($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'mail_account' => $mail_name,
                'domain_name' => $domain_name,
                'domain_id' => $domain_id,
                'mail_id' => $mail_id,
                'alias_id' => $alias_id,
                'alias_name' => $this->request->getPost('mail_alias'),
                'old_aliasname' => $mail_alias_data['alias_name']
            );
            $mail_alias = new Mails();
            $mail_alias->setPosts($post_data);
            $mail_alias_update = $mail_alias->updateMailAlias();
            if ($mail_alias_update == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($mail_alias->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if (!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($mail_alias_update == true) {
                    $this->flashSession->success(__('Mail Alias updated'));
                    return $this->response->redirect($redirect_url, true);
                }
            }
        }

        $this->view->form = new MailForm(null, $options);
        return true;
    }

    public function deletealiasAction($alias_id, $mail_id)
    {
        $this->view->disable();
        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $messages = $this->deleteAlias($alias_id, $mail_id);
            $this->response->setJsonContent($messages)->send();
        } else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"), true);
        }
    }
}