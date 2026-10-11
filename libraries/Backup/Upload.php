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

use FPanel\Process\BackupRestore;
use ZipArchive;

trait Upload
{
    public function uploadDomainBackupToLocal(): bool
    {
        $file_name = trim($this->getPost("file_name"));
        $domain_id = $this->getPost("domain_id");
        $upload_dir = $this->getPost("upload_dir");
//        $ts = $this->getPost("ts");

//        $backup_name = str_replace('.'.BACKUP_EXTENSION,'', $file_name);

        $zip = new ZipArchive();
        $zip_file = $zip->open($upload_dir.DIR_SEPARATOR.$file_name);
        if($zip_file === true) {
            $comments = $zip->getArchiveComment();
            $comments = str_replace("FCONFIG=","", $comments);
            $result = $this->crypto->staticDecode(trim($comments));
            $result = preg_replace('/[[:cntrl:]]/', '', $result);
            $decoded_comments = json_decode($result, true);
            if(json_last_error() !== JSON_ERROR_NONE) {
                $this->error_messages["file_name"] = __("Backup file is not correct");
                echo json_encode(["status" => false, "message" => json_last_error_msg()]);
                header("HTTP/1.1 400 Bad Request");
                unlink($zip_file);
                rmdir($upload_dir);
                $this->debug->debug(json_last_error_msg(), false);
                return false;
            }
        }
        else {
            if(file_exists($upload_dir.DIR_SEPARATOR.$file_name)) {
                $this->error_messages["file_name"] = __("File is exists but not readable: %file_name%", ["file_name" => "$upload_dir/$file_name"]);
                $this->debug->firebug("File is exists but not readable: $upload_dir/$file_name", "debug");
//                echo json_encode(["status" => false, "message" => __("File is exists but not readable: %file_name%", ["file_name" => $upload_dir/$file_name])]);
            }
            else {
                $this->error_messages["file_name"] = __("File doesn't exists: %file_name%", ["file_name" => "$upload_dir/$file_name"]);
                $this->debug->firebug("File doesnt exists: $upload_dir/$file_name", "debug");
//                echo json_encode(["status" => false, "message" => __("File doesnt exists: %file_name%", ["file_name" => $upload_dir/$file_name])]);
            }
            unlink($zip_file);
            rmdir($upload_dir);
            header("HTTP/1.1 400 Bad Request");
            return false;
        }
        $created_date = $decoded_comments["backup_data"]["created_date"];
        $backup_name = $decoded_comments["backup_data"]["name"];
        $description = $decoded_comments["backup_data"]["description"];
        $content = $decoded_comments["backup_data"]["content"];
        $backup = \FPanel\Models\Backup::findFirst("name = '$backup_name' and created_date = '$created_date'");
        rename($upload_dir.DIR_SEPARATOR.$file_name, $upload_dir.DIR_SEPARATOR . "$backup_name.zip");
        if(!$backup) {
            $backup = new \FPanel\Models\Backup();
            $backup->assign(array(
                "id" => $this->env->createUuid(),
                'type' => DOMAIN,
                'repository' => 'server',
                'status' => BACKUP_READING,
                'name'  => $backup_name,
                'description' => "$description (Uploaded Backup)",
                'split_size' => "0",
                'created_date' => $created_date,
                'domain_id' => $domain_id,
                'content' => json_encode($content)
            ));
            $domain_name = $this->getPost("domain_name");

            if($backup->create()) {
                $backupmng = new BackupRestore();
                $params = array(
                    'domain' => $domain_name,
                    'tmp-date' => $this->getPost("ts"),
                    'date' => $this->date->getTimeStamp($created_date),
                    'backup-id' => $backup->id,
                    'name' => $backup_name,
                    'data' => "upload",
                    'repo' => "server",
                );
                $params["server-addr"] = $this->request->getServerAddress();
                $backupmng->uploadBackup($params);
                $this->debug->firebug("File uploaded: $upload_dir/$file_name", "debug");
                return true;
            }
            else {
                foreach($backup->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "Upload.php",
                    "Error" => "Backup upload create failed",
                    "Data" => $this->error_messages
                ]);
            }
        }

