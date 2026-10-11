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
 * Class Backup
 * @package FPanel\Models
 *
 *
 * @property Domains Domains
 * @method static Domains|Model\ResultsetInterface getDomains($params = null)
 *
 * @property Users Users
 * @method static Users|Model\ResultsetInterface getUsers($params = null)
 *
 * @method static Backup|Model\ResultsetInterface findFirst($params = null)
 * @method static Backup|Model\ResultsetInterface findFirstById($params = null)
 * @method static Backup[]|Model\ResultsetInterface find($params = null)
 */
class Backup extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $type;

    public $domain_id;

    public $client_id;

    public $server_id;

    public $repository;

    public $ftp_repository_id;

    public $status;

    public $name;

    public $email;

    public $suspend;

    public $progress;

    public $description;

    public $content;

    public $sizes;

    public $restore_contents;

    public $storage_id;

    public $progress_data;

    public $created_date;

    /**
	 * init
	 */
	public function initialize()
	{
		$this->setSource('backup');
        $this->belongsTo('domain_id', 'FPanel\Models\Domains', 'id', ['alias' => 'Domains']);
        $this->belongsTo('client_id', 'FPanel\Models\Users', 'id', ['alias' => 'Users']);
    }
}
