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
 */
namespace FPanel\Models;

use Phalcon\Mvc\Model;

/**
 * Class Ssl
 * @package FPanel\Models
 * @method static Certificates|Model\ResultsetInterface findFirst($params = null)
 * @method static Certificates|Model\ResultsetInterface findFirstById($params = null)
 * @method static Certificates[]|Model\ResultsetInterface find($params = null)
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 */
class Certificates extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $domain_id;

    public $csr;

    public $private_key;

    public $crt;

    public $ca;

    public $name;

    public $bit;

    public $country;

    public $state;

    public $city;

    public $company;

    public $department;

    public $domain_name;

    public $email;

    public $common_name;

    public $valid_to;

    public $valid_from;

    public $signature_type;

    public $created_date;

    public function initialize()
    {
        $this->setSource('certificates');
        $this->belongsTo('domain_id', 'FPanel\Models\Domains', 'id', ['alias' => 'Domains']);
    }

}
