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
 */namespace FPanel\Components;

use Phalcon\Mvc\User\Component;

/**
 * Class Components
 * @package FPanel\Components
 */
class Components extends Component
{
    private $user_id;
    private $user_type;
    private $error_messages = array();
    private $restore = false;
    private $api = false;

    private $option_data;
    private $post_data;

    public function options($options = array()): Components
    {
        $this->option_data = $options;
        return $this;
    }

    public function post($post_data = array(), $restore = false, $api = false): Components
    {
        $this->restore = $restore;
        $this->api = $api;
        $this->post_data = $post_data;
        return $this;
    }

    public function userData($user_data = array()): Components
    {
        $this->user_id = $user_data['user_id'];
        $this->user_type = $user_data['user_type'];

        return $this;
    }

    public function create($type): bool
    {
        $builder = new Builder();

        switch($type) {
            case DOMAIN:
                $status = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                    ->createDomain($this->post_data, $this->option_data, $this->restore, $this->api);
                break;
            case USER:
                $status = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                    ->createUser($this->post_data, $this->option_data, $this->restore, $this->api);
                break;
            default:
                return false;
        }
        if(!$status) {
            $this->error_messages = $builder->getMessages();
            return false;
        }
        else
            return true;
    }

    public function delete($type, $domain_id = null, $client_id = null): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        $builder = new Builder();
        switch($type) {
            case DOMAIN:
                $status = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                    ->deleteDomain($domain_id);
                break;
            case USER:
                $status = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                    ->deleteUser($client_id);
                break;
            default:
                return false;
        }

        return $status;
    }

    public function update($type, $id): bool
    {
        $builder = new Builder();

        switch($type) {
            case DOMAIN:
                $status = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                    ->updateDomain($this->post_data, $this->option_data, $this->api);
                $this->error_messages = $builder->getMessages();
                break;
            case USER:
                $status = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                    ->updateUser($this->post_data, $this->option_data, $id, $this->api);
                $this->error_messages = $builder->getMessages();
                break;
            default:
                return false;
        }
        return $status;

    }

    public function getData($id)
    {
        $builder = new Builder();
        return $builder->data(array(
            'user_id' => $this->user_id,
            'user_type' => $this->user_type,
        ))
            ->getUserData($id);
    }

    public function suspend($type, $id, $message = null, $status = PASSIVE): bool
    {
        $builder = new Builder();

        switch($type) {
            case "domain":
                $result = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                ->suspendDomainStatus($id, $this->user_id, $message, $status);

                break;
            case "user":
                $result = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                ->suspendUserStatus($id, $this->user_id, $message);
                break;
            default:
                return false;
        }
        return $result;
    }

    public function unsuspend($type, $id, $message = null): bool
    {
        $builder = new Builder();

        switch($type) {
            case "domain":
                $result = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                ->unsuspendDomainStatus($id, $this->user_id, $message);

                break;
            case "user":
                $result = $builder->data(array(
                    'user_id' => $this->user_id,
                    'user_type' => $this->user_type,
                ))
                ->unsuspendUserStatus($id, $this->user_id, $message);
                break;
            default:
                return false;
        }
        return $result;
    }

    public function getMessages(): array
    {
        return $this->error_messages;
    }
}