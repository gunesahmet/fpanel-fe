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
 */namespace FPanel\System;

use FPanel\Environments\Parser;
use FPanel\Library\Library;
use FPanel\Models\SystemSettings;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use FPanel\Process\Ssl;

/**
 * Class AdvancedSettings
 * @package FPanel\System
 */
class AdvancedSettings extends Library
{
    public function savePasswordRule(): bool
    {
        if($this->passwordValidate() == false)
            return false;

        $misc = SystemSettings::findFirst("key = 'password_str'");
        if($misc) {
            if(!$misc->delete()) {
                foreach($misc->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "AdvancedSettings->savePasswordRule SystemSettings delete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        $misc = new SystemSettings();
        if(($this->has("alpha_chk")) && ($this->getPost("alpha_chk") == ON))
            $alpha = true;
        else
            $alpha = false;
        if(($this->has("numeric_chk")) && ($this->getPost("numeric_chk") == ON))
            $numeric = true;
        else
            $numeric = false;
        if(($this->has("special_chk")) && ($this->getPost("special_chk") == ON))
            $special = true;
        else
            $special = false;

        $data = array(
            'alpha' => $alpha,
            'numeric' => $numeric,
            'special' => $special,
            'minimum' => $this->getPost("minimum_len"),
            'maximum' => $this->getPost("maximum_len"),
        );
        $misc->assign(
            array(
                'key' => 'password_str',
                'value' => json_encode($data)
            )
        );
        if (!$misc->create()) {
            foreach ($misc->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "AdvancedSettings->savePasswordRule SystemSettings create",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;

    }

    public function saveCertificate(): bool
    {
        if($this->certificateValidate() == false) {
            return false;
        }

        $misc = SystemSettings::findFirst("key = 'certificate'");
        if($misc) {
            if(!$misc->delete()) {
                foreach($misc->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "AdvancedSettings->saveCertificate SystemSettings delete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        if($this->getPost("activate_certificate") == "on")
            $activate = true;
        else
            $activate = false;

        $pri_key = $this->getPost("pri_key");
        $crt = $this->getPost("crt");
        $ca_crt = $this->getPost("ca_crt");

        $params = array(
            'activate' => $activate,
            'pri_key' => $pri_key,
            'crt' => $crt,
            'ca_crt' => $ca_crt,
            'hostname' => $this->getPost("hostname"),
        );
        $misc = new SystemSettings();
        $misc->assign(array(
            'key' => 'certificate',
            'value' => json_encode($params)
        ));
        if (!$misc->create()) {
            foreach ($misc->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "AdvancedSettings->saveCertificate SystemSettings create",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        $sslmng = new Ssl();

        if($activate == true) {
            if(PANEL_OS_TYPE == WINDOWS) {
                $sslmng->insertSystemCert([
                    "crt" => $this->crypto->staticEncode($crt),
                    "pkey" => $this->crypto->staticEncode($pri_key),
                ]);
                $sslmng->insertSystemCertBundle([
                    "ca-bundle" => $this->crypto->staticEncode($ca_crt),
                ]);
            }
            $sslmng->installSystemSsl();
        }
        else {
            $sslmng->deactivateSystemSsl();
        }
        return true;
    }

    public function testSslConnection($hostname): bool
    {
        $url = "https://$hostname";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_COOKIESESSION, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CAINFO, ROOTPATH . "/tmp/BuiltinObjectToken-EquifaxSecureCA.crt");

        curl_setopt($curl, CURLOPT_PORT, 8443);

        curl_setopt($curl, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                    return $len;
                return $len;
            }
        );
        curl_exec($curl);

        if (curl_errno($curl) > 0) {
            return false;
        }
        curl_close($curl);
        return true;
    }

    public function saveRecaptcha(): bool
    {
        if($this->recaptchaValidate() == false)
            return false;

        $ini = new Parser(ROOTPATH.'/config/config.ini');
        $site_key = $this->getPost("site_key");
        $site_secret = $this->getPost("site_secret");

        if(($this->has("activate_recaptcha")) && ($this->getPost("activate_recaptcha") == "on"))
            $status = ACTIVE;
        else
            $status = PASSIVE;
        $ini->data['recaptcha']['status'] = "\"$status\"";

        $ini->data['recaptcha']['site_key'] = "\"$site_key\"";
        $ini->data['recaptcha']['site_secret'] = "\"$site_secret\"";
        $ini->write();

        return true;
    }

    public function addNewAdmin(): bool
    {
        if($this->adminValidation() == false)
            return false;
        $root_user = Users::findFirst("type = '0'");
        $user = new Users();
        $user->assign(array(
            "id"                        => $this->env->createUuid(),
            'username'                  => $this->getPost("username"),
            'password'                  => $this->security->hash($this->getPost("password")),
            'type'                      => ADMIN_USER,
            'must_change_password '     => PASSIVE,
            'status'                    => ACTIVE,
            'parent'                    => $root_user->id,
            'interface'                 => "default",
            'language'                  => "en_US",
            'api_status'                => PASSIVE,
        ));

        if($user->create()) {
            $user_data = new Userdata();
            $user_data->assign(array(
                'user_id' => $user->id,
                'fullname'  => $this->getPost("fullname"),
                'email'     => $this->getPost("email"),
            ));
            if(!$user_data->create()) {
                foreach ($user_data->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "AdvancedSettings->addNewAdmin userdata",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }


            return true;
        }
        else {
            foreach ($user->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "AdvancedSettings->addNewAdmin users",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }

    }

    private function adminValidation(): bool
    {
        $required_fields = array(
            array(
                'field' => 'username',
                'item' => $this->getPost("username"),
                'message' =>  __('Username is required')
            ),
            array(
                'field' => 'email_addr',
                'item' => $this->getPost("email"),
                'message' =>  __('Email is required')
            ),
        );

        $this->validation->Required($required_fields)
            ->Username($this->getPost("username"),'username')
            ->Email($this->getPost("email"),'email_addr')
            ->UniqueUser($this->getPost("username"),'username',null,false);

        $this->validation->Password($this->getPost("password"),'password',null,false);

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    private function recaptchaValidate(): bool
    {
        if(($this->has("activate_recaptcha")) && ($this->getPost("activate_recaptcha") == "on")) {
            $this->validation->Required(array(
                array(
                    'field' => 'site_key',
                    'item' => $this->getPost("site_key"),
                    'message' => __('Site Key is required'),
                ),
                array(
                    'field' => 'site_secret',
                    'item' => $this->getPost("site_secret"),
                    'message' => __('Site Secret is required'),
                ),
            ));
            if($this->validation->Validate() == false) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }
        }
        return true;
    }

    private function certificateValidate(): bool
    {
        if($this->getPost("activate_certificate") == "on") {
            $this->validation->Required(array(
                array(
                    'field' => 'hostname',
                    'item' => $this->getPost("hostname"),
                    'message' => __('Hostname is required'),
                ),
                array(
                    'field' => 'crt',
                    'item' => $this->getPost("crt"),
                    'message' => __('Certificate File is required')
                ),
                array(
                    'field' => 'pri_key',
                    'item' => $this->getPost("pri_key"),
                    'message' => __('Private Key File is required')
                ),
                array(
                    'field' => 'ca_crt',
                    'item' => $this->getPost("ca_crt"),
                    'message' => __('CA Bundle Certificate File is required')
                ),
            ));

            $this->validation->Domain($this->getPost("hostname"),'hostname');

            if($this->validation->Validate() == false) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }
            else {
                if($this->validateCertificate() == false) {
                    return false;
                }
            }
        }
        return true;
    }

    private function validateCertificate(): bool
    {
        if(openssl_x509_check_private_key($this->getPost("crt"), $this->getPost("pri_key")) == true) {
            $newsource = openssl_x509_parse($this->getPost("crt"),true);

            if($newsource['subject']['CN'] != $this->getPost("hostname")) {
                $this->error_messages['Error'] = __('This SSL Certificate not for this hostname');
                return false;
            }
            return true;
        }
        else {
            $this->error_messages['Error'] = __('Certificate does not corresponds with private key');
            return false;
        }
    }

    private function passwordValidate(): bool
    {
        $this->validation->Required(array(
            array(
                'field' => 'minimum_len',
                'item' => $this->getPost("minimum_len"),
                'message' => __('Minimum Length is required')
            ),
            array(
                'field' => 'maximum_len',
                'item' => $this->getPost("maximum_len"),
                'message' => __('Maximum Length is required')
            ),
        ))
            ->Numeric($this->getPost("minimum_len"),'minimum_len',__('Minimum Length is must be numeric'))
            ->Numeric($this->getPost("maximum_len"),'maximum_len',__('Maximum Length is must be numeric'))
            ->MinMax($this->getPost("minimum_len"),'minimum_len',6,32,__('Minimum Length is must be between 6 with 32'))
            ->MinMax($this->getPost("maximum_len"),'maximum_len',6,32,__('Maximum Length is must be between 6 with 32'));

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}