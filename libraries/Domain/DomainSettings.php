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
 */namespace FPanel\Domain;

use FPanel\Environments\Datatable;
use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\Models\FtpUsers;
use FPanel\Models\IpAddress;
use FPanel\Models\Serviceplans;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use FPanel\System\Resource as ResourceEdit;

/**
 * Class Domaindata
 * @package FPanel\Domaindata
 */
class DomainSettings extends Library
{
    public $parent_id;
    public $parent_type;

    public function domainList($user_id = null, $user_type = null): array
    {
        $datatable = new Datatable();
        $delete_button = $datatable->buttons('domains/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m'),__('Delete'));

        $datatable->select(array(
            "domains.id",
            "domains.name",
            "users.username",
            "domains.created_date",
            "domains.expire_date",
            "ip_address.ip_address",
            "domains.status",
            "domains.status_msg",
            "users.id",
            "domain_statistics.disk", // 9
            "domain_statistics.traffic",
            "domain_statistics.disk_max",
            "domain_statistics.traffic_max", // 12
        ))
            ->from("users")
            ->innerJoin("domains","users.id = domains.client_id")
            ->innerJoin("ip_address","domains.ipaddr_id = ip_address.id")
            ->innerJoin('domain_statistics',"domain_statistics.domain_id = domains.id")
            ->where("1 = 1");

        if(($user_type > ADMIN_USER) && ($user_type < SUPPORT_USER)) {
            $datatable->andwhere("users.id = '$user_id'")
                ->orWhere("users.parent = '$user_id'");
        }

        $data = $datatable->searchColumn(array("domains.name", "users.username"))
            ->addColumn('actions', $delete_button,"domains.id")
            ->unsetColumns("users.id")
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $error_messages = str_replace('Success','', $value[7]);
            $error_messages = str_replace('\n','', $error_messages);
            $error_messages = str_replace(PHP_EOL,'', $error_messages);

            // Statusu aktif yani 1 ise
            if($value[6] == ACTIVE) {
                $data['aaData'][$key][1] = '<span class="show-image-popup" id="' . $value[0] . '" style="display:none;"><img width="250" src="/' .$this->uri->url_encrypt('ss/'. $value[0], true).'" /></span><a data-id="data-show-image" data-target="#' . $value[0] .'" href="'. $this->uri->url_encrypt('dashboard/domain/'. $value[0]) .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m">'.  $value[1] .'</a>';
                $data['aaData'][$key][6] = ' <span class="label label-success" data-type="success" data-toggle="tooltip" title="'. __('Successfuly Created').'"> '. __('Active') .' </span>';
                $suspend_btn = '<a data-id="confirm" data-url="'. $this->uri->url_encrypt('domains/suspend/'. $value[0].'/suspend') .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m"><i class="fas fa-redo"></i> ' . __('Suspend') . '</a>';
                $btn_edit = '<a data-id="nested" href="'. $this->uri->url_encrypt('domains/edit/'. $value[0]) .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m"><i class="fa fa-edit"></i> '. __('Edit') .'</a>';
                $btn_dashboard = '<a data-id="nested" href="'. $this->uri->url_encrypt('dashboard/domain/'. $value[0]) .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m"><i class="fas fa-desktop"></i> '. __('Dashboard') .'</a>';
            }
            // Statusu Hatalı yani 6 ise
            else if($value[6] == ERROR_STATUS) {
                $data['aaData'][$key][1] = ' <a href="#" class="">'. $value[1] .'</a>';
                $data['aaData'][$key][6] = ' <span id="warning_error" data-toggle="tooltip" class="label label-warning tooltips" data-type="error" title="'. $error_messages .'"> '. __('Error') .' </span>';
                $suspend_btn = "";
                $btn_edit = "";
                $btn_dashboard = "";
            }
            // Statusu farklı yani pasif ise
            else {
                $data['aaData'][$key][1] = '<a href="'. $this->uri->url_encrypt('dashboard/domain/'. $value[0]) .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m">'. $value[1] .'</a>';
                $data['aaData'][$key][6] = ' <span class="label label-danger" data-toggle="tooltip" data-type="passive" title="'. $value[7] .'"> '. __('Passive') .' </span>';
                $suspend_btn = '<a data-id="confirm" data-url="'. $this->uri->url_encrypt('domains/suspend/'. $value[0].'/unsuspend') .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m"><i class="fas fa-retweet"></i> ' . __('Unsuspend') . '</a>';
                $btn_edit = '<a data-id="nested" href="'. $this->uri->url_encrypt('domains/edit/'. $value[0]) .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m"><i class="fa fa-edit"></i> '. __('Edit') .'</a>';
                $btn_dashboard = '<a data-id="nested" href="'. $this->uri->url_encrypt('dashboard/domain/'. $value[0]) .'" class="btn btn-default btn-xs btn-sm-block btn-xs-block btn-w-m"><i class="fas fa-desktop"></i> '. __('Dashboard') .'</a>';
            }

