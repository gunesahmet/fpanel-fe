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
use Phalcon\Mvc\User\Component;

class Url extends Component
{
    /**
     * @param $url
     * @param false $direct
     * @return string
     */
    public function url_encrypt($url, bool $direct = false): string
    {
        if (URL_CRYPT) {
            $converted = $this->encode($url);
        }
        else {
            $converted = "/".$url;
        }
        if($direct)
            return $converted;
        else
            return "#".$converted;
    }

    /**
     * @param $url
     * @return string
     */
    public function url_decrypt($url): string
    {
        $decrypted = $url;
        if (URL_CRYPT) {
            $decrypted = $this->decode($url);
        }
        return $decrypted;
    }

    /**
     * @param $value
     * @return bool|string|void
     */
    public function encode($value)
    {
        if(!$value){
            return "/";
        }
        try {
            $crypto = new Crypto();
            return $crypto->encryptSession($value);
        }
        catch(Exception $e) {
            print_r("URL encryption error");
            exit;
        }

    }

    /**
     * @param $value
     * @return string
     */
    public function decode($value): string
    {
        if(empty($value)) {
            return "/";
        }

        if($value == "favicon.ico") {
            exit(0);
        }

        try {
            if((!$value)) {
                return "/";
            }
            if((strpos($value, "language") !== false)) {
                return $value;
            }
            if($value == "index.php") {
                return $value;
            }
            if($this->session->has("identity") || $this->session->getId() != null) {
                $crypto = new Crypto();
                $decoded = $crypto->decryptSession(trim($value, "/"));
                $string = preg_replace('/[^\x00-\x7F]+/', '', $decoded);
                if($string == null) {
                    header("Location: /");
                    exit(0);
                }
            }
            else {
                header("Location: /");
                exit(0);
            }
            return trim($decoded);
        }
        catch(Exception $e) {
            print_r("URL decryption error");
            exit;
        }
    }

    /**
     * @param $string
     * @return array|string|string[]
     */
    public function safe_b64encode($string) {
        $data = base64_encode($string);
        return str_replace(array('+','/','='),array('-','_',''), $data);
    }

    /**
     * @param $string
     * @return false|string
     */
    public function safe_b64decode($string)
    {
        $data = str_replace(array('-','_'),array('+','/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}