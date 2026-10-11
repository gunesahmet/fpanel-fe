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
 */namespace ClientBackup\Controller;

use FPanel\Backup\Backup;
use FPanel\Backup\Backup as BackupLib;
use FPanel\File\Basic;
use FPanel\File\Config;
use FPanel\File\Request;
use FPanel\Forms\BackupRepoForm;
use FPanel\Forms\BackupStorageForm;
use FPanel\Forms\ClientBackupForm;
use FPanel\Globals\Globals;
use FPanel\Models\Backup as BackupModel;
use FPanel\Models\CloudStorage;
use FPanel\Models\FtpRepo;
use FPanel\Models\Users;
use FPanel\Process\BackupRestore;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class ClientbackupController
 * @package ClientBackup\Controller
 */
class ClientbackupController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $client_id;
    protected $client_type;
    protected $client_name;
    protected $user_id;
    protected $user_name;
    protected $user_type;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        if($this->dispatcher->getActionName() != "download") {
            $this->user_data = $this->acl->run('backup');
        }
        $user_data = $this->user_data;

        $this->view->setVar("system_items", $user_data);
        $this->view->setVar("username", $user_data['user']['info']['username']);

        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_name = $user_data['user']['info']['username'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->view->setVar("user_type", $this->user_type);

        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');

        $params = $this->dispatcher->getParams();
        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case 'client':

                    $client_id = $params['client_id'];
                    $user = Users::findFirstById($client_id);
                    if(!$user) {
                        $this->view->setVar('error_content',__('User not found'));
                        $this->response->setStatusCode(404, __('User not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {

                        $client_data = $user->toArray();
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($client_id, $client_data['type']);
                        $this->client_id = $client_id;
                        $this->client_name = $user->username;

                        $this->client_type = $client_data['type'];
                        $this->view->setVar('client_id', $this->client_id);
                        $this->view->setVar("client_type", $this->client_type);

                        $this->breadcrumbs->add(__('User List'),'users',' ');
                        $this->breadcrumbs->add($client_data['username'],'dashboard/client/'.$this->client_id,' ');
                        $client_menu = $this->sidebar->clientMenu($this->client_type, $this->client_id);
                        $this->view->setVar('client_menu', $client_menu);
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
    }

    public function indexAction()
    {
        $user_id = $this->user_id;
        $this->tag->appendTitle(__('Backup List'));

        $nested = false;
        if(isset($this->client_id)) {
            $this->view->setVar('client_id', $this->client_id);
            $nested = true;
            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
        }
        else {
            $client_id = $user_id;
        }

        $backup_status = false;
        $backups = BackupModel::findFirst("status NOT LIKE '1' AND status NOT LIKE '4' AND client_id = '$client_id'");
        if($backups) {
            $backup_status = true;
        }
        $this->view->setVar("backup_status", $backup_status);

        $this->breadcrumbs->add(__('Backup List'),'','');


        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listClientBackup($client_id, $nested))->send();
        }
    }

    /**
     * @return bool|ResponseInterface
     */
    public function createAction()
    {
        $this->tag->appendTitle(__('Create Backup'));
        $data = $this->user_data;
        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
            $this->view->setVar('client_type', $this->client_type);
            $redirect_url = $this->uri->url_encrypt('backupclient/client/'.$client_id);
            $post_url = $this->uri->url_encrypt('backupclient/create/client/'.$client_id);
            $client_name = $this->client_name;
            $this->breadcrumbs->add(__('Backup List'),'backupclient/client/'.$this->client_id,' ');
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $client_type = $data['user']['info']['user_type'];
            $this->view->setVar('client_type', $client_type);
            $redirect_url = $this->uri->url_encrypt('backupclient');
            $client_name = $data['user']['info']['username'];
            $post_url = $this->uri->url_encrypt('backupclient/create');
            $this->breadcrumbs->add(__('Backup List'),'backupclient',' ');
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Create Backup'), '', '');

        $backups = BackupModel::findFirst("status NOT LIKE '1' AND status NOT LIKE '4' AND client_id = '$client_id'");
        if($backups) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }


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
            'client_id' => $client_id,
            'client_username' => $client_name
        );
        $this->view->form = new ClientBackupForm(null, $options);


        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Rule updated');

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

            $backup = new Backup();
            $backup->setPost("client_name", $client_name);
            $backup->client_id = $client_id;

            $status = $backup->create(CLIENT_BACKUP,null,null, $client_id);
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to add');
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

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function restoreAction($id)
    {
        $this->tag->appendTitle(__('Restore Backup'));
        $data = $this->user_data;
        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
            $this->view->setVar('client_type', $this->client_type);
            $client_name = $this->client_name;
            $this->breadcrumbs->add(__('Backup List'),'backupclient/client/'.$client_id,' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/client/'.$client_id);
            $post_url = $this->uri->url_encrypt('backupclient/restore/'. $id.'/client/'.$client_id);
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $client_type = $data['user']['info']['user_type'];
            $this->view->setVar('client_type', $client_type);
            $client_name = $data['user']['info']['username'];
            $this->breadcrumbs->add(__('Backup List'),'backupclient',' ');
            $redirect_url = $this->uri->url_encrypt('backupclient');
            $post_url = $this->uri->url_encrypt('backupclient/restore/'.$id);
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Restore Backup'), '', '');

        $backup_model = BackupModel::findFirstById($id);

        if(!$backup_model) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $backupmng = new BackupRestore();

        $config_data = $backupmng->getConfig([
            "server-addr" => $this->request->getServerAddress(),
            "name" => $backup_model->name,
            "backup-path" => PANEL_BACKUP_DIR . "/clients/$client_name/". $this->date->getTimeStamp($backup_model->created_date) ."/",
        ]);
        $config = $this->crypto->fdecode($config_data);

        $config = json_decode($config, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->view->setVar('error_content',__('Backup file not found'));
            $this->response->setStatusCode(404, __('Backup file not found'));
            $this->view->pick("errors/404");
            return true;
        }


        $backup_data = $backup_model->toArray();
        $backup_content['content'] = json_decode($backup_data['content'],true);
        $backup_content['size'] = json_decode($backup_data['sizes'],true);
        $backup_content['subdomains'] = $config['backup_data']['content']['subdomain_content'];
        $backup_content['mail_accounts'] = $config['backup_data']['content']['mail_content'];
        $backup_content['databases'] = $config['backup_data']['content']['database_content'];
        $this->view->setVar('backup_content', $backup_content);
        $this->view->setVar("config", $config);

        $options = array(
            'action'    => 'restore',
            'backup_content' => $backup_content,
            'config' => $config,
            'client_name' => $client_name
        );
        $this->view->form = new ClientBackupForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();


            $messages = Globals::messages();
            $messages['type'] = 'info';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Restore started');

            $restore = new Backup();
            $restore->setPost("id", $id);
            $restore->setPost("client_name", $client_name);
            $restore->setPost("client_id", $client_id);
            $status = $restore->restore(CLIENT_BACKUP);

            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Restore failed');
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
                if ($status) {
                    $this->flashSession->notice((string)__('Restore process started'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }

    }

    /**
     * @param $backup_id
     */
    public function statusAction($backup_id)
    {
        $this->tag->appendTitle( __('Restore Status'));

        $data = $this->user_data;
        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
            $this->view->setVar('client_type', $this->client_type);
            $client_name = $this->client_name;
            $this->breadcrumbs->add(__('Backup List'),'backupclient/client/'.$client_id,' ');
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $client_type = $data['user']['info']['user_type'];
            $this->view->setVar('client_type', $client_type);
            $client_name = $data['user']['info']['username'];
            $this->breadcrumbs->add(__('Backup List'),'backupclient',' ');
        }

        $this->breadcrumbs->add(__('Restore Status'),'','');


        $this->view->setVar("restore_id", $backup_id);
        $this->view->setVar("title_backup",__('Restore Status'));

        $backup_model = BackupModel::findFirst("id = '$backup_id' AND client_id = '$client_id'");

        if(!$backup_model) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
        }
        else {
            $backup_data = $backup_model->toArray();
            $status = $this->env->readConfig(PANEL_BACKUP_DIR . "/clients/$client_name/" . $backup_data['created_date'] . '/status.config');
            $this->view->setVar("configs", $status);
        }
    }

    /**
     * @param $backup_id
     * @return bool|ResponseInterface
     */
    public function progressAction($backup_id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->client_id)) {
                $client_id = $this->client_id;
                $this->view->setVar('client_id', $client_id);
                $this->view->setVar('client_type', $this->client_type);
                $client_name = $this->client_name;
                $this->breadcrumbs->add(__('Backup List'),'backupclient/client/'.$client_id,' ');
            }
            else {
                $client_id = $data['user']['info']['user_id'];
                $client_type = $data['user']['info']['user_type'];
                $this->view->setVar('client_type', $client_type);
                $client_name = $data['user']['info']['username'];
                $this->breadcrumbs->add(__('Backup List'),'backupclient',' ');
            }

            $backup_model = BackupModel::findFirst("id = '$backup_id' AND client_id = '$client_id'");

            if(!$backup_model) {
                if($this->request->isAjax())
                    return $this->response->setJsonContent(array('error' => __('Backup not found')))->send();
            }
            else {
                $progress_config = PANEL_BACKUP_DIR . "/clients/$client_name/" . $this->date->getTimeStamp($backup_model->created_date) . '/progress.config';
                if(file_exists($progress_config)) {
                    $progress = $this->env->readConfig($progress_config);
                    if(empty($progress['domainlist']))
                        $progress['domainlist'] = "undefined";

                    return $this->response->setJsonContent($progress)->send();
                }
                else {
                    return $this->response->setJsonContent(array('error' => __('Restore process not found')))->send();
                }
            }
        }
        else {
            return $this->response->redirect($this->uri->url_encrypt("dashboard"));
        }
        return true;
    }

    /**
     * @param $id
     * @return bool|void
     */
    public function downloadAction($id)
    {
        $data = $this->user_data;
        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
            $this->view->setVar('client_type', $this->client_type);
//            $redirect_url = $this->uri->url_encrypt('backupclient/client/'.$client_id);
            $client_name = $this->client_name;
            $this->breadcrumbs->add(__('Backup List'),'backupclient/client/'.$this->client_id,' ');
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $client_type = $data['user']['info']['user_type'];
            $this->view->setVar('client_type', $client_type);
//            $redirect_url = $this->uri->url_encrypt('backupclient');
            $client_name = $data['user']['info']['username'];
            $this->breadcrumbs->add(__('Backup List'),'backupclient',' ');
        }
        $this->view->disable();

        $backup = BackupModel::findFirstById($id);
        if(!$backup) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }
        else {
            if($backup->client_id != $client_id) {
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
        $backup_file = $backup_name;

        $backup_location = PANEL_BACKUP_DIR."/clients/$client_name/$created_date/$backup_file";

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
            return true;
        }
    }

    /**
     * @param $id
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->client_id)) {
                $client_id = $this->client_id;
                $this->view->setVar('client_id', $client_id);
                $this->view->setVar('client_type', $this->client_type);
//                $redirect_url = $this->uri->url_encrypt('backupclient/client/'.$client_id);
                $client_name = $this->client_name;
            }
            else {
                $client_id = $data['user']['info']['user_id'];
                $client_type = $data['user']['info']['user_type'];
                $this->view->setVar('client_type', $client_type);
//                $redirect_url = $this->uri->url_encrypt('backupclient');
                $client_name = $data['user']['info']['username'];

            }

            $post_data = array(
                'type'          => CLIENT_BACKUP,
                'client_id'     => $client_id,
                'id'            => $id,
                'client_name'   => $client_name,
            );
            $backup = new BackupLib();
            $backup->setPosts($post_data);
            $result = $backup->delete(CLIENT_BACKUP, $id, array('client' => "$client_id"));
            $messages = Globals::messages();

            if ($result == true) {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Delete successful');
            }
            else {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Delete failed');
            }
            $this->response->setJsonContent($messages)->send();
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function uploadAction()
    {
        $this->tag->appendTitle(__('Upload Backup'));
        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $client_name = $this->client_name;
            $this->breadcrumbs->add(__('Client Backup Management'),'backupclient/client/'.$client_id,' ');
            $backup_url = $this->uri->url_encrypt('backupclient/upload/client/'.$client_id);
        }
        else {
            $this->breadcrumbs->add(__('Client Backup Management'),'backupclient',' ');
            $client_id = $this->user_id;
            $client_name = $this->user_name;
            $backup_url = $this->uri->url_encrypt('backupclient/upload');
        }
        $this->breadcrumbs->add(__('Upload Backup'), '', '');

        $this->view->setVar("backup_url", $backup_url);

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
                    'client_id' => $client_id,
                    'client_name' => $client_name,
                    'file_name' => $file_name,
                    'upload_dir' => ROOTPATH . "/tmp/$ts",
                    'ts' => $ts,
                ]);
                $status = $backup->uploadClientBackupToLocal();
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
    }

    /**
     * repo
     */
    public function repoAction()
    {
        $this->tag->appendTitle(__('Backup Repo Lists'));
        $data = $this->user_data;

        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->view->setVar('domain_id', $client_id);
            $this->breadcrumbs->add(__('Backup List'), 'backupclient/client/'.$client_id, ' ');
            $nested = true;
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $this->breadcrumbs->add(__('Backup List'), 'backupclient', ' ');
            $nested = false;
        }

        $this->breadcrumbs->add(__('Backup Repo Lists'), '', '');



        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listClientRepo($client_id, $nested))->send();
        }
    }

    /**
     * createRepo
     */
    public function createRepoAction()
    {
        $this->tag->appendTitle(__('Add New FTP Repo'));
        $data = $this->user_data;

        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->breadcrumbs->add(__('Backup List'), 'backupclient/client/'.$client_id, ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backupclient/repo/client/'.$client_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/repo/client/'.$client_id);
            $post_url = $this->uri->url_encrypt('backupclient/repo/create/client/'.$client_id);
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $this->breadcrumbs->add(__('Backup List'), 'backupclient', ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backupclient/repo', ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/repo');
            $post_url = $this->uri->url_encrypt('backupclient/repo/create');
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Add FTP Repo'), '', '');

        $this->view->setVar('client_id', $client_id);

        $this->view->form = new BackupRepoForm();
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Create successful');

            $backup = new Backup();
            $backup->setPost("client_id", $client_id);

            $status = $backup->createClientRepo();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Repo adding failed');
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

    /**
     * @param $id
     * @return bool|ResponseInterface
     */
    public function editRepoAction($id)
    {
        $this->tag->appendTitle(__('Edit FTP Repo'));
        $data = $this->user_data;

        $this->view->pick('clientbackup/editrepo');
        if(isset($this->client_id)) {

            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
            $this->breadcrumbs->add(__('Backup List'), 'backupclient/client/'.$client_id, ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backupclient/repo/client/'.$client_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/repo/client/'.$client_id);
            $post_url = $this->uri->url_encrypt("backupclient/repo/edit/$id/client/$client_id");
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $this->breadcrumbs->add(__('Backup List'), 'backupclient', ' ');
            $this->breadcrumbs->add(__('Repo List'), 'backupclient/repo', ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/repo');
            $post_url = $this->uri->url_encrypt("backupclient/repo/edit/$id");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit FTP Repo'), '', '');


        $ftp_repo = FtpRepo::findFirst("client_id = '$client_id' AND id = '$id'");
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
            $backup->setPost("client_id", $client_id);
            $status = $backup->editClientRepo($id);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to add');
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

    /**
     * @param $id
     */
    public function deleteRepoAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->client_id)) {
                $client_id = $this->client_id;
//                $url = $this->uri->url_encrypt('backupclient/client/'.$client_id);
            }
            else {
                $client_id = $data['user']['info']['user_id'];
//                $url = $this->uri->url_encrypt('backupclient');
            }
            $backup = new Backup();
            $result = $backup->deleteClientRepo($client_id, $id);
            $messages = Globals::messages();

            if ($result == true) {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Delete successful');
            }
            else {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Delete failed');
            }
            $this->response->setJsonContent($messages)->send();

        }
    }

    /**
     *
     */
    public function storageAction()
    {
        $this->tag->appendTitle(__('Backup Storage Lists'));
        $data = $this->user_data;
        $this->view->pick('/clientbackup/storage');
        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
            $this->breadcrumbs->add(__('Backup List'), 'backupclient/client/'.$client_id, ' ');
            $links = '<a class="btn btn-primary" href="'.$this->uri->url_encrypt("backupclient/storage/create/client/$client_id") .'" ><i class="fa fa-plus-circle"></i> '. __('Add New Storage') .'</a>';
            $nested = true;
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $this->breadcrumbs->add(__('Backup List'), 'backupclient', ' ');
            $links = '<a class="btn btn-primary" href="'.$this->uri->url_encrypt("backupclient/storage/create") .'" ><i class="fa fa-plus-circle"></i> '. __('Add New Storage') .'</a>';
            $nested = false;
        }
        $this->view->setVar('links', $links);

        $this->breadcrumbs->add(__('Backup Storage Lists'), '', '');



        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $backup = new Backup();
            $this->response->setJsonContent($backup->listClientStorages($client_id, $nested))->send();
        }
    }

    /**
     * @return bool|ResponseInterface
     */
    public function addStorageAction()
    {
        $this->view->pick('/clientbackup/createstorage');
        $this->tag->appendTitle(__('Add New Storage'));
        $data = $this->user_data;

        if(isset($this->client_id)) {
            $client_id = $this->client_id;
            $this->breadcrumbs->add(__('Backup List'), 'backupclient/client/'.$client_id, ' ');
            $this->breadcrumbs->add(__('Storage List'), 'backupclient/storage/client/'.$client_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/storage/client/'.$client_id);
            $post_url = $this->uri->url_encrypt('backupclient/storage/create/client/'.$client_id);
            $links = '<a class="btn btn-danger" href="'.$this->uri->url_encrypt('backupclient/storage/client/'.$client_id)  .'" type="button"><i class="fa fa-backward"></i> '. __('Go Back') .'</a>';
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $this->breadcrumbs->add(__('Backup List'), 'backupclient', ' ');
            $this->breadcrumbs->add(__('Storage List'), 'backupclient/storage', ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/storage');
            $post_url = $this->uri->url_encrypt('backupclient/storage/create');
            $links = '<a class="btn btn-danger" href="'.$this->uri->url_encrypt('backupclient/storage')  .'" type="button"><i class="fa fa-backward"></i> '. __('Go Back') .'</a>';
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Add New Storage'), '', '');

        $this->view->setVar('links', $links);
        $this->view->setVar('client_id', $client_id);

        $this->view->form = new BackupStorageForm();
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding sucessful');


            $backup = new Backup();
            $backup->setPost("type", "dropbox");

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

    /**
     * @param $id
     * @return bool|ResponseInterface
     */
    public function editStorageAction($id)
    {
        $this->view->pick('/clientbackup/editstorage');
        $this->tag->appendTitle(__('Edit Storage'));
        $data = $this->user_data;

        if(isset($this->client_id)) {

            $client_id = $this->client_id;
            $this->view->setVar('client_id', $client_id);
            $this->breadcrumbs->add(__('Backup List'), 'backupclient/client/'.$client_id, ' ');
            $this->breadcrumbs->add(__('Storage List'), 'backupclient/storage/client/'.$client_id, ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/storage/client/'.$client_id);
            $post_url = $this->uri->url_encrypt("backupclient/storage/edit/$id/client/$client_id");
            $links = '<a class="btn btn-danger" href="'.$this->uri->url_encrypt('backupclient/storage/client/'.$client_id)  .'" type="button"><i class="fa fa-backward"></i> '. __('Go Back') .'</a>';
        }
        else {
            $client_id = $data['user']['info']['user_id'];
            $this->breadcrumbs->add(__('Backup List'), 'backupclient', ' ');
            $this->breadcrumbs->add(__('Storage List'), 'backupclient/storage', ' ');
            $redirect_url = $this->uri->url_encrypt('backupclient/storage');
            $post_url = $this->uri->url_encrypt("backupclient/storage/edit/$id");
            $links = '<a class="btn btn-danger" href="'.$this->uri->url_encrypt('backupclient/storage')  .'" type="button"><i class="fa fa-backward"></i> '. __('Go Back') .'</a>';
        }
        $this->view->setVar('links', $links);
        $this->view->setVar('post_url', $post_url);

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
            $messages['messages'] = __('Update sucessful');


            $backup = new Backup();
            $backup->setPost("type", "dropbox");
            $status = $backup->editStorage($id, $client_id);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to update');
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

    /**
     * @param $id
     */
    public function deleteStorageAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost() == true) || ($this->request->isAjax() == true)) {
            $data = $this->user_data;
            if(isset($this->client_id)) {
                $client_id = $this->client_id;
//                $url = $this->uri->url_encrypt('backupclient/client/'.$client_id);
            }
            else {
                $client_id = $data['user']['info']['user_id'];
//                $url = $this->uri->url_encrypt('backupclient');
            }
            $backup = new Backup();
            $result = $backup->deleteStorage($id, $client_id);

            $messages = Globals::messages();
            if ($result == true) {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Delete successful');
            }
            else {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Delete failed');
            }
            $this->response->setJsonContent($messages)->send();

        }
    }

}
