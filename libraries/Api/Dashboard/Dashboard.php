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
 */namespace FPanel\Api\Dashboard;

use FPanel\Process\Admin;
use Phalcon\Mvc\User\Component;

class Dashboard extends Component
{
    public function get($client_id, $client_type): array
    {
        $dashboard_data = array();

        switch($client_type) {
            case ROOT_USER:
            case ADMIN_USER:
                $dashboard_data = $this->adminDashboard($client_id);
                break;
            case RESELLER_USER:
                $dashboard_data = $this->resellerDashboard($client_id);
                break;
            case CLIENT_USER:
                $dashboard_data = $this->clientDashboard($client_id);
                break;
            case DOMAIN_USER:
                $dashboard_data = $this->domainDashboard($client_id);
                break;
        }
        $dashboard_data['status'] = "1";
        return $dashboard_data;
    }

    public function domainDashboard($domain_id): array
    {
        if($domain_id == null) {

        }

        return array(
            'type' => 'domain',
            'data' => array(
                'usages' => array(),
                'buttons' => array(
                    'webhosting' => 'domain/webhosting',
                    'dns' => 'domain/dns',
                    'databases' => 'domain/database',
                    'subdomains' => 'domain/subdomain',
                    'crons' => 'domain/cron',
                    'protect_dirs' => 'domain/protect_dir',
                    'mails' => 'domain/mail'
                )
            )
        );
    }

    public function clientDashboard($client_id): array
    {
        if($client_id == null) {

        }

        return array(
            'type' => 'client',
            'data' => array(
                'usages' => array(),
                'buttons' => array(
                    array(
                        '@attributes' => array(
                            'name' => 'domains',
                            'status' => '1',
                        ),
                        'link' => 'domains/list',
                    ),
                    array(
                        '@attributes' => array(
                            'name' => 'templates',
                            'status' => '1',
                        ),
                        'link' => 'templates/list',
                    ),
                )
            )
        );
    }

    public function resellerDashboard($reseller_id): array
    {
        if($reseller_id == null) {

        }
        return array(
            'type' => 'reseller',
            'data' => array(
                'usages' => array(),
                'buttons' => array(
                    array(
                        '@attributes' => array(
                            'name' => 'clients',
                            'status' => '1',
                        ),
                        'link' => 'clients/list',
                    ),
                    array(
                        '@attributes' => array(
                            'name' => 'domains',
                            'status' => '1',
                        ),
                        'link' => 'domains/list',
                    ),
                    array(
                        '@attributes' => array(
                            'name' => 'templates',
                            'status' => '1',
                        ),
                        'link' => 'templates/list',
                    ),
                )
            )
        );
    }

    public function adminDashboard($id): array
    {

        $sysinfo = new Admin();
        return array(
            'type' => 'admin',
            'data' => array(
                'usages' => array(),
                'buttons' => array(
                    array(
                        '@attributes' => array(
                            'name' => 'clients',
                            'status' => '1',
                        ),
                        'link' => 'clients/list',
                    ),
                    array(
                        '@attributes' => array(
                            'name' => 'domains',
                            'status' => '1',
                        ),
                        'link' => 'domains/list',
                    ),
                    array(
                        '@attributes' => array(
                            'name' => 'templates',
                            'status' => '1',
                        ),
                        'link' => 'templates/list',
                    ),
                ),
                'sysinfo' => $sysinfo->sysinfo()
            )
        );
    }
}