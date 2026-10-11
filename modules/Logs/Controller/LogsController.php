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
 */namespace Logs\Controller;

use FPanel\Models\Domains;
use FPanel\Process\Log as Logmng;
use Main\Controller\ControllerBase;

/**
 * Class LogsController
 * @package Logs\Controller
 */
class LogsController extends ControllerBase
{
    protected $user_data = array();
    protected $domain_data = array();
    protected $user_type;
    protected $user_id;
    protected $client_id;
    protected $parent_id;
    protected $client_type;
    protected $domain_id;
    protected $user_domain_id;
    protected $domain_name;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $this->user_data = $this->acl->run('dashboard');
        $user_data = $this->user_data;
        if($this->request->isAjax() != true) {
            $this->response->redirect($this->uri->url_encrypt('dashboard'));
        }
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        if(isset($user_data['user']['domain_info']['domain_id']))
            $this->user_domain_id = $user_data['user']['domain_info']['domain_id'];

        $params = $this->dispatcher->getParams();
        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "domain":
                    $domain_id = $params['domain_id'];
                    $domain = Domains::findFirstById($domain_id);
                    if(!$domain) {
                        $this->view->setVar('error_content',__('Domain not found'));
                        $this->response->setStatusCode(404, __('Domain not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($domain_id,DOMAIN_USER);
                        $domain_data = $domain->toArray();
                        $this->domain_id = $domain_id;
                        $this->domain_name = $domain_data['name'];
                        $this->parent_id = $domain_data['client_id'];
                        $this->view->setVar('domain_id', $domain_id);
                        $this->view->setVar('client_type', DOMAIN_USER);
                        $client_menu = $this->sidebar->clientMenu(DOMAIN_USER, $domain_id);
                        $this->view->setVar("client_menu", $client_menu);
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        return true;
    }

    public function indexAction()
    {
        $this->view->disable();
        $data = $this->user_data;
        $log = new Logmng();
        if(isset($this->domain_id)) {
            $domain_name = $this->domain_name;
        }
        else {
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        $domains = Domains::findFirst("name = '$domain_name'");

        $params = array(
            'domain' => $domain_name,
            'prefix' => $domains->prefix,
        );
        $this->view->setVar("domain_params", $params);
        $result = $log->showDomain($params);

//        $this->debug->debug($result, false);
        if(trim($result) == "Err3801") {
            echo $domain_name . __('Log file does not exist');
        }
        else {
            $logs = array();
            foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $line){
                preg_match('~^\[(.*?)\]~', $line, $date);
                if(empty($date[1])) {
                    continue;
                }
                preg_match('~\] \[([a-z]*?)\] \[~', $line, $type);
                preg_match('~\] \[client ([0-9\.]*)\]~', $line, $client);
                preg_match('~\] (.*)$~', $line, $message);
                preg_match('~\] (.*)$~', $message[1], $message);
                preg_match('~\] (.*)$~', $message[1], $message);

                if(strpos("error", $type[1]) !== false)
                    $style = "text-danger";
                else if(strpos("warning", $type[1]) !== false)
                    $style = "text-warning";
                else
                    $style = "text-success";
                $logs[] = array(
                    'date' => $date[1],
                    'type' => '<a class="'. $style .'">'. $type[1] .'</a>',
                    'client' => $client[1],
                    'message' => $message[1],
                );

            }
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action success');
            $messages['logs'] = $logs;
            $this->response->setJsonContent($messages)->send();
        }
    }
}