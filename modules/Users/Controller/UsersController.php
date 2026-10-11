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
namespace Users\Controller;

use FPanel\Components\Components;
use FPanel\Forms\UserForm;
use FPanel\Globals\Globals;
use FPanel\Models\Serviceplans;
use FPanel\Models\Users;
use FPanel\Server\Ipaddress;
use FPanel\Users\Usersdata;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class UsersController
 * @package Users\Controller
 */
class UsersController extends ControllerBase
{
    protected $user_data = array();
    protected $js;
    protected $user_type;
    protected $user_id;
    protected $client_id;
    protected $client_type;
    protected $system_type;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $this->user_data = $this->acl->run('users');

        $user_data = $this->user_data;
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->user_id = $user_data['user']['info']['user_id'];

        $this->system_type = $user_data["system_data"]["system_type"];
        $this->view->setVar("system_items", $user_data);
        $this->view->setVar("system_type", $this->system_type);
        $this->view->setVar("user_type", $this->user_type);
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');

        $params = $this->dispatcher->getParams();

        if(array_key_exists('type', $params)) {
            switch($params['type']) {
                case "client":
                    $client_id = $params['client_id'];
                    $user = Users::findFirstById($client_id);
                    if(!$user) {
                        $this->view->setVar('error_content',__('User not found'));
                        $this->response->setStatusCode(404, __('User not found'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $client_data = $user->toArray();
                        if (($this->auth->getUserType() != ROOT_USER) && ($this->auth->getUserType() != ADMIN_USER))
                            $this->acl->checkParent($client_id, $client_data['type']);

                        $this->client_id = $client_id;
                        $this->client_type = $client_data['type'];

                        $this->view->setVar('client_id', $this->client_id);
                        $this->view->setVar("client_type", $this->client_type);

                        $this->breadcrumbs->add(__('User List'),'users',' ');
                        $this->breadcrumbs->add($client_data['username'],'dashboard/client/'.$this->client_id,' ');
                        $client_menu = $this->sidebar->clientMenu($client_data['type'], $client_id);
                        $this->view->setVar("client_menu", $client_menu);

                    }
                    break;
                case "default":
                    $this->response->setStatusCode(404, 'Not Found');
                    $this->view->setVar('error_content',__('Unreconized url'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
    }

    public function indexAction()
    {
        $user_id = $this->user_data['user']['info']['user_id'];
        $user_type = $this->user_data['user']['info']['user_type'];
        $this->tag->appendTitle(__('User List'));

        $this->breadcrumbs->add(__('User List'),null,'');


        if($this->user_data['user']['remaining']['clients'] < "1")
            $active_class = "disabled";
        else
            $active_class = "";

        $this->view->setVar('active_class', $active_class);

        if ($this->request->isAjax() && $this->request->isPost()) {
            $this->view->disable();
            $users = new Usersdata();
            switch($user_type) {
                case SUPPORT_USER:
                case ROOT_USER:
                case ADMIN_USER:
                    $user_id = $this->client_id ?? "";
                break;
                case RESELLER_USER:
                case CLIENT_USER:
                    if(isset($this->client_id)) {
                        $user_id = $this->client_id;
                    }
                    break;
            }
            $this->response->setJsonContent($users->userList($user_id))->send();
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function createAction()
    {
        $data = $this->user_data;
        $user_type = $data['user']['info']['user_type'];
        $user_id = $data['user']['info']['user_id'];

        $this->tag->appendTitle(__('Create User'));
        if($user_type == ROOT_USER || $user_type == ADMIN_USER) {
            $admin_user = Users::findFirst("type = '0'");
            $template_list_admin = Serviceplans::find("client_id = '$admin_user->id'")->toArray();
            $template_list_user = Serviceplans::find("client_id = '$user_id'")->toArray();
            $template_list = array_merge($template_list_admin, $template_list_user);
        }
        else {
            $template_list = Serviceplans::find("client_id = '$user_id'")->toArray();
        }

        $options = array(
            'template'      => $template_list,
            'user_id'       => $user_id,
            'user_type'     => $user_type,
            'part'          => 'all',
            'action'        => 'create',
            "system_type"   => $this->system_type,
        );

        $ipaddress = new Ipaddress();
        $ip_address_list = $ipaddress->getIpAddress($options, $this->system_type);

        $options['ip_address_list'] = $ip_address_list;

        if(isset($this->client_id)) {
            $options['client_id'] = $this->client_id;
            $options['client_type'] = $this->client_type;
            $this->view->setVar("client_type", $this->client_type);

            $this->breadcrumbs->add(__('User List'),'users/client/'.$this->client_id,' ');
            $this->breadcrumbs->add(__('Create User'),'','');
            $redirect_url = $this->uri->url_encrypt('users/client/'.$this->client_id);
            $post_url = $this->uri->url_encrypt('users/create/client/'.$this->client_id);
        }
        else {
            $this->breadcrumbs->add(__('User List'),'users',' ');
            $this->breadcrumbs->add(__('Create User'),'','');
            $redirect_url = $this->uri->url_encrypt('users');
            $post_url = $this->uri->url_encrypt('users/create');
        }

        $this->view->setVar('post_url', $post_url);

        if ($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');
            $messages['redirect_url'] = $redirect_url;

            if (($user_type > ADMIN_USER) && ($user_type < SUPPORT_USER)) {
                $type_of_user = CLIENT_USER;
            }
            else {
                $type_of_user = $this->request->getPost('type_of_user');
            }

            if((isset($this->client_type)) && ($this->client_type > ADMIN_USER) && ($this->client_type < SUPPORT_USER))
                $type_of_user = CLIENT_USER;

            $post_data = array(
                // Permissionda Client için özel kural devreye alınacak
                'man_client' => $this->request->getPost('access_client',null,OFF),
                'man_domain' => $this->request->getPost('access_domain',null,OFF),
                'man_template'   => $this->request->getPost('access_template',null,OFF),
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
                // Limitlerde client limit için özel kural devreye alınacak
                'client_limit'    => $this->request->getPost('clients',null,'0'),
                'domain_limit'    => $this->request->getPost('domain'),
                'template_limit' => $this->request->getPost('template'),
                'disk_limit'  => $this->request->getPost('disk'),
                'traffic_limit'   => $this->request->getPost('traffic'),
                'subdomain_limit' => $this->request->getPost('subdomain'),
                'domain_alias_limit'  => $this->request->getPost('domain_alias'),
                'email_box_limit' => $this->request->getPost('email_box'),
                'email_box_size_limit'    => $this->request->getPost('email_box_size'),
                'email_forward_limit' => $this->request->getPost('email_forward',null,'0'),
                'email_groups_limit'  => $this->request->getPost('email_groups',null,'0'),
                'email_autoresponder_limit'   => $this->request->getPost('email_autoresponder',null,'0'),
                'email_lists_limit'    => $this->request->getPost('email_list',null,'0'),
                'postgresql_limit'  => $this->request->getPost('postgresql',null,'0'),
                'mssql_limit'  => $this->request->getPost('mssql',null,'0'),
                'mysql_limit'  => $this->request->getPost('mysql',null,'0'),
                'database_limit'  => $this->request->getPost('database'),
                'database_size_limit' => $this->request->getPost('database_size',null,'0'),
                'expire_time_limit' => $this->request->getPost('expire_time_limit',null,UNLIMITED),
                'overuse' => $this->request->getPost('overuse',null,OFF),
                // Domain Ayarları
                'php'  => $this->request->getPost('php_support',null,OFF),
                'php_version'  => $this->request->getPost('php_version',null,''),
                'cgi'  => $this->request->getPost('cgi_support',null,OFF),
                'perl'  => $this->request->getPost('perl_support',null,OFF),
                'python'  => $this->request->getPost('python_support',null,OFF),
                'fast_cgi'  => OFF,
                'web_statistics'  => $this->request->getPost('web_statistics_support',null,OFF),
                'web_statistics_type'  => $this->request->getPost('web_statistics_type',null),
                'asp'  => $this->request->getPost('asp_support',null,OFF),
                'aspnet'  => $this->request->getPost('asp_net_support',null,OFF),
                'aspnet_core'  => $this->request->getPost('asp_net_core_support',null,OFF),
                'aspnet_version'  => $this->request->getPost('asp_net_version',null,v40),
                'dedicated_pool'  => $this->request->getPost('dedicated_pool',null,OFF),
                'max_cpu'  => $this->request->getPost('max_cpu_usage'),
                'application_pool_mode'  => $this->request->getPost('application_pool_mode'),
                'ssl'  => OFF, // Burası false çünkü sonradan burası değiştirilebilir
                // User Info
                'username'   => $this->request->getPost('username'),
                'type'   => $type_of_user,
                'password'   => $this->request->getPost('password'),
                'confirm-password'   => $this->request->getPost('confirm-password'),
                'fullname'   => $this->request->getPost('fullname'),
                'email'   => $this->request->getPost('email_addr'),
                'company' => null,
                'telephone' => null,
                'address' => null,
                'city' => null,
                'zipcode' => null,
                'country' => null,
                // Ip adressleri
                'ipaddr_id'    => $this->request->getPost('ipaddress_list', null, []),
            );

            if(isset($this->client_id)) {
                $user_id = $this->client_id;
                $user_type = $this->client_type;
            }
            else {
                $user_id = $this->user_id;
                $user_type = $this->user_type;
            }
            $user_components = new Components();

            $user_status = $user_components->userData(array(
                    'user_id' => $user_id,
                    'user_type' => $user_type,
                ))
                ->options($options)
                ->post($post_data)
                ->create(USER);

            if(!$user_status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($user_components->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($user_status) {
                    $this->flashSession->success(__('User is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        $this->view->form = new UserForm(null, $options);
    }

    /**
     * @param $id
     */
    public function deleteAction($id)
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

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if (($id == "1") || ($id == $user_id)) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('User not found');

            }
            else {
                $messages = $this->delete($user_id, $user_type, $id);
            }

            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('users'),true);
        }
    }

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function editAction($id)
    {
        $components = new Components();
        $user = $components->getData($id);
        if(!$user) {
            $this->view->setVar('error_content',__('User not found'));
            $this->response->setStatusCode(404, __('User not found'));
            $this->view->pick("errors/404");
            return true;
        }
        else {
            $this->view->setVar('user_edit_data', $user);
            $this->view->setVar("client_name", $user['username']);
            $data = $this->user_data;
            $user_type = $data['user']['info']['user_type'];
            $user_id = $data['user']['info']['user_id'];

            $this->tag->appendTitle(__('Edit User'));

            $this->view->setVar('id', $id);


            $options = array(
                'action' => 'edit',
                'client_data' => $user,
                'user_id' => $user_id,
                'user_type' => $user_type,
                'user_ip_list' => $user['UserIpAddresses'],
            );

            $ipaddress = new Ipaddress();
            $ip_address_list = $ipaddress->getIpAddress($options, $this->system_type);

            $options['ip_address_list'] = $ip_address_list;


            if(isset($this->client_id)) {
                $options['client_id'] = $this->client_id;
                $options['client_type'] = $this->client_type;

                $this->breadcrumbs->add(__('User List'),'users/client/'.$this->client_id,' ');
                $this->breadcrumbs->add(__('Edit User'),'','');
                $redirect_url = $this->uri->url_encrypt('users/client/'.$this->client_id);
                $post_url = $this->uri->url_encrypt("users/edit/$id/client/".$this->client_id);
            }
            else {
                $this->breadcrumbs->add(__('User List'),'users',' ');
                $this->breadcrumbs->add(__('Edit User'),'','');
                $redirect_url = $this->uri->url_encrypt('users');
                $post_url = $this->uri->url_encrypt("users/edit/$id");
            }

            $this->view->setVar('post_url', $post_url);


            $this->view->form = new UserForm(null, $options);

            if($this->request->isPost()) {
                if($this->request->isAjax())
                    $this->view->disable();

                $messages = Globals::messages();
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Action successful');
//                $messages['redirect_url'] = $redirect_url;

                if (($user_type > ADMIN_USER) && ($user_type < SUPPORT_USER)) {
                    $type_of_user = CLIENT_USER;
                }
                else {
                    $type_of_user = $this->request->getPost('type_of_user');
                }

                if((isset($this->client_type)) && ($this->client_type > ADMIN_USER) && ($this->client_type < SUPPORT_USER))
                    $type_of_user = CLIENT_USER;

                $post_data = array(
                    // Permissionda Client için özel kural devreye alınacak
                    'man_client' => $this->request->getPost('access_client',null,OFF),
                    'man_domain' => $this->request->getPost('access_domain',null,OFF),
                    'man_template'   => $this->request->getPost('access_template',null,OFF),
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

                    // Limitlerde client limit için özel kural devreye alınacak
                    'client_limit'    => $this->request->getPost('clients',null,'0'),
                    'domain_limit'    => $this->request->getPost('domain'),
                    'template_limit' => $this->request->getPost('template'),
                    'disk_limit'  => $this->request->getPost('disk'),
                    'traffic_limit'   => $this->request->getPost('traffic'),
                    'subdomain_limit' => $this->request->getPost('subdomain'),
                    'domain_alias_limit'  => $this->request->getPost('domain_alias'),
                    'email_box_limit' => $this->request->getPost('email_box'),
                    'email_box_size_limit'    => $this->request->getPost('email_box_size'),
                    'email_forward_limit' => $this->request->getPost('email_forward',null,'0'),
                    'email_groups_limit'  => $this->request->getPost('email_groups',null,'0'),
                    'email_autoresponder_limit'   => $this->request->getPost('email_autoresponder',null,'0'),
                    'email_lists_limit'    => $this->request->getPost('email_list',null,'0'),
                    'database_limit'  => $this->request->getPost('database'),
                    'database_size_limit' => $this->request->getPost('database_size',null,'0'),
                    'expire_time_limit' => $this->request->getPost('expire_time_limit',null,UNLIMITED),
                    'overuse' => $this->request->getPost('overuse',null,OFF),
                    // Domain Ayarları
                    'php'  => $this->request->getPost('php_support',null,OFF),
                    'php_version'  => $this->request->getPost('php_version',null,''),
                    'cgi'  => $this->request->getPost('cgi_support',null,OFF),
                    'perl'  => $this->request->getPost('perl_support',null,OFF),
                    'python'  => $this->request->getPost('python_support',null,OFF),
                    'fast_cgi'  => OFF,
                    'web_statistics'  => $this->request->getPost('web_statistics_support',null,OFF),
                    'web_statistics_type'  => $this->request->getPost('web_statistics_type'),
                    'asp'  => $this->request->getPost('asp_support',null,OFF),
                    'aspnet'  => $this->request->getPost('asp_net_support',null,OFF),
                    'aspnet_core'  => $this->request->getPost('asp_net_core_support',null,OFF),
                    'aspnet_version'  => $this->request->getPost('asp_net_version',null,v40),
                    'dedicated_pool'  => $this->request->getPost('dedicated_pool',null,OFF),
                    'max_cpu'  => $this->request->getPost('max_cpu_usage'),
                    'application_pool_mode'  => $this->request->getPost('application_pool_mode'),
                    'ssl'  => OFF, // Burası false çünkü sonradan burası değiştirilebilir
                    // User Info
                    'username'   => $this->request->getPost('username'),
                    'type'   => $type_of_user,
                    'password'   => $this->request->getPost('password'),
                    'confirm-password'   => $this->request->getPost('confirm-password'),
                    'fullname'   => $this->request->getPost('fullname'),
                    'email'   => $this->request->getPost('email_addr'),
                    // Ip adressleri
                    'ipaddr_id'    => $this->request->getPost('ipaddress_list', null, []),
                    'old_username'    => $user['username'],

                    // Userdata
                    'company' => $this->request->getPost('company'),
                    'fax' => $this->request->getPost('fax'),
                    'telephone' => $this->request->getPost('telephone'),
                    'address' => $this->request->getPost('address'),
                    'city' => $this->request->getPost('city'),
                    'region' => $this->request->getPost('region'),
                    'zipcode' => $this->request->getPost('zipcode'),
                    'country' => $this->request->getPost('country'),

                );

                if(isset($this->client_id)) {
                    $user_id = $this->client_id;
                    $user_type = $this->client_type;
                }
                else {
                    $user_id = $this->user_id;
                    $user_type = $this->user_type;
                }
                $user_components = new Components();

                $status = $user_components->userData(array(
                    'user_id' => $user_id,
                    'user_type' => $user_type,
                ))
                    ->options($options)
                    ->post($post_data)
                    ->update(USER, $id);

                if(!$status) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                    $messages['errors'] = [];
                    foreach ($user_components->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
                else {
                    if (!$this->request->isAjax()) {
                        $this->flashSession->success(__('User updated'));
                        return $this->response->redirect($this->uri->url_encrypt('users/edit/'.$id),true);
                    }
                }

                if ($this->request->isAjax())
                    $this->response->setJsonContent($messages)->send();
            }

        }
    }

    /**
     * @param $id
     * @param $status
     * @return ResponseInterface|void
     */
    public function suspendAction($id, $status)
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

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            if (($id == "1") || ($id == $user_id)) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('User not found');

            }
            else {
                $messages = $this->suspend($user_id, $user_type, $id, $status, $this->request->getPost('message'));
            }
            $this->response->setJsonContent($messages)->send();
        }
        else {
            return $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
    }

    public function multipleAction()
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

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $action = $this->request->getPost("action");
            $id = $this->request->getPost('id');

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
                    case "suspend":
                        $messages = $this->suspend($user_id, $user_type, $id,"suspend");
                        break;
                    case "unsuspend":
                        $messages = $this->suspend($user_id, $user_type, $id,"unsuspend");
                        break;
                    case "delete":
                        $messages = $this->delete($user_id, $user_type, $id);
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

    /**
     * @param $user_id
     * @param $user_type
     * @param $id
     * @param $status
     * @param null $message
     * @return array
     */
    private function suspend($user_id, $user_type, $id, $status, $message = null): array
    {
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        $users = Users::findFirstById($id);
        if(!$users) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('User not found');
        }
        else {
            $user = new Components();
            if($status == "suspend") {
                $result = $user->userData(array(
                    'user_id' => $user_id,
                    'user_type' => $user_type,
                ))
                    ->suspend('user', $id, $message);
            }
            else {
                $result = $user->userData(array(
                    'user_id' => $user_id,
                    'user_type' => $user_type,
                ))
                    ->unsuspend('user', $id, $message);
            }


            if($result == false) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('User not found');
            }
            else {
                if($status == "suspend") {
                    $content = __('User is suspended');
                }
                else {
                    $content = __('User is unsuspended');
                }

                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = $content;
            }
        }

        return $messages;
    }

    /**
     * @param $user_id
     * @param $user_type
     * @param $id
     * @return array
     */
    private function delete($user_id, $user_type, $id): array
    {
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');

        $users = Users::findFirstById($id);
        if(!$users) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('User not found');
            return $messages;
        }


        $user = new Components();
        $status = $user->userData(array(
            'user_id' => $user_id,
            'user_type' => $user_type,
        ))
            ->delete(USER,null, $id);

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Action failed');
        }


        return $messages;
    }

    /**
     * @param $id
     */
    public function changeAction($id)
    {
        $this->view->disable();
        if (($this->request->isPost()) && ($this->request->isAjax())) {
            $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $user_id = $this->client_id ?? $this->user_id;

            if (($id == "1") || ($id == $user_id)) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('User not found');
            }
            else {
                $user_lib = new Usersdata();
                $status = $user_lib->changeUserType($id);

                if (is_array($status)) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                    $messages['errors'] = [];

                    foreach ($status as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                }
                else if($status === true) {
                    $messages['type'] = 'success';
                    $messages['title'] = __('Success');
                    $messages['messages'] = __('User type is changed');
                }
                else {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                }
            }
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
    }
}