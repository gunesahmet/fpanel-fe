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
 */
namespace FPanel\Common\Statistics;

use DateInterval;
use DateTime;
use FPanel\Components\Components;
use FPanel\Domain\DomainDatabase;
use FPanel\Domain\Mails;
use FPanel\Models\ClientStatistics;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\Models\FtpUsers;
use FPanel\Models\Users;
use FPanel\Process\Mail;
use FPanel\Process\User;
use FPanel\Process\Vhost;
use Phalcon\Mvc\User\Component;

class Client extends Component
{
    protected $status_message;

    public function initReseller()
    {
        $users = Users::find("type = '2'");
        /**
         * @var $user Users
         */
        foreach ($users as $user) {
            $domain_statistics = DomainStatistics::find("client_id = '$user->id'");
            $total_stats = [
                'mail' => 0,
                'db' => 0,
                'httpdoc' => 0,
                'logs' => 0,
                'disk' => 0,
                'traffic' => 0,
                'status' => $user->status,
                'last_init' => "",
            ];

            /**
             * @var $domain DomainStatistics
             */
            foreach ($domain_statistics as $domain) {
                $total_stats['mail'] += ($domain->mail != null ? $domain->mail : 0);
                $total_stats['db'] += ($domain->db != null ? $domain->db : 0);
                $total_stats['httpdoc'] += ($domain->httpdoc != null ? $domain->httpdoc : 0);
                $total_stats['logs'] += ($domain->logs != null ? $domain->logs : 0);
                $total_stats['disk'] += ($domain->disk != null ? $domain->disk : 0);
                $total_stats['traffic'] += ($domain->traffic != null ? $domain->traffic : 0);
                $total_stats['last_init'] = ($domain->last_init != null ? $domain->last_init : $this->date->dateTime());
            }

            $child_statistics = ClientStatistics::find("parent_id = '$user->id'");
            /**
             * @var $child ClientStatistics
             */
            foreach ($child_statistics as $child) {
                $total_stats['mail'] += ($child->mail != null ? $child->mail : 0);
                $total_stats['db'] += ($child->db != null ? $child->db : 0);
                $total_stats['httpdoc'] += ($child->httpdoc != null ? $child->httpdoc : 0);
                $total_stats['logs'] += ($child->logs != null ? $child->logs : 0);
                $total_stats['disk'] += ($child->disk != null ? $child->disk : 0);
                $total_stats['traffic'] += ($child->traffic != null ? $child->traffic : 0);
                if($total_stats['last_init'] == null) {
                    $domain_statistics = DomainStatistics::findFirst("client_id = '$child->client_id'");
                    $total_stats['last_init'] = ($domain_statistics->last_init != null ? $domain_statistics->last_init : $this->date->dateTime());
                }
            }

            $this->calculateUser($user->id, $total_stats);
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }

        $this->log->info([
            "message" => "Reseller stats calculate is done"
        ]);
    }

    public function initClient()
    {
        $users = Users::find("type = '3'");
        /**
         * @var $user Users
         */
        foreach ($users as $user) {
            $domain_statistics = DomainStatistics::find("client_id = '$user->id'");
            $total_stats = [
                'mail' => 0,
                'db' => 0,
                'httpdoc' => 0,
                'logs' => 0,
                'disk' => 0,
                'traffic' => 0,
                'status' => $user->status,
                'last_init' => null,
            ];

            /**
             * @var $domain DomainStatistics
             */
            foreach ($domain_statistics as $domain) {
                $total_stats['mail'] += ($domain->mail != null ? $domain->mail : 0);
                $total_stats['db'] += ($domain->db != null ? $domain->db : 0);
                $total_stats['httpdoc'] += ($domain->httpdoc != null ? $domain->httpdoc : 0);
                $total_stats['logs'] += ($domain->logs != null ? $domain->logs : 0);
                $total_stats['disk'] += ($domain->disk != null ? $domain->disk : 0);
                $total_stats['traffic'] += ($domain->traffic != null ? $domain->traffic : 0);
                $total_stats['last_init'] = ($domain->last_init != null ? $domain->last_init : $this->date->dateTime());
            }

            $this->calculateUser($user->id, $total_stats);
            $this->log->info([
                "message" => "Clients stats calculate is done",
                "data" => $total_stats
            ]);
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }


    }


