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
 */namespace FPanel\Users;

use FPanel\Environments\Datatable;
use FPanel\Environments\Environments;
use FPanel\Library\Library;
use FPanel\Models\Userdata;
use FPanel\Models\Users;

/**
 * Class Usersdata
 * @package FPanel\Usersdata
 */
class Usersdata extends Library
{
    public $parent_id;
    public $options;
    /**
     * @var string
     */
    private $id;

    public function create($date, bool $rollback = false, bool $restore = false)
    {
        if ($this->saveUser($date, $rollback, $restore)) {
            if ($this->saveUserInfo($this->id, $date)) {
                return $this->id;
            }
        }
        return false;
    }

    private function saveUser($date, $rollback = null, $restore = false): bool
    {
        $users = new Users();
        if($restore == false) {
            $password = $this->security->hash($this->getPost("password"));
        }
        else {
            $password = $this->getPost("password");
        }

        $assigned = array(
            "id"                    => $this->env->createUuid(),
            'username'              => $this->getPost("username"),
            'password'              => $password,
            'parent'                => $this->parent_id,
            'type'                  => $this->getPost("type"),
            'must_change_password'  => PASSIVE,
            'expire_date'           => $date['expire_date'] == null || $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date']),
            'interface'             => 'default',
            'language'              => 'en_US',
            'status'                => ACTIVE,
            'api_status'            => PASSIVE
        );
        $users->assign($assigned);

        if ($users->create()) {
            $this->id = $users->id;
            return true;
        }
        else {
            foreach ($users->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Usersdata->create",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
    }

    public function saveUserInfo($create_user_id, $rollback = null): bool
    {
        if($rollback == null) {

        }

        $user_data = new Userdata();
        $user_data->assign(array(
            "id"            => $this->env->createUuid(),
            'user_id'       => $create_user_id,
            'fullname'      => $this->getPost("fullname"),
            'email'         => $this->getPost("email"),
            'company'       => $this->getPost("company"),
            'telephone'     => $this->getPost("telephone"),
            'address'       => $this->getPost("address"),
            'city'          => $this->getPost("city"),
            'zipcode'       => $this->getPost("zipcode"),
            'country'       => $this->getPost("country"),
        ));
        if (!$user_data->create()) {
            foreach ($user_data->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Usersdata->saveUserInfo",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function delete($user_id, $client_type, $parent_id): bool
    {
        // $user_id: Silinecek userin id'si
        // $user_type: Silinecek userin türü
        // $client_id: Silme işlemini yapan userin id'si
        // $client_type: Silme işlemini yapacak userin türü

        // @Not: Eğer silme işlemini yapacak user admin ise her türlü işlemi yapmakta serbesttir
        // @Not: Bu işlem sadece user silme içindir. Domain silme işleminde farklı prosedürler uygulanacak bu yüzden bu fonksiyon kesinlikle domain silme işleminde kullanılmayacaktır.
        if(($client_type > ADMIN_USER) && ($client_type < SUPPORT_USER)) {
            $user = Users::find("parent = '$parent_id'");
            if(!$user) {
                return false;
            }
        }

        $this->deleteUserInfo($user_id);
        return $this->deleteUser($user_id);
    }

    private function deleteUser($id): bool
    {
        $users = Users::findFirstById($id);
        if ($users) {
            if(!$users->delete()) {
                foreach($users->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Usersdata->deleteUser",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        return true;
    }

    public function deleteUserInfo($id): bool
    {
        $users_info = Userdata::findfirst("user_id = '$id'");
        if ($users_info) {
            if(!$users_info->delete()) {
                foreach($users_info->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Usersdata->deleteUserInfo",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        return true;
    }

    public function selectableUsers($user_id, $user_type): array
    {
        $user_list = array($user_id => __('Self'));
        if(($user_type == ROOT_USER) || ($user_type == ADMIN_USER) || ($user_type == SUPPORT_USER)) {
            $users = $this->modelsManager->createBuilder()
                ->columns(array('FPanel\Models\Users.username','FPanel\Models\Users.id'))
                ->from('FPanel\Models\Users')
                ->where("FPanel\Models\Users.type > 1")
                ->andWhere("FPanel\Models\Users.type < 4")
                ->getQuery()
                ->execute()
                ->toArray();
        }
        else {
            $users = $this->modelsManager->createBuilder()
                ->columns(array('FPanel\Models\Users.username','FPanel\Models\Users.id'))
                ->from('FPanel\Models\Users')
                ->where("FPanel\Models\Users.parent = '$user_id'")
                ->andWhere("FPanel\Models\Users.type < 4")
                ->getQuery()
                ->execute()
                ->toArray();
        }

        foreach ($users as $user) {
            $user_list[$user['id']] = $user['username'];
        }

        return $user_list;
    }

    public function userList($user_id = null): array
    {
        $datatable = new Datatable();
        $delete_button = $datatable->buttons('users/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
        $edit_button = $datatable->buttons('users/edit/$1','normal',array('i' => 'fa fa-edit','a' => 'text-info'),__('Edit'),null,'data-id="nested"');

        $datatable->select(array('users.id',
            'users.username',
            'parent_users.username as parent_username',
            'users.expire_date',
            '(SELECT COUNT(*) FROM domains WHERE domains.client_id=users.id) AS domain_count',
            '(SELECT COUNT(*) from users as parent join users as child on parent.id = child.parent WHERE parent.id = users.id AND child.type < 4) AS client_count',
            'users.type',
            'users.status',
            'users.status_msg',
            'parent_users.id as parent_id'))
            ->from("users")
            ->leftJoin('users AS parent_users','users.parent = parent_users.id')
            ->where("users.type > 1 AND users.type < 4");

        if($user_id != null) {
            $datatable->andWhere("users.parent = '$user_id'");
        }
        $data = $datatable
            ->searchColumn(array('users.username','parent_users.username'))
            ->addColumn('actions', $edit_button . $delete_button,'users.id')
            ->unsetColumns('parent_id')
            ->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            // Expire Date
            $data['aaData'][$key][0] = '<input type="checkbox" value="'. $value[0] .'" class="checkbox-blue" id="clients" name="clients[]" />';
            $data['aaData'][$key][3] = $this->env->convertDate($value[3],__('Unlimited'));
            switch($value[6]) {
                case ADMIN_USER:
                    $data['aaData'][$key][6] = __('Admin');
                    break;
                case RESELLER_USER:
                    $data['aaData'][$key][6] = __('Reseller');
                    break;
                case CLIENT_USER:
                    $data['aaData'][$key][6] = __('Client');
                    break;
            }

            if ($value[7] == '1'){
                $btn = ' <a data-id="confirm" data-url="'. $this->uri->url_encrypt('users/suspend/'. $value[0].'/suspend').'" class="text-warning"><i class="fa fa-exclamation-circle"></i> ' . __('Suspend') . '</a>';
                $data['aaData'][$key][7] = '<span class="label label-success"> '. __('Active') .' </span>';
                $data['aaData'][$key][1] = ' <a href="'. $this->uri->url_encrypt('dashboard/client/'. $value[0]) .'">'. $value[1] .'</a>';
            }
            else{
                $data['aaData'][$key][7] = '<span title="'. $value[8].'" class="label label-danger"> '. __('Passive') .' </span>';
                $btn = ' <a data-id="confirm" data-url="'. $this->uri->url_encrypt('users/suspend/'. $value[0].'/unsuspend').'" class="text-warning"><i class="fa fa-exclamation-circle"></i> ' . __('Unsuspend') . '</a>';
                $data['aaData'][$key][1] = ' <a href="'. $this->uri->url_encrypt('dashboard/client/'. $value[0]) .'" class="text-danger">'. $value[1] .'</a>';
            }

            $data['aaData'][$key][8] = $value[10].$btn;
        }
        return $data;
    }

    public function update($user_id): bool
    {
        if(!$this->updateUser($user_id))
            return false;
        if(!$this->updateUserInfo($user_id))
            return false;
        return true;
    }

    public function updateUser($user_id): bool
    {
        $user = Users::findFirstById($user_id);

        $created_date = new Environments();
        $date = $created_date->createDates($user->created_date, $this->getPost("expire_time_limit"));

        $assign = array();

        $assign['username'] = $this->getPost("username");

        if($this->getPost("password") != null) {
            $assign['password'] = $this->security->hash($this->getPost("password"));
            $assign['password_last_set'] = $this->date->dateTime();
        }

        $assign['expire_date'] = $date['expire_date'] == UNLIMITED ? null : $this->date->tsToDate($date['expire_date']);

        $user->assign($assign);
        if(!$user->update()) {
            foreach($user->getMessages() as $title => $message) {
                $this->error_messages['errors'][$title] = $message;
            }
            $this->log->error([
                "Module" => "Usersdata->updateUser",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function updateUserInfo($user_id): bool
    {
        $userdata = Userdata::findFirst("user_id = '$user_id'");

        $userdata->assign(array(
            'fullname'  => $this->getPost("fullname"),
            'email'     => $this->getPost("email"),
            'company'   => $this->getPost("company"),
            'fax'       => $this->getPost("fax"),
            'telephone' => $this->getPost("telephone"),
            'address'   => $this->getPost("address"),
            'city'      => $this->getPost("city"),
            'region'    => $this->getPost("region"),
            'zipcode'   => $this->getPost("zipcode"),
            'country'   => $this->getPost("country")
        ));

        if(!$userdata->update()) {
            foreach($userdata->getMessages() as $title => $message) {
                $this->error_messages['errors'][$title] = $message;
            }
            $this->log->error([
                "Module" => "Usersdata->updateUserInfo",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        return true;
    }

    public function suspend($user_id, $parent_id, $message = null): bool
    {
        if($parent_id != "1") {
            $user = Users::findFirst("id = '$user_id' AND parent = '$parent_id'");
        }
        else {
            $user = Users::findFirst("id = '$user_id'");
        }

        if(!$user) {
            return false;
        }
        else {
            $status = PASSIVE;
            $user->assign(array(
                'status' => $status,
                'status_msg' => $message
            ));
            if (!$user->update()) {
                foreach ($user->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Usersdata->suspend",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
    }

    public function unsuspend($user_id, $parent_id, $message = null): bool
    {
        if($parent_id != "1") {
            $user = Users::findFirst("id = '$user_id' AND parent = '$parent_id'");
        }
        else {
            $user = Users::findFirst("id = '$user_id'");
        }

        if(!$user) {
            return false;
        }
        else {
            $status = "1";
            $user->assign(array(
                'status' => $status,
                'status_msg' => $message
            ));
            if (!$user->update()) {
                foreach ($user->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Usersdata->unsuspend",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
    }

    public function changeUserType($id): bool
    {
        $user = Users::findFirst("id = '$id'");

        if(!$user) {
            return false;
        }
        else {
            if($user->type == RESELLER_USER) {
                $type = CLIENT_USER;
                $user_data = Users::find("parent = '$id'");
                if(count($user_data->toArray()) > 0 ) {
                    $this->error_messages['content'] = __('This user can not convert to client');
                    return false;
                }
            }
            else {
                $type = RESELLER_USER;
            }

            $user->assign(array(
                'type' => $type,
            ));
            if (!$user->update()) {
                foreach ($user->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Usersdata->changeUserType",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
    }
}