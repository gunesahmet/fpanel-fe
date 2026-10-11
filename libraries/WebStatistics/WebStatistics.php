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
 */namespace FPanel\WebStatistics;

use FPanel\Library\Library;
use FPanel\Models\Domains;

class WebStatistics extends Library
{
    public $host_report = array();
    public $general_report = array();
    public $daily_report = array();
    public $monthly_report = array();
    public $host_redirection_report = array();
    public $host_failure_report = array();
    public $redirected_referrer_report = array();
    public $failed_referrer_report = array();
    public $referring_site_report = array();
    public $referrer_report = array();
    public $browser_report = array();
    public $status_report = array();
    private $scan_status = true;

    public function __construct($domain_name)
    {
        parent::__construct();
        $domains = Domains::findFirst("name = '$domain_name'");

        if(!$domains)  {
            $this->error_messages["domain"] = __("Domain not found");
            return;
        }

        $webstatistics = new \FPanel\Process\Webstatistics();
        $webstatistics->setServerId($domains->web_server_id);
        $report = $webstatistics->getReport([
            "domain" => $domain_name
        ]);

        if(empty($report)) {
            $this->scan_status = false;
            return;
        }

        $general_summary = array();
        $monthly_summary = array();
//        $weekly_summary = array();
        $daily_summary = array();
//        $hourly_summary = array();

        $host_report = array();
        $host_redirection_report = array();
        $host_failure_report = array();

        $referrer_report = array();
        $referring_site_report = array();
        $redirected_referrer_report = array();
        $failed_referrer_report = array();

        $browser_report = array();
//        $os_report = array();
        $status_report = array();
//        $file_size_report = array();
//        $file_type_report = array();

//        $redirection_report = array();
//        $failure_report = array();
//        $request_report = array();

        if($report != false) {
            $json_data = json_decode(trim($this->crypto->staticDecode($report)),true);
            foreach($json_data['report'] as $id => $json_item) {
                // General Summary
                if($json_item['@attributes']['name'] == "rep_gensum")
                    $general_summary = $json_data['report'][$id];

                // Monthly Summary
                if($json_item['@attributes']['name'] == "rep_month") {
                    $monthly_summary = $json_data['report'][$id];
                }

                // Weekly Summary
//                if($json_item['@attributes']['name'] == "rep_week")
//                    $weekly_summary = $json_data['report'][$id];

                // Daily Summary
                if($json_item['@attributes']['name'] == "rep_dayrep")
                    $daily_summary = $json_data['report'][$id];

                // Hourly Summary
//                if($json_item['@attributes']['name'] == "rep_hourrep")
//                    $hourly_summary = $json_data['report'][$id];

                // Host Report
                if($json_item['@attributes']['name'] == "rep_host")
                    $host_report = $json_data['report'][$id];

                // Host Redirection Report
                if($json_item['@attributes']['name'] == "rep_redirhost")
                    $host_redirection_report = $json_data['report'][$id];

                // Host Failure Report
                if($json_item['@attributes']['name'] == "rep_failhost")
                    $host_failure_report = $json_data['report'][$id];

                // Referrer Report
                if($json_item['@attributes']['name'] == "rep_ref")
                    $referrer_report = $json_data['report'][$id];

                // Referrer Site Report
                if($json_item['@attributes']['name'] == "rep_refsite")
                    $referring_site_report = $json_data['report'][$id];

                // Redirected Referrer Report
                if($json_item['@attributes']['name'] == "rep_redirref")
                    $redirected_referrer_report = $json_data['report'][$id];

                // Referrer Failure Report
                if($json_item['@attributes']['name'] == "rep_failref")
                    $failed_referrer_report = $json_data['report'][$id];

                // Browser Report
                if($json_item['@attributes']['name'] == "rep_browrep")
                    $browser_report = $json_data['report'][$id];

                // OS Report
//                if($json_item['@attributes']['name'] == "rep_os")
//                    $os_report = $json_data['report'][$id];

                // Status Report
                if($json_item['@attributes']['name'] == "rep_code")
                    $status_report = $json_data['report'][$id];

                // File Size Report
//                if($json_item['@attributes']['name'] == "rep_size")
//                    $file_size_report = $json_data['report'][$id];

                // File Type Report
//                if($json_item['@attributes']['name'] == "rep_type")
//                    $file_type_report = $json_data['report'][$id];

                // Redirection Report
//                if($json_item['@attributes']['name'] == "rep_redir")
//                    $redirection_report = $json_data['report'][$id];

                // Failure Report
//                if($json_item['@attributes']['name'] == "rep_fail")
//                    $failure_report = $json_data['report'][$id];

                // Request Report
//                if($json_item['@attributes']['name'] == "rep_req")
//                    $request_report = $json_data['report'][$id];

            }

            $daily_report = array_slice($daily_summary['row'], -7, 7, true);
            $daily_report = array_values(array_filter($daily_report));

            $this->daily_report = $daily_report;
            $this->monthly_report = $monthly_summary;
            $this->general_report = $general_summary;
            $this->host_report = $host_report;
            $this->host_redirection_report = $host_redirection_report;
            $this->host_failure_report = $host_failure_report;
            $this->redirected_referrer_report = $redirected_referrer_report;
            $this->failed_referrer_report = $failed_referrer_report;
            $this->referring_site_report = $referring_site_report;
            $this->referrer_report = $referrer_report;
            $this->browser_report = $browser_report;
            $this->status_report = $status_report;
        }
    }

