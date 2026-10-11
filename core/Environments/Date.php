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

use DateTime;
use Exception;
use Phalcon\Mvc\User\Component;

/**
 * Class Date
 * @package FPanel\Environments
 */
class Date extends Component
{
    /**
     * @param bool $time
     * @param string|null $format
     * @return false|string
     */
    public function dateTime(bool $time = true, string $format = null)
    {
        if($time == false) {
            if($format == null) {
                $format = "Y-m-d";
            }
        }
        else {
            if($format == null) {
                $format = "Y-m-d H:i:s";
            }
        }
        return date($format);
    }

    /**
     * @param $date
     * @param int $days
     * @return string
     */
    public function addDayToDate($date, int $days): string
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if($days > 1)
            $day_string = "days";
        else
            $day_string = "day";

        $date->modify("+{$days}". $day_string);
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $date
     * @param int $hours
     * @return string
     */
    public function addHourToDate($date, int $hours): string
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if($hours > 1)
            $hour_string = "hours";
        else
            $hour_string = "hour";

        $date->modify("+{$hours}". $hour_string);
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $date
     * @param $months
     * @param string $format
     * @return false|string
     */
    public function addMonthToDate($date, $months, string $format = "Y-m-d H:i:s")
    {
        $time = strtotime($date);
        return date($format, strtotime("+$months month", $time));
    }

    /**
     * @param $ts
     * @param $days
     * @return float|int
     */
    public function addDayToTs($ts, $days)
    {
        return abs($ts + $days * 24 * 3600);
    }

