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
 * Class DomainStatistics
 * @package FPanel\Models
 * @method static DomainStatistics|Model\ResultsetInterface findFirst($params = null)
 * @method static DomainStatistics|Model\ResultsetInterface findFirstById($params = null)
 * @method static DomainStatistics[]|Model\ResultsetInterface find($params = null)
 */
class DomainStatistics extends FModel
{
    /**
     * @Primary
     * @var int
     */
    public $id;

    public $domain_id;

    public $client_id;

    public $domain_name;

    public $mail;

    public $mail_max;

    public $db;

    public $db_max;

    public $httpdoc;

    public $disk;

    public $disk_max;

    public $traffic;

    public $traffic_max;

    public $logs;

    public $status;

    public $statistic;

    public $over_use;

    public $created_date;

    public $expire_date;

    public $last_init;

    public $updated_date;

    public function initialize()
	{
        $this->setSource('domain_statistics');
    }
}
