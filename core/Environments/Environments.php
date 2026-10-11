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

use FPanel\Models\Domains;
use FPanel\Models\Serviceplans;
use FPanel\Models\Users;
use Phalcon\Mvc\User\Component;

class Environments extends Component
{
    public $post;
    use File,
        Check,
        Dns,
        Core,
        Convert,
        Config,
        Servers,
        Timezones,
        Extract,
        Domain,
        User,
        Connection;

    public function createDates($created_time, $time_limit, bool $day = false): array
    {

        if (is_numeric($time_limit)) {
            if($day == true) {
                if($time_limit != "1") {
                    $time = "+".$time_limit . " days";
                }
                else {
                    $time = "+".$time_limit . " day";
                }
            }
            else {
                if($this->post['expire_time_limit'] != "1") {
                    $time = "+".$time_limit . " years";
                }
                else {
                    $time = "+".$time_limit . " year";
                }
            }
            $expire_date = strtotime($time, $created_time);
        }
        else {
            $expire_date = UNLIMITED;
        }
        $date = array();

        $date['created_date'] = $created_time;
        $date['expire_date'] = $expire_date;

        return $date;
    }

    public function getCountryList(): array
    {
        return array(
            "Afghanistan" => "Afghanistan",
            "Albania" => "Albania",
            "Algeria" => "Algeria",
            "American Samoa" => "American Samoa",
            "Andorra" => "Andorra",
            "Angola" => "Angola",
            "Anguilla" => "Anguilla",
            "Antarctica" => "Antarctica",
            "Argentina" => "Argentina",
            "Armenia" => "Armenia",
            "Aruba" => "Aruba",
            "Australia" => "Australia",
            "Austria" => "Austria",
            "Azerbaijan" => "Azerbaijan",
            "Bahamas" => "Bahamas",
            "Bahrain" => "Bahrain",
            "Bangladesh" => "Bangladesh",
            "Barbados" => "Barbados",
            "Belarus" => "Belarus",
            "Belgium" => "Belgium",
            "Belize" => "Belize",
            "Benin" => "Benin",
            "Bermuda" => "Bermuda",
            "Bhutan" => "Bhutan",
            "Bolivia" => "Bolivia",
            "Bosnia and Herzegowina" => "Bosnia and Herzegowina",
            "Botswana" => "Botswana",
            "Bouvet Island" => "Bouvet Island",
            "Brazil" => "Brazil",
            "British Indian Ocean Territory" => "British Indian Ocean Territory",
            "Brunei Darussalam" => "Brunei Darussalam",
            "Bulgaria" => "Bulgaria",
            "Burkina Faso" => "Burkina Faso",
            "Burundi" => "Burundi",
            "Cambodia" => "Cambodia",
            "Cameroon" => "Cameroon",
            "Canada" => "Canada",
            "Cape Verde" => "Cape Verde",
            "Cayman Islands" => "Cayman Islands",
            "Central African Republic" => "Central African Republic",
            "Chad" => "Chad",
            "Chile" => "Chile",
            "China" => "China",
            "Christmas Island" => "Christmas Island",
            "Cocos (Keeling) Islands" => "Cocos (Keeling) Islands",
            "Colombia" => "Colombia",
            "Comoros" => "Comoros",
            "Congo" => "Congo",
            "Congo, the Democratic Republic of the" => "Congo, the Democratic Republic of the",
            "Cook Islands" => "Cook Islands",
            "Costa Rica" => "Costa Rica",
            "Cote d'Ivoire" => "Cote d'Ivoire",
            "Croatia (Hrvatska)" => "Croatia (Hrvatska)",
            "Cuba" => "Cuba",
            "Cyprus" => "Cyprus",
            "Czech Republic" => "Czech Republic",
            "Denmark" => "Denmark",
            "Djibouti" => "Djibouti",
            "Dominica" => "Dominica",
            "Dominican Republic" => "Dominican Republic",
            "Ecuador" => "Ecuador",
            "Egypt" => "Egypt",
            "El Salvador" => "El Salvador",
            "Equatorial Guinea" => "Equatorial Guinea",
            "Eritrea" => "Eritrea",
            "Estonia" => "Estonia",
            "Ethiopia" => "Ethiopia",
            "Falkland Islands (Malvinas)" => "Falkland Islands (Malvinas)",
            "Faroe Islands" => "Faroe Islands",
            "Fiji" => "Fiji",
            "Finland" => "Finland",
            "France" => "France",
            "French Guiana" => "French Guiana",
            "French Polynesia" => "French Polynesia",
            "French Southern Territories" => "French Southern Territories",
            "Gabon" => "Gabon",
            "Gambia" => "Gambia",
            "Georgia" => "Georgia",
            "Germany" => "Germany",
            "Ghana" => "Ghana",
            "Gibraltar" => "Gibraltar",
            "Greece" => "Greece",
            "Greenland" => "Greenland",
            "Grenada" => "Grenada",
            "Guadeloupe" => "Guadeloupe",
            "Guam" => "Guam",
            "Guatemala" => "Guatemala",
            "Guinea" => "Guinea",
            "Guinea-Bissau" => "Guinea-Bissau",
            "Guyana" => "Guyana",
            "Haiti" => "Haiti",
            "Heard and Mc Donald Islands" => "Heard and Mc Donald Islands",
            "Holy See (Vatican City State)" => "Holy See (Vatican City State)",
            "Honduras" => "Honduras",
            "Hong Kong" => "Hong Kong",
            "Hungary" => "Hungary",
            "Iceland" => "Iceland",
            "India" => "India",
            "Indonesia" => "Indonesia",
            "Iran (Islamic Republic of)" => "Iran (Islamic Republic of)",
            "Iraq" => "Iraq",
            "Ireland" => "Ireland",
            "Israel" => "Israel",
            "Italy" => "Italy",
            "Jamaica" => "Jamaica",
            "Japan" => "Japan",
            "Jordan" => "Jordan",
            "Kazakhstan" => "Kazakhstan",
            "Kenya" => "Kenya",
            "Kiribati" => "Kiribati",
            "Korea, Democratic People's Republic of" => "Korea, Democratic People's Republic of",
            "Korea, Republic of" => "Korea, Republic of",
            "Kuwait" => "Kuwait",
            "Kyrgyzstan" => "Kyrgyzstan",
            "Lao People's Democratic Republic" => "Lao People's Democratic Republic",
            "Latvia" => "Latvia",
            "Lebanon" => "Lebanon",
            "Lesotho" => "Lesotho",
            "Liberia" => "Liberia",
            "Libyan Arab Jamahiriya" => "Libyan Arab Jamahiriya",
            "Liechtenstein" => "Liechtenstein",
            "Lithuania" => "Lithuania",
            "Luxembourg" => "Luxembourg",
            "Macau" => "Macau",
            "Macedonia, The Former Yugoslav Republic of" => "Macedonia, The Former Yugoslav Republic of",
            "Madagascar" => "Madagascar",
            "Malawi" => "Malawi",
            "Malaysia" => "Malaysia",
            "Maldives" => "Maldives",
            "Mali" => "Mali",
            "Malta" => "Malta",
            "Marshall Islands" => "Marshall Islands",
            "Martinique" => "Martinique",
            "Mauritania" => "Mauritania",
            "Mauritius" => "Mauritius",
            "Mayotte" => "Mayotte",
            "Mexico" => "Mexico",
            "Micronesia, Federated States of" => "Micronesia, Federated States of",
            "Moldova, Republic of" => "Moldova, Republic of",
            "Monaco" => "Monaco",
            "Mongolia" => "Mongolia",
            "Montserrat" => "Montserrat",
            "Morocco" => "Morocco",
            "Mozambique" => "Mozambique",
            "Myanmar" => "Myanmar",
            "Namibia" => "Namibia",
            "Nauru" => "Nauru",
            "Nepal" => "Nepal",
            "Netherlands" => "Netherlands",
            "Netherlands Antilles" => "Netherlands Antilles",
            "New Caledonia" => "New Caledonia",
            "New Zealand" => "New Zealand",
            "Nicaragua" => "Nicaragua",
            "Niger" => "Niger",
            "Nigeria" => "Nigeria",
            "Niue" => "Niue",
            "Norfolk Island" => "Norfolk Island",
            "Northern Mariana Islands" => "Northern Mariana Islands",
            "Norway" => "Norway",
            "Oman" => "Oman",
            "Pakistan" => "Pakistan",
            "Palau" => "Palau",
            "Panama" => "Panama",
            "Papua New Guinea" => "Papua New Guinea",
            "Paraguay" => "Paraguay",
            "Peru" => "Peru",
            "Philippines" => "Philippines",
            "Pitcairn" => "Pitcairn",
            "Poland" => "Poland",
            "Portugal" => "Portugal",
            "Puerto Rico" => "Puerto Rico",
            "Qatar" => "Qatar",
            "Reunion" => "Reunion",
            "Romania" => "Romania",
            "Russian Federation" => "Russian Federation",
            "Rwanda" => "Rwanda",
            "Saint Kitts and Nevis" => "Saint Kitts and Nevis",
            "Saint LUCIA" => "Saint LUCIA",
            "Saint Vincent and the Grenadines" => "Saint Vincent and the Grenadines",
            "Samoa" => "Samoa",
            "San Marino" => "San Marino",
            "Sao Tome and Principe" => "Sao Tome and Principe",
            "Saudi Arabia" => "Saudi Arabia",
            "Senegal" => "Senegal",
            "Seychelles" => "Seychelles",
            "Sierra Leone" => "Sierra Leone",
            "Singapore" => "Singapore",
            "Slovakia (Slovak Republic)" => "Slovakia (Slovak Republic)",
            "Slovenia" => "Slovenia",
            "Solomon Islands" => "Solomon Islands",
            "Somalia" => "Somalia",
            "South Africa" => "South Africa",
            "South Georgia and the South Sandwich Islands" => "South Georgia and the South Sandwich Islands",
            "Spain" => "Spain",
            "Sri Lanka" => "Sri Lanka",
            "St. Helena" => "St. Helena",
            "St. Pierre and Miquelon" => "St. Pierre and Miquelon",
            "Sudan" => "Sudan",
            "Suriname" => "Suriname",
            "Svalbard and Jan Mayen Islands" => "Svalbard and Jan Mayen Islands",
            "Swaziland" => "Swaziland",
            "Sweden" => "Sweden",
            "Switzerland" => "Switzerland",
            "Syrian Arab Republic" => "Syrian Arab Republic",
            "Taiwan, Province of China" => "Taiwan, Province of China",
            "Tajikistan" => "Tajikistan",
            "Tanzania, United Republic of" => "Tanzania, United Republic of",
            "Thailand" => "Thailand",
            "Togo" => "Togo",
            "Tokelau" => "Tokelau",
            "Tonga" => "Tonga",
            "Trinidad and Tobago" => "Trinidad and Tobago",
            "Tunisia" => "Tunisia",
            "Turkey" => "Turkey",
            "Turkmenistan" => "Turkmenistan",
            "Turks and Caicos Islands" => "Turks and Caicos Islands",
            "Tuvalu" => "Tuvalu",
            "Uganda" => "Uganda",
            "Ukraine" => "Ukraine",
            "United Arab Emirates" => "United Arab Emirates",
            "United Kingdom" => "United Kingdom",
            "United States" => "United States",
            "United States Minor Outlying Islands" => "United States Minor Outlying Islands",
            "Uruguay" => "Uruguay",
            "Uzbekistan" => "Uzbekistan",
            "Vanuatu" => "Vanuatu",
            "Venezuela" => "Venezuela",
            "Viet Nam" => "Viet Nam",
            "Virgin Islands (British)" => "Virgin Islands (British)",
            "Virgin Islands (U.S.)" => "Virgin Islands (U.S.)",
            "Wallis and Futuna Islands" => "Wallis and Futuna Islands",
            "Western Sahara" => "Western Sahara",
            "Yemen" => "Yemen",
            "Zambia" => "Zambia",
            "Zimbabwe" => "Zimbabwe",
        );
    }

