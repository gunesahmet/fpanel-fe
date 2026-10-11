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
 */namespace FPanel\Validations;

use FPanel\Models\SystemSettings;

trait General
{

    /**
     * <code>
     * $validators->Required(array(
     * array(
     * 'field' => 'username',
     * 'message' => 'This field is required',
     * 'item' => $post['username']
     * )
     * ))
     * </code>
     * @param array $elements
     * @return Validation
     */
    public function Required(array $elements = array()): Validation
    {
        foreach($elements as $element) {
            if(is_array($element['item'])) {
                if(empty($element['item'])) {
                    $this->error_status = true;
                    if(isset($element['message'])) {
                        $this->message[$element['field']] = $element['message'];
                    }
                    else {
                        $this->message[$element['field']] = __('This field is required');
                    }
                }
            }
            else {
                if(!is_numeric($element['item']) && empty((string)$element['item'])) {
                    $this->error_status = true;
                    if(isset($element['message'])) {
                        $this->message[$element['field']] = $element['message'];
                    }
                    else {
                        $this->message[$element['field']] = __('This field is required');
                    }
                }
            }
        }
        return $this;
    }

    public function Email($email, $field, $message = null): Validation
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid email address');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function Between($element, $field, $min, $max, $message = null): Validation
    {
        if(strlen($element) > $max) {
            $this->error_status = true;
        }
        if(strlen($element) < $min) {
            $this->error_status = true;
        }
        if($this->error_status) {
            if($message == null) {
                $this->message[$field] = __('Please select a value ') . "$min-$max";
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function MinMax($element, $field, $min, $max, $message = null): Validation
    {
        if($element > $max) {
            $this->error_status = true;
        }
        if($element < $min) {
            $this->error_status = true;
        }
        if($this->error_status) {
            if($message == null) {
                $this->message[$field] = __('Please select a value from ') . $min . __(' to ') . $max;
            }
            else {
                $this->message[$field] = $message;
            }
        }

        return $this;
    }

    public function Ip($ip, $field, $message = null): Validation
    {
        if(!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid ip address');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function IpTemplate($ip, $field, $message = null): Validation
    {
        if($ip == "[ip]") {
            return $this;
        }
        if(!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid ip address');
            }
            else {
                $this->message[$field] = $message;
            }
        }


        return $this;
    }

    public function IpOrDomain($item, $field, $message = null): Validation
    {
        $status = false;
        if(filter_var($item, FILTER_VALIDATE_IP)) {
           $status = true;
        }
        $status_domain = preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \. )*  #Subdomains
   							[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?            #Domain
   							\. [a-z]{2,6} $                               #Top-level domain
							/ix', $item);

        if($status_domain) {
           $status = true;
        }

        if(filter_var($item,FILTER_VALIDATE_URL)) {
            $status = true;
        }

        if($item == "localhost") {
            $status = true;
        }

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid server address');
            }
            else {
                $this->message[$field] = $message;
            }
        }

        return $this;
    }

    public function Domain($domain, $field, $message = null): Validation
    {
        $status = preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \. )*  #Subdomains
   							[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?            #Domain
   							\. [a-z]{2,6} $                               #Top-level domain
							/ix', $domain);

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid domain name');
            }
            else {
                $this->message[$field] = $message;
            }
        }

        if(strlen($domain) >= 200) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid domain name. Domain name can be max 200 characters.');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function Username($username, $field, $message = null, $len = "4"): Validation
    {
        $status = preg_match('/^[a-z0-9A-Z_.-]{'.$len .',16}$/', $username) && preg_match('/\w/', $username);

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid username');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function FtpUser($ftpuser, $field, $message = null): Validation
    {
        $status = preg_match('/^[a-z0-9A-Z]{4,32}$/', $ftpuser) && preg_match('/\w/', $ftpuser);

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid ftp username');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function DatabaseUser($ftpuser, $field, $message = null): Validation
    {
        $status = preg_match('/^[a-z0-9A-Z_-]{4,32}$/', $ftpuser) && preg_match('/\w/', $ftpuser);

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid database username');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function Password($password, $field, $message = null, $update = false): Validation
    {
        if($this->error_status) {
            return $this;
        }
        if($update) {
            if($password == null) {
                $this->message[$field] = __('Password can not be empty');
                return $this;
            }
        }
        if($password != null) {
            if(preg_match('/\s/', $password)) {
                $this->message[$field] = __('Password can not contains any space character');
                $this->error_status = true;
            }
        }

        $min = 5;
        $max = 32;

        $special_complexity = false;
        $numeric_complexity = false;
        $alpha_complexity = false;
        $error_message = "";
        if(!file_exists(ROOTPATH.'/.firstboot')) {
            $system_settings = SystemSettings::findFirst("key = 'password_str'");
            if($system_settings) {
                $params = json_decode($system_settings->value,true);

                if(isset($params['minimum'])) {
                    if($params['minimum'] != null) {
                        $min = $params['minimum'];
                    }
                }
                if(isset($params['maximum'])) {
                    if($params['maximum'] != null) {
                        $max = $params['maximum'];
                    }
                }
                if(isset($params['alpha'])) {
                    if($params['alpha'] != null) {
                        $alpha_complexity = $params['alpha'];
                    }
                }
                if(isset($params['special'])) {
                    if($params['special'] != null) {
                        $special_complexity = $params['special'];
                    }
                }
                if(isset($params['numeric'])) {
                    if($params['numeric'] != null) {
                        $numeric_complexity = $params['numeric'];
                    }
                }
            }
        }



        if(strlen($password) < $min) {
            $this->error_status = true;
            $error_message = $error_message . __('Password must be min %min% characters',['min' => $min]);
        }

        if(strlen($password) > $max) {
            $this->error_status = true;
            $error_message = $error_message . __('Password must be max 32 characters');
        }

        if($alpha_complexity) {
            if (!preg_match('/[a-zA-Z]+/', $password)) {
                $this->error_status = true;
                $error_message = $error_message . __('Password must be contain alpha characters');
            }
        }

        if($numeric_complexity) {
            if (!preg_match('/[0-9]+/', $password)) {
                $this->error_status = true;
                $error_message = $error_message . __('Password must be contain numeric characters');
            }
        }


        if($special_complexity) {
            if(!preg_match('#[^a-zA-Z0-9]#', $password)) {
                $this->error_status = true;
                $error_message = $error_message . __('Password must be contain special characters');
            }
        }


        if($this->error_status) {
            if($message == null) {
                $this->message[$field] = $error_message;
            }
            else {
                $this->message[$field] = $message;
            }
        }

        return $this;
    }

    public function Numeric($number, $field, $message = null): Validation
    {

        if(!is_numeric($number)) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('This value must be numeric');
            }
            else {
                $this->message[$field] = $message;
            }
        }


        return $this;
    }

    public function Confirm($element, $field, $with, $message = null): Validation
    {

        if($element != $with) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please confirm this value');
            }
            else {
                $this->message[$field] = $message;
            }
        }


        return $this;
    }

