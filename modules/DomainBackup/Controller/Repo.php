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
 */namespace DomainBackup\Controller;

use FPanel\Backup\Backup;
use FPanel\Forms\BackupRepoForm;
use FPanel\Globals\Globals;
use FPanel\Models\FtpRepo;
use FPanel\Models\Users;

trait Repo
{
    public function repoAction()
    {
        $this->tag->appendTitle(__('Backup Repo Lists'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
//            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
            $nested = true;
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
//            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
            $nested = false;
        }

        $this->breadcrumbs->add(__('Backup Repo Lists'), '', '');

        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listRepo($domain_id, $nested))->send();
        }
    }

    public function createRepoAction()
    {
        $this->tag->appendTitle(__('Add New FTP Repo'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/repo/domain/'.$domain_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backup/repo/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt('backup/repo/create/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/repo', ' ');
            $redirect_url = $this->uri->url_encrypt('backup/repo');
            $post_url = $this->uri->url_encrypt('backup/repo/create');
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Add FTP Repo'), '', '');


        $this->view->form = new BackupRepoForm();
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $backup = new Backup();
            $post = $this->request->getPost();
            $post['domain_id'] = $domain_id;
            $post['domain_name'] = $domain_name;
            $backup->setPosts($post);

            $status = $backup->createRepo();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];

                foreach ($backup->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Repo Created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function editRepoAction($id)
    {
        $this->tag->appendTitle(__('Add New FTP Repo'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/repo/domain/'.$domain_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backup/repo/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt("backup/repo/edit/$id/domain/$domain_id");
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/repo', ' ');
            $redirect_url = $this->uri->url_encrypt('backup/repo');
            $post_url = $this->uri->url_encrypt("backup/repo/edit/$id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Add FTP Repo'), '', '');


        $user = Users::findFirst("name = '$domain_name'");
        $ftp_repo = FtpRepo::findFirst("client_id = '$user->id' AND id = '$id'");
        if(!$ftp_repo) {
            $this->view->setVar('error_content',__('FTP Repo not found'));
            $this->response->setStatusCode(404, __('FTP Repo not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $options = array(
            'action' => 'edit',
            'data'=> $ftp_repo->toArray()
        );
        $this->view->form = new BackupRepoForm(null, $options);
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $messages['redirect_url'] = $redirect_url;

            $backup = new Backup();
            $post = $this->request->getPost();
            $post['domain_id'] = $domain_id;
            $post['domain_name'] = $domain_name;

            $backup->setPosts($post);
            $status = $backup->editRepo($id);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($backup->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }
            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Repo updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function deleteRepoAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->domain_id)) {
//                $domain_id = $this->domain_id;
                $domain_name = $this->domain_data['name'];
//                $url = $this->uri->url_encrypt('backup/domain/'.$domain_id);
            }
            else {
                $domain_name = $data['user']['domain_info']['domain_name'];
//                $url = $this->uri->url_encrypt('backup');
            }
            $backup = new Backup();
            $result = $backup->deleteRepo($domain_name, $id);

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            if ($result == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Repo not found');
            }

            $this->response->setJsonContent($messages)->send();

        }
    }
}