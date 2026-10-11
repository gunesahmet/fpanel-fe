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
 */namespace FPanel\Backup;

use FPanel\Environments\Datatable;
use FPanel\Environments\Environments;
use FPanel\Exceptions\Exception;
use FPanel\Library\Library;
use FPanel\Models\CloudStorage;
use FPanel\Models\Domains;
use FPanel\Models\FtpRepo;
use FPanel\Models\Users;
use FPanel\Process\BackupRestore;
use FPanel\Process\Cloudbackup;

/**
 * Class Backup
 * @package FPanel\Backup
 */
class Backup extends Library
{
    use Repo, Storage, Upload, Restore;
    public $client_id;

    public function listBackup($domain_id, $nested = false): array
    {
        try {
            $enviorement = new Environments();
            $backup_status = \FPanel\Models\Backup::findFirst("status NOT LIKE '1' AND status NOT LIKE '4' AND domain_id = '$domain_id'");
            $datatable = new Datatable();
            $datatable->select([
                "backup.id",
                "backup.name",
                "backup.description",
                "backup.created_date",
                "backup.status",
                "backup.repository",
                "backup.sizes"
            ])
                ->from("backup")
                ->where("backup.domain_id = '$domain_id'")
                ->andWhere("backup.type = '1'");
            $datatable->searchColumn(array('backup.name'));
            $data = $datatable->generate()->execute()->result();
            foreach($data['aaData'] as $key => $value){

                // Description
                $data['aaData'][$key][1] = $value[2];

                // Create Date
                $data['aaData'][$key][2] = $value[3];

                switch($value[5]) {
                    case "ftp":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('FTP') .' </span>';
                        break;
                    case "server":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('Server') .' </span>';
                        break;
                    case "storage":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('Cloud Storage') .' </span>';
                        break;
                }

                if($nested) {
                    $restore_url = "backup/restore/$value[0]/domain/$domain_id";
                    $delete_url = "backup/delete/$value[0]/domain/$domain_id";
                    $download_url = "backup/download/$value[0]/domain/$domain_id";
                }
                else {
                    $restore_url = "backup/restore/$value[0]";
                    $delete_url = "backup/delete/$value[0]";
                    $download_url = "backup/download/$value[0]";
                }
                if(!$backup_status) {
                    $buttons = '<a class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m btn btn-sm-block btn-xs-block" href="'. $this->uri->url_encrypt($restore_url) .'" data-id="nested"><i class="fa fa-sync"></i> ' . __("Restore"). '</a>';
                    $buttons .= ' <a class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m btn btn-sm-block btn-xs-block" data-url="'. $this->uri->url_encrypt($delete_url) .'" data-id="confirm"><i class="fa fa-times"></i> ' . __("Delete"). '</a>';
                    $buttons .= ' <a class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m btn btn-sm-block btn-xs-block" target="_blank" href="/'. $this->uri->url_encrypt($download_url, true) .'"><i class="fas fa-download"></i> ' . __("Download") . '</a>';
                }
                else {
                    $buttons = '<a class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m btn btn-sm-block btn-xs-block" data-url="'. $this->uri->url_encrypt($delete_url) .'" data-id="confirm"><i class="fa fa-times"></i> ' . __("Delete"). '</a>';
                }

                // Status
                switch($value[4]) {
                    case BACKUP_WAITING:
                        $data['aaData'][$key][3] = '<span class="label label-warning"> '.__('Processing').' </span>';
                        if($nested) {
                            $cancel_url = $this->uri->url_encrypt('backup/cancel/'.$value[0].'/domain/'.$domain_id);
                        }
                        else {
                            $cancel_url = $this->uri->url_encrypt('backup/cancel/'.$value[0]);
                        }
                        $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Cancel') .'</a>';
                        break;
                    case BACKUP_FINISHED:
                        $data['aaData'][$key][3] = '<span class="label label-success"> '. __('Finished') .' </span>';
                        $data['aaData'][$key][6] = $buttons;
                        break;
                    case BACKUP_RESTORING:
                        $data['aaData'][$key][3] = '<span class="label label-danger">'. __('Restoring') .'</span>';
//                    $data['aaData'][$key][0] = '<a href="'. $this->uri->url_encrypt('backup/status/'.$value[0].'/domain/'.$domain_id).'">'.$value[1].'</a>';
                        $data['aaData'][$key][6] = "";
                        break;
                    case BACKUP_READING:
                        $data['aaData'][$key][3] = '<span class="label label-warning"> '. __('Reading') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        $data['aaData'][$key][6] = "";
                        break;
                    case BACKUP_FAILED:
                        $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Corrupted') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        if($nested) {
                            $cancel_url = $this->uri->url_encrypt('backup/cancel/'.$value[0].'/domain/'.$domain_id);
                        }
                        else {
                            $cancel_url = $this->uri->url_encrypt('backup/cancel/'.$value[0]);
                        }
                        $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Cancel') .'</a>';
                        break;
                    case RESTORE_FAILED:
                        $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Restore Failed') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        if($nested) {
                            $cancel_url = $this->uri->url_encrypt('backup/cancel/'.$value[0].'/domain/'.$domain_id);
                        }
                        else {
                            $cancel_url = $this->uri->url_encrypt('backup/cancel/'.$value[0]);
                        }
                        $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Cancel') .'</a>';
                        break;
                }

                if(!$backup_status) {
                    if($nested) {
                        $data['aaData'][$key][0] = '<a href="'.$this->uri->url_encrypt('backup/restore/'.$value[0].'/domain/'.$domain_id).'">'.$value[1].'</a>';
                    }
                    else {
                        $data['aaData'][$key][0] = '<a href="'.$this->uri->url_encrypt('backup/restore/'.$value[0]).'">'.$value[1].'</a>';
                    }
                }
                else {
                    $data['aaData'][$key][0] = $value[1];
                }

                $data['aaData'][$key][4] = $enviorement->convert($value[6]);



                if($value[5] == "ftp") {
                    if($nested) {
                        $cancel_url = $this->uri->url_encrypt('backup/delete/'.$value[0].'/domain/'.$domain_id);
                    }
                    else {
                        $cancel_url = $this->uri->url_encrypt('backup/delete/'.$value[0]);
                    }
                    $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Delete') .'</a>';
                }

            }
            return $data;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return [];
        }
    }

    public function listServerBackup($server_id = "1"): array
    {
        try {
            $backup_status = \FPanel\Models\Backup::findFirst("status NOT LIKE '1' AND status NOT LIKE '4'");

            $datatable = new Datatable();

            if($server_id == "1") {
                $delete_button = $datatable->buttons('serverbackup/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $restore_button = $datatable->buttons('serverbackup/restore/$1','normal',array('i' => 'fa fa-rotate-left','a' => 'text-success'),__('Restore'),null,'data-id="nested"');
                $download_button = $datatable->buttons('serverbackup/download/$1','normal',array('i' => 'fa fa-download','a' => 'text-warning'),__('Download'));
            }
            else {
                $delete_button = $datatable->buttons('serverbackup/delete/$1/server/'.$server_id,'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $restore_button = $datatable->buttons('serverbackup/restore/$1/server/'.$server_id,'normal',array('i' => 'fa fa-rotate-left','a' => 'text-success'),__('Restore'),null,'data-id="nested"');
                $download_button = $datatable->buttons('serverbackup/download/$1/server/'.$server_id,'normal',array('i' => 'fa fa-download','a' => 'text-warning'),__('Download'));
            }


            $datatable->select(array('backup.id','backup.name','backup.description','backup.created_date','backup.status','backup.repository','backup.sizes'))
                ->from("backup");
            if($server_id != "1") {
                $datatable->where("backup.server_id = '$server_id'");
                $datatable->andWhere("backup.type = 'server'");
            }
            else {
                $datatable->where("backup.type = 'server'");
            }

            if(!$backup_status) {
                $datatable->addColumn('actions', $restore_button.$delete_button.$download_button,'backup.id');
            }
            $datatable->searchColumn(array('backup.name'));

            $data = $datatable->generate()->execute()->result();

            foreach($data['aaData'] as $key => $value){

                // Description
                $data['aaData'][$key][1] = $value[2];

                // Create Date
                $data['aaData'][$key][2] = gmdate("Y-m-d H:i:s", $value[3]);

                switch($value[5]) {
                    case "ftp":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('FTP') .' </span>';
                        break;
                    case "server":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('Server') .' </span>';
                        break;
                    case "storage":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('Cloud Storage') .' </span>';
                        break;
                }

                // Status
                switch($value[4]) {
                    case "0":
                        $data['aaData'][$key][3] = '<span class="label label-warning"> '.__('Backuping').' </span>';
                        if($server_id != "1") {
                            $cancel_url = $this->uri->url_encrypt('serverbackup/cancel/'.$value[0].'/server/'.$server_id);
                        }
                        else {
                            $cancel_url = $this->uri->url_encrypt('serverbackup/cancel/'.$value[0]);
                        }
                        $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Cancel') .'</a>';
                        break;
                    case "1":
                        $data['aaData'][$key][3] = '<span class="label label-success"> '. __('Finished') .' </span>';
                        $data['aaData'][$key][6] = $value[7];
                        break;
                    case "2":
                        $data['aaData'][$key][3] = '<span class="label label-danger">'. __('Restoring') .'</span>';
                        $data['aaData'][$key][6] = "";
                        break;
                    case "3":
                        $data['aaData'][$key][3] = '<span class="label label-warning"> '. __('Reading') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        $data['aaData'][$key][6] = "";
                        break;
                    case "4":
                        $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Corrupted') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        if($server_id != "1") {
                            $cancel_url = $this->uri->url_encrypt('serverbackup/cancel/'.$value[0].'/server/'.$server_id);
                        }
                        else {
                            $cancel_url = $this->uri->url_encrypt('serverbackup/cancel/'.$value[0]);
                        }
                        $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Cancel') .'</a>';
                        break;
                }
                if(!$backup_status) {
                    if($server_id != "1") {
                        $data['aaData'][$key][0] = '<a href="'.$this->uri->url_encrypt('serverbackup/restore/'.$value[0].'/server/'.$server_id).'">'.$value[1].'</a>';
                    }
                    else {
                        $data['aaData'][$key][0] = '<a href="'.$this->uri->url_encrypt('serverbackup/restore/'.$value[0]).'">'.$value[1].'</a>';
                    }
                }
                else {
                    $data['aaData'][$key][0] = $value[1];
                }

                $sizes = json_decode($value[6],true);
                $data['aaData'][$key][4] = $this->env->convert($sizes['backup_size']);

                if($value[5] == "ftp") {
                    if($server_id != "1") {
                        $data['aaData'][$key][6] = '<a data-id="confirm" class="text-danger" data-url="'. $this->uri->url_encrypt('serverbackup/delete/'.$value[0].'/server/'.$server_id) .'"><i class="fa fa-times"></i> '. __('Delete').'</a>';
                    }
                    else {
                        $data['aaData'][$key][6] = '<a data-id="confirm" class="text-danger" data-url="'. $this->uri->url_encrypt('serverbackup/delete/'.$value[0]) .'"><i class="fa fa-times"></i> '. __('Delete').'</a>';
                    }
                }

            }
            return $data;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return [];
        }
    }

    public function listClientBackup($client_id, $nested = false): array
    {
        try {
            $enviorement = new Environments();
            $backup_status = \FPanel\Models\Backup::findFirst("status NOT LIKE '1' AND status NOT LIKE '4' AND client_id = '$client_id'");
            $datatable = new Datatable();
            if($nested) {
                $delete_button = $datatable->buttons("backupclient/delete/$1/client/$client_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $restore_button = $datatable->buttons("backupclient/restore/$1/client/$client_id",'normal',array('i' => 'fa fa-rotate-left','a' => 'text-success'),__('Restore'),null,'data-id="nested"');
                $download_button = $datatable->buttons("backupclient/download/$1/client/$client_id",'normal',array('i' => 'fa fa-download','a' => 'text-info'),__('Download'));
            }
            else {
                $delete_button = $datatable->buttons('backupclient/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
                $restore_button = $datatable->buttons('backupclient/restore/$1','normal',array('i' => 'fa fa-rotate-left','a' => 'text-success'),__('Restore'),null,'data-id="nested"');
                $download_button = $datatable->buttons('backupclient/download/$1','normal',array('i' => 'fa fa-download','a' => 'text-info'),__('Download'));
            }
            $datatable->select(array('backup.id','backup.name','backup.description','backup.created_date','backup.status','backup.repository','backup.sizes'))
                ->from("backup")
                ->where("backup.client_id = '$client_id'")
                ->andWhere("backup.type = 'client'");
            if(!$backup_status) {
                $datatable->addColumn('actions', $restore_button.$delete_button.$download_button,'backup.id');
            }
            else {
                $datatable->addColumn('actions', $delete_button.$download_button,'backup.id');
            }
            $datatable->searchColumn(array('backup.name'));
            $data = $datatable->generate()->execute()->result();
            foreach($data['aaData'] as $key => $value){

                // Description
                $data['aaData'][$key][1] = $value[2];

                // Create Date
                $data['aaData'][$key][2] = gmdate("Y-m-d H:i:s", $value[3]);

                switch($value[5]) {
                    case "ftp":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('FTP') .' </span>';
                        break;
                    case "server":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('Server') .' </span>';
                        break;
                    case "storage":
                        $data['aaData'][$key][5] = '<span class="label label-info"> '. __('Cloud Storage') .' </span>';
                        break;
                }

                switch($value[4]) {
                    case BACKUP_WAITING:
                        $data['aaData'][$key][3] = '<span class="label label-warning"> '.__('Processing').' </span>';
                        break;
                    case BACKUP_FINISHED:
                        $data['aaData'][$key][3] = '<span class="label label-success"> '. __('Finished') .' </span>';
                        $data['aaData'][$key][6] = $value[7];
                        break;
                    case BACKUP_RESTORING:
                        $data['aaData'][$key][3] = '<span class="label label-danger">'. __('Restoring') .'</span>';
                        $data['aaData'][$key][6] = "";
                        break;
                    case BACKUP_READING:
                        $data['aaData'][$key][3] = '<span class="label label-warning"> '. __('Reading') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        $data['aaData'][$key][6] = "";
                        break;
                    case BACKUP_FAILED:
                        $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Corrupted') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        if($nested) {
                            $cancel_url = $this->uri->url_encrypt('backupclient/delete/'.$value[0].'/client/'.$client_id);
                        }
                        else {
                            $cancel_url = $this->uri->url_encrypt('backupclient/delete/'.$value[0]);
                        }
                        $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Delete') .'</a>';
                        break;
                    case RESTORE_FAILED:
                        $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Restore Failed') .' </span>';
                        $data['aaData'][$key][0] = $value[1];
                        if($nested) {
                            $cancel_url = $this->uri->url_encrypt('backupclient/delete/'.$value[0].'/client/'.$client_id);
                        }
                        else {
                            $cancel_url = $this->uri->url_encrypt('backupclient/delete/'.$value[0]);
                        }
                        $data['aaData'][$key][6] = ' <a class="text-danger" data-url="'.$cancel_url.'" data-id="confirm"><i class="fa fa-times"></i> '.__('Delete') .'</a>';
                        break;
                }

                if(!$backup_status) {
                    if($nested) {
                        $data['aaData'][$key][0] = '<a href="'.$this->uri->url_encrypt('backupclient/restore/'.$value[0].'/client/'.$client_id).'">'.$value[1].'</a>';
                    }
                    else {
                        $data['aaData'][$key][0] = '<a href="'.$this->uri->url_encrypt('backupclient/restore/'.$value[0]).'">'.$value[1].'</a>';
                    }
                }
                else {
                    $data['aaData'][$key][0] = $value[1];
                }

                $sizes = json_decode($value[6],true);
                $data['aaData'][$key][4] = $enviorement->convert($sizes['backup_size'] ?? 0);

                if($value[5] == "ftp") {
                    if($nested) {
                        $data['aaData'][$key][6] = '<a data-id="confirm" data-url="'. $this->uri->url_encrypt('backupclient/delete/'.$value[0].'/client/'.$client_id) .'" class="text-danger"><i class="fa fa-times"></i> '. __('Delete').'</a>';
                    }
                    else {
                        $data['aaData'][$key][6] = '<a data-id="confirm" data-url="'. $this->uri->url_encrypt('backupclient/delete/'.$value[0]) .'" class="text-danger"><i class="fa fa-times"></i> '. __('Delete').'</a>';
                    }
                }

            }
            return $data;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return [];
        }
    }


    // Create Backup

    public function create(int $type, $domain_id = null, $server_id = null, $client_id = null): bool
    {
        try {
            /**
             * status
             * continue = 0
             * finished = 1
             * restoring = 2
             * failed = 3
             */
            $created_date = $this->date->dateTime();

            switch($this->getPost("backup_repo")) {
                case "server":
                    $repository = "server";
                    break;
                case "ftp":
                    $repository = "ftp";
                    break;
                case "storage":
                    $repository = "storage";
                    break;
                default:
                    return false;
            }

            $this->setPost("repository", $repository);
            $backup_name = $this->getPost("backup_name");

            $data = array(
                'type' => $type,
                'repo' => $repository,
                'storages' => $this->getPost("storages"),
                'ftp_repo' => $this->getPost("ftp_repo"),
                'email' => $this->getPost("email_address"),
                'name' => $backup_name,
                'date' => $created_date,
                'domain_id' => $domain_id,
                'server_id' => $server_id,
                'client_id' => $client_id,
                'split_size' => $this->getPost("split_size"),
                'desc' => $this->getPost("description"),
            );

            if(!$this->backupValidation($type))
                return false;

            if(!$this->backup($type, $data))
                return false;

            $this->backupStartCurl($type, $client_id, $server_id);
            return true;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    private function backup(int $type, $data): bool
    {
        try {
            $backup = new \FPanel\Models\Backup();
            $assign = array(
                "id" => $this->env->createUuid(),
                'type' => $type,
                'repository' => $data['repo'],
                'status' => BACKUP_WAITING,
                'ftp_repository_id' => $data['ftp_repo'],
                'storage_id' => $data['storages'],
                'email' => $data['email'],
                'name'  => $data['name'],
                'description' => $data['desc'],
                'split_size' => $data['split_size'],
                'created_date' => $data['date']
            );
            $content = $this->createContent($type);

            switch($type) {
                case SERVER_BACKUP:
                    $assign['server_id'] = $data['server_id'];
                    break;
                case CLIENT_BACKUP:
                    $assign['client_id'] = $data['client_id'];
                    break;
                case DOMAIN_BACKUP:
                    $assign['domain_id'] = $data['domain_id'];
                    break;
            }
            $assign['content'] = $content;

            $backup->assign($assign);


            if (!$backup->create()) {
                foreach ($backup->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "Backup.php",
                    "Error" => "Backup create failed",
                    "Data" => $this->error_messages
                ]);
                return false;
            }
            $this->setPost("created_date", $data['date']);
            $this->setPost("backup_id", $backup->id);
            return true;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    private function createContent(int $type)
    {
        switch($type) {
            case DOMAIN_BACKUP:
                $content = array(
                    'domain_settings' => $this->getPost("domain_settings"),
                    'www_content' => $this->getPost("www_content"),
                    'database_content' => $this->getPost("database_content"),
                    'dns_content' => $this->getPost("dns_content"),
                    'mail_content' => $this->getPost("mail_content"),
                    'subdomain_content' => $this->getPost("subdomain_content"),
                );
                break;
            case SERVER_BACKUP:
            case CLIENT_BACKUP:
                $content = array(
                    'www_content' => $this->getPost("www_content"),
                    'subdomain_content' => $this->getPost("subdomain_content"),
                    'database_content' => $this->getPost("database_content"),
                    'dns_content' => $this->getPost("dns_content"),
                    'mail_content' => $this->getPost("mail_content"),
                    'domains' => $this->getPost("domains")
                );
                break;
//                $content = array(
//                    'www_content' => $this->getPost("www_content"),
//                    'subdomain_content' => $this->getPost("subdomain_content"),
//                    'database_content' => $this->getPost("database_content"),
//                    'dns_content' => $this->getPost("dns_content"),
//                    'mail_content' => $this->getPost("mail_content"),
//                    'domains' => $this->getPost("domains")
//                );
//                break;
            default:
                return false;
        }


        return json_encode($content);
    }

    private function backupStartCurl(int $type, $client_id, $server_id): bool
    {
        switch($type) {
            case DOMAIN_BACKUP:
                $domain_name = $this->getPost("domain_name");
                $domain = Domains::findFirst("name = '$domain_name'");
                $params = array(
                    'domain' => $domain_name,
                    'prefix' => $domain->prefix,
                    'date' => $this->date->getTimeStamp($this->getPost("created_date")),
                    'name' => $this->getPost("backup_name"),
                    'backup-id' => $this->getPost("backup_id"),
                    'repo' => $this->getPost("repository"),
                );

                if($this->getPost("repository") == "ftp") {
                    $ftp_repos = FtpRepo::findFirstById($this->getPost("ftp_repo"));
                    $params['ftp'] = $ftp_repos->ftp_server;
                    $params['ftp-user'] = $ftp_repos->ftp_user;
                    $params['ftp-folder'] = $ftp_repos->ftp_folder;
                    $params['ftp-passwd'] = trim($this->crypto->bdecode($ftp_repos->ftp_pass));
                }
                $params["server-addr"] = $this->getPost("server_addr");
                if($this->getPost("system_type") == CLOUD) {
                    $cloudbackup = new Cloudbackup();
                    $cloudbackup->backupDomain($params);
                }
                else {
                    $backupdomain = new BackupRestore();
                    $backupdomain->backupDomain($params);
                }
                break;
            case SERVER_BACKUP:
                $backupclient = new BackupRestore();
                $params = array(
                    'date' => $this->date->getTimeStamp($this->getPost("created_date")),
                    'name' => $this->getPost("backup_name"),
                    'backup-id' => $this->getPost("backup_id"),
                    'data' => "dir",
                    'repo' => $this->getPost("repository"),
                    'server-id' => $server_id,
                );
                $params["server-addr"] = $this->getPost("server_addr");
                $backupclient->backupServer($params);
                break;
            case CLIENT_BACKUP:
                $backupclient = new BackupRestore();
                $params = array(
                    'date' => $this->date->getTimeStamp($this->getPost("created_date")),
                    'name' => $this->getPost("backup_name"),
                    'client-name' => $this->getPost("client_name"),
                    'backup-id' => $this->getPost("backup_id"),
                    'data' => "dir",
                    'repo' => $this->getPost("repository"),
                    'client-id' => $client_id,
                );
                $params["server-addr"] = $this->getPost("server_addr");
                $backupclient->backupClient($params);
                break;
            default:
                return false;
        }
        return true;
    }


    //Delete

    public function delete(int $type, $id, $user_id = array()): bool
    {
        try {
            switch($type) {
                case DOMAIN_BACKUP:
                    $conditions = "id = ?1 AND domain_id = ?2 AND type = ?3";
                    $parameters = array(1 => $id, 2 => $user_id['domain'], 3 => DOMAIN_BACKUP);
                    break;
                case CLIENT_BACKUP:
                    $conditions = "id = ?1 AND client_id = ?2 AND type = ?3";
                    $parameters = array(1 => $id, 2 => $user_id['client'], 3 => CLIENT_BACKUP);
                    break;
                case SERVER_BACKUP:
                    $conditions = "id = ?1 AND server_id = ?2 AND type = ?3";
                    $parameters = array(1 => $id, 2 => $user_id['server'], 3 => SERVER_BACKUP);
                    break;
                default:
                    $conditions = "";
                    $parameters = "";
            }

            $backup = \FPanel\Models\Backup::findFirst(array($conditions,"bind" => $parameters));

            if($backup) {
                $backup_data = $backup->toArray();
                if(!$backup->delete()) {
                    foreach($backup->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    return false;
                }
                $this->setPost("backup_name", $backup_data['name']);
                $this->setPost("created_date", $backup_data['created_date']);
                $this->deleteCurl();
                return true;
            }
            else {
                return false;
            }
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    private function deleteCurl()
    {
        try {
            $backupmng = new BackupRestore();
            $path = PANEL_BACKUP_DIR;
            if($this->getPost("type") == DOMAIN_BACKUP) {
                $path .= "/domains/" . $this->getPost("domain_name");
            }
            else if($this->getPost("type") == CLIENT_BACKUP) {
                $path .= "/clients/" . $this->getPost("client_name") ;
            }
            else if($this->getPost("type") == SERVER_BACKUP) {
                $path .= "/server/";
            }

            $params = array(
                'backup-path' => $path,
                'date' => $this->date->getTimeStamp($this->getPost("created_date")),
                'name' => $this->getPost("backup_name")
            );
            $params["server-addr"] = $this->request->getServerAddress();
            $backupmng->removeBackup($params);
            return true;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    // Delete Domain

    public function deleteAll(int $type, $client_id): bool
    {
        try {
            switch($type) {
                case DOMAIN:
                    $type = DOMAIN_BACKUP;
                    $conditions = "domain_id = '$client_id' AND type = '$type'";
                    $domain = Domains::findFirstById($client_id);
                    if($domain) {
                        $domain_data = $domain->toArray();
                        $this->setPost("domain_name", $domain_data['name']);
                    }
                    break;
                case USER:
                    $type = CLIENT_BACKUP;
                    $conditions = "client_id = '$client_id' AND type = '$type'";
                    $user = Users::findFirstById($client_id);
                    if($user) {
                        $this->setPost("user_name", $user->username);
                    }
                    break;
                default:
                    return false;
            }

            $backup = \FPanel\Models\Backup::find($conditions);

            if($backup) {
                if(!$backup->delete()) {
                    foreach($backup->getMessages() as $title => $message) {
                        $this->error_messages[$title] = $message;
                    }
                    return false;
                }
                $this->deleteAllCurl($type);
                return true;
            }
            else {
                return false;
            }
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }

    public function deleteAllCurl(int $type): bool
    {
        $backupmng = new BackupRestore();
        $path = PANEL_BACKUP_DIR;
        if($type == DOMAIN_BACKUP) {
            $path .= DIR_SEPARATOR . $this->getPost("domain_name");
        }
        else if($type == CLIENT_BACKUP) {
            $path .= "/clients/" . $this->getPost("client_name") ;
        }
        else if($type == SERVER_BACKUP) {
            $path .= "/server/";
        }

        $params = array(
            'backup-path' => $path,
        );
        $params["server-addr"] = $this->request->getServerAddress();
        $backupmng->removeAllBackup($params);
        return true;

    }

    private function backupValidation(int $type = DOMAIN_BACKUP): bool
    {
        try {
            $required = array(
                array(
                    'field' => 'backup_name',
                    'item' => $this->getPost("backup_name"),
                    'message' => __('Backup name is required')
                ),
                array(
                    'field' => 'backup_repo',
                    'item' => $this->getPost("backup_repo"),
                    'message' => __('Please select a backup repository')
                ),
            );

            if($this->getPost("send_mail") == ON) {
                $required[] = array(
                    'field' => 'email_address',
                    'item' => $this->getPost("email_address"),
                    'message' => __('Email address is required')
                );
            }

            if($type == DOMAIN_BACKUP) {
                $backup_parts = array(
                    'domain_settings',
                    'www_content',
                    'database_content',
                    'dns_content',
                    'mail_content',
                    'subdomain_content',
                );

                $parts = false;
                foreach($backup_parts as $part) {
                    if($this->getPost($part) == ON)
                        $parts = true;
                }

                if(!$parts) {
                    $this->error_messages['backup_part'] = __('Please select a backup part');
                }
            }

            if($this->getPost("backup_repo") != "server") {
                if(($this->getPost("backup_repo") != "ftp") && ($this->getPost("backup_repo") != "storage")) {
                    $this->error_messages['backup_repo'] = __('Please select a backup repository');
                    return false;
                }
                if($this->getPost("backup_repo") == "ftp") {
                    $ftp_repo_id = $this->getPost("ftp_repo");
                    $ftp_repos = FtpRepo::findFirst("id = '$ftp_repo_id' AND client_id = '$this->client_id'");
                    if(!$ftp_repos) {
                        $this->error_messages['backup_repo'] = __('Please select a valid ftp repository');
                        return false;
                    }
                }
                if($this->getPost("backup_repo") == "storage") {
                    $storage_id = $this->getPost("storages");
                    $ftp_repos = CloudStorage::findFirst("id = '$storage_id' AND client_id = '$this->client_id'");
                    if(!$ftp_repos) {
                        $this->error_messages['backup_repo'] = __('Please select a valid storage');
                        return false;
                    }
                }

            }

            $this->validation->Required($required)
                ->WithoutSpace($this->getPost("backup_name"),"backup_name",__('Please enter valid backup name'));

            if($this->getPost("send_mail") == ON)
                $this->validation->Email($this->getPost("email_address"),'email_address');

            if(!$this->validation->Validate()) {
                foreach($this->validation->getMessage() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }

            return true;
        }
        catch (\Exception $e) {
            $this->error_messages["exception"] = $e->getMessage();
            return false;
        }
    }
}