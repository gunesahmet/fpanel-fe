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
 */namespace WebStatistics\Controller;

use FPanel\Domain\DomainSettings;
use FPanel\Models\Domains;
use FPanel\WebStatistics\WebStatistics;
use Main\Controller\ControllerBase;

/**
 * Class WebstatisticsController
 * @package WebStatistics\Controller
 */
class WebstatisticsController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('resource');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
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
                        // @TODO: Domain datalar� ve limitleri �ekilecek
                        $domains = new DomainSettings();
                        $domain_data = $domains->getDomainData($domain_id);
                        $this->domain_id = $domain_id;
                        if(($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($domain_id,DOMAIN_USER);
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->view->setVar('domain_data', $domain_data);
                        $this->view->setVar('domain_name', $domain_data['name']);
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$domain_id,' ');
                        $this->domain_data = $domain_data;
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }

    }

    /**
     * management
     */
    public function indexAction()
    {
        $this->view->pick('domains/statistics');
        $this->tag->appendTitle(__('Web Statistics'));

        $data = $this->user_data;
        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
        }
        else {
//            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        $this->breadcrumbs->add(__('Web Statistics'),'','');


        $web_statistics = new WebStatistics($domain_name);

        if(!$web_statistics->getScanStatus()) {
            $this->view->setVar("scan_status", false);
        }
        else {
            $this->view->setVar("scan_status", true);
        }

        $web_statistics->hostReport();
        $web_statistics->hostRedirectionReport();
        $web_statistics->hostFailureReport();
        $web_statistics->redirectedReferrerReport();
        $web_statistics->failedReferrerReport();
        $web_statistics->referrerSiteReport();
        $web_statistics->referrerReport();
        $web_statistics->browserReport();
        $web_statistics->statusCodeReport();
        $web_statistics->finish();
    }
}