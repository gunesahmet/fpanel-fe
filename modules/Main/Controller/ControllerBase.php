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
 */namespace Main\Controller;

use FPanel\Environments\Parser;
use FPanel\Models\SystemSettings;
use FPanel\System\SysCore;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;

class ControllerBase extends Controller
{
    public $themes;
    public $default_theme;
    public $parsed_data;
    public $api = false;
    public $telegram = false;
    public $session_status = false;
    private $start_time;
    protected $script_vars;

    public function beforeExecuteRoute($dispatcher)
    {
        header('Server: FPanel HTTP Server 1.4');
        $this->start_time = microtime(true); // Top of page
        $parse_ini = new Parser(ROOTPATH.'/config/config.ini');
        $this->parsed_data = $parse_ini->data;
        $this->checkBlockList();
        $parse_ini = new Parser(ROOTPATH.'/config/config.ini');
        $this->parsed_data = $parse_ini->data;
        if((isset($this->parsed_data['api']['status'])) && ($this->parsed_data['api']['status'] == "1")) {
            $this->api = true;
        }

        if((isset($this->parsed_data['telegram']['status'])) && ($this->parsed_data['telegram']['status'] == "1")) {
            $this->telegram = true;
        }

        if($this->api) {
            if(strpos(ltrim($_GET['_url'],"/"), trim($this->config->api->address)) === false) {
                $this->api = false;
            }
        }

        if($this->telegram) {
            if(($this->dispatcher->getModuleName() != "Telegram") || ($this->dispatcher->getActionName() != "api")) {
                $this->telegram = false;
            }
        }

        if(!$this->api && !$this->telegram) {
            $this->logging();
        }

        if(!$this->telegram) {
            $this->session_status = $this->auth->checkAuth();
        }

        if(!$this->api && !$this->telegram) {
            $this->env->generateCsrfTokenKey();
            $syscore = new SysCore();
            $syscore->checkCsrf();

            if(($this->dispatcher->getControllerName() != "main") && ($this->dispatcher->getActionName() != "login")) {
                if(!$this->session_status) {
                    $this->response->redirect("/");
                }
            }
        }

        if($this->panel_installed) {
            $panel_data = SystemSettings::findFirst("key = 'system_data'")->toArray();
            $panel_settings = json_decode($panel_data['value'],true);
            define('PANEL_TYPE', trim($panel_settings['system_type']));
        }

        if($this->session_status) {
            $this->setScriptVars();
        }
    }

    public function initialize()
    {
        $this->response->setHeader("x-request-id", $this->request_id);
        $this->response->setHeader("x-request-auth", $this->session_status);
        $ss = false;
        $module_name = $this->dispatcher->getModuleName();
        $controller_name = $this->dispatcher->getControllerName();
        $method_name = $this->dispatcher->getActionName();
        $this->view->setVar("controller_name", $controller_name);
        if(!$this->telegram) {
            if(($module_name != "Api") && ($module_name != "Main")) {
                if(($controller_name != "main") && ($controller_name != "api")) {
                    if($this->session_status) {
                        $ss = true;
                        if(!$this->fcookies->has("language")) {
                            $this->fcookies->set("language", "en_US", time() + 15 * 86400);
                        }
                        $this->changeLanguage();

                        $this->view->setVar('firstboot',false);

                        if($this->fcookies->has("template"))
                            $template = $this->fcookies->get("template");
                        else {
                            $template = "default";
                            $this->fcookies->set("template", $template, time() + 15 * 86400);
                        }

                        $this->default_theme = $template;
                        $this->view->setViewsDir(ROOTPATH."/templates/".$template);
                        $this->view->setVar('user_type', $this->auth->getUserType());
                        $this->tag->setTitle("");

                        $this->view->setVar('controller_name', $controller_name);
                        $this->themes = $this->getTemplateList();
                        $this->view->setVar('themes', $this->themes);
                        $this->view->setVar('method_name', $method_name);

                    }
                    else {
                        if($this->request->isGet()) {
                            header("Location: /");
                            exit(0);
                        }
                    }
                }
            }
        }
        $this->initView(true, $controller_name);
        $this->view->setVar("ss", $ss);
    }

    /**
     * @return array
     */
    private function getNames(): array
    {
        $names = [];
        foreach ($this->router->getRoutes() as $route) {
            $names[] = $route->getName();
        }
        return array_unique($names);
    }

    private function checkBlockList(): void
    {
		if(isset($this->parsed_data['session']['block'])) {
            $blocked_ip = $this->parsed_data['session']['blocked_ip'];
            $blocked_status = $this->parsed_data['session']['block'];

            $allowed_ip = $this->parsed_data['session']['allowed_ip'];
            $allowed_status = $this->parsed_data['session']['allow'];

            if($blocked_status === "true") {
                $blocked_list = explode(',', $blocked_ip);
                foreach($blocked_list as $ip) {
                    if($ip == $this->request->getClientAddress()) {
                        if(!$this->api) {
                            $this->view->setVar('error_content',__('Forbidden'));
                        }
                        $this->response->setStatusCode(403, __('Forbidden'));
                        return;
                    }
                }
            }

            if($allowed_status == "true") {
                $allowed_list = explode(',', $allowed_ip);
                $status = false;
                foreach($allowed_list as $ip) {
                    if($ip == $this->request->getClientAddress()) {
                        $status = true;
                    }
                }

                if(!$status) {
                    if(!$this->api) {
                        $this->view->setVar('error_content',__('Forbidden'));
                    }
                    $this->response->setStatusCode(403, __('Forbidden'));
                }
            }

        }
    }

