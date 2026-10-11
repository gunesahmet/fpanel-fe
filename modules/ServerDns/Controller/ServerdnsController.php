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
 */namespace ServerDns\Controller;

use FPanel\Forms\DnsServerForm;
use FPanel\Globals\Globals;
use FPanel\Models\DnsTemplate;
use FPanel\Models\Servers;
use FPanel\Server\NamedServer;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class ServerdnsController
 * @package ServerDns\Controller
 */
class ServerdnsController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
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
        $user_data = $this->acl->run('system');
        $this->user_data = $user_data;
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];

        $this->system_data = $user_data['system_data'];

        $this->view->setVar('system_items', $user_data);

        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        if($this->system_data["system_type"] == CLOUD) {
            $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
        }
        else {
            $this->breadcrumbs->add(__('System Dashboard'),'system',' ');
        }
        $params = $this->dispatcher->getParams();

        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "server":
                    $server_id = $params['server_id'];
                    $server = Servers::findFirstById($server_id);
                    if(!$server) {
                        $this->view->setVar('error_content',__('Server not found'));
                        $this->response->setStatusCode(404, __('Server not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $server_data = $server->toArray();
                        $this->server_id = $server_id;
                        $this->view->setVar('server_id', $server_id);
                        $this->breadcrumbs->add($server_data['name'],'server/dashboard/server/'.$server_id,' ');
                    }
                    break;
                default:
                    $this->response->setStatusCode(404, 'Not Found');
                    $this->view->setVar('error_content',__('Unreconized url'));
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
        $this->tag->appendTitle(__('Default DNS Records'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->view->setVar("restore_url", 'dnsserver/restoreDns/server/'.$server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $this->view->setVar("restore_url", 'dnsserver/restoreDns');
        }

        $this->breadcrumbs->add(__('DNS Records'),'','');


        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $dns = new NamedServer();
            $this->response->setJsonContent($dns->dnslist($server_id, $system_data['system_type']))->send();
        }
    }

    /**
     * create
     */
    public function createAction()
    {
        $this->tag->appendTitle(__('Add New DNS Record'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('DNS Records'),'dnsserver/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("dnsserver/server/$server_id");
            $post_url = $this->uri->url_encrypt("dnsserver/create/server/$server_id");

        }
        else {
            $this->breadcrumbs->add(__('DNS Records'),'dnsserver',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("dnsserver");
            $post_url = $this->uri->url_encrypt("dnsserver/create");
        }
        $this->view->setVar("post_url", $post_url);

        $this->breadcrumbs->add(__('Add New DNS Record'),'','');


        $this->view->form = new DnsServerForm();

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');

            $txt_host = '[domain]';
            if($this->request->getPost('txt_record_prefix') != null)
                $txt_host = $this->request->getPost('txt_record_prefix').'.[domain]';

            $ptr_value = '[domain]';
            if($this->request->getPost('ptr_record') != null)
                $ptr_value = $this->request->getPost('ptr_record').'.[domain]';
            $post_data = array(
                // Değişkenler
                'server_id' => $server_id,
                'domain_name'   => '[domain]',
                'time_stamp'   => '0',
                // Post Dataları
                'type'   => $this->request->getPost('record_type'),

                // NS
                'ns_host'  => '[domain]',
                'ns_value' => $this->request->getPost('ns_record'),
                'ns_opt' => '',

                // A
                'a_host' => $this->request->getPost('a_record').'.'.'[domain]',
                'a_value' => $this->request->getPost('a_record_ip_address'),
                'a_opt' => '',

                // CNAME
                'cname_host' => $this->request->getPost('cname_record_domain_name').'.'.'[domain]',
                'cname_value' => $this->request->getPost('cname_record'),
                'cname_opt' => '',

                // MX
                'mx_host'  => '[domain]',
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

                'old_type' => "",
                'old_host' => "",
                'old_value' => "",
                'update' => false,
            );
            $dns = new NamedServer();
            $dns->setPosts($post_data);
            $status = $dns->createDNSRecords();
            if (!$status) {
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

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('DNS Record is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }

        }
        return true;
    }

    /**
     * @param $record_id
     * @return bool|ResponseInterface
     */
    public function editAction($record_id)
    {
        $this->tag->appendTitle(__('Edit DNS Record'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('DNS Records'),'dnsserver/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("dnsserver/server/$server_id");
            $post_url = $this->uri->url_encrypt("dnsserver/edit/$record_id/server/$server_id");

        }
        else {
            $this->breadcrumbs->add(__('DNS Records'),'dnsserver',' ');
            $redirect_url = $this->uri->url_encrypt("dnsserver");
            $post_url = $this->uri->url_encrypt("dnsserver/edit/$record_id");
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }
        $this->view->setVar("post_url", $post_url);

        $this->breadcrumbs->add(__('Edit DNS Record'),'','');



        $get_dns_records = DnsTemplate::findFirstById($record_id);
        $dns_records = $get_dns_records->toArray();
        $this->view->setVar("record_data", $dns_records);
        $this->view->setVar("record_id", $record_id);

        $options = array();
        $options['action'] = "edit";
        $options['id'] = $record_id;
        $options['dns_records'] = $dns_records;

        $this->view->form = new DnsServerForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            $txt_host = '[domain]';
            if($this->request->getPost('txt_record_prefix') != null)
                $txt_host = $this->request->getPost('txt_record_prefix').'.[domain]';

            $ptr_value = '[domain]';
            if($this->request->getPost('ptr_record') != null)
                $ptr_value = $this->request->getPost('ptr_record').'.[domain]';

            $post_data = array(
                // Değişkenler
                'server_id' => $server_id,
                'id'        => $record_id,
                'domain_name'   => '[domain]',
                'time_stamp'   => '0',

                // Post Dataları
                'type'   => $dns_records['type'],

                // NS
                'ns_host'  => '[domain]',
                'ns_value' => $this->request->getPost('ns_record'),
                'ns_opt' => '',

                // A
                'a_host' => $this->request->getPost('a_record').'.'.'[domain]',
                'a_value' => $this->request->getPost('a_record_ip_address'),
                'a_opt' => '',

                // CNAME
                'cname_host' => $this->request->getPost('cname_record_domain_name').'.'.'[domain]',
                'cname_value' => $this->request->getPost('cname_record'),
                'cname_opt' => '',

                // MX
                'mx_host'  => '[domain]',
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

                'old_type' => $dns_records['type'],
                'old_host' => $dns_records['host'],
                'old_value' => $dns_records['value'],
                'update' => true,
            );
            $dns = new NamedServer();
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

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('DNS Record is updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    /**
     * @param $record_id
     */
    public function deleteAction($record_id)
    {
        $this->view->disable();
        if(($this->request->isPost()) && ($this->request->isAjax())) {
            $messages = $this->delete($record_id);
            $this->response->setJsonContent($messages)->send();
        }
    }

    /**
     * @param $record_id
     * @return array
     */
    private function delete($record_id): array
    {
        $messages = Globals::messages();
        if(demo == true) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Sorry, this feature is disabled in demo mode');
            return $messages;
        }
        $dns = new NamedServer();
        $post_data = array(
            'id'    => $record_id
        );
        $dns->setPosts($post_data);
        $status = $dns->deleteDNSRecords();


        if ( !$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Delete failed');
        }
        else {
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Delete successful');
        }
        return $messages;
    }

    /**
     * restoreDnsAction
     */
    public function restoreDnsAction()
    {
        $this->view->disable();
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true)) {
            if($this->system_data['system_type'] == CLOUD) {
                $server_id = $this->server_id;
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }

            $restore_dns = new NamedServer();
            $restore_dns->restoreDNSRecords($server_id);
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
    public function multipleAction()
    {

        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');

            $messages = Globals::messages();
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Action failed');
            if($this->request->getPost('id') == null) {
                $messages['messages'] = __('Please select a record');

            }
            else if($this->request->getPost("action") == null) {
                $messages['messages'] = __('Please select an action');
            }
            else {

                switch($action) {
                    case "delete":
                        $messages = $this->delete($id);
                        break;
                    default:
                        $messages['messages'] = __('Action is not found');

                        break;
                }
            }

            return $this->response->setJsonContent($messages)->send();
        }
    }


}