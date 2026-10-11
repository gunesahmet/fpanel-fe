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
namespace Account\Controller;

use FPanel\Forms\AccountForm;
use FPanel\Globals\Globals;
use FPanel\System\Account;
use Main\Controller\ControllerBase;

/**
 * Class AccountController
 * @package Account\Controller
 */
class AccountController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array(); // Login olan userin verileri
    protected $client_id;           // Userin kontrol edeceği client idsi
    protected $client_type;         // Userin kontrol edeceği client türü
    protected $client_data;         // Userin kontrol edeceği clientin verileri
    protected $user_id;             // Login olan user idsi
    protected $user_type;           // Login olan user türü

    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $this->user_data = $this->acl->run('account');
        $user_data = $this->user_data;
        $this->view->setVar("system_items", $user_data);
        $this->user_id = $user_data['user']['info']['user_id'];
        $this->user_type = $user_data['user']['info']['user_type'];
        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
    }

    /**
     * index
     */
    public function indexAction()
    {
        $this->view->pick('system/account');
        $data = $this->user_data;
        $this->tag->appendTitle(__('My Account'));

        $this->breadcrumbs->add(__('My Account'),'','');

        $options = array(
            'username' => $data['user']['info']['username'],
            'fullname' => $data['user']['info']['fullname'],
            'email' => $data['user']['info']['email'],
            'company' => $data['user']['info']['company'],
            'fax' => $data['user']['info']['fax'],
            'telephone' => $data['user']['info']['telephone'],
            'address' => $data['user']['info']['address'],
            'city' => $data['user']['info']['city'],
            'region' => $data['user']['info']['region'],
            'country' => $data['user']['info']['country'],
        );
        $this->view->form = new AccountForm(null, $options);

        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            if(demo == true) {
                $dataRules = array('message' => 'warning', 'content' => __('This feature disabled in demo mode!'));
                $this->flashSession->error(json_encode([__('This feature disabled in demo mode!')]));
                if($this->request->isAjax() == true)
                    return $this->response->setJsonContent($dataRules)->send();
                else
                    return $this->response->redirect($this->uri->url_encrypt("account"),true);
            }

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Account updated');

            $account = new Account();

            $status = $account->save();
            if (!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to update');
                $messages["errors"] = [];
                foreach ($account->getMessages() as $title => $message) {
                    $messages["errors"][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages["errors"]));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Account updated'));
                    return $this->response->redirect($this->uri->url_encrypt("account"),true);
                }
            }
        }
        return true;
    }
}