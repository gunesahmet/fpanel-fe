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
 */
namespace DomainDns\Controller;

use FPanel\Domain\NamedService;
use FPanel\Domain\Services as DomainServices;
use FPanel\Forms\DnsForm;
use FPanel\Globals\Globals;
use FPanel\Models\DnsRecords;
use FPanel\Models\DnsZone;
use FPanel\Models\Domains;
use FPanel\Process\Services;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class DnsController
 * @package DomainDns\Controller
 */
class DomaindnsController extends ControllerBase
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
        $user_data = $this->acl->run('dns');
        $this->user_data = $user_data;
        $this->view->setVar('system_items', $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        $params = $this->dispatcher->getParams();
        $restore_url = $this->uri->url_encrypt("dns/restore");

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
                        $restore_url = $this->uri->url_encrypt("dns/restore/domain/".$domain_id);
                        $this->view->setVar('domain_id', $this->domain_id);
                        $this->breadcrumbs->add(__('Domain List'),'domains',' ');
                        $this->breadcrumbs->add($domain_data['name'],'dashboard/domain/'.$domain_id,' ');
                        $this->domain_data = $domain_data;
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
        else {
            $domain_id = $user_data['user']['domain_info']['domain_id'];
        }

        if(isset($this->domain_id)) {
            $redirect_url = $this->uri->url_encrypt("dashboard/domain/".$domain_id);
        }
        else {
            $redirect_url = $this->uri->url_encrypt("dashboard");
        }

        $this->view->setVar("restore_url", $restore_url);
        $domain_services = new DomainServices();
        $domain_services->checkDomainService($domain_id,"dns", $redirect_url);
    }

    public function indexAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('DNS Records'));

        $this->breadcrumbs->add(__('DNS Records'),'','');

        if($this->request->isPost() && $this->request->isAjax()) {
            $this->view->disable();
            $dns = new NamedService();
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $this->response->setJsonContent($dns->dnsRecordList($domain_id,true))->send();
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $this->response->setJsonContent($dns->dnsRecordList($domain_id))->send();
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function createAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Create DNS Record'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('DNS Record List'),'dns/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("dns/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("dns/create/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('DNS Record List'),'dns',' ');
            $redirect_url = $this->uri->url_encrypt("dns");
            $post_url = $this->uri->url_encrypt("dns/create");
        }

        $this->breadcrumbs->add(__('Create DNS Record'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('post_url', $post_url);

        $this->view->form = new DnsForm(null);

        if ($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $txt_host = $domain_name;
            if($this->request->getPost('txt_record_prefix') != null)
                $txt_host = $this->request->getPost('txt_record_prefix').'.'.$domain_name;

            $ptr_value = $domain_name;
            if($this->request->getPost('ptr_record') != null)
                $ptr_value = $this->request->getPost('ptr_record').'.'.$domain_name;


            $post_data = array(
                'action' => "create",
                'update' => false,
                // Değişkenler
                'domain_id'   => $domain_id,
                'domain_name'   => $domain_name,
                'time_stamp'   => time(),
                // Post Dataları
                'type'   => $this->request->getPost('record_type'),

                // NS
                'ns_host'  => $domain_name,
                'ns_value' => $this->request->getPost('ns_record'),
                'ns_opt' => '',

                // A
                'a_host' => $this->request->getPost('a_record'),
                'a_value' => $this->request->getPost('a_record_ip_address'),
                'a_opt' => '',

                // CNAME
                'cname_host' => $this->request->getPost('cname_record_domain_name'),
                'cname_value' => $this->request->getPost('cname_record'),
                'cname_opt' => '',

                // MX
                'mx_host'  => $this->request->getPost('mx_domain').'.'.$domain_name,
                'mx_value' => $this->request->getPost('mx_record'),
                'mx_opt' => $this->request->getPost('mx_record_priority'),

                // PTR
                'ptr_host' => $this->request->getPost('ptr_record_ip_address'),
                'ptr_value' => $ptr_value,
                'ptr_opt' => $this->request->getPost('ptr_record_ip_block'),

                // TXT
                'txt_host' => $txt_host,
                'txt_value' => $this->request->getPost('txt_record'),
                'txt_opt' => '',

                // SRV
                'srv_service_name' => $this->request->getPost('srv_service_name'),
                'srv_protocol' => $this->request->getPost('srv_protocol'),
                'srv_domain_name' => $this->request->getPost('srv_domain_name'),
                'srv_priority' => $this->request->getPost('srv_priority'),
                'srv_priority_weight' => $this->request->getPost('srv_priority_weight'),
                'srv_target_host' => $this->request->getPost('srv_target_host'),
                'srv_target_port' => $this->request->getPost('srv_target_port'),

                'old_type' => '',
                'old_host' => '',
                'old_value' => '',
            );
            $dns = new NamedService();
            $dns->domain_name = $domain_name;
            $dns->setPosts($post_data);
            $dns_record_status = $dns->createDNSRecords();
            $dns->initCurl($domain_id);
            if ($dns_record_status === false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];
                foreach ($dns->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($dns_record_status === true) {
                    $this->flashSession->success(__('DNS Record is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }

        }
    }

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function editAction($id)
    {
        $data = $this->user_data;
        $this->view->setVar("system_items", $data);
        $this->tag->appendTitle(__('Edit DNS Record'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('DNS Record List'),'dns/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("dns/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("dns/edit/$id/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('DNS Record List'),'dns',' ');
            $redirect_url = $this->uri->url_encrypt("dns");
            $post_url = $this->uri->url_encrypt("dns/edit/$id");
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Edit DNS Record'),'','');

        $this->view->setVar('domain_name', $domain_name);

        $dns_zone = DnsZone::findFirst("domain_id = '$domain_id'");
        if(!$dns_zone) {
            $this->view->setVar('error_content',__('DNS Record Zone not found'));
            $this->response->setStatusCode(404, __('DNS Record Zone not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $get_dns_records = DnsRecords::findFirst("id = '$id' AND dns_zone_id = '$dns_zone->id'");
        if(!$get_dns_records) {
            $this->view->setVar('error_content',__('DNS Record not found'));
            $this->response->setStatusCode(404, __('DNS Record not found'));
            $this->view->pick("errors/404");
            return true;
        }
        $dns_records = $get_dns_records->toArray();

        $this->view->setVar("record_data", $dns_records);

        $options = array();
        $options['action'] = "edit";
        $options['id'] = $id;
        $options['dns_records'] = $dns_records;
        $options['domain_name'] = $domain_name;

        $this->view->setVar("id", $id);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $messages['redirect_url'] = $redirect_url;

            $txt_host = $domain_name;
            if($this->request->getPost('txt_record_prefix') != null)
                $txt_host = $this->request->getPost('txt_record_prefix').'.'.$domain_name;

            $ptr_value = $domain_name;
            if($this->request->getPost('ptr_record') != null)
                $ptr_value = $this->request->getPost('ptr_record').'.'.$domain_name;


            $post_data = array(
                'update' => true,
                // Değişkenler
                'domain_id'   => $domain_id,
                'dns_zone_id'   => $dns_zone->id,
                'domain_name'   => $domain_name,
                'id'        => $id,
                // Post Dataları
                'type'   => $dns_records['type'],

                // NS
                'ns_host'  => $domain_name,
                'ns_value' => $this->request->getPost('ns_record'),
                'ns_opt' => '',

                // A
                'a_host' => $this->request->getPost('a_record'),
                'a_value' => $this->request->getPost('a_record_ip_address'),
                'a_opt' => '',

                // CNAME
                'cname_host' => $this->request->getPost('cname_record_domain_name'),
                'cname_value' => $this->request->getPost('cname_record'),
                'cname_opt' => '',

                // MX
                'mx_host'  => $this->request->getPost('mx_domain').'.'.$domain_name,
                'mx_value' => $this->request->getPost('mx_record'),
                'mx_opt' => $this->request->getPost('mx_record_priority'),

                // PTR
                'ptr_host' => $this->request->getPost('ptr_record_ip_address'),
                'ptr_value' => $ptr_value,
                'ptr_opt' => $this->request->getPost('ptr_record_ip_block'),

                // TXT
                'txt_host' => $txt_host,
                'txt_value' => $this->request->getPost('txt_record'),
                'txt_opt' => '',

                // SRV
                'srv_service_name' => $this->request->getPost('srv_service_name'),
                'srv_protocol' => $this->request->getPost('srv_protocol'),
                'srv_domain_name' => $this->request->getPost('srv_domain_name'),
                'srv_priority' => $this->request->getPost('srv_priority'),
                'srv_priority_weight' => $this->request->getPost('srv_priority_weight'),
                'srv_target_host' => $this->request->getPost('srv_target_host'),
                'srv_target_port' => $this->request->getPost('srv_target_port'),

                'old_type' => $dns_records['type'],
                'old_host' => $dns_records['host'],
                'old_value' => $dns_records['value'],
            );
            $dns = new NamedService();
            $dns->domain_name = $domain_name;
            $dns->setPosts($post_data);
            $status = $dns->updateDNSRecords();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($dns->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status === true) {
                    $this->flashSession->success(__('DNS Record is updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        $this->view->form = new DnsForm(null, $options);
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        if ($this->request->isAjax() && $this->request->isPost()) {
            $data = $this->user_data;
            $dns = new NamedService();
            $post_data = array(
                'id'    => $id
            );
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $domain_name = $this->domain_data['name'];
//                $url = $this->uri->url_encrypt('dns/domain/'.$domain_id);
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $domain_name = $data['user']['domain_info']['domain_name'];
//                $url = $this->uri->url_encrypt('dns');
            }

            $dns->setPosts($post_data);
            $dns->domain_name = $domain_name;
            $status = $dns->deleteDNSRecords(null, $domain_id);
            $servicemng = new Services();
            $servicemng->serviceReload("dns");

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Delete successful');

            if ($status === false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Delete failed');

            }
            $this->response->setJsonContent($messages)->send();
        }
    }

    public function restoreAction()
    {

        $this->view->disable();
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
//            $domain_name = $this->domain_data['name'];
//            $url = $this->uri->url_encrypt('dns/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
//            $domain_name = $data['user']['domain_info']['domain_name'];
//            $url = $this->uri->url_encrypt('dns');
        }

        $this->view->disable();
        if ($this->request->isAjax() && $this->request->isPost()) {
            $restore_dns = new NamedService();
            $restore_dns->restoreDNSRecords($domain_id);

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Restore successful');


            $this->response->setJsonContent($messages)->send();
        }

    }

    /**
     * @return ResponseInterface|void
     */
    public function removeMultipleAction()
    {
        $this->view->disable();
        if($this->request->isPost()) {
            $data = $this->user_data;
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $domain_name = $this->domain_data['name'];
//                $url = $this->uri->url_encrypt('dns/domain/'.$domain_id);
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $domain_name = $data['user']['domain_info']['domain_name'];
//                $url = $this->uri->url_encrypt('dns');
            }
            $checked_ids = $this->request->getPost('id');
            if(!empty($checked_ids)) {
                foreach($checked_ids as $id) {
                    $post_data = array(
                        'id'    => $id
                    );

                    $dns = new NamedService();
                    $dns->setPosts($post_data);
                    $dns->domain_name = $domain_name;
                    $dns->deleteDNSRecords(null, $domain_id);
                }

                $servicemng = new Services();
                $servicemng->serviceReload("dns");

                $messages = Globals::messages();
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Action successful');


                $this->response->setJsonContent($messages)->send();

            }
        }
        else {
            $this->session->destroy();
            return $this->response->redirect("/",true);
        }
    }
}