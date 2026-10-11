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
 * Class Databases
 * @package FPanel\Models
 * @property DatabaseServer SystemDatabaseServer
 * @property DatabaseUsers DatabaseUsers
 *
 * @method static DatabaseServer|Model\ResultsetInterface getSystemDatabaseServer($params = null)
 *
 * @method static DatabaseUsers|Model\ResultsetInterface getDatabaseUsers($params = null)
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 *
 * @property Servers Servers
 * @method static Servers|Model\ResultsetInterface getServers($params = null)
 *
 * @method static Databases|Model\ResultsetInterface findFirst($params = null)
 * @method static Databases[]|Model\ResultsetInterface find($params = null)
 *
 * @method static Databases|Model\ResultsetInterface findFirstById($params = null)
 */
class Databases extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $name;

    public $type;

    public $database_user_id;

    public $domain_id;

    public $database_server_id;

    public $server_id;

    public $created_date;

    public $updated_date;

	public function initialize()
	{
		$this->setSource('databases');
        $this->belongsTo('database_server_id', 'FPanel\Models\DatabaseServer', 'id', ['alias' => 'DatabaseServer']);
        $this->belongsTo('database_user_id', 'FPanel\Models\DatabaseUsers', 'id', ['alias' => 'DatabaseUsers']);
        $this->belongsTo('domain_id', 'FPanel\Models\Domains', 'id', ['alias' => 'Domains']);
        $this->belongsTo('server_id', 'FPanel\Models\Servers', 'id', ['alias' => 'Servers']);
	}
}
