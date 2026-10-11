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
 */namespace Certificate\Controller;

use Exception;
use FPanel\Domain\Services;
use FPanel\Domain\Sslcertificate;
use FPanel\Forms\CertificateForm;
use FPanel\Globals\Globals;
use FPanel\Models\Certificates;
use FPanel\Models\Domains;
use Main\Controller\ControllerBase;
use Phalcon\Http\ResponseInterface;

/**
 * Class CertificateController
 * @package Certificate\Controller
 */
class CertificateController extends ControllerBase
{
    protected $server_data  = array();
    protected $user_data = array();
    protected $domain_id;
    protected $domain_data;
    protected $user_id;
    protected $user_type;
    protected $error_messages;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;
        $user_data = $this->acl->run('ssl');
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
                    $domain = Domains::findFirstById($domain_id);
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
                        $this->view->setVar('client_type',DOMAIN_USER);
                        $client_menu = $this->sidebar->clientMenu(DOMAIN_USER, $domain_id);
                        $this->view->setVar("client_menu", $client_menu);
                    }
                    break;
                default:
                    $this->view->setVar('error_content',__('URL not found'));
                    $this->response->setStatusCode(404, __('Url not found'));
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
        $domain_services->checkDomainService($domain_id,"web", $redirect_url);
    }

    /**
     *
     */
    public function indexAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('SSL Certificates'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $this->view->setVar('domain_id', $domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
        }

        $this->breadcrumbs->add(__('SSL Certificates'),'','');


        if($this->request->isPost() && $this->request->isAjax()) {
            $this->view->disable();
            $ssl = new Sslcertificate();
            if(isset($this->domain_id)) {
                $this->response->setJsonContent($ssl->listSSL($domain_id,true))->send();
            }
            else {
                $this->response->setJsonContent($ssl->listSSL($domain_id))->send();
            }
        }
    }

    /**
     * @return ResponseInterface|void
     * @throws Exception
     */
    public function createAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('SSL Certificate Request'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('SSL Certificates'),'certificate/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("certificate/domain/".$domain_id);
            $post_url = $this->uri->url_encrypt("certificate/create/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('SSL Certificates'),'certificate',' ');
            $redirect_url = $this->uri->url_encrypt("certificate");
            $post_url = $this->uri->url_encrypt("certificate/create");
        }

        $this->breadcrumbs->add(__('SSL Certificate Request'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('post_url', $post_url);

        $options = array(
            'page' => 'request',
        );
        $options['domain_name'] = $domain_name;

        if($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __("Certificate created");
            $messages['redirect_url'] = $redirect_url;

            $ssl_certificate = new Sslcertificate();
            $post_data = array(
                'domain_id'     => $domain_id,
                'domain_name'   => $this->request->getPost('certificate_domain_name'),
                'name'          => $this->request->getPost('certificate_name'),
                'bit'           => $this->request->getPost('certificate_keysize'),
                'country'       => $this->request->getPost('certificate_country'),
                'state'         => $this->request->getPost('certificate_state'),
                'city'          => $this->request->getPost('certificate_city'),
                'company'       => $this->request->getPost('certificate_company'),
                'department'    => $this->request->getPost('certificate_department'),
                'email'         => $this->request->getPost('certificate_user_email'),
            );
            $ssl_certificate->setPosts($post_data);
            $ssl_status = $ssl_certificate->requestCSR();

            if(!$ssl_status) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Failed to create');
                $messages['errors'] = [];
                foreach ($ssl_certificate->getMessages() as $title => $message) {
                    $messages['errors'][$title] = $message;
                }
                if(!$this->request->isAjax())
                    $this->flashSession->error(json_encode($messages['errors']));
            }

            if($this->request->isAjax())
                return $this->response->setJsonContent($messages)->send();
            else {
                if($ssl_status) {
                    $this->flashSession->success(__('SSL Certificate Request is created'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        $this->view->form = new CertificateForm(null, $options);
    }

    /**
     * @param $id
     * @return bool|ResponseInterface|void
     */
    public function installAction($id)
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Add SSL Certificate'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('SSL Certificates'),'certificate/domain/'.$domain_id,' ');
            $redirect_url = $this->uri->url_encrypt("certificate/install/$id/domain/".$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $this->breadcrumbs->add(__('SSL Certificates'),'certificate',' ');
            $redirect_url = $this->uri->url_encrypt("certificate/install/".$id);
        }

        $this->breadcrumbs->add(__('Add SSL Certificate'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('id', $id);
        $this->view->setVar('post_url', $redirect_url);

        $get_ssl_data = Certificates::findFirst("id = '$id' AND domain_id = '$domain_id'");
        if($get_ssl_data) {
            $ssl_data = $get_ssl_data->toArray();
        }
        else {
            $this->view->setVar('error_content',__('Certificate not found'));
            $this->response->setStatusCode(404, __('Certificate not found'));
            $this->view->pick("errors/404");
            return true;
        }

        $options = array(
            'action' => 'install',
            'page'  => 'install',
            'ssl_data' => $ssl_data
        );

        $this->view->setVar('csr_code', $ssl_data['csr']);
        $this->view->setVar('private_key', $ssl_data['private_key']);

        if ($this->request->isPost()) {
            if($this->request->isAjax())
                $this->view->disable();

            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['messages'] = __('Rule updated');


            if($this->request->hasFiles()) {
                $ssl_upload_status = $this->uploadedFile($id, $domain_id, $domain_name);
                $status = $ssl_upload_status;
                if(!$ssl_upload_status) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Failed to installing');
                    $messages['errors'] = [];

                    foreach ($this->error_messages as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
            }
            else {
                $ssl_send_status = $this->withoutFile($id, $domain_id, $domain_name);
                $status = $ssl_send_status;
                if(!$ssl_send_status) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Failed to installing');
                    $messages['errors'] = [];

                    foreach($this->error_messages as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
            }

            if($this->request->isAjax())
                return $this->response->setJsonContent($messages)->send();
            else {
                if($status) {
                    $this->flashSession->success(__('SSL Certificate installed'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
        $this->view->form = new CertificateForm(null, $options);
    }

    /**
     * @return ResponseInterface|void
     */
    public function uploadAction()
    {
        $data = $this->user_data;
        $this->tag->appendTitle(__('Upload SSL Certificate'));

        if(isset($this->domain_id)) {
            $domain_id = $this->domain_id;
            $domain_name = $this->domain_data['name'];
            $redirect_url = $this->uri->url_encrypt('certificate/domain/'.$domain_id);
            $this->view->setVar('domain_id', $domain_id);
            $this->breadcrumbs->add(__('SSL Certificates'),'certificate/domain/'.$domain_id,' ');
            $post_url = $this->uri->url_encrypt('certificate/upload/domain/'.$domain_id);
        }
        else {
            $domain_id = $data['user']['domain_info']['domain_id'];
            $domain_name = $data['user']['domain_info']['domain_name'];
            $redirect_url = $this->uri->url_encrypt('certificate');
            $this->breadcrumbs->add(__('SSL Certificates'),'certificate',' ');
            $post_url = $this->uri->url_encrypt('certificate/upload');
        }

        $this->breadcrumbs->add(__('Upload SSL Certificate'),'','');

        $this->view->setVar('domain_name', $domain_name);
        $this->view->setVar('post_url', $post_url);


        $options = array(
            'action' => 'upload',
            'page'  => 'install',
            'ssl_data' => null,
        );
        $options['domain_name'] = $domain_name;
        $this->view->form = new CertificateForm(null, $options);

        if ($this->request->isPost()) {
            $messages = Globals::messages();
            $messages['type'] = 'success';
            $messages['title'] = __('Success');
            $messages['redirect_url'] = $redirect_url;
            $messages['messages'] = __('Certificate uploaded');

            if($this->request->hasFiles()) {
                $ssl_upload_status = $this->uploadWithPrivateKey($domain_id, $domain_name);
                $status = $ssl_upload_status;
                if(!$ssl_upload_status) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Failed to certificate upload');
                    $messages['errors'] = [];

                    foreach ($this->error_messages as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
            }
            else {
                if($this->request->isAjax())
                    $this->view->disable();

                $ssl_send_status = $this->uploadWithoutFile($domain_id, $domain_name);
                $status = $ssl_send_status;
                if(!$ssl_send_status) {
                    $messages['type'] = 'error';
                    $messages['title'] = __('Failed');
                    $messages['messages'] = __('Failed to certificate upload');
                    $messages['errors'] = [];

                    foreach ($this->error_messages as $title => $message) {
                        $messages['errors'][$title] = $message;
                    }
                    if(!$this->request->isAjax())
                        $this->flashSession->error(json_encode($messages['errors']));
                }
            }
            if($this->request->isAjax())
                return $this->response->setJsonContent($messages)->send();
            else {
                if(!$status) {
                    $this->flashSession->success(__('SSL Certificate upload is successful'));
                    return $this->response->redirect($redirect_url,true);
                }
            }
        }
    }

    /**
     * @param $id
     * @return ResponseInterface|void
     */
    public function deleteAction($id)
    {
        $this->view->disable();
        if ($this->request->isAjax()) {
            if ($this->request->isPost()) {

                $messages = $this->delete($id);

                return $this->response->setJsonContent($messages)->send();
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

    /**
     * @param $id
     * @param $domain_id
     * @param $domain_name
     * @return bool
     */
    private function uploadedFile($id, $domain_id, $domain_name): bool
    {
        $crt = null;
        $ca_crt = null;

        foreach ($this->request->getUploadedFiles() as $file) {
            switch($file->getKey()) {
                case "crtFile":
                    if($file->getSize() > 1) {
                        $file_temp_name = $file->getTempName();
                        $crt = $this->readFile($file_temp_name);
                        unlink($file->getTempName());
                    }
                    else {
                        $this->error_messages['message'] = __('Certificate file can not be empty');
                        return false;
                    }
                    break;
                case "cacrtFile":
                    if($file->getSize() > 1) {
                        $file_temp_name = $file->getTempName();
                        $ca_crt = $this->readFile($file_temp_name);
                        unlink($file->getTempName());
                    }
                    else {
                        $this->error_messages['message'] = __('Certificate file can not be empty');
                        return false;
                    }
                    break;
            }
        }


        $ssl = Certificates::findFirstById($id);
        if(openssl_x509_check_private_key($crt, $ssl->private_key)) {
            $newsource = openssl_x509_parse($crt);
            if($newsource['subject']['CN'] != $domain_name) {
                $this->error_messages['Error'] = __('This SSL Certificate not for this domain');
                return false;
            }

            $ssl_certificate = new Sslcertificate();
            $post_data = array(
                'id'            => $id,
                'domain_id'     => $domain_id,
                'domain_name'   => $domain_name,
                'crt'           => $crt,
                'ca_crt'        => $ca_crt,
            );
            $ssl_certificate->setPosts($post_data);
            $ssl_status = $ssl_certificate->installSSLFiles($id);

            if($ssl_status) {
                return true;
            }
            else {
                foreach ($ssl_certificate->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }
        }
        else {
            $this->error_messages['Error'] = __('Certificate does not corresponds with private key');
            return false;
        }
    }

    /**
     * @param $id
     * @param $domain_id
     * @param $domain_name
     * @return bool
     */
    private function withoutFile($id, $domain_id, $domain_name): bool
    {
        $ssl_certificate = new Sslcertificate();

        $ssl_certificate->setPost("domain_id", $domain_id);
        $ssl_certificate->setPost("domain_name", $domain_name);
        $ssl_status = $ssl_certificate->installSSLFiles($id);

        if($ssl_status) {
            return true;
        }
        else {
            foreach ($ssl_certificate->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return false;
        }

    }

    /**
     * @param $domain_id
     * @param $domain_name
     * @return bool
     */
    private function uploadWithPrivateKey($domain_id, $domain_name): bool
    {
        $this->error_messages = array('content' => __('SSL Certificate was not install! Please check your fields.'));
        $crt = null;
        $ca_crt = null;
        $pri_key = null;


        foreach ($this->request->getUploadedFiles() as $file) {
            switch($file->getKey()) {
                case "crtFile":
                    if($file->getSize() > 1) {
                        $file_temp_name = $file->getTempName();
                        $crt = $this->readFile($file_temp_name);
                        unlink($file->getTempName());
                    }
                    else {
                        $this->error_messages['message'] = __('Certificate file can not be empty');
                        return false;
                    }
                    break;
                case "cacrtFile":
                    if($file->getSize() > 1) {
                        $file_temp_name = $file->getTempName();
                        $ca_crt = $this->readFile($file_temp_name);
                        unlink($file->getTempName());
                    }
                    break;
                case "privateKeyFile":
                    if($file->getSize() > 1) {
                        $file_temp_name = $file->getTempName();
                        $pri_key = $this->readFile($file_temp_name);
                        unlink($file->getTempName());
                    }
                    else {
                        $this->error_messages['message'] = __('Private Key file can not be empty');
                        return false;
                    }
                    break;
            }

        }

        if($this->request->getPost('crt_name_file') == null) {
            $this->error_messages['Error'] = __('Certificate name is need');
            return false;
        }

        if($pri_key == null) {
            $this->error_messages['Error'] = __('Private key file is need');
            return false;
        }
        if($crt == null) {
            $this->error_messages['Error'] = __('Certificate file is need');
            return false;
        }

        if(openssl_x509_check_private_key($crt, $pri_key)) {
            $ssl_certificate = new Sslcertificate();
            $post_data = array(
                'name'          => $this->request->getPost('crt_name_file'),
                'domain_name'   => $domain_name,
                'domain_id'     => $domain_id,
                'private_key'   => $pri_key,
                'crt'           => $crt,
                'ca_crt'        => $ca_crt,
            );
            $ssl_certificate->setPosts($post_data);
            $ssl_status = $ssl_certificate->uploadCertificateFile();


            if($ssl_status) {
                return true;
            }
            else {
                foreach ($ssl_certificate->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }
        }
        else {
            $this->error_messages['Error'] = __('Certificate does not corresponds with private key');
            return false;
        }
    }

    /**
     * @param $domain_id
     * @param $domain_name
     * @return bool
     */
    private function uploadWithoutFile($domain_id, $domain_name): bool
    {

        if(($this->request->getPost('crt') == null) || ($this->request->getPost('pri_key') == null)) {
            $this->error_messages['content'] = __('Private key or Crt is missing');
            return false;
        }

        if(openssl_x509_check_private_key($this->request->getPost('crt'), $this->request->getPost('pri_key'))) {
            $ssl_certificate = new Sslcertificate();
            $ssl_certificate->setPost("domain_id", $domain_id);
            $ssl_certificate->setPost("domain_name", $domain_name);
            $ssl_status = $ssl_certificate->uploadCertificateFile();

            if($ssl_status) {
                return true;
            }
            else {
                foreach ($ssl_certificate->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }
        }
        else {
            $this->error_messages['content'] = __('Certificate does not corresponds with private key');
            return false;
        }

    }

    private function delete($id): array
    {
        $ssl = new Sslcertificate();
        $ssl_status = $ssl->deleteSSLCert($id);
        $messages = Globals::messages();
        $messages['type'] = 'success';
        $messages['title'] = __('Success');
        $messages['messages'] = __('Action successful');
        if (!$ssl_status) {
            $messages['type'] = 'error';
            $messages['title'] = __('Failed');
            $messages['messages'] = __('Action failed');
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

            if($this->request->getPost('id') == null) {
                $messages['type'] = 'success';
                $messages['title'] = __('Success');
                $messages['messages'] = __('Action successful');

            }
            else if($this->request->getPost("action") == null) {
                $messages['type'] = 'error';
                $messages['title'] = __('Failed');
                $messages['messages'] = __('Action failed');
                $messages['errors'] = [];
                $messages['errors']['message_summary'] = __('Please select an action');

            }
            else {
                switch($action) {
                    case "delete":
                        $messages = $this->delete($id);
                        break;
                    default:
                        $messages['type'] = 'error';
                        $messages['title'] = __('Failed');
                        $messages['messages'] = __('Action failed');
                        $messages['errors'] = [];
                        $messages['errors']['message_summary'] = __('Action is not found');

                        break;
                }
            }
            $this->response->setJsonContent($messages)->send();
        }
        else {
            $this->response->redirect($this->uri->url_encrypt('dashboard'),true);
        }
    }
}