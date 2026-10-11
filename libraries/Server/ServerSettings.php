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
 */namespace FPanel\Server;

use FPanel\Library\Library;
use FPanel\Models\Domains;
use FPanel\Models\ServerLogSettings;
use FPanel\Models\Servers;
use FPanel\Models\SystemSettings;
use FPanel\Models\Users;
use FPanel\Process\Admin;
use FPanel\Process\Date as Datemng;
use FPanel\Process\Log as Logmng;

/**
 * Class ServerSettings
 * @package FPanel\System
 */
class ServerSettings extends Library
{
    public $user_id;
    protected $store_time;
    protected $compressing = "off";

    public function serverLogSettings(): bool
    {
        $required  = array();
        $required[] = array(
            'item' => $this->getPost("store_time"),
            'field' => 'store_time',
            'message' => __('Keep log time is required'),
        );

        if($this->getPost("send_information") == ON) {
            $required[] = array(
                'item' => $this->getPost("email_address"),
                'field' => 'email_address',
                'message' => __('Email Address is required')
            );
        }

        $this->validation->Required($required)
            ->Numeric($this->getPost("store_time"),'store_time',__('Please enter valid format'));

        if($this->getPost("send_information") == ON) {
            $this->validation->Email($this->getPost("email_address"),'email_address',__('Please enter valid email format'));
        }

        if(!$this->validation->Validate()) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }

        $server_id = $this->getPost("server_id");
        $save_logs_time = $this->getPost("store_time");
        $email_address = $this->getPost("email_address");

        $periodic_del_log = "0";
        if($this->getPost("periodic_del") == ON) {
            $periodic_del_log = "1";
        }

        $this->compressing = "0";
        $log_compress = "0";
        if($this->getPost("log_compress") == ON) {
            $log_compress = "1";
            $this->compressing = "1";
        }

        $send_information = "0";
        if($this->getPost("send_information") == ON) {
            $send_information = "1";
        }
        if(is_numeric($this->getPost("store_time"))) {
            $this->store_time = $this->getPost("store_time");
        }
        else {
            $this->error_messages['store_time'] = __('Please enter valid time');
            return false;
        }

