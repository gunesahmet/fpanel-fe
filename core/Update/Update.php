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
 */namespace FPanel\Update;

use FPanel\Models\SystemSettings;
use FPanel\Models\SystemVersions;
use FPanel\Models\Updates;
use FPanel\Server\Packages;
use Phalcon\Mvc\User\Component;

abstract class Update extends Component
{
    private $error_messages = [];

    protected $urgency = false;

    abstract protected function initUpdate();
    abstract public function core($data);

    public function checkVersion($current_version): bool
    {
        if (version_compare($current_version, $this->version) == -1) {
            return true;
        }
        return false;
    }

    public function init()
    {
        $update_file_path = ROOTPATH . "/.update";
        if(!file_exists($update_file_path)) {
            $update_file = fopen($update_file_path, "w") or die("Unable to open file!");
            $txt = "Updating...";
            fwrite($update_file, $txt);// File creating
            fclose($update_file);
        }

        $system_versions = new SystemVersions();
        $system_versions->assign([
            "version" => $this->version,
        ]);
        $system_settings = SystemSettings::findFirst("key = 'system_data'");
        if($system_settings) {
            $data = json_decode($system_settings->value, true);
            $data["panel_version"] = $this->version;
            $system_settings->value = json_encode($data);
            if(!$system_settings->update()) {
                foreach ($system_settings->getMessages() as $message) {
                    $this->error_messages[] = $message;
                }
                $this->log->error([
                    "File" => "Uupdate.php",
                    "Error" => "SystemSettings update failed",
                    "Data" => $this->error_messages
                ]);
                return false;
            }
        }

        if(!$system_versions->create()) {
            foreach ($system_versions->getMessages() as $message) {
                $this->error_messages[] = $message;
            }
            $this->log->error([
                "File" => "Uupdate.php",
                "Error" => "SystemVersions create failed",
                "Data" => $this->error_messages
            ]);
            return false;
        }
        return true;
    }

    public function getMessages(): array
    {
        return $this->error_messages;
    }

    public function getUrgency(): bool
    {
        return $this->urgency;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function start()
    {
        $update_file_path = ROOTPATH . "/.update";
        if(!file_exists($update_file_path))  {
            $update_file = fopen($update_file_path, "w") or die("Unable to open file!");
            $txt = "Updating...";
            fwrite($update_file, $txt);// File creating
            fclose($update_file);
        }

        echo print_r("200 OK", true);
        return true;
    }

    public function package($data): bool
    {
        if(!is_array($data)) {
            $this->log->error([
                "File" => "Update",
                "Error" => "Incoming data is not array",
                "Data" => $data
            ]);
            echo print_r("sending data is not array", true);
            return false;
        }
        if(strpos($data["package_name"], "panel-htdoc") !== false)
            $this->initUpdate();

        $update = new Updates();
        $update->assign([
            "created_date" => $this->date->dateTime(true),
            "description" => $data["update_note"],
            "package" => $data["package_name"],
            "version" => $data["package_version"],
            "server_id" => "1" //@TODO change it
        ]);
        if(!$update->create()) {
            foreach($update->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "File" => "Update.php",
                "Error" => "Saving update failed",
                "Data" => $this->error_messages
            ]);
        }
        echo print_r("200 OK", true);
        return true;
    }

    public function finish(): bool
    {
        $packages = new Packages();
        $packages->updateSourceTree("1");
        $update_file_path = ROOTPATH . "/.update";
        if(file_exists($update_file_path))  {
            unlink($update_file_path);
        }
        echo print_r("200 OK", true);
        return true;
    }
}