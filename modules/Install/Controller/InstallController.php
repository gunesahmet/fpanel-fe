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
 */namespace Install\Controller;

use Exception;
use FPanel\Forms\InstallForm;
use FPanel\Globals\Globals;
use FPanel\Process\Admin;
use FPanel\Process\Networking;
use FPanel\Process\Services;
use FPanel\System\CloudInstaller;
use FPanel\System\StandaloneInstaller;
use FPanel\Translate\Translate;
use Main\Controller\ControllerBase;

/**
 * Class InstallController
 * @package Index\Controller
 */
class InstallController extends ControllerBase
{

    public function initialize()
    {
        ini_set("memory_limit","-1");
        parent::initialize();
        if(!$this->session_status)
            return;

        $this->view->setVar("support_status",false);
        $this->view->setMainView("index");
        if($this->dispatcher->getActionName() == "index") {
            $services = new Services();
            $services->serviceStart("database");
        }

        $this->view->setVar('username','Administrator');
        $sidebar = $this->sidebar->installationSideBar();
        $this->view->setVar('system_items', ['sidebar' => $sidebar]);
        $this->view->setVar('changed_data',false);
        $this->view->setVar('firstboot',true);
    }

    public function indexAction()
    {
        $system = new Admin();
        $system->conf();
        $this->tag->setTitle(__('Licence Agreement'));
        $this->session->set("panel_type", $this->config->application->type);
        $this->breadcrumbs->add(__('Licence Agreement'), '', '');

        if (($this->request->isAjax()) && ($this->request->isPost())) {
            $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Save successful');
            $this->response->setJsonContent($messages)->send();
        }
    }

    public function licenceAction()
    {
        $this->tag->setTitle(__('Licence Agreement'));
        $this->session->set("panel_type", $this->config->application->type);
        $this->breadcrumbs->add(__('Licence Agreement'), '', '');
        $this->view->pick('install/licence');


        if (($this->request->isAjax()) && ($this->request->isPost())) {
            $this->view->disable();
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Save successful');
            $this->response->setJsonContent($messages)->send();
        }

    }