        $log_settings = ServerLogSettings::findFirst("server_id = '$server_id'");
        if($log_settings) {
            $log_settings->assign(array(
                'periodic_delete'   => $periodic_del_log,
                'store_day'   => $save_logs_time,
                'compressing'   => $log_compress,
                'send_mail'   => $send_information,
                'email'   => $email_address,
            ));
            if(!$log_settings->update()) {
                foreach ($log_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "ServerSettings->serverLogSettings update",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        else {
            $log_settings = new ServerLogSettings();
            $log_settings->assign(array(
                'server_id'     => $server_id,
                'periodic_delete'   => $periodic_del_log,
                'store_day'   => $save_logs_time,
                'compressing'   => $log_compress,
                'send_mail'   => $send_information,
                'email'   => $email_address,
            ));
            if(!$log_settings->create()) {
                foreach ($log_settings->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "ServerSettings->serverLogSettings create",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }

        $this->logSettingCurl();
        return true;
    }

    public function serverTimeSettings($server_id): bool
    {
        //@TODO: Timezone komutu başarılı gönderiliyor ve date komutundan çıktı doğru alınıyor ancak /etc/sysconfig/clock dosyasında bu gönderilen değer işlenmemiş olarak duruyor
        $required = array(
            array(
                'item' => $this->getPost("system_time"),
                'field' => 'system_time',
                'message' => __('Time is required')
            ),
            array(
                'item' => $this->getPost("system_date"),
                'field' => 'system_date',
                'message' => __('Date is required')
            ),
            array(
                'item' => $this->getPost("timezone"),
                'field' => 'timezone',
                'message' => __('Timezone is required')
            ),
        );

        $this->validation->Required($required)
            ->TimeFormat($this->getPost("system_time"),'system_time')
            ->DateFormat($this->getPost("system_date"),'system_date');

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        if($server_id == "1") {
            $servers = Servers::findFirst("type = 'main'");
        }
        else {
            $servers = Servers::findFirstById($server_id);
        }

        $time_zone_status = $this->checkTimeZone($this->getPost("timezone"), $servers->os);
        if($time_zone_status == false) {
            $this->error_messages['time_zone'] = __('Please select valid a timezone');
            return false;
        }

        $this->setTimeZoneCurl($this->getPost("server_id"));
        $this->setDateCurl($this->getPost("server_id"));
        $this->setTimeCurl($this->getPost("server_id"));
        $this->setSyncDateTime($this->getPost("server_id"));
        return true;
    }

    public function getDateFromServer($server_id): array
    {
        $date_time = $this->getDateTimeCurl($server_id);
        $date_time = json_decode($date_time,true);

        $date = $date_time['date'];
        $time = $date_time['time'];
        $time_zone = $date_time['timezone'];
        $sync = $date_time['sync'];
        $ntp_address = "";
        if(isset($date_time['ntp_address'])) {
            $ntp_address = $date_time['ntp_address'];
        }

        $time = explode(':', $time);
        $second = $time['2'];
        $minute = $time['1'];
        $hour = $time['0'];

        if(strpos($date,'-') !== false) {
            $date = explode('-', $date);
        }
        else {
            $date = explode('/', $date);
        }

        $month = $date['0'];
        $day = $date['1'];
        $year = $date['2'];


        return array(
            'hour'  => $hour,
            'minute'    => $minute,
            'seconds'   => $second,
            'day'   => $day,
            'month' => $month,
            'year'  => $year,
            'timezone'  => $time_zone,
            'ntp_address'  => $ntp_address,
            'sync'  => $sync,
        );
    }

    private function checkTimeZone($post_time_zone, $os): bool
    {
        $time_zone = $this->env->systemTimeZones($os);
        if($post_time_zone == "default") {
            return true;
        }
        if(in_array($post_time_zone, $time_zone)) {
            return true;
        }
        return false;
    }

    public function logSettingCurl()
    {
        $params = array(
            'clean-err' => 'on',
            'comp'  => $this->compressing,
            'day'   => $this->store_time,
        );
        $logmng = new Logmng();
        $logmng->setServerId($this->getPost("server_id"));
        $logmng->init($params);
    }

    private function getDateTimeCurl($server_id)
    {
        $datemng = new Datemng();
        $datemng->setServerId($server_id);
        return $datemng->showDate();
    }

    private function setTimeCurl($server_id): void
    {
        $datemng = new Datemng();
        $datemng->setServerId($server_id);
        $params = array(
            'time' => $this->getPost("system_time")
        );
        $datemng->setTime($params);
    }

    private function setSyncDateTime($server_id): void
    {
        $datemng = new Datemng();
        $datemng->setServerId($server_id);
        $params = array(
            'auto-sync' => $this->getPost("sync_time", OFF)
        );
        $datemng->setSync($params);
    }

    private function setDateCurl($server_id): void
    {
        $datemng = new Datemng();
        $datemng->setServerId($server_id);

        $params = array(
            'date' => preg_replace("/[^0-9,.]/","", $this->getPost("system_date"))
        );
        $datemng->setDate($params);
    }

    private function setTimeZoneCurl($server_id): void
    {
        $datemng = new Datemng();
        $datemng->setServerId($server_id);
        $params = array(
            'zone' => $this->getPost("timezone"),
            'ntp-server' => $this->getPost("ntp_address")
        );
        $datemng->setTimezone($params);
    }

    public function getResourceServer($server_id): array
    {
        $current_time = time();

        $panel_data = SystemSettings::findFirst("key = 'system_data'")->toArray();

        $panel_data = json_decode($panel_data['value'],true);

        $domains = Domains::find("web_server_id = '$server_id'");
        $total_domains = count($domains);

        $domains = Domains::find("status = '1' AND web_server_id = '$server_id'");
        $active_domains = count($domains);

        $clients = Users::find("status = '1' AND type > 0 AND type < 4");
        $active_clients = count($clients);

        $passive_domains = abs($total_domains - $active_domains);

        $expired_domains = array();
        $expired_clients = array();
        if(!empty($domains)) {
            $domain_list = $domains->toArray();
            foreach($domain_list as $domain) {
                if(($domain['expire_date'] != UNLIMITED) && ($domain['expire_date'] < $current_time)) {
                    $expired_domains[] = $domain['name'];
                }
            }
        }
        $clients = Users::find();

        $total_clients = array();
        if($clients) {
            $client_list = $clients->toArray();
            foreach($client_list as $client) {

                if(($client['type'] > ADMIN_USER) && ($client['type'] < DOMAIN_USER)) {
                    $total_clients[] = $client;
                }

                if(($client['expire_date'] != UNLIMITED) && ($client['expire_date'] < $current_time)) {
                    $expired_clients[] = $client['username'];
                }
            }
        }

        $total_clients = count($total_clients);
        $passive_clients = abs($total_clients - $active_clients);

        $expired_domains = count($expired_domains);
        $expired_clients = count($expired_clients);

        $adminmng = new Admin();
        $adminmng->setServerId($server_id);
        $system_data = $adminmng->sysinfo();

        return array(
            'cpu_type' => $system_data['cpu_info'],
            'version' => $panel_data['panel_version'],
            'os'    => $system_data['system']['os'] . ' ('. $system_data['system']['kernel'].')',
            'uptime' => $this->env->secondsToTime2($system_data['uptime']),
            'min1' => $system_data['loadavg']['min1'],
            'min5'  => $system_data['loadavg']['min5'],
            'min15' => $system_data['loadavg']['min15'],
            'ram_total' => $this->env->convert($system_data['memory']['total']),
            'ram_used' => $this->env->convert($system_data['memory']['used']),
            'ram_free' => $this->env->convert($system_data['memory']['free']),
            'disk_total' => $this->env->convert($system_data['disk']['total']),
            'disk_used' => $this->env->convert($system_data['disk']['used']),
            'disk_free' => $this->env->convert($system_data['disk']['free']),
            'total_users' => $total_clients,
            'active_users' => $active_clients,
            'passive_users' => $passive_clients,
            'expired_users' => $expired_clients,
            'total_domains' => $total_domains,
            'active_domains' => $active_domains,
            'passive_domains' => $passive_domains,
            'expired_domains' => $expired_domains
        );
    }
}
