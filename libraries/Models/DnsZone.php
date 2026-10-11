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
 * Class DnsZone
 * @package FPanel\Models
 *
 * @property DnsRecords DnsRecords
 * @method static DnsRecords|Model\ResultsetInterface getDnsRecords($params = null)
 *
 * @property Servers Servers
 * @method static Servers|Model\ResultsetInterface getServers($params = null)
 *
 * @method static DnsZone|Model\ResultsetInterface findFirst($params = null)
 * @method static DnsZone|Model\ResultsetInterface findFirstById($params = null)
 * @method static DnsZone[]|Model\ResultsetInterface find($params = null)
 */
class DnsZone extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $zone_type;

    public $domain_id;

    public $domain_name;

    public $status;

    public $email;

    public $type;

    public $ttl;

    public $refresh;

    public $retry;

    public $expire;

    public $minimum;

    public $serial_format;

    public $serial;

    public $server_id;

    public $created_date;

    public $updated_date;

    public function initialize()
    {
        $this->setSource('dns_zone');
        $this->hasMany('id', 'FPanel\Models\DnsRecords', 'dns_zone_id', ['alias' => 'DnsRecords']);
        $this->belongsTo('server_id', 'FPanel\Models\Servers', 'id', ['alias' => 'Servers']);
    }
}
