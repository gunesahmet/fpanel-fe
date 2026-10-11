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

use FPanel\Components\Domain\Create as DomainCreate;
use FPanel\Components\Domain\Delete as DomainDelete;
use FPanel\Components\Domain\Update as DomainUpdate;
use FPanel\Components\User\Create as UserCreate;
use FPanel\Components\User\Delete as UserDelete;
use FPanel\Components\User\Update as UserUpdate;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\Domains;
use FPanel\Models\DomainStatistics;
use FPanel\Models\Users;

/**
 * Class Builder
 * @package FPanel\Components
 */
class Builder extends Components
{
    private $user_data;

    /**
     * @var array
     */
    private $error_messages = [];


    public function data($user_data = array()): Builder
    {
        $this->user_data = $user_data;
        return $this;
    }

    public function createDomain($post_data = array(), $options = array(), $restore = false, $api = false): bool
    {
        $this->db->begin();
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            $domain = new DomainCreate();

            $domain->setPost($post_data)
                ->setOptions($options)
                ->setParent($this->user_data["user_id"], $this->user_data["user_type"]);

            if($api || $restore) {
                if(!$domain->validationForApi()) {
                    $this->error_messages = $domain->getMessages();
                    return false;
                }
            }
            else {
                if(!$domain->validation()) {
                    $this->error_messages = $domain->getMessages();
                    return false;
                }
            }

            $domain_status = $domain
                ->isCloud($api)
                ->date()
                ->getIpAddress()
                ->domain()
                ->saveUser()
                ->saveLimitsPermissions($restore)
                ->saveStatistics()
                ->saveDnsZone()
                ->createDnsRecords()
                ->saveServices()
                ->saveMailSettings()
                ->result();

            if(!$domain_status) {
                $this->db->rollback();
                $this->error_messages = $domain->getMessages();
                return false;
            }
            else {
                $response = $domain->createCommand();
                if($response === true) {
                    $this->db->commit();
                    return true;
                }
                else {
                    $result = "";
                    foreach($domain->getMessages() as $field => $msg) {
                        $this->error_messages[$field] = $msg;
                        $msg = trim($msg);
                        if (strpos($msg, 'Err') !== false) {
                            $result = empty($result) ? $msg : $result . " " . $msg;
                        }
                    }

                    $domains = Domains::findFirst("name = '$post_data[name]'");
                    $domains->status = DOMAIN_CREATE_FAILED;
                    $domains->status_msg = $result;
                    if (!$domains->update()) {
                        foreach ($domains->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->db->rollback();
                        return false;
                    }
                    $users = Users::findFirst("username = '$post_data[name]'");
                    $users->status = DOMAIN_CREATE_FAILED;
                    if (!$users->update()) {
                        foreach ($users->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->db->rollback();
                        return false;
                    }
                    $statistics = DomainStatistics::findFirst("domain_name = '$post_data[name]'");
                    $statistics->status = DOMAIN_CREATE_FAILED;
                    if (!$statistics->update()) {
                        foreach ($statistics->getMessages() as $message) {
                            $this->error_messages[$message->getField()] = $message->getMessage();
                        }
                        $this->db->rollback();
                        return false;
                    }
                    $this->error_messages["result"] = $result;
                    $this->db->rollback();
                    return false;
                }
            }
        }
        catch (\Exception $e) {
            $this->db->rollback();
            $this->error_messages[] = $e->getMessage();
            return false;
        }
    }

    public function updateDomain($post_data, $options, $api = false): bool
    {
        $this->db->begin();
        try {
            if(REDIS_STATUS) {
                if($this->redis->exists('Users'))
                    $this->redis->del("Users");
            }
            $domains = Domains::findFirst("name = '$post_data[domain_name]'");
            $domain = new DomainUpdate();
            if($api) {
                $domain_validation_for_api = $domain->setPost($post_data)
                    ->setOptions($options)
                    ->setParent($this->user_data["user_id"], $this->user_data["user_type"])
                    ->validationForApi();
                if(!$domain_validation_for_api) {
                    $this->error_messages = $domain->getMessages();
                    $this->db->rollback();
                    return false;
                }
            }
            else {
                $domain_validation = $domain->setPost($post_data)
                    ->setOptions($options)
                    ->setParent($this->user_data["user_id"], $this->user_data["user_type"])
                    ->validation();
                if(!$domain_validation) {
                    $this->error_messages = $domain->getMessages();
                    $this->db->rollback();
                    return false;
                }
            }

            $status = $domain
                ->setDomainId($domains->id)
                ->updateStatistics()
                ->updateLimitsPermissions($domains->getUsers()->id);
            if(!$status) {
                $this->error_messages = $domain->getMessages();
                return false;
            }
            $this->db->commit();
        }
        catch (\Exception $e) {
            $this->db->rollback();
            $this->error_messages[] = $e->getMessage();
            return false;
        }
        return true;
    }

    public function deleteDomain($domain_id, $roll_back = false): bool
    {
        $domain = new DomainDelete();
        $domain
            ->getDomain($domain_id)
            ->setParent($this->user_data["user_id"], $this->user_data["user_type"]);
        if(!$roll_back) {
            $domain_validation = $domain->validation();
            if(!$domain_validation) {
                $this->error_messages = $domain->getMessages();
                return false;
            }
        }

        $domain
            ->deleteDns()
            ->deleteDatabases()
            ->deleteBackups()
            ->deleteStorages()
            ->deleteDomainAlias()
            ->deleteSSL()
            ->deleteProDir()
            ->deleteTasks()
            ->deleteVirtualDirectory()
            ->deleteSubdomains()
            ->deleteMail()
            ->deleteLimitsPerm()
            ->deleteStatistics()
            ->deleteServices()
            ->deleteCommand(true)
            ->deleteClient()
            ->deleteDomain();

        try {
            if(REDIS_STATUS) {
                if($this->redis->hExists("Users", $this->user_data['user_id'])) {
                    $this->redis->hDel("Users", $this->user_data['user_id']);
                }
            }
        }
        catch (\Exception $e) {
            $this->error_messages[] = $e->getMessage();
            return false;
        }
        return true;
    }

    public function suspendDomainStatus($domain_id, $parent_id, $message = null, $status = PASSIVE): bool
    {
        $domain = new DomainUpdate();
        return $domain->suspend($domain_id, $parent_id, $message, $status);
    }

    public function unsuspendDomainStatus($domain_id, $parent_id, $message = null): bool
    {
        $domain = new DomainUpdate();
        return $domain->unsuspend($domain_id, $parent_id, $message);
    }


    /******* USERS *******/
    public function createUser($post_data, $options, $restore = false, $api = false): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }

