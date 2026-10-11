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
 */namespace FPanel\Api\Domain;

use FPanel\Domain\Protectdir;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\Domains;
use FPanel\Models\ProtectDirectory;
use FPanel\Models\ProtectDirectoryUser;
use FPanel\Models\Subdomains;
use Phalcon\Mvc\User\Component;

class Prodir extends Component
{
    private $error_messages = [];

    public function directory($process, $params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        if(!$this->env->checkDomainOwner($params['domain_name'], $client_id)) {
            $this->error_messages['domain_name'] = "You dont have access for this domain";
        }
        switch($process) {
            case "list":
                return $this->dirlist($params);
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            case "user_list":
                return $this->listUser($params);
            case "user_create":
                return $this->createUser($params);
            case "user_update":
                return $this->updateUser($params);
            case "user_delete":
                return $this->deleteUser($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    public function dirlist($params): array
    {
        $qbuilder = new QueryBuilder();

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        $pro_dirs = $qbuilder->select([
            "protect_directory.path",
            "protect_directory.header",
            "domains.name",
            "subdomains.full_name as subdomain",
            "protect_directory.id"
        ])
            ->from('domains')
            ->innerJoin('protect_directory','domains.id = protect_directory.domain_id')
            ->leftJoin('subdomains','protect_directory.subdomain_id = subdomains.id')
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domains->id]
            ])
            ->find();

        $list = array();
        foreach($pro_dirs as $dir) {
            $pro_users = ProtectDirectoryUser::find("protect_dir_id = '$dir[id]'");
            $user_list = array();
            if(!empty($pro_users)) {
                foreach($pro_users->toArray() as $user) {
                    $user_list[] = array(
                        'username' => $user['username'],
                    );
                }
            }

            $list[] = array(
                'path' => $dir['path'],
                'title' => $dir['header'],
                'domain_name' => $dir['name'],
                'subdomain' => $dir['subdomain'],
                'users' => $user_list,
            );
        }

        $list['status'] = "1";

        return $list;
    }

