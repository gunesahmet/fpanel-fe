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
 */namespace FPanel\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class SubdomainForm
 * @package FPanel\Forms
 */
class SubdomainForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
	{
        $this->requiredSubDomainFormElements($options);
	}

    /**
     * @param $options
     */
	private function requiredSubDomainFormElements($options)
	{

        $action = $options['action'];
        $subdomain_domain_data = $options['domain_data'];

        // PHP
        $php_support = new Check('php_support', array(
            'name' => 'php_support',
            'id' => 'php_support',
        ));
        $php_version = new Select("php_version",array(
                PHP56 => 'PHP 5.6',
                PHP72 => 'PHP 7.2',
                PHP74 => 'PHP 7.4',
            ),array(
                'id' => 'php_version',
            ));



        $status = new Check('status', array(
            'name' => 'status',
            'id' => 'status',
        ));

        // ASP.NET
        $asp_net_support = new Check('asp_net_support', array(
            'name' => 'asp_net_support',
            'id' => 'asp_net_support',
        ));
        $asp_net_version = new Select("asp_net_version",array(
            v20 => '2.0',
            v40 => '4.0',
        ),array(
            'id' => 'asp_net_version',
        ));

        // ASP.NET CORE
        $asp_net_core_support = new Check('asp_net_core_support', array(
            'name' => 'asp_net_core_support',
            'id' => 'asp_net_core_support',
        ));


        // ASP
        $asp_support = new Check('asp_support', array(
            'name' => 'asp_support',
            'id' => 'asp_support',
        ));


        // Perl
        $perl_support = new Check('perl_support', array(
            'name' => 'perl_support',
            'id' => 'perl_support',
        ));


        // CGI
        $cgi_support = new Check('cgi_support', array(
            'name' => 'cgi_support',
            'id' => 'cgi_support',
        ));


        // Python
        $python_support = new Check('python_support', array(
            'name' => 'python_support',
            'id' => 'python_support',
        ));



        // Dedicated pool
        $dedicated_pool = new Check('dedicated_pool', array(
            'name' => 'dedicated_pool',
            'id' => 'dedicated_pool',
        ));

        // Max Cpu Usage
        $max_cpu_usage = new Text('max_cpu_usage', array(
            'name' => 'max_cpu_usage',
            'id' => 'max_cpu_usage',
        ));

        // Subdomain Name
        $subdomain_name = new Text('subdomain_name', array(
            'name' => 'subdomain_name',
            'id' => 'subdomain_name',
        ));

        // Native FTP
        $use_native_ftp = new Check('use_native_ftp', array(
            'name' => 'use_native_ftp',
            'id' => 'use_native_ftp',
        ));

        // Sub FTP User Name
        $sub_ftp_user = new Text('username', array(
            'name' => 'username',
            'id' => 'username',
        ));

        // Sub FTP Password
        $sub_password = new Password('password', array(
            'name' => 'password',
            'id' => 'password',
        ));

        $domain_php = $subdomain_domain_data['domain_php'];
        $domain_aspnet = $subdomain_domain_data['domain_aspnet'];
        $domain_aspnet_core = $subdomain_domain_data['domain_aspnet_core'];
        $domain_asp = $subdomain_domain_data['domain_asp'];
        $domain_perl = $subdomain_domain_data['domain_perl'];
        $domain_cgi = $subdomain_domain_data['domain_cgi'];
        $domain_python = $subdomain_domain_data['domain_python'];

        if($domain_aspnet == PASSIVE) {
            $asp_net_support->setAttribute('disabled','disabled');
        }
        if($domain_php == PASSIVE) {
            $php_support->setAttribute('disabled','disabled');
        }
        if($domain_aspnet_core == PASSIVE) {
            $asp_net_core_support->setAttribute('disabled','disabled');
        }
        if($domain_asp == PASSIVE) {
            $asp_support->setAttribute('disabled','disabled');
        }
        if($domain_perl == PASSIVE) {
            $perl_support->setAttribute('disabled','disabled');
        }
        if($domain_cgi == PASSIVE) {
            $cgi_support->setAttribute('disabled','disabled');
        }
        if($domain_python == PASSIVE) {
            $python_support->setAttribute('disabled','disabled');
        }

        if($action == "edit") {
            $subdomain_data = $options["subdomain_data"];
            if($subdomain_data['ftpuser_type'] != MAIN) {
                $sub_ftp_user->setAttribute('value', $subdomain_data['ftpname']);

                $use_native_ftp->setAttribute('checked','checked');
            }
            else {
                $sub_ftp_user->setAttribute('disabled','disabled');
                $sub_password->setAttribute('disabled','disabled');
            }
            $subdomain_name->setAttribute('value', $subdomain_data['subdomain_name']);
            $subdomain_name->setAttribute('disabled','disabled');
            if($subdomain_data['php'] == ACTIVE) {
                $php_support->setAttribute('checked','checked');


                $php_version->setDefault($subdomain_data['php_version']);
            }
            else {
                $php_version->setAttribute('disabled','disabled');
            }

            if($subdomain_data['aspnet_core'] == ACTIVE) {
                $asp_net_core_support->setAttribute('checked','checked');
            }
            else {
                $asp_net_core_support->setAttribute('disabled','disabled');
            }

            if($subdomain_data['aspnet'] == ACTIVE) {
                $asp_net_support->setAttribute('checked','checked');
                $asp_net_version->setDefault($subdomain_data['aspnet_version']);
            }
            else {
                $asp_net_version->setAttribute('disabled','disabled');
            }

            if($subdomain_data['asp'] == ACTIVE) {
                $asp_support->setAttribute('checked','checked');
            }

            if($subdomain_data['python'] == ACTIVE) {
                $python_support->setAttribute('checked','checked');
            }

            if($subdomain_data['cgi'] == ACTIVE) {
                $cgi_support->setAttribute('checked','checked');
            }

            if($subdomain_data['perl'] == ACTIVE) {
                $perl_support->setAttribute('checked','checked');
            }

            if($subdomain_data['dedicated_pool'] == ACTIVE) {
                $dedicated_pool->setAttribute('checked','checked');
                $max_cpu_usage->setAttribute('value', $subdomain_data['max_cpu']);
            }
            else {
                $max_cpu_usage->setAttribute('disabled','disabled');
            }

            if($subdomain_data["status"] == ACTIVE) {
                $status->setAttribute("checked", "checked");
            }
        }
        else {
            $status->setAttribute('checked','checked');
            $sub_ftp_user->setAttribute('disabled','disabled');
            $sub_password->setAttribute('disabled','disabled');
            $php_version->setAttribute('disabled','disabled');
            $asp_net_version->setAttribute('disabled','disabled');
            $max_cpu_usage->setAttribute('disabled','disabled');

        }

        $this->add($sub_ftp_user);
        $this->add($sub_password);
        $this->add($use_native_ftp);
        $this->add($subdomain_name);
        $this->add($max_cpu_usage);
        $this->add($dedicated_pool);
        $this->add($python_support);
        $this->add($cgi_support);
        $this->add($perl_support);
        $this->add($asp_support);
        $this->add($asp_net_core_support);
        $this->add($asp_net_support);
        $this->add($asp_net_version);
        $this->add($php_support);
        $this->add($php_version);
        $this->add($status);
    }
}