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

trait Restore
{
    public function restoreAction($id)
    {
        $this->response->setHeader("title", __('Restore Backup'));
        $this->tag->appendTitle(__('Restore Backup'));
        $data = $this->user_data;
        $this->view->setVar('id', $id);

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $prefix = $this->domain_data["prefix"];
            $this->view->setVar('domain_id', $domain_id);
            $redirect_url = $this->uri->url_encrypt('backup/domain/'.$domain_id);
            $post_url = $this->uri->url_encrypt("backup/restore/$id/domain/$domain_id");
            $this->breadcrumbs->add(__('Backup List'), 'backup/domain/'.$domain_id, ' ');
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $prefix = $data['user']['domain_info']["prefix"];
            $redirect_url = $this->uri->url_encrypt('backup');
            $post_url = $this->uri->url_encrypt("backup/restore/$id");
            $this->breadcrumbs->add(__('Backup List'), 'backup', ' ');
        }

        $this->view->setVar('post_url', $post_url);
        $this->view->setVar("domain_name", $domain_name);
        $this->view->setVar("domain_prefix", $prefix);
        $this->breadcrumbs->add(__('Restore Backup'), '', '');

        $backup_model = \FPanel\Models\Backup::findFirst("id = '$id' and domain_id = '$domain_id'");

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
            "backup-path" => PANEL_BACKUP_DIR . "/domains/$domain_name/" . $this->date->getTimeStamp($backup_model->created_date) . DIRECTORY_SEPARATOR,
        ]);

        $config_data = json_decode($config_data, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            $this->view->setVar('error_content',__('Backup not found'));
            $this->response->setStatusCode(404, __('Backup not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $this->debug->debug($config_data, false);
        $content = trim($backup_model->content, "\"");
        $backup_content['content'] = json_decode($content,true);
        $backup_content['size'] = json_decode($backup_model->sizes,true);
        $backup_content['subdomains'] = [];
        $backup_content['database_names'] = [];
        $backup_content['mail_names'] = [];
        foreach ($config_data['subdomains'] as $subdomain) {
            $backup_content['subdomains'][md5($subdomain["full_name"])] = $subdomain["full_name"];
        }
        foreach ($config_data['databases'] as $key => $database_name) {
            $backup_content['database_names'][$key] = md5($database_name["type"]."_".$database_name["name"]);
        }
        foreach ($config_data['mails'] as $k => $mail) {
            $backup_content['mail_names'][$k] = md5($mail["mail_name"]."@".$domain_name);
        }

        $backup_content['mail_accounts'] = $config_data['mails'];
        $backup_content['databases'] = $config_data['databases'];
        $backup_content['sizes'] = $config_data['sizes'];


//        $this->debug->debug($backup_content, false);
        $this->view->setVar('backup_content', $backup_content);

        $options = array(
            'action'    => 'restore',
            'backup_content' => $backup_content
        );
        $this->view->form = new BackupForm(null, $options);

        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'info';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Restore starting');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'domain_name' => $domain_name,
                'domain_id' => $domain_id,
                'domain_prefix' => $prefix,
                'backup_id' => $id,
                'restore_dns' => $this->request->getPost('restore_dns',null,"off"),
                'restore_httpdoc' => $this->request->getPost('restore_httpdoc',null,"off"),
                'restore_subdomains' => $this->request->getPost('restore_subdomains',null,"off"),
                'restore_databases' => $this->request->getPost('restore_databases',null,"off"),
                'restore_mails' => $this->request->getPost('restore_mails',null,"off"),
                "databases" => $this->request->getPost("databases"),
                "subdomains" => $this->request->getPost("subdomains"),
                "mails" => $this->request->getPost("mails"),
            );


            $backup = new \FPanel\Backup\Backup();
            $backup->setPosts($post_data);
            $status = $backup->restore(DOMAIN_BACKUP);
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Restore starting failed');
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
                    $this->flashSession->notice(__('Restore starting'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }
}