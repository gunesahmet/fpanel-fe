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
 * Class Subdomains
 * @package FPanel\Models
 *
 * @property FtpUsers FtpUsers
 * @method static FtpUsers|Model\ResultsetInterface getFtpUsers($params = null)
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 *
 * @method static Subdomains|Model\ResultsetInterface findFirst($params = null)
 * @method static Subdomains|Model\ResultsetInterface findFirstById($params = null)
 * @method static Subdomains[]|Model\ResultsetInterface find($params = null)
 */
class Subdomains extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $domain_id;

    public $ipaddr_id;

    public $subdomain_name;

    public $full_name;

    public $ftpuser_type;

    public $ftpuser_id;

    public $ssl;

    public $php;

    public $php_version;

    public $cgi;

    public $perl;

    public $python;

    public $fast_cgi;

    public $status;

    public $asp;

    public $aspnet_core;

    public $aspnet;

    public $aspnet_version;

    public $dedicated_pool;

    public $max_cpu;

    public $custom_error;

    public $created_date;

    public $updated_date;


    public function initialize()
	{
		$this->setSource('subdomains');
        $this->belongsTo('ftpuser_id', 'FPanel\Models\FtpUsers', 'id', ['alias' => 'FtpUsers']);
        $this->belongsTo('domain_id', 'FPanel\Models\Domains', 'id', ['alias' => 'Domains']);
	}
}
