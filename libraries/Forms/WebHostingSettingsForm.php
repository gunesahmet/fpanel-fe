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

use FPanel\Models\Servers;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class WebHostingSettingsForm
 * @package FPanel\Forms
 */
class WebHostingSettingsForm extends Form
{

    /**
     * @param null $entity
     * @param null $options
     */
    public function initialize($entity = null, $options = null)
    {
        $this->formElements($options);
    }

    /**
     * @param $options
     */
    private function formElements($options)
    {
        $domain_data = $options["domain_data"];
        $statistic_type = new Select("web_statistics_type", array(
            AWSTATS => __("Awstats"),
            WEBALIZER => __("Web Alizer")
        ),array(
            "id" => "web_statistics_type",
            "name" => "web_statistics_type",
            "disabled" => "disabled",
        ));


        $this->add(new Text("ftp_username", array(
            "name" => "ftp_username",
            "id" => "ftp_username",
            "value" => $domain_data["ftpname"],
        )));

        $this->add(new Password("ftp_password", array(
            "name" => "password",
            "id" => "password",
        )));
        $elements = array(
            "php_support" => "php",
            "asp_support" => "asp",
            "asp_net_core_support" => "aspnet_core",
            "asp_net_support" => "aspnet",
            "cgi_support" => "cgi",
            "perl_support" => "perl",
            "ssl_support" => "ssl",
            "python_support" => "python",
            "web_statistics_support" => "statistic_support",
            "dedicated_pool" => "dedicated_pool"
        );

        $php_version_list = array(
            PHP56 => "PHP 5.6",
            PHP72 => "PHP 7.2",
            PHP74 => "PHP 7.4",
        );

        $php_version = new Select("php_version",
            $php_version_list,array(
                "id" => "php_version",
                "name" => "php_version",
            ));
        $php_version->setDefault($domain_data["php_version"]);

        if(!$domain_data["php"]) {
            $php_version->setAttribute("disabled","disabled");
        }
        $application_pool_mode = new Select("application_pool_mode", [
            INTEGRATED => "Integrated",
            CLASSIC => "Classic",
        ], [
            "name" => "application_pool_mode",
            "id" => "application_pool_mode",
        ]);
        $aspnet_version = new Select("aspnet_version", [
            v20 => "2.0",
            v40 => "4.0",
        ], [
            "id" => "aspnet_version",
            "name" => "aspnet_version",
        ]);

        if($domain_data["aspnet"]) {
            $aspnet_version->setDefault($domain_data["aspnet_version"]);
            $application_pool_mode->setDefault($domain_data["application_pool_mode"]);
        }
        else {
            $application_pool_mode->setAttribute("disabled","disabled");
            $aspnet_version->setAttribute("disabled","disabled");
        }
        $statistic_type->setDefault($domain_data["statistic_type"]);

        if(!$domain_data["statistic_support"]) {
            $statistic_type->setAttribute("disabled","disabled");
        }

        $max_cpu_usage = new Text ("max_cpu_usage", array(
            "id" => "max_cpu_usage",
            "name" => "max_cpu_usage",
        ));
        if(!$domain_data["dedicated_pool"]) {
            $max_cpu_usage->setAttribute("disabled","disabled");
        }
        else {
            $max_cpu_usage->setAttribute("value", $domain_data["max_cpu"]);
        }

        $ssl_support = new Check("ssl_support", [
            "name" => "ssl_support",
            "id" => "ssl_support",
        ]);
        $ssl_lists = new Select("ssl_list", $options["ssl_list"],array("id" => "ssl_list","name" => "ssl_list"));


        switch($options["user_type"]) {
            case ROOT_USER:
            case ADMIN_USER:
            case RESELLER_USER:
            case CLIENT_USER:
                $ip_list = $options["ip_list"];
                $ip_addresses = [];
                $ssl_ip_addresses = [];

                $web_server_id = $domain_data["web_server_id"];
                $servers = Servers::find("id = '$web_server_id'")->toArray();

                foreach($ip_list as $ip_address) {
                    $server_type = $this->getServer($ip_address, $servers);
                    if($ip_address["type"] == "exclusive") {
                        $ip_type = __("Exclusive");
                    }
                    else {
                        $ip_type = __("Shared");
                        $ssl_ip_addresses[$ip_address["id"]] = $ip_address["ip_address"]." ($ip_type)";
                    }
                    $ip_addresses[$ip_address["id"]] = $ip_address["ip_address"]." ($ip_type) $server_type";


                    if($ip_address["id"] == $domain_data["ipaddr_id"])
                        $ip_addresses[$ip_address["id"]] = $ip_address["ip_address"]." ($ip_type) $server_type";

                }

                $ip_address_list = new Select("ipaddress_list", $ip_addresses, array(
                    "id" => "ipaddress_list",
                    "name" => "ipaddress_list",
                ));
                $ip_address_list->setDefault($domain_data["ipaddr_id"]);

                foreach ($elements as $items => $value) {
                    if(isset($domain_data[$value]) && $domain_data[$value] == ALLOWED) {
                        $this->add(new Check(
                            $items, array(
                            "name" => $items,
                            "id" => $items,
                            "checked" => "checked",
                        )));
                    }
                    else {
                        $this->add(new Check(
                            $items, array(
                            "name" => $items,
                            "id" => $items,
                        )));
                    }
                }

                $ssl_lists->setDefault($domain_data["ssl_cert_id"]);
                if($domain_data["man_ssl"] == ALLOWED) {
                    if($domain_data["ssl"])
                        $ssl_support->setAttribute("checked","checked");

                    $ssl_lists->setDefault($domain_data["ssl_cert_id"]);
                    $this->add($ssl_lists);
                }
                break;
            default:
                $ip_list = $options["ip_list"];
                $ip_addresses = array();

                foreach($ip_list as $ip_address) {
                    $ip_addresses[$ip_address["ip_address"]] = $ip_address["ip_address"];
                }

                $ip_address_list = new Select("ipaddress_list", $ip_addresses, array(
                    "id" => "ipaddress_list",
                    "name" => "ipaddress_list",
                    "disabled" => "disabled",
                ));
                $ip_address_list->setDefault($domain_data["ipaddr_id"]);


                $aspnet_version->setAttribute("disabled","disabled");
                $statistic_type->setAttribute("disabled","disabled");

                foreach ($elements as $items => $value) {
                    if(isset($domain_data[$value]) && ($domain_data[$value] == ALLOWED)) {
                        $this->add(new Check($items, [
                            "name" => $items,
                            "id" => $items,
                            "checked" => "checked",
                            "disabled" => "disabled",
                        ]));
                    }
                    else {
                        $this->add(new Check($items, [
                            "name" => $items,
                            "id" => $items,
                            "disabled" => "disabled",
                        ]));
                    }
                }

                if($domain_data["man_ssl"] == ALLOWED) {
                    if($domain_data["ssl"])
                        $ssl_support->setAttribute("checked","checked");

                    $ssl_lists->setDefault($domain_data["ssl_cert_id"]);
                    $this->add($ssl_lists);
                }
                else {
                    $ssl_support->setAttribute("disabled","disabled");
                }

                break;
            }

        $this->add($ip_address_list);
        $this->add($php_version);
        $this->add($aspnet_version);
        $this->add($statistic_type);
        $this->add($max_cpu_usage);
        $this->add($application_pool_mode);
        $this->add($ssl_support);
    }

    private function getServer($ip_address, $servers): string
    {
        $type = __("Unknown");
        foreach($servers as $server) {
            if($server["id"] == $ip_address["server_id"]) {
                if(strpos(strtolower($server["description"]),"debian") !== false) {
                    $type = "(".$server["name"].")" . " (Debian)";
                }
                else if(strpos(strtolower($server["description"]),"centos") !== false) {
                    $type = "(".$server["name"].")" . " (CentOS)";
                }
                else if(strpos(strtolower($server["description"]),"ubuntu") !== false) {
                    $type = "(".$server["name"].")" . " (Ubuntu)";
                }
                else if(strpos(strtolower($server["description"]),WINDOWS) !== false) {
                    $type = "(" . $server["name"] .")" . " (Windows)";
                }
                return $type;
            }
        }
        return $type;
    }
}