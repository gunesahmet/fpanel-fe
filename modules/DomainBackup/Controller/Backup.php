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

use FPanel\File\Basic;
use FPanel\File\Config;
use FPanel\File\Request;
use FPanel\Forms\BackupForm;
use FPanel\Globals\Globals;
use FPanel\Models\CloudStorage;
use FPanel\Models\FtpRepo;
use FPanel\Models\IpAddress;
use FPanel\Models\Servers;
use FPanel\Models\Users;
use FPanel\Process\BackupRestore;

trait Backup
{
    public function indexAction()
    {
        $this->tag->appendTitle(__('Domain Backups'));
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $this->view->setVar('domain_id', $domain_id);
            $nested = true;
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $nested = false;
        }

        $this->breadcrumbs->add(__('Domain Backups'), '', '');


        $backup_status = false;
        $backups = \FPanel\Models\Backup::findFirst("status NOT LIKE '1' AND status NOT LIKE '4' AND domain_id = '$domain_id'");
        if($backups) {
            $backup_status = true;
        }
        $this->view->setVar("backup_status", $backup_status);

        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new \FPanel\Backup\Backup();
            $this->response->setJsonContent($backup->listBackup($domain_id, $nested))->send();
        }

    }

    public function createAction()
    {
        $this->tag->appendTitle(__('Create Domain Backups'));
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $redirect_url = $this->uri->url_encrypt('backup/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt('backup/create/domain/'.$domain_id);
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $redirect_url = $this->uri->url_encrypt('backup');
            $post_url = $this->uri->url_encrypt('backup/create');
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Create Domain Backups'), '', '');


        $backups = \FPanel\Models\Backup::findFirst("status NOT LIKE '1' AND status NOT LIKE '4' AND domain_id = '$domain_id'");
        if($backups) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $domain_users = Users::findFirst("username = '$domain_name'");
        $client_id = $domain_users->id;

        $ftp_repos = FtpRepo::find("client_id = '$client_id'");
        $repo_list = array();

        if($ftp_repos) {
            $repo_list = $ftp_repos->toArray();
        }
        $this->view->setVar("ftp_repos", $repo_list);


        $storages = CloudStorage::find("client_id = '$client_id'");
        $storage_list = array();

        if($storages) {
            $storage_list = $storages->toArray();
        }
        $this->view->setVar("storages", $storage_list);

        $options = array(
            'action'    => 'backup',
        );
        $this->view->form = new BackupForm(null, $options);
        $servers = Servers::findFirst("type = 'main'");
        if(!$servers) {
            $this->view->setVar('error_content',__('Server IP not found'));
            $this->response->setStatusCode(404, __('Server IP not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $server_ip = IpAddress::findFirst("server_id = '$servers->id'");
        if(!$server_ip) {
            $this->view->setVar('error_content',__('Server IP not found'));
            $this->response->setStatusCode(404, __('Server IP not found'));
            $this->view->pick("errors/404");
            return true;
        }
        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('This feature disabled in demo mode!');
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
                if($this->request->isAjax() == true)
                    return $this->response->setJsonContent($messages)->send();
                else
                    return $this->response->redirect($redirect_url,true);
            }

            $post_data = array(
                'domain_name' => $domain_name,
                'backup_name' => $this->request->getPost('backup_name'),
                'description' => $this->request->getPost('description'),
                'backup_repo' => $this->request->getPost('backup_repo'),
                'storages' => $this->request->getPost('storages'),
                'ftp_repo' => $this->request->getPost('repo'),
                'split_size' => $this->request->getPost('split_size'),
                'send_mail' => $this->request->getPost('send_mail',null,OFF),
                'email_address' => $this->request->getPost('email_address'),
                'domain_settings' => $this->request->getPost('domain_settings',null,OFF),
                'www_content' => $this->request->getPost('www_content',null,OFF),
                'database_content' => $this->request->getPost('database_content',null,OFF),
                'dns_content' => $this->request->getPost('dns_content',null,OFF),
                'mail_content' => $this->request->getPost('mail_content',null,OFF),
                'subdomain_content' => $this->request->getPost('subdomain_content',null,OFF),
                "system_type" => $this->user_data["system_data"]["system_type"],
                "server_addr" => $server_ip->ip_address,
            );

            $backup = new \FPanel\Backup\Backup();
            $backup->client_id = $client_id;
            $backup->setPosts($post_data);
            $status = $backup->create(DOMAIN_BACKUP, $domain_id);
            if( !$status) {
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
                    $this->flashSession->notice(__('Backup Creating'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function downloadAction($id)
    {
//        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_name;
            $this->view->setVar('domain_id', $domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        $backup = \FPanel\Models\Backup::findFirstById($id);
        if(!$backup) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }
        else {
            if($backup->domain_id != $domain_id) {
                $this->view->setVar('error_content',__('Backup not found'));
                $this->response->setStatusCode(404, __('Backup not found'));
                $this->view->pick("errors/404");
                return true;
            }
            else {
                $backup_name = $backup->name.'.zip';
                $created_date =  $this->date->getTimeStamp($backup->created_date);
            }
        }

        $backup_location = PANEL_BACKUP_DIR."/domains/$domain_name/$created_date/$backup_name";

        ob_end_clean();
        if (file_exists($backup_location)) {
            $this->view->disable();
            ini_set("zlib.output_compression", 0);
            header("Content-Description: File Transfer");
            header("Content-Type: application/zip");
            header('Content-Disposition: attachment; filename="'.basename($backup_location).'"');
            header("Expires: 0");
//            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//            header("Cache-Control: private",false);
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Pragma: public");
            header("Content-Disposition: attachment; filename=\"".basename($backup_location)."\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($backup_location));
            $chunk_size = 1024 * 1024;
            if ($fd = fopen ($backup_location, "r")) {
                while(!feof($fd)) {
                    $buffer = fread($fd, $chunk_size);
                    echo $buffer;
                    ob_flush();
                    flush();
                }
            }
            fclose($fd);
        }
        else {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }
        exit(0);
//
//        header('Content-Type: application/force-download');
//        header('Content-Length:' . filesize($backup_location));
//        header("Content-Disposition: inline; filename=\"".$backup_file."\"");
//        $filesPointer = fopen($backup_location,"rb");
//        fpassthru($filesPointer);

    }

    public function deleteAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $domain_name = $this->domain_data['name'];
//                $url = $this->uri->url_encrypt('backup/domain/'.$domain_id);
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $domain_name = $data['user']['domain_info']['domain_name'];
//                $url = $this->uri->url_encrypt('backup');
            }
            $post_data = array(
                'type'          => DOMAIN_BACKUP,
                'domain_name'   => $domain_name,
                'id'            => $id,
            );
            $backup = new \FPanel\Backup\Backup();
            $backup->setPosts($post_data);
            $result = $backup->delete(DOMAIN_BACKUP, $id,array('domain' => $domain_id));
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Delete successful');
            if ($result == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Delete failed');
            }
            $this->response->setJsonContent($messages)->send();
        }
    }

    public function cancelAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $domain_name = $this->domain_data['name'];
//                $url = $this->uri->url_encrypt('backup/domain/'.$domain_id);
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $domain_name = $data['user']['domain_info']['domain_name'];
//                $url = $this->uri->url_encrypt('backup');
            }
            $post_data = array(
                'type'          => DOMAIN_BACKUP,
                'domain_name'   => $domain_name,
                'id'            => $id,
            );
            $backup = new \FPanel\Backup\Backup();
            $backup->setPosts($post_data);
            $result = $backup->delete(DOMAIN_BACKUP, $id,array('domain' => $domain_id));

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Backup canceled');
            if ($result == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Backup not found');
            }
            $this->response->setJsonContent($messages)->send();
        }
    }

    public function uploadAction()
    {
        $this->tag->appendTitle(__('Upload Backup'));
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $backup_url = $this->uri->url_encrypt('backup/upload/domain/'.$domain_id, true);
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $backup_url = $this->uri->url_encrypt('backup/upload', true);
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
        }

        $this->breadcrumbs->add(__('Upload Backup'), '', '');

        $this->view->setVar('backup_url', $backup_url);

        if(($this->request->isPost() == true)) {
            $this->view->disable();
            $file_name = $this->request->getPost("flowFilename");
            $ts = $this->request->getPost('ts');


            if(!is_dir(ROOTPATH . "/tmp/$ts")) {
                mkdir(ROOTPATH . "/tmp/$ts");
            }

            $config = new Config();
            $config->setTempDir(ROOTPATH . '/tmp/');
            $request = new Request();
            $basic = new Basic();
            if ($basic->save(ROOTPATH . "/tmp/$ts/$file_name", $config, $request)) {
                if($this->request->getPost("flowTotalChunks") == $this->request->getPost("flowChunkNumber")) {

                    $backup = new \FPanel\Backup\Backup();
                    $backup->setPosts([
                        'domain_id' => $domain_id,
                        'domain_name' => $domain_name,
                        'file_name' => $file_name,
                        'upload_dir' => ROOTPATH . "/tmp/$ts",
                        'ts' => $ts,
                    ]);
                    if(!$backup->uploadDomainBackupToLocal()) {
                        $messages['type'] = 'error';
                        $messages['status'] = false;
                        $message = "";
                        foreach ($backup->getMessages() as $msg) {
                            $message .= $msg . "\n";
                        }
                        $messages['message'] = $message;
                        return $this->response->setJsonContent($messages)->send();
                    }
                }
            }
            else {
                $messages['type'] = 'error';
                $messages['status'] = false;
                $messages['message'] = $basic->errors;
                return $this->response->setJsonContent($messages)->send();
            }
        }
        return true;
    }
}