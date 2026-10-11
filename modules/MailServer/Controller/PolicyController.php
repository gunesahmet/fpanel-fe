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
 */namespace MailServer\Controller;

use FPanel\Forms\MailServerForm;
use FPanel\Globals\Globals;
use FPanel\MailServer\PolicydSettings;
use FPanel\Models\Servers;
use Phalcon\Http\ResponseInterface;

/**
 *
 */
class PolicyController extends MailserverController
{
    /**
     * @return ResponseInterface|void
     */
    public function policydAction()
    {
        $this->view->pick("mailserver/policyd");

        $this->tag->appendTitle(__('Mail Server Policies'));

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/server/'.$server_id,' ');
            $this->view->setVar("create_url", $this->uri->url_encrypt("mailserver/addpolicy/server/$server_id"));
            $this->view->setVar("save_url", $this->uri->url_encrypt("mailserver/savepolicysettings/server/$server_id"));
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $this->view->setVar("create_url", $this->uri->url_encrypt("mailserver/addpolicy"));
            $this->view->setVar("save_url", $this->uri->url_encrypt("mailserver/savepolicysettings"));
        }
        $policyd = new PolicydSettings();


        $this->breadcrumbs->add(__("Mail Server Policies"),'','');
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true) ) {
            $this->view->disable();
            return $this->response->setJsonContent($policyd->policyLists($server_id))->send();
        }

        $this->view->form = new MailServerForm(null, ["data" => $policyd->getPolicySettings(), "part" => "policyd"]);
    }

    public function savePolicySettingsAction()
    {
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('New policy created');

            $policy_settings = new PolicydSettings();
            $policy_settings->setPost("server_id", $this->server_id);

            $status = $policy_settings->savePolicySettings();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to new policy create');
                $messages['errors'] = [];
                foreach ($policy_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status == true) {
                    $this->flashSession->success(__('New policy created'));
                }
            }
        }
    }

    public function addPolicyAction()
    {
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('New policy created');

            $policy_settings = new PolicydSettings();
            $policy_settings->setPost("server_id", $this->server_id);

            $status = $policy_settings->addPolicy();
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to new policy create');
                $messages['errors'] = [];
                foreach ($policy_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status == true) {
                    $this->flashSession->success(__('New policy created'));
                }
            }
        }
    }

    public function updatePolicyAction($policy_id)
    {
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Policy updated');

            $policy_settings = new PolicydSettings();
            $policy_settings->setPost("server_id", $this->server_id);

            $status = $policy_settings->updatePolicy($policy_id);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to update policy');
                $messages['errors'] = [];
                foreach ($policy_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status == true) {
                    $this->flashSession->success(__('Policy updated'));
                }
            }
        }
    }

    public function deletePolicyAction($policy_id)
    {
        if($this->request->isPost() == true) {
            if($this->request->isAjax() == true)
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Delete successful');

            $policy_settings = new PolicydSettings();
            $policy_settings->setPost("server_id", $this->server_id);

            $status = $policy_settings->deletePolicy($policy_id);
            if(!$status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to delete policy');
                $messages['errors'] = [];
                foreach ($policy_settings->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                $this->response->setJsonContent($messages)->send();
            else {
                if($status == true) {
                    $this->flashSession->success(__('Policy deleted'));
                }
            }
        }
    }


    public function policyMemberAction($policy_id)
    {
        if((isset($this->server_id) && ($this->server_id != "1"))) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/server/'.$server_id,' ');
        }
        else {
            $server_id = "1";
        }

        $policy_members = new PolicydSettings();
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true) ) {
            $this->view->disable();
            $this->response->setJsonContent($policy_members->policyMemberLists($policy_id, $server_id))->send();
        }
    }

    public function addPolicyMemberAction($policy_id)
    {

    }

    public function updatePolicyMemberAction($policy_id, $member_id)
    {

    }

    public function deletePolicyMemberAction($policy_id, $member_id)
    {

    }


    public function quotasAction($policy_id)
    {
        if((isset($this->server_id) && ($this->server_id != "1"))) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $this->server_id);
            $this->breadcrumbs->add(__('Server Dashboard'),'system/server/'.$server_id,' ');
        }
        else {
            $server_id = "1";
        }

        $policy_quotas = new PolicydSettings();
        if(($this->request->isPost() == true) && ($this->request->isAjax() == true) ) {
            $this->view->disable();
            $this->response->setJsonContent($policy_quotas->quotaLimitLists($policy_id, $server_id))->send();
        }
    }

    public function addQuotaAction($policy_id, $quota_id)
    {

    }

    public function updateQuotaAction($policy_id, $quota_id)
    {

    }

    public function deleteQuotaAction($policy_id, $quota_id)
    {

    }
}