    private function getTemplateList(): array
    {
        $template_dir = ROOTPATH."/templates";
        $template_list = array_diff(scandir($template_dir), array('..', '.'));

        $result = array();
        foreach($template_list as $template) {
            $result[$template] = ucfirst($template);
        }
        return $result;
    }

    protected function initView($ajax, $controller_name)
    {
        if (!$ajax) {
            if (!$this->request->isAjax() && $controller_name != "install") {
                $this->view->disableLevel(array(
                    View::LEVEL_LAYOUT => true,
                    View::LEVEL_MAIN_LAYOUT => true
                ));
            }
            else {
                if ($this->request->isAjax()) {
                    $this->view->disableLevel(array(
                        View::LEVEL_LAYOUT => true,
                        View::LEVEL_MAIN_LAYOUT => true
                    ));
                }
            }
        }
        else {
            if ($this->request->isAjax()) {
                $this->view->disableLevel(array(
                    View::LEVEL_LAYOUT => true,
                    View::LEVEL_MAIN_LAYOUT => true
                ));
            }
        }
    }

    private function setScriptVars()
    {
        $this->script_vars = [
            'enter_file_name'       => __('Enter File Name'),
            'enter_folder_name'     => __('Enter Folder Name'),
            'create_file'           => __('Create File'),
            'create_folder'         => __('Create Folder'),
            'success'               => __('Success'),
            'copied'                => __('Copied'),
            'submit'                => __('Submit'),
            'session_timeout'       => __('Session Timeout'),
            'rename'                => __('Rename'),
            'description'           => __('Description'),
            'close'                 => __('Close'),
            'loading'               => __('Loading'),
            'stop'                  => __('Stop'),
            'start'                 => __('Start'),
            'resume_upload'         => __('Resume'),
            'pause_upload'          => __('Pause'),
            'uploading'             => __('Uploading'),
            'used'                  => __('Used'),
            'free'                  => __('Free'),
            'rename_message'        => __('Rename'),
            'system_action_url'     => $this->uri->url_encrypt('system/checkstatus'),
            'folder_name'           => __('Directory Name'),
            'file_name'             => __('File Name'),
            'move_message'          => __('Move'),
            'target_folder'         => __('Target Folder'),
            'are_you_sure'          => __('Are you sure?'),
            'records_per_page'      => __('records per page'),
            'no_data_available'     => __('No data available in table'),
            'showing_in_table'      => __('Showing page _PAGE_ of _PAGES_'),
            'no_record_available'   => __('No records available'),
            'filtered_table'        => __('(filtered from _MAX_ total records)'),
            'first'                 => __('First'),
            'last'                  => __('Last'),
            'next'                  => __('Next'),
            'previous'              => __('Previous'),
            'search'                => __('Search'),
            'name'                  => __('Name'),
            'modified'              => __('Modified'),
            'size'                  => __('Size'),
            'code'                  => __('Code'),
            'type'                  => __('Type'),
            'message'               => __('Message'),
            'client'                => __('Client'),
            'date'                  => __('Date'),
            'refresh'               => __('Refresh'),
            'canceled'              => __('Canceled'),
            'confirm'               => __('Confirm'),
            'extract'               => __('Extract'),
            'status'                => __('Status'),
            'cancel'                => __('Cancel'),
            'please_wait'           => __('Please wait...'),
            'select_target'         => __("Select target"),
            'permission'            => __("Permission"),
            'dns_will_disable'      => __("DNS Service status will be disabled"),
            'dns_will_enable'       => __("DNS Service status will be enabled"),

            'dns_service_enabled'   => __("DNS Service enabled"),
            'dns_service_disabled'  => __("DNS Service disabled"),

            'mail_will_disable'     => __("Mail Service status will be disabled"),
            'mail_will_enable'      => __("Mail Service status will be enabled"),

            'mail_service_enabled'  => __("Mail Service enabled"),
            'mail_service_disabled' => __("Mail Service disabled"),
            'illegal_process'       => __("Illegal process"),
            'dependencies'          => __("Dependencies"),
            'package_name'          => __("Package Name"),
            'cname'                 => $this->dispatcher->getControllerName(),
            'aname'                 => $this->dispatcher->getActionName(),
            'all'                   => __("All"),
            'stk'                   => $this->auth->getCsrfKey(),
            'stc'                   => $this->auth->getCsrfToken(),
        ];
    }

    private function errors()
    {
        $errors = $this->flashSession->getMessages("error", false);
        $this->view->setVar('errors',json_encode($errors));
    }

