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
 */namespace FPanel\Validations;

use Exception;

trait Permissions
{
    public function Limits($limits): Validation
    {
        $error_messages = array();
        $general = array(null,ON, OFF);
        try {
            if($limits['client_limit'] != UNLIMITED) {
                if(!is_numeric($limits['client_limit'])) {
                    $error_messages['clients'] = __('Limit must be valid');
                }
            }
            if($limits['domain_limit'] != UNLIMITED) {
                if(!is_numeric($limits['domain_limit'])) {
                    $error_messages['domain'] = __('Limit must be valid');
                }
            }
            if($limits['disk_limit'] != UNLIMITED) {
                if(!is_numeric($limits['disk_limit'])) {
                    $error_messages['disk'] = __('Limit must be valid');
                }
            }
            if($limits['traffic_limit'] != UNLIMITED) {
                if(!is_numeric($limits['traffic_limit'])) {
                    $error_messages['traffic'] = __('Limit must be valid');
                }
            }
            if($limits['subdomain_limit'] != UNLIMITED) {
                if(!is_numeric($limits['subdomain_limit'])) {
                    $error_messages['subdomain'] = __('Limit must be valid');
                }
            }
            if($limits['domain_alias_limit'] != UNLIMITED) {
                if(!is_numeric($limits['domain_alias_limit'])) {
                    $error_messages['domain_alias'] = __('Limit must be valid');
                }
            }
            if($limits['email_box_limit'] != UNLIMITED) {
                if(!is_numeric($limits['email_box_limit'])) {
                    $error_messages['email_box'] = __('Limit must be valid');
                }
            }
            if($limits['email_box_size_limit'] != UNLIMITED) {
                if(!is_numeric($limits['email_box_size_limit'])) {
                    $error_messages['email_box_size'] = __('Limit must be valid');
                }
            }
            if($limits['email_forward_limit'] != UNLIMITED) {
                if(!is_numeric($limits['email_forward_limit'])) {
                    $error_messages['email_forward'] = __('Limit must be valid');
                }
            }
            if($limits['email_groups_limit'] != UNLIMITED) {
                if(!is_numeric($limits['email_groups_limit'])) {
                    $error_messages['email_groups'] = __('Limit must be valid');
                }
            }
            if($limits['email_autoresponder_limit'] != UNLIMITED) {
                if(!is_numeric($limits['email_autoresponder_limit'])) {
                    $error_messages['email_autoresponder'] = __('Limit must be valid');
                }
            }
            if($limits['email_lists_limit'] != UNLIMITED) {
                if(!is_numeric($limits['email_lists_limit'])) {
                    $error_messages['email_list'] = __('Limit must be valid');
                }
            }
            if($limits['database_limit'] != UNLIMITED) {
                if(!is_numeric($limits['database_limit'])) {
                    $error_messages['database'] = __('Limit must be valid');
                }
            }
            if($limits['database_size_limit'] != UNLIMITED) {
                if(!is_numeric($limits['database_size_limit'])) {
                    $error_messages['database_size'] = __('Limit must be valid');
                }
            }
            if($limits['expire_time_limit'] != UNLIMITED) {
                if(!is_numeric($limits['expire_time_limit'])) {
                    $error_messages['expire_time_limit'] = __('Limit must be valid');
                }
            }

            if(!in_array($limits['overuse'], $general)) {
                $error_messages['overuse'] = __('Value is must be valid');
            }

            if(!empty($error_messages)) {
                $this->error_status = true;
                foreach($error_messages as $key => $message) {
                    $this->message[$key] = $message;
                }
            }
        }
        catch (Exception $e) {
            $this->message["exception"] = $e->getMessage();
        }

        return $this;

    }

