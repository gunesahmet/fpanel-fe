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

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class UserForm
 * @package FPanel\Forms
 */
class UserForm extends Form
{
	/**
	 * @param null $entity
	 * @param null $options
	 */
	public function initialize($entity = null, $options = null)
	{
        if($options["action"] != "edit") {
            if($options["part"] == "all") {
                $options = $this->listTemplateAndDefault($options);

                $this->selectTemplateElements($options);

                $this->serviceplanLimitsElements($options);
                $this->serviceplanPermissionElements($options);
                $this->serviceplanDomainSettingsElements($options);

                $this->requiredUserFieldsElements($options);
                $this->optionalUserFieldsElements($options);
            }
        }
        else {
            $this->requiredUserFieldsElements($options);
            $this->optionalUserFieldsElements($options);

            $this->serviceplanLimitsElements($options);
            $this->serviceplanPermissionElements($options);
            $this->serviceplanDomainSettingsElements($options);
        }

	}

    /**
     * @param $options
     * @return mixed
     */
    private function listTemplateAndDefault($options)
    {
        $serviceplan_list = $options["template"];

        $options["template_list"] = array(__("Select a plan"));

        foreach ($serviceplan_list as $plan) {
            $options["template_list"][$plan["id"]] = $plan["name"];
        }

        foreach ($serviceplan_list as $value) {
            if($value["is_default"] == ACTIVE) {
                $default_template = $value["id"];
                $options["default_template_data"] = $value;
            }
        }

        if(isset($default_template))
            $options["default_template"] = $default_template;

        unset($options["template"]);

        return $options;
    }

    /**
     * @param $options
     */
    private function selectTemplateElements($options)
    {
        if(isset($options["default_template"])) {
            // Template List
            $template_select = new Select(
                "template_select",
                $options["template_list"], array(
                    "id" => "template_select",
                    "name" => "template_select",
                )
            );

            $template_select->setDefault($options["default_template"]);
        }
        else {
            // Use Template
            $this->add(new Check("use_template", array(
                "id" => "use_template",
                "name" => "use_template"
            )));
            // Template List
            $template_select = new Select(
                "template_select",
                $options["template_list"], array(
                    "id" => "template_select",
                    "name" => "template_select",
                )
            );
        }
        $this->add($template_select);
    }

    /**
     * @param $options
     */
    private function requiredUserFieldsElements($options)
	{
        // Full Name
		$fullname = new Text("fullname", array(
			"id" => "fullname",
			"name" => "fullname",
		));

        // Username
        $username = new Text("username", array(
			"id" => "username",
			"name" => "username",
		));

        // Password
        $password = new Password("password", array(
			"id" => "password",
			"name" => "password",
		));

        // Email Address
        $email = new Text("email_addr", array(
			"id" => "email_addr",
			"name" => "email_addr",
		));

        if($options["action"] == "edit") {
            $client_data = $options["client_data"];
            $fullname->setAttribute("value", $client_data["fullname"]);
            $username->setAttribute("value", $client_data["username"]);
            $email->setAttribute("value", $client_data["email"]);
        }
        $this->add($fullname);
        $this->add($username);
        $this->add($password);
        $this->add($email);
	}

