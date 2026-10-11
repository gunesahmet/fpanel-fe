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
 */namespace FPanel\Api\Client;

use FPanel\Components\Components;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\ClientIpAddress;
use FPanel\Models\IpAddress;
use FPanel\Models\Serviceplans;
use FPanel\Models\Users;
use Phalcon\Mvc\User\Component;

class Client extends Component
{
    public function clientCreate($params, $client_id)
    {
        $error_messages = array();
        $template_name = $params['template_name'];
        $serviceplans = Serviceplans::findFirst("name = '$template_name' AND client_id = '$client_id'");
        if(!$serviceplans) {
            // Serviceplan yok
            return __("Serviceplan doesn't exists");
        }

        $template_data = $serviceplans->toArray();

        $client = Users::findFirstById($client_id);
        if($client) {
            $client_type = $client->type;
        }
        else {
            return $error_messages['username'] = "This user doesn't exist on system.";
        }

        $ip_addr_list = array();
        if(!empty($params["ip_address"])) {

            $ip_list = explode(',', $params['ip_address']);

            foreach($ip_list as $ipaddr) {
                $system_ip_address = IpAddress::findFirst("ip_address = '$ipaddr'");
                if(($client_type == ROOT_USER) || ($client_type == ADMIN_USER)) {
                    if($system_ip_address)
                        $ip_addr_list[] = $system_ip_address->id;
                    else
                        return $error_messages['ip_address'] = "This ip doesn't exist on system";
                }
                else {
                    if($system_ip_address) {
                        $ip_addr_id = $system_ip_address->id;
                        $client_ips = ClientIpAddress::findFirst("client_id = '$client_id' AND ipaddr_id = '$ip_addr_id'");
                        if(!$client_ips) {
                            return $error_messages['ip_address'] = "This ip doesn't exist on system";
                        }
                        $ip_addr_list[] = $ip_addr_id;
                    }
                    else {
                        return $error_messages['ip_address'] = "This ip doesn't exist on system";
                    }
                }
            }
        }
        else {
            $system_ip_address = IpAddress::findFirst("is_default = '1'");
            $ip_addr_list[] = $system_ip_address->id;
        }

        switch($params['client_type']) {
            case "reseller":
                $type_of_user = RESELLER_USER;
                break;
            case "client":
                $type_of_user = CLIENT_USER;
                break;
            default:
                return $error_messages['client_type'] = "Please enter valid client type";
        }

        $post_data = array(
            // Permissionda Client için özel kural devreye alınacak
            'client_limit'    => '0',
            'domain_limit'    => '0',
            'template_limit' => '0',
            'disk_limit'  => $template_data['disk_limit'],
            'traffic_limit'   => $template_data['traffic_limit'],
            'subdomain_limit' => $template_data['subdomain_limit'],
            'domain_alias_limit'  => $template_data['domain_alias_limit'],
            'email_box_limit' => $template_data['email_box_limit'],
            'email_box_size_limit'    => $template_data['email_box_size_limit'],
            'email_forward_limit' => $template_data['email_forward_limit'],
            'email_groups_limit'  => $template_data['email_groups_limit'],
            'email_autoresponder_limit'   => $template_data['email_autoresponder_limit'],
            'email_lists_limit'    => $template_data['email_lists_limit'],
            'database_limit'  => $template_data['database_limit'],
            'database_size_limit' => $template_data['database_size_limit'],
            'expire_time_limit' => $template_data['expire_time_limit'],
            'overuse' => $template_data['overuse'],

            // Permission
            'man_client' => NOT_ALLOWED,
            'man_domain' => NOT_ALLOWED,
            'man_template'   => NOT_ALLOWED,
            'access_panel'  => $template_data['access_panel'],
            'man_hosting_settings'   => $template_data['man_hosting_settings'],
            'man_subdomain'  => $template_data['man_subdomain'],
            'man_database'   => $template_data['man_database'],
            'man_domain_alias'   => $template_data['man_domain_alias'],
            'man_email'  => $template_data['man_email'],
            'man_email_alias'    => $template_data['man_email_alias'],
            'man_mailing_lists'  => $template_data['man_mailing_lists'],
            'man_spam_filters'   => $template_data['man_spam_filters'],
            'man_task'   => $template_data['man_task'],
            'man_dns'    => $template_data['man_dns'],
            'man_ssl'    => $template_data['man_ssl'],
            'man_backup'   => $template_data['man_backup'],
            'man_protect_dir'    => $template_data['man_protect_dir'],

            // Domain Settings
            'php'  => $template_data['php'],
            'php_version'  => $template_data['php_version'],
            'cgi'  => $template_data['cgi'],
            'perl'  => $template_data['perl'],
            'python'  => $template_data['python'],
            'fast_cgi'  => PASSIVE,
            'web_statistics'  => $template_data['web_statistics'],
            'web_statistics_type'  => $template_data['web_statistics_type'],
            'asp'  => $template_data['asp'],
            'aspnet_core'  => $template_data['aspnet_core'],
            'aspnet'  => $template_data['aspnet'],
            'aspnet_version'  => $template_data['aspnet_version'],
            'dedicated_pool'  => $template_data['dedicated_pool'],
            'max_cpu'  => $template_data['max_cpu'],
            'application_pool_mode'  => $template_data['application_pool_mode'],
            'ssl'  => PASSIVE,

            // User Info
            'username'   => $params['client_name'],
            'type'   => $type_of_user,
            'password'   => $params['client_password'],
            'fullname'   => $params['client_name'],
            'email'   => $params['client_email'],
            'company' => null,
            'telephone' => null,
            'address' => null,
            'city' => null,
            'zipcode' => null,
            'country' => null,
            // Ip adressleri
            'ipaddr_id'    => $ip_addr_list,
        );

        $user_components = new Components();

        $user_status = $user_components->userData(array(
            'user_id' => $client_id,
            'user_type' => $client_type,
        ))
            ->options(array())
            ->post($post_data,false,true)
            ->create(USER);

        if($user_status == false) {
            foreach ($user_components->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }

    public function clientUpdate($params, $parent_id)
    {
        $error_messages = array();
        $template_name = $params['template_name'];
        $serviceplans = Serviceplans::findFirst("name = '$template_name' AND client_id = '$parent_id'");
        if(!$serviceplans) {
            // Serviceplan yok
            return __("Serviceplan not found");
        }

        $template_data = $serviceplans->toArray();

        if(!array_key_exists("id", $params)) {
            return $error_messages['id'] = "Client not found.";
        }
        $user = Users::findFirstById($params["id"]);
        if(!$user) {
            return $error_messages['id'] = "Client not found.";
        }

        $parent = Users::findFirstById($user->parent);

        $ip_addr_list = array();
        if(!empty($params["ip_address"])) {
            $ip_list = explode(',', $params['ip_address']);
            foreach($ip_list as $ipaddr) {
                $system_ip_address = IpAddress::findFirst("ip_address = '$ipaddr'");
                if(($user->type == ROOT_USER) || ($user->type == ADMIN_USER)) {
                    if($system_ip_address)
                        $ip_addr_list[] = $system_ip_address->id;
                    else
                        return $error_messages['ip_address'] = "This ip doesn't exist on system";
                }
                else {
                    if($system_ip_address) {
                        $ip_addr_id = $system_ip_address->id;
                        $client_ips = ClientIpAddress::findFirst("client_id = '$user->id' AND ipaddr_id = '$ip_addr_id'");
                        if(!$client_ips) {
                            return $error_messages['ip_address'] = "This ip doesn't exist on system";
                        }
                        $ip_addr_list[] = $ip_addr_id;
                    }
                    else {
                        return $error_messages['ip_address'] = "This ip doesn't exist on system";
                    }
                }
            }
        }
        else {
            $system_ip_address = IpAddress::findFirst("is_default = '1'");
            $ip_addr_list[] = $system_ip_address->id;
        }

        switch($params['client_type']) {
            case RESELLER:
                $type_of_user = RESELLER_USER;
                break;
            case CLIENT:
                $type_of_user = CLIENT_USER;
                break;
            default:
                return $error_messages['client_type'] = "Please enter valid client type";
        }

        $post_data = array(
            // Permissionda Client için özel kural devreye alınacak
            'client_limit'    => '0',
            'domain_limit'    => '0',
            'template_limit' => '0',
            'disk_limit'  => $template_data['disk_limit'],
            'traffic_limit'   => $template_data['traffic_limit'],
            'subdomain_limit' => $template_data['subdomain_limit'],
            'domain_alias_limit'  => $template_data['domain_alias_limit'],
            'email_box_limit' => $template_data['email_box_limit'],
            'email_box_size_limit'    => $template_data['email_box_size_limit'],
            'email_forward_limit' => $template_data['email_forward_limit'],
            'email_groups_limit'  => $template_data['email_groups_limit'],
            'email_autoresponder_limit'   => $template_data['email_autoresponder_limit'],
            'email_lists_limit'    => $template_data['email_lists_limit'],
            'database_limit'  => $template_data['database_limit'],
            'database_size_limit' => $template_data['database_size_limit'],
            'expire_time_limit' => $template_data['expire_time_limit'],
            'overuse' => $template_data['overuse'],

            // Permission
            'man_client' => NOT_ALLOWED,
            'man_domain' => NOT_ALLOWED,
            'man_template'   => NOT_ALLOWED,
            'access_panel'  => $template_data['access_panel'],
            'man_hosting_settings'   => $template_data['man_hosting_settings'],
            'man_subdomain'  => $template_data['man_subdomain'],
            'man_database'   => $template_data['man_database'],
            'man_domain_alias'   => $template_data['man_domain_alias'],
            'man_email'  => $template_data['man_email'],
            'man_email_alias'    => $template_data['man_email_alias'],
            'man_mailing_lists'  => $template_data['man_mailing_lists'],
            'man_spam_filters'   => $template_data['man_spam_filters'],
            'man_task'   => $template_data['man_task'],
            'man_dns'    => $template_data['man_dns'],
            'man_ssl'    => $template_data['man_ssl'],
            'man_backup'   => $template_data['man_backup'],
            'man_protect_dir'    => $template_data['man_protect_dir'],

            // Domain Settings
            'php'  => $template_data['php'],
            'php_version'  => $template_data['php_version'],
            'cgi'  => $template_data['cgi'],
            'perl'  => $template_data['perl'],
            'python'  => $template_data['python'],
            'fast_cgi'  => PASSIVE,
            'web_statistics'  => $template_data['web_statistics'],
            'web_statistics_type'  => $template_data['web_statistics_type'],
            'asp'  => $template_data['asp'],
            'aspnet_core'  => $template_data['aspnet_core'],
            'aspnet'  => $template_data['aspnet'],
            'aspnet_version'  => $template_data['aspnet_version'],
            'dedicated_pool'  => $template_data['dedicated_pool'],
            'max_cpu'  => $template_data['max_cpu'],
            'application_pool_mode'  => $template_data['application_pool_mode'],
            'ssl'  => PASSIVE,

            // User Info
            'username'   => $params['client_name'],
            'type'   => $type_of_user,
            'password'   => $params['client_password'],
            'fullname'   => $params['client_name'],
            'email'   => $params['client_email'],
            'company' => null,
            'telephone' => null,
            'address' => null,
            'city' => null,
            'zipcode' => null,
            'country' => null,
            // Ip adressleri
            'ipaddr_id'    => $ip_addr_list,
        );

        $user_components = new Components();

        $user_status = $user_components->userData(array(
            'user_id' => $parent_id,
            'user_type' => $parent->type,
        ))
            ->options(array())
            ->post($post_data,false,true)
            ->update(USER, $user->id);

        if(!$user_status) {
            foreach ($user_components->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        else {
            return "Success";
        }
    }

    public function clientDelete($params, $client_id): string
    {
        $error_messages = array();
        if(!array_key_exists("id", $params)) {
            return $error_messages['id'] = "Client not found.";
        }
        $users = Users::findFirstById($params["id"]);
        if(!$users) {
            return $error_messages['id'] = "Client not found.";
        }
        $user_id = $users->id;

        $client = Users::findFirstById($client_id);
        if($client) {
            $client_type = $client->type;
        }
        else {
            return $error_messages['username'] = "User not found.";
        }


        $user = new Components();
        $status = $user->userData(array(
            'user_id' => $client_id,
            'user_type' => $client_type,
        ))
            ->delete(USER,null, $user_id);

        if(!$status) {
            return $error_messages['client_name'] = "Client not found";
        }
        else {
            return "Success";
        }
    }

    public function clientList($params, $client_id)
    {
        $client = Users::findFirstById($client_id);
        if($client) {
            $client_type = $client->type;
        }
        else {
            return "This user doesn't exist on system.";
        }
        $qbuilder = new QueryBuilder();

        $database_name = DATABASE_NAME;
        $qbuilder->select([
            "$database_name.users.id",
            "$database_name.users.username",
            "$database_name.users.username",
            "parents.username as parent_name",
            "$database_name.users.type",
            "$database_name.users.created_date",
            "$database_name.users.expire_date",
            "$database_name.users.status_msg",
            "$database_name.users.status",
            "client_statistics.disk",
            "client_statistics.traffic",
        ])
            ->from("$database_name.users")
            ->innerJoin("$database_name.users AS parents","$database_name.users.parent = parents.id")
            ->innerJoin("client_statistics","$database_name.users.id = client_statistics.client_id")
            ->where([
                'conditions' => "$database_name.users.type = ?1",
                'bind' => [1 => RESELLER_USER]
            ])
            ->orWhere([
                'conditions' => "$database_name.users.type = ?1",
                'bind' => [1 => CLIENT_USER]
            ]);

        if(($client_type == ROOT_USER) || ($client_type == ADMIN_USER)) {
            $qbuilder->orWhere([
                    'conditions' => "$database_name.users.parent = ?1",
                    'bind' => [1 => $client_id]
                ]);
        }
        $client_list = $qbuilder->find();

        foreach ($client_list as $k => $v) {
            $client_list[$k]["type"] = USER_TYPES[$v["type"]];
            $client_list[$k]["status"] = STATUSES[$v["status"]];
        }
        $client_list['status'] = "1";
        return $client_list;
    }
}
