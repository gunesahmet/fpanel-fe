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
 */namespace FPanel\Models;

use Phalcon\Mvc\Model;

/**
 * Class ClientLimitsPermissions
 * @package FPanel\Models
 * @method static ClientLimitsPermissions|Model\ResultsetInterface findFirst($params = null)
 * @method static ClientLimitsPermissions|Model\ResultsetInterface findFirstById($params = null)
 * @method static ClientLimitsPermissions[]|Model\ResultsetInterface find($params = null)
 */
class ClientLimitsPermissions extends FModel
{
    /**
     * @Primary
     * @var int
     */
    public $id;

    public $domain_id;

    public $client_id;

    public $client_limit;

    public $domain_limit;

    public $disk_limit;

    public $traffic_limit;

    public $subdomain_limit;

    public $domain_alias_limit;

    public $email_box_limit;

    public $email_box_size_limit;

    public $email_forward_limit;

    public $email_groups_limit;

    public $email_autoresponder_limit;

    public $email_lists_limit;

    public $database_limit;

    public $database_size_limit;

    public $template_limit;

    public $access_panel;

    public $man_client;

    public $man_domain;

    public $man_subdomain;

    public $man_domain_alias;

    public $man_email_alias;

    public $man_hosting_settings;

    public $man_task;

    public $man_dns;

    public $man_mailing_lists;

    public $man_spam_filters;

    public $man_backup;

    public $man_email;

    public $man_database;

    public $man_protect_dir;

    public $man_ssl;

    public $man_template;

    public $overuse;

    public $php;

    public $php_version;

    public $cgi;

    public $perl;

    public $python;

    public $fast_cgi;

    public $statistic_support;

    public $statistic_type;

    public $asp;

    public $aspnet;

    public $aspnet_core;

    public $aspnet_version;

    public $dedicated_pool;

    public $max_cpu;

    public $application_pool_mode;

    public $ssl;

    public $created_date;

    public $updated_date;

	public function initialize()
	{
		$this->setSource('client_limits_permissions');
	}

    /**
     * @param $data
     * @param null $params
     * @return bool
     */
    public function updateDomainData($data, $params = null): bool
    {
        $post_data = array(
            // Sadece parentlari güncelleyebilir
            'php_version',
            'fast_cgi',
            'python',
            'perl',
            'cgi',
            'asp',
            'aspnet_core',
            'aspnet',
            'aspnet_version',
            'php',
            'statistic_support' ,
            'statistic_type',
            'dedicated_pool',
            'max_cpu',
            'application_pool_mode',
        );
        $domain_id = $data['domain_id'];
        $dom_ssl = "ssl = '".$data['ssl']."'";

        $domain_settings = null;

        if($data['nested'] == "true") {
            foreach ($post_data as $post_item) {
                if($domain_settings == null) {
                    $domain_settings = "$post_item = '".$data[$post_item]."'";
                }
                else {
                    $domain_settings = $domain_settings.","."$post_item = '".$data[$post_item]."'";
                }
            }
        }

        if($domain_settings == null)
            $sql = "UPDATE client_limits_permissions SET ". $dom_ssl ." WHERE domain_id='$domain_id' ";
        else
            $sql = "UPDATE client_limits_permissions SET ". $domain_settings ."," . $dom_ssl ." WHERE domain_id='$domain_id' ";


        $result = new ClientLimitsPermissions();
        $result->getReadConnection()->query($sql, $params);
        return true;
    }
}