            $data['aaData'][$key][2] = $value[2];
            $data['aaData'][$key][5] = $value[5];

            if($value[3] == UNLIMITED) {
                $data['aaData'][$key][3] = __('Unlimited');
            }
            else {
                $data['aaData'][$key][3] = $this->date->getOnlyDate($value[3], "d/m/Y");
            }
            if($value[4] == UNLIMITED) {
                $data['aaData'][$key][4] = __('Unlimited');
            }
            else {
                $data['aaData'][$key][4] = $this->date->getOnlyDate($value[4], "d/m/Y");
            }

            $traffic_class = "progress-bar-success";
            $disk_class = "progress-bar-success";
            if($value[11] != UNLIMITED) {
                $disk_percent = $value[9] == 0 ? 0 : ($value[9] / $value[11])*100;
                $disk_percent = $disk_percent == 0 ? 0 : round($disk_percent);
                if($disk_percent >= 100) {
                    $disk_class = "progress-bar-danger";
                }
            }
            else {
                $disk_percent = 0;
            }
            $disk_bar = ' <div class="project-completion"> <div class="progress progress-mini">
            <div style="width: '. $disk_percent .'%;" class="progress-bar '. $disk_class .'"></div>
            </div></div>';

            if($value[12] != UNLIMITED)  {
                $traffic_percent = $value[10] == 0 ? 0 : ($value[10] / $value[12])*100;
                $traffic_percent = $traffic_percent == 0 ? 0 : round($traffic_percent);
                if($traffic_percent >= 100) {
                    $traffic_class = "progress-bar-danger";
                }
            }
            else {
                $traffic_percent = 0;
            }
            $traffic_bar = ' <div class="project-completion"> <div class="progress progress-mini">
            <div style="width: '. $traffic_percent.'%;" class="progress-bar '. $traffic_class .'"></div>
            </div></div>';
            $data['aaData'][$key][7] = $this->env->convert($value[9]). $disk_bar;
            $data['aaData'][$key][8] = $this->env->convert($value[10]) . $traffic_bar;
            $data['aaData'][$key][9] = "$btn_edit $btn_dashboard $suspend_btn $value[13]";

