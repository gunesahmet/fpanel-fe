<php/*
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
 */namespace FPanel\Log;

class ErrorHandler
{
    public static function errorHandler($errNo, $errStr, $errFile, $errLine, $errContext = NULL): bool
    {

        if ($errNo != E_STRICT) {

            if (PHP_SAPI == 'cli') {
                $script = $_SERVER['PHP_SELF'];
                $ip = 'CLI';
            } else {
                $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $script = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                $ip = $_SERVER['REMOTE_ADDR'];
            }
//
//            $rt->title = $errStr;
//            $rt->file = $errFile;
//            $rt->line = $errLine;
//            $rt->error_type = $errNo;
//            $rt->server_name = php_uname('n');
//            $rt->execution_script = $script;
//            $rt->pid = getmypid();
//            $rt->ip_address = $ip;

        }

        return false;
    }


    public static function runtimeShutdown()
    {
        $e = error_get_last();
        if (!empty($e)) {

            if(PRODUCTION_STATUS == "DEBUG") {
                ErrorHandler::errorHandler($e['type'], $e['message'], $e['file'], $e['line']);
            }
            else {
                ErrorHandler::errorHandler($e['type'], $e['message'],null,null);
            }
        }
    }
}
