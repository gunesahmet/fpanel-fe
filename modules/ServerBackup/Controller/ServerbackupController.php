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
 */namespace ServerBackup\Controller;

use FPanel\Backup\Backup;
use FPanel\File\Basic;
use FPanel\File\Config;
use FPanel\File\Request;
use FPanel\Forms\BackupRepoForm;
use FPanel\Forms\BackupStorageForm;
use FPanel\Forms\ServerBackupForm;
use FPanel\Globals\Globals;
use FPanel\Models\Backup as BackupModel;
use FPanel\Models\CloudStorage;
use FPanel\Models\Domains;
use FPanel\Models\ServerFtpRepo;
use FPanel\Models\Servers;
use FPanel\Models\Users;
use FPanel\Process\BackupRestore;
use Main\Controller\ControllerBase;

/**
 * Class ServerbackupController
 * @package ServerBackup\Controller
 */
class ServerbackupController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
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
        $user_data = $this->acl->run('system');

        $this->user_data = $user_data;
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];

        $this->system_data = $user_data['system_data'];

        $this->view->setVar('system_items', $user_data);

        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        if($this->system_data['system_type'] == CLOUD) {
            $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
        }
        else {
            $this->breadcrumbs->add(__('System Dashboard'),'system',' ');
        }
        $params = $this->dispatcher->getParams();

        if (array_key_exists('type', $params)) {
            switch ($params['type']) {
                case "server":
                    $server_id = $params['server_id'];
                    $server = Servers::findFirstById($server_id);
                    if (!$server) {
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
                    $this->response->setStatusCode(404, 'Not Found');
                    $this->view->setVar('error_content',__('Unreconized url'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
    }

    public function indexAction()
    {
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
        }
        else {
            $server_id = "1";
        }
        $this->tag->appendTitle( __('Server Backup Management'));
        $this->breadcrumbs->add(__('Server Backup Management'),'','');


        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listServerBackup($server_id))->send();
        }
    }

    public function createAction()
    {
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
            $post_url = $this->uri->url_encrypt("serverbackup/create/server/$server_id");
            $redirect_url = $this->uri->url_encrypt('serverbackup/server/'.$server_id);
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $post_url = $this->uri->url_encrypt("serverbackup/create");
            $redirect_url = $this->uri->url_encrypt('serverbackup');
        }

        $this->tag->appendTitle( __('Create Backup'));
        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Create Backup'),'','');


        $ftp_repos = ServerFtpRepo::find("status = 'on'");
        $repo_list = array();
        if($ftp_repos) {
            $repo_list = $ftp_repos->toArray();
        }
        $this->view->setVar("ftp_repos", $repo_list);

        $storages = CloudStorage::find("client_id = '1'");
        $storage_list = array();

        if($storages) {
            $storage_list = $storages->toArray();
        }

        $this->view->setVar("storages", $storage_list);

        $user_list = array();
        $list_user = array();
        $users = Users::find("type = '2' OR type = '3'");

        if($users) {
            $user_list = $users->toArray();
        }

        foreach($user_list as $user) {
            switch($user['type']) {
                case SUPPORT_USER:
                case ROOT_USER:
                case ADMIN_USER:
                    $user['type'] = __('Admin');
                    break;
                case RESELLER_USER:
                    $user['type'] = __('Reseller');
                    break;
                case CLIENT_USER:
                    $user['type'] = __('Client');
                    break;
            }
            $list_user[] = $user;
        }
        $domains = Domains::find();

        $domain_list = array();

        if($domains) {
            $domain_list = $domains->toArray();
        }

        $this->view->setVar('domain_list', $domain_list);
        $this->view->setVar('user_list', $list_user);


        $options = array(
            'action' => 'backup',
        );
        $this->view->form = new ServerBackupForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Backup successful');

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('This feature disabled in demo mode!');

                if($this->request->isAjax() == true)
                    return $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
                    return $this->response->redirect($redirect_url,true);
                }
            }

            $post_data = array(
                'backup_name' => $this->request->getPost('backup_name'),
                'description' => $this->request->getPost('description'),
                'backup_repo' => $this->request->getPost('backup_repo'),
                'storages' => $this->request->getPost('storages'),
                'ftp_repo' => $this->request->getPost('repo'),
                'split_size' => $this->request->getPost('split_size'),

                'www_content' => $this->request->getPost('www_content',null,OFF),
                'subdomain_content' => $this->request->getPost('subdomain_content',null,OFF),
                'database_content' => $this->request->getPost('database_content',null,OFF),
                'dns_content' => $this->request->getPost('dns_content',null,OFF),
                'mail_content' => $this->request->getPost('mail_content',null,OFF),
                'send_mail' => $this->request->getPost('send_mail',null,OFF),
                'email_address' => $this->request->getPost('email_address'),
                'domains' => $this->request->getPost('client_list'),
            );

            $backup = new Backup();

            $backup->setPosts($post_data);
            $backup->client_id = "1";
            $status = $backup->create(SERVER_BACKUP,null, $server_id);

            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Backup failed');
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
                    $this->flashSession->success(__('Backup Creating'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function restoreAction($id)
    {
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/server/'.$server_id,' ');
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
            $post_url = $this->uri->url_encrypt("serverbackup/restore/$id/server/$server_id");
            $redirect_url = $this->uri->url_encrypt('serverbackup/server/'.$server_id);
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $post_url = $this->uri->url_encrypt("serverbackup/restore/$id");
            $redirect_url = $this->uri->url_encrypt('serverbackup/'.$id);
        }
        $this->tag->appendTitle( __('Restore Backup'));
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Restore Backup'),'','');


        $this->view->setVar("id", $id);
        $backup_model = BackupModel::findFirst("id = '$id' AND server_id = '$server_id'");

        if($backup_model) {
            $backupmng = new BackupRestore();
            $config_data = $backupmng->getConfig([
                "server-addr" => $this->request->getServerAddress(),
                "name" => $backup_model->name,
                "backup-path" => PANEL_BACKUP_DIR . "/server/". $this->date->getTimeStamp($backup_model->created_date) ."/",
            ]);
            $config = $this->crypto->fdecode($config_data);

            $config = json_decode($config, true);
            if(json_last_error() != JSON_ERROR_NONE) {
                $this->view->setVar('error_content',__('Backup file not found'));
                $this->response->setStatusCode(404, __('Backup file not found'));
                $this->view->pick("errors/404");
                return true;
            }
        }
        else {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $config['backup_data']['sizes'] = $this->env->convert(json_decode($backup_model->sizes, true)["backup_size"]);
        $this->view->setVar("config", $config);

        $options = array(
            'action' => 'restore',
            'config' => $config
        );

        $this->view->form = new ServerBackupForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'info';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Restoring successful');

            $post_data = array(
                'backup_id' => $id,
                'server_id' => $server_id,
                'restore_all' => $this->request->getPost('restore_all'),
                'restore_domain_ftproot' => $this->request->getPost('restore_domain_ftproot'),
                'restore_subdomains' => $this->request->getPost('restore_subdomains'),
                'restore_databases' => $this->request->getPost('restore_databases'),
                'restore_dns' => $this->request->getPost('restore_dns'),
                'restore_mails' => $this->request->getPost('restore_mails'),
                'domain_list' => $this->request->getPost('domain_list'),

                'restore_cronjobs' => $this->request->getPost('restore_cronjobs'),
                'restore_blacklists' => $this->request->getPost('restore_blacklists'),
                'restore_dns_server' => $this->request->getPost('restore_dns_server'),
                'restore_mail_server_settings' => $this->request->getPost('restore_mail_server_settings'),
                'restore_spam_settings' => $this->request->getPost('restore_spam_settings'),
                'restore_spam_list' => $this->request->getPost('restore_spam_list'),
            );
            $restore = new Backup();
            $restore->setPosts($post_data);
            $status = $restore->restore(SERVER_BACKUP);

            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($restore->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->notice((string)__('Restore process started'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }

        return true;

    }

    public function downloadAction($id)
    {
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/server/'.$server_id,' ');
        }

        $this->view->disable();

        $backup = BackupModel::findFirstById($id);
        if(!$backup) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
        }
        else {
            $backup_name = $backup->name.'.zip';
            $created_date =  $this->date->getTimeStamp($backup->created_date);

            $backup_file = $backup_name;

            $backup_location = PANEL_BACKUP_DIR."/server/$created_date/$backup_file";

            if (file_exists($backup_location)) {
                ob_end_clean();
                ini_set('zlib.output_compression', 0);
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="'.basename($backup_location).'"');
                header('Expires: 0');
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header('Pragma: public');
                header("Content-Disposition: attachment; filename=\"".basename($backup_location)."\"");
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: ' . filesize($backup_location));
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
            }
        }
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            if($this->system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
                $this->view->setVar("server_id", $server_id);
                $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
//                $url = $this->uri->url_encrypt('serverbackup/server/'.$server_id);
            }
            else {
                $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
//                $url = $this->uri->url_encrypt('serverbackup');
            }

            $post_data = array(
                'type'          => SERVER_BACKUP,
                'server_id'   => $server_id,
                'id'            => $id,
            );
            $backup = new Backup();
            $backup->setPosts($post_data);
            $result = $backup->delete(SERVER_BACKUP, $id,array('server' => "1"));

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

    public function uploadAction()
    {
        $this->tag->appendTitle(__('Upload Backup'));
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
            $backup_url = $this->uri->url_encrypt("serverbackup/upload/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $backup_url = $this->uri->url_encrypt('serverbackup/upload');
        }

        $this->view->setVar('backup_url', $backup_url);

        $this->breadcrumbs->add(__('Upload Backup'), '', '');


        if(($this->request->isPost() == true)) {
            $this->view->disable();
            $file_name = $this->request->getPost("flowFilename");
            $ts = $this->request->getPost('ts');

            $config = new Config();

            $config->setTempDir(ROOTPATH . '/tmp');
            $request = new Request();
            if(!is_dir(ROOTPATH . "/tmp/$ts")) {
                mkdir(ROOTPATH . "/tmp/$ts");
            }
            $basic = new Basic();
            if ($basic->save(ROOTPATH . "/tmp/$ts/$file_name", $config, $request)) {
                $backup = new Backup();
                $backup->setPosts([
                    'server_id' => $server_id,
                    'file_name' => $file_name,
                    'upload_dir' => ROOTPATH . "/tmp/$ts",
                    'ts' => $ts,
                ]);
                $status = $backup->uploadServerBackupToLocal();
                if(!$status) {
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
            else {
                $messages['type'] = 'error';
                $messages['status'] = false;
                $messages['message'] = $basic->errors;
                return $this->response->setJsonContent($messages)->send();
            }
        }
        return true;
    }

    public function repoAction()
    {
        $this->tag->appendTitle(__('FTP Repo Lists'));

        $list_url = $this->uri->url_encrypt("serverbackup/repo");
        $create_repo_url = $this->uri->url_encrypt("serverbackup/repo/create");
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar('server_id', $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
            $create_repo_url = $this->uri->url_encrypt("serverbackup/repo/create/server/$server_id");
            $list_url = $this->uri->url_encrypt("serverbackup/repo/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
        }

        $this->breadcrumbs->add(__('FTP Repo Lists'), '', '');
        $this->view->setVar("create_repo_url", $create_repo_url);
        $this->view->setVar("list_url", $list_url);

        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listServerRepo())->send();
        }
    }

    public function createRepoAction()
    {
        $this->tag->appendTitle(__('Add New FTP Repo'));
        $redirect_url = $this->uri->url_encrypt("serverbackup/repo");

        $post_url = $this->uri->url_encrypt("serverbackup/repo/create");

        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar('server_id', $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
            $post_url = $this->uri->url_encrypt("serverbackup/repo/create/server/$server_id");
            $redirect_url = $this->uri->url_encrypt("serverbackup/repo/server/$server_id");
            $this->breadcrumbs->add(__('FTP Repo List'), "serverbackup/repo/server/$server_id",' ');
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
            $this->breadcrumbs->add(__('FTP Repo List'), 'serverbackup/repo',' ');
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


            $backup = new Backup();
            $status = $backup->createServerRepo();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
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
        $redirect_url = $this->uri->url_encrypt("serverbackup/repo");

        $post_url = $this->uri->url_encrypt("serverbackup/repo/edit/$id");

        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar('server_id', $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
            $post_url = $this->uri->url_encrypt("serverbackup/repo/edit/$id/server/$server_id");
            $redirect_url = $this->uri->url_encrypt("serverbackup/repo/server/$server_id");
            $this->breadcrumbs->add(__('FTP Repo List'), "serverbackup/repo/server/$server_id",' ');
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
            $this->breadcrumbs->add(__('FTP Repo List'), 'serverbackup/repo',' ');
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit FTP Repo'), '', '');


        $ftp_repo = ServerFtpRepo::findFirstById($id);
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


            $backup = new Backup();
            $status = $backup->editServerRepo($id);
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
            $backup = new Backup();
            $result = $backup->deleteServerRepo($id);

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

    public function storageAction()
    {
        $this->tag->appendTitle(__('Cloud Storage Lists'));
        $create_storage_url = $this->uri->url_encrypt("serverbackup/storage/create");
        $list_url = $this->uri->url_encrypt("serverbackup/storage");
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar('server_id', $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup/server/'.$server_id,' ');
            $create_storage_url = $this->uri->url_encrypt("serverbackup/storage/create/server/$server_id");
            $list_url = $this->uri->url_encrypt("serverbackup/storage/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
        }
        $this->view->setVar("create_storage_url", $create_storage_url);
        $this->view->setVar("list_url", $list_url);

        $this->breadcrumbs->add(__('Cloud Storage Lists'), '', '');

        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listServerStorages())->send();
        }
    }

    public function addStorageAction()
    {
        $this->view->pick('/storage/create');
        $this->tag->appendTitle(__('Add New Storage'));

        $redirect_url = $this->uri->url_encrypt("serverbackup/storage");
        $post_url = $this->uri->url_encrypt("serverbackup/storage/create");
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar('server_id', $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),"serverbackup/server/$server_id",' ');
            $this->breadcrumbs->add(__('Cloud Storage List'),"serverbackup/storage/server/$server_id",' ');
            $redirect_url = $this->uri->url_encrypt("serverbackup/storage/server/$server_id");
            $post_url = $this->uri->url_encrypt("serverbackup/storage/create/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
            $this->breadcrumbs->add(__('Storage List'), 'serverbackup/storage', ' ');
        }

        $this->view->setVar('post_url', $post_url);

        $this->view->pick("serverbackup/add_storage");
        $this->breadcrumbs->add(__('Add New Storage'), '', '');


        $this->view->form = new BackupStorageForm();
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');


            $backup = new Backup();
            $post = array(
                'type' => "dropbox",
                'name' => $this->request->getPost('storage_name'),
                'access_token' => $this->request->getPost('access_token'),
                'g_secret' => $this->request->getPost('secret_key'),
                'google_client_id' => $this->request->getPost('gclient_id'),
                'folder' => $this->request->getPost('folder'),
                'status' => $this->request->getPost('status',null,"off"),
            );
            $backup->setPosts($post);

            $status = $backup->createStorage("1");
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];
                foreach ($backup->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }

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
        $this->view->pick('/storage/create');
        $this->tag->appendTitle(__('Add New Storage'));

        $redirect_url = $this->uri->url_encrypt("serverbackup/storage");
        $post_url = $this->uri->url_encrypt("serverbackup/storage/edit/$id");
        if($this->system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar('server_id', $server_id);
            $this->breadcrumbs->add(__('Server Backup Management'),"serverbackup/server/$server_id",' ');
            $this->breadcrumbs->add(__('Cloud Storage List'),"serverbackup/storage/server/$server_id",' ');
            $redirect_url = $this->uri->url_encrypt("serverbackup/storage/server/$server_id");
            $post_url = $this->uri->url_encrypt("serverbackup/storage/edit/$id/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Server Backup Management'),'serverbackup',' ');
            $this->breadcrumbs->add(__('Storage List'), 'serverbackup/storage', ' ');
        }

        $this->view->setVar('post_url', $post_url);

        $this->view->pick("serverbackup/edit_storage");
        $this->breadcrumbs->add(__('Edit Cloud Storage'), '', '');


        $storage = CloudStorage::findFirst("id = '$id'");
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


            $backup = new Backup();
            $post = array(
                'type' => "dropbox",
                'name' => $this->request->getPost('storage_name'),
                'access_token' => $this->request->getPost('access_token'),
                'g_secret' => $this->request->getPost('secret_key'),
                'google_client_id' => $this->request->getPost('gclient_id'),
                'folder' => $this->request->getPost('folder'),
                'status' => $this->request->getPost('status',null,"off"),
            );

            $backup->setPosts($post);
            $status = $backup->editStorage($id,"1");
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
            $backup = new Backup();
            $result = $backup->deleteStorage($id,"1");
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
}
