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
 */
use Phalcon\Di;

class Connections
{
    /**
     * @param $di Di
     * @throws Exception
     */
    public function loadServices(Di $di)
    {
        $postgresql = false;
        $mysql = false;
        if(file_exists(PANEL_DIR . "/etc/.sys.packages")) {
            $sys_pkg = file_get_contents(PANEL_DIR . "/etc/.sys.packages");
            $sys_pkg_content = explode("\n", $sys_pkg);
            foreach($sys_pkg_content as $sys_content) {
                if(strpos($sys_content,"postgresql") !== false) {
                    $postgresql = true;
                }
                if(strpos($sys_content,"mariadb") !== false) {
                    $mysql = true;
                }
                if(strpos($sys_content,"mysql") !== false) {
                    $mysql = true;
                }
            }
        }
        else {
            throw new Exception("\n<span>Invalid System Packages</span>\nMessage: Can't load system packages. Please contact with system administrator" ."\nLine Number: " . __LINE__ ."\n");
        }

        if(!file_exists(ROOTPATH."/.firstboot") || (file_exists(ROOTPATH . "/.update"))) {
            if($mysql) {
                $di->set("db", function() {
                    return  new Phalcon\Db\Adapter\Pdo\Mysql(array(
                        "host"     => "127.0.0.1",
                        "username" => USER_SQL_ADMIN,
                        "password" => PANEL_SQL_PASS,
                        "port" => "3306",
                        "dbname" => DATABASE_NAME,
                        "charset"     => "utf8",
                    ));
                });
            }
            else if($postgresql){
                $di->set("db", function () {
                    return new Phalcon\Db\Adapter\Pdo\Postgresql(array(
                        "host"     => "127.0.0.1",
                        "username" => USER_SQL_ADMIN,
                        "password" => PANEL_SQL_PASS,
                        "port" => "5432",
                        "dbname" => DATABASE_NAME,
                    ));
                });
            }
        }
    }
}