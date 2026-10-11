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
namespace ApiManagement\Controller;

use Exception;
use FPanel\Forms\ApiForm;
use FPanel\Globals\Globals;
use FPanel\Models\Users;
use FPanel\System\Api;
use Main\Controller\ControllerBase;

/**
 * Class ApisettingsController
 * @package ApiManagement\Controller
 */
class ApisettingsController extends ControllerBase
{
    /**
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('system');
        $this->view->setVar("system_items", $user_data);

        $system_data = $user_data['system_data'];

        $this->breadcrumbs->add(__('Dashboard'),'dashboard',' ');
        if($system_data["system_type"] == CLOUD) {
            $this->breadcrumbs->add(__('Cloud Dashboard'),'system',' ');
        }
        else {
            $this->breadcrumbs->add(__('System Dashboard'),'system',' ');
        }
    }

    public function indexAction()
    {
        $this->view->pick('system/api');
        $this->breadcrumbs->add(__('Api Management'),'','');

        $this->tag->appendTitle(__('Api Management'));

        $options = array(
            'user_list' => $this->getAllUsers()
        );
        $this->view->form = new ApiForm(null, $options);

        $this->view->setVar('post_url', $this->uri->url_encrypt("apisettings"));
        if ($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Api Settings updated');

            $api = new Api();
            $status = $api->updateApiSettings();
            if( !$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Update failed');
                $messages['errors'] = [];
                foreach ($api->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if ($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if ($status == true) {
                    $this->flashSession->success(__('Api Settings updated'));
                    return $this->response->redirect($this->uri->url_encrypt('apisettings'),true);
                }
            }
        }
        return true;
    }

    private function getAllUsers(): array
    {
        $list = array();
        $users = Users::find();
        if($users) {
            $user_list = $users->toArray();
            foreach($user_list as $user) {
                $list[$user['id']] = $user['username'];
            }
        }
        return $list;
    }
}