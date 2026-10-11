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
 * Class ProtectDirectory
 * @package FPanel\Models
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 *
 * @property SubDomains SubDomains
 * @method static SubDomains|Model\ResultsetInterface getSubDomains($params = null)
 *
 * @method static ProtectDirectory|Model\ResultsetInterface findFirst($params = null)
 * @method static ProtectDirectory|Model\ResultsetInterface findFirstById($params = null)
 * @method static ProtectDirectory[]|Model\ResultsetInterface find($params = null)
 */
class ProtectDirectory extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $domain_id;

    public $subdomain_id;

    public $path;

    public $header;

    public $created_date;

    public $updated_date;

    public function initialize()
    {
        $this->setSource('protect_directory');
        $this->belongsTo('domain_id', 'FPanel\Models\Domains', 'id', ['alias' => 'Domains']);
        $this->belongsTo('subdomain_id', 'FPanel\Models\SubDomains', 'id', ['alias' => 'SubDomains']);
    }
}
