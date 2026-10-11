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
 */namespace FPanel\Update;

use Exception;
use Phalcon\Db\Adapter\Pdo\Mysql;

/**
 *
 */
class Initialize extends Update
{
    protected $version = F_VERSION;

    protected $urgency = false;

    private function getSql(): string
    {
        $sql_query = "";
        if($this->version == "1.0.2") {
//            $f_sql_query = "ALTER TABLE `fpanel`.`client_limits_permissions` ADD COLUMN `aspnet_core` enum('true','false') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'false' AFTER `asp`;";
//            $s_sql_query = "ALTER TABLE `fpanel`.`serviceplans` ADD COLUMN `aspnet_core` enum('true','false') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'false' AFTER `asp`;";
//            $sql_query = $f_sql_query . $s_sql_query;
        }
        return $sql_query;
    }

    protected function initUpdate(): bool
    {
        try {
            $connection = new Mysql(array(
                'host' => '127.0.0.1',
                'username' => USER_SQL_ADMIN,
                'password' => PANEL_SQL_PASS,
                'port' => '3306'
            ));
            $sql_query = $this->getSql();

            if(!$connection->connect()) {
                throw new Exception("\n<span>Could not connect</span>\n");
            }
            if($sql_query != null) {
                $connection->execute($sql_query);
            }
            $connection->close();
        } catch (Exception $e) {
            $this->log->error([
                "File" => "Initialize::Update",
                "Error" => "InitUpdate execute query",
                "Data" => $e->getMessage()
            ]);
        }

        return true;
    }

    public function core($data): bool
    {
        return true;
    }
}