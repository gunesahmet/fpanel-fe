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
 * Class Userdata
 * @package FPanel\Models
 * @method static Userdata|Model\ResultsetInterface findFirst($params = null)
 * @method static Userdata|Model\ResultsetInterface findFirstById($params = null)
 * @method static Userdata[]|Model\ResultsetInterface find($params = null)
 */
class Userdata extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $user_id;

    public $fullname;

    public $email;

    public $company;

    public $fax;

    public $telephone;

    public $address;

    public $city;

    public $region;

    public $zipcode;

    public $country;

    public $updated_date;

	public function initialize()
	{
		$this->setSource('userdata');
	}
}
