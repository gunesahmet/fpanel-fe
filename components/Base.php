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

use FPanel\Environments\Environments;
use FPanel\System\SysCore;
use Phalcon\Mvc\User\Component;

/**
 * Class Create
 * @package FPanel\Components\Create
 */
abstract class Base extends Component
{
    protected $error_messages = [];

    protected $post = [];
    protected $options = [];

    protected $parent_id;
    protected $parent_type;

    protected $user_id;
    protected $user_type;

    protected $system_type;

    protected $dates;

    public function __construct()
    {
        // Maalesef her islem icin system type'i almaliyiz
        $sys_core = new SysCore();
        $system_data = $sys_core->getSystemData();
        $this->system_type = $system_data["system_type"];
    }

    public function date(): Base
    {
        if(!empty($this->error_messages)) {
            return $this;
        }

        if(isset($this->post['expire_time_limit'])) {
            $create_date = new Environments();
            $this->dates = $create_date->createDates(time(), $this->post['expire_time_limit']);
        }
        else {
            $this->error_messages["expire_time_limit"] = __("Please define an expire date");
        }

        return $this;
    }

    public function setPost($post_data): Base
    {
        $this->post = $post_data;
        return $this;
    }

    public function setOptions($options): Base
    {
        $this->options = $options;
        return $this;
    }

    public function setParent($parent_user_id, $parent_user_type): Base
    {
        $this->parent_id = $parent_user_id;
        $this->parent_type = $parent_user_type;
        return $this;
    }

    public function setUser($user_id, $user_type): Base
    {
        $this->user_id = $user_id;
        $this->user_type = $user_type;
        return $this;
    }

    public function setClient($client_id): Base
    {
        $this->user_id = $client_id;
        return $this;
    }

    public function getMessages(): array
    {
        return $this->error_messages;
    }
}