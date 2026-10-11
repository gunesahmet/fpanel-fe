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

trait Task
{
    public function CronMinuteHourDay($item, $field, $message = null): Validation
    {
        try {
            if(!$this->error_status) {
                $status = false;

                $illegal = "\\#$%^&()+=-[]\';,.{}|\":<>?~";

                if(strpbrk($item, $illegal)) {
                    $status = true;
                }

                if(preg_match('/[A-Za-z]/', $item, $match)) {
                    $status = true;
                }

                $allowed = "/*";

                if(!strpbrk($item, $allowed)) {
                    if(!is_numeric($item))
                        $status = true;
                }

                if($status) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Please enter valid minute');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }
                return $this;
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function CronWeek($item, $field, $message = null): Validation
    {
        try {
            if(!$this->error_status) {
                $status = false;

                $illegal = "\\#$%^&()+=-[]\';,.{}|\":<>?~";

                if(strpbrk($item, $illegal)) {
                    $status = true;
                }
                else {
                    $allowed = "/*";
                    if(!strpbrk($item, $allowed)) {
                        $week_array = array('mon','tue','wed','thu','fri','sat','sun');
                        $status = true;
                        foreach($week_array as $day) {
                            if($day == $item) {
                                $status = false;
                                break;
                            }
                        }
                    }
                    else {
                        if(preg_match('/[A-Za-z]/', $item, $match)) {
                            $status = true;
                        }
                    }
                }

                if($status) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Please enter valid week');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }
                return $this;
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }

    public function CronMonth($item, $field, $message = null): Validation
    {
        try {
            if(!$this->error_status) {
                $status = false;

                $illegal = "\\#$%^&()+=-[]\';,.{}|\":<>?~";

                if(strpbrk($item, $illegal)) {
                    $status = true;
                }
                else {
                    $allowed = "/*";
                    if(!strpbrk($item, $allowed)) {
                        $months_array = array('jan','feb','marc','apr','may','jun','jul','aug','sep','oct','nov','dec');
                        $status = true;
                        foreach($months_array as $month) {
                            if($item == $month) {
                                $status = false;
                                break;
                            }
                        }
                    }
                    else {
                        if(preg_match('/[A-Za-z]/', $item, $match)) {
                            $status = true;
                        }
                    }
                }

                if($status) {
                    $this->error_status = true;
                    if($message == null) {
                        $this->message[$field] = __('Please enter valid month');
                    }
                    else {
                        $this->message[$field] = $message;
                    }
                }
                return $this;
            }
        }
        catch (\Exception $e) {
            $this->message[$field] = $e->getMessage();
        }
        return $this;
    }
}
