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

use Phalcon\Mvc\User\Component;


/**
 * Class Parser
 * @package FPanel\Environments
 */
class Parser extends Component
{

    public $file = NULL;
    public $data = array();
    private $content = null;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $ini_array = @parse_ini_file($this->file, true);
        $this->data = $ini_array;
    }

    /**
     * @param null $section
     * @param null $key
     * @return array
     */
    public function read($section = null, $key = null): array
    {
        if($section != null) {
            if($key != null) {
                return $this->data[$section][$key];
            }
            else {
                return $this->data[$section];
            }
        }
        else {
            return $this->data;
        }
    }

    private function init()
    {
        foreach($this->data as $section => $keys_values) {
            $this->content .= "[$section]" . PHP_EOL;
            if(!empty($keys_values)) {
                foreach($keys_values as $key => $values) {
                    if(is_array($values)) {
                        if(!empty($values)) {
                            foreach ($values as $value) {
                                $this->content .= $key . "[] = $value" . PHP_EOL;
                            }
                        }
                        else {
                            $this->content .= $key . " = " . PHP_EOL;
                        }
                    }
                    else {
                        $this->content .= "$key = $values" . PHP_EOL;
                    }
                }
            }
            $this->content .= PHP_EOL;
        }
    }

    /**
     * write
     */
    public function write()
    {
        $this->init();
//        $this->debug->debug([
//            $this->content,
//            $this->data
//        ], false);
        @file_put_contents($this->file, $this->content);
    }
}