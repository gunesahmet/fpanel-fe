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
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class SystemTimeForm
 * @package FPanel\Forms
 */
class SystemTimeForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
    }

    /**
     * @param $options
     */
    private function formElements($options)
    {
        // Date
        $system_date = new Text("system_date", array(
            "id"    => "system_date",
            "name"  => "system_date",
            "value" => $options["system_date"],
        ));

        $system_time = new Text("system_time", array(
            "id"    => "system_time",
            "name"  => "system_time",
            "value" => $options["system_time"],
        ));

        if($options["server_os"] == LINUX) {
            $timezones = $this->env->php_timezone();
        }
        else {
            $timezones = array(
                "Dateline Standard Time" => "(UTC-12:00) International Date Line West ",
                "Samoa Standard Time" => "(UTC-11:00) Midway Island, Samoa ",
                "Hawaiian Standard Time" => "(UTC-10:00) Hawaii ",
                "Alaskan Standard Time" => "(UTC-09:00) Alaska ",
                "Pacific Standard Time" => "(UTC-08:00) Pacific Time (US & Canada) ",
                "Pacific Standard Time (Mexico)" => "(UTC-08:00) Tijuana, Baja California ",
                "US Mountain Standard Time" => "(UTC-07:00) Arizona ",
                "Mountain Standard Time (Mexico)" => "(UTC-07:00) Chihuahua, La Paz, Mazatlan ",
                "Mountain Standard Time" => "(UTC-07:00) Mountain Time (US & Canada) ",
                "Central America Standard Time" => "(UTC-06:00) Central America ",
                "Central Standard Time" => "(UTC-06:00) Central Time (US & Canada) ",
                "Central Standard Time (Mexico)" => "(UTC-06:00) Guadalajara, Mexico City, Monterrey ",
                "Canada Central Standard Time" => "(UTC-06:00) Saskatchewan ",
                "SA Pacific Standard Time" => "(UTC-05:00) Bogota, Lima, Quito ",
                "Eastern Standard Time" => "(UTC-05:00) Eastern Time (US & Canada) ",
                "US Eastern Standard Time" => "(UTC-05:00) Indiana (East) ",
                "Venezuela Standard Time" => "(UTC-04:30) Caracas ",
                "Paraguay Standard Time" => "(UTC-04:00) Asuncion ",
                "Atlantic Standard Time" => "(UTC-04:00) Atlantic Time (Canada) ",
                "SA Western Standard Time" => "(UTC-04:00) Georgetown, La Paz, San Juan ",
                "Central Brazilian Standard Time" => "(UTC-04:00) Manaus ",
                "Pacific SA Standard Time" => "(UTC-04:00) Santiago ",
                "Newfoundland Standard Time" => "(UTC-03:30) Newfoundland ",
                "E. South America Standard Time" => "(UTC-03:00) Brasilia ",
                "Argentina Standard Time" => "(UTC-03:00) Buenos Aires ",
                "SA Eastern Standard Time" => "(UTC-03:00) Cayenne ",
                "Greenland Standard Time" => "(UTC-03:00) Greenland ",
                "Montevideo Standard Time" => "(UTC-03:00) Montevideo ",
                "Mid-Atlantic Standard Time" => "(UTC-02:00) Mid-Atlantic ",
                "Azores Standard Time" => "(UTC-01:00) Azores ",
                "Cape Verde Standard Time" => "(UTC-01:00) Cape Verde Is. ",
                "Morocco Standard Time" => "(UTC) Casablanca ",
                "UTC" => "(UTC) Coordinated Universal Time ",
                "GMT Standard Time" => "(UTC) Dublin, Edinburgh, Lisbon, London ",
                "Greenwich Standard Time" => "(UTC) Monrovia, Reykjavik ",
                "W. Europe Standard Time" => "(UTC+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna ",
                "Central Europe Standard Time" => "(UTC+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague ",
                "Romance Standard Time" => "(UTC+01:00) Brussels, Copenhagen, Madrid, Paris ",
                "Central European Standard Time" => "(UTC+01:00) Sarajevo, Skopje, Warsaw, Zagreb ",
                "W. Central Africa Standard Time" => "(UTC+01:00) West Central Africa ",
                "Jordan Standard Time" => "(UTC+02:00) Amman ",
                "GTB Standard Time" => "(UTC+02:00) Athens, Bucharest, Istanbul ",
                "Middle East Standard Time" => "(UTC+02:00) Beirut ",
                "Egypt Standard Time" => "(UTC+02:00) Cairo ",
                "South Africa Standard Time" => "(UTC+02:00) Harare, Pretoria ",
                "FLE Standard Time" => "(UTC+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius ",
                "Israel Standard Time" => "(UTC+02:00) Jerusalem ",
                "E. Europe Standard Time" => "(UTC+02:00) Minsk ",
                "Namibia Standard Time" => "(UTC+02:00) Windhoek ",
                "Arabic Standard Time" => "(UTC+03:00) Baghdad ",
                "Arab Standard Time" => "(UTC+03:00) Kuwait, Riyadh ",
                "Russian Standard Time" => "(UTC+03:00) Moscow, St. Petersburg, Volgograd ",
                "E. Africa Standard Time" => "(UTC+03:00) Nairobi ",
                "Georgian Standard Time" => "(UTC+03:00) Tbilisi ",
                "Iran Standard Time" => "(UTC+03:30) Tehran ",
                "Arabian Standard Time" => "(UTC+04:00) Abu Dhabi, Muscat ",
                "Azerbaijan Standard Time" => "(UTC+04:00) Baku ",
                "Mauritius Standard Time" => "(UTC+04:00) Port Louis ",
                "Caucasus Standard Time" => "(UTC+04:00) Yerevan ",
                "Afghanistan Standard Time" => "(UTC+04:30) Kabul ",
                "Ekaterinburg Standard Time" => "(UTC+05:00) Ekaterinburg ",
                "Pakistan Standard Time" => "(UTC+05:00) Islamabad, Karachi ",
                "West Asia Standard Time" => "(UTC+05:00) Tashkent ",
                "India Standard Time" => "(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi ",
                "Sri Lanka Standard Time" => "(UTC+05:30) Sri Jayawardenepura ",
                "Nepal Standard Time" => "(UTC+05:45) Kathmandu ",
                "N. Central Asia Standard Time" => "(UTC+06:00) Almaty, Novosibirsk ",
                "Central Asia Standard Time" => "(UTC+06:00) Astana, Dhaka ",
                "Myanmar Standard Time" => "(UTC+06:30) Yangon (Rangoon) ",
                "SE Asia Standard Time" => "(UTC+07:00) Bangkok, Hanoi, Jakarta ",
                "North Asia Standard Time" => "(UTC+07:00) Krasnoyarsk ",
                "China Standard Time" => "(UTC+08:00) Beijing, Chongqing, Hong Kong, Urumqi ",
                "North Asia East Standard Time" => "(UTC+08:00) Irkutsk, Ulaan Bataar ",
                "Singapore Standard Time" => "(UTC+08:00) Kuala Lumpur, Singapore ",
                "W. Australia Standard Time" => "(UTC+08:00) Perth ",
                "Taipei Standard Time" => "(UTC+08:00) Taipei ",
                "Tokyo Standard Time" => "(UTC+09:00) Osaka, Sapporo, Tokyo ",
                "Korea Standard Time" => "(UTC+09:00) Seoul ",
                "Yakutsk Standard Time" => "(UTC+09:00) Yakutsk ",
                "Cen. Australia Standard Time" => "(UTC+09:30) Adelaide ",
                "AUS Central Standard Time" => "(UTC+09:30) Darwin ",
                "E. Australia Standard Time" => "(UTC+10:00) Brisbane ",
                "AUS Eastern Standard Time" => "(UTC+10:00) Canberra, Melbourne, Sydney ",
                "West Pacific Standard Time" => "(UTC+10:00) Guam, Port Moresby ",
                "Tasmania Standard Time" => "(UTC+10:00) Hobart ",
                "Vladivostok Standard Time" => "(UTC+10:00) Vladivostok ",
                "Central Pacific Standard Time" => "(UTC+11:00) Magadan, Solomon Is., New Caledonia ",
                "New Zealand Standard Time" => "(UTC+12:00) Auckland, Wellington ",
                "Fiji Standard Time" => "(UTC+12:00) Fiji, Marshall Is. ",
                "Kamchatka Standard Time" => "(UTC+12:00) Petropavlovsk-Kamchatsky ",
                "Tonga Standard Time" => "(UTC+13:00) Nuku\"alofa ",
            );
        }

        $time_zone = new Select("time_zone", $timezones,[
            "id"    => "time_zone",
            "name"  => "time_zone",
        ]);

        $time_zone->setDefault($options["timezone"]);

        $sync_time = new Check("sync_time", array(
            "id" => "sync_time",
            "name" => "sync_time",
        ));

        if(PANEL_OS_TYPE == LINUX) {
            $ntp_address = new Text("ntp_address", array(
                "id" => "ntp_address",
                "name" => "ntp_address",
                "value" => $options["ntp_address"],
            ));
        }
        else {
            $ntp_address = new Select("ntp_address", [
                "1" => "time.windows.com",
                "2" => "time.nist.gov",
                "3" => "time-nw.nist.gov",
                "4" => "time-a.nist.gov",
                "5" => "time-b.nist.gov",
            ], [
                "id" => "ntp_address",
                "name" => "ntp_address",
            ]);
            $ntp_address->setDefault($options["ntp_address"]);
        }


        // Compression
        if($options["sync_time"] == true) {
            $sync_time->setAttribute("checked", "checked");
        }

        $this->add($system_date);
        $this->add($system_time);
        $this->add($time_zone);
        $this->add($sync_time);
        $this->add($ntp_address);

    }



}