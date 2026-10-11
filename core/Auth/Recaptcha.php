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
 */namespace FPanel\Auth;
use FPanel\Library\Library;

/**
 * Class Recaptcha
 * @package FPanel\Auth
 */
class Recaptcha extends Library
{
    public function validate(): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $this->config->recaptcha->site_secret, 'response' => $this->getPost("token"))));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($response, true);
        $this->error_messages["results"] = $result;
        if($result["success"] === true) {
            if($result["score"] >= 0.5) {
                if($result["action"] == md5($this->crypto->bencode($this->session->getId())))
                    return true;
                else {
                    $this->log->error([
                        "File" => "Recaptcha",
                        "Error" => "Action validate failed " . $result["action"] . " not equal to " . $this->session->getId(),
                    ]);
                    return false;
                }
            }
            else {
                $this->log->error([
                    "File" => "Recaptcha",
                    "Error" => "Score failed " . $result["score"],
                ]);
                $this->error_messages["score"] = __("Recaptcha validation score failed. Your score is %score%", [ "score" => $result["score"]]);
                return false;
            }
        }
        else {
            $this->log->error([
                "File" => "Recaptcha",
                "Error" => "Verification failed " . $result["success"] . " type: " . gettype($result["success"])
            ]);
            $this->error_messages["score"] = $result["error-codes"];
            return false;
        }
    }
}