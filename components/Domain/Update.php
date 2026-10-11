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
 */namespace FPanel\Components\Domain;


use FPanel\Components\Base;
use FPanel\Domain\DomainSettings;
use FPanel\Exceptions\Exception;
use FPanel\System\LimitsPermission;
use FPanel\System\Statistics;

/**
 * Class Update
 * @package FPanel\Components\Update
 */
class Update extends Base
{
    use Helper;

    protected $domain_id;

    public function validation(): bool
    {
        if(!$this->validator()) {
            $this->error_messages = $this->getMessages();
            return false;
        }
        return true;
    }

    public function validationForApi(): bool
    {
        if(!$this->apiValidator(true)) {
            $this->error_messages =  $this->getMessages();
            return false;
        }
        return true;
    }

    public function setDomainId($domain_id): Update
    {
        $this->domain_id = $domain_id;
        return $this;
    }

    public function updateStatistics(): Update
    {
        try {
            $save_domain_statistics = new Statistics();
            $save_domain_statistics->setPosts($this->post);
            $domain_statistics_status = $save_domain_statistics->updateDomainStatistic($this->domain_id, []);
            if (!$domain_statistics_status) {
                foreach ($save_domain_statistics->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return $this;
            }
        }
        catch (\Exception $e) {
            $this->log->error([
                "Module" => "components->Domain->Update->updateStatistics",
                "data" => $this->post,
                "errors" => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
        return $this;
    }

    public function updateLimitsPermissions($domain_user_id): bool
    {
        if(!empty($this->error_messages)) {
            return false;
        }
        try {
            $save_limits = new LimitsPermission();
            $save_limits->setPosts($this->post);
            $save_limits->user_id = $domain_user_id;
            $save_limits->domain_id = $this->domain_id;
            $status = $save_limits->update($domain_user_id);

            if (!$status) {
                foreach ($save_limits->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                return false;
            }
        }
        catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
        return true;
    }

    public function suspend($domain_id, $parent_id, $message = null, $status = PASSIVE): bool
    {
        try {
            $domain_settings = new DomainSettings();
            $result = $domain_settings->suspend($domain_id, $parent_id, $message, $status);

            if($result) {
                $statistics = new Statistics();
                $statistics->suspend(DOMAIN, $domain_id, $status);
                return true;
            }
        }
        catch (\Exception $e) {
            $this->error_messages[] = $e->getMessage();
        }
        return false;

    }

    public function unsuspend($domain_id, $parent_id, $message = null): bool
    {
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            $domain_settings = new DomainSettings();
            $result = $domain_settings->unsuspend($domain_id, $parent_id, $message);
            if($result) {
                $statistics = new Statistics();
                $statistics->unsuspend(DOMAIN, $domain_id);
                return true;
            }
        }
        catch (\Exception $e) {
            $this->error_messages[] = $e->getMessage();
        }
        return false;
    }
}