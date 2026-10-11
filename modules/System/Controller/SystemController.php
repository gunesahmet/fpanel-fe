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
 */namespace System\Controller;

use FPanel\Cloud\Servers as CloudServers;
use FPanel\Environments\Parser;
use FPanel\Forms\AdvancedSettingForm;
use FPanel\Forms\NotyfyForm;
use FPanel\Globals\Globals;
use FPanel\Menu\System;
use FPanel\Models\Servers;
use FPanel\Models\SystemSettings;
use FPanel\System\AdvancedSettings;
use FPanel\System\Notyfy;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class SystemController
 * @package System\Controller
 */
class SystemController extends ControllerBase
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

        $this->view->setVar('system_items', $user_data);

        $this->system_data = $user_data['system_data'];

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
                        $this->tag->appendTitle(__('Error 404 Server Not Found') );
                        $this->response->setStatusCode(404, 'Not Found');
                        $this->view->setVar('error_content',__('Unreconized url'));
                        $this->view->pick("errors/404");
                        return true;
                    }
                    else {
                        $server_data = $server->toArray();
                        $this->server_id = $server_id;
                        $this->view->setVar('server_id', $server_id);
                        $this->breadcrumbs->add(__('Server List'),'system',' ');
                        $this->server_data = $server_data;
                    }
                    break;
                default:
                    $this->response->setStatusCode(404, 'Not Found');
                    $this->view->setVar('error_content',__('Unreconized url'));
                    $this->view->pick("errors/404");
                    return true;
            }
        }
        else {
            $server = Servers::findFirst("type = 'main'");
            $this->server_id = $server->id;
            $this->server_data = $server->toArray();
        }
    }

    /**
     * index
     */
    public function indexAction()
    {
        $menu = new System();
        $system_type = $this->system_data['system_type'];
        $this->view->setVar('system_type', $system_type);
        if($system_type == CLOUD) {
            $this->view->setVar("server_id", $this->server_id);
            $server = Servers::findFirstById($this->server_id)->toArray();
            $this->view->setVar('server_data', $server);

            $this->tag->appendTitle(__('Cloud Dashboard'));

            if ($this->request->isAjax()) {
                if ($this->request->isPost()) {
                    $this->view->disable();
                    $cloud = new CloudServers();
                    $this->response->setJsonContent($cloud->serverList())->send();
                }
            }
        }
        else if($system_type == STANDALONE) {
            $this->view->setVar('', $system_type);
        }
        $this->view->setVar('dashboard_buttons', $menu->getButtons($system_type));
    }

    public function notifyAction()
    {
        $this->tag->appendTitle(__('Notifications & Alerts'));

        $this->breadcrumbs->add(__('Notifications & Alerts'),'','');

        $system_settings = SystemSettings::findFirst("key = 'notyfy'");
        if(!$system_settings) {
            $this->view->setVar('error_content',__('System Settings not found'));
            $this->response->setStatusCode(404, __('System Settings not found'));
            $this->view->pick("errors/404");
            return true;
        }
        else {
            $params = json_decode($system_settings->value,true);
            $options = array(
                'data' => $params
            );
            $this->view->form = new NotyfyForm(null, $options);

            if($this->request->isPost()) {
                $this->view->disable();

                $messages = Globals::messages();
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Action successful');

                $notyfy = new Notyfy();
                $status = $notyfy->save();

                if(!$status) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Action failed');
                    $messages['errors'] = [];
                    foreach ($notyfy->getMessages() as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if (PRODUCTION_STATUS == "DEBUG") {
                        $messages["debugs"] = $notyfy->getDebugs();
                    }
                    $this->flashSession->error(json_encode($messages['errors']));
                }
                $this->response->setJsonContent($messages)->send();
            }
        }
    }

    /**
     *
     */
    public function advancedSettingsAction()
    {
        $this->view->pick("advanced/index");
    }

    /**
     * @return ResponseInterface|void
     */
    public function passwordSettingsAction()
    {
        $this->tag->appendTitle(__('Password Settings'));
        $this->view->pick("advanced/password");

        $this->breadcrumbs->add(__('Password Settings'),'','');

        $system_settings = SystemSettings::findFirst("key = 'password_str'");
        if(!$system_settings) {
            $params = array(
                'alpha' => false,
                'numeric' => false,
                'special' => false,
                'minimum' => '5',
                'maximum' => '32'
            );
        }
        else {
            $params = json_decode($system_settings->value,true);
            if(empty($params)) {
                $params = array(
                    'alpha' => false,
                    'numeric' => false,
                    'special' => false,
                    'minimum' => '5',
                    'maximum' => '32'
                );
            }
        }




        $options = array(
            'page' => 'password',
            'data' => $params
        );
        $this->view->form = new AdvancedSettingForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $advanced_settings = new AdvancedSettings();
            $status = $advanced_settings->savePasswordRule();

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($advanced_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Settings saved'));
                    return $this->response->redirect($this->uri->url_encrypt('system/passwordsettings'),true);
                }
            }
        }
    }

    /**
     * @return bool|ResponseInterface|void
     */
    public function certificateAction()
    {
        $this->tag->appendTitle(__('System Certificate'));
        $this->view->pick("advanced/certificate");

        $this->breadcrumbs->add(__('System Certificate'),'','');

        $system_settings = SystemSettings::findFirst("key = 'certificate'");
        if(!$system_settings) {
            $params = array(
                'activate' => false,
                'pri_key' => '',
                'crt' => '',
                'ca_crt' => '',
                'hostname' => '',
            );
            $this->view->setVar('uploaded_certificate',false);
        }
        else {
            $params = json_decode($system_settings->value,true);
            if(empty($params)) {
                $params = array(
                    'activate' => false,
                    'pri_key' => '',
                    'crt' => '',
                    'ca_crt' => '',
                    'hostname' => '',
                );
                $this->view->setVar('uploaded_certificate',false);
            }
            else {
                $this->view->setVar('uploaded_certificate', $params);
            }
        }


        $options = array(
            'page' => 'certificate',
            'data' => $params
        );
        $this->view->form = new AdvancedSettingForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $crt = "";
            $ca_crt = "";
            $pri_key = "";
            if($this->request->hasFiles()) {
                foreach ($this->request->getUploadedFiles() as $file) {
                    if($file->getSize() > 0) {
                        if($file->getRealType() != "text/plain") {
                            if(!$this->request->isAjax()) {
                                $this->flashSession->error(json_encode([__('Unknown file type')]));
                                return true;
                            }
                        }
                    }
                    if($file->getSize() > 0) {
                        switch($file->getKey()) {
                            case "crt":
                                if($file->getSize() > 1) {
                                    $file_temp_name = $file->getTempName();
                                    $crt = $this->readFile($file_temp_name);
                                    unlink($file->getTempName());
                                }
                                else {
                                    if(!$this->request->isAjax()) {
                                        $this->flashSession->error(json_encode([__('Certificate file can not be empty')]));
                                        return true;
                                    }
                                }
                                break;
                            case "ca_crt":
                                if($file->getSize() > 1) {
                                    $file_temp_name = $file->getTempName();
                                    $ca_crt = $this->readFile($file_temp_name);
                                    unlink($file->getTempName());
                                }
                                break;
                            case "pri_key":
                                if($file->getSize() > 1) {
                                    $file_temp_name = $file->getTempName();
                                    $pri_key = $this->readFile($file_temp_name);
                                    unlink($file->getTempName());
                                }
                                break;
                        }
                    }
                }
            }

            $ssl_activate = $this->request->getPost("activate_certificate",null,OFF);
            $advanced_settings = new AdvancedSettings();
            $post = array(
                'crt' => $crt,
                'pri_key' => $pri_key,
                'ca_crt' => $ca_crt,
                'hostname' => $this->request->getPost('hostname'),
                'activate_certificate' => $ssl_activate,
            );
            $advanced_settings->setPosts($post);
            $status = $advanced_settings->saveCertificate();

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];

                foreach ($advanced_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax()) {
                if($status && $ssl_activate) {
                    $conn_test = $advanced_settings->testSslConnection($this->request->getPost("hostname"));
                    if(!$conn_test)
                        $messages["messages"] = __("Certificate defined on your system. But ssl connection test is failed. Please try again");
                }
                $this->response->setJsonContent($messages)->send();
            }
            else {
                if ($status) {
                    if($ssl_activate) {
                        $conn_test = $advanced_settings->testSslConnection($this->request->getPost("hostname"));
                        if(!$conn_test)
                            $this->flashSession->success(__('Certificate defined on your system. But ssl connection test is failed. Please try again'));
                    }
                    else
                        $this->flashSession->success(__('Settings saved'));
                    return $this->response->redirect($this->uri->url_encrypt('system/certificate'),true);
                }
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function recaptchasettingsAction()
    {
        $this->tag->appendTitle(__('Recaptcha Settings'));
        $this->view->pick("advanced/recaptcha");

        $this->breadcrumbs->add(__('Recaptcha Settings'),'','');

        $ini = new Parser(ROOTPATH . "/config/config.ini");
        $params = array(
            'status' => $ini->data['recaptcha']['status'],
            'site_key' => $ini->data['recaptcha']['site_key'],
            'site_secret' => $ini->data['recaptcha']['site_secret'],
            'attempts' => $ini->data['recaptcha']['attempts'],
        );

        if($ini->data['recaptcha']['status'] == "1") {
            $this->view->setVar('recaptcha_activate',true);
            $this->view->setVar('site_key', $ini->data['recaptcha']['site_key']);
        }
        else {
            $this->view->setVar('recaptcha_activate',false);
        }

        $options = array(
            'page' => 'recaptcha',
            'data' => $params
        );
        $this->view->form = new AdvancedSettingForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $advanced_settings = new AdvancedSettings();
            $status = $advanced_settings->saveRecaptcha();

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($advanced_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('Settings saved'));
                    return $this->response->redirect($this->uri->url_encrypt('system/recaptchasettings'),true);
                }
            }
        }
    }

    /**
     * @return ResponseInterface|void
     */
    public function addnewadminAction()
    {
        $this->view->pick("advanced/addnewadmin");

        $this->tag->appendTitle(__('Additional System Administrator'));

        $this->breadcrumbs->add(__('Additional System Administrator'),'','');

        $options = array(
            'page' => 'newadmin'
        );
        $this->view->form = new AdvancedSettingForm(null, $options);

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Action successful');

            $notyfy = new AdvancedSettings();
            $status = $notyfy->addNewAdmin();

            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                foreach ($notyfy->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status) {
                    $this->flashSession->success(__('User is added'));
                    return $this->response->redirect($this->uri->url_encrypt('system/addnewadmin'),true);
                }
            }
        }
    }

    /**
     * @param $path
     * @return false|string|void
     */
    private function readFile($path)
    {
        $file = fopen($path, "r") or die(false);
        $data = fread($file,filesize($path));
        fclose($file);
        return $data;
    }
}