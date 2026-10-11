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
 * Class Migration
 * @package FPanel\Models
 * @method static Migration|Model\ResultsetInterface findFirst($params = null)
 * @method static Migration|Model\ResultsetInterface findFirstById($params = null)
 * @method static Migration[]|Model\ResultsetInterface find($params = null)
 */
class Migration extends FModel
{
    /**
     * @Primary
     * @var int
     */
    public $id;

    public $server_type;

    public $ip_address;

    public $root;

    public $root_pass;

    public $admin;

    public $admin_pass;

    public $configuration;

    public $description;

    public $created_date;

    public $last_migration_date;

    public $status;

    public $server_id;

    public function initialize()
    {
        $this->setSource('migration');
    }

}
