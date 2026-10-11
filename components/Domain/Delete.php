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
 */namespace FPanel\Components\Domain;

use FPanel\Backup\Backup;
use FPanel\Components\Base;
use FPanel\Domain\Cronjobs;
use FPanel\Domain\DomainAlias;
use FPanel\Domain\DomainDatabase;
use FPanel\Domain\DomainSettings;
use FPanel\Domain\Mails;
use FPanel\Domain\NamedService;
use FPanel\Domain\Protectdir;
use FPanel\Domain\Services;
use FPanel\Domain\Sslcertificate;
use FPanel\Domain\Subdomain;
use FPanel\Domain\Vdir;
use FPanel\Models\Databases;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\Mails as Mail;
use FPanel\Models\MailSettings;
use FPanel\Models\ProtectDirectory;
use FPanel\Models\Subdomains;
use FPanel\Models\Users;
use FPanel\Process\Bind;
use FPanel\Process\Directory;
use FPanel\Process\User;
use FPanel\Process\Vhost;
use FPanel\System\LimitsPermission;
use FPanel\System\Statistics;
use FPanel\Users\Usersdata;

/**
 * Class Delete
 * @package FPanel\Components\Delete
 */
class Delete extends Base
{
    /**
     * @var Domains $domain
     */
    protected $domain;

    protected $deleted_user_id;

    public function getDomain($domain_id): Delete
    {
        $domain = Domains::findFirstById($domain_id);
        if($domain) {
            $users = Users::findFirst("username = '$domain->name'");
            $this->deleted_user_id = $users->id;
            $this->domain = $domain;
        }
        else {
            $this->error_messages["content"] = __("Domain not found");
        }
        return $this;
    }

    public function validation(): bool
    {
        if($this->parent_type != ROOT_USER) {
            $domain_user = Users::findFirstById($this->domain->client_id);
            $domain_parent = Users::findFirstById($domain_user->parent); // It can be CLIENT so we need this parent
            if($domain_parent->id == $this->parent_id) {
                return true;
            }
            else {
                $domain_top_parent = Users::findFirstById($domain_parent->id);
                if($domain_top_parent->id == $this->parent_id) {
                    return true;
                }
            }
        }
        else {
            return true; // Adam admin!!! istedigini yapar!!!
        }
        $this->error_messages["content"] = __("You have no right to do this.");
        return false;
    }

    public function deleteClient(): Delete
    {
//        $this->debug->firebug(json_encode([
//            "file" => __FILE__,
//            "class" => __CLASS__,
//            "function" => __FUNCTION__,
//            "method" => __METHOD__,
//            "line" => __LINE__,
//        ]), "debug");
        $domain_name = $this->domain->name;
        $user = Users::findFirst("username = '$domain_name'");
        if($user) {
            $deleted_domain_user = new Usersdata();
            $deleted_domain_user->delete($user->id, $this->parent_type, $this->parent_id);
        }

        return $this;
    }

    public function deleteLimitsPerm(): Delete
    {
        $deleted_domain_user_limits = new LimitsPermission();
        if($this->deleted_user_id != null)
            $deleted_domain_user_limits->delete($this->deleted_user_id);
        return $this;
    }

    public function deleteStatistics(): Delete
    {
        $delete_domain_statistic = new Statistics();
        $delete_domain_statistic->deleteDomainStatistic($this->domain->id);
        return $this;
    }

    public function deleteDns(): Delete
    {
        if(!empty($this->error_messages)) {
            return $this;
        }
        $dns = new NamedService();
        $dns->deleteDomainRecords($this->domain->id);
        return $this;
    }

    public function deleteStorages(): Delete
    {
        $backup = new Backup();
        $backup->deleteAllStorage($this->deleted_user_id);
        return $this;
    }

    public function deleteBackups(): Delete
    {
        $backup = new Backup();
        $backup->deleteAll(DOMAIN, $this->domain->id);
        return $this;
    }

    public function deleteSSL(): Delete
    {
        $ssl = new Sslcertificate();
        $ssl->deleteAllSSLDomain($this->domain->id);
        return $this;
    }

    public function deleteDatabases(): Delete
    {
        $database = new DomainDatabase();
        $domain_id = $this->domain->id;
        $database_list = Databases::find("domain_id = '$domain_id'");
        if($database_list) {
            foreach($database_list as $db) {
                $database->deleteDatabase($db->id, $domain_id);
            }
        }
        return $this;
    }

    public function deleteDomainAlias(): Delete
    {
        $domain_id = $this->domain->id;
        $aliases = \FPanel\Models\DomainAlias::find("domain_id = '$domain_id'");
        if($aliases) {
            foreach($aliases as $alias) {
                $domain_alias = new DomainAlias();
                $domain_alias->setPosts(array(
                    'domain_id' => $domain_id,
                ));
                $domain_alias->deleteAlias($alias->id, $this->domain->name, $domain_id);
            }
        }
        return $this;
    }

