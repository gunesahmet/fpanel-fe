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

trait Time
{
    public function TimeFormat($time, $field): Validation
    {
        if($this->error_status) {
            return $this;
        }

        try {
            $status = false;

            $array = explode(':', $time);
            if(count($array) < 3) {
                $this->error_status = true;
                $this->message[$field] = __('Please enter valid time format');
                return $this;
            }
            $second = $array['2'];
            $minute = $array['1'];
            $hour = $array['0'];

            if((!is_numeric($hour)) || (!is_numeric($second)) ||(!is_numeric($minute)))
                $status = true;

            if(($hour > 23) || ($minute > 59) || ($second > 59))
                $status = true;

            if($status) {
                $this->error_status = true;
                $this->message[$field] = __('Please enter valid time format');
            }
        }
        catch (\Exception $e) {
            $this->error_status = true;
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function DateFormat($date, $field): Validation
    {
        if($this->error_status) {
            return $this;
        }
        try {
            $status = false;

            $array = explode('/', $date);
            $day = $array['2'];
            $month = $array['1'];
            $year = $array['0'];

            if((!is_numeric($year)) || (!is_numeric($month)) ||(!is_numeric($day)))
                $status = true;

            if(($day > 31) || ($month > 12))
                $status = true;

            if($status) {
                $this->error_status = true;
                $this->message[$field] = __('Please enter valid date format');
            }
        }
        catch (\Exception $e) {
            $this->error_status = true;
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }
}