    public function serverInfoAction()
    {
        $network = new Networking();
        $system = new Admin();
        $ip_list = json_decode($network->show(),true);
        $sysinfo = $system->sysinfo();

        $this->breadcrumbs->add(__('Licence Agreement'), 'install/', ' ');
        $this->breadcrumbs->add(__('Server Informations'), '', '');

        $this->tag->appendTitle(__('Server Informations'));

        $this->view->setVar('server_hostname', $sysinfo['network']['hostname']);

        $options = array(
            'server_hostname'       => $sysinfo['network']['hostname'],
            'ip_list'               => $ip_list,
            'default_ip_address'    => $sysinfo['network']['ipaddr'],
        );

        $options['page'] = "serverinfo";

        $this->view->form = new InstallForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Save successful');

            if (!$this->session->has("panel_type")) {
                $status = false;
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Session is timeout. You will redirect first step in 2 seconds');
                if(!$this->request->isAjax()) {
                    $this->flashSession->error(json_encode([__('Session is timeout. You will redirect first step in 2 seconds')]));
                    return $this->response->redirect($this->uri->encode('install'),true);
                }
            }
            else {
                $this->session->set("panel_type", strtolower($this->config->application->type));
                $ips = array();
                foreach($ip_list as $ip) {
                    $ips[] = $ip['2'];
                }
                $required = array(
                    array(
                        'field' => 'hostname',
                        'item' => $this->request->getPost("hostname"),
                        'message' => __('Hostname is required'),
                    ),
                    array(
                        'field' => 'admin_email',
                        'item' => $this->request->getPost("admin_email"),
                        'message' => __('Email Address is required'),
                    ),
                    array(
                        'field' => 'username',
                        'item' => $this->request->getPost("username"),
                        'message' => __('Username is required'),
                    ),
                    array(
                        'field' => 'password',
                        'item' => $this->request->getPost("password"),
                        'message' => __('Password is required'),
                    ),
                    array(
                        'field' => 'confirm-password',
                        'item' => $this->request->getPost("confirm-password"),
                        'message' => __('Please confirm password'),
                    ),
                );

                if($this->session->get("panel_type") == STANDALONE) {
                    $required[] = array(
                        'field' => 'ipaddress_lists',
                        'item' => $this->request->getPost("ipaddress_list"),
                        'message' => __('Please select an ip address'),
                    );
                }
                $this->validation->Required($required);
                $this->validation->Domain($this->request->getPost("hostname"),"hostname",__('Please enter valid hostname'))
                    ->Email($this->request->getPost("admin_email"),"admin_email",__('Please enter valid email address'))
                    ->Username($this->request->getPost("username"),"username",__('Please enter valid username'))
                    ->Password($this->request->getPost("password"),"password",__('Please enter valid password'))
                    ->Confirm($this->request->getPost("password"),"password", $this->request->getPost("confirm-password"), __('Please confirm password'));

                if($this->session->get("panel_type") == STANDALONE) {
                    $this->validation->InclusionInArray($this->request->getPost("ipaddress_list"),"ipaddress_lists", $ips ,__('Please select valid ip address'));
                }

                $status = $this->validation->Validate();

                if(!$status) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                    $messages['errors'] = [];
                    foreach ($this->validation->getMessage() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
                else {
                    $this->session->set("email", $this->request->getPost('admin_email'));
                    $server_init_data = [
                        'hostname'  => $this->request->getPost('hostname'),
                        'email'  => $this->request->getPost('admin_email'),
                        'default_ip_address'  => $sysinfo['network']['ipaddr'],
                        'username'  => $this->request->getPost('username'),
                        'password'  => $this->request->getPost('password'),
                        'confirm-password'  => $this->request->getPost('confirm-password'),
                        'ip_list'  => $ip_list,
                        'selected_ip_list'  => $this->request->getPost('ipaddress_list'),
                        'panel_type'  => strtolower($this->config->application->type),
                        'server_os'  => $sysinfo['system']['os'],
                    ];
                    $this->session->set("server_init_data", $server_init_data);
                }
            }
            if ($this->request->isAjax()) {
                return $this->response->setJsonContent($messages)->send();
            }
            else {
                if ($status) {
                    $this->flashSession->success(__('Request init success'));
                    return $this->response->redirect($this->uri->encode('install/generalinfo'),true);
                }
            }
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function generalInfoAction()
    {
        if(!$this->session->has("server_init_data")) {
            $this->flashSession->error(json_encode([__('You must be complete this step')]));
            return $this->response->redirect($this->uri->encode('install/serverinfo'),true);
        }

        $this->breadcrumbs->add(__('Licence Agreement'), 'install', ' ');
        $this->breadcrumbs->add(__('Server Informations'), 'install/serverinfo', ' ');
        $this->breadcrumbs->add(__('General Information'), '', '');

        $this->tag->appendTitle(__('Step 2'));

        $translate = new Translate();

        $lang_list = $translate->getLangList();

        $options = array(
            'email' => $this->session->get("email"),
            'languages' => $lang_list,
            'themes'    => array('default'  => __('Default Template'))
        );
        $options['page'] = "generalinfo";
        $this->view->form = new InstallForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Save successful');

            if (!$this->session->has("server_init_data")) {
                $status = false;
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Session is timeout. You will redirect first step in 2 seconds');
                if(!$this->request->isAjax()) {
                    $this->flashSession->error(json_encode([__('Session is timeout. You will redirect first step in 2 seconds')]));
                    return $this->response->redirect($this->uri->encode('install'),true);
                }
            }
            else {
                if($this->session->get("panel_type") == STANDALONE) {
                    $server_init_data = $this->session->get('server_init_data');
                    $installer = new StandaloneInstaller();

                    $post_data = array(
                        'fullname'  => $this->request->getPost('fullname'),
                        'email'     => $this->request->getPost('email'),
                        'company'   => $this->request->getPost('company'),
                        'fax'       => $this->request->getPost('fax'),
                        'telephone' => $this->request->getPost('telephone'),
                        'address'   => $this->request->getPost('address'),
                        'city'      => $this->request->getPost('city'),
                        'zipcode'   => $this->request->getPost('zipcode'),
                        'country'   => $this->request->getPost('country'),
                        'region'    => $this->request->getPost('region'),
                        'interface' => $this->request->getPost('themes'),
                        'language'  => 'en_US',
                    );

                    $installer->setPosts($post_data);
                    $status = $installer->hostname($server_init_data['hostname'])
                        ->contactMail($server_init_data['email'])
                        ->defaultIpAddr($server_init_data['default_ip_address'])
                        ->adminUsername($server_init_data['username'])
                        ->adminPassword($server_init_data['password'])
                        ->ipAddresses($server_init_data['ip_list'], $server_init_data['selected_ip_list'])
                        ->panelType(STANDALONE)
                        ->serverOs($server_init_data['server_os'])
                        ->save();
                }
                else {
                    $server_init_data = $this->session->get('server_init_data');
                    $installer = new CloudInstaller();

                    $post_data = array(
                        'fullname'  => $this->request->getPost('fullname'),
                        'email'     => $this->request->getPost('email'),
                        'company'   => $this->request->getPost('company'),
                        'fax'       => $this->request->getPost('fax'),
                        'telephone' => $this->request->getPost('telephone'),
                        'address'   => $this->request->getPost('address'),
                        'city'      => $this->request->getPost('city'),
                        'zipcode'   => $this->request->getPost('zipcode'),
                        'country'   => $this->request->getPost('country'),
                        'region'    => $this->request->getPost('region'),
                        'interface' => $this->request->getPost('themes'),
                        'language'  => 'en_US',
                    );

                    $installer->setPosts($post_data);
                    $status = $installer->hostname($server_init_data['hostname'])
                        ->contactMail($server_init_data['email'])
                        ->defaultIpAddr($server_init_data['default_ip_address'])
                        ->adminUsername($server_init_data['username'])
                        ->adminPassword($server_init_data['password'])
                        ->ipAddresses($server_init_data['ip_list'])
                        ->panelType(CLOUD)
                        ->serverOs($server_init_data['server_os'])
                        ->save();
                }


                if($status) {
                    $this->session->set("status", array(
                        'finish'  => 'success'
                    ));
                }
                else {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                    $messages['errors'] = [];
                    foreach ($installer->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
            }

            if ($this->request->isAjax())
                return $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Initializing success'));
                    return $this->response->redirect($this->uri->encode('install/finish'),true);
                }
            }
        }
        return true;
    }

    public function finishAction()
    {
        if(!$this->session->has("status")) {
            $this->flashSession->error(json_encode([__('You must be complete previous steps')]));
            return $this->response->redirect($this->uri->encode('install/serverinfo'),true);
        }
        else {
            $status = $this->session->get('status');
            if($status['finish'] != "success") {
                $this->flashSession->error(json_encode([__('You must be complete previous steps')]));
                return $this->response->redirect($this->uri->encode('install/serverinfo'),true);
            }
        }
        $this->tag->appendTitle(__('Finish'));
        $this->breadcrumbs->add(__('Licence Agreement'), 'install', ' ');
        $this->breadcrumbs->add(__('Server Informations'), 'install/serverinfo', ' ');
        $this->breadcrumbs->add(__('General Information'), 'install/generalinfo', ' ');
        $this->breadcrumbs->add(__('Finish'), '', '');


        // @TODO: Varsayılan ip dışarı çıkabiliyor mu kontrol edilecek. Çıkamıyorsa lisans işlemleri için sorun olacağından finish işlemi tamamlanmayacak veya kullanıcıya uyarı verilecek
        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            if(file_exists(ROOTPATH."/".".firstboot")) {
                unlink(ROOTPATH."/".".firstboot");
            }

            $this->session->destroy();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Server init success');

            if ($this->request->isAjax())
                return $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Server init success please login'));
                    return $this->response->redirect('/',true);
                }
            }
        }
        return true;
    }

}