    public function getScanStatus(): bool
    {
        return $this->scan_status;
    }

    public function hostReport()
    {
        $hosts = array();
        $new_hosts = array();

        if(!isset($this->host_report['row'])) {
            $this->view->setVar('host_report', $new_hosts);
            return;
        }

        foreach($this->host_report['row'] as $host) {
            $hosts[] = array(
                'request' => $host['col'][0],
                'ip' => $host['col'][15]
            );
        }


        usort($hosts, function($a, $b) {
            return $b['request'] - $a['request'];
        });

        $i = 0;
        $reqs = 0;
        $total_request = $this->general_report['row'][0]['col'][1];
        foreach($hosts as $host) {
            if($i <= 10) {
                $reqs += $host['request'];
                $new_hosts[] = array(
                    'request' => $host['request'],
                    'ip' => $host['ip']
                );
            }
            else {
                $remained_reqs = $total_request - $reqs;
                $new_hosts[] = array(
                    'request' => $remained_reqs,
                    'ip' => __('Others')
                );
                break;
            }
            $i++;
        }

        $this->view->setVar('host_report', $new_hosts);
    }

    public function hostRedirectionReport(): WebStatistics
    {
        $host_redirect_report = array();
        $reqs = 0;
        $i = 0;
        if(!isset($this->host_redirection_report['row'])) {
            $this->host_redirection_report = $host_redirect_report;
            return $this;
        }

        foreach($this->host_redirection_report['row'] as $host_redirect) {
            if($i < 10) {
                $host_redirect_report[] = array(
                    'host' => $host_redirect['col'][15],
                    'request' => $host_redirect['col'][0],
                );
            }
            else {
                $reqs += $host_redirect['col'][0];
                $host_redirect_report['others'] = array(
                    'host' => __('Others'),
                    'request' => $reqs,
                );
                continue;
            }

            $i++;
        }

        $this->host_redirection_report = $host_redirect_report;
        return $this;
    }

    public function hostFailureReport(): WebStatistics
    {
        $host_failure_report = array();
        $reqs = 0;
        $i = 0;
        if(!isset($this->host_failure_report['row'])) {
            $this->host_failure_report = $host_failure_report;
            return $this;
        }

        foreach($this->host_failure_report['row'] as $host_fail) {
            if($i < 10) {
                $host_failure_report[] = array(
                    'host' => $host_fail['col'][15],
                    'request' => $host_fail['col'][0],
                );
            }
            else {
                $reqs += $host_fail['col'][0];
                $host_failure_report['others'] = array(
                    'host' => __('Others'),
                    'request' => $reqs,
                );
                continue;
            }

            $i++;
        }
        $this->host_failure_report = $host_failure_report;
        return $this;
    }

    public function failedReferrerReport(): WebStatistics
    {
        $failed_referrer_report = array();
        $reqs = 0;
        $i = 0;
        if(!isset($this->failed_referrer_report['row'])) {
            $this->failed_referrer_report = $failed_referrer_report;
            return $this;
        }

        foreach($this->failed_referrer_report['row'] as $host_fail) {
            if($i < 10) {
                $failed_referrer_report[] = array(
                    'page' => $host_fail['col'][15],
                    'request' => $host_fail['col'][0],
                );
            }
            else {
                $reqs += $host_fail['col'][0];
                $failed_referrer_report['others'] = array(
                    'page' => __('Others'),
                    'request' => $reqs,
                );
                continue;
            }

            $i++;
        }
        $this->failed_referrer_report = $failed_referrer_report;
        return $this;
    }

    public function redirectedReferrerReport(): WebStatistics
    {
        $redirected_referrer = array();
        $reqs = 0;
        $i = 0;
        if(!isset($this->redirected_referrer_report['row'])) {
            $this->redirected_referrer_report = $redirected_referrer;
            return $this;
        }

        foreach($this->redirected_referrer_report['row'] as $referrer) {
            if($i < 10) {
                $redirected_referrer[] = array(
                    'page' => $referrer['col'][15],
                    'request' => $referrer['col'][0],
                );
            }
            else {
                $reqs += $referrer['col'][0];
                $redirected_referrer['others'] = array(
                    'page' => __('Others'),
                    'request' => $reqs,
                );
                continue;
            }

            $i++;
        }
        $this->redirected_referrer_report = $redirected_referrer;
        return $this;
    }

