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
 */namespace FPanel\WebServer;

use FPanel\Library\Library;
use FPanel\Process\Php as PhpCurl;

/**
 * Class PhpSettings
 * @package FPanel\WebServer
 */
class PhpSettings extends Library
{
    public function update($server_id): bool
    {
        if($this->validation() == false) {
            return false;
        }

        $this->sendSystemCurl($server_id);
        return true;
    }

    public function getPhpIniSettingsDomain($domain_name)
    {
        $php_setting = new PhpCurl();
        return $php_setting->show(array(
            'domain' => $domain_name
        ));
    }

    public function getPhpIniSettingsServer($server_id)
    {
        $php_setting = new PhpCurl();
        $php_setting->setServerId($server_id);
        return $php_setting->show(array(
            'domain' => ''
        ));
    }

    public function sendDomainCurl()
    {

//        $php_setting = new PhpCurl();
//        $result = $php_setting->create($post_data);
//        if(trim($result) != "Success") {
//            $result = str_replace("Success","", $result);
//            $result = trim($result);
//            $result = explode(" ", $result);
//            return $result;
//        }
//        else {
//            return true;
//        }
    }

    public function sendSystemCurl($server_id)
    {
        $php_setting = new PhpCurl();
        $php_setting->setServerId($server_id);
//        $result = $php_setting->setSystem($params);
//        return $result;
    }

    private function validation(): bool
    {

        $times = array('default' => __('Default'),'30' => '30s','60' => '60s','90' => '90s');
        $limits = array('default' => __('Default'),'8M' => '8MB','16M' => '16MB','32M' => '32MB','64M' => '64MB','128M' => '128MB','256M' => '256MB','512M' => '512MB');


        $this->validation->InclusionIn($this->getPost("dtz"),'date_timezone', $this->env->php_timezone(),null,true);
        $this->validation->InclusionIn($this->getPost("ml"),'memory_limit', $limits,null,true);
        $this->validation->InclusionIn($this->getPost("met"),'max_execution_time', $times,null,true);
        $this->validation->InclusionIn($this->getPost("mit"),'max_input_time', $times,null,true);
        $this->validation->InclusionIn($this->getPost("pms"),'post_max_size', $limits,null,true);
        $this->validation->InclusionIn($this->getPost("umf"),'upload_max_filesize', $limits,null,true);
        $this->validation->InclusionIn($this->getPost("fu"),'file_uploads',array("default","on","off"));
        $this->validation->InclusionIn($this->getPost("rg"),'register_globals',array("default","on","off"));
        $this->validation->InclusionIn($this->getPost("mqg"),'magic_quotes_gpc',array("default","on","off"));
        $this->validation->InclusionIn($this->getPost("de"),'display_errors',array("default","on","off"));
        $this->validation->InclusionIn($this->getPost("le"),'log_errors',array("default","on","off"));
        $this->validation->InclusionIn($this->getPost("auf"),'allow_url_fopen',array("default","on","off"));
        $this->validation->InclusionIn($this->getPost("sot"),'short_open_tag',array("default","on","off"));

        $result = $this->validation->Validate();
        if($result == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;

    }

}