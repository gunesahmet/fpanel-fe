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

trait File
{
    public function writeFile($file_path, $content)
    {
        try {
            $temp_file = fopen("$file_path", "w") or die("Unable to open file!");
            fwrite($temp_file, $content);
            fclose($temp_file);
            return true;
        }
        catch(Exception $e) {
            return false;
        }
    }

    public function readFile($file_path)
    {
        try {
            if(file_exists($file_path)) {
                return file_get_contents($file_path);
            }
            else {
                return false;
            }
        }
        catch(Exception $e) {
            return false;
        }
    }

}