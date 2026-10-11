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
 */namespace FPanel\Api\Domain;

use FPanel\Domain\DomainSettings;
use FPanel\Exceptions\Exception;
use FPanel\Models\Domains;
use Phalcon\Mvc\User\Component;

class Resource extends Component
{
    public function show($params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is requeired"];
            }
        }
        try {
            $error_messages = array();
            $domain_name = $params['domain_name'];
            if(!$this->env->checkDomainOwner($domain_name, $client_id)) {
                return $error_messages['domain_name'] = __("You dont have access for this domain");
            }
            $domains = Domains::findFirst("name = '$domain_name'");
            if(!$domains) {
                return $error_messages['id'] = "Domain not found.";
            }
            $domain = new DomainSettings();
            $domain_data = $domain->getDomainData($domains->id);
            $list['status'] = "1";
            $list['data'] = $this->remakeData($domain_data);
            return $list;
        }
        catch (\Exception $e) {
            return ["exception" => $e->getMessage()];
        }
    }

    private function remakeData($domain_data)
    {
        try {
            unset($domain_data['client_id']);
            unset($domain_data['id']);
            unset($domain_data['ipaddr_id']);
            unset($domain_data['ftp_user_id']);
            unset($domain_data['ssl_cert_id']);
            unset($domain_data['server_id']);
            unset($domain_data['password']);
            unset($domain_data['domain_id']);
            unset($domain_data['client_limit']);
            unset($domain_data['domain_limit']);
            unset($domain_data['template_limit']);
            unset($domain_data['man_client']);
            unset($domain_data['man_domain']);
            unset($domain_data['man_template']);
            unset($domain_data['ftp_password']);


            if($domain_data['status'] == "1")
                $domain_data['status'] = "active";
            else
                $domain_data['status'] = "passive";

            if($domain_data['access_panel'] == ALLOWED) {
                $domain_data['access_panel'] = true;
            }
            else {
                $domain_data['access_panel'] = false;
            }
            unset($domain_data['access_panel']);

            if($domain_data['man_subdomain'] == ALLOWED) {
                unset($domain_data['man_subdomain']);
                $domain_data['manage_subdomain'] = true;
            }
            else {
                unset($domain_data['man_subdomain']);
                $domain_data['manage_subdomain'] = false;
            }

            if($domain_data['man_domain_alias'] == ALLOWED) {
                unset($domain_data['man_domain_alias']);
                $domain_data['manage_domain_alias'] = true;
            }
            else {
                unset($domain_data['man_domain_alias']);
                $domain_data['manage_domain_alias'] = false;
            }

            if($domain_data['man_email_alias'] == ALLOWED) {
                unset($domain_data['man_email_alias']);
                $domain_data['manage_email_alias'] = true;
            }
            else {
                unset($domain_data['man_email_alias']);
                $domain_data['manage_email_alias'] = false;
            }

            if($domain_data['man_hosting_settings'] == ALLOWED) {
                unset($domain_data['man_hosting_settings']);
                $domain_data['manage_hosting_settings'] = true;
            }
            else {
                unset($domain_data['man_hosting_settings']);
                $domain_data['manage_hosting_settings'] = false;
            }

            if($domain_data['man_task'] == ALLOWED) {
                unset($domain_data['man_task']);
                $domain_data['manage_task'] = true;
            }
            else {
                unset($domain_data['man_task']);
                $domain_data['manage_task'] = false;
            }

            if($domain_data['man_dns'] == ALLOWED) {
                unset($domain_data['man_dns']);
                $domain_data['manage_dns'] = true;
            }
            else {
                unset($domain_data['man_dns']);
                $domain_data['manage_dns'] = false;
            }

            if($domain_data['man_mailing_lists'] == ALLOWED) {
                unset($domain_data['man_mailing_lists']);
                $domain_data['manage_mailing_list'] = true;
            }
            else {
                unset($domain_data['man_mailing_lists']);
                $domain_data['manage_mailing_list'] = false;
            }

            if($domain_data['man_spam_filters'] == ALLOWED) {
                unset($domain_data['man_spam_filters']);
                $domain_data['manage_span_filters'] = true;
            }
            else {
                unset($domain_data['man_spam_filters']);
                $domain_data['manage_span_filters'] = false;
            }

            if($domain_data['man_backup'] == ALLOWED) {
                unset($domain_data['man_backup']);
                $domain_data['manage_backup'] = true;
            }
            else {
                unset($domain_data['man_backup']);
                $domain_data['manage_backup'] = false;
            }

            if($domain_data['man_email'] == ALLOWED) {
                unset($domain_data['man_email']);
                $domain_data['manage_email'] = true;
            }
            else {
                unset($domain_data['man_email']);
                $domain_data['manage_email'] = false;
            }

            if($domain_data['man_database'] == ALLOWED) {
                unset($domain_data['man_database']);
                $domain_data['manage_database'] = true;
            }
            else {
                unset($domain_data['man_database']);
                $domain_data['manage_database'] = false;
            }

            if($domain_data['man_protect_dir'] == ALLOWED) {
                unset($domain_data['man_protect_dir']);
                $domain_data['manage_protect_directory'] = true;
            }
            else {
                unset($domain_data['man_protect_dir']);
                $domain_data['manage_protect_directory'] = false;
            }

            if($domain_data['man_ssl'] == ALLOWED) {
                unset($domain_data['man_ssl']);
                $domain_data['manage_ssl'] = true;
            }
            else {
                unset($domain_data['man_ssl']);
                $domain_data['manage_ssl'] = false;
            }

        $domain_data['php'] = $domain_data['php'] == "1";
//            unset($domain_data['php']);

            $domain_data['php_version'] = $this->env->getPhpVersion($domain_data['php_version']);
//            unset($domain_data['php_version']);

//       $domain_data['cgi'] = $domain_data['cgi'];
            unset($domain_data['cgi']);

            $domain_data['perl'] = $domain_data['perl'] == "1";
//            unset($domain_data['perl']);

            $domain_data['python'] = $domain_data['python'] == "1";
//            unset($domain_data['python']);

            unset($domain_data['fast_cgi']);

            $domain_data['overuse'] = $domain_data['overuse'] == "1";

            $domain_data['statistics'] = $domain_data['statistic_support'] == "1";
            unset($domain_data['statistic_support']);

            $domain_data['statistics_type'] = $this->env->getStatisticsType($domain_data['statistic_type']);

//        $domain_data['asp'] = $domain_data['asp'];
            $domain_data['asp'] = $domain_data['asp'] == "1";
//            unset($domain_data['asp']);

//        $domain_data['aspnet'] = $domain_data['aspnet'];
            $domain_data['aspnet'] = $domain_data['aspnet'] == "1";
//            unset($domain_data['aspnet']);

//        $domain_data['aspnet_core'] = $domain_data['aspnet_core'];
            $domain_data['aspnet_core'] = $domain_data['aspnet_core'] == "1";
//            unset($domain_data['aspnet_core']);

            $domain_data['aspnet_version'] = $this->env->getNetVersion($domain_data['aspnet_version']);
//            unset($domain_data['aspnet_version']);

//        $domain_data['dedicated_pool'] = $domain_data['dedicated_pool'];
            $domain_data['dedicated_pool'] = $domain_data['dedicated_pool'] == "1";
//            unset($domain_data['dedicated_pool']);

            $domain_data['cpu_limit'] = $domain_data['max_cpu'];
//            unset($domain_data['max_cpu']);

            $domain_data['application_pool_mode'] = $this->env->getApplicationMode($domain_data['application_pool_mode']);
//        $domain_data['application_pool_mode'] = $domain_data['application_pool_mode'];
//            unset($domain_data['application_pool_mode']);

//        $domain_data['ssl'] = $domain_data['ssl'];
            unset($domain_data['ssl']);
            unset($domain_data['prefix']);

            return $domain_data;
        }
        catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}