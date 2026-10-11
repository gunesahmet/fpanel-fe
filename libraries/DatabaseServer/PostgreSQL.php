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
 */namespace FPanel\DatabaseServer;

use FPanel\Library\Library;
use PDO;
use PDOException;

class PostgreSQL extends Library
{
    public function create($username, $password, $port, $host): bool
    {
        try {
            $dbh = new PDO("pgsql:dbname=postgres;host=$host;port=$port;user=$username;password=$password");
            $dbh->exec("REVOKE ALL ON pg_user FROM public;");
            $dbh->exec("REVOKE ALL ON pg_roles FROM public;");
            $dbh->exec("REVOKE ALL ON pg_group FROM public;");
            $dbh->exec("REVOKE ALL ON pg_authid FROM public;");
            $dbh->exec("REVOKE ALL ON pg_auth_members FROM public;");
            $dbh->exec("REVOKE ALL ON pg_database FROM public;");
            $dbh->exec("REVOKE ALL ON pg_tablespace FROM public;");
            $dbh->exec("REVOKE ALL ON pg_settings FROM public;");

            $errors = $dbh->errorInfo();
            if($errors[2] != null) {
                $this->error_messages['content'] = $errors[2];
                return false;
            }
            return true;
        }
        catch (PDOException $e) {
            $this->error_messages['content'] = $e->getMessage();
            return false;
        }
    }
}