    /**
     * @param $options
     */
    private function optionalUserFieldsElements($options)
	{
        $ip_address_list = $options["ip_address_list"];
        $owned_ips = [];
        $client_data = [];
        if($options["action"] == "edit") {
            $client_data = $options["client_data"];
            $ips = explode(",", $client_data["UserIpAddresses"]);
            $ip_list = array();
            foreach($ips as $ip) {
                $ip_list[] = explode(":", $ip);
            }
            $owned_ips = array();
            foreach($ip_list as $ipaddr) {
                $ip_address_list[$ipaddr[0]] = $ipaddr[1];
                $owned_ips[] = $ipaddr[0];
            }
        }


        $ipaddress_list = new Select(
            "ipaddress_list",
            $ip_address_list,
            array(
                "multiple" => "multiple",
                "id" => "ipaddress_list",
                "data-selectable" => __("Available"),
                "data-selection" => __("Selected"),
                "class" => "multiselect",
                "name" => "ipaddress_list[]"
            )
        );

        if($options["action"] == "edit") {
            $ipaddress_list->setDefault($owned_ips);
        }
        $this->add($ipaddress_list);

        if($options["action"] == "edit") {
            $telephone = new Text("telephone", array(
                "id" => "telephone",
                "name" => "telephone",
                "value" => $client_data["telephone"],
            ));
            $fax = new Text("fax", array(
                "id" => "fax",
                "name" => "fax",
                "value" => $client_data["fax"],
            ));

            $company = new Text("company", array(
                "id" => "company",
                "name" => "company",
                "value" => $client_data["company"],
            ));

            $address = new Text("address", array(
                "id" => "address",
                "name" => "address",
                "value" => $client_data["address"],
            ));
            $city = new Text("city", array(
                "id" => "city",
                "name" => "city",
                "value" => $client_data["city"],
            ));

            $region = new Text("region", array(
                "id" => "region",
                "name" => "region",
                "value" => $client_data["region"],
            ));

            $country = new Text("country", array(
                "id" => "country",
                "name" => "country",
                "value" => $client_data["country"],
            ));


            $this->add($telephone);
            $this->add($fax);
            $this->add($company);
            $this->add($address);
            $this->add($city);
            $this->add($region);
            $this->add($country);
        }
        else {
            $change_password_after_login = new Check("change_password_after_login", array(
                "id" => "change_password_after_login",
                "name" => "change_password_after_login",
                "checked" => "checked"
            ));
            $this->add($change_password_after_login);
            $user_type = $options["user_type"];
            if(isset($options["client_type"])) {
                $user_type = $options["client_type"];
            }
            $data[""] = __("Please select an user type");

            if ($user_type == ROOT_USER) {
                $data["2"] = "Reseller";
            } else if ($user_type == ADMIN_USER) {
                $data["2"] = "Reseller";
            }

            $data["3"] = "Client";

            // @TODO User type için validation yapilacak
            // User type RESELLER_USER ise otomatik olarak sadece client oluşturabilir. 0 ise Administrator, Reseller veya Client; Eğer 1 ise sadece Reseller veya Client oluşturabilir
            if ($user_type != RESELLER_USER) {
                $type_of_user = new Select(
                    "type_of_user",
                    $data,
                    array(
                        "id" => "type_of_user",
                        "name" => "type_of_user",
                    )
                );
                $this->add($type_of_user);
            }

        }

    }

