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
 */namespace Subdomain\Controller;

use FPanel\Domain\Services;
use FPanel\Domain\Subdomain;
use FPanel\Environments\QueryBuilder;
use FPanel\Forms\SubdomainForm;
use FPanel\Globals\Globals;
use FPanel\Models\Domains;
use Main\Controller\ControllerBase;

class SubdomainController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $id;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('subdomain');

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
                    $domain = Domains::findFirst("id = '$domain_id'");
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

        $domain_services = new Services();
        $domain_services->checkDomainService($domain_id,"mail", $redirect_url);

        return true;
    }

    public function indexAction()
    {
        $this->tag->appendTitle(__('Subdomain List'));

        $data = $this->user_data;
        $this->breadcrumbs->add(__('Subdomain List'),'','');


        if($this->user_data['user']['remaining']['subdomains'] < "1")
            $active_class = "disabled";
        else
            $active_class = "";

        $this->view->setVar('active_class', $active_class);

        if($this->request->isAjax() && $this->request->isPost()) {
            $this->view->disable();
            $subdomains = new Subdomain();
            if(isset($this->domain_id)) {
                $domain_id = $this->domain_id;
                $this->response->setJsonContent($subdomains->subdomainList($domain_id,true))->send();
            }
            else {
                $domain_id = $data['user']['domain_info']['domain_id'];
                $this->response->setJsonContent($subdomains->subdomainList($domain_id))->send();
            }
        }
    }

    public function createAction()
    {
        $this->tag->appendTitle(__('Create Subdomain'));
        $data = $this->user_data;

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Subdomain List'),'subdomain/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("subdomain/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("subdomain/create/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Subdomain List'),'subdomain',' ');
            $redirect_url = $this->uri->url_encrypt("subdomain");
            $post_url = $this->uri->url_encrypt("subdomain/create");
        }

        $domain_web_server = Domains::findFirstById($domain_id)->getWebServers();

        $this->view->setVar("server_os", $domain_web_server->os);
        $this->view->setVar('post_url', $post_url);
        $this->view->setVar('domain_name', $domain_name);

        $this->breadcrumbs->add(__('Create Subdomain'),'','');

        $domain_data = $this->getdomain($domain_id);

        if(empty($domain_data)) {
            $this->view->setVar('error_content',__('Domain not found'));
            $this->response->setStatusCode(404, __('Domain not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $options = array(
            'action'    => 'create',
            'domain_id'    => $domain_id,
            'domain_data'    => $domain_data,
            'subdomain_data'    => [],
        );

        $this->view->form = new SubdomainForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Adding successful');
            $messages['redirect_url'] = $redirect_url;

            $post_data = array(
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'domain_ftpuser' => $domain_data['ftpname'], // Domaine ait ftp user
                'ipaddress' => $domain_data['ip_address'], // Domaine ait ipadresi
                'domain_ftp_user_id' => $domain_data['ftp_user_id'], // Domaine ait ftp user idsi
                'domain_parent' => $domain_data['client_id'], // Domainin sahibinin idsi
                'ipaddr_id' => $domain_data['ipaddr_id'], // Domainin ip adresinin idsi
                'subdomain_name' => $this->request->getPost('subdomain_name'),
                'full_name' => $this->request->getPost('subdomain_name').'.'.$domain_name,
                'status' => $this->request->getPost('status'),

                // Özel kural devreye girecek
                'use_native_ftp' => $this->request->getPost('use_native_ftp',null,OFF),
                'ftpname'   => $this->request->getPost('username'),
                'password'   => $this->request->getPost('password'),

                // Subdomain host parametreleri
                'php' => $this->request->getPost('php_support',null,OFF),
                'php_version' => $this->request->getPost('php_version'),
                'asp' => $this->request->getPost('asp_support',null,OFF),
                'aspnet_core' => $this->request->getPost('asp_net_core_support',null,OFF),
                'aspnet' => $this->request->getPost('asp_net_support',null,OFF),
                'aspnet_version' => $this->request->getPost('asp_net_version'),
                'cgi' => $this->request->getPost('cgi_support',null,OFF),
                'perl' => $this->request->getPost('perl_support',null,OFF),
                'python' => $this->request->getPost('python_support',null,OFF),
                'ssl' => OFF,
            );

            $subdomain = new Subdomain();
            $subdomain->setPosts($post_data);
            $result = $subdomain->createSubdomain();

            if (!$result) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Create subdomain failed');
                $messages['errors'] = [];

                foreach ($subdomain->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));

                if($this->request->isAjax()) {
                    return $this->response->setJsonContent($messages)->send();
                }
            }
            else {

                if($this->request->isAjax()) {
                    return $this->response->setJsonContent($messages)->send();
                }
                else {
                    $this->flashSession->success(__('Subdomain created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        return true;
    }

    public function editAction($id)
    {
        $data = $this->user_data;
        $this->view->setVar("id", $id);
        $this->tag->appendTitle(__('Edit Subdomain'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('Subdomain List'),'subdomain/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("subdomain/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("subdomain/edit/$id/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('Subdomain List'),'subdomain',' ');
            $redirect_url = $this->uri->url_encrypt("subdomain");
            $post_url = $this->uri->url_encrypt("subdomain/edit/$id");
        }
        $this->view->setVar('post_url', $post_url);
        $this->breadcrumbs->add(__('Edit Subdomain'),'','');

        $subdomain_data = $this->getsubdomain($id, $domain_id);
        $domain_web_server = Domains::findFirstById($domain_id)->getWebServers();
        $this->view->setVar("server_os", $domain_web_server->os);
        if(empty($subdomain_data)) {
            $this->view->setVar('error_content',__('Subdomain not found'));
            $this->response->setStatusCode(404, __('Subdomain not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $domain_data = $this->getdomain($domain_id);

        if(empty($domain_data)) {
            $this->view->setVar('error_content',__('Domain not found'));
            $this->response->setStatusCode(404, __('Domain not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $options = array(
            'action'    => 'edit',
            'domain_id'    => $domain_id,
            'domain_data'    => $domain_data,
            'subdomain_data'    => $subdomain_data,
        );
//        $this->debug->debug($subdomain_data, false);

        $this->view->setVar('domain_name', $domain_name);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Update successful');
            $messages['redirect_url'] = $redirect_url;

            $subdomain_name = $subdomain_data['subdomain_name'];
            $post_data = array(
                'domain_id' => $domain_id,
                'domain_name' => $domain_name,
                'old_ftpname' => $subdomain_data['ftpname'],
                'ipaddress' => $subdomain_data['ip_address'],
                'subdomain_ftp_user_id' => $subdomain_data['ftpuser_id'],
                'domain_ftp_user_id' => $subdomain_data['ftp_user_id'],
                'domain_parent' => $subdomain_data['domain_parent'],
                'ipaddr_id' => $subdomain_data['ipaddr_id'],
                'domain_ftpuser' => $subdomain_data['domain_ftpuser'], // Domaine ait ftp user
                'subdomain_name' => $subdomain_name,
                'full_name' => $subdomain_name.'.'.$domain_name,
                'status' => $this->request->getPost('status'),

                'use_native_ftp' => $this->request->getPost('use_native_ftp',null,OFF),
                'ftpname'   => $this->request->getPost('username'),
                'password'   => $this->request->getPost('password'),

                // Subdomain host parametreleri
                'php'           => $this->request->getPost('php_support',null,OFF),
                'php_version'   => $this->request->getPost('php_version'),
                'asp'           => $this->request->getPost('asp_support',null,OFF),
                'aspnet_core'   => $this->request->getPost('asp_net_core_support',null,OFF),
                'aspnet'        => $this->request->getPost('asp_net_support',null,OFF),
                'aspnet_version' => $this->request->getPost('asp_net_version',null,OFF),
                'cgi'           => $this->request->getPost('cgi_support',null,OFF),
                'perl'          => $this->request->getPost('perl_support',null,OFF),
                'python'        => $this->request->getPost('python_support',null,OFF),
                'dedicated_pool' => $this->request->getPost('dedicated_pool',null,OFF),
                'max_cpu'        => $this->request->getPost('max_cpu_usage',null,OFF),
                'ssl'           => OFF,
                'subdomain_data' => $subdomain_data,
            );

            $subdomain = new Subdomain();
            $subdomain->setPosts($post_data);
            $update_status = $subdomain->updateSubdomain();

            if(!$update_status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];

                foreach($subdomain->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
                if($this->request->isAjax()) {
                    $this->response->setJsonContent($messages)->send();
                }
            }
            else {
                if($this->request->isAjax()) {
                    $this->response->setJsonContent($messages)->send();
                }
                else {
                    $this->flashSession->success(__('Subdomain updated'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        $this->view->form = new SubdomainForm(null, $options);
        return true;
    }

    public function deleteAction($id)
    {
        $this->view->disable();
        if ($this->request->isAjax() && $this->request->isPost()) {
            $messages = $this->delete($id);
            $this->response->setJsonContent($messages)->send();
        }
    }

    private function delete($id): array
    {
        $data = $this->user_data;
        $domain_id = $this->domain_id ?? $data['user']['domain_info']['domain_id'];

        $subdomain = new Subdomain();
        $subdomain->setPosts(array(
            'id' => $id,
            'domain_id' => $domain_id,
        ));
        $subdomain_status = $subdomain->deleteSubdomain($domain_id);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Delete successful');

        if (!$subdomain_status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Delete failed');
        }
        return $messages;
    }

    public function multipleAction()
    {
        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if($this->request->getPost('id') == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select at least one');
            }
            else if($this->request->getPost("action") == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Select an action');
            }
            else {
                switch($action) {
                    case "delete":
                        $messages = $this->delete($id);
                        break;
                    default:
                        $messages['type'] = 'error';
                        $messages['title'] = __('Failed');
                        $messages['messages'] = __('Action not found');
                        break;
                }
            }
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
    }

    private function getdomain($domain_id)
    {
        $qbuilder = new QueryBuilder();
        return $qbuilder->select([
            "domains.name",
            "domains.ftp_user_id",
            "domains.client_id",
            "domains.status as domain_status",
            "dns_zone.id as dns_zone_id",
            "ip_address.id as ipaddr_id",
            "ip_address.ip_address",
            "ftp_users.username as ftpname",
            "client_limits_permissions.php as domain_php",
            "client_limits_permissions.asp  as domain_asp",
            "client_limits_permissions.cgi  as domain_cgi",
            "client_limits_permissions.perl as domain_perl",
            "client_limits_permissions.python as domain_python",
            "client_limits_permissions.aspnet_core as domain_aspnet_core",
            "client_limits_permissions.aspnet as domain_aspnet",
            "client_limits_permissions.aspnet_version as domain_aspnet_version",
            "client_limits_permissions.php_version as domain_php_version",
        ])
            ->from("domains")
            ->innerJoin("ip_address","domains.ipaddr_id = ip_address.id")
            ->innerJoin("client_limits_permissions","domains.id = client_limits_permissions.domain_id")
            ->innerJoin("dns_zone","domains.id = dns_zone.domain_id")
            ->innerJoin("ftp_users","domains.ftp_user_id = ftp_users.id")
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();
    }

    private function getsubdomain($sub_id, $domain_id)
    {
        $qbuilder = new QueryBuilder();
        //        $key_values = [
//            "domain_aspnet_core" => "aspnet_core",
//            "domain_aspnet" => "aspnet",
//            "domain_asp" => "asp",
//            "domain_fast_cgi" => "fast_cgi",
//            "domain_python" => "python",
//            "domain_perl" => "perl",
//            "domain_cgi" => "cgi",
//            "domain_php" => "php",
//        ];
//
//        foreach ($key_values as $key => $value) {
//            if($result[$key] == OFF) {
//                $result[$value] = OFF;
//            }
//        }

        return $qbuilder->select(["subdomains.id",
            "subdomains.php",
            "subdomains.php_version",
            "subdomains.cgi",
            "subdomains.perl",
            "subdomains.python",
            "subdomains.fast_cgi",
            "subdomains.asp",
            "subdomains.status",
            "subdomains.aspnet_core",
            "subdomains.aspnet",
            "subdomains.aspnet_version",
            "subdomains.dedicated_pool",
            "subdomains.max_cpu",
            "subdomains.ssl",
            "subdomains.ftpuser_id",
            "subdomains.ftpuser_type",
            "subdomains.subdomain_name",
            "client_limits_permissions.aspnet_core as domain_aspnet_core",
            "client_limits_permissions.aspnet as domain_aspnet",
            "client_limits_permissions.asp as domain_asp",
            "client_limits_permissions.fast_cgi as domain_fast_cgi",
            "client_limits_permissions.python as domain_python",
            "client_limits_permissions.perl as domain_perl",
            "client_limits_permissions.cgi as domain_cgi",
            "client_limits_permissions.php as domain_php",
            "dns_zone.id as dns_zone_id",
            "ip_address.ip_address",
            "domains.ipaddr_id",
            "domains.client_id as domain_parent",
            "domains.`name`",
            "domains.`status` as domain_status",
            "domains.ftp_user_id as ftp_user_id",
            "domain_ftpusers.username as domain_ftpuser",
            "ftp_users.username as ftpname",
            "ftp_users.`password`"
        ])
            ->from("subdomains")
            ->innerJoin("domains","subdomains.domain_id = domains.id")
            ->innerJoin("client_limits_permissions","subdomains.domain_id = client_limits_permissions.domain_id")
            ->innerJoin("dns_zone","subdomains.domain_id = dns_zone.domain_id")
            ->innerJoin("ftp_users as domain_ftpusers","domains.ftp_user_id = domain_ftpusers.id")
            ->innerJoin("ip_address","subdomains.ipaddr_id = ip_address.id")
            ->innerJoin("ftp_users","subdomains.ftpuser_id = ftp_users.id")
            ->where([
                'conditions' => "subdomains.id = ?1",
                'bind' => [1 => $sub_id]
            ])
            ->andWhere([
                'conditions' => "subdomains.domain_id = ?1",
                'bind' => [1 => $domain_id]
            ])
            ->findFirst();
    }
}