    public function Permissions($permissions): Validation
    {
        $error_messages = array();
        $general = array(null, ON, OFF);
        try {
            if(!in_array($permissions['man_client'], $general)) {
                $error_messages['access_client'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_domain'], $general)) {
                $error_messages['access_domain'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_template'], $general)) {
                $error_messages['access_template'] = __('Value is must be valid');
            }
            if(!in_array($permissions['access_panel'], $general)) {
                $error_messages['access_panel'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_hosting_settings'], $general)) {
                $error_messages['access_hosting_settings'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_subdomain'], $general)) {
                $error_messages['access_subdomain'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_database'], $general)) {
                $error_messages['access_database'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_domain_alias'], $general)) {
                $error_messages['access_domain_alias'] = __('Value is must be valid');
            }

            if(!in_array($permissions['man_email'], $general)) {
                $error_messages['access_email'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_email_alias'], $general)) {
                $error_messages['access_email_alias'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_mailing_lists'], $general)) {
                $error_messages['access_mailing_lists'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_spam_filters'], $general)) {
                $error_messages['access_spam_filters'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_task'], $general)) {
                $error_messages['access_task'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_dns'], $general)) {
                $error_messages['access_dns'] = __('Value is must be valid');
            }
            if(!in_array($permissions['man_ssl'], $general)) {
                $error_messages['access_ssl'] = __('Value is must be valid');
            }

            if(!in_array($permissions['man_backup'], $general)) {
                $error_messages['access_backup'] = __('Value is must be valid');
            }

            if(!in_array($permissions['man_protect_dir'], $general)) {
                $error_messages['access_protect_dir'] = __('Value is must be valid');
            }

            if(!empty($error_messages)) {
                $this->error_status = true;
                foreach($error_messages as $key => $message) {
                    $this->message[$key] = $message;
                }
            }
        }
        catch (Exception $e) {
            $this->message["exception"] = $e->getMessage();
        }
        return $this;
    }

    public function DomainSettings($domain_settings): Validation
    {
        $error_messages = array();
        $php_version = array(null, PHP56, PHP72, PHP74);
        $aspnet_version = array(null, v40, v20);
        $dom_web_statistics_type = array(NO_STATS, AWSTATS, WEBALIZER);
        $general = array(null, ON, OFF);
        try {
            if(!in_array($domain_settings['php_version'], $php_version)) {
                $error_messages['php_version'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['php'], $general)) {
                $error_messages['php_support'] = __('Value is must be valid');
            }

            if(!in_array($domain_settings['cgi'], $general)) {
                $error_messages['cgi_support'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['perl'], $general)) {
                $error_messages['perl_support'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['python'], $general)) {
                $error_messages['python_support'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['asp'], $general)) {
                $error_messages['asp_support'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['aspnet_core'], $general)) {
                $error_messages['asp_net_core_support'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['aspnet'], $general)) {
                $error_messages['asp_net_support'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['dedicated_pool'], $general)) {
                $error_messages['dedicated_pool'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['web_statistics'], $general)) {
                $error_messages['web_statistics_support'] = __('Value is must be valid');
            }
            if(isset($domain_settings['web_statistics_type']) && $domain_settings['web_statistics_type'] != null && !in_array($domain_settings['web_statistics_type'], $dom_web_statistics_type)) {
                $error_messages['web_statistics_type'] = __('Value is must be valid');
            }
            if(!in_array($domain_settings['aspnet_version'], $aspnet_version)) {
                $error_messages['asp_net_version'] = __('Value is must be valid');
            }
            if($domain_settings['max_cpu'] != null) {
                if(!is_numeric($domain_settings['max_cpu'])) {
                    $error_messages['max_cpu_usage'] = __('Value is must be valid');
                }
            }
            if($domain_settings['application_pool_mode'] != null) {
                if(!in_array($domain_settings['application_pool_mode'], [INTEGRATED, CLASSIC])) {
                    $error_messages['application_pool_mode'] = __('You must select valid mode');
                }
            }

            if(!empty($error_messages)) {
                $this->error_status = true;
                foreach($error_messages as $key => $message) {
                    $this->message[$key] = $message;
                }
            }
        }
        catch (Exception $e) {
            $this->message["exception"] = $e->getMessage();
        }

        return $this;
    }

}