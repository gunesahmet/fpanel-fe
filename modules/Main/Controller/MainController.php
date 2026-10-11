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
 */namespace Main\Controller;

use FPanel\Environments\Parser;
use FPanel\Exceptions\AuthException;
use FPanel\Forms\LoginForm;
use FPanel\Models\Support;
use Phalcon\Http\ResponseInterface;

/**
 * Class MainController
 * @package Main\Controller
 */
class MainController extends ControllerBase
{

    /**
     * @return bool|ResponseInterface|void
     */
    public function indexAction()
    {
        if(!$this->session->has('identity')) {
            $this->view->setMainView("login/index");
            $form = new LoginForm();
            $this->view->form = $form;
            $captcha = false;
            if((isset($this->config->recaptcha->status)) && ($this->config->recaptcha->status  == "1")) {
                $captcha = true;
            }
            $this->view->setVar('captcha', $captcha);

            $this->tag->appendTitle(__('Login'));

            if(demo) {
                if((isset($_GET['username'])) && (isset($_GET['password']))) {
                    $username = $_GET['username'];
                    $password = $_GET['password'];

                    try {
                        $credentials = array(
                            'username' => $username,
                            'password' => $password,
                            'remember' => ""
                        );

                        if($this->session->has('captcha')) {
                            $this->view->setVar('captcha',true);
                        }

                        if($this->auth->check($credentials) == true) {
                            return $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
                        }

                        $identity = $this->session->get('identity');

                        if(($identity['type'] > ADMIN_USER) && ($identity['type'] < SUPPORT_USER)) {
                            $ini = new Parser(ROOTPATH.'/config/config.ini');
                            if($ini->data['support']['logins'] == "true") {
                                $this->session->destroy();
                                $this->view->setVar('error_content',__('System is under maintenance'));
                                $this->response->setStatusCode(500, __('System is under maintenance'));
                                $this->view->pick("errors/500");
                                return true;
                            }
                        }
                        if((isset($identity['installation'])) && ($identity['installation'] == "true")) {
                            return $this->response->redirect($this->uri->url_encrypt("install"),true);
                        }
                        else {
                            return $this->response->redirect('/',true);
                        }
                    }
                    catch (AuthException $e) {
                        if(!$this->request->isAjax())
                            $this->flashSession->error(json_encode([$e->getMessage()]));
                    }
                }
                else {
                    if($this->request->isPost() == true) {
                        try {
                            $username = $this->request->getPost('username');
                            $password = $this->request->getPost('password');
                            $remember = $this->request->getPost('remember');
                            $credentials = array(
                                'username' => $username,
                                'password' => $password,
                                'remember' => $remember
                            );

                            if($this->session->has('captcha')) {
                                $this->view->setVar('captcha',true);
                            }

                            if($this->auth->check($credentials) == true) {
                                return $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
                            }

                            $identity = $this->session->get('identity');

                            if(($identity['type'] > ADMIN_USER) && ($identity['type'] < SUPPORT_USER)) {
                                $ini = new Parser(ROOTPATH.'/config/config.ini');
                                if($ini->data['support']['logins'] == "true") {
                                    $this->session->destroy();
                                    $this->view->setVar('error_content',__('System is under maintenance'));
                                    $this->response->setStatusCode(500, __('System is under maintenance'));
                                    $this->view->pick("errors/500");
                                    return true;
                                }
                            }
                            if((isset($identity['installation'])) && ($identity['installation'] == "true")) {
                                return $this->response->redirect($this->uri->url_encrypt("install"),true);
                            }
                            else {
                                return $this->response->redirect('/',true);
                            }
                        }
                        catch (AuthException $e) {
                            if(!$this->request->isAjax())
                                $this->flashSession->error(json_encode([$e->getMessage()]));
                        }
                    }
                }
            }
            else {
                if($this->request->isPost()) {
                    try {
                        $username = $this->request->getPost('username');
                        $password = $this->request->getPost('password');
                        $remember = $this->request->getPost('remember');
                        $credentials = array(
                            'username' => $username,
                            'password' => $password,
                            'remember' => $remember
                        );


                        if($this->auth->check($credentials)) {
                            $identity = $this->session->get('identity');

                            if(($identity['type'] > ADMIN_USER) && ($identity['type'] < SUPPORT_USER)) {
                                $ini = new Parser(ROOTPATH.'/config/config.ini');
                                if($ini->data['support']['logins'] == "true") {
                                    $this->session->destroy();
                                    $this->view->setVar('error_content',__('System is under maintenance'));
                                    $this->response->setStatusCode(500, __('System is under maintenance'));
                                    $this->view->pick("errors/500");
                                    return true;
                                }
                            }

                            if($this->request->has('templates')) {
                                $template = $this->request->getPost('templates');
                                $this->fcookies->set("template", $this->crypto->staticDecode($template), time() + 15 * 86400);
                            }

                            if((isset($identity['installation'])) && ($identity['installation'] === true)) {
                                return $this->response->redirect($this->uri->encode("install"),true);
                            }
                            else {
                                return $this->response->redirect($this->uri->url_encrypt("dashboard"),true);
                            }
                        }
                    }
                    catch (AuthException $e) {
                        if(!$this->request->isAjax()) {
                            $this->flashSession->setCssClasses([]);
                            $this->flashSession->error($e->getMessage());
                        }
                    }
                }
            }
        }
        else {
            if(file_exists(ROOTPATH.'/.firstboot')) {
                return $this->response->redirect($this->uri->encode('install'),true);
            }
            else {
                return $this->response->redirect($this->uri->url_encrypt("dashboard/empty", true).DIRECTORY_SEPARATOR.$this->uri->url_encrypt("dashboard"),false);
            }
        }
    }

