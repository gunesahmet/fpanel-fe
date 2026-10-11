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
 */namespace FPanel\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Email;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;


/**
 * Class CertificateForm
 * @package FPanel\Forms
 */
class CertificateForm extends Form
{
	/**
	 * @param null $entity
	 * @param $options
	 */
	public function initialize($entity = null, $options)
	{

        switch($options["page"]) {
            case "request":
                $this->requestFormElements($options);
                break;
            case "install":
                $this->installFormElements($options);
                break;
        }
	}

	/**
	 * @param $options
	 */
	private function requestFormElements($options)
	{

		// Certificate Name
        $cert_name = new Text("certificate_name", array(
            "id" => "certificate_name",
            "name" => "certificate_name",
        ));

		// Certificate Use www
        $email = new Email("certificate_user_email", array(
            "id" => "certificate_user_email",
            "name" => "certificate_user_email",
            "value" => "admin@" . $options["domain_name"]
        ));

		// Certificate Domain Name
        $domain_name = new Text("certificate_domain_name", array(
            "id" => "certificate_domain_name",
            "name" => "certificate_domain_name",
            "value" => $options["domain_name"],
        ));

		// Certificate City
        $city = new Text("certificate_city", array(
            "id" => "certificate_city",
            "name" => "certificate_city",
        ));

		// Certificate State
        $state = new Text("certificate_state", array(
            "id" => "certificate_state",
            "name" => "certificate_state",
        ));

		// Certificate Country
        $certificate_country = new Select(
            "certificate_country",
            array(
                "AF" => "Afghanistan",
                "AX" => "Aland Islands",
                "AL" => "Albania",
                "DZ" => "Algeria",
                "AS" => "American Samoa",
                "AD" => "Andorra",
                "AO" => "Angola",
                "AI" => "Anguilla",
                "AQ" => "Antarctica",
                "AG" => "Antigua and Barbuda",
                "AR" => "Argentina",
                "AM" => "Armenia",
                "AW" => "Aruba",
                "AU" => "Australia",
                "AT" => "Austria",
                "AZ" => "Azerbaijan",
                "BS" => "Bahamas",
                "BH" => "Bahrain",
                "BD" => "Bangladesh",
                "BB" => "Barbados",
                "BY" => "Belarus",
                "BE" => "Belgium",
                "BZ" => "Belize",
                "BJ" => "Benin",
                "BM" => "Bermuda",
                "BT" => "Bhutan",
                "BO" => "Bolivia",
                "BA" => "Bosnia and Herzegovina",
                "BW" => "Botswana",
                "BV" => "Bouvet Island",
                "BR" => "Brazil",
                "VG" => "British Virgin Islands",
                "IO" => "British Indian Ocean Territory",
                "BN" => "Brunei Darussalam",
                "BG" => "Bulgaria",
                "BF" => "Burkina Faso",
                "BI" => "Burundi",
                "KH" => "Cambodia",
                "CM" => "Cameroon",
                "CA" => "Canada",
                "CV" => "Cape Verde",
                "KY" => "Cayman Islands",
                "CF" => "Central African Republic",
                "TD" => "Chad",
                "CL" => "Chile",
                "CN" => "China",
                "HK" => "Hong Kong, Special Administrative Region of China",
                "MO" => "Macao, Special Administrative Region of China",
                "CX" => "Christmas Island",
                "CC" => "Cocos (Keeling) Islands",
                "CO" => "Colombia",
                "KM" => "Comoros",
                "CG" => "Congo (Brazzaville)",
                "CD" => "Congo, Democratic Republic of the",
                "CK" => "Cook Islands",
                "CR" => "Costa Rica",
                "CI" => "Côte d\"Ivoire",
                "HR" => "Croatia",
                "CU" => "Cuba",
                "CY" => "Cyprus",
                "CZ" => "Czech Republic",
                "DK" => "Denmark",
                "DJ" => "Djibouti",
                "DM" => "Dominica",
                "DO" => "Dominican Republic",
                "EC" => "Ecuador",
                "EG" => "Egypt",
                "SV" => "El Salvador",
                "GQ" => "Equatorial Guinea",
                "ER" => "Eritrea",
                "EE" => "Estonia",
                "ET" => "Ethiopia",
                "FK" => "Falkland Islands (Malvinas)",
                "FO" => "Faroe Islands",
                "FJ" => "Fiji",
                "FI" => "Finland",
                "FR" => "France",
                "GF" => "French Guiana",
                "PF" => "French Polynesia",
                "TF" => "French Southern Territories",
                "GA" => "Gabon",
                "GM" => "Gambia",
                "GE" => "Georgia",
                "DE" => "Germany",
                "GH" => "Ghana",
                "GI" => "Gibraltar",
                "GR" => "Greece",
                "GL" => "Greenland",
                "GD" => "Grenada",
                "GP" => "Guadeloupe",
                "GU" => "Guam",
                "GT" => "Guatemala",
                "GG" => "Guernsey",
                "GN" => "Guinea",
                "GW" => "Guinea-Bissau",
                "GY" => "Guyana",
                "HT" => "Haiti",
                "HM" => "Heard Island and Mcdonald Islands",
                "VA" => "Holy See (Vatican City State)",
                "HN" => "Honduras",
                "HU" => "Hungary",
                "IS" => "Iceland",
                "IN" => "India",
                "ID" => "Indonesia",
                "IR" => "Iran, Islamic Republic of",
                "IQ" => "Iraq",
                "IE" => "Ireland",
                "IM" => "Isle of Man",
                "IL" => "Israel",
                "IT" => "Italy",
                "JM" => "Jamaica",
                "JP" => "Japan",
                "JE" => "Jersey",
                "JO" => "Jordan",
                "KZ" => "Kazakhstan",
                "KE" => "Kenya",
                "KI" => "Kiribati",
                "KP" => "Korea, Democratic People\"s Republic of",
                "KR" => "Korea, Republic of",
                "KW" => "Kuwait",
                "KG" => "Kyrgyzstan",
                "LA" => "Lao PDR",
                "LV" => "Latvia",
                "LB" => "Lebanon",
                "LS" => "Lesotho",
                "LR" => "Liberia",
                "LY" => "Libya",
                "LI" => "Liechtenstein",
                "LT" => "Lithuania",
                "LU" => "Luxembourg",
                "MK" => "Macedonia, Republic of",
                "MG" => "Madagascar",
                "MW" => "Malawi",
                "MY" => "Malaysia",
                "MV" => "Maldives",
                "ML" => "Mali",
                "MT" => "Malta",
                "MH" => "Marshall Islands",
                "MQ" => "Martinique",
                "MR" => "Mauritania",
                "MU" => "Mauritius",
                "YT" => "Mayotte",
                "MX" => "Mexico",
                "FM" => "Micronesia, Federated States of",
                "MD" => "Moldova",
                "MC" => "Monaco",
                "MN" => "Mongolia",
                "ME" => "Montenegro",
                "MS" => "Montserrat",
                "MA" => "Morocco",
                "MZ" => "Mozambique",
                "MM" => "Myanmar",
                "NA" => "Namibia",
                "NR" => "Nauru",
                "NP" => "Nepal",
                "NL" => "Netherlands",
                "AN" => "Netherlands Antilles",
                "NC" => "New Caledonia",
                "NZ" => "New Zealand",
                "NI" => "Nicaragua",
                "NE" => "Niger",
                "NG" => "Nigeria",
                "NU" => "Niue",
                "NF" => "Norfolk Island",
                "MP" => "Northern Mariana Islands",
                "NO" => "Norway",
                "OM" => "Oman",
                "PK" => "Pakistan",
                "PW" => "Palau",
                "PS" => "Palestinian Territory, Occupied",
                "PA" => "Panama",
                "PG" => "Papua New Guinea",
                "PY" => "Paraguay",
                "PE" => "Peru",
                "PH" => "Philippines",
                "PN" => "Pitcairn",
                "PL" => "Poland",
                "PT" => "Portugal",
                "PR" => "Puerto Rico",
                "QA" => "Qatar",
                "RE" => "Réunion",
                "RO" => "Romania",
                "RU" => "Russian Federation",
                "RW" => "Rwanda",
                "BL" => "Saint-Barthélemy",
                "SH" => "Saint Helena",
                "KN" => "Saint Kitts and Nevis",
                "LC" => "Saint Lucia",
                "MF" => "Saint-Martin (French part)",
                "PM" => "Saint Pierre and Miquelon",
                "VC" => "Saint Vincent and Grenadines",
                "WS" => "Samoa",
                "SM" => "San Marino",
                "ST" => "Sao Tome and Principe",
                "SA" => "Saudi Arabia",
                "SN" => "Senegal",
                "RS" => "Serbia",
                "SC" => "Seychelles",
                "SL" => "Sierra Leone",
                "SG" => "Singapore",
                "SK" => "Slovakia",
                "SI" => "Slovenia",
                "SB" => "Solomon Islands",
                "SO" => "Somalia",
                "ZA" => "South Africa",
                "GS" => "South Georgia and the South Sandwich Islands",
                "SS" => "South Sudan",
                "ES" => "Spain",
                "LK" => "Sri Lanka",
                "SD" => "Sudan",
                "SR" => "Suriname *",
                "SJ" => "Svalbard and Jan Mayen Islands",
                "SZ" => "Swaziland",
                "SE" => "Sweden",
                "CH" => "Switzerland",
                "SY" => "Syrian Arab Republic (Syria)",
                "TW" => "Taiwan, Republic of China",
                "TJ" => "Tajikistan",
                "TZ" => "Tanzania *, United Republic of",
                "TH" => "Thailand",
                "TL" => "Timor-Leste",
                "TG" => "Togo",
                "TK" => "Tokelau",
                "TO" => "Tonga",
                "TT" => "Trinidad and Tobago",
                "TN" => "Tunisia",
                "TR" => "Turkey",
                "TM" => "Turkmenistan",
                "TC" => "Turks and Caicos Islands",
                "TV" => "Tuvalu",
                "UG" => "Uganda",
                "UA" => "Ukraine",
                "AE" => "United Arab Emirates",
                "GB" => "United Kingdom",
                "US" => "United States of America",
                "UM" => "United States Minor Outlying Islands",
                "UY" => "Uruguay",
                "UZ" => "Uzbekistan",
                "VU" => "Vanuatu",
                "VE" => "Venezuela (Bolivarian Republic of)",
                "VN" => "Viet Nam",
                "VI" => "Virgin Islands, US",
                "WF" => "Wallis and Futuna Islands",
                "EH" => "Western Sahara",
                "YE" => "Yemen",
                "ZM" => "Zambia",
                "ZW" => "Zimbabwe"
            ),
            array(
                "id" => "certificate_country",
            ));

		// Certificate Company
        $company = new Text("certificate_company", array(
            "id" => "certificate_company",
            "name" => "certificate_company",
        ));

		// Certificate Department
        $department = new Text("certificate_department", array(
            "id" => "certificate_department",
            "name" => "certificate_department",
        ));

		// Certificate Key Size
        $key_size = new Select(
            "certificate_keysize",
            array(
                "2048"  => "2048",
                "4096"  => "4096",
            ),
            array(
                "id" => "certificate_keysize",
            ));


		$this->add($key_size);
        $this->add($company);
        $this->add($department);
        $this->add($certificate_country);
        $this->add($state);
        $this->add($city);
        $this->add($domain_name);
        $this->add($email);
        $this->add($cert_name);

    }

