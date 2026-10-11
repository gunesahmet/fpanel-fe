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
 */namespace FPanel\Common;

use FPanel\Common\Migration\Migration;
use FPanel\Components\Components;
use FPanel\Environments\QueryBuilder;
use FPanel\Update\Initialize;
use Phalcon\Mvc\User\Component;

/**
 * Class Backend
 * @package FPanel\Backend
 */
class Base extends Component
{
    protected $action;
    protected $data;

    public function action($action): Base
    {
        $this->action = $action;
        return $this;
    }

    public function data($data): Base
    {
        $this->data = $data;
        return $this;
    }

    public function backupRestore()
    {
        $builder = new Builder();
        switch($this->action) {
            case "backup":
                $builder->backup($this->data);
                break;
            case "restore":
                $builder->restore($this->data);
                break;
            case "upload":
                $builder->uploadBackup($this->data);
                break;
        }
    }

    public function migration()
    {
        $builder = new Builder();
        $migration = new Migration();
        switch($this->action) {
            case "get-data":
                $result = $builder->getMigrationData();
                break;
            case "get-user":
                $result = $builder->getUserData($this->data);
                break;
            case "get-domain":
                $result = $builder->getDomainData($this->data);
                break;
            case "start-migration":
                $result = $builder->startMigration();
                break;
            case "finish-domain":
                $result = $migration->start()->domains();
                break;
            default:
                $result = false;
                break;
        }
        return $result;
    }

    public function getList()
    {
        $builder = new Builder();
        switch($this->action) {
            case "domain":
                $result = $builder->getDomainList($this->data);
                break;
            case "user":
                $result = $builder->getUserList($this->data);
                break;
            default:
                $result = false;
                break;
        }
        return $result;
    }

    public function getConfig()
    {
        $builder = new Builder();
        switch($this->action) {
            case "server":
                $result = $builder->getServerConfig($this->data);
                break;
            default:
                $result = false;
                break;
        }
        return $result;
    }

    public function statistics($data, $key, $iv): bool
    {
        $builder = new Builder();
        switch($this->action) {
            case "domain":
                $result = $builder->domainStatistics($data, $key, $iv);
                break;
            case "client":
                $result = $builder->clientStatistics();
                break;
            case "reseller":
                $result = $builder->resellerStatistics();
                break;
            case "server":
                $result = $builder->serverStatistics();
                break;
            default:
                return false;
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        return $result;
    }

    public function subdomain($data)
    {
        $builder = new Builder();
        switch($this->action) {
            case "create":
                $result = $builder->createSubdomain($data);
                break;
            default:
                $result = false;
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        return $result;
    }

    /**
     * @param $data
     * @return array|false|string
     */
    public function mail($data)
    {
        $builder = new Builder();
        switch($this->action) {
            case "create":
                $result = $builder->createMail($data);
                break;
            default:
                $result = false;
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        return $result;
    }

    /**
     * @param $data
     * @return array|false|string
     */
    public function database($data)
    {
        $builder = new Builder();
        switch($this->action) {
            case "create":
                $result = $builder->createDatabase($data);
                break;
            default:
                $result = false;
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        return $result;
    }

    /**
     * @param $data
     * @return array|false|string
     */
    public function mailAlias($data)
    {
        $builder = new Builder();
        switch($this->action) {
            case "create":
                $result = $builder->createMailAlias($data);
                break;
            default:
                $result = false;
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        return $result;
    }

    /**
     * @param $data
     * @return array|false|string
     */
    public function domainAlias($data)
    {
        $builder = new Builder();
        switch($this->action) {
            case "create":
                $result = $builder->createDomainAlias($data);
                break;
            default:
                $result = false;
        }
        if(REDIS_STATUS == true) {
            if($this->redis->exists('Users'))
                $this->redis->del("Users");
        }
        return $result;
    }

    public function update($data)
    {
        $update = new Initialize();
        switch($this->action) {
            case "start":
                $update->start();
                break;
            case "init":
                $update->init();
                break;
            case "package":
                $update->package($data);
                break;
            case "finish":
                $update->finish();
                break;
        }
    }

    public function reset()
    {
        $qbuilder = new QueryBuilder();
        $resellers = $qbuilder->select([
            "users.id",
        ])
            ->from('users')
            ->where([
                'conditions' => "users.id != ?1",
                'bind' => [1 => DOMAIN_USER]
            ])
            ->andWhere([
                'conditions' => "users.type != ?1",
                'bind' => [1 => ROOT_USER]
            ])
            ->andWhere([
                'conditions' => "users.type != ?1",
                'bind' => [1 => CLIENT_USER]
            ])
            ->find();

        $components = new Components();
        if(!empty($resellers)) {
            foreach($resellers as $reseller) {
                $components->userData(array(
                    'user_id' => "1",
                    'user_type' => ROOT_USER,
                ))->delete(USER,null, $reseller['id']);
            }
        }

        $clients = $qbuilder->select([
            "users.id",
        ])
            ->from('users')
            ->where([
                'conditions' => "users.id != ?1",
                'bind' => [1 => DOMAIN_USER]
            ])
            ->andWhere([
                'conditions' => "users.type != ?1",
                'bind' => [1 => ROOT_USER]
            ])
            ->andWhere([
                'conditions' => "users.type != ?1",
                'bind' => [1 => RESELLER_USER]
            ])
            ->find();

        if(!empty($clients)) {
            foreach($clients as $client) {
                $components->userData(array(
                    'user_id' => "1",
                    'user_type' => ROOT_USER,
                ))->delete(USER,null, $client['id']);
            }
        }

        $domains = $qbuilder->select([
            "domains.id",
        ])
            ->from('domains')
            ->find();

        if(!empty($domains)) {
            foreach($domains as $domain) {
                $components->userData(array(
                    'user_id' => "1",
                    'user_type' => ROOT_USER,
                ))->delete(DOMAIN, $domain['id']);
            }
        }

    }
}