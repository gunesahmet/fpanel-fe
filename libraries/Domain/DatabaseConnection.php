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
 */namespace FPanel\Domain;

use FPanel\Models\DatabaseServer;

trait DatabaseConnection
{
    public function connection($db_server_id): bool
    {
        $db_server = DatabaseServer::findFirst("id = '$db_server_id'");
        if(!$db_server) {
            $this->error_messages['content'] = __('Database server not found');
            $this->error_messages['content_id'] = $db_server_id;
            return false;
        }

        $database_server = new \FPanel\DatabaseServer\DatabaseServer();
        $database_server->setPosts(array(
            'db_server_name' => $db_server->db_server_name
        ));
        $test_connection = $database_server->connection($db_server->type, $db_server->username, trim($this->crypto->bdecode(trim($db_server->password))), $db_server->port, $db_server->host);

        if($test_connection == false) {
            $this->error_messages['content'] = __('Server connection failed. Please try another server');
            return false;
        }
        else {
            return true;
        }
    }

}