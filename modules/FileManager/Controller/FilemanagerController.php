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
 */namespace FileManager\Controller;

use FPanel\Domain\Services;
use FPanel\Ftp\Ftp;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use FPanel\Models\Servers;
use Main\Controller\ControllerBase;

/**
 * Class FilemanagerController
 * @package FileManager\Controller
 */
class FilemanagerController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    private $server_os;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('webhosting');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        $params = $this->dispatcher->getParams();
        $this->server_os = PANEL_OS_TYPE;
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
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($domain_id,DOMAIN_USER);
                        $domain_data = $domain->toArray();
                        $this->domain_id = $domain_id;
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$domain_id,' ');
                        $this->domain_data = $domain_data;
                        $this->view->setVar('client_type', DOMAIN_USER);
                        $client_menu = $this->sidebar->clientMenu(DOMAIN_USER, $domain_id);
                        $this->view->setVar("client_menu", $client_menu);
                        $servers = Servers::findFirstById($domain_data["web_server_id"]);
                        $this->server_os = $this->env->getOs($servers->os);
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

        $this->view->setVar("server_type", $this->server_os);
        $domain_services = new Services();
        $domain_services->checkDomainService($domain_id,"web", $redirect_url);
        return true;
    }

    public function indexAction()
    {
        $this->view->setVar("default_folder", $this->crypto->encryptSession('/'));
        $this->tag->appendTitle(__('File Manager'));
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $rename_url = $this->uri->url_encrypt("filemanager/rename/domain/$domain_id", true);
            $url_files = $this->uri->url_encrypt('filemanager/getfiles/domain/'.$domain_id, true);
            $move_url = $this->uri->url_encrypt('filemanager/move/domain/'.$domain_id, true);
            $paste_url = $this->uri->url_encrypt('filemanager/paste/domain/'.$domain_id, true);
            $delete_url = $this->uri->url_encrypt('filemanager/delete/domain/'.$domain_id, true);
            $create_folder_url = $this->uri->url_encrypt('filemanager/createfolder/domain/'.$domain_id, true);
            $permissions_url = $this->uri->url_encrypt('filemanager/permission/domain/'.$domain_id, true);
            $get_permissions_url = $this->uri->url_encrypt('filemanager/getpermission/domain/'.$domain_id, true);
            $create_file_url = $this->uri->url_encrypt('filemanager/createfile/domain/'.$domain_id, true);
            $edit_file_url = $this->uri->url_encrypt('filemanager/editfile/domain/'.$domain_id, true);
            $save_file_url = $this->uri->url_encrypt('filemanager/savefile/domain/'.$domain_id, true);
            $upload_url = $this->uri->url_encrypt('filemanager/upload/domain/'.$domain_id, true);
            $download_url = $this->uri->url_encrypt('filemanager/download/domain/'.$domain_id, true);
            $index_url = $this->uri->url_encrypt('filemanager/domain/'.$domain_id, true);
            $extract_url = $this->uri->url_encrypt("filemanager/extract/domain/$domain_id", true);
            $cancel_url = $this->uri->url_encrypt("filemanager/cancel/domain/$domain_id", true);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $rename_url = $this->uri->url_encrypt("filemanager/rename", true);
            $url_files = $this->uri->url_encrypt('filemanager/getfiles', true);
            $move_url = $this->uri->url_encrypt('filemanager/move', true);
            $paste_url = $this->uri->url_encrypt('filemanager/paste', true);
            $delete_url = $this->uri->url_encrypt('filemanager/delete', true);
            $create_folder_url = $this->uri->url_encrypt('filemanager/createfolder', true);
            $permissions_url = $this->uri->url_encrypt('filemanager/permission', true);
            $get_permissions_url = $this->uri->url_encrypt('filemanager/getpermission', true);
            $create_file_url = $this->uri->url_encrypt('filemanager/createfile', true);
            $edit_file_url = $this->uri->url_encrypt('filemanager/editfile', true);
            $save_file_url = $this->uri->url_encrypt('filemanager/savefile', true);
            $upload_url = $this->uri->url_encrypt('filemanager/upload', true);
            $download_url = $this->uri->url_encrypt('filemanager/download', true);
            $index_url = $this->uri->url_encrypt('filemanager', true);
            $extract_url = $this->uri->url_encrypt("filemanager/extract", true);
            $cancel_url = $this->uri->url_encrypt("filemanager/cancel", true);
        }

        $this->view->setVar('index_url', $index_url);
        $this->view->setVar('move_url', $move_url);
        $this->view->setVar('rename_url', $rename_url);
        $this->view->setVar('url_files', $url_files);
        $this->view->setVar('paste_url', $paste_url);
        $this->view->setVar('delete_url', $delete_url);
        $this->view->setVar('create_folder_url', $create_folder_url);
        $this->view->setVar('permissions_url', $permissions_url);
        $this->view->setVar('get_permissions_url', $get_permissions_url);
        $this->view->setVar('create_file_url', $create_file_url);
        $this->view->setVar('edit_file_url', $edit_file_url);
        $this->view->setVar('save_file_url', $save_file_url);
        $this->view->setVar('upload_url', $upload_url);
        $this->view->setVar('download_url', $download_url);
        $this->view->setVar('extract_url', $extract_url);
        $this->view->setVar('cancel_url', $cancel_url);
        $this->breadcrumbs->add(__('File Manager'),'','');

        $file_manager = new Ftp();
        if($this->request->isPost() && $this->request->isAjax()) {
            $this->view->disable();
            $file_manager->setOs($this->server_os);
            return $this->response->setJsonContent($file_manager->getFolders($domain_id, $domain_name))->send();
        }
        else {
            $file_manager->setOs($this->server_os);
            $folder_list = $file_manager->getFolders($domain_id, $domain_name);
            $file_list = $file_manager->getFiles($this->crypto->encryptSession("/"), $domain_name);
//            $this->debug->debug([
//                $file_list,
//            ], false);
            $this->view->setVar("folder_list", $folder_list);
            $this->view->setVar("file_list", $file_list);
        }

        return true;
    }

    public function getfilesAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
            $nested = true;
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $nested = false;
        }

        if(($this->request->isAjax()) && ($this->request->isPost())) {
            $file_manager = new Ftp();
            $file_manager->setOs($this->server_os);
            $file_manager->nested = $nested;
            $folder = "";
            if($this->request->getPost('folder') != null) {
                $folder = $this->request->getPost("folder");
            }
            $result = $file_manager->getFiles($folder, $domain_name);

            $this->response->setJsonContent($result)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
    }

    public function createFolderAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
            $domain_id = $this->domain_data['id'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $data['user']['domain_info']['domain_id'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Folder created');

            $name = $this->request->getPost("name");
            $current_folder = $this->request->getPost("current_folder");
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            if(!$ftp->createFileOrFolder($name, $domain_name, "folder", $current_folder, Domains::findFirstById($domain_id))) {

                $messages['type'] = 'error';
                $messages['messages'] = __('Failed');
                $messages['title'] = __('Folder create failed');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $message) {
                    $messages['messages'] = $message;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function renameAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
            $domain_id = $this->domain_data['id'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $data['user']['domain_info']['domain_id'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $name = $this->request->getPost("name");
            $new_name = $this->request->getPost("new_name");
            $type = $this->request->getPost("type");
            $ftp = new Ftp();
            $domains = Domains::findFirstById($domain_id);
            $ftp->setOs($this->server_os);
            if(!$ftp->renameFileOrFolder($name, $new_name, $domain_name, $type, $domains)) {

                $messages['type'] = 'error';
                $messages['messages'] = __('Failed');
                $messages['title'] = __('Rename failed');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $message) {
                    $messages['messages'] = $message;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function deleteAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
            $domain_id = $this->domain_data['id'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $data['user']['domain_info']['domain_id'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Successful');
            $messages['messages'] = __('File deleted');

            $selecteds = $this->request->getPost("selecteds");
            $domains = Domains::findFirstById($domain_id);
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            if(!$ftp->deleteFileOrFolder($selecteds, $domain_name, $domains)) {

                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $message) {
                    $messages['messages'] = $message;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function moveAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
            $domain_id = $this->domain_data['id'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $data['user']['domain_info']['domain_id'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Moving successful');

            $source = $this->request->getPost("source");
            $target = $this->request->getPost("target");
            $domains = Domains::findFirstById($domain_id);
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            if(!$ftp->moveFileOrFolder($source, $target, $domain_name, $domains)) {
                $messages['type'] = 'error';
                $messages['messages'] = __('Failed');
                $messages['title'] = __('Move failed');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $message) {
                    $messages['messages'] = $message;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function pasteAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $source = $this->request->getPost("source");
            $target = $this->request->getPost("target");
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);

            if(!$ftp->pasteFileOrFolder($source, $target, $domain_name)) {

                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function permissionAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }

        if($this->request->isAjax() && $this->request->isPost()) {
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            if($this->server_os == LINUX) {
                $file_name = $this->request->getPost("name");
                $permission = $this->request->getPost('permission');
                if(!$ftp->permissionFileOrFolderLinux($file_name, $domain_name, $permission)) {


                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                    $messages['errors'] = [];

                    foreach($ftp->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                }

            }
            else {
                $file_name = $this->request->getPost("name");
                $permission = $this->request->getPost('permissions');
                if(!$ftp->permissionFileOrFolderWindows($file_name, $domain_name, $permission)) {

                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                    $messages['errors'] = [];

                    foreach($ftp->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                }

            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function getPermissionAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $name = $this->request->getPost("name");
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            $messages = $ftp->getFilePermission($name, $domain_name);
            $this->response->setJsonContent($messages)->send();
        }
    }

    public function createFileAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
            $domain_id = $this->domain_id;
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
            $domain_id = $data['user']['domain_info']['domain_id'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('File Create successful');

            $name = $this->request->getPost("name");
            $current_folder = $this->request->getPost("current_folder");
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            if(!$ftp->createFileOrFolder($name, $domain_name, "file", $current_folder, Domains::findFirstById($domain_id))) {
                $messages['type'] = 'error';
                $messages['messages'] = __('Failed');
                $messages['title'] = __('File create failed');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $message) {
                    $messages['messages'] = $message;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function editFileAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $name = $this->request->getPost("file");
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            if(!$ftp->editFile($name, $domain_name)) {
                $messages['type'] = 'error';
                $messages['messages'] = __('Failed');
                $messages['title'] = __('File content can\'t get');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $message) {
                    $messages['messages'] = $message;
                }
            }
            else {
                $messages['content'] = $ftp->getResult();
            }

            $this->response->setJsonContent($messages, JSON_INVALID_UTF8_IGNORE)->send();
        }
    }

    public function saveFileAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        if($this->request->isAjax() && $this->request->isPost()) {
            $name = $this->request->getPost("name");
            $content = $this->request->getPost("content");
            $ftp = new Ftp();
            $ftp->setOs($this->server_os);
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('File saved');

            if(!$ftp->saveFile($name, $domain_name, $content)) {
                $messages['type'] = 'error';
                $messages['messages'] = __('Failed');
                $messages['title'] = __('File content can\'t save');
                $messages['errors'] = [];

                foreach($ftp->getMessages() as $message) {
                    $messages['messages'] = $message;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function uploadAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }

        $ftp = new Ftp();
        $ftp->setOs($this->server_os);
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('File uploaded');
        $file_name = $this->request->getPost("flowFilename");
        $folder = $this->request->getPost('upload_folder');
        $messages['upload_folder'] = $this->crypto->decryptSession(trim($folder));
        $status = $ftp->uploadFile($file_name, $folder, $domain_name);
        $messages['file_name'] = $ftp->getChunkedFileName();

        if(!$status) {
            $messages['type'] = 'error';
            $messages['messages'] = __('Failed');
            $messages['title'] = __('File content can\'t upload');
            $messages['errors'] = [];
            foreach($ftp->getMessages() as $message) {
                $messages['messages'] = $message;
            }
            return $this->response->setJsonContent($messages)->send();
        }
        else {
            // Son part gonderilmis oluyor burada
            if($this->request->getPost("flowTotalChunks") == $this->request->getPost("flowChunkNumber")) {
                if(!$ftp->uploadFtp($file_name, $folder, $domain_name)) {
                    $messages['type'] = 'error';
                    $messages['messages'] = __('Failed');
                    $messages['title'] = __('File content can\'t upload');
                    $messages['errors'] = [];

                    foreach($ftp->getMessages() as $message) {
                        $messages['messages'] = $message;
                    }
                }
                $ftp->clearOldUploadedFiles();
            }
        }
        $this->response->setJsonContent($messages)->send();
        return true;
    }

    public function downloadAction($file_name)
    {
        //@TODO: Duzelt
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        $this->view->setVar('domain_id', $domain_id);

        if(ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');
        $file = $file_name;
        $file_name = $this->crypto->decryptSession($file);
        $default_path = VHOST_DIR."/$domain_name";
        $file_name = $default_path.$file_name;


        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false); // required for certain browsers
        header("Content-Type: application/force-download");

        header("Content-Disposition: attachment; filename=\"".basename($file_name)."\";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($file_name));
        readfile("$file_name");
    }

    public function cancelAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_data['name'];
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }

        $ftp = new Ftp();
        $ftp->setOs($this->server_os);
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Upload canceled');
        $file_name = $this->request->getPost("file_name");
        $folder = $this->request->getPost('upload_folder');
        $messages['upload_folder'] = $this->crypto->decryptSession(trim($folder));
        $ftp->cancelUploadedFile($file_name, $folder, $domain_name);
        $this->response->setJsonContent($messages)->send();
    }
}