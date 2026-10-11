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
 * Class ClientIpAddress
 * @package FPanel\Models
 * @method static ClientIpAddress|Model\ResultsetInterface findFirst($params = null)
 * @method static ClientIpAddress|Model\ResultsetInterface findFirstById($params = null)
 * @method static ClientIpAddress[]|Model\ResultsetInterface find($params = null)
 *
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 *
 *
 * @property Users Users
 * @method static Users|Model\ResultsetInterface getUsers($params = null)
 */
class ClientIpAddress extends FModel
{
    /**
     * @Primary
     * @var int
     */
    public $id;

    public $ipaddr_id;

    public $client_id;

    public $created_date;

	public function initialize()
	{
		$this->setSource('client_ip_address');
        $this->belongsTo('domain_id', 'FPanel\Models\Domains', 'id', ['alias' => 'Domains']);
        $this->belongsTo('client_id', 'FPanel\Models\Users', 'id', ['alias' => 'Users']);
	}
}