    private function changeStatusDomains($parent_id, $status)
    {
        $domains = Domains::find("client_id = '$parent_id'");
        foreach($domains as $domain) {

            $domains = Domains::findFirstById($domain->id);
            $domains->assign(array(
                'status' => $status,
                'status_msg' => $this->status_message
            ));
            $domains->update();

            $users = Users::findFirst("username = '$domains->name'");
            $users->assign(array(
                'status' => $status,
                'status_msg' => $this->status_message
            ));
            $users->update();

            $statistics = DomainStatistics::findFirst("domain_id = '$domain->id'");
            $ftp_users = FtpUsers::findFirstById($domains->ftp_user_id);
            $statistics->assign(array('status' => $status));
            $statistics->update();
            $this->changeStatusCurl($status, $domains, $ftp_users->username);
        }
    }


    private function suspend($id)
    {
        $this->status_message = "Over limit";
        $user = Users::findFirstById($id);
        if($user) {
            $user->assign(array('status' => '-1','status_msg' => 'Over limit'));
            $user->update();
            $statistics = ClientStatistics::findFirst("client_id = '$id'");
            $statistics->assign(array('status' => '-1'));
            $statistics->update();

            $clients = Users::find("parent = '$id'");
            foreach($clients as $client) {
                $this->changeStatusDomains($id,"-1");
                $this->changeStatusChild($client->id,"-1");
            }
        }
    }

    private function unsuspend($id)
    {
        $this->status_message = "Reset by system";
        $user = Users::findFirstById($id);
        if($user) {
            $user->assign(array('status' => '1','status_msg' => 'Reset by system'));
            $user->update();
            $statistics = ClientStatistics::findFirst("client_id = '$id'");
            $statistics->assign(array('status' => '1'));
            $statistics->update();

            $clients = Users::find("parent = '$id'");
            foreach($clients as $client) {
                $this->changeStatusChild($client->id,"1");
                $this->changeStatusDomains($client->id,"1");
            }
        }
    }

    private function changeStatusChild($id, $status)
    {
        $users = Users::findFirstById($id);
        $users->assign(array(
            'status' => $status,
            'status_msg' => $this->status_message
        ));
        $users->update();

        $statistics = ClientStatistics::findFirst("client_id = '$id'");
        $statistics->assign(array('status' => "$status"));
        $statistics->update();
        $this->changeStatusDomains($id, $status);

    }

    private function changeStatusCurl($status, Domains $domain , $ftp_name)
    {
        $params = array(
            'domain' => $domain->name,
            'user' => $ftp_name
        );
        $usermng = new User();
        $vhostmng = new Vhost();
        $mailmng = new Mail();
        $usermng->setServerId($domain->web_server_id);
        $vhostmng->setServerId($domain->web_server_id);
        $mailmng->setServerId($domain->mail_server_id);

        if($status == "1") {
            $usermng->unsuspend($params);
            $vhostmng->unsuspend($params);
            $mailmng->unsuspendDomain($params);
        }
        else {
            $usermng->suspend($params);
            $vhostmng->suspend($params);
            $mailmng->suspendDomain($params);
        }
    }

