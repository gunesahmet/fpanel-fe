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
 * Class VirtualDirectory
 * @method static VirtualDirectory|Model\ResultsetInterface findFirst($params = null)
 * @method static VirtualDirectory|Model\ResultsetInterface findFirstById($params = null)
 * @method static VirtualDirectory[]|Model\ResultsetInterface find($params = null)
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 */
class VirtualDirectory extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $domain_id;

    public $name;

    public $path;

    public $app;

    public $created_date;

    public $updated_date;

    public function initialize()
    {
        $this->setSource('virtual_directory');
        $this->belongsTo('domain_id', 'FPanel\Models\Domains', 'id', ['alias' => 'Domains']);
    }
}
