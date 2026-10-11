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
 * Class ProtectDirectoryUser
 * @package FPanel\Models
 * @method static ProtectDirectoryUser|Model\ResultsetInterface findFirst($params = null)
 * @method static ProtectDirectoryUser|Model\ResultsetInterface findFirstById($params = null)
 * @method static ProtectDirectoryUser[]|Model\ResultsetInterface find($params = null)
 */
class ProtectDirectoryUser extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $username;

    public $password;

    public $protect_dir_id;

    public $created_date;

    public $updated_date;

    public function initialize()
    {
        $this->setSource('protect_directory_users');
    }
}