            $data['aaData'][$key][0] = '<input class="checkbox-blue" type="checkbox" value="' . $value[0] . '" name="domains[]" />';

        }
        return $data;
    }

    public function create($date)
    {
        $created_ftp_user_id = $this->createFTP();
        if ($created_ftp_user_id !== false) {
            return $this->createDomain($created_ftp_user_id, $date);
        }
        else {
            return $created_ftp_user_id;
        }
    }

    private function createFTP()
    {
        $ftp_user = new FtpUsers();
        $password = $this->crypto->bencode($this->getPost("password"));

        $ftp_user->assign(array(
            // FTP User Data
            "id"        => $this->env->createUuid(),
            'username'   => $this->getPost("ftpname"),
            'password'  => $password
        ));

        if ($ftp_user->create()) {
            return $ftp_user->id;
        }
        else {
            foreach ($ftp_user->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DomainSettings->createFTP",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
    }

    private function createDomain($ftp_user_id, $date)
    {
        $domain = new Domains();
        $domain_name = $this->getPost("name");
        $ip_addr_id = $this->getPost("ipaddr_id");
        if($this->getPost("system_type") == STANDALONE) {
            $system_ip = IpAddress::findFirstById($ip_addr_id);
            $web_server_id = $system_ip->server_id;
            $dns_server_id = $system_ip->server_id;
            $mail_server_id = $system_ip->server_id;
        }
        else {
            $web_server_id = $this->getPost("web_servers");
            $dns_server_id = $this->getPost("dns_servers");
            $mail_server_id = $this->getPost("mail_servers");
        }

        $domain->assign(array(
            // Domain Datas
            "id" => $this->env->createUuid(),
            'name' => $domain_name,
            'client_id' => $this->parent_id,
            'ssl_cert_id' => null,
            'ftp_user_id' => $ftp_user_id,
            'ipaddr_id' => $ip_addr_id,
            'created_date' => $this->date->tsToDate($date['created_date']),
            'expire_date' => $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date']),
            'status' => '1',
            'prefix' => $this->env->createPrefix(),
            'web_server_id' => $web_server_id,
            'dns_server_id' => $dns_server_id,
            'mail_server_id' => $mail_server_id,
        ));

        if ($domain->create()) {
            return $domain->id;
        }
        else {
            foreach ($domain->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "DomainSettings->createDomain",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
    }

    public function edit($domain_id, $api = false): bool
    {
        if(!$api) {
            if(!$this->editValidation())
                return false;
        }

        $domains = Domains::findFirstById($domain_id);
        if($domains) {
            $users = Users::findFirst("username = '$domains->name'");
            if($users) {
                $user_data = Userdata::findFirst("user_id = '$users->id'");
                if($user_data) {
                    $current_parent = $domains->client_id;
                    if($this->has("password"))
                        $password = $this->getPost("password");
                    else
                        $password = null;

                    if($password != null) {
                        $users->password = $this->security->hash($password);
                    }
                    if(!$api) {
                        $users->parent = $this->crypto->bdecode($this->getPost("parent"));
                    }

                    if(!$users->update()) {
                        foreach($users->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "DomainSettings->edit->Users",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }

                    if($this->has("fullname")) {
                        $user_data->fullname = $this->getPost("fullname");
                    }
                    if($this->has("telephone")) {
                        $user_data->telephone = $this->getPost("telephone");
                    }
                    if($this->has("fax")) {
                        $user_data->fax = $this->getPost("fax");
                    }
                    if($this->has("company")) {
                        $user_data->company = $this->getPost("company");
                    }
                    if($this->has("email")) {
                        $user_data->email = $this->getPost("email");
                    }
                    if($this->has("address")) {
                        $user_data->address = $this->getPost("address");
                    }
                    if($this->has("city")) {
                        $user_data->city = $this->getPost("city");
                    }
                    if($this->has("region")) {
                        $user_data->region = $this->getPost("region");
                    }
                    if($this->has("country")) {
                        $user_data->country = $this->getPost("country");
                    }

                    if(!$user_data->update()) {
                        foreach($user_data->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "DomainSettings->edit->Userdata",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    else {
                        if(!$api) {
                            $domains->client_id = $this->crypto->bdecode($this->getPost("parent"));
                        }
                        else {
                            $domains->client_id = $current_parent;
                        }

                        if(!$domains->update()) {
                            foreach($domains->getMessages() as $message) {
                                $this->error_messages[$message->getField()] = $message->getMessage();
                            }
                            $this->log->error([
                                "Module" => "DomainSettings->edit->Domains",
                                "data" => $this->getPost(),
                                "errors" => $this->error_messages,
                            ]);
                            return false;
                        }
                        return true;
                    }
                }
                $this->error_messages['content'] = __('Userdata not found');
                return false;
            }
            $this->error_messages['content'] = __('User not found');
            return false;
        }
        $this->error_messages['content'] = __('Domain not found');
        return false;
    }

    public function delete($id): bool
    {
        $domain = $this->findDomainData($id);
        if ($domain) {
            $status = true;
            if(!$this->deleteDomain($domain->id)) {
                $status = false;
            }
            if(!$this->deleteFtpUser($domain->ftp_user_id)) {
                $status = false;
            }
            return $status;
        }
        else {
            return false;
        }
    }

    private function findDomainData($id)
    {
        $domains = Domains::findFirstById($id);
        if ($domains) {
            return $domains;
        }
        else {
            return false;
        }
    }

    private function deleteDomain($id): bool
    {
        $domains = Domains::findFirstById($id);
        if ($domains) {
            if(!$domains->delete()) {
                foreach($domains->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainSettings->deleteDomain",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        return false;
    }

    private function deleteFtpUser($ftp_user_id): bool
    {
        $ftpusers = FtpUsers::findFirstById($ftp_user_id);
        if ($ftpusers) {
            if(!$ftpusers->delete()) {
                foreach($ftpusers->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainSettings->deleteFtpUser",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        return false;
    }

    public function suspend($domain_id, $parent_id, $message = null, int $status = PASSIVE): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }

        $parent = Users::findFirstById($parent_id);
        if($parent->type != ROOT_USER) {
            $domain = Domains::findFirst("id = '$domain_id' AND client_id = '$parent_id'");
        }
        else {
            $domain = Domains::findFirstById($domain_id);
        }

        if($domain) {
            $domain->status = $status;
            if($message != null)
                $domain->status_msg = $message;

            if(!$domain->update()) {
                foreach($domain->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainSettings->suspend Domains",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            $users = Users::findFirst("username = '$domain->name'");
            $users->status = $status;
            if($message != null)
                $users->status_msg = $message;
            if(!$users->update()) {
                foreach($users->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainSettings->suspend Users",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    public function unsuspend($domain_id, $parent_id, $message = null): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        $parent = Users::findFirstById($parent_id);
        if($parent->type != ROOT_USER) {
            $domain = Domains::findFirst("id = '$domain_id' AND client_id = '$parent_id'");
        }
        else {
            $domain = Domains::findFirstById($domain_id);
        }

        if($domain) {
            $domain->status = ACTIVE;
            if($message != null)
                $domain->status_msg = $message;

            if(!$domain->update()) {
                foreach($domain->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainSettings->unsuspend Domains",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }

            $users = Users::findFirst("username = '$domain->name'");
            $users->status = ACTIVE;
            if($message != null)
                $users->status_msg = $message;
            if(!$users->update()) {
                foreach($users->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "DomainSettings->unsuspend Users",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    public function changeParent($domain_id): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }

        if(!$this->changeParentValidation())
            return false;

        $domains = Domains::findFirstById($domain_id);
        $statistics = DomainStatistics::findFirst("domain_id = '$domain_id'");
        $users = Users::findFirst("username = '$domains->name'");
        if(($domains) && ($statistics) && ($users)) {
            $domains->assign(array(
                'client_id' => $this->getPost("parent"),
            ));
            if(!$domains->update()) {
                foreach ($domains->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "DomainSettings->changeParent Domains",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            } else {
                $statistics->assign(array(
                    'client_id' => $this->getPost("parent"),
                ));

                if(!$statistics->update()) {
                    foreach ($statistics->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "DomainSettings->changeParent DomainStatistics",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                } else {
                    $users->assign(array(
                        'parent' => $this->getPost("parent"),
                    ));
                    if (!$users->update()) {
                        foreach ($users->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->log->error([
                            "Module" => "DomainSettings->changeParent Users",
                            "data" => $this->getPost(),
                            "errors" => $this->error_messages,
                        ]);
                        return false;
                    }
                    return true;
                }
            }
        }
        else
            return false;
    }

    public function changeLimits($domain_id, $serviceplan_id): bool
    {
        if(!$this->changeLimitValidation($domain_id, $serviceplan_id))
            return false;

        $serviceplans = Serviceplans::findFirstById($serviceplan_id);
        $template = $serviceplans->toArray();
        $domains = Domains::findFirstById($domain_id);

        if(is_numeric($template['disk_limit'])) {
            $disk_limit = abs($template['disk_limit']/1024/1024);
        }
        else {
            $disk_limit = UNLIMITED;
        }
        if(is_numeric($template['traffic_limit'])) {
            $traffic_limit = abs($template['traffic_limit']/1024/1024);
        }
        else {
            $traffic_limit = UNLIMITED;
        }
        if(is_numeric($template['email_box_size_limit'])) {
            $email_box_size_limit = abs($template['email_box_size_limit']/1024/1024);
        }
        else {
            $email_box_size_limit = UNLIMITED;
        }
        if(is_numeric($template['database_size_limit'])) {
            $database_size_limit = abs($template['database_size_limit']/1024/1024);
        }
        else {
            $database_size_limit = UNLIMITED;
        }
        $post_data = array(
            'created_date' => $domains->created_date,
            'domain_id' => $domain_id,
            'client_limit'    => '0',
            'domain_limit'    => '0',
            'template_limit' => '0',
            'disk_limit'  => $disk_limit,
            'traffic_limit'   => $traffic_limit,
            'subdomain_limit' => $template['subdomain_limit'],
            'domain_alias_limit'  => $template['domain_alias_limit'],
            'email_box_limit' => $template['email_box_limit'],
            'email_box_size_limit'    => $email_box_size_limit,
            'email_forward_limit' => $template['email_forward_limit'],
            'email_groups_limit'  => $template['email_groups_limit'],
            'email_autoresponder_limit'   => $template['email_autoresponder_limit'],
            'email_lists_limit'    => $template['email_lists_limit'],
            'database_limit'  => $template['database_limit'],
            'database_size_limit' => $database_size_limit,
            'expire_time_limit' => $template['expire_time_limit'],
            'overuse' => ($template['overuse'] == ACTIVE ? ACTIVE : PASSIVE),
            // Permission
            'man_client' => NOT_ALLOWED,
            'man_domain' => NOT_ALLOWED,
            'man_template'   => NOT_ALLOWED,
            'access_panel'  => $template['access_panel'],
            'man_hosting_settings'   => $template['man_hosting_settings'],
            'man_subdomain'  => $template['man_subdomain'],
            'man_database'   => $template['man_database'],
            'man_domain_alias'   => $template['man_domain_alias'],
            'man_email'  => $template['man_email'],
            'man_email_alias'    => $template['man_email_alias'],
            'man_mailing_lists'  => $template['man_mailing_lists'],
            'man_spam_filters'   => $template['man_spam_filters'],
            'man_task'   => $template['man_task'],
            'man_dns'    => $template['man_dns'],
            'man_ssl'    => $template['man_ssl'],
            'man_backup'   => $template['man_backup'],
            'man_protect_dir'    => $template['man_protect_dir'],
        );

        $resource = new ResourceEdit();
        $resource->setPosts($post_data);
        $status = $resource->updateDomain();

        if(!$status) {
            $this->error_messages = $resource->getMessages();
            return false;
        }
        else {
            return true;
        }
    }

    private function changeLimitValidation($domain_id, $template_id): bool
    {
        $user_id = $this->getPost("user_id");
        $user_type = $this->getPost("user_type");

        $qbuilder = new QueryBuilder();
        if(($user_type != ROOT_USER) && ($user_type != ADMIN_USER)) {
            $user_domains = $qbuilder->select([
                "domains.id"
            ],true)
                ->from('users')
                ->innerJoin('users as clients',"clients.parent = users.id")
                ->innerJoin("domains","users.id = domains.client_id OR clients.id = domains.client_id")
                ->where([
                    'conditions' => "users.id = ?1",
                    'bind' => [1 => $user_id]
                ])
                ->find();

            $status = false;
            if(!empty($user_domains)) {
                foreach($user_domains as $domain) {
                    if($domain['id'] == $domain_id) {
                        $status = true;
                    }
                }
            }
            if(!$status) {
                $this->error_messages['content'] = __('You don\' have permission for this domain');
                return false;
            }
        }

        $user_templates = $qbuilder->select([
            "serviceplans.id",
        ],true)
            ->from('users')
            ->innerJoin('serviceplans',"serviceplans.client_id = users.id")
            ->where([
                'conditions' => "users.id = ?1",
                'bind' => [1 => $user_id]
            ])
            ->find();

        $status = false;
        if(!empty($user_templates)) {
            foreach($user_templates as $sp) {
                if($sp['id'] == $template_id) {
                    $status = true;
                }
            }
        }
        if(!$status) {
            $this->error_messages['content'] = __('You don\' have permission');
            return false;
        }
        return true;
    }

    private function changeParentValidation(): bool
    {
        $required = array(
            array(
                'field' => 'content',
                'item' => $this->getPost("parent"),
                'message' => __('Please select a client')
            ),
        );
        $this->validation->Required($required);
        if(!$this->validation->Validate()) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        else {
            $domain_id = $this->getPost("domain_id");
            $user_id = $this->getPost("user_id");
            $user_type = $this->getPost("user_type");
            $parent_id = $this->getPost("parent");

            if(($user_type != ROOT_USER) && ($user_type != ADMIN_USER)) {
                $qbuilder = new QueryBuilder();
                $user_domains = $qbuilder->select([
                    "domains.id",
                ],true)
                    ->from('users')
                    ->innerJoin('users as clients',"clients.parent = users.id")
                    ->innerJoin("domains","users.id = domains.client_id OR clients.id = domains.client_id")
                    ->where([
                        'conditions' => "users.id = ?1",
                        'bind' => [1 => $user_id]
                    ])
                    ->find();

                $status = false;
                if(!empty($user_domains)) {
                    foreach($user_domains as $domain) {
                        if($domain['id'] == $domain_id) {
                            $status = true;
                        }
                    }
                }
                if(!$status) {
                    $this->error_messages['content'] = __('You don\' have permission for this domain');
                    return false;
                }
                else {
                    $parent_lists = $qbuilder->select([
                        "clients.id",
                    ])
                        ->from('users')
                        ->innerJoin('users as clients','clients.parent = users.id')
                        ->where([
                            'conditions' => "users.id = ?1",
                            'bind' => [1 => $user_id]
                        ])
                        ->andWhere([
                            'conditions' => "clients.type != ?1",
                            'bind' => [1 => DOMAIN_USER]
                        ])
                        ->find();
                    $status_parent = false;
                    if(!empty($parent_lists)) {
                        foreach($parent_lists as $parent) {
                            if($parent['id'] == $parent_id) {
                                $status_parent = true;
                            }
                        }
                    }
                    if(!$status_parent) {
                        $this->error_messages['content'] = __('You don\' have permission for this client');
                        return false;
                    }
                }
            }
            return true;
        }
    }

    private function editValidation(): bool
    {
        if($this->getPost("password") != null) {
            $this->validation->Password($this->getPost("password"),'password');
            if(!$this->validation->Validate()) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }
        }
        return true;
    }

    public function getDomainData($domain_id)
    {
        $qbuilder = new QueryBuilder();
        $result = $qbuilder->select([
            "domains.id",
            "domains.name",
            "domains.client_id",
            "domains.ssl_cert_id",
            "domains.ipaddr_id",
            "domains.status",
            "domains.status_msg",
            "domains.created_date",
            "domains.expire_date",
            "web_server.name as web_server_name",
            "dns_server.name as dns_server_name",
            "mail_server.name as mail_server_name",
            "domains.prefix",
            "domains.params",
            "(select count(s.id) from subdomains as s where s.domain_id = domains.id) AS subdomain_count",
            "(select count(da.id) from domain_alias as da where da.domain_id = domains.id) AS domain_alias_count",
            "(select count(m.id) from mails as m where m.domain_id = domains.id) AS mail_count",
            "(select count(db.id) from `databases` as db where db.domain_id = domains.id) AS database_count",
            "ftp_users.username as ftpname",
            "ftp_users.password as ftp_password",
            "ip_address.ip_address",
            "perms.domain_id",
            "perms.client_id",
            "perms.client_limit",
            "perms.domain_limit",
            "perms.disk_limit",
            "perms.traffic_limit",
            "perms.subdomain_limit",
            "perms.domain_alias_limit",
            "perms.email_box_limit",
            "perms.email_box_size_limit",
            "perms.email_forward_limit",
            "perms.email_groups_limit",
            "perms.email_autoresponder_limit",
            "perms.email_lists_limit",
            "perms.database_limit",
            "perms.database_size_limit",
            "perms.template_limit",
            "perms.access_panel",
            "perms.man_client",
            "perms.man_domain",
            "perms.man_subdomain",
            "perms.man_domain_alias",
            "perms.man_email_alias",
            "perms.man_hosting_settings",
            "perms.man_task",
            "perms.man_dns",
            "perms.man_mailing_lists",
            "perms.man_spam_filters",
            "perms.man_backup",
            "perms.man_email",
            "perms.man_database",
            "perms.man_protect_dir",
            "perms.man_ssl",
            "perms.man_template",
            "perms.overuse",
            "perms.php",
            "perms.php_version",
            "perms.cgi",
            "perms.perl",
            "perms.python",
            "perms.fast_cgi",
            "perms.statistic_support",
            "perms.statistic_type",
            "perms.asp",
            "perms.aspnet_core",
            "perms.aspnet",
            "perms.aspnet_version",
            "perms.dedicated_pool",
            "perms.max_cpu",
            "perms.application_pool_mode",
            "perms.ssl",
        ], true)
            ->from("domains")
            ->innerJoin("client_limits_permissions as perms", "perms.domain_id = domains.id")
            ->innerJoin("ip_address", "domains.ipaddr_id = ip_address.id")
            ->innerJoin("ftp_users", "domains.ftp_user_id = ftp_users.id")
            ->innerJoin("servers as web_server", "web_server.id = domains.web_server_id")
            ->innerJoin("servers as dns_server", "dns_server.id = domains.dns_server_id")
            ->innerJoin("servers as mail_server", "mail_server.id = domains.mail_server_id")
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();

        $mail_asilases = $qbuilder->select([
            "count(mail_alias.id) as mail_alias_count",
        ], true)
            ->from("mails")
            ->innerJoin("mail_alias", "mail_alias.mail_id = mails.id")
            ->findFirst();

        $result["email_alias_count"] = $mail_asilases["mail_alias_count"];
        return $result;
    }
}