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
 */namespace Domains\Controller;

use FPanel\Components\Components;
use FPanel\Domain\DomainSettings;
use FPanel\Domain\Mails;
use FPanel\Domain\NamedService;
use FPanel\Environments\QueryBuilder;
use FPanel\Forms\DomainForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use FPanel\Models\Serviceplans;
use FPanel\Models\Users;
use FPanel\Server\Ipaddress;
use FPanel\System\SysCore;
use FPanel\Users\Usersdata;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class DomainsController
 * @package Domains\Controller
 */
class DomainsController extends ControllerBase
{
    protected $server_data  = array();
    protected $web_servers = array();
    protected $user_data = array(); // Login olan userin verileri
    protected $client_id;           // Userin kontrol edeceği client idsi
    protected $client_type;         // Userin kontrol edeceği client türü
    protected $client_data;         // Userin kontrol edeceği clientin verileri
    protected $user_id;             // Login olan user idsi
    protected $user_type;           // Login olan user türü
    protected $installed_web = true;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $this->user_data = $this->acl->run("domains");
        $server = new SysCore();
        $user_data = $this->user_data;
        $this->view->setVar("system_items", $user_data);
        $this->server_data = $server->getSystemData();

        if($this->server_data["system_type"] == CLOUD) {
            $get_cloud_parts = $server->getCloudServers();
            if(empty($get_cloud_parts)) {
                $this->tag->appendTitle(__("Error 404 Cloud Server Not Found") );
                $this->response->setStatusCode(404, "Not Found");
                $this->view->setVar("error_content",__("Server not found"));
                $this->view->pick("errors/404");
                $this->installed_web = false;
                return;
            }
            else {
                $web_servers = array();
                foreach ($get_cloud_parts as $server) {
                    if(isset($server["type"]) && $server["type"] != null) {
                        $types = json_decode($server["type"], true);
                        if(json_last_error() == JSON_ERROR_NONE) {
                            if($types["WEB"]) {
                                $web_servers[] = $server["id"];
                            }
                        }
                    }
                }
                if(empty($web_servers)) {
                    $this->view->setVar("error_content",__("Web Server not found"));
                    $this->response->setStatusCode(404, __("Web Server not found"));
                    $this->view->pick("errors/404");
                    $this->installed_web = false;
                    return;
                }
                $this->web_servers = $web_servers;
            }
        }

