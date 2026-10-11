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
 */namespace FPanel\Backup;
use FPanel\Dropbox\Auth;
use FPanel\Dropbox\FileProperties;
use FPanel\Dropbox\FileRequests;
use FPanel\Dropbox\Files;
use FPanel\Dropbox\Misc;
use FPanel\Dropbox\Paper;
use FPanel\Dropbox\Sharing;
use FPanel\Dropbox\Users;

class Init
{

    private static $token;
    public $auth;
    public $files;
    public $file_properties;
    public $file_requests;
    public $paper;
    public $misc;
    public $sharing;
    public $users;
    public $app;
    public $client;

    public function __construct($accesstoken) {
        self::$token = $accesstoken;
        $this->auth = new Auth();
        $this->files = new Files();
        $this->file_properties = new FileProperties();
        $this->file_requests = new FileRequests();
        $this->paper = new Paper();
        $this->misc = new Misc();
        $this->sharing = new Sharing();
        $this->users = new Users();
    }

    /*
    * Main function for handling post requests.
    */
    public static function postRequest($endpoint, $headers, $data, $json = TRUE) {
        $ch = curl_init($endpoint);
        array_push($headers, "Authorization: Bearer " . self::$token);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $r = curl_exec($ch);
        curl_close($ch);

        if ($json)
            return json_decode($r, true);
        else
            return $r;
    }

    /*
    * Special case function for handling the from_oauth1 request
    */
    public static function oauth1Request($endpoint, $data, $app_key, $app_secret) {
        $ch = curl_init($endpoint);
        $headers = array("Content-Type: application/json");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERPWD, "$app_key:$app_secret");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $r = curl_exec($ch);
        curl_close($ch);
        return json_decode($r, true);
    }

    /*
    * Updates the access token.
    */
    public function updateAccessToken($accesstoken) {
        self::$token = $accesstoken;
    }
}
