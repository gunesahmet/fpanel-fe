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
 */namespace Serviceplans\Controller;

use FPanel\Forms\ServiceplanForm;
use FPanel\Globals\Globals;
use FPanel\Models\Serviceplans;
use FPanel\Models\Users;
use FPanel\System\Serviceplans as Settings;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class ServiceplansController
 * @package Serviceplans\Controller
 */
class ServiceplansController extends ControllerBase
{
    protected $user_data = array();
    protected $js;
    protected $user_type;
    protected $user_id;
    protected $client_id;
    protected $client_type;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $this->user_data = $this->acl->run("templates");
        $user_data = $this->user_data;

        $this->view->setVar("system_items", $user_data);
        $this->view->setVar("username", $user_data["user"]["info"]["username"]);

        $this->user_id = $user_data["user"]["info"]["user_id"];
        $this->user_type = $user_data["user"]["info"]["user_type"];
        $this->view->setVar("user_type", $this->user_type);
        $this->view->setVar("system_type", $user_data["system_data"]["system_type"]);

        $this->breadcrumbs->add(__("Dashboard"),"dashboard"," ");

        $params = $this->dispatcher->getParams();
        if(array_key_exists("type", $params)) {
            switch($params["type"]) {
                case "client":
                    $client_id = $params["client_id"];
                    $user = Users::findFirstById($client_id);
                    if(!$user) {
                        $this->view->setVar("error_content",__("User not found"));
                        $this->response->setStatusCode(404, __("User not found"));
                        $this->view->setVar("error_content",__("User has an error"));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $client_data = $user->toArray();
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($client_id, $client_data["type"]);
                        $this->client_id = $client_id;
                        $this->client_type = $client_data["type"];
                        $this->view->setVar("client_id", $this->client_id);
                        $this->view->setVar("client_type", $this->client_type);

                        $this->breadcrumbs->add(__("User List"),"users"," ");
                        $this->breadcrumbs->add($client_data["username"],"dashboard/client/".$this->client_id," ");
                        $client_menu = $this->sidebar->clientMenu($this->client_type, $this->client_id);
                        $this->view->setVar("client_menu", $client_menu);
                    }
                    break;
                    default:
                        $this->view->setVar("error_content",__("URL not found"));
                        $this->response->setStatusCode(404, __("Url not found"));
                        $this->view->pick("errors/404");
                        return true;
            }
        }
    }

    public function indexAction()
    {
        $this->tag->appendTitle(__("Serviceplan List"));
        $user_id = $this->user_id;


        $this->breadcrumbs->add(__("Serviceplan List"),"","");

        if ($this->request->isAjax() && $this->request->isPost()) {
            $this->view->disable();
            $templates = new Settings();
            if(isset($this->client_id))
                $this->response->setJsonContent($templates->templateList($this->client_id,true))->send();
            else
                $this->response->setJsonContent($templates->templateList($user_id, false))->send();
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function createAction()
    {
        $this->tag->appendTitle(__("Serviceplan Create"));

        $options = array(
            "part"    => "all",
            "action"    => "create",
            "user_type"    => $this->user_type
        );

        if(isset($this->client_id)) {
            $options["client_id"] = $this->client_id;
            $options["client_type"] = $this->client_type;

            $this->breadcrumbs->add(__("Serviceplan List"),"serviceplans/client/".$this->client_id," ");
            $this->breadcrumbs->add(__("Create Serviceplan"),"","");
            $post_url = $this->uri->url_encrypt("serviceplans/client/".$this->client_id);
            $redirect_url = $this->uri->url_encrypt("serviceplans/create/client/".$this->client_id);
        }
        else {
            $this->breadcrumbs->add(__("Serviceplan List"),"serviceplans"," ");
            $this->breadcrumbs->add(__("Create Serviceplan"),"","");
            $post_url = $this->uri->url_encrypt("serviceplans/create");
            $redirect_url = $this->uri->url_encrypt("serviceplans");
        }

        $this->view->setVar("post_url", $post_url);

        if($this->request->isPost()){
            if($this->request->isAjax())
                $this->view->disable();

            $post_data = array(
                "serviceplan_name"   => $this->filter->sanitize($this->request->getPost("serviceplan_name"),"string"),
                "overuse" => $this->request->getPost("overuse",null, NO_OVERUSE),
                "is_default"   => $this->request->getPost("is_default",null,OFF),

                // Limitler
                "client_limit"    => $this->request->getPost("clients"),
                "domain_limit"    => $this->request->getPost("domain"),
                "disk_limit"  => $this->request->getPost("disk"),
                "traffic_limit"   => $this->request->getPost("traffic"),
                "subdomain_limit" => $this->request->getPost("subdomain"),
                "domain_alias_limit"  => $this->request->getPost("domain_alias"),
                "email_box_limit" => $this->request->getPost("email_box"),
                "email_box_size_limit"    => $this->request->getPost("email_box_size"),
                "database_limit"  => $this->request->getPost("database"),
                "database_size_limit" => $this->request->getPost("database_size"),
                "template_limit" => $this->request->getPost("template"),
                "expire_time_limit" => $this->request->getPost("expire_time_limit"),

                "php"  => $this->request->getPost("php_support",null,OFF),
                "php_version"  => $this->request->getPost("php_version",null,""),
                "asp"  => $this->request->getPost("asp_support",null,OFF),
                "aspnet_core"  => $this->request->getPost("asp_net_core_support",null,OFF),
                "aspnet"  => $this->request->getPost("asp_net_support",null,OFF),
                "aspnet_version"  => $this->request->getPost("asp_net_version"),
                "application_pool_mode"  => $this->request->getPost("application_pool_mode"),
                "perl"  => $this->request->getPost("perl_support",null,OFF),
                "python"  => $this->request->getPost("python_support",null,OFF),
                "statistic_support"  => $this->request->getPost("web_statistics_support",null,OFF),
                "statistic_type"  => $this->request->getPost("web_statistics_type",null,NO_STATS),
                "dedicated_pool"  => $this->request->getPost("dedicated_pool",null,OFF),
                "max_cpu"  => $this->request->getPost("max_cpu_usage"),

                "cgi"  => $this->request->getPost("cgi_support",null,OFF),

                "access_panel"  => $this->request->getPost("access_panel",null,OFF),

                // Permissionlar
                "man_client" => $this->request->getPost("access_client",null,OFF),
                "man_domain" => $this->request->getPost("access_domain",null,OFF),
                "man_template"   => $this->request->getPost("access_template",null,OFF),
                "man_hosting_settings"   => $this->request->getPost("access_hosting_settings",null,OFF),
                "man_subdomain"  => $this->request->getPost("access_subdomain",null,OFF),
                "man_database"   => $this->request->getPost("access_database",null,OFF),
                "man_domain_alias"   => $this->request->getPost("access_domain_alias",null,OFF),
                "man_email"  => $this->request->getPost("access_email",null,OFF),
                "man_email_alias"    => $this->request->getPost("access_email_alias",null,OFF),
                "man_task"   => $this->request->getPost("access_task",null,OFF),
                "man_dns"    => $this->request->getPost("access_dns",null,OFF),
                "man_ssl"    => $this->request->getPost("access_ssl",null,OFF),
                "man_backup"   => $this->request->getPost("access_backup",null,OFF),
                "man_protect_dir"    => $this->request->getPost("access_protect_dir",null,OFF),
            );
            $messages = Globals::messages();
            $messages["type"] = "success";
            $messages["redirect_url"] = $redirect_url;
            $messages["title"] = __("Success");
            $messages["messages"] = __("Serviceplan created");

            $serviceplans = new Settings();
            $serviceplans->setPosts($post_data);

            if(isset($this->client_id)) {
                $serviceplans->user_id = $this->client_id;
                $serviceplans->user_type = $this->client_type;
            }
            else {
                $serviceplans->user_id = $this->user_id;
                $serviceplans->user_type = $this->user_type;
            }

            if (!$serviceplans->save()) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Failed to create");
                $messages["errors"] = [];

                foreach ($serviceplans->getMessages() as $title => $message) {
                    $messages["errors"][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages["errors"]));
                else if($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
            }
            else {
                if($this->request->isAjax()) {
                    $this->response->setJsonContent($messages)->send();
                }
                else {
                    $this->flashSession->success(__("Serviceplan is created"));
                    return $this->response->redirect($redirect_url,true);
                }
            }

        }
        $this->view->form = new ServiceplanForm(null, $options);
    }

    public function editAction($sp_id)
    {
        $this->tag->appendTitle(__("Edit Serviceplan"));

        $options = array(
            "part"    => "all",
            "action"    => "edit",
            "user_type"    => $this->user_type,
        );
        if(isset($this->client_id)) {
            $options["client_id"] = $this->client_id;
            $options["client_type"] = $this->client_type;

            $this->breadcrumbs->add(__("Serviceplan List"),"serviceplans/client/".$this->client_id," ");
            $this->breadcrumbs->add(__("Edit Serviceplan"),"","");
            $post_url = $this->uri->url_encrypt("serviceplans/edit/$sp_id/client/".$this->client_id);
            $redirect_url = $this->uri->url_encrypt("serviceplans/client/".$this->client_id);
        }
        else {
            $this->breadcrumbs->add(__("Serviceplan List"),"serviceplans"," ");
            $this->breadcrumbs->add(__("Edit Serviceplan"),"","");
            $post_url = $this->uri->url_encrypt("serviceplans/edit/$sp_id");
            $redirect_url = $this->uri->url_encrypt("serviceplans");
        }
        $this->view->setVar("post_url", $post_url);

        
        $this->view->setVar("id", $sp_id);

        $templates = Serviceplans::findFirstById($sp_id);
        if(!$templates) {
            $this->view->setVar("error_content",__("Serviceplan not found"));
            $this->response->setStatusCode(404, __("Serviceplan not found"));
            $this->view->pick("errors/404");
            return true;
        }

        $options["template"] = $templates->toArray();


        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $post_data = array(
                "serviceplan_name"   => $this->filter->sanitize($this->request->getPost("serviceplan_name"),"string"),
                "overuse" => $this->request->getPost("overuse",null, NO_OVERUSE),
                "is_default"   => $this->request->getPost("is_default",null,OFF),

                // Limitler
                "client_limit"    => $this->request->getPost("clients"),
                "domain_limit"    => $this->request->getPost("domain"),
                "disk_limit"  => $this->request->getPost("disk"),
                "traffic_limit"   => $this->request->getPost("traffic"),
                "subdomain_limit" => $this->request->getPost("subdomain"),
                "domain_alias_limit"  => $this->request->getPost("domain_alias"),
                "email_box_limit" => $this->request->getPost("email_box"),
                "email_box_size_limit"    => $this->request->getPost("email_box_size"),
                "database_limit"  => $this->request->getPost("database"),
                "database_size_limit" => $this->request->getPost("database_size"),
                "template_limit" => $this->request->getPost("template"),
                "expire_time_limit" => $this->request->getPost("expire_time_limit"),

                "php"  => $this->request->getPost("php_support",null,OFF),
                "php_version"  => $this->request->getPost("php_version",null,""),
                "asp"  => $this->request->getPost("asp_support",null,OFF),
                "aspnet_core"  => $this->request->getPost("asp_net_core_support",null,OFF),
                "aspnet"  => $this->request->getPost("asp_net_support",null,OFF),
                "aspnet_version"  => $this->request->getPost("asp_net_version"),
                "application_pool_mode"  => $this->request->getPost("application_pool_mode"),
                "perl"  => $this->request->getPost("perl_support",null,OFF),
                "python"  => $this->request->getPost("python_support",null,OFF),
                "statistic_support"  => $this->request->getPost("web_statistics_support",null,OFF),
                "statistic_type"  => $this->request->getPost("web_statistics_type",null,NO_STATS),
                "dedicated_pool"  => $this->request->getPost("dedicated_pool",null,OFF),
                "max_cpu"  => $this->request->getPost("max_cpu_usage"),

                "cgi"  => $this->request->getPost("cgi_support",null,OFF),

                "access_panel"  => $this->request->getPost("access_panel",null,OFF),

                // Permissionlar
                "man_client" => $this->request->getPost("access_client",null,OFF),
                "man_domain" => $this->request->getPost("access_domain",null,OFF),
                "man_template"   => $this->request->getPost("access_template",null,OFF),
                "man_hosting_settings"   => $this->request->getPost("access_hosting_settings",null,OFF),
                "man_subdomain"  => $this->request->getPost("access_subdomain",null,OFF),
                "man_database"   => $this->request->getPost("access_database",null,OFF),
                "man_domain_alias"   => $this->request->getPost("access_domain_alias",null,OFF),
                "man_email"  => $this->request->getPost("access_email",null,OFF),
                "man_email_alias"    => $this->request->getPost("access_email_alias",null,OFF),
                "man_task"   => $this->request->getPost("access_task",null,OFF),
                "man_dns"    => $this->request->getPost("access_dns",null,OFF),
                "man_ssl"    => $this->request->getPost("access_ssl",null,OFF),
                "man_backup"   => $this->request->getPost("access_backup",null,OFF),
                "man_protect_dir"    => $this->request->getPost("access_protect_dir",null,OFF),
            );

            $messages = Globals::messages();
            $messages["type"] = "success";
            $messages["redirect_url"] = $redirect_url;
            $messages["title"] = __("Success");
            $messages["messages"] = __("Serviceplan updated");

            $serviceplans_update = new Settings();
            $serviceplans_update->setPosts($post_data);
            if(isset($this->client_id)) {
                $post_data["user_id"] = $this->client_id;
                $serviceplans_update->user_type = $this->client_type;
                $serviceplans_update->user_id = $this->client_id;
            }
            else {
                $post_data["user_id"] = $this->user_id;
                $serviceplans_update->user_id = $this->user_id;
                $serviceplans_update->user_type = $this->user_type;
            }

            if (!$serviceplans_update->update($sp_id)) {
                $messages["type"] = "error";
                $messages["title"] = __("Failed");
                $messages["messages"] = __("Failed to update");
                $messages["errors"] = [];
                foreach ($serviceplans_update->getMessages() as $title => $message) {
                    $messages["errors"][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages["errors"]));
                else if($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
            }
            else {
                if($this->request->isAjax()) {
                    $this->response->setJsonContent($messages)->send();
                }
                else {
                    $this->flashSession->success(__("Serviceplan is updated"));
                    $this->response->redirect($redirect_url,true);
                }
            }
        }
        $this->view->form = new ServiceplanForm(null, $options);
    }

    private function delete($id): array
    {
        $serviceplans_delete = new Settings();
        $serviceplans_delete->user_id = $this->user_id;
        $status = $serviceplans_delete->delete($id);
        $url = $this->uri->url_encrypt("serviceplans");
        if(isset($this->client_id)) {
            $url = $this->uri->url_encrypt("serviceplans/client/".$this->client_id);
        }
        $messages = Globals::messages();
        $messages["type"] = "success";
        $messages["title"] = __("Success");
        $messages["messages"] = __("Delete successful");
        $messages["url"] = $url;

        if (!$status) {
            $messages["type"] = "error";
            $messages["title"] = __("Failed");
            $messages["messages"] = __("Failed to delete");
        }
        return $messages;
    }

    public function deleteAction($sp_id)
    {
        $this->view->disable();
        if($this->request->isAjax() == true) {
            $messages = $this->delete($sp_id);

            if(isset($this->client_id)) {
                $messages["url"] = $messages["url"]."/client/".$this->client_id;
            }

            $this->response->setJsonContent($messages)->send();
        }
    }

    public function multipleAction()
    {
        $this->view->disable();
        if($this->request->isAjax() == true) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost("id");
            $url = $this->uri->url_encrypt("serviceplans");
            if(isset($this->client_id)) {
                $url = $this->uri->url_encrypt("serviceplans/client/".$this->client_id);
            }

            $messages = Globals::messages();
            $messages["type"] = "error";
            $messages["title"] = __("Failed");
            $messages["url"] = $url;
            $messages["messages"] = __("Failed to delete");
            $messages["errors"] = [];
            if($this->request->getPost("id") == null) {
                $messages["errors"]["message_summary"] = __("Please select a plan");
            }
            else if($this->request->getPost("action") == null) {
                $messages["errors"]["message_summary"] = __("Please select an action");
            }
            else {
                switch($action) {
                    case "delete":
                        $messages = $this->delete($id);
                        break;
                    default:
                        $messages["errors"]["message_summary"] = __("Action is not found");
                        break;
                }
            }

            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
        }
    }

    public function getAction()
    {
        $id = $this->request->getPost("template");
        $this->view->disable();

        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)){
            if(($id != "0") || ($id != "")) {
                $serviceplan = Serviceplans::findFirstById($id)->toArray();

                if($serviceplan["disk_limit"] != UNLIMITED)
                    $serviceplan["disk_limit"] = $serviceplan["disk_limit"]/1024/1024;

                if($serviceplan["traffic_limit"] != UNLIMITED)
                    $serviceplan["traffic_limit"] = $serviceplan["traffic_limit"]/1024/1024;

                if($serviceplan["email_box_size_limit"] != UNLIMITED)
                    $serviceplan["email_box_size_limit"] = $serviceplan["email_box_size_limit"]/1024/1024;

                if($serviceplan["database_size_limit"] != UNLIMITED)
                    $serviceplan["database_size_limit"] = $serviceplan["database_size_limit"]/1024/1024;
                $this->response->setJsonContent($serviceplan)->send();
            }
        }
    }
}