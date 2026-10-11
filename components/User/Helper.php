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
 */namespace FPanel\Components\User;

/**
 * Class Helper
 * @package FPanel\Components\User
 */
Trait Helper
{
    public function validator($update = false, $old_username = null): bool
    {
        $required_fields = array(
            array(
                'field' => 'fullname',
                'item' => $this->post['fullname'],
                'message' =>  __('Fullname is required')
            ),
            array(
                'field' => 'username',
                'item' => $this->post['username'],
                'message' =>  __('Username is required')
            ),
            array(
                'field' => 'email_addr',
                'item' => $this->post['email'],
                'message' =>  __('Email is required')
            ),
            array(
                'field' => 'ipaddress_list',
                'item' => $this->post['ipaddr_id'],
                'message' =>  __('Please select an ip address')
            )
        );
        if($update == false) {
            $required_fields[] = array(
                'field' => 'password',
                'item' => $this->post['password'],
                'message' =>  __('Password is required')
            );
            $required_fields[] = array(
                'field' => 'confirm-password',
                'item' => $this->post['confirm-password'],
                'message' =>  __('Password confirmation is required')
            );
            $required_fields[] =  array(
                'field' => 'type_of_user',
                'item' => $this->post['type'],
                'message' =>  __('Please select an user type')
            );
        }


        $this->validation
            ->Required($required_fields)
            ->Confirm($this->post['password'],"password", $this->post['confirm-password'])
            ->Username($this->post['username'],'username')
            ->Email($this->post['email'],'email_addr')
            ->InclusionInArray($this->post['ipaddr_id'],'ipaddress_list', array_keys($this->options['ip_address_list']))
            ->Permissions($this->post)
            ->DomainSettings($this->post)
            ->Limits($this->post)
            ->UniqueUser($this->post['username'],'username',null, $update, $old_username);

        if($update == false)
            $this->validation->Password($this->post['password'],'password',null, $update);
        else {
            if($this->post['password'] != null)
                $this->validation->Password($this->post['password'],'password',null, $update);
        }

        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }

    public function apiValidator($update = false, $old_username = null): bool
    {
        $required_fields = array(
            array(
                'field' => 'client_name',
                'item' => $this->post['username'],
                'message' =>  __('Username is required.')
            ),
            array(
                'field' => 'client_email',
                'item' => $this->post['email'],
                'message' =>  __('Email is required.')
            ),
            array(
                'field' => 'ip_address',
                'item' => $this->post['ipaddr_id'],
                'message' =>  __('Please select an ip address')
            )
        );
        if($update == false) {
            $required_fields[] = array(
                'field' => 'client_password',
                'item' => $this->post['password'],
                'message' =>  __('Password is required')
            );
            $required_fields[] =  array(
                'field' => 'client_type',
                'item' => $this->post['type'],
                'message' =>  __('Please select an user type')
            );
        }


        $this->validation
            ->Required($required_fields)
            ->Username($this->post['username'],'username')
            ->Password($this->post['password'],'password',null, $update)
            ->Email($this->post['email'],'email_addr')
            ->UniqueUser($this->post['username'],'username',null, $update, $old_username);


        if($this->validation->Validate() == false) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}
