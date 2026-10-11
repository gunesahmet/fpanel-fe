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
 */namespace FPanel\Session;
use Phalcon\Mvc\User\Component;

/**
 * Class Cookie
 * @package Kowengo\Session
 */
class Cookie extends Component
{
    private $encryption;
    private $encryption_key;
    private $encryption_iv;
    private $set_status = false;

    /**
     * Cookie constructor.
     *
     * @param null $secret_key string
     */
    public function __construct($secret_key = null, $secret_iv = null)
    {
        $this->encryption = true;
        $this->encryption_key = CRYPT_KEY;
        $this->encryption_iv = CRYPT_IV;
        if($secret_key != null) {
            $this->encryption_key = $secret_key;
        }
        if($secret_iv != null) {
            $this->encryption_iv = $secret_iv;
        }
    }

    /**
     * @param $key
     * @param $value
     * @param float|int $time
     * @param string $path
     * @return $this
     */
    public function set($key, $value, $time = (86400 * 30), string $path = "/"): Cookie // 86400 = 1 day
    {
        $expire = 0;
        if($time == null) {
            $expire = time() + $time;
        }
        $skey = $this->crypto->staticEncode($key, $this->encryption_key, $this->encryption_iv);
        $svalue = $this->crypto->staticEncode($value, $this->encryption_key, $this->encryption_iv);

        $status = setrawcookie($skey, $svalue, $expire , $path);
        $this->set_status = $status;
        return $this;
    }

    /**
     * @param null $key
     * @return array|bool|string
     */
    public function get($key = null)
    {
        if($key != null) {
            $encrypted_key = $this->crypto->staticEncode($key, $this->encryption_key, $this->encryption_iv);
            if($this->crypto->getStatus()) {
                return false;
            }
            if(isset($_COOKIE[$encrypted_key])) {
                return $this->crypto->staticDecode($_COOKIE[$encrypted_key], $this->encryption_key, $this->encryption_iv);
            }
            else
                return false;
        }
        else {
            $cleared_cookies = [];
            if(!empty($_COOKIE)) {
                foreach($_COOKIE as $key => $value) {
                    $decrypted_key =$this->crypto->staticDecode($key, $this->encryption_key, $this->encryption_iv);
                    $decrypted_value = $this->crypto->staticDecode($value, $this->encryption_key, $this->encryption_iv);
                    $cleared_cookies[$decrypted_key] = $decrypted_value;
                }
            }
            return $cleared_cookies;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function delete($key): bool
    {
        $default_cookies = $_COOKIE;
        if (isset($default_cookies[$this->crypto->staticEncode($key, $this->encryption_key, $this->encryption_iv)])) {
            setcookie($this->crypto->staticEncode($key, $this->encryption_key, $this->encryption_iv), "", (86400 * 30) , "/");
            return true;
        }
        return true;
    }

    /**
     * @param $key string
     * @return bool
     */
    public function has(string $key): bool
    {
        if (isset($_COOKIE[$this->crypto->staticEncode($key, $this->encryption_key, $this->encryption_iv)])) {
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function reset()
    {
        if (!empty($_COOKIE)) {
            foreach($_COOKIE as $key => $value) {
                $this->delete($key);
            }
        }
    }

    /**
     * @return bool
     */
    public function getSetStatus(): bool
    {
        return $this->set_status;
    }


}