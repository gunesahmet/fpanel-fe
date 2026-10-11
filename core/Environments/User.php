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
 */namespace FPanel\Environments;
use FPanel\Models\Users;

/**
 * Class User
 * @package FPanel\Environments
 */
trait User
{
    public function checkUser($username)
    {
        $users = Users::findFirst("username = '$username'");
        if($users) {
            echo '<span data-toggle="tooltip" data-title="'. __('This user already exists on system.').'"><i class="text-error fa fa-exclamation-circle"></i></span>';
        }
    }
}