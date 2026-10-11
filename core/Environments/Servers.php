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
 */namespace FPanel\Environments;

trait Servers
{
    protected $asp;
    protected $aspnet;
    protected $servers  = array();
    protected $web_server;
    protected $database_server;

    /**
     * @param $params   = array('asp' => 'true|false','aspnet' => 'true|false');
     * @return Environments
     */
    public function domainSettings($params): Environments
    {
        if(isset($params['asp']) && ($params['asp'] == true))
            $this->asp = $params['asp'];
        else
            $this->asp = false;

        if(isset($params['aspnet']) && ($params['aspnet'] == true))
            $this->aspnet = $params['aspnet'];
        else
            $this->aspnet = false;

        return $this;
    }

    /**
     * @param int|null $os_type = "windows|linux|unix"
     * @return Environments
     */
    public function getServers(int $os_type = null): Environments
    {
        if(($this->aspnet == true) || ($this->asp == true)) {
            $os_type = WINDOWS;
        }
        else {
            if($os_type != WINDOWS)
                $os_type = LINUX;
        }

        $this->servers = $this->queryBuilder->select([
        "servers.id",
        "servers.`name`",
        "servers.os",
        "servers.type",
        "servers.`user`",
        "servers.`status`",
        "servers.domain_limit",
        "servers.database_limit",
        "servers.mail_limit",
        "GROUP_CONCAT(DISTINCT ip_address.ip_address,':',ip_address.id) as ip_addresses",
        "(SELECT COUNT(*) FROM domains WHERE domains.web_server_id = servers.id) as domain_count",
        "(SELECT COUNT(*) FROM domains WHERE `databases`.web_server_id = servers.id) as database_count",
        ])
            ->from("servers")
            ->innerJoin("ip_address", "servers.id = ip_address.server_id")
            ->leftJoin("domains", "servers.id = domains.web_server_id")
            ->leftJoin("`databases`", "servers.id = `databases`.server_id")
            ->where([
                'conditions' => "servers.os LIKE ?1",
                'bind' => [1 => "%$os_type%"]
            ])
            ->andWhere([
                'conditions' => "ip_address.type = ?1",
                'bind' => [1 => SHARED]
            ])
            ->groupBy([
                "servers.`name`"
            ])
            ->orderDesc("servers.created_date")
            ->find();

        return $this;
    }

    /**
     * @return Environments
     */
    public function selectWebServerRandom(): Environments
    {
        $servers = $this->servers;

        // Find All Used Domains
        $web_servers = array();
        foreach($servers as $value) {
            if($value['type'] == "web")
                $web_servers[] = $value['domain_count'];
        }

        $minimum_domain = min($web_servers);
        $has_minimum_domains = array();
        foreach($servers as $key => $value) {
            if($value['type'] == "web") {
                if($value['domain_count'] == $minimum_domain) {
                    $has_minimum_domains[$key] = $value;
                }
            }
        }

        $random_selected_server = array_rand($has_minimum_domains);

        $this->web_server = $servers[$random_selected_server];

        return $this;
    }

    /**
     * choose database servers
     * @return Environments
     */
    public function selectDatabaseServerRandom(): Environments
    {
        $servers = $this->servers;

        // Find All Databases
        $database_servers = array();
        foreach($servers as $value) {
            if($value['type'] == "database")
                $database_servers[] = $value['database_count'];
        }

        // @TODO: Db server sistemde kayıtlı değil bu yüzden sistem otomatik olarak eğer üzerinde kurulu bir db server varsa web sunucusundaki db serveri kullanacak
        if(empty($database_servers)) {
            $this->database_server = false;
            return $this;
        }

        $minimum_database = min($database_servers);
        $has_minimum_databases = array();
        foreach($servers as $key => $value) {
            if($value['type'] == "database") {
                if($value['database_count'] == $minimum_database) {
                    $has_minimum_databases[$key] = $value;
                }
            }
        }

        $random_selected_server = array_rand($has_minimum_databases);

        $this->database_server = $servers[$random_selected_server];

        return $this;

    }

    /**
     * listing minimum used servers
     * @param null $database    = different or same server
     * @return array
     */
    public function listSelected($database = null): array
    {
        $database_servers = $this->database_server;
        $selected_server = array(
            'web_server'    => $this->web_server,
        );

        if(($database_servers === false)) {
            $selected_server['database_server'] = $this->web_server;
        }
        else if(($database == "different") || ($database == null)) {
            $selected_server['database_server'] = $this->database_server;
        }

        return $selected_server;
    }

}