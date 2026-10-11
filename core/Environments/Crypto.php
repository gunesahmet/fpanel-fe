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
use Phalcon\Crypt;
use Phalcon\Mvc\User\Component;

class Crypto extends Component
{
    private $fail = false;

    /**
     * @param $string
     * @return bool|string
     */
    public function encryptSession($string)
    {
        try {
            $sess_key = trim(mb_substr(md5($this->session->getId()), 0, 16));
            $sess_key = preg_replace('/[[:cntrl:]]/', '', $sess_key);
            $sess_key = preg_replace('/\0/', '', $sess_key);
            $sess_key = preg_replace('/:/', '', $sess_key);
            $encoded = $this->safeB64Encode($this->crypt->encrypt($string, $sess_key));
            $this->fail = false;
            return trim($encoded);
        }
        catch (Exception $e) {
            $this->fail = true;
            return false;
        }
    }

    /**
     * @param $string
     * @return bool|string
     */
    public function decryptSession($string)
    {
        try {
//            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $sess_key = trim(mb_substr(md5($this->session->getId()), 0, 16));
            $sess_key = preg_replace('/[[:cntrl:]]/', '', $sess_key);
            $sess_key = preg_replace('/\0/', '', $sess_key);
            $sess_key = preg_replace('/:/', '', $sess_key);

            $decoded = $this->crypt->decrypt($this->safeB64Decode($string), $sess_key);
            $this->fail = false;
            return trim($decoded);
        }
        catch (Exception $e) {
            $this->fail = true;
            return false;
        }
    }

    /**
     * @param $string
     * @param string $secret_iv
     * @return false|string
     */
    public function encode($string, string $secret_iv = CRYPT_IV)
    {
        try {
            $encoded = $this->safeB64Encode($this->crypt->encrypt($string, mb_substr($secret_iv, 0, 16)));
            $this->fail = false;
            return trim($encoded);
        }
        catch (Exception $e) {
            $this->fail = true;
            return false;
        }
    }

    /**
     * @param $string
     * @param string $secret_iv
     * @return bool|string
     */
    public function decode($string, string $secret_iv = CRYPT_IV)
    {
        try {
            $decoded = $this->crypt->decrypt($this->safeB64Decode($string), mb_substr($secret_iv, 0, 16));
            $this->fail = false;
            return trim($decoded);
        }
        catch (Exception $e) {
            $this->fail = true;
            return false;
        }
    }

    /**
     * @param $string
     * @param string $secret_key
     * @return array|string|string[]
     */
    public function staticEncode($string, string $secret_key = STATIC_KEY, string $secret_iv = STATIC_IV)
    {
        return $this->safeB64Encode(openssl_encrypt($string, "AES-256-CFB", $secret_key, OPENSSL_RAW_DATA, $secret_iv));
    }

    /**
     * @param $string
     * @param string $secret_key
     * @return false|string
     */
    public function staticDecode($string, string $secret_key = STATIC_KEY, string $secret_iv = STATIC_IV)
    {
        return openssl_decrypt($this->safeB64Decode($string), "AES-256-CFB", $secret_key, OPENSSL_RAW_DATA, $secret_iv);
    }

    /**
     * @param $string
     * @param string $secret_key
     * @param string $secret_iv
     * @return array|string|string[]
     */
    public function bencode($string, string $secret_key = CLI_USERNAME, string $secret_iv = CLI_PASSWORD)
    {
        return $this->safeB64Encode(openssl_encrypt($string, "AES-256-CFB", $secret_key, OPENSSL_RAW_DATA, mb_substr($secret_iv, 0, 16)));
    }

    /**
     * @param $string
     * @param string $secret_key
     * @param string $secret_iv
     * @return false|string
     */
    public function bdecode($string, string $secret_key = CLI_USERNAME, string $secret_iv = CLI_PASSWORD)
    {
        return openssl_decrypt($this->safeB64Decode($string), "AES-256-CFB", $secret_key, OPENSSL_RAW_DATA, mb_substr($secret_iv, 0, 16));
    }

    /**
     * @param $string
     * @return bool|string
     */
    public function fdecode($string)
    {
        try {
            $crypt = new Crypt();
            $crypt->setKey(STATIC_KEY);
            $data = str_replace(array('-','_'),array('+','/'), $string);
            $mod4 = strlen($data) % 4;
            if ($mod4) {
                $data .= substr('====', $mod4);
            }
            $decoded = $crypt->decryptBase64($data, mb_substr(STATIC_KEY, 0, 16));
            $this->fail = false;
            return trim($decoded);
        }
        catch (Exception $e) {
            $this->fail = true;
            return false;
        }
    }

    /**
     * @param $string
     * @return bool|string
     */
    public function fencode($string)
    {
        try {
            $crypt = new Crypt();
            $crypt->setKey(STATIC_KEY);
            $encoded = str_replace(array('+','/','='),array('-','_',''), $crypt->encryptBase64($string, mb_substr(STATIC_KEY, 0, 16)));
            $this->fail = false;
            return trim($encoded);
        }
        catch (Exception $e) {
            $this->fail = true;
            return false;
        }
    }

    /**
     * @param $string
     * @return array|string|string[]
     */
    public function safeB64Encode($string)
    {
        $data = base64_encode($string);
        return str_replace(array('+','/','='),array('-','_',''), $data);
    }

    /**
     * @param $string
     * @return false|string
     */
    public function safeB64Decode($string)
    {
        $data = str_replace(array('-','_'),array('+','/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->fail;
    }
}