        $user = new UserCreate();
        $user->setPost($post_data)
            ->setOptions($options)
            ->setParent($this->user_data["user_id"], $this->user_data["user_type"]);

        if(!$restore && !$api){
            if(!$user->validation()) {
                $this->error_messages = $user->getMessages();
                return false;
            }
        }

        if($api) {
            if(!$user->validationForApi()) {
                $this->error_messages = $user->getMessages();
                return false;
            }
        }

        $user->date()
            ->saveUser($restore)
            ->saveIpAddresses()
            ->saveLimitsPermissions($restore)
            ->saveStatistics();
        if(!empty($user->getMessages())) {
            $this->error_messages = $user->getMessages();
            return false;
        }
        return true;
    }

    public function updateUser($post_data, $options, $client_id, $api = false): bool
    {
        if(REDIS_STATUS) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        $user = new UserUpdate();
        $user->setPost($post_data)
            ->setOptions($options)
            ->setUser($this->user_data["user_id"], $this->user_data["user_type"])
            ->setClient($client_id);

        if($api) {
            if(!$user->validationForApi()) {
                $this->error_messages = $user->getMessages();
                return false;
            }
        }
        else {
            if(!$user->validation()) {
                $this->error_messages = $user->getMessages();
                return false;
            }
        }

        $user->updateUser()
            ->updateIpAddress()
            ->updateLimitsPermissions()
            ->updateStatistics();
        if(!empty($user->getMessages())) {
            $this->error_messages = $user->getMessages();
            return false;
        }
        return true;
    }

    public function deleteUser($client_id): bool
    {
        $user = new UserDelete();
        $user->getUserData($client_id)
            ->deleteUser();
        if(REDIS_STATUS) {
            $this->redis->del("Users");
        }

        return true;
    }

    public function suspendUserStatus($client_id, $parent_id, $message = null): bool
    {
        $user = new UserUpdate();
        return $user->suspend($client_id, $parent_id, $message);
    }

    public function unsuspendUserStatus($client_id, $parent_id, $message = null): bool
    {
        $user = new UserUpdate();
        return $user->unsuspend($client_id, $parent_id, $message);
    }

    public function getUserData($client_id)
    {
        $qbuilder = new QueryBuilder();
        $result = $qbuilder->select([
            "user.username",
            "user.type,limits.*",
            "userdata.email",
            "userdata.fullname",
            "userdata.company",
            "userdata.fax",
            "userdata.telephone",
            "userdata.address",
            "userdata.city",
            "userdata.region",
            "userdata.zipcode",
            "user.last_login",
            "user.last_login_ip",
            "userdata.country",
            "client_statistics.disk",
            "client_statistics.traffic",
            "(SELECT COUNT(*) FROM domains WHERE domains.client_id = user.id) + (SELECT COUNT(*) FROM users INNER JOIN domains ON users.id = domains.client_id WHERE users.parent = user.id) AS domain_count",
            "(SELECT COUNT(*) FROM users WHERE users.parent = user.id AND users.type < 4) AS client_count",
            "(SELECT GROUP_CONCAT(ip_address.id,\":\", ip_address.ip_address) FROM users INNER JOIN client_ip_address ON users.id = client_ip_address.client_id INNER JOIN ip_address ON client_ip_address.ipaddr_id = ip_address.id  WHERE users.id = user.id) as UserIpAddresses",
        ])
            ->from(DATABASE_NAME . '.users as user')
            ->innerJoin(DATABASE_NAME . '.userdata as userdata','userdata.user_id = user.id')
            ->innerJoin(DATABASE_NAME . '.client_limits_permissions as limits','limits.client_id = user.id')
            ->innerJoin('client_statistics','client_statistics.client_id = user.id')
            ->where([
                'conditions' => "user.id = ?1",
                'bind' => [1 => $client_id]
            ])
            ->andWhere([
                'conditions' => "(user.type > ?1 AND user.type < ?2)",
                'bind' => [
                    1 => ADMIN_USER,
                    2 => DOMAIN_USER,
                ]
            ])
            ->findFirst();


        if(!empty($result)) {
            unset($result['id']);
            unset($result['domain_id']);
            return $result;
        }
        else {
            return false;
        }
    }

    public function getMessages(): array
    {
        return $this->error_messages;
    }
}