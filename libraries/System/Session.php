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

use FPanel\Environments\Datatable;
use FPanel\Environments\Parser;
use FPanel\Library\Library;
use FPanel\Models\SuccessLogins;
use Phalcon\Config\Adapter\Ini;

/**
 * Class Session
 * @package FPanel\Session
 */
class Session extends Library
{
    public $parent_id;
    public $user_id;
    public $user_type;


    /**
     * @return array
     */
    public function sessionList(): array
    {
        $current_session = $this->session->getId();
        $datatable = new Datatable();
        $delete_button = $datatable->buttons('session/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));

        $datatable->select(array('success_logins.id',
            'success_logins.user_id',
            'domains.id',
            'users.type',
            'users.username',
            'domains.name',
            'success_logins.ip_address',
            'success_logins.created_date',
            'success_logins.session'))
            ->from("success_logins")
            ->innerJoin('users','success_logins.user_id = users.id')
            ->leftJoin('domains','users.username= domains.name')
            ->addColumn('actions', $delete_button,'success_logins.id');

        $data = $datatable
            ->generate()
            ->execute()
            ->result();


        foreach($data['aaData'] as $key => $value) {
            if($current_session != $value[8]) {
                $data['aaData'][$key][0] = '<input type="checkbox" class="checkbox-blue" name="sessions[]" value="'. $value[0] . '" />';
            }
            else {
                $data['aaData'][$key][0] = "";
            }

            if($value[3] == DOMAIN_USER) {
                $data['aaData'][$key][1] = '<a href="'. $this->uri->url_encrypt('dashboard/domain/'. $value['2']).'">'.$value[5].'</a>';
            }
            else if (($value[3] > ADMIN_USER) && ($value[3] < DOMAIN_USER)) {
                $data['aaData'][$key][1] = '<a href="'. $this->uri->url_encrypt('dashboard/users/'. $value['1']).'">'.$value[4].'</a>';
            }
            else {
                $data['aaData'][$key][1] = $value['4'];
            }


            $data['aaData'][$key][2] = $value[6];
            $data['aaData'][$key][3] = $value[7];

            $data['aaData'][$key][4] = $this->date->secondsToTime((time()- $this->date->getTimeStamp($value[7])));

            if($current_session != $value[8]) {
                $data['aaData'][$key][5] = $data['aaData'][$key][9];
            }
            else {
                $data['aaData'][$key][5] = '<span class="label label-danger">'. __('Current Session') .'</span>';
            }

            unset($data['aaData'][$key][6]);
            unset($data['aaData'][$key][7]);
            unset($data['aaData'][$key][8]);
            unset($data['aaData'][$key][9]);
            unset($data['aaData'][$key][10]);
        }
        return $data;
    }


    /**
     * @param $id
     * @return bool
     */
    public function sessionDelete($id): bool
    {
        $sessions = SuccessLogins::findFirstById($id);
        if($sessions) {
            $session_id = $sessions->session;
            if(!$sessions->delete()) {
                foreach($sessions->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Session->sessionDelete",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            $this->removeSessionFile($session_id);
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param $session_id
     * @return bool
     */
    public function removeSessionFile($session_id): bool
    {
        if(file_exists('tmp/sess_'.$session_id)) {
            unlink("tmp/sess_".$session_id);
        }
        return true;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        $config = new Ini(ROOTPATH . "/config/config.ini");

        $allow = false;
        $block = false;
        $allowed_ip = "";
        $blocked_ip = "";
        if(isset($config->session->allow))
            $allow = $config->session->allow;
        if(isset($config->session->block))
            $block = $config->session->block;

        if(isset($config->session->allowed_ip))
            $allowed_ip = $config->session->allowed_ip;
        if(isset($config->session->blocked_ip))
            $blocked_ip = $config->session->blocked_ip;

        return array(
            'timeout' => $config->session->timeout,
            'allow_ip' => $allow,
            'block_ip' => $block,
            'allowed_ip' => $allowed_ip,
            'blocked_ip' => $blocked_ip,
        );
    }

    /**
     * @return bool
     */
    public function saveRules(): bool
    {
        if(!is_numeric($this->getPost("session_time")) || $this->getPost("session_time") < 600) {
            $this->error_messages['session_time'] = __('Please enter valid time. Session Time must be numeric and at least 600 seconds.');
            return false;
        }
        $allowed_ip_list = [];
        $blocked_ip_list = [];

        if($this->getPost("allow_ip_address") == ON) {
            if(!empty($this->getPost("allowed_ip_addresses"))) {
                $alloweds = $this->getPost("allowed_ip_addresses");
                foreach ($alloweds as $allowed_ip_address) {
                    if(filter_var($allowed_ip_address, FILTER_VALIDATE_IP)) {
                        $allowed_ip_list[] = $allowed_ip_address;
                    }
                    else {
                        $this->error_messages['allowed_ip_addresses'] = __('Please enter valid ip address');
                        return false;
                    }
                }
            }
            else {
                $this->error_messages['allowed_ip_addresses'] = __('Please enter min one ip address');
                return false;
            }
        }

        if($this->getPost("block_ip_address") == ON) {
            if(!empty($this->getPost("blocked_ip_addresses"))) {
                $blockeds = $this->getPost("blocked_ip_addresses");
                foreach ($blockeds as $blocked_ip_address) {
                    if(filter_var($blocked_ip_address, FILTER_VALIDATE_IP)) {
                        $blocked_ip_list[] = $blocked_ip_address;
                    }
                    else {
                        $this->error_messages['blocked_ip_addresses'] = __('Please enter valid ip address');
                        return false;
                    }
                }
            }
            else {
                $this->error_messages['blocked_ip_addresses'] = __('Please enter min one ip address');
                return false;
            }
        }

        $ini = new Parser(ROOTPATH.'/config/config.ini');

        $ini->data['session']['blocked_ip'] = $blocked_ip_list ?? [];
        $ini->data['session']['allowed_ip'] = $allowed_ip_list ?? [];
        $ini->data['session']['block'] = false;
        $ini->data['session']['allow'] = false;

        if($this->getPost("block_ip_address") == ON) {
            $ini->data['session']['block'] = true;
        }

        if($this->getPost("allow_ip_address") == ON) {
            $ini->data['session']['allow'] = true;
        }

        if($this->getPost("session_time") != null) {
            $ini->data['session']['timeout'] = $this->getPost("session_time");
        }

        $ini->write();

        return true;
    }

}