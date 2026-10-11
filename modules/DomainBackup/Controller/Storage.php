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
use FPanel\Forms\BackupStorageForm;
use FPanel\Globals\Globals;
use FPanel\Models\CloudStorage;
use FPanel\Models\Users;

trait Storage
{
    /**
     * @return bool
     */
    public function storageAction(): bool
    {
        $this->tag->appendTitle(__('Backup Storage Lists'));
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
            $this->view->setVar('domain_id', $domain_id);
            $nested = true;
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
            $nested = false;
        }

        $this->view->setVar('domain_id', $domain_id);
        $domain_users = Users::findFirst("username = '$domain_name'");
        if($domain_users)
            $client_id = $domain_users->id;
        else {
            $this->view->setVar('error_content',__('Domain not found'));
            $this->response->setStatusCode(404, __('Domain not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $this->breadcrumbs->add(__('Backup Storage Lists'), '', '');

        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listDomainStorages($client_id, $domain_id, $nested))->send();
        }
        return true;
    }

    public function addStorageAction()
    {
        $this->view->pick('domainbackup/createstorage');
        $this->tag->appendTitle(__('Add New Storage'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/storage/domain/'.$domain_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backup/storage/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt("backup/storage/create/domain/$domain_id");
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/storage', ' ');
            $redirect_url = $this->uri->url_encrypt('backup/storage');
            $post_url = $this->uri->url_encrypt("backup/storage/create");
        }
        $this->view->setVar('post_url', $post_url);
        $this->view->setVar('domain_id', $domain_id);

        $domain_users = Users::findFirst("username = '$domain_name'");
        $client_id = $domain_users->id;

        $this->breadcrumbs->add(__('Add New Storage'), '', '');


        $this->view->form = new BackupStorageForm();
        if($this->request->isPost() == true) {

            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $backup = new Backup();
            $post = array(
                'type' => $this->request->getPost('storages'),
                'name' => $this->request->getPost('storage_name'),
                'access_token' => $this->request->getPost('access_token'),
                'gdrive_client_id' => $this->request->getPost('gdrive_client_id'),
                'gdrive_secret_key' => $this->request->getPost('gdrive_secret_key'),
                'folder' => $this->request->getPost('folder'),
                'status' => $this->request->getPost('status',null,"off"),
            );
            $backup->setPosts($post);

            $status = $backup->createStorage($client_id);
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
                    $this->flashSession->success(__('Storage added'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function editStorageAction($id)
    {
        $this->view->pick('domainbackup/editstorage');
        $this->tag->appendTitle(__('Edit Storage'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/storage/domain/'.$domain_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backup/storage/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt("backup/storage/edit/$id/domain/$domain_id");
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backup/storage', ' ');
            $redirect_url = $this->uri->url_encrypt('backup/storage');
            $post_url = $this->uri->url_encrypt("backup/storage/edit/$id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->view->setVar('domain_id', $domain_id);
        $domain_users = Users::findFirst("username = '$domain_name'");
        $client_id = $domain_users->id;

        $this->breadcrumbs->add(__('Edit Storage'), '', '');


        $storage = CloudStorage::findFirst("client_id = '$client_id' AND id = '$id'");
        if(!$storage) {
            $this->view->setVar('error_content',__('Storage not found'));
            $this->response->setStatusCode(404, __('Storage not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $options = array(
            'action' => 'edit',
            'data'=> $storage->toArray()
        );
        $this->view->form = new BackupStorageForm(null, $options);
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $messages['redirect_url'] = $redirect_url;

            $backup = new Backup();
            $post = array(
//                'type' => $this->request->getPost('storages'),
                'type' => "dropbox",
                'name' => $this->request->getPost('storage_name'),
                'access_token' => $this->request->getPost('access_token'),
                'g_secret' => $this->request->getPost('secret_key'),
                'google_client_id' => $this->request->getPost('gclient_id'),
                'folder' => $this->request->getPost('folder'),
                'status' => $this->request->getPost('status',null,"off"),
            );

            $backup->setPosts($post);
            $status = $backup->editStorage($id, $client_id);
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
                    $this->flashSession->success(__('Storage updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function deleteStorageAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $domain_name = $this->domain_data['name'];
                $this->view->setVar('domain_id', $domain_id);
                $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
                $this->breadcrumbs->add(__('Repo List'), 'backup/storage/domain/'.$domain_id, ' ');
//                $redirect_url = $this->uri->url_encrypt('backup/storage/domain/'.$domain_id);
            }
            else {
//                $domain_id = $data['user']['domain_info']['domain_id'];
                $domain_name = $data['user']['domain_info']['domain_name'];
                $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
                $this->breadcrumbs->add(__('Repo List'), 'backup/storage', ' ');
//                $redirect_url = $this->uri->url_encrypt('backup/storage');
            }

            $domain_users = Users::findFirst("username = '$domain_name'");
            $client_id = $domain_users->id;
            $backup = new Backup();
            $result = $backup->deleteStorage($id, $client_id);

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            if ($result == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Storage not found');
            }

            $this->response->setJsonContent($messages)->send();

        }
    }
}