    private function calculateUser($client_id, $datas)
    {
        $current_traffic = $datas['traffic'];
        $current_db_disk = $datas['db'];
        $current_httpdoc_disk = $datas['httpdoc'];
        $current_logs_disk = $datas['logs'];
        $current_disk = $datas['disk'];
        $current_mail_disk = $datas['mail'];
        $message = "";
        $client_statistics = ClientStatistics::findFirst("client_id = '$client_id'");
        $client = Users::findFirst("id = '$client_id'");
        if($client_statistics) {
            if($client_statistics->last_init != null) {
                $last_init = $client_statistics->last_init;
            }
            else {
                $last_init = $client_statistics->created_date;
            }

            $d1 = new DateTime($last_init);
            $d2 = new DateTime("now");
            $months = 0;

            $d1->add(new DateInterval('P1M'));
            while ($d1 <= $d2){
                $months ++;
                $d1->add(new DateInterval('P1M'));
            }

            if($months > 1) {
                $last_init = strtotime("+1 month", $last_init);
            }

            $assign = [
                'traffic'   => $current_traffic,
                'db'        => $current_db_disk,
                'httpdoc'   => $current_httpdoc_disk,
                'logs'      => $current_logs_disk,
                'disk'      => $current_disk,
                'mail'      => $current_mail_disk,
                'last_init'      => $last_init,
            ];

            $status = ACTIVE;

            if($months >= 1) {
                $assign['traffic'] = "0";
                if(($client_statistics->status != MANUAL_PASSIVE_BY_OWNER) && ($client_statistics->status != MANUAL_PASSIVE_BY_ADMIN))
                    $status = ACTIVE;
            }

            $mail_status = ACTIVE;

            if(($client_statistics->traffic_max != UNLIMITED) && is_numeric($client_statistics->traffic_max)) {
                if ($current_traffic >= $client_statistics->traffic_max) {
                    $message = 'Traffic limit exceeded';
                    if($client_statistics->over_use == OVERUSE) {
                        $this->log->debug([
                            "Module" => "Statistics\\Client",
                            "Action" => "calculateUser",
                            "Message" => "Traffic limit exceeded Overuse is active Client ID: $client_statistics->client_id"
                        ]);
                    }
                    else {
                        $status = TRAFFIC_LIMIT_EXCEEDED;
                        $this->log->debug([
                            "Module" => "Statistics\\Client",
                            "Action" => "calculateUser",
                            "Message" => "Traffic limit exceeded Overuse is disabled Client ID: $client_statistics->client_id"
                        ]);
                    }
                }
            }
            if(($client_statistics->disk_max != UNLIMITED) && is_numeric($client_statistics->disk_max)) {
                if ($current_disk >= $client_statistics->disk_max) {
                    $message = 'Disk limit exceeded';
                    if($client_statistics->over_use != OVERUSE) {
                        $status = DISK_LIMIT_EXCEEDED;
                    }
                }
            }
            if(($client_statistics->mail_max != UNLIMITED) && is_numeric($client_statistics->mail_max)) {
                if ($current_mail_disk >= $client_statistics->mail_max) {
                    $message = 'Mail Disk limit exceeded';
                    if($client_statistics->over_use != OVERUSE) {
                        $mail_status = PASSIVE;
                    }
                }
                else {
                    $mail_status = ACTIVE;
                }
            }
            if(($client_statistics->db_max != UNLIMITED) && is_numeric($client_statistics->db_max)) {
                if($client_statistics->over_use == OVERUSE) {
                    $message .= "\nDatabase disk size limit overused";
                }
                else {
                    $domains = Domains::find("client_id = '$client_statistics->client_id'");
                    foreach ($domains as $domain) {
                        $domain_statistics = DomainStatistics::findFirst("domain_id = '$domain->id'");
                        $domain_database = new DomainDatabase();
                        if((int)$client_statistics->db_max <= (int)$current_db_disk) {
                            $lock_status = $domain_database->lockAllMySQLPrivileges($domain->id);
                            if(!$lock_status) {
                                $this->log->error($domain_database->getMessages());
                            }

                            $lock_status = $domain_database->lockAllPostgreSQLPrivileges($domain->id);
                            if(!$lock_status) {
                                $this->log->error($domain_database->getMessages());
                            }

                            $lock_status = $domain_database->lockAllMSSQLPrivileges($domain->id);
                            if(!$lock_status) {
                                $this->log->error($domain_database->getMessages());
                            }
                        }
                        else {
                            if($domain_statistics->over_use != "0") {
                                if(($domain_statistics->disk_max > $domain_statistics->disk) || ($domain_statistics->db_max > $domain_statistics->db)) {
                                    $unlock_status = $domain_database->unlockAllMySQLPrivileges($domain->id);
                                    if(!$unlock_status) {
                                        $this->log->error($domain_database->getMessages());
                                    }

                                    $unlock_status = $domain_database->unlockAllPostgreSQLPrivileges($domain->id);
                                    if(!$unlock_status) {
                                        $this->log->error($domain_database->getMessages());
                                    }

                                    $unlock_status = $domain_database->unlockAllMSSQLPrivileges($domain->id, $domain_statistics->db_max);
                                    if(!$unlock_status) {
                                        $this->log->error($domain_database->getMessages());
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if(($client_statistics->status != MANUAL_PASSIVE_BY_OWNER) && ($client_statistics->status != MANUAL_PASSIVE_BY_ADMIN)) {
                if($client_statistics->over_use == OVERUSE) {
                    $this->log->debug([
                        "Module" => "Statistics\\Client",
                        "Action" => "calculateUser",
                        "Message" => "Client ID: $client_statistics->client_id status will be active because of overuse enabled",
                    ]);
                    $status = ACTIVE;
                }
            }
            else {
                $this->log->debug([
                    "Module" => "Statistics\\Client",
                    "Action" => "calculateUser",
                    "Message" => "Client is not disabled by admins",
                ]);
                $status = $client_statistics->status;
            }

            $this->log->debug([
                "Module" => "Statistics\\Client",
                "Action" => "calculateUser",
                "Message" => "Status is $status Client ID: $client_statistics->client_id",
            ]);

            $assign['status'] = $status;
            $client_statistics->assign($assign);
            if(!$client_statistics->update()) {
                $this->log->error([
                    "Module" => "Statistics\\Client",
                    "Action" => "calculateUser",
                    "Error" => $client_statistics->getMessages(),
                    "Message" => "Client statistics cant update Client ID: $client_statistics->client_id",
                ]);
            }
            if($status != ACTIVE) {
                $domains = Domains::find("client_id = '$client_statistics->client_id'");
                foreach ($domains as $domain) {
                    $domain->status = PASSIVE;
                    $domain->status_msg = $message;
                    if(!$domain->update()) {
                        $this->log->error([
                            "Module" => "Statistics\\Client",
                            "Action" => "Domain update",
                            "Error" => $domain->getMessages(),
                            "Message" => "Domain cant update",
                        ]);
                    }
                    $domain->DomainStatistics->status = PASSIVE;

                    if(!$domain->DomainStatistics->update()) {
                        $this->log->error([
                            "Module" => "Statistics\\Client",
                            "Action" => "Domain statistics update",
                            "Error" => $domain->DomainStatistics->getMessages(),
                            "Message" => "Domain statistics cant update",
                        ]);
                    }

                    $domain->Users->status = PASSIVE;
                    $domain->Users->status_msg = $message;
                    if(!$domain->Users->update()) {
                        $this->log->error([
                            "Module" => "Statistics\\Client",
                            "Action" => "Domain Users update",
                            "Error" => $domain->Users->getMessages(),
                            "Message" => "Domain Users cant update",
                        ]);
                    }
                }
            }

            $user = new Components();

            if ($status != ACTIVE) {
                $this->log->info([
                    "client_name" => $client->username,
                    "message" => "Suspending"
                ]);
                $user->userData(array(
                    'user_id' => "1",
                    'user_type' => "0",
                ))
                    ->suspend('user', $client_id, $message, $status);
            }
            else {
                $this->log->info([
                    "client_name" => $client->username,
                    "message" => "Unsuspending"
                ]);
                $user->userData(array(
                    'user_id' => "1",
                    'user_type' => "0",
                ))
                    ->unsuspend('user', $client_id, $message);
                $this->changeStatusUserDomainsMailService($client_id, $mail_status);
            }
        }
    }

    private function changeStatusUserDomainsMailService($client_id, $status)
    {
        $domains = Domains::find("client_id = '$client_id'");
        if($domains) {
            /**
             * @var $domain Domains
             */
            foreach ($domains as $domain) {
                $this->changeStatusDomainMailService($domain->id, $status);
            }
        }
    }

    private function changeStatusDomainMailService($domain_id, $status)
    {
        $mail = new Mails();
        $mail->changeMailSettings($status, $domain_id);
    }
}