    public function referrerSiteReport(): WebStatistics
    {
        $referring_site_report = array();
        $reqs = 0;
        $i = 0;
        if(!isset($this->referring_site_report['row'])) {
            $this->referring_site_report = $referring_site_report;
            return $this;
        }

        foreach($this->referring_site_report['row'] as $referrer) {
            if($i < 10) {
                $referring_site_report[] = array(
                    'page' => $referrer['col'][15],
                    'request' => $referrer['col'][0],
                );
            }
            else {
                $reqs += $referrer['col'][0];
                $referring_site_report['others'] = array(
                    'page' => __('Others'),
                    'request' => $reqs,
                );
                continue;
            }

            $i++;
        }
        $this->referring_site_report = $referring_site_report;
        return $this;
    }

    public function referrerReport(): WebStatistics
    {
        $referrer_report = array();
        $reqs = 0;
        $i = 0;
        if(!isset($this->referrer_report['row'])) {
            $this->referrer_report = $referrer_report;
            return $this;
        }

        foreach($this->referrer_report['row'] as $referrer) {
            if($i < 10) {
                $referrer_report[] = array(
                    'page' => $referrer['col'][15],
                    'request' => $referrer['col'][0],
                );
            }
            else {
                $reqs += $referrer['col'][0];
                $referrer_report['others'] = array(
                    'page' => __('Others'),
                    'request' => $reqs,
                );
                continue;
            }

            $i++;
        }
        $this->referrer_report = $referrer_report;
        return $this;
    }

    public function browserReport(): WebStatistics
    {
//        $browser_report = array();
//        $reqs = 0;
//        $i = 0;

        $browser_types = array();
        if(!isset($this->browser_report['row'])) {
            $this->browser_report = $browser_types;
            return $this;
        }

        foreach($this->browser_report['row'] as $browser) {
            $browsers = new Browser();
            $browsers->setUserAgent($browser['col'][15]);
            if (isset($browser['col'][15]) && preg_match('/bot|crawl|slurp|spider/i', $browser['col'][15])) {
                if(empty($browser_types["search_engine_bots"]))
                    $browser_types["Search Engine Spiders"] = 1;
                else
                    $browser_types["Search Engine Spiders"] += $browser_types["Search Engine Spiders"];
            }
            else {
                if(empty($browser_types[$browsers->getBrowser()]))
                    $browser_types[$browsers->getBrowser()] = 1;
                else
                    $browser_types[$browsers->getBrowser()] += $browser_types[$browsers->getBrowser()];
            }

//
//            if($i < 10) {
//                $browser_report[] = array(
//                    'browser' => $browser['col'][15],
//                    'request' => $browser['col'][0],
//                );
//            }
//            else {
//                $reqs += $browser['col'][0];
//                $browser_report['others'] = array(
//                    'browser' => __('Others'),
//                    'request' => $reqs,
//                );
//                continue;
//            }

//            $i++;
        }

        $this->browser_report = $browser_types;
        return $this;
    }

    public function statusCodeReport(): WebStatistics
    {
        $status_report = array();
        $reqs = 0;
        $i = 0;
        if(!isset($this->status_report['row'])) {
            $this->status_report = $status_report;
            return $this;
        }

        foreach($this->status_report['row'] as $status) {
            if($i < 10) {
                $status_report[] = array(
                    'code' => $status['col'][15],
                    'request' => $status['col'][0],
                );
            }
            else {
                $reqs += $status['col'][0];
                $status_report['others'] = array(
                    'code' => __('Others'),
                    'request' => $reqs,
                );
                continue;
            }

            $i++;
        }
        $this->status_report = $status_report;
        return $this;
    }

    public function finish()
    {
        $this->view->setVar('daily_report', $this->daily_report);
        $this->view->setVar('monthly_report', $this->monthly_report);

//        $general_report[10]['col'][1]

        $this->view->setVar('general_report',($this->general_report['row'] ?? []));
        $this->view->setVar('host_repot', $this->host_report);
        $this->view->setVar('host_redirection_report', $this->host_redirection_report);
        $this->view->setVar('host_failure_report', $this->host_failure_report);
        $this->view->setVar('redirected_referrer_report', $this->redirected_referrer_report);
        $this->view->setVar('failed_referrer_report', $this->failed_referrer_report);
        $this->view->setVar('referring_site_report', $this->referring_site_report);
        $this->view->setVar('referrer_report', $this->referrer_report);
        $this->view->setVar('browser_report', $this->browser_report);
        $this->view->setVar('status_report', $this->status_report);

    }
}