    /**
     * @param $date
     * @param bool $micro
     * @return int|string|null
     */
    public function getTimeStamp($date, bool $micro = false)
    {
        try {
            if(!$micro) {
                $date = new DateTime($date);
                return $date->getTimestamp();
            }
            else {
                if($date == "now") {
                    return (int)(microtime(true) * 1000000);
                }
                else {
                    $ms = substr($date, strpos($date, ".") + 1);
                    $date = new DateTime($date);
                    return $date->getTimestamp().$ms;
                }
            }
        }
        catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $ts
     * @param bool $time
     * @param bool $microtime
     * @return string|null
     */
    public function tsToDate($ts, bool $time = true, bool $microtime = false): ?string
    {
        $ms = "000000";
        if(strlen($ts) > 10) {
            $new_ts = substr($ts, 0, 10);
            $ms = str_replace($new_ts, "", $ts);
        }
        else
            $new_ts = $ts;
        if($ts != null) {
            $date = new DateTime();
//            if(!is_numeric($new_ts)) {
//                $this->debug->debug($new_ts, false);
//            }
            $date->setTimestamp($new_ts);
            if($time == true)
                $result = $date->format('Y-m-d H:i:s');
            else
                $result = $date->format('Y-m-d');

            if($microtime) {
                $result .= ".$ms";
            }
            return $result;
        }
        else
            return null;
    }

    /**
     * @param $old_time
     * @param $current_time
     * @return string
     */
    public function differenceTime($old_time, $current_time): string
    {
        $greater = $this->getTimeStamp($old_time);
        $lower = $this->getTimeStamp($current_time);
        $result = (int)$greater - (int)$lower;
        return strval($result);
    }

    /**
     * @param $seconds
     * @return string
     */
    public function secondsToTime($seconds): string
    {

        if ($seconds <= 0)
            return "0";
        $date = "";
        $days = (int)($seconds / 86400);
        $hours = (int)(($seconds - ($days * 86400))/ 3600);
        $minutes = (int)((($seconds - ($days * 86400) - ($hours * 3600)) / 60));

        if($days >= 1) {
            $day_str = $days == 1 ? __("day") : __("days");
            $date .= $days . " " .$day_str . " ";
            return $date;
        }

        if($hours >= 1) {
            $hour_str = $hours == 1 ? __("hour") : __("hours");
            $date .= $hours . " " . $hour_str . " ";
            return $date;
        }

        if($minutes >= 1) {
            $minute_str = $minutes == 1 ? __("min") : __("mins");
            $date .= $minutes . " " . $minute_str;
            return $date;
        }

        return "1" . __('min');
    }

    /**
     * @param $days
     * @param bool $seconds
     * @return bool|string
     */
    public function getDateAgoDateTime($days, bool $seconds = true)
    {
        if($days > 1) {
            $days = "-$days days";
        }
        else {
            $days = "-$days day";
        }
        if($seconds == true)
            return date("Y-m-d H:i:s",strtotime("$days"));
        else
            return date("Y-m-d",strtotime("$days"));
    }

    /**
     * @param $date
     * @param string $format
     * @return string|null
     */
    public function getOnlyDate($date, string $format = "d-m-Y"): ?string
    {
        try {
            if(($date != null) && ($date != "0") && ($date != __('Never'))) {
                $date = new DateTime($date);
                return $date->format($format);
            }
            else {
                return null;
            }
        }
        catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param $timestamp
     * @return string
     */
    public function dateTimeFromTsWithMillisecond($timestamp): string
    {
        $date = date_create();
        date_timestamp_set($date, $timestamp);
        return date_format($date, 'Y-m-d H:i:s');
    }

    /**
     * @param $date
     * @param $format
     * @return string|null
     */
    public function getCustomDateTime($date, $format): ?string
    {
       try {
           if(($date != null) && ($date != "0") && ($date != __('Never'))) {
               $date = new DateTime($date);
               return $date->format($format);
           }
           else {
               return null;
           }
       }
       catch (Exception $e) {
           return null;
       }
    }

    /**
     * @param $date
     * @param string $format
     * @return false|string|null
     */
    public function getOnlyTime($date, string $format = "H:i:s")
    {
        if(($date != null) && ($date != "0") && ($date != __('Never'))) {
            return date($format,strtotime($date));
        }
        else {
            return null;
        }
    }

    /**
     * @param null $current_date
     * @return array
     */
    public function getDate($current_date = null): array
    {
        if($current_date == null)
            $current_date = $this->dateTime();

        $exploded = explode(' ', $current_date);
        $date = explode('-', $exploded[0]);
        $month = $date[1];
        $year = $date[0];
        $day = $date[2];
        $time_without_milliseconds = preg_replace('/\.[0-9]+/', '', $exploded[1]);

        $time = $time_without_milliseconds;

        return array(
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'time' => $time
        );
    }

    /**
     * @param $month
     * @return mixed
     */
    public function getMonthName($month)
    {
        $months = [1=>__('Jan'),2=>__('Feb'),3=>__('Mar'),4=>__('Apr'),5=>__('May'),6=>__('Jun'),7=>__('Jul'),8=>__('Aug'),9=>__('Sep'),10=>__('Oct'),11=>__('Nov'),12=>__('Dec')];
        return $months[(int)$month];
    }

    /**
     * @param $date
     * @return mixed
     */
    public function getDayName($date)
    {
        return __(date('l', strtotime($date)));
    }

    /**
     * @param $date
     * @param $format
     *
     * @return bool
     */
    public function isValidateDate($date, $format): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * @param $date
     * @return mixed
     * @throws Exception
     */
    public function timeAgo($date)
    {
        $timestamp = $this->getTimeStamp($date);
        $now = time();
        $difference = abs($now - $timestamp);
        $days = intval($difference/(3600*24));
        if($days > 0) {
            return __('%day% days ago',['day' => $days]);
        }
        else {
            $difference = abs($difference - ($days*3600*24));
            $hours = intval($difference/3600);
            if($hours > 0) {
                return __('%hour% hours ago',['hour' => $hours]);
            }
            else {
                $difference = abs($difference - ($hours*3600));
                $mins = intval($difference/60);
                if($mins < 5)
                    return __('A few minutes ago');
                else
                    return __('%min% minutes ago',['min' => $mins]);
            }
        }
    }

    /**
     * @param string $cdate
     * @return array
     * @throws Exception
     */
    public function currentDateTimeWithMs(string $cdate = "now"): array
    {
        $cts = $this->getTimeStamp($cdate, true);
        $cdt = $this->tsToDate($cts, true, true);
        return [
            "timestamp" => $cts,
            "datetime" => $cdt,
        ];
    }
}