    public function ExclusionIn($element, $field, array $params = array(), $message = null): Validation
    {
        if($this->error_status) {
            return $this;
        }

        foreach($params as $param) {
            if($element == $param) {
                $this->error_status = true;
                if($message == null) {
                    $this->message[$field] = __('Please enter valid value');
                }
                else {
                    $this->message[$field] = $message;
                }
            }
        }


        return $this;
    }

    public function InclusionIn($element, $field, array $haystacks = array(), $message = null, $withkey = false): Validation
    {
        if($this->error_status) {
            return $this;
        }

        $status = false;
        if(!$withkey) {
            foreach($haystacks as $haystack) {
                if($element == $haystack) {
                    $status = true;
                }
            }
        }
        else {
            foreach($haystacks as $haystack => $key) {
                if($element == $haystack) {
                    $status = true;
                }
            }
        }

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please select one of from this values');
            }
            else {
                $this->message[$field] = $message;
            }
        }

        return $this;
    }

    public function InclusionInArray(array $elements, $field, array $params, $message = null): Validation
    {
        if($this->error_status) {
            return $this;
        }
        $status = false;

        if(!empty($params)) {
            foreach($params as $param) {
                foreach($elements as $element) {
                    if((string)$element === (string)$param) {
                        $status = true;
                        break;
                    }
                }
                if($status)
                    break;
            }
        }

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please select one of from this values');
            }
            else {
                $this->message[$field] = $message;
            }
        }

        return $this;
    }

    public function Directory($element, $field, $message = null): Validation
    {

        $status = false;
        if($element == null) {
            $status = true;
        }
        if (strpbrk($element, "\\?%*:|\"<>")) {
            $status = true;
        }

        if($status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter valid directory name');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;

    }

    public function Netmask($mask, $field, $message = null): Validation
    {

        $status = true;

        $netmask = ['16','17','18','19','20','21','22','23','24','25','26','27','28','29','30'];

        foreach($netmask as $index) {
            if($index == $mask)
                $status = false;
        }


//        if(preg_match('/(0|255)\.(0|255)\.(0|255)\.(0|255)/', $mask)){   // check if mask is either 0.0.0.0 or 255.255.255.255
//            $status = false;
//        }

        if($status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter valid netmask');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;

    }

    public function WithoutSpace($element, $field, $message = null): Validation
    {

        $status = false;
        if($element == null) {
            $status = true;
        }

        if(strpos($element, " ") !== false) {
            $status = true;
        }

        if(strlen($element) > 255) {
            $status = true;
        }

        if($status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter valid name');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function Clock($element, $field, $message = null): Validation
    {
        $status = false;
        if($element == null) {
            $status = true;
        }

        if(!preg_match("/(2[0-3]|[01][0-9]):[0-5][0-9]/", $element)) {
            $status = true;
        }

        if($status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter time');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;

    }

    public function Port($element, $field, $message = null): Validation
    {

        $status = false;
        if($element == null) {
            $status = true;
        }

        if($this->error_status) {
            return $this;
        }

        if($element < 1) {
            $status = true;
        }
        if($element > 65535) {
            $status = true;
        }

        if($status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter valid port number');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function MySQLDatabaseName($db_name, $field, $message = null): Validation
    {
        $status = preg_match('/^(?=.{4})(?!.{64})(?!.*(.)\1{2})\w[\w.-]+\w$/', $db_name) && preg_match('/\w/', $db_name);

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid database name');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

    public function MinMaxStr($element, $field, $min = 4, $max = 255, $message = null): Validation
    {
        if((strlen($element) < $min) || (strlen($element) > $max)) {
            $this->error_status = true;
        }

        if($this->error_status) {
            if($message == null) {
                $this->message[$field] = __("Please enter min. %min% or max. %max% characters", [ "min" => $min, "max" => $max]);
            }
            else {
                $this->message[$field] = $message;
            }
        }

        return $this;
    }

    public function WindowsHostname($hostname, $field, $message = null): Validation
    {
        if($this->error_status) {
            return $this;
        }

        $status = preg_match('/^[a-z0-9A-Z-]{3,16}$/', $hostname);

        if(!$status) {
            $this->error_status = true;
            if($message == null) {
                $this->message[$field] = __('Please enter a valid hostname. You can use a-z, A-Z, 0-9 and - characters');
            }
            else {
                $this->message[$field] = $message;
            }
        }
        return $this;
    }

}