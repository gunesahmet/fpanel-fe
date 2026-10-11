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

use DateInterval;
use DateTime;
use Exception;

trait Convert
{
    /**
     * @param $time
     * @param $day
     * @return array
     */
    public function expireDateForDay($time, $day): array
    {
        $created_date = $time;

        $date = new DateTime();
        $date->setTimestamp($time);

        $date->add(DateInterval::createFromDateString('+'. $day .' day'));

        $expire_date = $date->format('U');
        $date = array();
        $date['created_date'] = $created_date;
        $date['expire_date'] = $expire_date;

        return $date;
    }

    /**
     * @param $size
     * @param null $unlimited_str
     * @return string
     */
    public function convert($size, $unlimited_str = null): string
    {
        if (strtolower($size) != UNLIMITED) {
            if($size != null) {
                if($size > 0) {
                    $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
                    $power = floor(log($size, 1024));
                    $converted = number_format($size / pow(1024, $power), 2, ',', '');

                    $after_float = explode(',', $converted);
                    if(end($after_float) == "00") {
                        $result = str_replace(','.end($after_float),"", $converted);
                    }
                    else {
                        $result = $converted;
                    }

                    return $result . ' ' . $units[$power];
                }
                else {
                    return '0 B';
                }

            }
            else {
                return "0 MB";
            }
        }
        else {
            return ($unlimited_str == null  || $unlimited_str == UNLIMITED ? __('Unlimited') : $unlimited_str);
        }
    }

    /**
     * @param $seconds
     * @param bool $long
     * @return string|void
     */
    public function secondsToTime2($seconds, bool $long = true) {
        try {
            $dtF = new DateTime("@0");
            $dtT = new DateTime("@$seconds");
            if($long)
                return $dtF->diff($dtT)->format('%a '.__('Day(s)') .' %h '.__('Hour(s)') .' %i '.__('Minute(s)') .' '. __('and').' %s '.__('Second(s)'));
            else
                return $dtF->diff($dtT)->format(' %h '.__('h') .' %i '.__('m') .' %s '.__('s'));
        }
        catch (Exception $e) {

        }
    }

    /**
     * @param $seconds
     * @return string
     */
    public function secondsToTime($seconds): string
    {
        $date = "";
        $days = explode('.', $seconds / 86400)[0];
        $hours = explode('.',($seconds - ($days * 86400))/ 3600)[0];
        $minutes = explode('.',(($seconds - ($days * 86400) - ($hours * 3600)) / 60))[0];
        if($days >= 1)
            $date .= $days . " " . __('days') . " ";
        if($hours >= 1)
            $date .= $hours . " " . __('hours') . " ";
        if($minutes >= 1)
            $date .= $minutes . " " . __('mins');
        return $date;
    }

    /**
     * @param $ts
     * @param null $string
     * @param bool $withsec
     * @return mixed|string|null
     */
    public function convertDate($ts, $string = null, bool $withsec = true)
    {
        try {
            if($ts != UNLIMITED) {
                if(is_numeric($ts)) {
                    $date = new DateTime();
                    $date->setTimestamp($ts);
                    if($withsec)
                        $data = $date->format('d-m-Y H:i:s');
                    else
                        $data = $date->format('d-m-Y');
                }
                else {
                    if($string == null)
                        $data = __('Unknown');
                    else
                        $data = $string;
                }
            }
            else {
                $data = $string;
            }

            return $data;
        }
        catch (Exception $e) {
            return __('Unknown');
        }
    }

    /**
     * @param $ts
     * @param null $format
     * @return string
     * @throws Exception
     */
    public function convertTsToDate($ts, $format = null): string
    {
        if($ts != UNLIMITED) {
            if(is_numeric($ts)) {
                $date = new DateTime();
                $date->setTimestamp($ts);
                $data = $date->format($format);
            }
            else {
                $data = 'unknown';
            }
        }
        else {
            $data = UNLIMITED;
        }


        return $data;

    }

    /**
     * @param $ts
     *
     * @return bool|string
     */
    public function convertDateString($ts)
    {
        return date_format(date_create($ts), 'l jS F Y');
    }

    /**
     * @param $date
     *
     * @return int
     */
    public function dateTimetoTS($date): int
    {
        return strtotime($date);
    }

    /**
     * @param $status
     * @return string
     */
    public function convertStatus($status): string
    {
        if($status == "1") {
            $status = __('Active');
        }
        else {
            $status = __('Passive');
        }
        return $status;

    }

