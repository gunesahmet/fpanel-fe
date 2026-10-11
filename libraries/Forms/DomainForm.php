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

use FPanel\Environments\QueryBuilder;
use FPanel\Models\IpAddress;
use FPanel\Models\Servers;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class DomainForm
 * @package FPanel\Forms
 */
class DomainForm extends Form
{
	public function initialize($entity = null, $options = null)
	{
        if(!empty($options["page"])) {
            $this->domainEditElements($options);
        }
        else {
            $options = $this->listTemplateAndDefault($options);

            $this->selectTemplateElements($options);

            $this->serviceplanLimitsElements($options);

            $this->serviceplanPermissionElements($options);

            $this->requiredDomainFieldsElements($options);

            $this->serviceplanDomainSettingsElements($options);

            $this->serverIpSelection($options);

            $this->cloudItems($options);
        }
	}

    private function listTemplateAndDefault($options)
    {
        $serviceplan_list = $options["template"];

        $options["template_list"] = array();

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

    private function selectTemplateElements($options)
    {
        if(isset($options["default_template"])) {
            $this->add(new Check("use_template", array(
                "id" => "use_template",
                "name" => "use_template",
                "checked" => "checked",
            )));

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
            $this->add(new Check("use_template", array(
                "id" => "use_template",
                "name" => "use_template"
            )));
            $template_select = new Select(
                "template_select",
                $options["template_list"], array(
                    "id" => "template_select",
                    "name" => "template_select",
                )
            );
            $template_select->addOption(__("Select a plan"));
        }
        $this->add($template_select);
    }

    public function serviceplanLimitsElements($options)
    {
        $overuse = new Check("overuse", array(
            "id" => "overuse",
            "name" => "overuse",
        ));
        if($options["action"] == "edit")
            if((isset($options["template"]["overuse"])) && ($options["template"]["overuse"]== OVERUSE))
                $overuse->setAttribute("checked","checked");

        if(isset($options["default_template"]))
            if((isset($options["default_template_data"]["overuse"])) && ($options["default_template_data"]["overuse"]== OVERUSE))
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
            "postgresql_limit" => "postgresql",
            "mssql_limit" => "mssql",
            "mysql_limit" => "mysql",
            "database_limit" => "database",
            "database_size_limit" => "database_size",
            "expire_time" => "expire_time_limit",
        );
        switch($options["user_type"]) {
            case RESELLER_USER:
                unset($limit_items["client_limit"]);
                unset($limit_items["template_limit"]);
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

    private function requiredDomainFieldsElements($options)
	{
        $user_list_client = new Select(
            "user_select",
            $options["user_list"], array(
                "id" => "user_select",
                "name" => "user_select",
            )
        );
        $this->add($user_list_client);

		$domain_name = new Text("domain_name", array(
			"id" => "domain_name",
			"name" => "domain_name",
		));

		$ftp_user = new Text("ftp_user", array(
			"id" => "ftp_user",
			"name" => "ftp_user",
		));

		$password = new Password("password", array(
			"id" => "password",
			"name" => "password",
		));

        $enable_web = new Check("enable_web",array(
            "id" => "enable_web",
            "name" => "enable_web",
            "checked" => "checked"
        ));
        $enable_dns = new Check("enable_dns",array(
            "id" => "enable_dns",
            "name" => "enable_dns",
            "checked" => "checked"
        ));
        $enable_mail = new Check("enable_mail",array(
            "id" => "enable_mail",
            "name" => "enable_mail",
            "checked" => "checked"
        ));
		$this->add($domain_name);
		$this->add($ftp_user);
		$this->add($password);

		$this->add($enable_web);
		$this->add($enable_dns);
		$this->add($enable_mail);
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

        $application_pool_mode = new Select("application_pool_mode",[
            INTEGRATED => "Integrated",
            CLASSIC => "Classic",
        ],array(
            "id" => "application_pool_mode",
        ));

        $web_statistics_type = new Select("web_statistics_type",array(
            AWSTATS => "Awstats",
            WEBALIZER => "WebAlizer"
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

            if((isset($options["template"]["web_statistics"])) && ($options["template"]["web_statistics"] == ACTIVE)) {
                $web_statistics_support->setAttribute("checked","checked");
                $web_statistics_type->setDefault($options["template"]["web_statistics_type"]);
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
            if((isset($options["default_template"])) && (isset($options["default_template_data"]))) {
                if((isset($options["default_template_data"]["php"])) && ($options["default_template_data"]["php"] == ACTIVE))
                    $php_version->setDefault($options["default_template_data"]["php_version"]);

                if((isset($options["default_template_data"]["aspnet"])) && ($options["default_template_data"]["aspnet"] == ACTIVE)) {
                    $asp_net_support->setAttribute("checked", "checked");
                    $asp_net_version->setDefault($options["default_template_data"]["aspnet_version"]);
                }

                if((isset($options["default_template_data"]["aspnet_core"])) && ($options["default_template_data"]["aspnet_core"] == ACTIVE)) {
                    $asp_net_core_support->setAttribute("checked", "checked");
                }

                if((isset($options["default_template_data"]["asp"])) && ($options["default_template_data"]["asp"] == ACTIVE)) {
                    $asp_support->setAttribute("checked", "checked");
                }

                if((isset($options["default_template_data"]["statistic_support"])) && ($options["default_template_data"]["statistic_support"] == ACTIVE))
                    $web_statistics_support->setAttribute("checked","checked");
                if(isset($options["default_template_data"]["statistic_type"])) {
                    $web_statistics_type->setDefault($options["default_template_data"]["statistic_type"]);
                }

                if((isset($options["default_template_data"]["dedicated_pool"])) && ($options["default_template_data"]["dedicated_pool"] == ACTIVE)) {
                    $dedicated_pool->setAttribute("checked","checked");
                    $max_cpu_usage->setAttribute("value", $options["default_template_data"]["max_cpu"]);
                }

                if(isset($options["default_template_data"]["application_pool_mode"]))
                    $application_pool_mode->setDefault($options["default_template_data"]["application_pool_mode"]);

                if((isset($options["default_template_data"]["php"])) && ($options["default_template_data"]["php"] == ACTIVE))
                    $php_support->setAttribute("checked","checked");

                if((isset($options["default_template_data"]["perl"])) && ($options["default_template_data"]["perl"] == ACTIVE))
                    $perl_support->setAttribute("checked","checked");

                if((isset($options["default_template_data"]["cgi"])) && ($options["default_template_data"]["cgi"] == ACTIVE))
                    $cgi_support->setAttribute("checked","checked");

                if((isset($options["default_template_data"]["python"])) && ($options["default_template_data"]["python"] == ACTIVE))
                    $python_support->setAttribute("checked","checked");



            }
            else {
                $php_support->setAttribute("checked","checked");
                $php_version->setDefault(PHP56);
                $asp_net_support->setAttribute("checked","checked");
                $asp_net_core_support->setAttribute("checked","checked");
                $asp_net_version->setDefault(v40);
                $web_statistics_type->setAttribute("disabled","disabled");
                $max_cpu_usage->setAttribute("disabled","disabled");
            }

        }
        $this->add($php_version);
        $this->add($asp_net_version);
        $this->add($application_pool_mode);
        $this->add($web_statistics_type);

        $this->add($php_support);
        $this->add($asp_support);
        $this->add($asp_net_support);
        $this->add($asp_net_core_support);
        $this->add($cgi_support);
        $this->add($perl_support);
        $this->add($python_support);
        $this->add($web_statistics_support);
        $this->add($dedicated_pool);
        $this->add($max_cpu_usage);
    }

    private function serverIpSelection($options)
    {
        $ip_list = $options["ip_list"];
        $ipaddress_list = new Select(
            "ipaddress_list",
            $ip_list,
            array(
                "id" => "ipaddress",
                "name" => "ipaddress_list",
            )
        );
        $this->add($ipaddress_list);
    }

    public function cloudItems($options)
    {
        if($options["system_type"] == CLOUD) {
            $servers = Servers::find("type != 'main'");
            $dns_server_list = [
                "0" => __("Select a dns server")
            ];
            $web_server_list = [
                "0" => __("Select a web server")
            ];
            $mail_server_list = [
                "0" => __("Select a mail server")
            ];
            foreach ($servers as $server) {
                $server_services = json_decode($server->type, true);
                foreach ($server->getIpAddress() as $system_ip_address) {
                    if(isset($server_services["WEB"]) && $server_services["WEB"]) {
                        $web_server_list[$system_ip_address->id] = sprintf("(%s) %s (%s)", ucfirst(strtolower($server->os)), $server->name, $system_ip_address->ip_address);
                    }
                    if(isset($server_services["DNS"]) && $server_services["DNS"]) {
                        $dns_server_list[$system_ip_address->id] = sprintf("(%s) %s (%s)", ucfirst(strtolower($server->os)), $server->name, $system_ip_address->ip_address);
                    }
                    if(isset($server_services["MAIL"]) && $server_services["MAIL"]) {
                        $mail_server_list[$system_ip_address->id] = sprintf("(%s) %s (%s)", ucfirst(strtolower($server->os)), $server->name, $system_ip_address->ip_address);
                    }
                }
            }

            $manual_select_server = new Check("manual_select_server", array(
                "id" => "manual_select_server",
                "name" => "manual_select_server",
            ));

            $dns_servers = new Select(
                "dns_servers",
                $dns_server_list,
                array(
                    "id" => "dns_servers",
                    "name" => "dns_servers",
                )
            );


            $web_servers = new Select(
                "web_servers",
                $web_server_list,
                array(
                    "id" => "web_servers",
                    "name" => "web_servers",
                )
            );


            $mail_servers = new Select(
                "mail_servers",
                $mail_server_list,
                array(
                    "id" => "mail_servers",
                    "name" => "mail_servers",
                )
            );

            $this->add($manual_select_server);
            $this->add($dns_servers);
            $this->add($web_servers);
            $this->add($mail_servers);
        }
    }

    public function getWindowsServers($ip_list): array
    {
        $windows_ip_list = array();
        foreach($ip_list as $id => $ip) {
            $ip_addresses = IpAddress::findFirstById($id);
            $servers = Servers::findFirstById($ip_addresses->server_id);
            if(strpos(strtolower($servers->os),WINDOWS) !== false) {
                $windows_ip_list[$id] = $ip . " (Windows)"." ".$servers->name;
            }
            else {
                $windows_ip_list[$id] = $ip . " (Linux)"." ".$servers->name;
            }
        }
        return $windows_ip_list;
    }

    private function domainEditElements($options)
    {
        $client_data = $options["domain_data"];

        $username = new Text("username",array(
            "id" => "username",
            "disabled" => "disabled",
            "value" => $client_data["user"]["info"]["username"]
        ));

        $password = new Password("password",array(
            "id" => "password",
            "name" => "password"
        ));

        $confirm_password = new Password("confirm_password",array(
            "id" => "confirm_password",
            "name" => "confirm_password"
        ));

        $selectable_parents = $this->getParents();

        $parents = new Select("parents", $selectable_parents,array(
            "id" => "parents",
            "name" => "parents"
        ));


        $parents->setDefault($client_data["user"]["info"]["parent_id"]);

        $template = new Select(
            "templates",
            $this->getTemplates(), array(
                "id" => "templates",
                "name" => "templates",
            )
        );

        $fullname = new Text("fullname", array(
            "id" => "fullname",
            "name" => "fullname",
            "value" => $client_data["user"]["info"]["fullname"],
        ));

        $telephone = new Text("telephone", array(
            "id" => "telephone",
            "name" => "telephone",
            "value" => $client_data["user"]["info"]["telephone"],
        ));

        $fax = new Text("fax", array(
            "id" => "fax",
            "name" => "fax",
            "value" => $client_data["user"]["info"]["fax"],
        ));

        $company = new Text("company", array(
            "id" => "company",
            "name" => "company",
            "value" => $client_data["user"]["info"]["company"],
        ));

        $address = new Text("address", array(
            "id" => "address",
            "name" => "address",
            "value" => $client_data["user"]["info"]["address"],
        ));

        $city = new Text("city", array(
            "id" => "city",
            "name" => "city",
            "value" => $client_data["user"]["info"]["city"],
        ));

        $region = new Text("region", array(
            "id" => "region",
            "name" => "region",
            "value" => $client_data["user"]["info"]["region"],
        ));

        $country = new Text("country", array(
            "id" => "country",
            "name" => "country",
            "value" => $client_data["user"]["info"]["country"],
        ));

        $email = new Text("email_addr", array(
            "id" => "email_addr",
            "name" => "email_addr",
            "value" => $client_data["user"]["info"]["email"],
        ));

        $this->add($username);
        $this->add($password);
        $this->add($confirm_password);
        $this->add($parents);
        $this->add($template);
        $this->add($fullname);
        $this->add($telephone);
        $this->add($fax);
        $this->add($company);
        $this->add($address);
        $this->add($city);
        $this->add($region);
        $this->add($country);
        $this->add($email);

    }

    protected function getParents(): array
    {
        $parents = array();
        $qbuilder = new QueryBuilder();
        if(($this->auth->getUserType() == ROOT_USER) || ($this->auth->getUserType() == ADMIN_USER)) {
            $client_list = $qbuilder->select([
                "users.id",
                "users.username",
            ])
                ->from("users")
                ->where([
                    "conditions" => "users.type in (?1, ?2 , ?3, ?4)",
                    "bind" => [
                        1 => CLIENT_USER ,
                        2 => RESELLER_USER,
                        3 => ROOT_USER,
                        4 => ADMIN_USER,
                    ]
                ])
                ->find();
        }
        else {
            $user_id = $this->auth->getUserId();
            $client_list = $qbuilder->select([
                "clients.id",
                "clients.username",
            ])
                ->from("users")
                ->innerJoin("users as clients","clients.parent = users.id")
                ->where([
                    "conditions" => "users.id = = ?1",
                    "bind" => [1 => $user_id]
                ])
                ->andWhere([
                    "conditions" => "clients.type in (?1)",
                    "bind" => [
                        1 => CLIENT_USER ,
                    ]
                ])
                ->find();

        }
        if(!empty($client_list)) {
            foreach($client_list as $client) {
                $parents[$this->crypto->bencode($client["id"])] = $client["username"];
            }
        }

        return $parents;
    }

    protected function getTemplates(): array
    {
        $templates = array(
            "0" => __("Please select a template"),
        );
        $user_id = $this->auth->getUserId();
        $qbuilder = new QueryBuilder();
        $template_list = $qbuilder->select([
            "serviceplans.id",
            "serviceplans.name",
        ])
            ->from("serviceplans")
            ->where([
                "conditions" => "serviceplans.client_id = ?1",
                "bind" => [1 => $user_id]
            ])
            ->find();
        if(!empty($template_list)) {
            foreach($template_list as $template) {
                $templates[$template["id"]] = $template["name"];
            }
        }
        return $templates;
    }
}