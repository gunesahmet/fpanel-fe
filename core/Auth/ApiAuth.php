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
 */namespace FPanel\Auth;
use Exception;
use FPanel\Jwt\Exception\JwtException;
use FPanel\Jwt\Jwt;
use FPanel\Models\Userdata;
use FPanel\Models\Users;
use Phalcon\Mvc\User\Component;

/**
 * Class ApiAuth
 * @package NGTek\Auth
 */
class ApiAuth extends Component
{
    public $message;

    /**
     * @var Jwt
     */
    private $jwt;

    /**
     * @param $credentials
     * @return false|string
     * @throws JwtException
     */
    public function check($credentials)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];

        $user = Users::findFirst("username = '$username'");

        if(!$user) {
            $this->debug->firebug("User not found in users", 'debug');
            $this->message = __('Wrong username/password combination');
            return false;
        }

        if (!$this->security->checkHash($password, $user->password)) {
            $this->debug->firebug("Password is wrong", 'debug');
            $this->message = __('Wrong username/password combination');
            return false;
        }

        if($user->api_status != "1") {
            $this->debug->firebug("This user not allowed to access api", 'debug');
            $this->message = __('Wrong username/password combination');
            return false;
        }

        $user_data = Userdata::findFirst("user_id = '$user->id'");

        if(!$user_data) {
            $this->debug->firebug("User not found in user_data", 'debug');
            $this->message = __('Wrong username/password combination');
            return false;
        }

        $jwt = Jwt::create();

        $login_date = time();
        $expire = $login_date + 3600;
        $jwt->iss =  "http://" . $this->request->getServerAddress() . ":8880/" . $this->config->api->address;
        $jwt->sub =  $this->crypto->staticEncode("UserID:$user->id");


        $jwt->username =  $user->username;
        $jwt->account_type =  $user->type;
        $jwt->email =  $user_data->email;
        $jwt->expire = $expire;
        $jwt->expire_date =  $this->date->tsToDate($expire);
        $jwt->login_date =  $this->date->tsToDate($login_date);
        $jwt->request_id =  $this->request_id;
        $jwt->client_address =  $this->request->getClientAddress();

        $token = $jwt->encode(CRYPT_KEY);

//        $this->jwt = Jwt::decode($this->request->getHeader("token"));
//        $status = $this->jwt->verify(mb_substr($this->crypto->staticEncode(CRYPT_IV), 0, 16));

        $this->message = __("Request success");
        return $token;
    }

    /**
     * @param $status
     * @param $status_message
     * @throws Exception
     */
    public function checkUserFlags($status, $status_message)
    {
        if ($status == '4')
            throw new Exception(__('User is disabled as temporarily.') .'<br />' . $status_message);
        if ($status == '3')
            throw new Exception(__('Activation approval is expected by the administrator.').'<br />' . $status_message);
        if($status == '2')
            throw new Exception(__('Activation is waiting for this user') .'<br />' . $status_message);
    }

    /**
     * @return mixed|null
     */
    public function getUsername()
    {
        if(!isset($this->jwt->username)) {
            return null;
        }
        return $this->jwt->username;
    }

    /**
     * @return mixed|null
     */
    public function getUserType()
    {
        if(!isset($this->jwt->account_type)) {
            return null;
        }
        return $this->jwt->account_type;
    }

    /**
     * @return array|string|string[]|null
     */
    public function getUserId()
    {
        if(!isset($this->jwt->sub)) {
            return null;
        }
        return str_replace("UserID:", "", $this->crypto->staticDecode($this->jwt->sub));
    }

    /**
     * @return array
     */
    public function getIdentity(): array
    {
        return [
            "id" => str_replace("UserID:", "", $this->crypto->staticDecode($this->jwt->sub)),
            "username" => $this->jwt->username,
            "email" => $this->jwt->email,
            "expire" => $this->jwt->expire,
            "request_id" => $this->jwt->request_id,
            "type" => $this->jwt->account_type,
            "client_address" => $this->jwt->client_address,
        ];
    }


    /**
     * @return bool
     */
    public function checkToken(): bool
    {
        if($this->request->hasHeader("token")) {
            try {
                $jwt = new Jwt();
                $this->jwt = $jwt->decode($this->request->getHeader("token"));
                $status = $this->jwt->verify(CRYPT_KEY);
                if($status) {
                    if(isset($this->jwt->expire)) {
                        if($this->jwt->expire > time()) {
                            return true;
                        }
//                        else {
//                            $this->debug->debug("expired", false);
//                        }
                    }
//                    else {
//                        $this->debug->debug("expire", false);
//                    }
                }
//                else {
//                    $this->debug->debug("status", false);
//                }
            }
            catch (Exception $e) {
                $this->debug->debug($e->getMessage(), false);
                $this->debug->firebug($e->getMessage(), 'debug');
            }
        }
        return false;
    }
}