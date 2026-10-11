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
 */namespace FPanel\Translate;

use Phalcon\Mvc\User\Component;

class Initialize extends Component
{
    private $founded_str = array();

    public function initLanguages($result, $lang)
    {
        $data = [];
        foreach ($result as $item) {
            $data[$item['key']] = $item['value'];
        }
        $content = json_encode($data,JSON_PRETTY_PRINT);

        $file = fopen(ROOTPATH . "/languages/$lang", "w");
        fwrite($file, $content);
        fclose($file);
    }

    public function synchronize()
    {
        $this->listsFiles();

        $result = $this->env->arrayUnique($this->founded_str,"key");
        $cdir = scandir(ROOTPATH . "/languages");
        foreach ($cdir as $value) {
            if (!in_array($value, array(".", ".."))) {
                $this->initLanguages($result, $value);
            }
        }
    }

    private function listsFiles($dir = ROOTPATH)
    {
        $cdir = scandir($dir);
        foreach ($cdir as $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIR_SEPARATOR . $value)) {
                    $this->listsFiles($dir . DIR_SEPARATOR . $value);
                }
                else {
                    if(strpos($value,'.php') !== false) {
                        $this->extractStrings($dir . DIR_SEPARATOR . $value);
                    }
                }
            }
        }
    }

    private function extractStrings($file)
    {
        if (is_file($file)) {
            if(strpos($file,'Initialize.php') !== false)
                return;
            if(strpos($file,'Translate.php') !== false)
                return;

            if(strpos($file,ROOTPATH . '/config/') !== false)
                return;

            $data = htmlentities(file_get_contents($file));

            foreach (preg_split("/((\r?\n)|(\r\n?))/", $data) as $line) {
                if (preg_match("/__\((.*)\)/", $line, $array)) {

                    if((strpos($array[0],'[') !== false)) {
                        $value = $this->getStringBetween($array[0], ',');
                        $value = str_replace("__(","", $value);
                        $value = str_replace("',","'", $value);
                    }
                    else {
                        $value = $this->getStringBetween($array[0], "')");
                    }

                    if(is_array($value)) {
                        $value = $value[0];
                    }

                    if ($value != null) {
                        $value = explode(')/*', $value);
                        $trimed = trim($this->stripQuotes($value[0]),'"');

                        if((isset($value[1])) && ($value[1] != null)) {
                            $result = $this->env->arrayValues($trimed,'key', $this->founded_str);
                            if ($result === false) {
                                $set_value = array(
                                    'key' => $trimed,
                                    'value' => trim($value[1]),
                                );

                                $this->founded_str[] = $set_value;
                            }
                        }
                        else {
                            if((isset($value[0])) && ($value[0] != null)) {

                                $result = $this->env->arrayValues($trimed,'key', $this->founded_str);

                                if ($result === false) {
                                    $set_value = array(
                                        'key' => $trimed,
                                        'value' => '',
                                    );
                                    $this->founded_str[] = $set_value;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function getStringBetween($string, $end)
    {
        $string = " ".$string;
        $ini = strpos($string, '__(');
        if ($ini == 0) return "";
        $ini += strlen('__(');
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    private function stripQuotes($string)
    {
        return preg_replace('/(^[\"\']|[\"\']$)/', '', $string);
    }
}