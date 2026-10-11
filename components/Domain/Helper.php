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

use FPanel\Exceptions\Exception;

/**
 * Class Helper
 * @package FPanel\Components\Domain
 */
trait Helper
{
    public function validator(): bool
    {
        try {
            $required = array(
                array(
                    'field' => 'domain_name',
                    'item' => $this->post['name'],
                    'message' => __('Domain is required.')
                ),
                array(
                    'field' => 'password',
                    'item' => $this->post['password'],
                    'message' => __('Password is required')
                ),
                array(
                    'field' => 'ftp_user',
                    'item' => $this->post['ftpname'],
                    'message' => __('Ftpname is required')
                ),
            );

            if($this->system_type == STANDALONE) {
                $required[] = array(
                    'field' => 'ipaddress_list',
                    'item' => $this->post['ipaddr_id'],
                    'message' => __('Please select an ip address')
                );
            }

            if($this->system_type == CLOUD || PANEL_OS_TYPE == WINDOWS) {
                $required[] = array(
                    'field' => 'application_pool_mode',
                    'item' => $this->post['application_pool_mode'],
                    'message' => __('Application pool mode is required.')
                );
            }

            if($this->parent_type != CLIENT_USER) {
                $required[] =  array(
                    'field' => 'user_select',
                    'item' => $this->post['selected_user'],
                    'message' => __('Please select an user.')
                );
                $this->validation->InclusionIn($this->post['selected_user'],'user_select', $this->post['user_list'],null,true);
            }

            // Dedicated Pool & MAX Cpu Usage
            if($this->post['dedicated_pool'] == ON) {
                $required[] = array(
                    'field' => 'max_cpu_usage',
                    'item' => $this->post['max_cpu'],
                    'message' => __('CPU Usage is required.')
                );

                if($this->post['max_cpu'] != null) {
                    $this->validation->Between($this->post['max_cpu'],"max_cpu_usage","1","99",__('Please enter 1 to 99.'));
                }
            }
            $this->validation->Required($required);


            if($this->system_type == CLOUD || PANEL_OS_TYPE == WINDOWS) {
                $this->validation->InclusionIn($this->post['application_pool_mode'],"application_pool_mode",[INTEGRATED, CLASSIC], __('Please select a valid mode.'));
            }

            if($this->system_type == STANDALONE) {
                $this->validation->InclusionIn($this->post['ipaddr_id'],'ipaddress_list', $this->post['ip_list'], __('Please select valid ip address'), true);
            }
            else {
                if($this->post["manual_select_server"] == ON) {
                    // WEB
                    $this->validation->InclusionIn($this->post['web_servers'],'web_servers', $this->post['ip_list'], __('Please select valid server'), true);

                    // DNS
                    if($this->post["dns_service"] == ON) {
                        $this->validation->InclusionIn($this->post['dns_servers'],'dns_servers', $this->post['ip_list'], __('Please select valid server'), true);
                    }
                    // MAIL
                    if($this->post["mail_service"] == ON) {
                        $this->validation->InclusionIn($this->post['mail_servers'],'mail_servers', $this->post['ip_list'], __('Please select valid server'), true);
                    }
                }
            }

            $status = $this->validation->Password($this->post['password'],'password')
                ->Domain($this->post['name'],'domain_name')
                ->FtpUser($this->post['ftpname'],'ftp_user')
                ->Permissions($this->post)
                ->DomainSettings($this->post)
                ->Limits($this->post)
                ->UniqueDomain($this->post['name'],'domain_name')
                ->UniqueDomainUser($this->post['name'],'domain_name')
                ->UniqueFtpUser($this->post['ftpname'],'ftp_user')
                ->CheckBlackList($this->post['ftpname'],'ftp_user','ftp_user',false,__('This username can\'t be use.'))
                ->CheckBlacklist($this->post['name'],'domain_name','domain_name',false,__('This domain name can\'t be use'))
                ->Validate();
            if(!$status) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }
        }
        catch (Exception $e) {
            $this->error_messages[] = $e->getMessage();
            return false;
        }
        return true;
    }

    public function apiValidator($update = false): bool
    {
        try {
            $required = array(
                array(
                    'field' => 'domain_name',
                    'item' => $this->post['name'],
                    'message' => __('Domain is required.')
                ),
            );

            if(!$update) {
                $required[] =    array(
                    'field' => 'ftp_password',
                    'item' => $this->post['password'],
                    'message' => __('Password is required')
                );
                $required[] = array(
                    'field' => 'ftp_username',
                    'item' => $this->post['ftpname'],
                    'message' => __('Ftpname is required')
                );
                $required[] = array(
                    'field' => 'ip_address',
                    'item' => $this->post['ipaddr_id'],
                    'message' => __('Please select an ip address')
                );

                if(!empty($this->post["manual_select_server"]) && $this->post["manual_select_server"] == ON) {
                    $required[] = array(
                        'field' => 'web_server_ip',
                        'item' => $this->post['web_server_ip'],
                        'message' => __('Please select an ip address')
                    );
                    $required[] = array(
                        'field' => 'dns_server_ip',
                        'item' => $this->post['dns_server_ip'],
                        'message' => __('Please select an ip address')
                    );
                    $required[] = array(
                        'field' => 'mail_server_ip',
                        'item' => $this->post['mail_server_ip'],
                        'message' => __('Please select an ip address')
                    );
                }
            }

            $this->validation->Required($required);


            if(!$update) {
                $this->validation->FtpUser($this->post['ftpname'],'ftp_username')
                    ->Password($this->post['password'],'ftp_password')
                    ->CheckBlacklist($this->post['name'],'domain_name','domain_name',false,__('This domain name can\'t be use'))
                    ->CheckBlackList($this->post['ftpname'],'ftp_user','ftp_username',false,__('This ftp username can\'t be use.'))
                    ->Domain($this->post['name'],'domain_name')
                    ->Limits($this->post)
                    ->UniqueDomain($this->post['name'],'domain_name')
                    ->UniqueDomainUser($this->post['name'],'domain_name', $update)
                    ->UniqueFtpUser($this->post['ftpname'],'ftp_username')
                    ->Permissions($this->post)
                    ->DomainSettings($this->post);
            }

            $status = $this->validation->Validate();

            if(!$status) {
                $this->error_messages = $this->validation->getMessage();
                return false;
            }
            return true;
        }
        catch (\Exception $e) {
            $this->error_messages[] = $e->getMessage();
            return false;
        }
    }
}
