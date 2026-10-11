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
 */namespace FPanel\Common\Client;

use Exception;
use FPanel\Models\ClientLimitsPermissions;
use FPanel\Models\Databases;
use FPanel\Models\DatabaseUsers;
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;
use FPanel\Models\DomainAlias;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\MailAlias;
use FPanel\Models\Mails as Mail;
use FPanel\Models\MailSettings;
use FPanel\Models\ScheduledTasks;
use FPanel\Models\Subdomains;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use Phalcon\Mvc\User\Component;

class DomainConfig extends Component
{
    public $domain_data = array(
        'version' => "1.0",
        'backup_data' => array(),
        'domain' => array(),
        'user' => array(),
        'userdata' => array(),
        'limits' => array(),
        'dns' => array(),
        'domain_alias' => array(),
        'mail_settings' => array(),
        'cronjobs' => array(),
        'subdomains' => array(),
        'databases' => array(),
        'prodirs' => array(),
        'mails' => array(),
        'mail_params' => array(),
    );
    public $domain_id;
    public $ftp_user_id;
    public $domain_name;
    public $ftpname;
    public $ftp_pass;
    public $user_id;
    public $error_status = false;

    public function domain($domain_id): DomainConfig
    {
        $this->domain_id = $domain_id;
        $domain = Domains::findFirstById($domain_id);
        if($domain) {
            $this->ftp_user_id = $domain->ftp_user_id;
            $this->domain_name = $domain->name;
            $this->domain_id = $domain_id;

            $this->domain_data['domain'] = array(
                'domain_name'   => $domain->name,
                'status'        => $domain->status,
                'status_msg'    => $domain->status_msg,
                'create_date'   => $domain->created_date,
                'expire_date'   => $domain->expire_date
            );
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function ftpuser(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        $ftpuser = FtpUsers::findFirstById($this->ftp_user_id);
        if($ftpuser) {
            $this->ftpname = $ftpuser->username;
            $this->ftp_pass = trim($this->crypto->bdecode($ftpuser->password));
            $this->domain_data['ftpuser'] = array(
                'ftpname' => $this->ftpname,
                'password' => $this->ftp_pass
            );
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function user(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        $user = Users::findFirst("username = '$this->domain_name'");
        if($user) {
            $this->user_id = $user->id;
            // User Table
            $this->domain_data['user'] = array(
                'username'              => $user->username,
                'password'              => $user->password,
                'parent'                => $user->parent,
                'type'                  => $user->type,
                'must_change_password'  => $user->must_change_password,
                'created_date'          => $user->created_date,
                'expire_date'           => $user->expire_date,
                'status_msg'            => $user->status_msg,
                'status'                => $user->status,
                'interface'             => $user->interface,
                'language'              => $user->language
            );
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function userdata(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        // Userdata table
        $userdata = Userdata::findFirst("user_id = '$this->user_id'");
        if($userdata) {
            $this->domain_data['userdata'] = array(
                'fullname'      => $userdata->fullname,
                'email'         => $userdata->email,
                'company'       => $userdata->company,
                'fax'           => $userdata->fax,
                'telephone'     => $userdata->telephone,
                'address'       => $userdata->address,
                'city'          => $userdata->city,
                'region'        => $userdata->region,
                'zipcode'       => $userdata->zipcode,
                'country'       => $userdata->country,
            );
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function limits(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        $limits = ClientLimitsPermissions::findFirst("domain_id = '$this->domain_id'");
        if($limits) {
            $data = $limits->toArray();
            unset($data['id']);
            unset($data['client_id']);
            unset($data['domain_id']);
            $this->domain_data['limits'] = $data;
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function dns(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        $dns_zone = DnsZone::findFirst("domain_id = '$this->domain_id'");
        if($dns_zone) {
            $dns_zone_id = $dns_zone->id;
            $dns_records = DnsRecords::find("dns_zone_id = '$dns_zone_id'");
            if($dns_records) {
                $this->domain_data['dns'] = $dns_records->toArray();
            }
        }
        else {
            $this->error_status = true;
        }
        return $this;
    }

    public function domainalias(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }

        $domain_alias = DomainAlias::find("domain_id = '$this->domain_id'");
        if($domain_alias) {
            $this->domain_data['domain_alias'] = $domain_alias->toArray();
        }

        return $this;
    }

    public function mailsettings(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }

        $mail_settings = MailSettings::findFirst("domain_id = '$this->domain_id'");
        if($mail_settings) {
            $this->domain_data['mail_settings'] = $mail_settings->toArray();
        }
        else {
            $this->error_status = true;
        }

        return $this;
    }

    public function cronjobs(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }

        $cronjobs = ScheduledTasks::find("domain_id = '$this->domain_id'");
        if($cronjobs) {
            $this->domain_data['cronjobs'] = $cronjobs->toArray();
        }
        return $this;
    }

    public function subdomains(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }

        $subdomains = Subdomains::find("domain_id = '$this->domain_id'");
        if($subdomains) {
            $subdomain_data = array();

            foreach($subdomains as $sub) {
                $sub_data = array(
                    'subdomain_name'     => $sub->subdomain_name,
                    'full_name'          => $sub->full_name,
                    'ftpuser_type'       => $sub->ftpuser_type,
                    'php'            => $sub->php,
                    'php_version'    => $sub->php_version,
                    'cgi'            => $sub->cgi,
                    'perl'           => $sub->perl,
                    'python'         => $sub->python,
                    'fast_cgi'       => $sub->fast_cgi,
                    'status'         => $sub->status,
                    'asp'            => $sub->asp,
                    'aspnet_core'    => $sub->aspnet_core,
                    'aspnet'         => $sub->aspnet,
                    'aspnet_version' => $sub->aspnet_version,
                    'dedicated_pool' => $sub->dedicated_pool,
                    'max_cpu'        => $sub->max_cpu,
                );

                if($sub['ftpuser_type'] == MAIN) {
                    $sub_data['ftpname'] =  $this->ftpname;
                    $sub_data['password'] =  $this->ftp_pass;
                }
                else {
                    $ftp_user = FtpUsers::findFirstById($sub['ftpuser_id']);
                    if($ftp_user) {
                        $sub_data['ftpname'] =  $ftp_user->username;
                        $sub_data['password'] =  trim($this->crypto->bdecode($ftp_user->password));
                    }
                    else {
                        $this->error_status = true;
                    }
                }
                $subdomain_data[] = $sub_data;
            }
            $this->domain_data['subdomains'] = $subdomain_data;
        }
        return $this;
    }

    public function databases(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        $databases = Databases::find("domain_id = '$this->domain_id'");
        if(!empty($databases)) {
            if(isset($databases[0])) {
                $domain = Domains::findFirstById($databases[0]->domain_id);
                $database_data = array();
                foreach($databases as $db) {
                    $db_name = str_replace($domain->prefix."_","", $db->name);
                    $db_user = DatabaseUsers::findFirstById($db->database_user_id);
                    if($db_user) {
                        $database_data[] = array(
                            'id'            => $db->id,
                            'name'          => $db->name,
                            'clear_name'    => $db_name,
                            'type'          => $db->type,
                            'dbusername'    => $db_user->login,
                            'dbpassword'    => trim($this->crypto->bdecode($db_user->password)),
                        );
                    }
                    else {
                        $this->error_status = true;
                    }
                }
                $this->domain_data['databases'] = $database_data;
            }
        }
        return $this;
    }

    public function mails(): DomainConfig
    {
        if($this->error_status == true) {
            return $this;
        }
        $mails = Mail::find("domain_id = '$this->domain_id'");
        if($mails) {
            $mail_data = array();
            foreach($mails as $mail) {
                $mail_account = array(
                    'password'      => trim($this->crypto->bdecode($mail->password)),
                    'mail_name'     => $mail->mail_name,
                    'forward'       => $mail->forward,
                    'forward_addr'  => $mail->forward_addr,
                    'mail_group'    => $mail->mail_group,
                    'autoresponder' => $mail->autoresponder,
                    'mailbox_quota' => $mail->mailbox_quota,
                );
                $mail_id = $mail['id'];
                $mail_alias = MailAlias::find("mail_id = '$mail_id'");
                if($mail_alias) {
                    foreach($mail_alias->toArray() as $alias) {
                        $mail_account['mail_alias'][] = $alias['alias_name'];
                    }
                }
                $mail_data[] = $mail_account;
            }
            $this->domain_data['mails'] = $mail_data;
        }
        return $this;
    }
}