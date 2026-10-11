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
 */namespace FPanel\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\File;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class AdvancedSettingForm
 * @package FPanel\Forms
 */
class AdvancedSettingForm extends Form
{
    public function initialize($entity = null, $options)
    {
        switch($options['page']) {
            case "password":
                $this->passwordFormElements($options);
                break;
            case "certificate":
                $this->certificateFormElements($options);
                break;
            case "recaptcha":
                $this->recaptchaFormElements($options);
                break;
            case "newadmin":
                $this->addNewAdminFormElements();
                break;
            default:
                break;
        }
    }

    private function passwordFormElements($options)
    {
        $alpha_chk = new Check('alpha_chk', array(
            'id' => 'alpha_chk',
            'name' => 'alpha_chk',
        ));
        $numeric_chk = new Check('numeric_chk', array(
            'id' => 'numeric_chk',
            'name' => 'numeric_chk',
        ));
        $special_chk = new Check('special_chk', array(
            'id' => 'special_chk',
            'name' => 'special_chk',
        ));
        $minimum_len = new Text('minimum_len', array(
            'id' => 'minimum_len',
            'name' => 'minimum_len',
            ));
        $maximum_len = new Text('maximum_len', array(
            'id' => 'maximum_len',
            'name' => 'maximum_len',
        ));

        if($options['data']['alpha'] == true)
            $alpha_chk->setAttribute("checked","checked");
        if($options['data']['numeric'] == true)
            $numeric_chk->setAttribute("checked","checked");
        if($options['data']['special'] == true)
            $special_chk->setAttribute("checked","checked");

        $minimum_len->setAttribute("value", $options['data']['minimum']);
        $maximum_len->setAttribute("value", $options['data']['maximum']);
        $this->add($alpha_chk);
        $this->add($numeric_chk);
        $this->add($special_chk);
        $this->add($minimum_len);
        $this->add($maximum_len);

    }

    private function certificateFormElements($options)
    {
        $hostname = new Text('hostname',array(
            'id' => 'hostname',
            'name' => 'hostname',
        ));
        $activate_certificate = new Check('activate_certificate', array(
            'id' => 'activate_certificate',
            'name' => 'activate_certificate',
        ));

        $crt = new File('crt', array(
            'id' => 'crt',
            'name' => 'crt',
        ));

        $pri_key = new File('pri_key', array(
            'id' => 'pri_key',
            'name' => 'pri_key',
        ));

        $ca_crt = new File('ca_crt', array(
            'id' => 'ca_crt',
            'name' => 'ca_crt',
        ));

        if($options['data']['activate'] == true)
            $activate_certificate->setAttribute("checked","checked");

        if(isset($options['data']['hostname'])) {
            if($options['data']['hostname'] != null)
                $hostname->setAttribute("value", $options['data']['hostname']);
        }

        $this->add($hostname);
        $this->add($activate_certificate);
        $this->add($pri_key);
        $this->add($crt);
        $this->add($ca_crt);
    }

    private function recaptchaFormElements($options)
    {
        $status = new Check('activate_recaptcha', array(
            'id' => 'activate_recaptcha',
            'name' => 'activate_recaptcha',
        ));

        $attempts = new Text('attempts', array(
            'id' => 'attempts',
            'name' => 'attempts',
        ));

        $site_key = new Text('site_key', array(
            'id' => 'site_key',
            'name' => 'site_key',
        ));
        $site_secret = new Text('site_secret', array(
            'id' => 'site_secret',
            'name' => 'site_secret',
        ));

        if($options['data']['status'] != "0")
            $status->setAttribute("checked","checked");

        $attempts->setAttribute("value", $options['data']['attempts']);
        $site_key->setAttribute("value", $options['data']['site_key']);
        $site_secret->setAttribute("value", $options['data']['site_secret']);

        $this->add($status);
        $this->add($attempts);
        $this->add($site_key);
        $this->add($site_secret);
    }

    private function addNewAdminFormElements()
    {
        $username = new Text('username', array(
            'id' => 'username',
            'name' => 'username',
        ));
        $password = new Password('password', array(
            'id' => 'password',
            'name' => 'password',
        ));
        $fullname = new Text('fullname', array(
            'id' => 'fullname',
            'name' => 'fullname',
        ));
        $email = new Text('email', array(
            'id' => 'email',
            'name' => 'email',
        ));
        $this->add($username);
        $this->add($password);
        $this->add($fullname);
        $this->add($email);
    }
}
