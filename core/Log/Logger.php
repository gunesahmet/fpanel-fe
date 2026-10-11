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
 */namespace FPanel\Log;

use Phalcon\Logger\Adapter\File;
use Phalcon\Mvc\User\Component;

/**
 * Class Logger
 * @package FPanel\Log
 */
class Logger extends Component
{
    public function info($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."system.info.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->info((string) json_encode($log_str, JSON_UNESCAPED_UNICODE));

        return $this;

    }

    public function error($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."system.error.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
            "trace" => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->error(json_encode($log_str, JSON_UNESCAPED_UNICODE));
        return $this;
    }

    public function debug($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."system.debug.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->debug(json_encode($log_str, JSON_UNESCAPED_UNICODE));
        return $this;
    }

    public function telegramInfo($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."telegram.info.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->info(json_encode($log_str, JSON_UNESCAPED_UNICODE));

        return $this;
    }

    public function telegramError($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."telegram.error.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
            "trace" => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->error(json_encode($log_str, JSON_UNESCAPED_UNICODE));
        return $this;
    }

    public function telegramDebug($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."telegram.debug.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->debug(json_encode($log_str, JSON_UNESCAPED_UNICODE));
        return $this;
    }

    public function apiInfo($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."api.info.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data
        ];

        $logger->info(json_encode($log_str, JSON_UNESCAPED_UNICODE));
        return $this;
    }

    public function apiError($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."api.error.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
            "trace" => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->info(json_encode($log_str, JSON_UNESCAPED_UNICODE));

        return $this;
    }

    public function apiDebug($data): Logger
    {
        $logger = new File(PANEL_LOG_DIR."api.debug.log");
        if(is_array($data)) {
            if(isset($data["request"]["data"])) {
                $data["request"]["data"] = explode("&", $data["request"]["data"]);
            }
        }
        $log_str = [
            "request_id" => $this->request_id,
            "request" => $data,
            "trace" => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)
        ];

        if(strpos(json_encode($log_str, JSON_UNESCAPED_UNICODE), "iDisplayStart") === false)
            $logger->info(json_encode($log_str, JSON_UNESCAPED_UNICODE));

        return $this;
    }
}