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

use FPanel\Backup\Backup;
use FPanel\Components\Base;
use FPanel\Models\Domains;
use FPanel\Models\Users;
use FPanel\Server\Ipaddress;
use FPanel\System\LimitsPermission;
use FPanel\System\Serviceplans;
use FPanel\System\Statistics;
use FPanel\Users\Usersdata;

/**
 * Class Delete
 * @package FPanel\Components\Delete
 */
class Delete extends Base
{
    protected $client_data = [];

    public function getUserData($client_id): Delete
    {
        $client = Users::findFirstById($client_id);
        if($client) {
            $client_array = $client->toArray();
            $client_data = array(
                'client_id' => $client_array['id'],
                'client_type' => $client_array['type'],
                'parent_id' => $client_array['parent'],
                'childs' => array(),
            );
            if($client_data['client_type'] == RESELLER_USER) {
                $child_users = array();
                $childs = Users::find("parent = '$client_id'");
                if($childs) {
                    foreach($childs->toArray() as $child) {

                        if($child['type'] != DOMAIN_USER) {
                            $child_users[] = array('child_id' => $child['id'],'child_type' => $child['type']);
                        }
                    }
                }
                $client_data['childs'] = $child_users;
            }
            $this->client_data = $client_data;
        }

        return $this;
    }

    public function deleteUser()
    {
        if($this->client_data['client_type'] == RESELLER_USER) {
            if(!empty($this->client_data['childs'])) {
                foreach($this->client_data['childs'] as $child) {
                    $this->deleteClient($child['child_id'], $this->client_data['client_id']);
                }
            }

            $this->deleteReseller($this->client_data['client_id'], $this->client_data['parent_id']);
        }
        else {
            $this->deleteClient($this->client_data['client_id'], $this->client_data['parent_id']);
        }
    }

    private function deleteClient($client_id, $parent_id)
    {
        $user = new Usersdata();
        $limit_permissions = new LimitsPermission();
        $stats = new Statistics();
        $ip = new Ipaddress();
        $serviceplans = new Serviceplans();
        $backups = new Backup();

        $backups->deleteAll(USER, $client_id);
        $backups->deleteAllStorage($client_id);
        $this->deleteClientsDomains($client_id);
        $serviceplans->deleteClient($client_id);
        $ip->delete($client_id);
        $stats->deleteClientStatistic($client_id);
        $limit_permissions->delete($client_id);
        $user->delete($client_id,CLIENT_USER, $parent_id);
    }

    private function deleteReseller($reseller_id, $parent_id)
    {
        $user = new Usersdata();
        $limit_permissions = new LimitsPermission();
        $stats = new Statistics();
        $ip = new Ipaddress();
        $serviceplans = new Serviceplans();
        $backups = new Backup();


        $backups->deleteAll(USER, $reseller_id);
        $backups->deleteAllStorage($reseller_id);
        $serviceplans->deleteClient($reseller_id);
        $this->deleteResellerDomains($reseller_id);
        $ip->delete($reseller_id);
        $stats->deleteClientStatistic($reseller_id);
        $limit_permissions->delete($reseller_id);
        $user->delete($reseller_id,RESELLER_USER, $parent_id);
    }

    private function deleteClientsDomains($client_id)
    {
        $domains = Domains::find("client_id = '$client_id'");
        if($domains) {
            foreach($domains as $domain) {
                $domain_component = new \FPanel\Components\Domain\Delete();
                $domain_component
                    ->setParent($client_id, CLIENT_USER)
                    ->getDomain($domain->id)
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
            }
        }
    }

    private function deleteResellerDomains($reseller_id)
    {
        $domains = Domains::find("client_id = '$reseller_id'");
        if($domains) {
            foreach($domains as $domain) {
                $domain_component = new \FPanel\Components\Domain\Delete();
                $domain_component
                    ->setParent($reseller_id, RESELLER_USER)
                    ->getDomain($domain->id)
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
            }
        }

    }
}