    public function deleteMail(): Delete
    {
        $domain_id = $this->domain->id;
        $mail_addresses = Mail::find("domain_id = '$domain_id'");
        if($mail_addresses) {
            foreach($mail_addresses as $mail_addr) {
                $mail = new Mails();
                $mail->setPosts(array(
                    'domain_name' => $this->domain->name,
                    'domain_id' => $domain_id,
                    'mail_id' => $mail_addr->id,
                ));
                $mail->deleteMailAccount();
            }
        }

        $mail_param = MailSettings::findFirst("domain_id = '$domain_id'");
        if($mail_param) {
            if(!$mail_param->delete()) {
                foreach($mail_param->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
            }
        }
        return $this;
    }

    public function deleteProDir(): Delete
    {
        $domain_id = $this->domain->id;
        $pro_dir = ProtectDirectory::find("domain_id = '$domain_id'");
        if($pro_dir) {
            foreach($pro_dir as $pro) {
                $protect_dir = new Protectdir();
                $protect_dir->setPosts(array(
                    'dir_id' => $pro->id,
                    'domain_id' => $domain_id,
                ));
                $protect_dir->domain_name = $this->domain->name;
                $protect_dir->deleteProtectDir();
            }
        }
        return $this;
    }

    public function deleteTasks(): Delete
    {
        $scheduled_task = new Cronjobs();
        $scheduled_task->deleteAll(FtpUsers::findFirstById($this->domain->ftp_user_id), $this->domain);
        return $this;
    }

    public function deleteVirtualDirectory(): Delete
    {
        if(($this->system_type == CLOUD) || PANEL_OS_TYPE == WINDOWS) {
            $virtual_dir = new Vdir();
            $virtual_dir->deleteAll($this->domain->id);
        }
        return $this;
    }

    public function deleteSubdomains(): Delete
    {
        $domain_id = $this->domain->id;
        $subdomains = Subdomains::find("domain_id = '$domain_id'");
        if($subdomains) {
            foreach($subdomains as $subdomain) {
                $sub = new Subdomain();
                $sub->setPosts(array(
                    'id' => $subdomain->id,
                    'domain_id' => $domain_id
                ));
                $sub->deleteSubdomain($domain_id);
            }
        }
        return $this;
    }

    public function deleteServices(): Delete
    {
        $services = new Services();
        $services->delete($this->domain->id);
        return $this;
    }

    public function deleteDomain(): Delete
    {
        $domain_settings = new DomainSettings();
        $domain_settings->delete($this->domain->id);
        return $this;
    }

    public function deleteCommand($sock_open): Delete
    {
        if($sock_open) {
            // User delete
            // Vhost delete
            // Mail delete
            // Dns delete
            // cloud or windows ? apppoolmng

            $dnsmng = new Bind();
            $mailmng = new \FPanel\Process\Mail();
            $vhostmng = new Vhost();
            $dirmng = new Directory();
            $usermng = new User();

            $dnsmng->setServerId($this->domain->dns_server_id);
            $dirmng->setServerId($this->domain->dns_server_id);
            $mailmng->setServerId($this->domain->mail_server_id);
            $usermng->setServerId($this->domain->web_server_id);
            $vhostmng->setServerId($this->domain->web_server_id);

            $user_params = [
                'domain' => $this->domain->name, // Domain name
                'prefix' => $this->domain->prefix, // Domain name
                'user' => $this->domain->getFtpUsers()->username, // Ftp Username
            ];
            $dir_params = [
                'domain' => $this->domain->name, // Domain name
                'prefix' => $this->domain->prefix, // Domain name
                'user' => $this->domain->getFtpUsers()->username, // Ftp Username
            ];
            $vhost_params = [
                'domain' => $this->domain->name, // Domain name
                'prefix' => $this->domain->prefix, // Domain name
                'user' => $this->domain->getFtpUsers()->username, // Ftp Username
            ];
            $mail_params = [
                'domain' => $this->domain->name, // Domain name
                'prefix' => $this->domain->prefix, // Domain name
                'user' => $this->domain->getFtpUsers()->username, // Ftp Username
            ];
            $dns_params = [
                'domain' => $this->domain->name, // Domain name
                'prefix' => $this->domain->prefix, // Domain name
                'user' => $this->domain->getFtpUsers()->username, // Ftp Username
            ];

            $dnsmng->remove($dns_params);
            $mailmng->removeDomain($mail_params);
            $vhostmng->remove($vhost_params);
            $dirmng->remove($dir_params);
            $usermng->remove($user_params);
        }
        return $this;
    }

}