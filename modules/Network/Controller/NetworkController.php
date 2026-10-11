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
namespace Network\Controller;

use FPanel\Forms\IpAddressForm;
use FPanel\Globals\Globals;
use FPanel\Models\IpAddress;
use FPanel\Models\Servers;
use FPanel\Process\Admin;
use FPanel\Server\Network;
use Main\Controller\ControllerBase;

/**
 * Class NetworkController
 * @package Network\Controller
 */
class NetworkController extends ControllerBase
{
    protected $user_data = array();
    protected $server_data = array();
    protected $system_data = array();
    protected $server_id;
    protected $user_id;
    protected $user_type;
    protected $server_os_type;

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
            $this->server_os_type = PANEL_OS_TYPE;
        }
        $params = $this->dispatcher->getParams();


        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "server":
                    $server_id = $params['server_id'];
                    if($this->system_data["system_type"] == CLOUD) {
                        $server = Servers::findFirst("type = 'main'");
                    }
                    else {
                        $server = Servers::findFirstById($server_id);
                    }
                    if(!$server) {
                        $this->view->setVar('error_content',__('Server not found'));
                        $this->response->setStatusCode(404, __('Server not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $server_data = $server->toArray();
                        $this->breadcrumbs->add($server_data['name'],'server/dashboard/server/'.$server_id,' ');
                        $this->server_id = $server_id;
                        $this->view->setVar('server_id', $server_id);
                        $this->server_data = $server_data;
                        if(strpos(strtolower($server->os),WINDOWS) !== false) {
                            $this->server_os_type = WINDOWS;
                        }
                        else {
                            $this->server_os_type = LINUX;
                        }
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('URL not found'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        else{
            $this->server_id = "1";
        }
    }

    public function indexAction()
    {
        $this->tag->appendTitle( __('Network Management'));
        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
        }

        $this->breadcrumbs->add(__('Network Management'),'','');

        if(($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $network_list = new Network();
            $this->response->setJsonContent($network_list->listNetwork($server_id, $this->system_data['system_type']))->send();
        }
    }

    public function createAction()
    {
        $this->tag->appendTitle( __('Add New Network'));

        $system_data = $this->system_data;

        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Network Management'),'network/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("network/server/".$server_id);
            $post_url = $this->uri->url_encrypt("network/create/server/".$server_id);
        }
        else {
            $this->breadcrumbs->add(__('Network Management'),'network',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("network");
            $post_url = $this->uri->url_encrypt("network/create");
        }

        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Add New Network'),'','');

        $network = new Network();
        $interfaces = $network->getInterfaceList($server_id);

        $options = array(
            'action'    => 'create',
            'page'    => 'network',
            'interfaces'    => $interfaces
        );


        $this->view->form = new IpAddressForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode');

                if($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
                }
            }

            $post_data = array(
                'interfaces' => $interfaces,
                'interface' => $this->request->getPost('interface'),
                'ip_address' => $this->request->getPost('ip_address'),
                'format' => $this->request->getPost('format',null,OFF),
                'netmask' => $this->request->getPost('netmask'),
                'type' => $this->request->getPost('used_type'),
                'gateway' => $this->request->getPost('gateway'),
                'server_id' => $server_id,
                'server_os_type' => $this->server_os_type,
            );


            $network->setPosts($post_data);
            $status = $network->createNetwork();
            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Adding failed');
                $messages['errors'] = [];
                foreach ($network->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }


            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Network created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function editAction($id)
    {
        $this->tag->appendTitle( __('Edit Network'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Network Management'),'network/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("network/server/".$server_id);
            $post_url = $this->uri->url_encrypt("network/edit/$id/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Network Management'),'network',' ');
            $redirect_url = $this->uri->url_encrypt('network');
            $post_url = $this->uri->url_encrypt("network/edit/$id");
        }
        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Edit Network'),'','');

        $this->view->setVar("ip_address_id", $id);

        $network_data = IpAddress::findFirstById($id)->toArray();

        $options = array(
            'action'    => 'edit',
            'page'    => 'network',
            'network_data'    => $network_data
        );

        $this->view->form = new IpAddressForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode.');

                if($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
                }
            }

            $post_data = array(
                'ip_address_id' => $id,
                'server_id' => $this->server_id,
                'type' => $this->request->getPost('used_type',null,SHARED),
                'format' => $this->request->getPost('format',null,CIDR),
                'netmask' => $this->request->getPost('netmask'),
                'gateway' => $this->request->getPost('gateway')
            );
            $network = new Network();

            $network->setPosts($post_data);
            $status = $network->updateNetwork();
            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach ($network->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Network updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        if(($this->request->isPost()) && ($this->request->isAjax())) {

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Deleted successful');

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode.');

                if($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
                }
            }
            if($this->system_data["system_type"] == CLOUD) {
                $server_id = $this->server_id;
                $this->view->setVar("server_id", $this->server_id);
            }
            else {
                $servers = Servers::findFirst("type = 'main'");
                $server_id = $servers->id;
            }

            $post_data = array(
                'ip_address_id' => $id,
                'server_id' => $server_id,
                'server_os_type' => $this->server_os_type
            );
            $network = new Network();
            $network->setPosts($post_data);
            $network_delete_status = $network->deleteNetwork();
            if ($network_delete_status == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('IP Address not found');
            }
            $this->response->setJsonContent($messages)->send();
        }
    }

    public function hostnameAction()
    {
        $this->tag->appendTitle( __('Edit Hostname'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $this->breadcrumbs->add(__('Network Management'),'network/server/'.$server_id,' ');
            $redirect_url = $this->uri->url_encrypt("network/server/".$server_id);
            $post_url = $this->uri->url_encrypt("network/hostname/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Network Management'),'network',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("network");
            $post_url = $this->uri->url_encrypt("network/hostname");
        }
        $this->view->setVar('post_url', $post_url);

        $this->breadcrumbs->add(__('Edit Hostname'),'','');


        $hostname = new Admin();
        $hostname->setServerId($server_id);
        $sys_info = $hostname->sysinfo();

        $host_data = $this->hostnameExplode($sys_info['network']['hostname']);

        $options = array(
            'page'  => 'hostname',
            'action'    => 'edit',
            'host_domain'    => $host_data['domain'],
            'hostname'    => $host_data['hostname']
        );
        $this->view->form = new IpAddressForm(null, $options);

        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');

            if(demo == true) {
                $messages['type'] = 'warning';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Sorry, this feature is disabled in demo mode.');
                if($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
                else {
                    $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
                }
            }

            $network = new Network();
            $post_data = array(
                'hostname' => $this->request->getPost('hostname'),
                'domain' => $this->request->getPost('host_domain'),
                'server_id' => $server_id,
            );

            $network->setPosts($post_data);
            $status = $network->updateHostname($server_id);
            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($network->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }


            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    if(PANEL_OS_TYPE == WINDOWS) {
                        $this->flashSession->warning(__('Hostname updated. But until restart hostname will not effected.'));
                    }
                    else {
                        $this->flashSession->success(__('Hostname updated'));
                    }
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function rereadAction()
    {
        $this->view->disable();
        if($this->request->isAjax() != true) {
            $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
        else {
            if($this->request->isPost() == true) {
                if($this->system_data["system_type"] == CLOUD) {
                    $server_id = $this->server_id;
                    $this->view->setVar("server_id", $this->server_id);
                }
                else {
                    $servers = Servers::findFirst("type = 'main'");
                    $server_id = $servers->id;
                }


                $network = new Network();
                $network->reReadIpAddress($server_id);

                $messages = Globals::messages();
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Init successful');

                $this->response->setJsonContent($messages)->send();
            }
            else {
                $this->response->setJsonContent(array('message' => 'logout'))->send();
            }
        }
    }

    private function hostnameExplode($hostname): array
    {
        $host_data = explode('.', $hostname);
        $tld = end($host_data);
        $domain_host = prev($host_data);
        $host_name = str_replace(".$domain_host.$tld","", $hostname);
        return array('hostname' => $host_name,'domain' => $domain_host.'.'.$tld);
    }

    public function initAction($status)
    {
        $this->view->disable();
        $network = new Network();
        if($status == "apply") {
            $network->applyChanges();
        }
        else {
            $network->cancelChanges();
        }

        if(PANEL_TYPE == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("network/server/".$server_id);
        }
        else {
            $redirect_url = $this->uri->url_encrypt("network");
        }

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Process successful');
        $messages['redirect_url'] = $redirect_url;
        //server/dashboard
        $this->response->setJsonContent($messages)->send();
    }
}