    private function changeLanguage()
    {
        if ($this->request->isGet() && $this->request->has("language")) {
            $language = $this->request->get("language");
            if (array_key_exists($language, $this->translate->getLangList())) {
                $this->fcookies->set("language", $this->crypto->staticDecode($language), time() + 30 * 86400);
            }

            $referrer = $this->request->getHTTPReferer();
            $parsed_url = parse_url($referrer);
            $redirect_url = rtrim($parsed_url["scheme"].":".DIR_SEPARATOR.DIR_SEPARATOR.$parsed_url["host"].":".$parsed_url["port"].DIR_SEPARATOR.$parsed_url["path"], "/");
            $this->response->redirect($redirect_url);
        }

    }

    private function logging()
    {
        $client_ip = $this->request->getClientAddress();
        $referrer = $this->request->getHTTPReferer();
        $url = $this->request->getURI();
        $method = $this->request->getMethod();
        $browser_agent = $this->request->getUserAgent();
        if((strpos($browser_agent,'bot') === false)
            && (strpos($browser_agent,'spider') === false)
            && (strpos($browser_agent,'agent') === false)) {
            $post_datas = $this->env->getRawPost();
            foreach ($post_datas as $key => $value) {
                if(strpos($key, "password") !== false) {
                    $post_datas[$key] = "*********";
                }
                if(strpos($key, "secret") !== false) {
                    $post_datas[$key] = "*********";
                }
            }

            $data = array(
                'client_address' => $client_ip,
                'referrer' => $referrer,
                'url' => $this->uri->encode($url),
                'method' => $method,
                'browser' => $browser_agent,
                'data' => $post_datas
            );

            if(strpos($url,"favicon.ico") === false) {
                $this->log->info($data);
            }
        }
    }

    private function breadCrumb()
    {
        $breadcrumbs = $this->breadcrumbs->generate();
        $controller_name = $this->dispatcher->getControllerName();
        if(!$this->request->isAjax()) {
            $html = "";
            if($controller_name != "install") {
                if($this->config->application->type == STANDALONE) {
                    if(PANEL_OS_TYPE == WINDOWS) {
                        $html .= '<li><span><i class="fp fp-windows"></i></span></li>';
                    }
                    else {
                        $html .= '<li><span><i class="fp fp-linux"></i></span></li>';
                    }
                }
                else {
                    $html .= '<li><span><i class="fa fa-cloud"></i></span></li>';
                }
            }

            if(isset($breadcrumbs)) {
                foreach ($breadcrumbs as $items ) {
                    $html .= '<i class="fa fa-caret-right"></i>';
                    if(isset($items["text"])) {
                        if(isset($items['class']) && $items['class'] != "") {
                            $html .= sprintf("<li><a class='%s' href='%s'><i></i>%s</a></li>", $items["class"], $items["link"], $items["text"]);
                        }
                        else {
                            $html .= vsprintf("<li>%s</li>",$items["text"]);
                        }
                    }
                }
            }
            $this->view->setVar("breadcrumbs", $html);
        }
        else {
            $result = [];
            if($this->config->application->type == STANDALONE) {
                if(PANEL_OS_TYPE == WINDOWS) {
                    $html = '<li><span><i class="fp fp-windows"></i></span></li>';
                }
                else {
                    $html = '<li><span><i class="fp fp-linux"></i></span></li>';
                }
            }
            else {
                $html = '<li><span><i class="fa fa-cloud"></i></span></li>';
            }
            $result[] =  [
                "html" => $html,
            ];
            foreach ($breadcrumbs as $value) {
                if(!isset($value["text"])) {
                    continue;
                }
                if(count($result) > 0) {
                    $result[] = [
                        "html" => '<i class="fa fa-caret-right"></i>'
                    ];
                }
                $result[] = [
                    "html" => !$value["active"] ? sprintf("<li><a class='%s' href='%s'><i></i>%s</a></li>", $value["class"], $value["link"], $value["text"]) : vsprintf("<li>%s</li>",$value["text"]),
                ];
            }
            $this->view->setVar("breadcrumbs", $result);
        }
    }

    /**
     * @param Dispatcher $dispatcher
     * @return false|void
     */
    public function afterExecuteRoute(Dispatcher $dispatcher)
    {
//        $this->debug->debug($this->crypto->bdecode(""), false);
        $title = strip_tags($this->tag->getTitle());
        header('Title: '. $title);
        $this->breadCrumb();
        if($this->session_status) {
//            $this->setScriptVars();
            if($this->view->getVar("domain_id") != null) {
                $this->script_vars["did"] = $this->view->getVar("domain_id");
            }
            $this->view->setVar('script_vars',json_encode($this->script_vars));
        }
        if($this->telegram == false) {
            if ($this->dispatcher->getControllerName() != "Api") {
                $this->errors();
            }
        }
        $end_time = microtime(true); // Bottom of page
        $time_load = ($end_time - $this->start_time);
        $this->view->setVar("time_load", $time_load);
        $this->response->setHeader("x-execution-time", number_format($time_load*1000, 0));
        if(file_exists(ROOTPATH.".update")) {
            $this->view->setVar('error_content',__('System is updating please try again later.'));
            $this->response->setStatusCode(503, __('Service Unavailable'));
            $this->view->pick("errors/503");
            return false;
        }
    }
}