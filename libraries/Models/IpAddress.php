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
 * Class IpAddress
 * @package FPanel\Models
 * @method static IpAddress|Model\ResultsetInterface findFirst($params = null)
 * @method static IpAddress|Model\ResultsetInterface findFirstById($params = null)
 * @method static IpAddress|Model\ResultsetInterface findFirstByIpAddress($params = null)
 * @method static IpAddress[]|Model\ResultsetInterface find($params = null)
 * @property Servers servers
 * @method Servers getServers($params = [])
 */
class IpAddress extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $index;

    public $ip_address;

    public $old_data;

    public $ssl_status;

    public $netmask;

    public $gateway;

    public $interface;

    public $type;

    public $format;

    public $is_default;

    public $server_id;

    public $init;

    public $process;

    public $created_date;

    public $updated_date;

	public function initialize()
	{
		$this->setSource('ip_address');
        $this->belongsTo('server_id', 'FPanel\Models\Servers', 'id', ['alias' => 'servers',]);
    }
}
