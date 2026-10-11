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
 */namespace FPanel\Environments;

use FPanel\Models\DnsTemplate;

trait Dns
{

    /**
     * @param string $server_id
     * @return array
     */
    public function defaultDNSRecordsServer(string $server_id = "1"): array
    {
        return array(
            // NS
            array(
                'type'          => 'NS',
                'host'          => '[domain]',
                'value'         => 'ns1.[domain]',
                'opt'           => '',
                'time_stamp'    => '0',
                'order'         => '1',
                'server_id'     => $server_id,
            ),
            array(
                'type'          => 'NS',
                'host'          => '[domain]',
                'value'         => 'ns2.[domain]',
                'opt'           => '',
                'time_stamp'    => '0',
                'order'         => '2',
                'server_id'     => $server_id,
            ),
            // MX
            array(
                'type'          => 'MX',
                'host'          => '[domain]',
                'value'         => 'mail.[domain]',
                'opt'           => '10',
                'time_stamp'    => '0',
                'order'         => '3',
                'server_id'     => $server_id,
            ),
            // A
            array(
                'type'          => 'A',
                'host'          => 'ns1.[domain]',
                'value'         => '[ip]',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '4',
                'server_id'     => $server_id,
            ),
            array(
                'type'          => 'A',
                'host'          => 'ns2.[domain]',
                'value'         => '[ip]',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '5',
                'server_id'     => $server_id,
            ),
            array(
                'type'          => 'A',
                'host'          => '[domain]',
                'value'         => '[ip]',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '6',
                'server_id'     => $server_id,
            ),
            array(
                'type'          => 'A',
                'host'          => 'mail.[domain]',
                'value'         => '[ip]',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '7',
                'server_id'     => $server_id,
            ),
            // CNAME
            array(
                'type'          => 'CNAME',
                'host'          => 'webmail.[domain]',
                'value'         => '[domain]',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '11',
                'server_id'     => $server_id,
            ),
            array(
                'type'          => 'CNAME',
                'host'          => 'www.[domain]',
                'value'         => '[domain]',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '12',
                'server_id'     => $server_id,
            ),
//            // PTR
//            array(
//                'type'          => 'PTR',
//                'host'          => '[ip]',
//                'value'         => '[domain]',
//                'opt'           => '24',
//                'time_stamp'    => '0',
//                'order'         => '13',
//                'server_id'     => $server_id,
//            ),
            // TXT
            array(
                'type'          => 'TXT',
                'host'          => '[domain]',
                'value'         => 'v=spf1 +a +mx -all',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '14',
                'server_id'     => $server_id,
            ),
            array(
                'type'          => 'TXT',
                'host'          => '[domain]',
                'value'         => 'v=spf1 a mx ip4:[ip] ~all',
                'opt'           => '0',
                'time_stamp'    => '0',
                'order'         => '15',
                'server_id'     => $server_id,
            ),
        );
    }

    /**
     * @param $server_id
     * @return array
     */
    public function getDefaultDNSRecords($server_id): array
    {
        $default_dns = DnsTemplate::find("server_id = '$server_id'");
        $default_dns_records = $default_dns->toArray();
        return array_reverse($default_dns_records);
    }

}