    public function strAfter($string, $substring): string
    {
        $pos = strpos($string, $substring);
        if ($pos === false)
            return $string;
        else
            return(substr($string, $pos+strlen($substring)));
    }

    public function unlink($file)
    {
        if(PANEL_OS_TYPE == WINDOWS) {
            $lines = array();
            exec("DEL /F/Q \"$file\"", $lines, $deleteError);
        }
        else {
            unlink($file);
        }
    }

    public function searchArray($value, $key, &$array): array
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }
            foreach ($array as $subarray) {
                $results = array_merge($results, $this->searchArray($value, $key, $subarray));
            }
        }

        return $results;
    }

    public function searchArrayWithRegex($pattern, $key, &$array): array
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && preg_match("/$pattern/", $array[$key])) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->searchArrayWithRegex($pattern, $key, $subarray));
            }
        }

        return $results;
    }

    public function arrayValues($needle, $array_key, $haystack): bool
    {
        if(isset($haystack[$array_key])) {
            if($haystack[$array_key] == "Lowest (50)") {
                $this->debug->firebug($haystack[$array_key]);
            }
            if($haystack[$array_key] == $needle) {
                return $haystack[$array_key];
            }
        }
        foreach ($haystack as $value) {
            if(is_array($value)) {
                return $this->arrayValues($needle, $array_key, $value);
            }
        }
        return false;
    }

    public function arrayUnique($array, $key): array
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    public function getMessages()
    {
        if($this->flashSession->has("success")) {
            foreach($this->flashSession->getMessages("success", false) as $message) {
                echo '<div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        '. $message .'
                   </div>';
            }
        }

        if($this->flashSession->has("warning")) {
            foreach($this->flashSession->getMessages("warning", false) as $message) {
                echo '<div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                '. $message .'
                </div>';
            }
        }
        if($this->flashSession->has("error")) {
            foreach($this->flashSession->getMessages("error", false) as $message) {
                echo '<div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                '. $message .'
                </div>';
            }
        }
    }

    public function generateCsrf(int $length = 32): string
    {
        $chrDb  =   array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
        $code = '';
        for ($count = 0; $count < $length; $count++){

            $chr = $chrDb[rand(0,count($chrDb)-1)];

            if (rand(0,1) == 0){
                $chr = strtolower($chr);
            }
            $code .= $chr;
        }
        return $code;
    }

    public function checkCsrf(): bool
    {
        $csrf_key = $this->auth->getCsrfKey();
        $csrf_token = $this->auth->getCsrfToken();
        if($this->request->getPost($csrf_key) == $csrf_token)
            return true;
        else
            return false;
    }

    public function generateCsrfTokenKey()
    {
        if($this->auth->getCsrfToken() == null) {
            $this->auth->setCsrfToken();
        }
        if($this->auth->getCsrfKey() == null) {
            $this->auth->setCsrfKey();
        }
    }

    public function replaceAll($old, $new, $str): string
    {
        $str = str_replace($old, $new, $str);
        if(strpos($str, $old) !== false)
            return $this->replaceAll($old, $new, $str);
        return $str;
    }

    public function getRawPost($key = null, $default_value = null, string $filter = "striptags")
    {
        $raw_body = $this->request->getRawBody();
        if(!$this->isJson($raw_body)) {
            $raw_body = $this->request->getPost();
        }
        else {
            $raw_body = json_decode(json_encode($this->request->getJsonRawBody()), true);
        }
        return $this->parsePost($key, $raw_body, $filter, $default_value);
    }

    private function parsePost($key, $raw_body, $filter, $default_value)
    {
        if($key == null) {
            $result = [];
            foreach ($raw_body as $key => $value) {
                if(!is_array($value)) {
                    $result[$key] = $this->filter->sanitize($value, $filter);
                }
                else {
                    foreach ($value as $item_key => $item_value) {
                        if(is_array($item_value)) {
                            foreach ($item_value as $it_key => $it_value) {
                                $result[$key][$item_key][$it_key] = $this->filter->sanitize($it_value, $filter);
                            }
                        }
                        else {
                            $result[$key][$item_key] = $this->filter->sanitize($item_value, $filter);
                        }
                    }
                }
            }
            return $result;
        }
        else {
            $var = $raw_body[$key];
            if($var == null)
                return $default_value;
            else {
                if($filter == null)
                    return $var;
                else {
                    return $this->filter->sanitize($var, $filter);
                }
            }
        }
    }

    public function parseIniString($str) {

        if(empty($str)) return false;
        $str = str_replace("\r", "", $str);
        $lines = explode("\n", $str);
        $ret = [];
        $inside_section = false;

        foreach($lines as $line) {
            $line = trim($line);
            if(!$line || $line[0] == "#" || $line[0] == ";") continue;
            if($line[0] == "[" && $endIdx = strpos($line, "]")){
                $inside_section = substr($line, 1, $endIdx-1);
                continue;
            }
            if(!strpos($line, '=')) continue;
            $tmp = explode("=", $line, 2);
            if($inside_section) {
                $key = rtrim($tmp[0]);
                $value = ltrim($tmp[1]);
                if(preg_match("/^\".*\"$/", $value) || preg_match("/^'.*'$/", $value)) {
                    $value = mb_substr($value, 1, mb_strlen($value) - 2);
                }
                preg_match("^\[(.*?)\]^", $key, $matches);
                if(!empty($matches) && isset($matches[0])) {
                    $arr_name = preg_replace('#\[(.*?)\]#is', '', $key);
                    if(!isset($ret[$inside_section][$arr_name]) || !is_array($ret[$inside_section][$arr_name])) {
                        $ret[$inside_section][$arr_name] = array();
                    }
                    if(isset($matches[1]) && !empty($matches[1])) {
                        $ret[$inside_section][$arr_name][$matches[1]] = $value;
                    }
                    else {
                        $ret[$inside_section][$arr_name][] = $value;
                    }
                }
                else {
                    if($inside_section == "PHP") {
                        if(!isset($ret[$inside_section]["extension"])) {
                            $ret[$inside_section]["extension"] = [];
                        }
                    }

                    if((preg_match("/extension.*=/", $line)) && ($key != "extension_dir")) {
                        if(strpos($value, ";") !== false) {
                            $value = explode(";", $value)[0];
                        }
                        $ret[$inside_section]["extension"][] = trim($value);
                    }
                    else {
                        $ret[$inside_section][trim($tmp[0])] = $value;
                    }
                }
            }
            else {
                $ret[trim($tmp[0])] = ltrim($tmp[1]);
            }
        }
        return $ret;
    }

    public function createUuid(): string
    {
        static $guid = '';
        $uid = uniqid("", TRUE);
        $data = microtime();

        $data .= $_SERVER['REQUEST_TIME'];
        $data .= time();
//		$data .= $_SERVER['LOCAL_ADDR'];
//		$data .= $_SERVER['LOCAL_PORT'];
        if((php_sapi_name() == 'cli' ))
            $data .= "127.0.0.1";
        else
            $data .= $_SERVER['REMOTE_ADDR'];

        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        return '' .
            substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12) .
            '';
    }

    public function checkDomainOwner($domain_name, $client_id, $domain_id = null): bool
    {
        $users = Users::findFirstById($client_id);

        if($users->type <= ADMIN_USER) {
            return true;
        }

        if($domain_id != null)
            $condition = "name = '$domain_name' and id = '$domain_id'";
        else
            $condition = "name = '$domain_name'";
        $domain = Domains::find($condition);
        if(!$domain) {
            return false;
        }

        $client_domain = Domains::findFirst("name = '$domain_name' AND client_id = '$client_id'");
        if(!$client_domain) {
            $clients = Users::pluck("username", "id",["parent = '$client_id' AND type != '4'"]);
            if(empty($clients))
                return false;

            if(in_array($client_id, $clients)) {
                return true;
            }
        }
        return true;
    }

    public function checkServiceplanOwner($serviceplan_name, $client_id): bool
    {
        $users = Users::findFirstById($client_id);
        if($users->type <= ADMIN_USER) {
            return true;
        }

        $condition = "name = '$serviceplan_name'";
        $sp = Serviceplans::find($condition);
        if(!$sp) {
            return false;
        }

        $client_sp = Serviceplans::findFirst("name = '$serviceplan_name' AND client_id = '$client_id'");
        if(!$client_sp) {
            $clients = Users::pluck("username", "id",["parent = '$client_id' AND type != '4'"]);
            if(empty($clients))
                return false;

            if(in_array($client_id, $clients)) {
                return true;
            }
        }
        return true;
    }

    public function unserializeSession($session_data, $start_index = 0, &$dict = null)
    {
        isset($dict) or $dict = array();

        $name_end = strpos($session_data, SESSION_DELIM, $start_index);

        if ($name_end !== FALSE) {
            $name = substr($session_data, $start_index, $name_end - $start_index);
            $rest = substr($session_data, $name_end + 1);

            $value = unserialize($rest);      // PHP will unserialize up to "|" delimiter.
            $dict[$name] = $value;

            return $this->unserializeSession($session_data, $name_end + 1 + strlen(serialize($value)), $dict);
        }

        return $dict;
    }

    public function getOs($os): string
    {
        return strpos(strtolower($os), WINDOWS) !== false ? WINDOWS :  LINUX;
    }
}