        $this->user_id = $user_data["user"]["info"]["user_id"];
        $this->user_type = $user_data["user"]["info"]["user_type"];
        $this->breadcrumbs->add(__("Dashboard"),"dashboard"," ");
        $params = $this->dispatcher->getParams();
        if(array_key_exists("type", $params)) {
            switch($params["type"]) {
                case "client":
                    $client_id = $params["client_id"];
                    $client = Users::findFirstById($client_id);
                    if(!$client) {
                        $this->view->setVar("error_content",__("User not found"));
                        $this->response->setStatusCode(404, __("User not found"));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $client_data = $client->toArray();
                        if(($this->auth->getUserType() != ADMIN_USER) && ($this->auth->getUserType() != ROOT_USER))
                            $this->acl->checkParent($client_id, $client_data["type"]);
                        $this->client_id = $client_id;
                        $this->user_id = $client_id;
                        $this->client_type = $client_data["type"];
                        $this->user_type = $client_data["type"];
                        $this->view->setVar("client_type", $this->client_type);
                        $this->view->setVar("client_id", $this->client_id);
                        $this->breadcrumbs->add(__("User List"),"users"," ");
                        $this->breadcrumbs->add($client_data["username"],"dashboard/client/".$client_id," ");
                        $client_menu = $this->sidebar->clientMenu($client_data["type"], $client_id);
                        $this->view->setVar("client_menu", $client_menu);
                    }
                    break;
                default:
                    $this->view->setVar("error_content",__("URL not found"));
                    $this->response->setStatusCode(404, __("URL not found"));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        return true;
    }

    public function indexAction()
    {
        $user_id = $this->user_id;
        $user_type = $this->user_type;
        $this->view->setVar("user_id", $user_id);
        $this->view->setVar("user_type", $user_type);

        $this->tag->appendTitle(__("Domain List"));

        if(isset($this->client_id)) {
            $this->view->setVar("client_id", $this->client_id);
            $this->view->setVar("user_type", $this->client_type);
            $this->view->setVar("user_id", $this->client_id);
        }

        if($this->user_data["user"]["remaining"]["domains"] < "1")
            $active_class = "disabled";
        else
            $active_class = "";

        $this->view->setVar("active_class", $active_class);

        $this->breadcrumbs->add(__("Domain List"),"","");


        if ($this->request->isAjax() && $this->request->isPost()) {
            $this->view->disable();
            $domains = new DomainSettings();
            switch($user_type) {
                case 6:
                case 0:
                    $user_id = null;
                    $user_type = null;
                    if(isset($this->client_id)) {
                        $user_id = $this->client_id;
                        $user_type = $this->client_type;
                    }
                    break;
                case 2:
                case 3:
                    if(isset($this->client_id)) {
                        $user_id = $this->client_id;
                        $user_type = $this->client_type;
                    }
                    break;
            }
            $this->response->setJsonContent($domains->domainList($user_id, $user_type))->send();
        }
    }

    public function createAction()
    {
        if(!$this->installed_web) {
            return true;
        }
        if(is_numeric($this->user_data["user"]["remaining"]["domains"]) && $this->user_data["user"]["remaining"]["domains"] < 1) {
            $this->tag->appendTitle(__("Error 403 You dont have access for this process") );
            $this->response->setStatusCode(403, "Access to that resource is forbidden");
            $this->view->setVar("error_content",__("You dont have access for this process"));
            $this->view->pick("errors/403");
            return true;
        }

        $user = new Usersdata();

        $this->tag->appendTitle(__("Domain Create"));

        $data = $this->user_data;
        $user_id = $data["user"]["info"]["user_id"];
        $user_type = $data["user"]["info"]["user_type"];

        $this->view->setVar("system_items", $data);

        $system_type = $this->server_data["system_type"];
        $this->view->setVar("system_type", $system_type);
        $this->view->setVar("user_type", $user_type);

        $user_list = array();
        if(isset($this->client_id)) {
            $templates = Serviceplans::find("client_id = '$this->client_id'");
            $template_list = array();
            if($templates) {
                $template_list = $templates->toArray();
            }
            $this->breadcrumbs->add(__("Domain List"),"domains/client/".$this->client_id," ");
            $this->view->setVar("client_type", $this->client_type);
            if($this->client_type < 3)
                $user_list = $user->selectableUsers($this->client_id, $this->client_type);

            $redirect_url = $this->uri->url_encrypt("domains/client/".$this->client_id);
            $post_url = $this->uri->url_encrypt("domains/create/client/".$this->client_id);
        }
        else {
            $templates = Serviceplans::find("client_id = '$user_id'");
            $template_list = array();
            if($templates) {
                $template_list = $templates->toArray();
            }
            $this->breadcrumbs->add(__("Domain List"),"domains"," ");
            $user_list = $user->selectableUsers($this->user_id, $this->user_type);
            $redirect_url = $this->uri->url_encrypt("domains");
            $post_url = $this->uri->url_encrypt("domains/create");
        }

        $this->view->setVar("post_url", $post_url);
        $this->breadcrumbs->add(__("Create Domain"),"","");

        $options = array(
            "template" => $template_list,
            "user_id" => $user_id,
            "user_type" => $user_type,
            "system_type" => $system_type,
            "part" => "all",
            "action" => "create",
            "user_list" => $user_list,
        );

        if(isset($this->client_id)) {
            $options["client_id"] = $this->client_id;
            $options["client_type"] = $this->client_type;
            $user_options = array(
                "user_id" => $this->client_id,
                "user_type" => $this->client_type,
            );
        }
        else {
            $user_options = array(
                "user_id" => $user_id,
                "user_type" => $user_type,
            );
        }

        $ip = new Ipaddress();
        $ip_list = $ip->getIpAddress($user_options, $system_type);

        $options["ip_list"] = $ip_list;

        $this->view->form = new DomainForm(null, $options);

        if ($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages["type"] = "success";
            $messages["redirect_url"] = $redirect_url;
            $messages["title"] = __("Success");
            $messages["messages"] = __("Domain create successful");

            $post_data = array(
                // Limit
                "client_limit"    => "0",
                "domain_limit"    => "0",
                "template_limit" => "0",
                "disk_limit"  => $this->request->getPost("disk"),
                "traffic_limit"   => $this->request->getPost("traffic"),
                "subdomain_limit" => $this->request->getPost("subdomain"),
                "domain_alias_limit"  => $this->request->getPost("domain_alias"),
                "email_box_limit" => $this->request->getPost("email_box"),
                "email_box_size_limit"    => $this->request->getPost("email_box_size"),
                "email_forward_limit" => $this->request->getPost("email_forward",null,"0"),
                "email_groups_limit"  => $this->request->getPost("email_groups",null,"0"),
                "email_autoresponder_limit"   => $this->request->getPost("email_autoresponder",null,"0"),
                "email_lists_limit"    => $this->request->getPost("email_list",null,"0"),
                "database_limit"  => $this->request->getPost("database"),
                "database_size_limit" => $this->request->getPost("database_size"),
                "expire_time_limit" => $this->request->getPost("expire_time_limit"),
                "overuse" => $this->request->getPost("overuse",null,OFF),

                // Permission
                "man_client" => OFF,
                "man_domain" => OFF,
                "man_template"   => OFF,
                "access_panel"  => $this->request->getPost("access_panel",null,OFF),
                "man_hosting_settings"   => $this->request->getPost("access_hosting_settings",null,OFF),
                "man_subdomain"  => $this->request->getPost("access_subdomain",null,OFF),
                "man_database"   => $this->request->getPost("access_database",null,OFF),
                "man_domain_alias"   => $this->request->getPost("access_domain_alias",null,OFF),
                "man_email"  => $this->request->getPost("access_email",null,OFF),
                "man_email_alias"    => $this->request->getPost("access_email_alias",null,OFF),
                "man_mailing_lists"  => $this->request->getPost("access_mailing_lists",null,OFF),
                "man_spam_filters"   => $this->request->getPost("access_spam_filters",null,OFF),
                "man_task"   => $this->request->getPost("access_task",null,OFF),
                "man_dns"    => $this->request->getPost("access_dns",null,OFF),
                "man_ssl"    => $this->request->getPost("access_ssl",null,OFF),
                "man_backup"   => $this->request->getPost("access_backup",null,OFF),
                "man_protect_dir"    => $this->request->getPost("access_protect_dir",null,OFF),

                // Domain Settings
                "php"  => $this->request->getPost("php_support",null,OFF),
                "php_version"  => $this->request->getPost("php_version",null,""),
                "cgi"  => $this->request->getPost("cgi_support",null,OFF),
                "perl"  => $this->request->getPost("perl_support",null,OFF),
                "python"  => $this->request->getPost("python_support",null,OFF),
                "fast_cgi"  => OFF,
                "web_statistics"  => $this->request->getPost("web_statistics_support",null,OFF),
                "web_statistics_type"  => $this->request->getPost("web_statistics_type",null),
                "asp"  => $this->request->getPost("asp_support",null,OFF),
                "aspnet_core"  => $this->request->getPost("asp_net_core_support",null,OFF),
                "aspnet"  => $this->request->getPost("asp_net_support",null,OFF),
                "aspnet_version"  => $this->request->getPost("asp_net_version",null,""),
                "dedicated_pool"  => $this->request->getPost("dedicated_pool",null,OFF),
                "max_cpu"  => $this->request->getPost("max_cpu_usage",null,"0"),
                "application_pool_mode"  => $this->request->getPost("application_pool_mode",null,INTEGRATED),
                "ssl"  => OFF,
                // Userdata
                "domain_name"   => $this->request->getPost("domain_name"),
                "name"   => $this->request->getPost("domain_name"),
                "username"   => $this->request->getPost("domain_name"),
                "fullname"   => $this->request->getPost("domain_name"),
                "type"  => DOMAIN_USER,
                "email"   => "info@".$this->request->getPost("domain_name"),
                "ftpname"  => $this->request->getPost("ftp_user"),
                "password"  => $this->request->getPost("password"),
                "company" => null,
                "telephone" => null,
                "address" => null,
                "city" => null,
                "zipcode" => null,
                "country" => null,
                // Selecteds
                "selected_user"  => $this->request->getPost("user_select"),
                "ipaddr_id"    => $this->request->getPost("ipaddress_list"),
                "system_type"  => $system_type,
                "user_list"  => $user_list,
                "ip_list" => $ip_list,
                "web_service" => "on",
                "dns_service" => $this->request->getPost("enable_dns",null,OFF),
                "mail_service" => $this->request->getPost("enable_mail",null,OFF),

                "manual_select_server" => $this->request->getPost("manual_select_server", null, OFF),

                "dns_servers" => $this->request->getPost("dns_servers", null, "0"),
                "mail_servers" => $this->request->getPost("mail_servers", null, "0"),
                "web_servers" => $this->request->getPost("web_servers", null, "0"),
            );

            if(isset($this->client_id)) {
                if($this->client_type == RESELLER_USER)
                    $user_id = $this->request->getPost("user_select");
                else
                    $user_id = $this->client_id;
                $user_type = $this->client_type;
                $post_data["selected_user"] = $this->client_id;
            }
            else {
                if($this->user_type < CLIENT_USER)
                    $user_id = $this->request->getPost("user_select");
                $user_type = $this->user_type;
            }

            $domain_components = new Components();

            $domain_status = $domain_components->userData(array(
                    "user_id" => $user_id,
                    "user_type" => $user_type,
                ))
                ->options($options)
                ->post($post_data)
                ->create(DOMAIN);

            if(!$domain_status) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Adding failed");
                $messages["errors"] = [];
                foreach ($domain_components->getMessages() as $title => $message) {
                    $messages["errors"][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages["errors"]));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($domain_status) {
                    $this->flashSession->success(__("Domain created"));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function editAction($id)
    {
        $domain = Domains::findFirstById($id);
        if(!$domain) {
            $this->view->setVar("error_content",__("Domain not found"));
            $this->response->setStatusCode(404, __("Domain not found"));
            $this->view->pick("errors/404");
            return true;
        }
        $domain_client = Users::findFirst("username = '$domain->name'");
        $client_data = $this->acl->getClientData([
            "id" => $domain_client->id,
            "type" => $domain_client->type,
            "username" => $domain->name
        ]);


        if((empty($client_data)) || ($domain->name != $client_data["user"]["info"]["username"])) {
            $this->view->setVar("error_content",__("Client not found"));
            $this->response->setStatusCode(404, __("Client not found"));
            $this->view->pick("errors/404");
            return true;
        }

        $this->tag->appendTitle(__("Edit Domain Settings"));

        if(isset($this->client_id)) {
            $this->breadcrumbs->add(__("Domain List"),"domains/client/".$this->client_id," ");
            $go_back_url = $this->uri->url_encrypt("domains/client/".$this->client_id);
            $post_url = $this->uri->url_encrypt("domains/edit/$id/client/".$this->client_id);
        }
        else {
            $this->breadcrumbs->add(__("Domain List"),"domains"," ");
            $go_back_url = $this->uri->url_encrypt("domains");
            $post_url = $this->uri->url_encrypt("domains/edit/$id");
        }

        $this->view->setVar("post_url", $post_url);
        $this->breadcrumbs->add(__("Edit Domain Settings"),"","");


        $options = array(
            "page" => "edit",
            "domain_data" => $client_data
        );

        $this->view->setVar("client_data", $client_data);
        $this->view->form = new DomainForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages["type"] = "success";
            $messages["title"] = __("Success");
            $messages["messages"] = __("Update successful");

            $domain_settings = new DomainSettings();
            $domain_settings->setPosts(array(
                "password" => $this->request->getPost("password",null,null),
                "fullname" => $this->request->getPost("fullname",null,null),
                "parent" => $this->request->getPost("parents",null,null),
                "telephone" => $this->request->getPost("telephone",null,null),
                "fax" => $this->request->getPost("fax",null,null),
                "company" => $this->request->getPost("company",null,null),
                "email" => $this->request->getPost("email_addr",null,null),
                "address" => $this->request->getPost("address",null,null),
                "city" => $this->request->getPost("city",null,null),
                "region" => $this->request->getPost("region",null,null),
                "country" => $this->request->getPost("country",null,null),
            ));
            $status = $domain_settings->edit($id);
            if(!$status) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Update failed");
                $messages["errors"] = [];

                foreach ($domain_settings->getMessages() as $title => $message) {
                    $messages["errors"][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages["errors"]));
            }


            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status) {
                    $this->flashSession->success(__("Domain updated"));
                    return $this->response->redirect($go_back_url,true);
                }
            }
        }


        return true;
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        if ($this->request->isAjax() && $this->request->isPost()) {
            if(isset($this->client_id)) {
                $user_id = $this->client_id;
                $user_type = $this->client_type;
            }
            else {
                $user_id = $this->user_id;
                $user_type = $this->user_type;
            }
            $messages = $this->delete($user_id, $user_type, $id);
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $messages = Globals::messages();
            $messages["type"] = "success";
            $messages["title"] = __("Success");
            $messages["messages"] = __("Delete successful");
            if($this->request->isAjax()) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("System Error");
                $this->response->setJsonContent($messages)->send();
                exit;
            }
            else {
                $this->view->setVar("error_content",__("URL not found"));
                $this->response->setStatusCode(404, __("URL not found"));
                $this->view->pick("errors/404");
                return true;
            }
        }
        return true;
    }

    public function suspendAction($id, $status)
    {
        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $messages = Globals::messages();
            $messages["type"] = "success";
            $messages["title"] = __("Success");
            $messages["messages"] = __("Suspend successful");

            if(isset($this->client_id)) {
                $user_id = $this->client_id;
                $user_type = $this->client_type;
            }
            else {
                $user_id = $this->user_id;
                $user_type = $this->user_type;
            }

            $domains = Domains::findFirstById($id);
            if($domains) {
                if($domains->status != ACTIVE) {
                    $messages = $this->unsuspend($user_id, $user_type, $id, $this->request->getPost("message"));
                }
                else {
                    $messages = $this->suspend($user_id, $user_type, $id, $this->request->getPost("message"));
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
        else {
            if($this->request->isAjax()) {

                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("System Error");
                $this->response->setJsonContent($messages)->send();
                exit;
            }
            else {
                $this->view->setVar("error_content",__("URL not found"));
                $this->response->setStatusCode(404, __("URL not found"));
                $this->view->pick("errors/404");
                return true;
            }
        }
        return true;
    }

    public function multipleAction()
    {

        $this->view->disable();
        $messages = Globals::messages();
        $messages["type"] = "success";
        $messages["title"] = __("Success");
        $messages["messages"] = __("Action successful");

        if (($this->request->isPost()) && ($this->request->isAjax())) {

            $user_id = $this->user_id;
            $user_type = $this->user_type;

            $id = $this->request->getPost("id");
            if($this->request->getPost("id") == null) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Please select a domain");

            }
            else if($this->request->getPost("action") == null) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Please select an action");

            }
            else {
                switch($this->request->getPost("action")) {
                    case "suspend":
                        $messages = $this->suspend($user_id, $user_type, $id,"Suspended by owner");
                        break;
                    case "unsuspend":
                        $messages = $this->unsuspend($user_id, $user_type, $id,"Unuspended by owner");
                        break;
                    case "delete":
                        $messages = $this->delete($user_id, $user_type, $id);
                        break;
                    case "changeparent":
                        $parent_id = $this->request->getPost("parent");

                        $messages = $this->changeParent($user_id, $user_type, $id, $parent_id);
                        break;
                    case "changelimits":
                        $temaplte_id = $this->request->getPost("template");

                        $messages = $this->changelimits($user_id, $user_type, $id, $temaplte_id);
                        break;
                    default:
                        $messages["type"] = "error";
                        $messages["title"] = __("Failed");
                        $messages["messages"] = __("Action is not found");

                        break;
                }

            }
            $this->response->setJsonContent($messages)->send();
            return true;
        }
        else {
            if($this->request->isAjax()) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("System Error");

                $this->response->setJsonContent($messages)->send();
                exit;
            }
            else {
                $this->view->setVar("error_content",__("URL not found"));
                $this->response->setStatusCode(404, __("URL not found"));
                $this->view->pick("errors/404");
                return true;
            }
        }
    }

    private function suspend($user_id, $user_type, $id, $message = null): array
    {
        $domains = Domains::findFirstById($id);
        $messages = Globals::messages();
        $messages["type"] = "success";
        $messages["title"] = __("Success");

        if(!$domains) {
            $messages["type"] = "error";
            $messages["title"] = __("Failed");
            $messages["messages"] = __("Domain is not found");
        }
        else {
            if($user_id == $domains->client_id) {
                $status = MANUAL_PASSIVE_BY_OWNER;
            }
            else {
                $status = MANUAL_PASSIVE_BY_ADMIN;
            }

            $domain = new Components();
            $domain->userData(array(
                "user_id" => $user_id,
                "user_type" => $user_type,
            ));

            $result = $domain->suspend("domain", $id, $message, $status);
            if(!$result) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Domain is not found");

            }
            else {
                $content = __("Domain is suspended");
                $messages["messages"] = $content;

            }
        }
        return $messages;
    }

    private function unsuspend($user_id, $user_type, $id, $message = null): array
    {
        $domains = Domains::findFirstById($id);
        $messages = Globals::messages();
        $messages["type"] = "success";
        $messages["title"] = __("Success");

        if(!$domains) {
            $messages["type"] = "error";
            $messages["title"] = __("Failed");
            $messages["messages"] = __("Domain is not found");
        }
        else {

            $domain = new Components();
            $domain->userData(array(
                "user_id" => $user_id,
                "user_type" => $user_type,
            ));

            $result = $domain->unsuspend("domain", $id, $message);

            if(!$result) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Domain is not found");

            }
            else {
                $content = __("Domain is unsuspended");
                $messages["messages"] = $content;
            }
        }
        return $messages;
    }

    private function delete($user_id, $user_type, $domain_id): array
    {
        $messages = Globals::messages();
        $messages["type"] = "success";
        $messages["title"] = __("Success");
        $messages["messages"] = __("Delete successful");

        $domain = new Components();
        $status = $domain->userData(array(
            "user_id" => $user_id,
            "user_type" => $user_type,
        ))
        ->delete(DOMAIN, $domain_id);

        if(!$status) {
            $messages["type"] = "error";
            $messages["title"] = __("Failed");
            $messages["messages"] = __("Delete failed");
            $messages["errors"] = [];
            if($domain->getMessages() != null) {
                foreach ($domain->getMessages() as $title => $message) {
                    $messages["errors"][$title] = $message;
                }
            }

        }
        return $messages;
    }

    public function changeParentAction($id)
    {
        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            if(isset($this->client_id)) {
                $user_id = $this->client_id;
                $user_type = $this->client_type;
            }
            else {
                $user_id = $this->user_id;
                $user_type = $this->user_type;
            }
            $parent_id = $this->request->getPost("parent");

            $messages = $this->changeParent($user_id, $user_type, $id, $parent_id);

            $this->response->setJsonContent($messages)->send();
        }
        else {
            if($this->request->isAjax()) {
                $messages = Globals::messages();
                $messages["type"] = "success";
                $messages["title"] = __("Success");
                $messages["messages"] = __("Adding successful");
                $this->response->setJsonContent($messages)->send();
                exit;
            }
            else {
                $this->view->setVar("error_content",__("URL not found"));
                $this->response->setStatusCode(404, __("URL not found"));
                $this->view->pick("errors/404");
                return true;
            }
        }
        return true;
    }

    private function changeParent($user_id, $user_type, $id, $parent_id): array
    {
        $messages = Globals::messages();
        $messages["type"] = "success";
        $messages["title"] = __("Success");
        $messages["messages"] = __("Parent changing successful");

        $domains = new DomainSettings();
        $domains->setPosts(array(
            "parent" => $parent_id,
            "user_id" => $user_id,
            "user_type" => $user_type,
            "domain_id" => $id,
        ));
        if(!$domains->changeParent($id)) {
            $messages["type"] = "error";
            $messages["title"] = __("Failed");
            $messages["messages"] = __("Parent change failed");
            $messages["errors"] = [];
            foreach ($domains->getMessages() as $title => $message) {
                $messages["errors"][$title] = $message;
            }
        }

        return $messages;
    }

    public function serviceAction($domain_id)
    {
        $this->view->disable();

        if($this->request->isAjax() && $this->request->isPost()) {

            $dns = new NamedService();
            $mail = new Mails();

            $dns_service = "0";
            $mail_service = "0";

            $messages = Globals::messages();
            $messages["type"] = "success";
            $messages["title"] = __("Success");
            $messages["messages"] = __("Request successful");

            if($this->request->getPost("enable_dns") != null) {
                if($this->request->getPost("enable_dns") == "on") {
                    $dns_service = "1";
                }
                if(!$dns->changeDnsSettings($dns_service, $domain_id)) {
                    $messages["type"] = "error";
                    $messages["title"] = __("Failed");
                    $messages["messages"] = __("Request failed");
                    $messages["errors"] = [];

                    foreach ($dns->getMessages() as $title => $message) {
                        $messages["errors"][$title] = $message;
                    }
                }
                else {
                    if(!$this->request->isAjax())
                        $this->flashSession->success(__("Request completed"));
                 }
            }
            else if($this->request->getPost("enable_mail") != null) {
                if($this->request->getPost("enable_mail") == "on") {
                    $mail_service = "1";
                }
                if($mail->changeMailSettings($mail_service, $domain_id) == false) {
                    $messages["type"] = "error";
                    $messages["title"] = __("Failed");
                    $messages["messages"] = __("Request failed");
                    $messages["errors"] = [];
                    foreach ($mail->getMessages() as $title => $message) {
                        $messages["errors"][$title] = $message;
                    }
                }
                else {
                    if(!$this->request->isAjax())
                        $this->flashSession->success(__("Request completed"));
                }

            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
        }
        else {
            if($this->request->isAjax()) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("System error");
                $this->response->setJsonContent($messages)->send();
            }
            else {
                return $this->response->redirect($this->uri->url_encrypt("dashboard"), true);
            }
        }
        return true;
    }

    public function getClientsAction($user_id, $user_type)
    {
        $this->view->disable();

        if(($this->request->isAjax()) && ($this->request->isPost())) {
            $qbuilder = new QueryBuilder();
            if(($user_type != ROOT_USER) && ($user_type != ADMIN_USER)) {
                $client_list = $qbuilder->select([
                    "clients.id",
                    "clients.username"
                ])
                    ->from("users")
                    ->innerJoin("users as clients","clients.parent = users.id")
                    ->where([
                        "conditions" => "users.id = ?1",
                        "bind" => [1 => $user_id]
                    ])
                    ->andWhere([
                        "conditions" => "clients.type != ?1",
                        "bind" => [1 => DOMAIN_USER]
                    ])
                    ->andWhere([
                        "conditions" => "clients.type != ?1",
                        "bind" => [1 => SUPPORT_USER]
                    ])
                    ->find();
            }
            else {
                $client_list = $qbuilder->select([
                    "users.id",
                    "users.username"
                ])
                    ->from("users")
                    ->where([
                        "conditions" => "users.type != ?1",
                        "bind" => [1 => DOMAIN_USER]
                    ])
                    ->andWhere([
                        "conditions" => "users.type != ?1",
                        "bind" => [1 => SUPPORT_USER]
                    ])
                    ->find();
            }

            $list = array();

            if(!empty($client_list)) {
                foreach($client_list as $client) {
                    $list[$client["id"]] = $client["username"];
                }
            }

            return $this->response->setJsonContent($list)->send();
        }
        else {
            if($this->request->isAjax()) {
                $dataRules = array("message" => "error", "content" => __("System Error"));
                $this->response->setJsonContent($dataRules)->send();
                exit;
            }
            else {
                return $this->response->redirect($this->uri->url_encrypt("dashboard"), true);
            }
        }
    }

    /**
     * @param $id
     * @return ResponseInterface|void
     */
    public function changeLimitsAction($id)
    {
        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            if(isset($this->client_id)) {
                $user_id = $this->client_id;
                $user_type = $this->client_type;
            }
            else {
                $user_id = $this->user_id;
                $user_type = $this->user_type;
            }
            $template_id = $this->request->getPost("serviceplan");

            $messages = $this->changelimits($user_id, $user_type, $id, $template_id);

            $this->response->setJsonContent($messages)->send();
        }
        else {
            if($this->request->isAjax()) {
                $messages = Globals::messages();
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("System error");
                $this->response->setJsonContent($messages)->send();
                exit;
            }
            else {
                return $this->response->redirect($this->uri->url_encrypt("dashboard"), true);
            }
        }
    }

    private function changelimits($user_id, $user_type, $id, $template_id): array
    {
        $messages = Globals::messages();
        $messages["type"] = "success";
        $messages["title"] = __("Success");
        $messages["messages"] = __("Domain limits changed");
        $domains = new DomainSettings();
        $domains->setPosts(array(
            "user_id" => $user_id,
            "user_type" => $user_type,
        ));

        if(!$domains->changeLimits($id, $template_id)) {
            $messages["type"] = "error";
            $messages["title"] = __("Failed");
            $messages["messages"] = __("Limits change failed");
            $messages["errors"] = [];
            foreach ($domains->getMessages() as $title => $message) {
                $messages["errors"][$title] = $message;
            }
        }


        return $messages;
    }

}