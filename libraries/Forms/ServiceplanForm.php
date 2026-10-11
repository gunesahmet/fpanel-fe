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
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class ServiceplanForm
 * @package FPanel\Forms
 */
class ServiceplanForm extends Form
{
	public function initialize($entity = null, $options = null)
	{
        if($options["part"] == "all") {
            $this->formElements($options);
            $this->serviceplanLimitsElements($options);
            $this->serviceplanPermissionElements($options);
            $this->serviceplanDomainSettingsElements($options);
        }
	}

    public function formElements($options)
    {
        // Template Info
        $template_name = new Text("serviceplan_name", array(
            "id" => "serviceplan_name",
            "type" => "text",
            "name" => "serviceplan_name",
            "placeholder" => __("Serviceplan Name"),
            "autocomplete" => "off"
        ));
        $is_default = new Check("is_default", array(
            "id" => "is_default",
            "name" => "is_default",
            "type" => "checkbox",
            "style" => "float:none"
        ));

        if ($options["action"] == "edit") {
            $template_name->setAttribute("value",str_replace ( "&#39;", "", $options["template"]["name"]));
            if($options["template"]["is_default"] == ACTIVE)
                $is_default->setAttribute("checked","checked");
        }

        $this->add($template_name);
        $this->add($is_default);

    }

	public function serviceplanLimitsElements($options)
	{
        $overuse = new Check("overuse", array(
            "id" => "overuse",
            "name" => "overuse",
        ));
        if($options["action"] == "edit") {
            if((isset($options["template"]["overuse"])) && ($options["template"]["overuse"] == OVERUSE))
                $overuse->setAttribute("checked","checked");
        }
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

        switch($options["user_type"]) {
            case RESELLER_USER:
                unset($limit_items["client_limit"]);
                break;
            case CLIENT_USER:
                unset($limit_items["client_limit"]);
                unset($limit_items["domain_limit"]);
                unset($limit_items["template_limit"]);
                break;
        }

        foreach ($limit_items as $key => $value) {
            if($options["action"] == "edit") {
                if((isset($options["template"][$key])) && ($options["template"][$key] != UNLIMITED)) {
                    $key_value = $options["template"][$key];
                    if(($key == "disk_limit") || ($key == "traffic_limit") || ($key == "email_box_size_limit") || ($key == "database_size_limit"))
                        $key_value = $options["template"][$key]/1024/1024;
                    $this->add(new Text ($value, array(
                        "id" => $value,
                        "name" => $value,
                        "class" => "limits form-control-sm",
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
                        "class" => "limits form-control-sm",
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
                $this->add(new Text ($value, array(
                    "id" => $value,
                    "name" => $value,
                    "class" => "limits form-control-sm",
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

        foreach ($permission_items as $key => $value) {
            if($options["action"] == "edit") {
                if((isset($options["template"][$key])) && ($options["template"][$key] == ALLOWED)) {
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


        $this->add($php_version);
        $this->add($asp_net_version);
        $this->add($web_statistics_type);

        $max_cpu_usage = new Text ("max_cpu_usage", array(
			"id" => "max_cpu_usage",
			"name" => "max_cpu_usage",
		));

        $this->add($max_cpu_usage);

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


        $application_pool_mode = new Select("application_pool_mode", [
            CLASSIC => "Classic",
            INTEGRATED => "Integrated",
        ], array(
            "name" => "application_pool_mode",
            "id" => "application_pool_mode",
        ));

        $dedicated_pool = new Check("dedicated_pool", array(
                "name" => "dedicated_pool",
                "id" => "dedicated_pool",
            ));

        if($options["action"] == "edit") {

            if((isset($options["template"]["php"])) && ($options["template"]["php"] == ACTIVE)) {
                $php_support->setAttribute("checked","checked");
                $php_version->setDefault($options["template"]["php_version"]);
            }
            else {
                $php_version->setAttribute("disabled","disabled");
            }

            if((isset($options["template"]["asp"])) && ($options["template"]["asp"] == ACTIVE)) {
                $asp_support->setAttribute("checked","checked");
            }

            if((isset($options["template"]["aspnet_core"])) && ($options["template"]["aspnet_core"] == ACTIVE)) {
                $asp_net_core_support->setAttribute("checked","checked");
            }

            if((isset($options["template"]["aspnet"])) && ($options["template"]["aspnet"] == ACTIVE)) {
                $asp_net_support->setAttribute("checked","checked");
                $asp_net_version->setDefault($options["template"]["aspnet_version"]);
                $application_pool_mode->setDefault($options["template"]["application_pool_mode"]);
            }
            else {
                $application_pool_mode->setAttribute("disabled","disabled");
                $asp_net_version->setAttribute("disabled","disabled");
            }

            if((isset($options["template"]["cgi"])) && ($options["template"]["cgi"] == ACTIVE)) {
                $cgi_support->setAttribute("checked","checked");
            }
            if((isset($options["template"]["perl"])) && ($options["template"]["perl"] == ACTIVE)) {
                $perl_support->setAttribute("checked","checked");
            }
            if((isset($options["template"]["python"])) && ($options["template"]["python"] == ACTIVE)) {
                $python_support->setAttribute("checked","checked");
            }

            if((isset($options["template"]["statistic_support"])) && ($options["template"]["statistic_support"] == ACTIVE)) {
                $web_statistics_support->setAttribute("checked","checked");
                $web_statistics_type->setDefault($options["template"]["statistic_type"]);
            }
            else {
                $web_statistics_type->setAttribute("disabled","disabled");
            }

            if((isset($options["template"]["dedicated_pool"])) && ($options["template"]["dedicated_pool"] == ACTIVE)) {
                $dedicated_pool->setAttribute("checked","checked");
                $max_cpu_usage->setAttribute("value", $options["template"]["max_cpu"]);
            }
            else {
                $max_cpu_usage->setAttribute("disabled","disabled");
            }
        }
        else {
            $php_support->setAttribute("checked","checked");
            $php_version->setDefault(PHP56);
            $max_cpu_usage->setAttribute("disabled","disabled");
            $asp_net_version->setDefault(v40);
            $asp_net_version->setAttribute("disabled","disabled");
            $web_statistics_type->setAttribute("disabled","disabled");
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
        $this->add($application_pool_mode);

    }
}