    private function installFormElements($options)
    {
        $ssl_data = $options["ssl_data"];

        $upload_crt_name = new Text("crt_name_file", array(
            "id" => "crt_name_file",
            "name" => "crt_name_file",
        ));
        $crt_name = new Text("crt_name_text", array(
            "id" => "crt_name_text",
            "name" => "crt_name_text",
        ));
        if($options["action"] != "upload") {
            $crt_name->setAttribute("value", $ssl_data["name"]);
            $crt_name->setAttribute("disabled","disabled");
        }

        $csr = new TextArea("csr", array(
            "id" => "csr",
            "name" => "csr",
            "rows" => "5",
        ));
        if($options["action"] != "upload") {
            $csr->setAttribute("value", $ssl_data["csr"]);
            $csr->setAttribute("disabled","disabled");
        }

        $pri_key = new TextArea("pri_key", array(
            "id" => "pri_key",
            "name" => "pri_key",
            "rows" => "5",
        ));
        if($options["action"] != "upload") {
            $pri_key->setAttribute("value", $ssl_data["private_key"]);
            $pri_key->setAttribute("disabled","disabled");
        }


        $crt = new TextArea("crt", array(
            "id" => "crt",
            "name" => "crt",
        ));

        if($options["action"] != "upload") {
            if($ssl_data["crt"] != "NULL") {
                if(isset($ssl_data["crt"])) {
                    $crt->setAttribute("value", $ssl_data["crt"]);
                }
            }
        }

        $ca_crt = new TextArea("ca_crt", array(
            "id" => "ca_crt",
            "name" => "ca_crt",
        ));

        if($options["action"] != "upload") {
            if($ssl_data["ca"] != "NULL") {
                if(isset($ssl_data["ca"])) {
                    $ca_crt->setAttribute("value", $ssl_data["ca"]);
                }
            }
        }

        $this->add($pri_key);
        $this->add($csr);
        $this->add($crt_name);
        $this->add($upload_crt_name);
        $this->add($ca_crt);
        $this->add($crt);

    }
}