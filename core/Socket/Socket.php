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
 */namespace FPanel\Socket;

use Exception;

use FPanel\Exceptions\ServerNotFoundException;
use FPanel\Models\Servers;
use Phalcon\Mvc\User\Component;

/**
 * Class Socket
 * @package FPanel\Socket
 */
class Socket extends Component
{
    protected $job;
    protected $process;
    protected $params;
    public $command;
    protected $response = null;
    protected $error_messages = null;
    protected $timeout = false;
    private $key = CRYPT_KEY;
    private $iv = CRYPT_IV;

    private $sock;
    private $port = 8889;

    public function send($timeout = 5, $server_id = "1"): Socket
    {
        if(php_sapi_name() != 'cli' ) {
            ob_implicit_flush();
        }
        if(($server_id == "1") || ($server_id == null)) {
            $host = "127.0.0.1";
        }
        else {
            $server = Servers::findFirstById($server_id);
            if(!$server) {
                throw new \Exception("Server: $server_id not found");
            }
            if($server->type == "main") {
                $host = "127.0.0.1";
            }
            else {
                $server_data = json_decode(trim($this->crypto->bdecode($server->data)),true);
                $host = $server->ip_address;
                $this->key = $server_data['key'];
                $this->iv = $server_data['iv'];
            }
        }

        try {
            $this->log->info([
                "module" => "Socket",
                "data" => [
                    "host" => $host,
                ]
            ]);

            $out = $this->encrypt($this->command);

//            $this->debug->firebug($this->command, "debug");
            $out .= "\n";
            $this->sendOld($host, $timeout, $out);
//            $this->sendOld($host, $timeout, $out);
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "Socket",
                "Action" => "Response",
                "Command" => $this->command,
                "Server" => $server_id,
                "Clear" => "Socket Connection Failed: " . $e->getMessage() . " " . $e->getFile(),
            ]);
            throw new \Exception("Failed server: $host connection");
        }
        return $this;
    }

    /**
     * @param $host
     * @param $timeout
     * @param $out
     */
    private function sendNew($host, $timeout, $out)
    {
        try {
            $response = null;
            $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if(!is_resource($socket)) {
                throw new \Exception("Server: $host resource failed");
            }

            try {
                @socket_connect($socket, $host, $this->port);
            }
            catch (Exception $e) {
                throw new \Exception("Failed server: $host connect");
            }

            $timeout = array( 'sec' => $timeout ,'usec' => 0);
            @socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout);

            $status = @socket_write($socket, $out, strlen($out));
            if(!$status) {
                throw new \Exception("Server: $host write failed");
            }

            if ($timeout > 0) {
                while (true) {
                    $resp = @socket_read($socket, 16384);
                    if(substr($resp, -1) === "\r") {
                        // read/skip one byte from the socket
                        // we assume that the next byte in the stream must be a \n.
                        // this is actually bad in practice; the script is vulnerable to unexpected values
                        @socket_read($socket, 1);
                    }
                    if(!$resp) {
                        break;
                    }

                    $response .=  $resp;
                    if($resp == PHP_EOL || trim($resp) == null) {
                        break;
                    }


                    if(php_sapi_name() != 'cli' ) {
                        ob_flush();
                        flush();
                    }
                    if(strpos($this->command, "init") !== false) {
                        break;
                    }
                }
            }

            @socket_close($socket);

            if(trim($response) != null) {
                $clear = $this->decrypt(trim($response));
                $this->response = $clear;
            }
        }
        catch (Exception $e) {
            $this->log->error([
                "Module" => "Socket",
                "Action" => "Response",
                "Command" => $this->command,
                "Clear" => "Socket Connection Failed: " . $e->getMessage() . " " . $e->getFile(),
            ]);
            throw new \Exception("Socket Connection Failed: " . $e->getMessage());
        }
    }

    private function sendOld($host, $timeout, $out)
    {
        $diff = [];
        $response = null;

        $this->sock = @fsockopen($host, $this->port, $errno, $errstr, $timeout);
        if ($this->sock === false) {
            $this->error_messages = array('connection' => 'failed');
            throw new \Exception("Server: $host write failed");
        }

        $status = @fwrite($this->sock, $out, strlen($out));

        if(!$status) {
            $this->error_messages = array('connection' => 'write failed');
            throw new \Exception("Server: $host write failed");
        }

        @stream_set_blocking($this->sock, true);
        @stream_set_timeout($this->sock, $timeout);

        $info = stream_get_meta_data($this->sock);
        if ($timeout > 0) {
            while (!feof($this->sock) && !$info['timed_out']) {
                $resp = @fgets($this->sock, 2048);
//                if(!$resp) {
//                    $this->debug->debug("false geldi", false);
//                }
                $response .=  $resp;
                if($resp == PHP_EOL) {
//                    $this->debug->firebug("EOL", "Info");
                    break;
                }
                if(trim($resp) == null) {
//                    $this->debug->firebug("Empty", "Info");
                    break;
                }

                $info = stream_get_meta_data($this->sock);
                if(php_sapi_name() != 'cli' ) {
                    ob_flush();
                    flush();
                }
                if(strpos($this->command, "init") !== false) {
                    break;
                }
            }
        }

        @fclose($this->sock);
        $this->debug->firebug($diff, "Time Debug");

        if(trim($response) != null) {
            $clear = $this->decrypt(trim($response));
            $this->log->info([
                "Module" => "Socket",
                "Action" => "Response",
                "Command" => $this->command,
                "Response" => $response,
                "Clear" => $clear,
            ]);
            $this->response = $clear;
        }
    }

    public function close()
    {
        @fclose($this->sock);
    }

    /**
     * @param $process
     * @return $this
     */
    public function process($process): Socket
    {
        $this->process = $process;
        return $this;
    }

    /**
     * @param array $joblist
     * @return $this
     */
    public function job(array $joblist = array()): Socket
    {
        $job_command = null;
        foreach ($joblist as $job) {

            if($job_command == null) {
                $job_command = "--$job";
            }
            else {
                $job_command = "--$job $job_command";
            }
        }
        $this->job = $job_command;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function params(array $params = array()): Socket
    {
        $param_list = null;
        foreach ($params as $param => $value) {
            if($param_list == null) {
                $param_list = "--$param=\"$value\"";
            }
            else {
                $param_list = $param_list . ' '. "--$param=\"$value\"";
            }
        }
        $this->params = $param_list;
        return $this;
    }

    /**
     * @return $this
     */
    public function create(): Socket
    {
        $this->command = $this->process . ' ' . $this->job . ' ' . $this->params;
        $this->command = str_replace("\\", "\\\\", $this->command);
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function response()
    {
        $response = $this->response;
        if($this->error_messages != null) {
            $response = $this->error_messages;
        }
        if(strpos($this->command, "packagemng --init") === false) {
            if(strpos($response, "Err") !== false) {
                $this->log->error([
                    "Message"   => "Command contain errors",
                    "response"  => $response,
                    "command"     => $this->command,
                ]);
            }
        }
        $this->command = null;
        $this->process = null;
        $this->job = null;
        $this->params = null;
        $this->response = null;
        $this->timeout = null;
        $this->error_messages = null;

        return $response;
    }

    /**
     * @param $data
     * @return string
     */
    private function encrypt($data): string
    {
        return base64_encode( openssl_encrypt ($data, "aes-256-cfb", $this->key, true, $this->iv));
    }

    /**
     * @param $data
     * @return string
     */
    private function decrypt($data): string
    {
        return openssl_decrypt( base64_decode($data), "aes-256-cfb", $this->key, true, $this->iv);
    }

}