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
 * Class Users
 *
 * @property ClientStatistics ClientStatistics
 * @method static ClientStatistics|Model\ResultsetInterface getClientStatistics($params = null)
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 *
 * @property Users Users
 * @method static Users|Model\ResultsetInterface getUsers($params = null)
 *
 * @method static Users|Model\ResultsetInterface findFirstById($params = null)
 * @method static Users|Model\ResultsetInterface findFirst($params = null)
 * @method static Users[]|Model\ResultsetInterface find($params = null)
 * @method static Users|Model\ResultsetInterface findFirstByUsername($params = null)
 */
class Users extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $username;

    public $password;

    public $password_last_set;

    public $last_login;

    public $last_login_ip;

    public $parent;

    public $type;

    public $must_change_password;

    public $created_date;

    public $updated_date;

    public $expire_date;

    public $status_msg;

    public $status;

    public $interface;

    public $language;

    public $api_status;

    public $changed_data;

    public function initialize()
	{
		$this->setSource('users');
        $this->belongsTo('username', 'FPanel\Models\Domains', 'name', ['alias' => 'Domains']);
        $this->belongsTo('id', 'FPanel\Models\ClientStatistics', 'client_id', ['alias' => 'ClientStatistics']);
        $this->hasMany('id', 'FPanel\Models\Users', 'parent', ['alias' => 'Users']);
	}
}