    /**
     * @param $options
     */
    public function serviceplanLimitsElements($options)
    {

        $overuse = new Check("overuse", array(
            "id" => "overuse",
            "name" => "overuse",
        ));
        if($options["action"] == "edit")
            if((isset($options["client_data"]["overuse"])) && ($options["client_data"]["overuse"] == ACTIVE))
                $overuse->setAttribute("checked","checked");

        if(isset($options["default_template"]))
            if((isset($options["default_template_data"]["overuse"])) && ($options["default_template_data"]["overuse"] == ACTIVE))
                $overuse->setAttribute("checked","checked");

        $this->add($overuse);

        $limit_items = array(
            "client_limit"  => "clients",
            "domain_limit"  => "domain",
            "template_limit"  => "template",
            "disk_limit" => "disk",
            "traffic_limit" => "traffic",
            "subdomain_limit" => "subdomain",
            "domain_alias_limit" => "domain_alias",
            "email_box_limit" => "email_box",
            "email_box_size_limit" => "email_box_size",
            "email_forward_limit" => "email_forward",
            "email_groups_limit" => "email_groups",
            "email_autoresponder_limit" => "email_autoresponder",
            "email_lists_limit" => "email_list",
            "database_limit" => "database",
            "database_size_limit" => "database_size",
            "expire_time" => "expire_time_limit",
        );
        if($options["action"] == "edit") {
            unset($limit_items["expire_time_limit"]);
        }


        $user_type = $options["user_type"];
        if(isset($options["client_type"])) {
            $user_type = $options["client_type"];
        }
        switch($user_type) {
            case 2:
                unset($limit_items["client_limit"]);
                break;
            case 3:
                unset($limit_items["client_limit"]);
                unset($limit_items["domain_limit"]);
                break;
        }

        foreach ($limit_items as $key => $value) {
            if($options["action"] == "edit") {
                if((isset($options["client_data"][$key])) && ($options["client_data"][$key] != UNLIMITED)) {
                    $key_value = $options["client_data"][$key];
                    if(($key == "disk_limit") || ($key == "traffic_limit") || ($key == "email_box_size_limit") || ($key == "database_size_limit"))
                        $key_value = $options["client_data"][$key]/1024/1024;

                    $this->add(new Text ($value, array(
                        "id" => $value,
                        "name" => $value,
                        "class" => "limits",
                        "value" => $key_value
                    )));
                    $this->add(new Check($value."_checks", array(
                        "id" => $value,
                        "name" => $value,
                        "value" => UNLIMITED,
                    )));
                }
                else {
                    $this->add(new Text ($value, array(
                        "id" => $value,
                        "name" => $value,
                        "class" => "limits",
                        "disabled"  => "disabled"
                    )));
                    $this->add(new Check($value."_checks", array(
                        "id" => $value,
                        "name" => $value,
                        "value" => UNLIMITED,
                        "checked" => "checked",
                    )));
                }
            }
            else {
                if(isset($options["default_template"])) {
                    if((isset($options["default_template_data"][$key])) && ($options["default_template_data"][$key] != UNLIMITED)) {
                        $key_value = $options["default_template_data"][$key];
                        if(($key == "disk_limit") || ($key == "traffic_limit") || ($key == "email_box_size_limit") || ($key == "database_size_limit"))
                            $key_value = $options["default_template_data"][$key]/1024/1024;

                        $this->add(new Text ($value, array(
                            "id" => $value,
                            "name" => $value,
                            "class" => "limits",
                            "value" => $key_value
                        )));
                        $this->add(new Check($value."_checks", array(
                            "id" => $value,
                            "name" => $value,
                            "value" => UNLIMITED,
                        )));
                    }
                    else {
                        $this->add(new Text ($value, array(
                            "id" => $value,
                            "name" => $value,
                            "class" => "limits",
                            "disabled" => "disabled"
                        )));
                        $this->add(new Check($value."_checks", array(
                            "id" => $value,
                            "name" => $value,
                            "value" => UNLIMITED,
                            "checked" => "checked",
                        )));
                    }
                }
                else {
                    $this->add(new Text ($value, array(
                        "id" => $value,
                        "name" => $value,
                        "class" => "limits",
                        "disabled" => "disabled"
                    )));
                    $this->add(new Check($value."_checks", array(
                        "id" => $value,
                        "name" => $value,
                        "value" => UNLIMITED,
                        "checked" => "checked",
                    )));
                }
            }
        }
    }

    /**
     * @param $options
     */
    public function serviceplanPermissionElements($options)
    {
        $permission_items = array(
            "access_panel" => "access_panel",
            "man_client" => "access_client",
            "man_domain" => "access_domain",
            "man_subdomain" => "access_subdomain",
            "man_domain_alias" => "access_domain_alias",
            "man_email_alias" => "access_email_alias",
            "man_hosting_settings" => "access_hosting_settings",
            "man_task" => "access_task",
            "man_dns" => "access_dns",
            "man_ssl" => "access_ssl",
            "man_mailing_lists" => "access_mailing_lists",
            "man_spam_filters" => "access_spam_filters",
            "man_backup" => "access_backup",
            "man_database" => "access_database",
            "man_email" => "access_email",
            "man_protect_dir" => "access_protect_dir",
            "man_template" => "access_template",
        );
        $user_type = $options["user_type"];
        if(isset($options["client_type"])) {
            $user_type = $options["client_type"];
        }
        switch($user_type) {
            case 2:
                unset($permission_items["man_client"]);
                break;
            case 3:
                unset($permission_items["man_client"]);
                unset($permission_items["man_domain"]);
                unset($permission_items["man_template"]);
                break;
        }

        foreach ($permission_items as $key => $value) {
            if($options["action"] == "edit") {
                if((isset($options["client_data"][$key])) && ($options["client_data"][$key] == ALLOWED)) {
                    $this->add(new Check(
                        $value,
                        array(
                            "name" => $value,
                            "id" => $value,
                            "type" => "checkbox",
                            "checked" => "checked",
                        )
                    ));
                }
                else {
                    $this->add(new Check(
                        $value,
                        array(
                            "name" => $value,
                            "id" => $value,
                            "type" => "checkbox",
                        )
                    ));
                }
            }
            else {
                if(isset($options["default_template"]) && (isset($options["default_template_data"]))) {
                    if($options["default_template_data"][$key] != ALLOWED) {
                        $this->add(new Check(
                            $value,
                            array(
                                "name" => $value,
                                "id" => $value,
                                "type" => "checkbox",
                            )
                        ));
                    }
                    else {
                        $this->add(new Check(
                            $value,
                            array(
                                "name" => $value,
                                "id" => $value,
                                "type" => "checkbox",
                                "checked" => "checked",
                            )
                        ));
                    }
                }
                else {
                    $this->add(new Check(
                        $value,
                        array(
                            "name" => $value,
                            "id" => $value,
                            "type" => "checkbox",
                            "checked" => "checked",
                        )
                    ));
                }
            }
        }

    }

