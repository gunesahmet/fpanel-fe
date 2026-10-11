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
namespace Packages\Controller;

use FPanel\Globals\Globals;
use FPanel\Models\Servers;
use FPanel\Server\Packages;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class PackagesController
 * @package Packages\Controller
 */
class PackagesController extends ControllerBase
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
                        $this->server_id = $server->id;
                        $this->view->setVar('server_id', $server_id);
                        if($server_id != "1")
                            $this->breadcrumbs->add($server->name,'server/dashboard/server/'.$server_id,' ');
                        $this->server_data = $server->toArray();
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
        else {
            $server = Servers::findFirst("type = 'main'");
            if(!$server) {
                $this->view->setVar('error_content',__('Server not found'));
                $this->response->setStatusCode(404, __('Server not found'));
                $this->view->pick("errors/404");
                return true;
            }
            else {
                $server_data = $server->toArray();
                $this->view->setVar('server_id', $server->id);
                $this->server_data = $server_data;
            }
            $this->server_os_type = PANEL_OS_TYPE;

        }
        $this->view->setVar("server_os_type", $this->server_os_type);
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->tag->appendTitle(__('Server Packages'));

        if($this->system_data["system_type"] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $update_source_url = $this->uri->url_encrypt("packages/updatesourcetree/server/$server_id");
            $source_lists_url = $this->uri->url_encrypt("packages/sourcelists/server/$server_id");
            $current_url = $this->uri->url_encrypt("packages/server/$server_id");
            $server_os = $this->server_os_type;
        }
        else {
            $update_source_url = $this->uri->url_encrypt("packages/updatesourcetree");
            $source_lists_url = $this->uri->url_encrypt("packages/sourcelists");
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $current_url = $this->uri->url_encrypt("packages");
            $server_os = PANEL_OS_TYPE;
        }

        $this->breadcrumbs->add(__('Server Packages'),'','');


        $this->view->setVar('current_url', $current_url);
        $this->view->setVar('source_lists_url', $source_lists_url);
        $this->view->setVar('update_source_url', $update_source_url);
        $this->view->setVar('server_id', $server_id);
        $this->view->setVar('server_os', $server_os);

        if (($this->request->isAjax() == true) && ($this->request->isPost() == true)) {
            $this->view->disable();
            $packages = new Packages();
            $this->response->setJsonContent($packages->getPackageListFromDB($server_id))->send();
        }
    }

    public function updateSourceTreeAction()
    {
        if($this->request->isAjax()) {
            $this->view->disable();
        }
        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("packages/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("packages");
        }

        $packages = new Packages();
        $status = $packages->updateSourceTree($server_id);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Update successful');

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Update failed');
            $messages['errors'] = [];

            foreach ($packages->getMessages() as $title => $message) {
                $messages['errors'][$title] = $message;
            }
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode($messages['errors']));
        }

        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            if ($status == true) {
                $this->flashSession->success(__('Source tree updated'));
                $this->response->redirect($redirect_url,true);
            }
        }
    }

    /**
     * @param $package_id
     * @param $confirm
     * @return ResponseInterface|void
     */
    public function installAction($package_id, $confirm)
    {
        if($this->request->isAjax()) {
            $this->view->disable();
        }

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("packages/server/$server_id");
        }
        else {
            $redirect_url = $this->uri->url_encrypt("packages");
        }

        $packages = new Packages();
        $status = $packages->installPackage($package_id, $confirm);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Install successful');

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Install failed');
            if($confirm == false)
                $messages["messages"] = $packages->getMessages()["content"];

            $messages['errors'] = [];

            foreach ($packages->getMessages() as $title => $message) {
                $messages['errors'][$title] = $message;
            }
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode($messages['errors']));
        }
        else {
            if($confirm == 0) {
                $messages['content'] = $status;
                $messages['url'] = $this->uri->url_encrypt("packages/install/$package_id/1");
            }
        }
        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            if ($status == true) {
                $this->flashSession->success(__('Source List updated'));
                return $this->response->redirect($redirect_url,true);
            }
        }
    }

    /**
     * @param $package_id
     */
    public function removeAction($package_id)
    {
        if($this->request->isAjax()) {
            $this->view->disable();
        }

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("packages/server/$server_id");
        }
        else {
            $redirect_url = $this->uri->url_encrypt("packages");
        }

        $packages = new Packages();
        $status = $packages->unInstallPackage($package_id);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Uninstall successful');
        $messages['redirect_url'] = $redirect_url;

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Uninstall failed');
            $messages['errors'] = [];

            foreach ($packages->getMessages() as $title => $message) {
                $messages['errors'][$title] = $message;
            }
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode($messages['errors']));
        }


        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            if ($status == true) {
                $this->flashSession->success(__('Source List updated'));
                $this->response->redirect($redirect_url,true);
            }
        }
    }

    public function sourcelistsAction()
    {
        $this->tag->appendTitle(__('Source Lists'));

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $this->view->setVar("server_id", $server_id);
            $packages_url = $this->uri->url_encrypt("packages/server/$server_id");
            $update_url = $this->uri->url_encrypt("packages/updatesourcelist/server/$server_id");
            $remove_url = $this->uri->url_encrypt("packages/removesourcelist/server/$server_id");
            $create_url = $this->uri->url_encrypt("packages/createsourcelist/server/$server_id");
            $init_source_list_url = $this->uri->url_encrypt("packages/initsourcelist/server/$server_id");
        }
        else {
            $this->breadcrumbs->add(__('Server Packages'),'packages',' ');
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $packages_url = $this->uri->url_encrypt("packages");
            $update_url = $this->uri->url_encrypt("packages/updatesourcelist");
            $remove_url = $this->uri->url_encrypt("packages/removesourcelist");
            $create_url = $this->uri->url_encrypt("packages/createsourcelist");
            $init_source_list_url = $this->uri->url_encrypt("packages/initsourcelist");
        }

        $this->breadcrumbs->add(__('Server Source Lists'),'','');

        $packages = new Packages();

        $source_list_contenst = $packages->getSourceListContent($server_id);
        if(!$source_list_contenst) {
            $this->view->setVar('error_content',__('Source list cant read'));
            $this->response->setStatusCode(500, __('Source list cant read'));
            $this->view->pick("errors/500");
        }
        else {
            $this->view->setVar('packages_url', $packages_url);
            $this->view->setVar('update_url', $update_url);
            $this->view->setVar('remove_url', $remove_url);
            $this->view->setVar('create_url', $create_url);
            $this->view->setVar('source_lists_contents', $source_list_contenst);
            $this->view->setVar('init_source_list_url', $init_source_list_url);
        }
    }

    public function createSourceListAction()
    {
        if($this->request->isAjax()) {
            $this->view->disable();
        }

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists");
        }

        $packages = new Packages();
        if((strtolower($system_data["os"]) == "debian") || (strtolower($system_data["os"]) == "ubuntu")) {
            $packages->setPost("extension", ".list");
        }
        else {
            $packages->setPost("extension", ".repo");
        }
        $status = $packages->createSourceList($server_id);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Update successful');
        $messages['redirect_url'] = $redirect_url;

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Update failed');
            $messages['errors'] = [];

            foreach ($packages->getMessages() as $title => $message) {
                $messages['errors'][$title] = $message;
            }
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode($messages['errors']));
        }


        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            if ($status == true) {
                $this->flashSession->success(__('Source List updated'));
                $this->response->redirect($redirect_url,true);
            }
        }
    }

    public function updateSourceListAction()
    {
        if($this->request->isAjax()) {
            $this->view->disable();
        }

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists");
        }

        $packages = new Packages();
        $status = $packages->updateSourceList($server_id);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Update successful');
        $messages['redirect_url'] = $redirect_url;

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Update failed');
            $messages['errors'] = [];

            foreach ($packages->getMessages() as $title => $message) {
                $messages['errors'][$title] = $message;
            }
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode($messages['errors']));
        }

        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            if ($status == true) {
                $this->flashSession->success(__('Source List updated'));
                $this->response->redirect($redirect_url,true);
            }
        }
    }

    public function removeSourceListAction()
    {
        if($this->request->isAjax()) {
            $this->view->disable();
        }

        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists");
        }

        $packages = new Packages();
        $status = $packages->removeSourceList($server_id);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Remove successful');
        $messages['redirect_url'] = $redirect_url;

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Remove failed');
            $messages['errors'] = [];

            foreach ($packages->getMessages() as $title => $message) {
                $messages['errors'][$title] = $message;
            }
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode($messages['errors']));
        }

        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            if ($status == true) {
                $this->flashSession->success(__('Source List removed'));
                $this->response->redirect($redirect_url,true);
            }
        }
    }

    public function initSourcelistAction()
    {
        if($this->request->isAjax()) {
            $this->view->disable();
        }
        $system_data = $this->system_data;
        if($system_data['system_type'] == CLOUD) {
            $server_id = $this->server_id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists/server/$server_id");
        }
        else {
            $servers = Servers::findFirst("type = 'main'");
            $server_id = $servers->id;
            $redirect_url = $this->uri->url_encrypt("packages/sourcelists");
        }

        $packages = new Packages();
        $status = $packages->initSourcelist($server_id);

        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Update successful');

        if(!$status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Update failed');
            $messages['errors'] = [];

            foreach ($packages->getMessages() as $title => $message) {
                $messages['errors'][$title] = $message;
            }
            if(!$this->request->isAjax())
                $this->flashSession->error(json_encode($messages['errors']));
        }

        if ($this->request->isAjax())
            $this->response->setJsonContent($messages)->send();
        else {
            if ($status == true) {
                $this->flashSession->success(__('Source List updated'));
                $this->response->redirect($redirect_url,true);
            }
        }
    }
}