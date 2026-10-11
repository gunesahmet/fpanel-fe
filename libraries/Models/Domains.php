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
 */namespace FPanel\Models;

use Phalcon\Mvc\Model;

/**
 * Class Domains
 * @package FPanel\Models
 *
 * @property DomainStatistics DomainStatistics
 * @method static DomainStatistics|Model\ResultsetInterface getDomainStatistics($params = null)
 *
 * @property Users Users
 * @method static Users|Model\ResultsetInterface getUsers($params = null)
 *
 * @property Databases Databases
 * @method static Databases|Model\ResultsetInterface getDatabases($params = null)
 *
 * @property Subdomains Subdomains
 * @method static Subdomains|Model\ResultsetInterface getSubdomains($params = null)
 *
 * @property Mails Mails
 * @method static Mails|Model\ResultsetInterface getMails($params = null)
 *
 * @property FtpUsers FtpUsers
 * @method static FtpUsers|Model\ResultsetInterface getFtpUsers($params = null)
 *
 * @property IpAddress IpAddress
 * @method static IpAddress|Model\ResultsetInterface getIpAddress($params = null)
 *
 * @property DnsZone DnsZone
 * @method static DnsZone|Model\ResultsetInterface getDnsZone($params = null)
 *
 * @property ProtectDirectory ProtectDirectory
 * @method static ProtectDirectory|Model\ResultsetInterface getProtectDirectory($params = null)
 *
 * @property Servers WebServers
 * @method static Servers|Model\ResultsetInterface getWebServers($params = null)
 *
 * @property Servers MailServers
 * @method static Servers|Model\ResultsetInterface getMailServers($params = null)
 *
 * @property Servers DnsServers
 * @method static Servers|Model\ResultsetInterface getDnsServers($params = null)
 *
 * @method static Domains|Model\ResultsetInterface findFirst($params = null)
 * @method static Domains|Model\ResultsetInterface findFirstById($params = null)
 * @method static Domains[]|Model\ResultsetInterface find($params = null)
 */
class Domains extends FModel
{
    /**
     * @Primary
     * @var string
     */
    public $id;

    public $name;

    public $client_id;

    public $ssl_cert_id;

    public $ftp_user_id;

    public $ipaddr_id;

    public $status;

    public $status_msg;

    public $created_date;

    public $expire_date;

    public $updated_date;

    public $web_server_id;

    public $dns_server_id;

    public $mail_server_id;

    public $params;

    public $prefix;

	public function initialize()
	{
		$this->setSource('domains');
        $this->belongsTo('id', 'FPanel\Models\DomainStatistics', 'domain_id', ['alias' => 'DomainStatistics']);
        $this->belongsTo('ftp_user_id', 'FPanel\Models\FtpUsers', 'id', ['alias' => 'FtpUsers']);
        $this->belongsTo('name', 'FPanel\Models\Users', 'username', ['alias' => 'Users']);
        $this->hasMany('id', 'FPanel\Models\Databases', 'domain_id', ['alias' => 'Databases']);
        $this->hasMany('id', 'FPanel\Models\Subdomains', 'domain_id', ['alias' => 'Subdomains']);
        $this->hasMany('id', 'FPanel\Models\Mails', 'domain_id', ['alias' => 'Mails']);
        $this->hasMany('id', 'FPanel\Models\ProtectDirectory', 'domain_id', ['alias' => 'ProtectDirectory']);
        $this->belongsTo('ipaddr_id', 'FPanel\Models\IpAddress', 'id', ['alias' => 'IpAddress']);
        $this->belongsTo('id', 'FPanel\Models\DnsZone', 'domain_id', ['alias' => 'DnsZone']);
        $this->belongsTo('web_server_id', 'FPanel\Models\Servers', 'id', ['alias' => 'WebServers']);
        $this->belongsTo('mail_server_id', 'FPanel\Models\Servers', 'id', ['alias' => 'MailServers']);
        $this->belongsTo('dns_server_id', 'FPanel\Models\Servers', 'id', ['alias' => 'DnsServers']);
    }
}