    /**
     * @param $options
     */
    public function serviceplanDomainSettingsElements($options)
    {
        $php_version = new Select("php_version",array(
            PHP56 => "PHP 5.6",
            PHP72 => "PHP 7.2",
            PHP74 => "PHP 7.4",
        ),array(
            "id" => "php_version",
        ));
        $asp_net_version = new Select("asp_net_version",array(
            v20 => "2.0",
            v40 => "4.0",
        ),array(
            "id" => "asp_net_version",
        ));
        $web_statistics_type = new Select("web_statistics_type",array(
            AWSTATS => "Awstats",
            WEBALIZER => "Web Alizer"
        ),array(
            "id" => "web_statistics_type",
        ));


        $max_cpu_usage = new Text ("max_cpu_usage", array(
            "id" => "max_cpu_usage",
            "name" => "max_cpu_usage",
        ));

        $php_support = new Check("php_support", array(
            "name" => "php_support",
            "id" => "php_support",
        ));

        $asp_support = new Check("asp_support", array(
            "name" => "asp_support",
            "id" => "asp_support",
        ));

        $asp_net_support = new Check("asp_net_support", array(
            "name" => "asp_net_support",
            "id" => "asp_net_support",
        ));

        $asp_net_core_support = new Check("asp_net_core_support", array(
            "name" => "asp_net_core_support",
            "id" => "asp_net_core_support",
        ));

        $cgi_support = new Check("cgi_support", array(
            "name" => "cgi_support",
            "id" => "cgi_support",
        ));

        $perl_support = new Check("perl_support", array(
            "name" => "perl_support",
            "id" => "perl_support",
        ));

        $python_support = new Check("python_support", array(
            "name" => "python_support",
            "id" => "python_support",
        ));

        $web_statistics_support = new Check("web_statistics_support", array(
            "name" => "web_statistics_support",
            "id" => "web_statistics_support",
        ));

        $dedicated_pool = new Check("dedicated_pool", array(
            "name" => "dedicated_pool",
            "id" => "dedicated_pool",
        ));

        $application_pool_mode = new Select("application_pool_mode", [
            INTEGRATED => "Integrated",
            CLASSIC => "Classic",
        ], array(
            "name" => "application_pool_mode",
            "id" => "application_pool_mode",
        ));

        if($options["action"] == "edit") {

            if((isset($options["client_data"]["php"])) && ($options["client_data"]["php"] == ACTIVE)) {
                $php_support->setAttribute("checked","checked");
                $php_version->setDefault($options["client_data"]["php_version"]);
            }
            else {
                $php_version->setAttribute("disabled","disabled");
            }

            if((isset($options["client_data"]["asp"])) && ($options["client_data"]["asp"] == ACTIVE)) {
                $asp_support->setAttribute("checked","checked");
            }

            if((isset($options["client_data"]["aspnet_core"])) && ($options["client_data"]["aspnet_core"] == ACTIVE)) {
                $asp_net_core_support->setAttribute("checked","checked");
            }

            if((isset($options["client_data"]["aspnet"])) && ($options["client_data"]["aspnet"] == ACTIVE)) {
                $asp_net_support->setAttribute("checked","checked");
                $asp_net_version->setDefault($options["client_data"]["aspnet_version"]);
                $application_pool_mode->setAttribute("value", $options["client_data"]["application_pool_mode"]);
            }
            else {
                $application_pool_mode->setAttribute("disabled","disabled");
                $asp_net_version->setAttribute("disabled","disabled");
            }

            if((isset($options["client_data"]["cgi"])) && ($options["client_data"]["cgi"] == ACTIVE)) {
                $cgi_support->setAttribute("checked","checked");
            }
            if((isset($options["client_data"]["perl"])) && ($options["client_data"]["perl"] == ACTIVE)) {
                $perl_support->setAttribute("checked","checked");
            }
            if((isset($options["client_data"]["python"])) && ($options["client_data"]["python"] == ACTIVE)) {
                $python_support->setAttribute("checked","checked");
            }

            if((isset($options["client_data"]["statistic_support"])) && ($options["client_data"]["statistic_support"] == ACTIVE)) {
                $web_statistics_support->setAttribute("checked","checked");
                $web_statistics_type->setDefault($options["client_data"]["statistic_type"]);
            }
            else {
                $web_statistics_type->setAttribute("disabled","disabled");
            }

            if((isset($options["client_data"]["dedicated_pool"])) && ($options["client_data"]["dedicated_pool"] == ACTIVE)) {
                $dedicated_pool->setAttribute("checked","checked");
                $max_cpu_usage->setAttribute("value", $options["client_data"]["max_cpu"]);
            }
            else {
                $max_cpu_usage->setAttribute("disabled","disabled");
            }
        }
        else {
            if((isset($options["default_template_data"]))) {
                $host_settings = $options["default_template_data"];
                if($host_settings["php"] == ACTIVE) {
                    $php_support->setAttribute("checked","checked");
                    $php_version->setDefault($host_settings["php_version"]);
                }
                else {
                    $php_version->setAttribute("disabled","disabled");
                }

                if($host_settings["asp"] == ACTIVE)
                    $asp_support->setAttribute("checked","checked");

                if($host_settings["aspnet_core"] == ACTIVE)
                    $asp_net_core_support->setAttribute("checked","checked");

                if($host_settings["aspnet"] == ACTIVE) {
                    $asp_net_support->setAttribute("checked","checked");
                    $asp_net_version->setDefault($host_settings["aspnet_version"]);
                }
                else {
                    $asp_net_version->setAttribute("disabled","disabled");
                }
                if($host_settings["cgi"] == ACTIVE)
                    $cgi_support->setAttribute("checked","checked");
                if($host_settings["perl"] == ACTIVE)
                    $perl_support->setAttribute("checked","checked");
                if($host_settings["python"] == ACTIVE)
                    $python_support->setAttribute("checked","checked");
                if($host_settings["statistic_support"] == ACTIVE) {
                    $web_statistics_support->setAttribute("checked","checked");
                    $web_statistics_type->setDefault($host_settings["statistic_type"]);
                }
                if($host_settings["dedicated_pool"] == ACTIVE) {
                    $dedicated_pool->setAttribute("checked","checked");
                    $max_cpu_usage->setAttribute("value", $host_settings["max_cpu"]);
                    $application_pool_mode->setAttribute("value", $host_settings["application_pool_mode"]);
                }
                else {
                    $max_cpu_usage->setAttribute("disabled","disabled");
                    $max_cpu_usage->setAttribute("disabled","disabled");
                }
            }
            else {
                $php_support->setAttribute("checked","checked");
                $php_version->setDefault(PHP56);
                $max_cpu_usage->setAttribute("disabled","disabled");
                $asp_net_version->setDefault(v40);
                $web_statistics_type->setAttribute("disabled","disabled");
            }
        }

        $this->add($php_support);
        $this->add($asp_support);
        $this->add($asp_net_support);
        $this->add($asp_net_core_support);
        $this->add($cgi_support);
        $this->add($perl_support);
        $this->add($python_support);
        $this->add($web_statistics_support);
        $this->add($dedicated_pool);
        $this->add($php_version);
        $this->add($asp_net_version);
        $this->add($web_statistics_type);
        $this->add($max_cpu_usage);
        $this->add($application_pool_mode);
    }
}