    public function logoutAction()
    {
        if($this->auth->getUserType() == SUPPORT_USER) {
            $ini = new Parser(ROOTPATH.'/config/config.ini');
            $ini->data['support']['status'] = "\"false\"";
            $ini->data['support']['logins'] = "\"false\"";
            $ini->write();
            $error_messages = [];
            $support = Support::findFirst("status = '1'");
            if($support) {
                $support->connecting_status = "0";
                if (!$support->update()) {
                    foreach ($support->getMessages() as $message) {
                        $error_messages[$message->getField()] = $message->getMessage();
                        $this->flashSession->error(json_encode($error_messages));
                    }
                    return;
                }
            }
        }
        $this->auth->remove();
    }

    public function captchaAction()
    {
        $this->view->disable();
        print_r($this->drawCaptcha());
        exit(0);
    }


    private function drawCaptcha()
    {
        $captcha_text = $this->randomString();
        header("Content-type: image/png");
        $this->session->set('captcha', $captcha_text);
        $text_arr = str_split($captcha_text);
        $im = @imagecreate('150', '50');
        imageline($im, rand(0, 179), rand(0, 49), rand(0, 179), rand(0, 49), imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255)));
        imageline($im, rand(0, 179), rand(0, 49), rand(0, 179), rand(0, 49), imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255)));
        imageline($im, rand(0, 179), rand(0, 49), rand(0, 179), rand(0, 49), imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255)));
        imageline($im, rand(0, 179), rand(0, 49), rand(0, 179), rand(0, 49), imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255)));
        imageline($im, rand(0, 179), rand(0, 49), rand(0, 179), rand(0, 49), imagecolorallocate($im, rand(200, 255), rand(200, 255), rand(200, 255)));
        imagestring($im, 5, rand(30, 40), rand(11, 33), $text_arr[0], imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100)));
        imagestring($im, 5, rand(50, 60), rand(11, 33), $text_arr[1], imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100)));
        imagestring($im, 5, rand(70, 80), rand(11, 33), $text_arr[2], imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100)));
        imagestring($im, 5, rand(90, 100), rand(11, 33), $text_arr[3], imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100)));
        imagestring($im, 5, rand(110, 120), rand(11, 33), $text_arr[4], imagecolorallocate($im, rand(0, 100), rand(0, 100), rand(0, 100)));
        imagepng($im);
        imagedestroy($im);
    }

    private function randomString() {
        $string = "23456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        return substr(str_shuffle($string), 0, 5);
    }

    /**
     * @param $theme
     */
    public function changethemeAction($theme)
    {
        $template_dir = ROOTPATH."/templates";
        $template_list = array_diff(scandir($template_dir), array('..', '.'));

        $status = false;
        foreach($template_list as $template) {
            if($template == $theme) {
                $status = true;
                break;
            }
        }

        if($status == true)
            $this->fcookies->set("template", $theme, time() + 15 * 86400);

        $referrer = $this->request->getHTTPReferer();
        $this->response->redirect($referrer,true);
    }

    public function languageAction($language)
    {
        $this->view->disable();
        if (array_key_exists($language, $this->translate->getLangList())) {
            $this->fcookies->set("language", $this->crypto->staticDecode($language), time() + 30 * 86400);
        }

        $referrer = $this->request->getHTTPReferer();
        $parsed_url = parse_url($referrer);

        $redirect_url = rtrim(DIR_SEPARATOR.$parsed_url["path"], "/");

        $redirect_url_dec = $this->env->replaceAll("/","", $redirect_url);
        $decrypted_url = $this->uri->url_decrypt($redirect_url_dec);

        if(strpos($decrypted_url, "language") !== false) {
            $redirect_url = "/";
        }

        $redirect_url = $this->env->replaceAll("//","/", $redirect_url);
        $redirect_url_last = $parsed_url["scheme"].":".DIR_SEPARATOR.DIR_SEPARATOR.$parsed_url["host"].":". $parsed_url["port"] . $redirect_url;
        $this->response->redirect($redirect_url_last);
    }

    public function showScreenShotAction($image_name)
    {
        $file = ROOTPATH . "/ss/$image_name.jpg";

        if (file_exists($file)) {
            $size = getimagesize($file);
            $fp = fopen($file, 'rb');
            if ($size and $fp) {
                header('Content-Type: '.$size['mime']);
                header('Content-Length: '.filesize($file));
                fpassthru($fp);
            }
        }
        exit(0);
    }
}