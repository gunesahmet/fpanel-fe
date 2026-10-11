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

use FPanel\Environments\Datatable;
use FPanel\Environments\QueryBuilder;
use FPanel\Library\Library;
use FPanel\Models\Servers;
use FPanel\Models\SystemPackages;
use FPanel\Process\Packages as Packagemng;

/**
 * Class ServerSettings
 * @package FPanel\System
 */
class Packages extends Library
{
    public $user_id;
    protected $server_data = array();

    public function getPackageListFromDB($server_id): array
    {
        $datatable = new Datatable();

        $datatable
            ->select(array(
                'system_packages.package_name',
                'system_packages.package_desc',
                'system_packages.version',
                'system_packages.status',
                'system_packages.id',
            ))
            ->from("system_packages");
        $datatable->where("system_packages.server_id = '$server_id'");


            $data = $datatable->searchColumn([
                "system_packages.package_name",
                "system_packages.package_desc",
                "system_packages.status",
            ])
            ->orderableColumn([
                'system_packages.package_name',
                'system_packages.package_desc',
                'system_packages.version',
                'system_packages.status',
            ])
            ->generate()
            ->execute()
            ->result();


        foreach($data['aaData'] as $key => $value){
            if ($value[3] == NOT_INSTALLED){
                $data['aaData'][$key][3] = '<span class="label label-danger"> '. __('Not Installed') .' </span>';
                $data['aaData'][$key][4] = ' <a data-url="'.$this->uri->url_encrypt('packages/install/'. $value[4]."/0").'" data-id="response" class="btn btn-primary btn-sm btn-sm-block"><i class="fa fa-download"></i> '. __("Install") .'</a>';
            }
            else{
                $data['aaData'][$key][3] = '<span class="label label-success"> '. __('Installed') .' </span>';
                $data['aaData'][$key][4] = ' <a data-url="'.$this->uri->url_encrypt('packages/remove/'. $value[4]).'" data-id="response" class="btn btn-default btn-sm btn-sm-block"><i class="fa fa-times"></i> '. __("Remove") .'</a>';
            }
        }
        return $data;
    }

    public function updateSourceTree($server_id): bool
    {
        $packagemng = new Packagemng();
        $packagemng->setServerId($server_id);
        $pkg_list = $packagemng->pkgList();

        $new_pkg_list = json_decode($pkg_list,true);
        $new_pkg_list = array_values(array_unique($new_pkg_list, SORT_REGULAR));

        if(json_last_error() != JSON_ERROR_NONE) {
            return false;
        }

        $packages = SystemPackages::find("server_id = '$server_id'");
        foreach ($packages as $package) {
            if(!$package->delete()) {
                $this->log->error([
                    "Module" => "Packages->updateSourceTree delete package",
                    "data" => $this->getPost(),
                    "errors" => $package->getMessages(),
                ]);
                $this->error_messages = $package->getMessages();
            }
        }

        $qbuilder = new QueryBuilder();
        $qbuilder->into("system_packages")
            ->columns([
                "package_name",
                "package_desc",
                "version",
                "status",
                "support",
                "server_id",
            ]);
        foreach($new_pkg_list as $package) {
            if(!isset($package['status']) || empty($package['status'])) {
                $status = "not_installed";
            }
            else {
                if($package["status"] != "not_installed") {
                    $status = "installed";
                }
                else {
                    $status = "not_installed";
                }
            }

            $qbuilder->assign([
                $package["package_name"],
                $package["description"],
                $package["version"],
                $status,
                1,
                $server_id,
            ]);
        }


        if(!$qbuilder->create()) {
            $this->log->error([
                "Module" => "Packages->updateSourceTree",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            $this->error_messages = $qbuilder->getMessages();
        }

        return true;
    }

    public function getSourceListContent($server_id)
    {
        $packagemng = new Packagemng();
        $packagemng->setServerId($server_id);
        $source_list_content = $packagemng->getSourceLists();
        $source_list_content_arr = json_decode($source_list_content, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            return false;
        }
        return $source_list_content_arr;
    }

    public function updateSourceList($server_id): bool
    {
        $packagemng = new Packagemng();
        $packagemng->setServerId($server_id);
        $params = [
            "name" => $this->crypto->decryptSession($this->getPost("name")),
            "content" => $this->crypto->staticEncode($this->getPost("content"))
        ];
        $packagemng->updateSourceList($params);
        return true;
    }

    public function removeSourceList($server_id): bool
    {
        $packagemng = new Packagemng();
        $packagemng->setServerId($server_id);
        $params = [
            "name" => $this->crypto->decryptSession(trim($this->getPost("name"))),
        ];
        $packagemng->removeSourceList($params);
        return true;
    }

    public function createSourceList($server_id): bool
    {
        $packagemng = new Packagemng();
        $packagemng->setServerId($server_id);
        $params = [
            "name" => $this->getPost("name").$this->getPost("extension"),
            "content" => $this->crypto->staticEncode($this->getPost("content"))
        ];

        $packagemng->createSourceList($params);
        return true;
    }

    public function initSourcelist($server_id): bool
    {
        $packagemng = new Packagemng();
        $packagemng->setServerId($server_id);
        $packagemng->initSourcelist();
        return true;
    }

    public function installPackage($package_id, $confirm)
    {
        $packages = SystemPackages::findFirstById($package_id);
        if($packages) {
            $package_name = $packages->package_name;
            $server_id = $packages->server_id;

            $packagemng = new Packagemng();
            $packagemng->setServerId($server_id);

            $params = [
                "pkg-name" => trim($package_name)
            ];

            if($confirm == 0) {
                $result = $packagemng->getDepsPackage($params);

                if(strpos($result, "Cant find package") !== false) {
                    $this->error_messages["content"] = __("Package not found");
                    return false;
                }

                $result = $this->crypto->staticDecode($result);

                $items = explode("\n", $result);
                $dep_packages = [];
                foreach ($items as $item) {
                    $item = trim($item);
                    $package = SystemPackages::findFirst("package_name = '$item'");
                    $dep_packages[$item] = ($package && $package->status == INSTALLED ? "<span class='text-navy'>". __("Installed") : "<span class='text-danger'>".__("Not Installed")) . "</span>";
                }

                return $dep_packages;
            }
            else {
                $packagemng->installPackage($params);
                $packages->status = INSTALLED;
                if(!$packages->update()) {
                    foreach ($packages->getMessages() as $message) {
                        $this->error_messages[$message->getField()] = $message->getMessage();
                    }
                    $this->log->error([
                        "Module" => "Packages->installPackage",
                        "data" => $this->getPost(),
                        "errors" => $this->error_messages,
                    ]);
                    return false;
                }
                return true;
            }
        }
        else {
            $this->error_messages[] = __("Package not found");
            return false;
        }
    }

    public function unInstallPackage($package_id): bool
    {
        $packages = SystemPackages::findFirstById($package_id);
        if($packages) {
            $package_name = $packages->package_name;
            $server_id = $packages->server_id;

            $packagemng = new Packagemng();
            $packagemng->setServerId($server_id);

            $params = [
                "pkg-name" => $package_name
            ];
            $packagemng->unInstallPackage($params);
            $packages->status = "not_installed";
            if(!$packages->update()) {
                foreach ($packages->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Packages->unInstallPackage",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        else {
            $this->error_messages[] = __("Package not found");
            return false;
        }
    }

}
