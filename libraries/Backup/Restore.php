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
use FPanel\Process\Cloudbackup;

trait Restore
{
    public function restore(int $type): bool
    {
        switch($type) {
            case DOMAIN_BACKUP:
                return $this->domainRestore($this->getPost("domain_id"));
            case CLIENT_BACKUP:
                return $this->clientRestore($this->getPost("client_id"));
            case SERVER_BACKUP:
                return $this->serverRestore($this->getPost("server_id"));
        }
        return true;
    }

    private function domainRestore($domain_id): bool
    {
        $restore_contents = array(
            'restore_dns' => $this->getPost("restore_dns") == "on",
            'restore_httpdoc' => $this->getPost("restore_httpdoc") == "on",

            'restore_subdomains' => $this->getPost("restore_subdomains") == "on",
            'subdomains' => $this->getPost("subdomains"),

            'restore_databases' => $this->getPost("restore_databases") == "on",
            'databases' => $this->getPost("databases"),

            'restore_mails' => $this->getPost("restore_mails") == "on",
            'mails' => $this->getPost("mails"),
        );

        $backup_id = $this->getPost("backup_id");
        $backup = \FPanel\Models\Backup::findFirst("id = '$backup_id' and domain_id = '$domain_id'");
        if (!$backup) {
            $this->error_messages["backup_name"] = __("Backup not found");
            return false;
        }
        $backup->status = BACKUP_RESTORING;
        $backup->restore_contents = json_encode($restore_contents);

        if(!$backup->update()) {
            foreach ($backup->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }
        $this->setPost("created_date", $backup->created_date);
        $this->setPost("backup_name", $backup->name);
        $this->restoreDomainCurl();
        return true;
    }

    private function serverRestore($server_id): bool
    {
        $backup_id = $this->getPost("backup_id");
        $backup = \FPanel\Models\Backup::findFirst("id = '$backup_id' AND server_id = '$server_id'");
        if (!$backup) {
            $this->error_messages["backup_name"] = __("Backup not found");
            return false;
        }
        $backup->status = BACKUP_RESTORING;
        $backup->restore_contents = json_encode([
            "domain_list" => $this->getPost("domain_list"),
            "restore_mails" => $this->getPost("restore_mails"),
            "restore_dns" => $this->getPost("restore_dns"),
            "restore_databases" => $this->getPost("restore_databases"),
            "restore_subdomains" => $this->getPost("restore_subdomains"),
            "restore_domain_ftproot" => $this->getPost("restore_domain_ftproot"),
        ]);
        if(!$backup->update()) {
            foreach ($backup->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        $this->setPost("created_date", $backup->created_date);
        $this->setPost("backup_name", $backup->name);

        $this->restoreServerCurl();
        return true;
    }

    private function clientRestore($client_id): bool
    {
        if(!empty($this->getPost("domain_list"))) {
            foreach($this->getPost("domain_list") as $domain) {
                $domain_list[$domain] = "0";
            }
        }

        $backup_id = $this->getPost("backup_id");
        $backup = \FPanel\Models\Backup::findFirst("id = '$backup_id' AND client_id = '$client_id'");
        if (!$backup) {
            $this->error_messages["backup_name"] = __("Backup not found");
            return false;
        }
        $backup->status = BACKUP_RESTORING;
        $backup->restore_contents = json_encode([
            "domain_list" => $this->getPost("domain_list"),
            "restore_mails" => $this->getPost("restore_mails"),
            "restore_dns" => $this->getPost("restore_dns"),
            "restore_databases" => $this->getPost("restore_databases"),
            "restore_subdomains" => $this->getPost("restore_subdomains"),
            "restore_domain_ftproot" => $this->getPost("restore_domain_ftproot"),
        ]);
        if(!$backup->update()) {
            foreach ($backup->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            return false;
        }

        $this->setPost("created_date", $backup->created_date);
        $this->setPost("backup_name", $backup->name);

        $this->restoreClientCurl();
        return true;
    }

    private function restoreDomainCurl()
    {
        if(PANEL_TYPE == CLOUD) {
            $backupmng = new Cloudbackup();
        }
        else {
            $backupmng = new BackupRestore();
        }
        $params = array(
            "prefix"    => $this->getPost("domain_prefix"),
            "domain"    => $this->getPost("domain_name"),
            "date"      => $this->date->getTimeStamp($this->getPost("created_date")),
            "name"      => $this->getPost("backup_name"),
            "backup-id" => $this->getPost("backup_id"),
            "server-addr" => $this->request->getServerAddress(),
        );
        $backupmng->restoreDomain($params);
    }

    private function restoreServerCurl()
    {
        $backupmng = new BackupRestore();
        $params = array(
            'date' => $this->date->getTimeStamp($this->getPost("created_date")),
            'name' => $this->getPost("backup_name"),
            'backup-id' => $this->getPost("backup_id"),
            "server-addr" => $this->request->getServerAddress(),
        );
        $backupmng->restoreServer($params);
    }

    private function restoreClientCurl()
    {
        $backupmng = new BackupRestore();
        $params = array(
            'date' => $this->date->getTimeStamp($this->getPost("created_date")),
            'name' => $this->getPost("backup_name"),
            'client-name' => $this->getPost("client_name"),
            'backup-id' => $this->getPost("backup_id"),
        );
        $params["server-addr"] = $this->request->getServerAddress();
        $backupmng->restoreClient($params);
    }
}