    public function create($params)
    {
        $requeired_keys = [
            "subdomain",
            "path",
            "title",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }
        $subdomain_id = "0";
        $subdomains_list = array();
        $subdomains = Subdomains::find("domain_id = '$domains->id'");
        if($subdomains)
            $subdomains_list = $subdomains->toArray();

        if(!empty($params['subdomain'])) {
            $subdomain = Subdomains::findFirst("subdomain_name = '$params[subdomain]'");
            if(!$subdomain) {
                $this->error_messages['subdomain'] = "Subdomain not found";
                return $this->error_messages;
            }
            $subdomain_id = $subdomain->id;
        }

        $post_data = array(
            'domain_id'      => $domains->id,
            'path'           => '/'.$params['path'],
            'ssl'            => PASSIVE,
            'cgi_bin'        => PASSIVE,
            'header'         => $params['title'],
            'subdomain_id'   => $subdomain_id,
            'subdomain_list' => $subdomains_list,
        );

        $protectdir = new Protectdir();
        $protectdir->domain_name = $domain_name;
        $protectdir->setPosts($post_data);

        $protectdir_id = $protectdir->saveProtectdir();

        if($protectdir_id !== false) {
            return "Success";
        }
        else {
            foreach ($protectdir->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    public function update($params)
    {
        $requeired_keys = [
            "subdomain",
            "old_path",
            "path",
            "title",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        if(!empty($params['subdomain'])) {
            $subdomain = Subdomains::findFirst("subdomain_name = '$params[subdomain]'");
            if(!$subdomain) {
                $this->error_messages['subdomain'] = "Subdomain not found";
                return $this->error_messages;
            }
            $subdomain_id = $subdomain->id;
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[old_path]' AND domain_id = '$domains->id' AND subdomain_id = '$subdomain_id'");
        }
        else {
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[old_path]' AND domain_id = '$domains->id' AND subdomain_id = '0'");
        }
        if(!$protect_dirs) {
            $this->error_messages['old_path'] = "Protect Directory not found";
            return $this->error_messages;
        }

        $protect_dir_data = $protect_dirs->toArray();

        $post_data = array(
            'domain_name'        => $domains->name,
            'dir_id'        => $protect_dir_data['id'],
            'domain_id'    => $domains->id,
            'old_path'    => '/'.$params['old_path'],
            'path'    => '/'.$params['path'],
            'header'    => $params['title'],
            'subdomain_id'  => $protect_dir_data['subdomain_id'],
        );

        $protectdir = new Protectdir();
        $protectdir->domain_name = $domain_name;
        $protectdir->setPosts($post_data);

        $protectdir_update_status = $protectdir->editProtectDir();

        if($protectdir_update_status) {
            return "Success";
        }
        else {
            foreach ($protectdir->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    public function delete($params)
    {
        $requeired_keys = [
            "subdomain",
            "path",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        if(!empty($params['subdomain'])) {
            $subdomain = Subdomains::findFirst("subdomain_name = '$params[subdomain]'");
            if(!$subdomain) {
                $this->error_messages['subdomain'] = "Subdomain not found";
                return $this->error_messages;
            }
            $subdomain_id = $subdomain->id;
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '$subdomain_id'");
        }
        else {
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '0'");
        }

        if(!$protect_dirs) {
            $this->error_messages['path'] = "Protect Directory not found";
            return $this->error_messages;
        }

        $protect_dir_data = $protect_dirs->toArray();

        $protectdir = new Protectdir();
        $protectdir->setPosts(array(
            'dir_id' => $protect_dir_data['id'],
            'domain_id' => $domains->id,
        ));
        $protectdir->domain_name = $domain_name;
        $protectdir_status = $protectdir->deleteProtectDir();
        if($protectdir_status) {
            return "Success";
        }
        else {
            $this->error_messages['path'] = "Protect Directory not found";
        }
        return $this->error_messages;
    }

    public function listUser($params): array
    {
        $qbuilder = new QueryBuilder();

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        $pro_dirs = $qbuilder->select([
            "protect_directory.path",
            "protect_directory.header",
            "domains.name",
            "subdomains.full_name as subdomain",
            "protect_directory.id"
        ])
            ->from('domains')
            ->innerJoin('protect_directory','domains.id = protect_directory.domain_id')
            ->leftJoin('subdomains','protect_directory.subdomain_id = subdomains.id')
            ->where([
                'conditions' => "domains.id = ?1",
                'bind' => [1 => $domains->id]
            ])
            ->find();

        $list = array();
        foreach($pro_dirs as $dir) {
            $pro_users = ProtectDirectoryUser::find("protect_dir_id = '$dir[id]'");
            $user_list = array();
            if(!empty($pro_users)) {
                foreach($pro_users->toArray() as $user) {
                    $user_list[] = array(
                        'username' => $user['username'],
                    );
                }
            }

            $list[] = array(
                'path' => $dir['path'],
                'title' => $dir['header'],
                'domain_name' => $dir['name'],
                'subdomain' => $dir['subdomain'],
                'users' => $user_list,
            );
        }

        $list['status'] = "1";

        return $list;
    }

    public function createUser($params)
    {
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        if(!empty($params['subdomain'])) {
            $subdomain = Subdomains::findFirst("subdomain_name = '$params[subdomain]'");
            if(!$subdomain) {
                $this->error_messages['subdomain'] = "Subdomain not found";
                return $this->error_messages;
            }
            $subdomain_id = $subdomain->id;
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '$subdomain_id'");
        }
        else {
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '0'");
        }
        if(!$protect_dirs) {
            $this->error_messages['path'] = "Protect Directory not found";
            return $this->error_messages;
        }

        $post_data = array(
            'domain_id'         => $domains->id,
            'protect_dir_id'    => $protect_dirs->id,
            'username'          => $params['pro_username'],
            'password'          => $params['pro_password'],
            'path'              => $protect_dirs->path,
        );

        $protectdir_user = new Protectdir();
        $protectdir_user->setPosts($post_data);
        $protectdir_user->domain_name = $domain_name;
        $protectdir_user_save_status = $protectdir_user->saveProtectDirUser();

        if($protectdir_user_save_status) {
            return "Success";
        }
        else {
            foreach ($protectdir_user->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    public function updateUser($params)
    {
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
        }

        if(!empty($params['subdomain'])) {
            $subdomain = Subdomains::findFirst("subdomain_name = '$params[subdomain]'");
            if(!$subdomain) {
                return $this->error_messages['subdomain'] = "Subdomain not found";
            }
            $subdomain_id = $subdomain->id;
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '$subdomain_id'");
        }
        else {
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '0'");
        }
        if(!$protect_dirs) {
            return $this->error_messages['path'] = "Protect Directory not found";
        }

        $pro_users = ProtectDirectoryUser::findFirst("protect_dir_id = '$protect_dirs->id' AND username = '$params[old_username]'");

        if(!$pro_users) {
            return $this->error_messages['old_username'] = "Protect directory user not found";
        }

        $post_data = array(
            'domain_id'         => $domains->id,
            'protect_dir_id'    => $protect_dirs->id,
            'pro_dir_data'      => $protect_dirs->toArray(),
            'user_id'           => $pro_users->id,
            'old_username'      => $params['old_username'],
            'username'          => $params['pro_username'],
            'password'          => $params['pro_password'],
        );
        $protectdir_user = new Protectdir();
        $protectdir_user->setPosts($post_data);
        $protectdir_user->domain_name = $domain_name;
        $protectdir_user_update_status = $protectdir_user->editUser();

        if($protectdir_user_update_status) {
            return "Success";
        }
        else {
            foreach ($protectdir_user->getMessages() as $title => $message) {
                return $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    public function deleteUser($params)
    {
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        if(!empty($params['subdomain'])) {
            $subdomain = Subdomains::findFirst("subdomain_name = '$params[subdomain]'");
            if(!$subdomain) {
                $this->error_messages['subdomain'] = "Subdomain not found";
            }
            $subdomain_id = $subdomain->id;
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '$subdomain_id'");
        }
        else {
            $protect_dirs = ProtectDirectory::findFirst("path = '/$params[path]' AND domain_id = '$domains->id' AND subdomain_id = '0'");
        }

        if(!$protect_dirs) {
            $this->error_messages['path'] = "Protect Directory not found";
            return $this->error_messages;
        }

        $pro_users = ProtectDirectoryUser::findFirst("protect_dir_id = '$protect_dirs->id' AND username = '$params[pro_username]'");

        if(!$pro_users) {
            $this->error_messages['pro_username'] = "Protect directory user not found";
            return $this->error_messages;
        }
        $protectdir_user = new Protectdir();

        $protectdir_user->setPosts(array(
            'user_id' => $pro_users->id,
            'dir_id' => $protect_dirs->id,
            'domain_id' => $domains->id,
        ));
        $pro_user_status = $protectdir_user->deleteUser();
        if($pro_user_status == true) {
            return "Success";
        }
        else {
            foreach ($protectdir_user->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }
}