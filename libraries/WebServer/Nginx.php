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
 */namespace FPanel\WebServer;

use FPanel\Process\WebServer as Webservermng;

/**
 * Class Nginx
 * @package FPanel\WebServer
 */
trait Nginx
{
    protected $web_error_messages = [];

    protected function saveNginx($server_id): bool
    {
        if(!$this->validateNginx())
            return false;

        $multi_accept = ($this->getPost("multi_accept") == "on" ? 'on' : 'off');
        $sendfile = ($this->getPost("sendfile") == "on" ? 'on' : 'off');
        $tcp_nopush = ($this->getPost("tcp_nopush") == "on" ? 'on' : 'off');
        $tcp_nodelay = ($this->getPost("tcp_nodelay") == "on" ? 'on' : 'off');
        $server_tokens = ($this->getPost("server_tokens") == "on" ? 'on' : 'off');
        $server_name_in_redirect = ($this->getPost("server_name_in_redirect") == "on" ? 'on' : 'off');
        $gzip = ($this->getPost("gzip") == "on" ? 'on' : 'off');
        $gzip_vary = ($this->getPost("gzip_vary") == "on" ? 'on' : 'off');

        $params = array(
            'worker_processes' => $this->getPost("worker_processes"),
            'worker_connections' => $this->getPost("worker_connections"),
            'keepalive_timeout' => $this->getPost("keepalive_timeout"),
            'types_hash_max_size' => $this->getPost("types_hash_max_size"),
            'server_names_hash_bucket_size' => $this->getPost("server_names_hash_bucket_size"),
            'multi_accept' => $multi_accept,
            'sendfile' => $sendfile,
            'tcp_nopush' => $tcp_nopush,
            'tcp_nodelay' => $tcp_nodelay,
            'server_tokens' => $server_tokens,
            'server_name_in_redirect' => $server_name_in_redirect,
            'gzip' => $gzip,
            'gzip_vary' => $gzip_vary,
            'gzip_buffers' => $this->getPost("gzip_buffers"),
            'gzip_disable' => $this->getPost("gzip_disable"),
            'gzip_proxied' => $this->getPost("gzip_proxied"),
            'gzip_comp_level' => $this->getPost("gzip_comp_level"),
            'gzip_http_version' => $this->getPost("gzip_http_version"),
            'gzip_types' => $this->getPost("gzip_types"),
        );
        $process = new Webservermng();
        $process->setServerId($server_id);
        $process->saveWebSertting($params);
        return true;
    }

    private function validateNginx(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("worker_processes"),
                'field' => 'worker_processes',
                'message' => __('Worker Process is required'),
            ),
            array(
                'item' => $this->getPost("worker_connections"),
                'field' => 'worker_connections',
                'message' => __('Worker Connections is required'),
            ),
            array(
                'item' => $this->getPost("keepalive_timeout"),
                'field' => 'keepalive_timeout',
                'message' => __('Keep Alive Timeout is required'),
            ),
        );
        $this->validation->Required($required)
            ->Numeric($this->getPost("keepalive_timeout"),"keepalive_timeout",__('Please enter valid setting. %keepalive_timeout%',array('keepalive_timeout' => '((Keep Alive Timeout))')))
            ->Numeric($this->getPost("worker_connections"),"worker_connections",__('Please enter valid setting. %worker_connections%',array('worker_connections' => '((Worker Connections))')))
            ->Numeric($this->getPost("worker_processes"),"worker_processes",__('Please enter valid setting. %worker_processes%',array('worker_processes' => '((Worker Processes))')));

        if($this->getPost("types_hash_max_size") != null)
            $this->validation->Numeric($this->getPost("types_hash_max_size"),"types_hash_max_size",__('Please enter valid setting. %types_hash_max_size%',array('types_hash_max_size' => '((Type Hash Max Size))')));

        if($this->getPost("server_names_hash_bucket_size") != null)
            $this->validation->Numeric($this->getPost("server_names_hash_bucket_size"),"server_names_hash_bucket_size",__('Please enter valid setting. %server_names_hash_bucket_size%',array('server_names_hash_bucket_size' => '((Server names Hash Bucket Size))')));

        $this->validation->InclusionIn($this->getPost("gzip_proxied"),"gzip_proxied",array('off' => 'off',
            'expired' => 'expired',
            'no-cache' => 'no-cache',
            'no-store' => 'no-store',
            'private' => 'private',
            'no_last_modified' => 'no_last_modified',
            'no_etag' => 'no_etag',
            'auth' => 'auth',
            'any' => 'any'),__('Please select valid option. %gzip_proxied%',array('gzip_proxied' => '((Gzip Proxied))')));

        $this->validation->InclusionIn($this->getPost("gzip_comp_level"),"gzip_comp_level",array('1' => '1','2' => '2','3' => '3','4' => '4','5' => '5','6' => '6','7' => '7','8' => '8','9' => '9'),__('Please select valid option. %gzip_comp_level%',array('gzip_comp_level' => '((Gzip Compression Level))')));                    $this->validation->InclusionIn($this->getPost("gzip_http_version"),"gzip_http_version",array('1.0' => '1.0',
        '1.1' => '1.1',),__('Please select valid option. %gzip_http_version%',array('gzip_http_version' => '((Gzip HTTP Version))')));


        if($this->validation->Validate() == false) {
            $this->web_error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}