    /**
     * @param $cidr
     * @return string
     */
    public function cidrConvert($cidr): string
    {
        $format = array(
            '0' => '0.0.0.0',
            '1' => '128.0.0.0',
            '2' => '192.0.0.0',
            '3' => '224.0.0.0',
            '4' => '248.0.0.0',
            '5' => '252.0.0.0',
            '6' => '254.0.0.0',
            '7' => '255.0.0.0',
            '8' => '255.128.0.0',
            '9' => '255.192.0.0',
            '10' => '255.224.0.0',
            '11' => '255.240.0.0',
            '12' => '255.248.0.0',
            '13' => '255.252.0.0',
            '14' => '255.254.0.0',
            '16' => '255.255.0.0',
            '17' => '255.255.128.0',
            '18' => '255.255.192.0',
            '19' => '255.255.224.0',
            '20' => '255.255.240.0',
            '21' => '255.255.248.0',
            '22' => '255.255.252.0',
            '23' => '255.255.254.0',
            '24' => '255.255.255.0',
            '25' => '255.255.255.128',
            '26' => '255.255.255.192',
            '27' => '255.255.255.224',
            '28' => '255.255.255.240',
            '29' => '255.255.255.248',
            '30' => '255.255.255.252',
            '31' => '255.255.255.254',
            '32' => '255.255.255.255',
        );

        if(!array_key_exists($cidr, $format)) {
            $data = "255.255.255.252";
        }
        else {
            $data = $format[$cidr];
        }
        return $data;
    }

    /**
     * @param $netmask
     * @return string
     */
    public function convertToCidr($netmask): string
    {
        $format = array(
            '0.0.0.0' => '0',
            '128.0.0.0' => '1',
            '192.0.0.0' => '2',
            '224.0.0.0' => '3',
            '248.0.0.0' => '4',
            '252.0.0.0' => '5',
            '254.0.0.0' => '6',
            '255.0.0.0' => '7',
            '255.128.0.0' => '8',
            '255.192.0.0' => '9',
            '255.224.0.0' => '10',
            '255.240.0.0' => '11',
            '255.248.0.0' => '12',
            '255.252.0.0' => '13',
            '255.254.0.0' => '14',
            '255.255.0.0' => '16',
            '255.255.128.0' => '17',
            '255.255.192.0' => '18',
            '255.255.224.0' => '19',
            '255.255.240.0' => '20',
            '255.255.248.0' => '21',
            '255.255.252.0' => '22',
            '255.255.254.0' => '23',
            '255.255.255.0' => '24',
            '255.255.255.128' => '25',
            '255.255.255.192' => '26',
            '255.255.255.224' => '27',
            '255.255.255.240' => '28',
            '255.255.255.248' => '29',
            '255.255.255.252' => '30',
            '255.255.255.254' => '31',
            '255.255.255.255' => '32',
        );

        if(!array_key_exists($netmask, $format)) {
            $data = "30";
        }
        else {
            $data = $format[$netmask];
        }
        return $data;
    }

    /**
     * @param $date
     * @param string $from_format
     * @param string $to_format
     * @return bool|string
     */
    public function reformatDate($date, string $from_format = 'd/m/Y', string $to_format = 'Y-m-d')
    {
        $date_aux = date_create_from_format($from_format, $date);
        return date_format($date_aux, $to_format);
    }

    public function mask($str): string
    {
        $firstn = substr($str, 0, 2);
        $lastn = substr($str, strlen($str) -2, 2);
        return "$firstn******$lastn";
    }

    public function getApplicationMode($str, $return_numeric = false)
    {
        if(!$return_numeric) {
            if(is_numeric($str)) {
                return POOL_MODE[$str];
            }
            else {
                return $str;
            }
        }
        else {
            foreach (POOL_MODE as $int => $version) {
                if($version == $str) {
                    return $int;
                }
            }
            return CLASSIC;
        }
    }

    public function getPhpVersion($str, $return_numeric = false)
    {
        if(!$return_numeric) {
            if(is_numeric($str)) {
                return PHP_VERSIONS[$str];
            }
            else {
                return $str;
            }
        }
        else {
            foreach (PHP_VERSIONS as $int => $version) {
                if($version == $str) {
                    return $int;
                }
            }
            return PHP72;
        }

    }

    public function getNetVersion($str, $return_numeric = false)
    {
        if(!$return_numeric) {
            if(is_numeric($str)) {
                return NET_VERSIONS[$str];
            }
            else {
                return $str;
            }
        }
        else {
            foreach (NET_VERSIONS as $int => $version) {
                if($version == $str) {
                    return $int;
                }
            }
            return v20;
        }
    }

    public function getStatisticsType($str, $return_numeric = false)
    {
        if(!$return_numeric) {
            if(is_numeric($str)) {
                return STATISTICS[$str];
            }
            else {
                return $str;
            }
        }
        else {
            foreach (STATISTICS as $int => $version) {
                if($version == $str) {
                    return $int;
                }
            }
            return NO_STATS;
        }

    }

    public function getDbTypes($str, $return_numeric = false)
    {
        if(!$return_numeric) {
            if(is_numeric($str)) {
                return DB_TYPES_ARR[$str];
            }
            else {
                return $str;
            }
        }
        else {
            foreach (DB_TYPES as $int => $version) {
                if($version == $str) {
                    return $int;
                }
            }
            return MYSQL;
        }
    }

    public function getFtpUserTypes($str, $return_numeric = false)
    {
        if(!$return_numeric) {
            if(is_numeric($str)) {
                return FTP_USER_TYPES_ARR[$str];
            }
            else {
                return $str;
            }
        }
        else {
            foreach (FTP_USER_TYPES_ARR as $int => $type) {
                if($type == $str) {
                    return $int;
                }
            }
            return MYSQL;
        }
    }
}