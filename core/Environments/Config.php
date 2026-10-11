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

use Exception;

trait Config
{
    /**
     * @param $path
     * @return bool|array
     */
    public function readConfig($path)
    {
        if (file_exists($path)) {
            try{
                $file = fopen($path, "r");
                $crypted_data = fread($file,filesize($path));
                return json_decode(trim($this->crypto->bdecode($crypted_data)),true);
            }
            catch(Exception $e) {
                return false;
            }
        }
        else {
            return false;
        }

    }

    /**
     * @param $path
     * @param $data
     * @param $file
     * @return bool
     */
    public function writeConfig($path, $file, $data): bool
    {
        if (is_dir($path)) {
            $json = json_encode($data);
            $crypted_data = $this->crypto->bencode($json);
            $config_file = fopen($path .'/'. $file, "w");
            fwrite($config_file, $crypted_data);
            fclose($config_file);
            return true;
        }
        else {
            return false;
        }
    }
}