        if(file_exists($zip_file))
            unlink($zip_file);
        if(is_dir($upload_dir))
            rmdir($upload_dir);
        $this->error_messages["backup_name"] = __("Backup already exists");
        return false;
    }

    public function uploadClientBackupToLocal(): bool
    {
        $file_name = $this->getPost("file_name");
        $client_id = $this->getPost("client_id");
        $upload_dir = $this->getPost("upload_dir");

        if(file_exists($upload_dir.DIR_SEPARATOR.$file_name)) {
            $zip = new ZipArchive();
            $zip_file = $zip->open($upload_dir.DIR_SEPARATOR.$file_name);
            $this->debug->firebug("File processing: $upload_dir/$file_name", "debug");
            if($zip_file === true) {
                $comments = $zip->getArchiveComment();
                $comments = str_replace("FCONFIG=","", $comments);
                $result = $this->crypto->fdecode($comments);
                $decoded_comments = json_decode($result, true);
                if(json_last_error() !== JSON_ERROR_NONE) {
                    $this->error_messages["file_name"] = __("Backup file is not correct");
                    echo json_encode(["status" => false, "message" => __("Backup file is not correct")]);
                    header("HTTP/1.1 400 Bad Request");
                    return false;
                }
            }
            else {
                $this->error_messages["file_name"] = __("Backup file is not correct. File doesnt exists.");
                $this->debug->firebug("File doesnt exists: $upload_dir/$file_name", "debug");
                echo json_encode(["status" => false, "message" => __("File doesnt exists: %file_name%", ["file_name" => $upload_dir/$file_name])]);
                header("HTTP/1.1 400 Bad Request");
                return false;
            }
        }
        else {
            $this->error_messages["file_name"] = __("Backup file is not correct. File cant read.");
            $this->debug->firebug("File is exists but not readable: $upload_dir/$file_name", "debug");
            echo json_encode(["status" => false, "message" => __("File is exists but not readable: %file_name%", ["file_name" => $upload_dir/$file_name])]);
            header("HTTP/1.1 400 Bad Request");
            return false;
        }

        $ts = $decoded_comments["backup_data"]["created_date"];
        $backup_name = $decoded_comments["backup_data"]["name"];
        $description = $decoded_comments["backup_data"]["description"];
        $content = $decoded_comments["backup_data"]["content"];
        $backup = \FPanel\Models\Backup::findFirst("name = '$backup_name' and created_date = '$ts' and client_id = '$client_id'");
        rename($upload_dir.DIR_SEPARATOR.$file_name, $upload_dir.DIR_SEPARATOR . "$backup_name.zip");

        if(!$backup) {
            $backup = new \FPanel\Models\Backup();
            $backup->assign(array(
                "id" => $this->env->createUuid(),
                'type' => 'client',
                'repository' => 'server',
                'status' => BACKUP_READING,
                'name'  => $backup_name,
                'description' => "$description (Uploaded Backup)",
                'split_size' => "0",
                'created_date' => $ts,
                'client_id' => $client_id,
                'content' => json_encode($content)
            ));
            $client_name = $this->getPost("client_name");

            if($backup->create()) {
                $backupmng = new BackupRestore();
                $params = array(
                    'client-name' => $client_name,
                    'tmp-date' => $this->getPost("ts"),
                    'date' => $ts,
                    'backup-id' => $backup->id,
                    'name' => $backup_name,
                    'data' => "upload",
                    'repo' => "server",
                );
                $params["server-addr"] = $this->request->getServerAddress();
                $backupmng->uploadClientBackup($params);
//                $this->debug->firebug("File uploaded: $upload_dir/$file_name", "Debug");
                return true;
            }
            else {
                foreach($backup->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "Upload.php",
                    "Error" => "Backup upload create failed",
                    "Data" => $this->error_messages
                ]);
            }
        }

        $this->error_messages["backup_name"] = __("Backup already exists");
        return false;
    }

    public function uploadServerBackupToLocal(): bool
    {
        $file_name = $this->getPost("file_name");
        $server_id = $this->getPost("server_id");
        $upload_dir = $this->getPost("upload_dir");

        $zip = new ZipArchive();
        $zip_file = $zip->open($upload_dir.DIR_SEPARATOR.$file_name);
        if($zip_file === true) {
            $comments = $zip->getArchiveComment();
            $comments = str_replace("FCONFIG=","", $comments);
            $result = $this->crypto->fdecode($comments);
            $decoded_comments = json_decode($result, true);
            if(json_last_error() !== JSON_ERROR_NONE) {
                $this->error_messages["file_name"] = __("Backup file is not correct.");
                echo json_encode(["status" => false, "message" => __("Backup file is not correct")]);
                header("HTTP/1.1 400 Bad Request");
                return false;
            }
        }
        else {
            $this->debug->firebug("Cant read zip file: $upload_dir/$file_name", "debug");
            if(!file_exists($upload_dir.DIR_SEPARATOR.$file_name)) {
                $this->error_messages["file_name"] = __("Backup file is not correct. File Can not readable.");
                $this->debug->firebug("File is exists but not readable: $upload_dir/$file_name", "debug");
                echo json_encode(["status" => false, "message" => __("File is exists but not readable: %file_name%", ["file_name" => "$upload_dir/$file_name"])]);
            }
            else {
                $this->error_messages["file_name"] = __("Backup file is not correct. File doesnt exists.");
                $this->debug->firebug("File doesnt exists: $upload_dir/$file_name", "debug");
                echo json_encode(["status" => false, "message" => __("File doesnt exists: %file_name%", ["file_name" => "$upload_dir/$file_name"])]);
            }
            header("HTTP/1.1 400 Bad Request");
            return false;
        }
        $ts = $decoded_comments["backup_data"]["created_date"];
        $backup_name = $decoded_comments["backup_data"]["name"];
        $description = $decoded_comments["backup_data"]["description"];
        $content = $decoded_comments["backup_data"]["content"];
        $backup = \FPanel\Models\Backup::findFirst("name = '$backup_name' and created_date = '$ts' and server_id = '$server_id'");
        rename($upload_dir.DIR_SEPARATOR.$file_name, $upload_dir.DIR_SEPARATOR . "$backup_name.zip");
        if(!$backup) {
            $backup = new \FPanel\Models\Backup();
            $backup->assign(array(
                "id" => $this->env->createUuid(),
                "type" => "server",
                "repository" => "server",
                "status" => BACKUP_READING,
                "name"  => $backup_name,
                "description" => "$description (Uploaded Backup)",
                "split_size" => "0",
                "created_date" => $ts,
                "server_id" => $server_id,
                "content" => json_encode($content)
            ));

            if($backup->create()) {
                $backupmng = new BackupRestore();
                $params = array(
                    'date' => $ts,
                    'tmp-date' => $this->getPost("ts"),
                    'backup-id' => $backup->id,
                    'name' => $backup_name,
                    'data' => "upload",
                    'repo' => "server",
                    "server-addr" => $this->request->getServerAddress(),
                );
                $backupmng->uploadServerBackup($params);
                $this->debug->firebug("File uploaded: $upload_dir/$file_name", "debug");
                return true;
            }
            else {
                foreach($backup->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "File" => "Upload.php",
                    "Error" => "Backup upload create failed",
                    "Data" => $this->error_messages
                ]);
            }
        }

        $this->error_messages["backup_name"] = __("Backup already exists");
        return false;
    }
}