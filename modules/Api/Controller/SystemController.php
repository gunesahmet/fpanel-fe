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
 */namespace Api\Controller;

class SystemController extends ApiController
{
    use \Api\Controller\System\Servers;
    use \Api\Controller\System\AdvancedSettings;
    use \Api\Controller\System\BackupManagement;
    use \Api\Controller\System\BlacklistManagement;
    use \Api\Controller\System\CronManagement;
    use \Api\Controller\System\DatabaseServerManagement;
    use \Api\Controller\System\DnsServerManagement;
    use \Api\Controller\System\FtpServerManagement;
    use \Api\Controller\System\MailServerManagement;
    use \Api\Controller\System\NetworkManegement;
    use \Api\Controller\System\ServiceManagement;
    use \Api\Controller\System\WebServerManagement;

    public function initialize(): void
    {
        parent::initialize();
        $this->checkAuth();
        $this->fapi->setRequestData($this->request_data)
            ->setUserId($this->api_auth->getUserId())
            ->setUserType($this->api_auth->getUsername());
    }

    public function serversAction($action): void
    {
        switch ($action) {
            case "list":
                $this->listServer();
                break;
            case "add":
                $this->addServer();
                break;
            case "update":
                $this->updateServer();
                break;
            case "delete":
                $this->deleteServer();
                break;
            case "resource":
                $this->getServerResources();
                break;
        }
    }

    public function advancedSettingsAction($action)
    {
        switch ($action) {
            case "password-rule":
                $this->passwordRules();
                break;
            case "system-certificate":
                $this->systemCertificate();
                break;
            case "recaptcha":
                $this->recaptcha();
                break;
            case "additional-admin":
                $this->additionalAdmin();
                break;
        }
    }

    public function backupAction($action)
    {
        switch ($action) {
            case "list":
                $this->backupList();
                break;
            case "create":
                $this->createBackup();
                break;
            case "backup-detail":
                $this->backupDetail();
                break;
            case "delete":
                $this->deleteBackup();
                break;
            case "restore":
                $this->restoreBackup();
                break;
        }
    }

    public function blacklistAction($action)
    {
        switch ($action) {
            case "list":
                $this->blacklist();
                break;
            case "create":
                $this->createBlackList();
                break;
            case "delete":
                $this->deleteBlackList();
                break;
        }
    }

    public function cronAction($action)
    {
        switch ($action) {
            case "list":
                $this->crons();
                break;
            case "create":
                $this->createCron();
                break;
            case "delete":
                $this->deleteCron();
                break;
        }
    }

    public function databaseAction($action)
    {
        switch ($action) {
            case "list":
                $this->databaseServers();
                break;
            case "add":
                $this->addDatabaseServer();
                break;
            case "update":
                $this->updateDatabaseServer();
                break;
            case "delete":
                $this->deleteDatabaseServer();
                break;
            case "settings":
                $this->databaseServerSettings();
                break;
        }
    }

    public function dnsAction($action)
    {
        switch ($action) {
            case "list":
                $this->dnsList();
                break;
            case "create":
                $this->createDnsRecord();
                break;
            case "update":
                $this->updateDnsRecord();
                break;
            case "delete":
                $this->deleteDnsRecord();
                break;
            case "restore":
                $this->restoreDefault();
                break;
        }
    }

    public function ftpAction($action)
    {
        switch ($action) {
            case "settings":
                $this->getFtpSettings();
                break;
            case "update":
                $this->updateFtpSettings();
                break;
            case "sessions":
                $this->ftpSessions();
                break;
        }
    }

    public function mailAction($action)
    {
        switch ($action) {
            case "settings":
                $this->getMailSettings();
                break;
            case "update":
                $this->updateMailSettings();
                break;
            case "spam-settings":
                $this->getSpamSettings();
                break;
            case "update-spam-settings":
                $this->updateSpamSettings();
                break;
            case "blacklist":
                $this->getSpamBlackList();
                break;
            case "add-blacklist":
                $this->addSpamBlackList();
                break;
            case "delete-blacklist":
                $this->deleteSpamBlackList();
                break;
            case "whitelist":
                $this->getSpamWhiteList();
                break;
            case "add-whitelist":
                $this->addSpamWhiteList();
                break;
            case "delete-whitelist":
                $this->deleteSpamWhiteList();
                break;
            case "smtp-queue":
                $this->getSmtpQueue();
                break;
            case "clear-smtp-queue":
                $this->clearSmtpQueue();
                break;
            case "flush-smtp-queue":
                $this->flushSmtpQueue();
                break;
        }
    }

    public function networkAction($action)
    {
        switch ($action) {
            case "list":
                $this->networkList();
                break;
            case "add":
                $this->addIpAddress();
                break;
            case "update":
                $this->updateIpAddress();
                break;
            case "delete":
                $this->deleteIpAddress();
                break;
            case "reread":
                $this->readAllIpAddress();
                break;
            case "hostname":
                $this->setHostname();
                break;
        }
    }

    public function serviceAction($action)
    {
        switch ($action) {
            case "list":
                $this->getServicesList();
                break;
            case "start":
                $this->startService();
                break;
            case "stop":
                $this->stopService();
                break;
            case "restart":
                $this->restartService();
                break;
        }
    }

    public function webserverAction($action)
    {
        switch ($action) {
            case "get":
                $this->getWebServerSettings();
                break;
            case "update":
                $this->updateWebServerSettings();
                break;
            case "modules":
                $this->getWebServerModules();
                break;
            case "set-module":
                $this->setWebServerModule();
                break;
            case "mimetypes":
                $this->getMimeTypes();
                break;
            case "add-mimetype":
                $this->addMimeTypes();
                break;
            case "remove-mimetype":
                $this->removeMimeTypes();
                break;
        }
    }
}