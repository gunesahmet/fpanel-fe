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
 */namespace Resources\Controller;

use FPanel\Domain\DomainSettings;
use FPanel\Forms\ResourceManagementForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\System\Resource as ResourceEdit;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class ResourcesController
 * @package Resources\Controller
 */
class ResourcesController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $js_code;

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
                        // @TODO: Domain dataları ve limitleri çekilecek
                        $domains = new DomainSettings();
                        $domain = $domains->getDomainData($domain_id);
                        $domain_data = $domain;
                        $this->domain_id = $domain_id;
                        if(($this->auth->getUserType() != ADMIN_USER) && ($this->auth->getUserType() != ROOT_USER))
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
     * index
     */
    public function indexAction()
    {
        $this->view->pick("domains/resources");
        $this->tag->appendTitle(__('Resource Viewer'));
        $this->breadcrumbs->add(__('Resource Viewer'),'','');

        $options = array(
            'action'    => 'view'
        );

        if(isset($this->domain_data)) {
            $options['domain_data'] = $this->domain_data;
            $this->view->setVar('domain_data', $this->domain_data);
            $domain_id = $this->domain_data['id'];
        }
        else {
            $domains = new DomainSettings();
            $domain_id = $this->user_data['user']['domain_info']['domain_id'];
            $domain_data = $domains->getDomainData($domain_id);
            $options['domain_data'] = $domain_data;
            $this->view->setVar('domain_data', $domain_data);
        }

        $resource_usages = DomainStatistics::findFirst("domain_id = '$domain_id'");

        $resource_usages_data = [
            'disk_limit' => 0,
            'traffic_limit' => 0,
            'subdomain_limit' => 0,
            'domain_alias_limit' => 0,
            'email_box_limit' => 0,
            'email_box_size_limit' => 0,
            'database_limit' => 0,
            'database_size_limit' => 0,
        ];

        if($resource_usages) {
            $resource_usages_data["disk_limit"] = $this->env->convert($resource_usages->disk);
            $resource_usages_data["traffic_limit"] = $this->env->convert($resource_usages->traffic);
            $resource_usages_data["subdomain_limit"] = $options['domain_data']["subdomain_count"];
            $resource_usages_data["domain_alias_limit"] = $options['domain_data']["domain_alias_count"];
            $resource_usages_data["email_box_limit"] = $options['domain_data']["mail_count"];
            $resource_usages_data["email_box_size_limit"] = $this->env->convert($resource_usages->mail);
            $resource_usages_data["database_limit"] = $options['domain_data']["database_count"];
            $resource_usages_data["database_size_limit"] = $this->env->convert($resource_usages->db);
        }

        $this->view->setVar("resource_usages", $resource_usages_data);
        $this->view->form = new ResourceManagementForm(null, $options);
    }

    /**
     * @return ResponseInterface|void
     */
    public function editAction()
    {
        $this->view->pick("domains/resourceedit");
        if($this->user_type == DOMAIN_USER) {
            return $this->response->redirect($this->uri->url_encrypt('dashboard'));
        }
        $this->tag->appendTitle(__('Update Settings'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
        }
        $overuse = $this->domain_data['overuse'];

        $this->breadcrumbs->add(__('Update Resource Settings'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('overuse', $overuse);

        $options = array(
            'action'  => 'edit',
        );

        if(isset($this->domain_data)) {
            $options['domain_data'] = $this->domain_data;
        }
        $this->view->form = new ResourceManagementForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $post_data = array(
                'created_date' => $this->domain_data['created_date'],
                'domain_id' => $domain_id,
                'client_limit'    => '0',
                'domain_limit'    => '0',
                'template_limit' => '0',
                'disk_limit'  => $this->request->getPost('disk_limit',null,'0'),
                'traffic_limit'   => $this->request->getPost('traffic_limit',null,'0'),
                'subdomain_limit' => $this->request->getPost('subdomain_limit',null,'0'),
                'domain_alias_limit'  => $this->request->getPost('domain_alias_limit',null,'0'),
                'email_box_limit' => $this->request->getPost('email_box_limit',null,'0'),
                'email_box_size_limit'    => $this->request->getPost('email_box_size_limit',null,'0'),
                'email_forward_limit' => $this->request->getPost('email_forward_limit',null,'0'),
                'email_groups_limit'  => $this->request->getPost('email_groups_limit',null,'0'),
                'email_autoresponder_limit'   => $this->request->getPost('email_autoresponder_limit',null,'0'),
                'email_lists_limit'    => $this->request->getPost('email_list_limit',null,'0'),
                'database_limit'  => $this->request->getPost('database_limit',null,'0'),
                'database_size_limit' => $this->request->getPost('database_size_limit',null,'0'),
                'expire_time_limit' => $this->request->getPost('expire_time_limit',null,UNLIMITED),
                'overuse' => $this->request->getPost('overuse',null,OFF),
                // Permission
                'man_client' => NOT_ALLOWED,
                'man_domain' => NOT_ALLOWED,
                'man_template'   => NOT_ALLOWED,
                'access_panel'  => $this->request->getPost('access_panel',null,OFF),
                'man_hosting_settings'   => $this->request->getPost('access_hosting_settings',null,OFF),
                'man_subdomain'  => $this->request->getPost('access_subdomain',null,OFF),
                'man_database'   => $this->request->getPost('access_database',null,OFF),
                'man_domain_alias'   => $this->request->getPost('access_domain_alias',null,OFF),
                'man_email'  => $this->request->getPost('access_email',null,OFF),
                'man_email_alias'    => $this->request->getPost('access_email_alias',null,OFF),
                'man_mailing_lists'  => $this->request->getPost('access_mailing_lists',null,OFF),
                'man_spam_filters'   => $this->request->getPost('access_spam_filters',null,OFF),
                'man_task'   => $this->request->getPost('access_task',null,OFF),
                'man_dns'    => $this->request->getPost('access_dns',null,OFF),
                'man_ssl'    => $this->request->getPost('access_ssl',null,OFF),
                'man_backup'  => $this->request->getPost('access_backup',null,OFF),
                'man_protect_dir'    => $this->request->getPost('access_protect_dir',null,OFF),
            );

            $resource = new ResourceEdit();
            $resource->setPosts($post_data);
            $status = $resource->updateDomain();

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($resource->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }


            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status != false) {
                    $this->flashSession->success(__('Domain resource updated'));
                    $this->response->redirect($this->uri->url_encrypt("resources/edit/domain/".$domain_id),true);
                }
            }
        }
    }
}