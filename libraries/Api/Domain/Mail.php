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

use FPanel\Domain\Mails;
use FPanel\Environments\QueryBuilder;
use FPanel\Models\Domains;
use FPanel\Models\MailAlias;
use Phalcon\Mvc\User\Component;

class Mail extends Component
{
    private $error_messages = [];
    
    public function mail($process, $params, $client_id)
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
            return $this->error_messages['domain_name'] = "You dont have access for this domain";
        }
        switch($process) {
            case "list":
                return $this->accountList($params);
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            case "create_alias":
                return $this->createAlias($params);
            case "update_alias":
                return $this->updateAlias($params);
            case "delete_alias":
                return $this->deleteAlias($params);
            default:
                return array("process" => 'Processs is required');
        }
    }

    private function accountList($params): array
    {

        $this->error_messages = array();

        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $error_messages;
        }
        $qbuilder = new QueryBuilder();
        $mails = $qbuilder->select([
            "mails.mail_name",
            "mails.mailbox_quota",
            "mails.status",
            "mails.forward",
            "mails.forward_addr",
            "mails.id"
        ])
            ->from('mails')
            ->where([
                'conditions' => "mails.domain_id = ?1",
                'bind' => [1 => $domains->id]
            ])
            ->find();

        $list = array();
        foreach($mails as $mail) {
            $pro_users = MailAlias::find("mail_id = '$mail[id]'");
            $alias_list = array();

            if($pro_users) {
                foreach($pro_users->toArray() as $user) {
                    $alias_list[] = array(
                        'alias_name' => $user['alias_name'],
                    );
                }
            }

            $list[] = array(
                'mail_name' => $mail['mail_name'],
                'quota' => $mail['mailbox_quota'],
                'status' => $mail['status'] == "1" ? STATUSES[ACTIVE] : STATUSES[PASSIVE],
                'forward' => $mail['forward'] == "1",
                'forward_address' => $mail['forward_addr'],
                'aliases' => $alias_list,
            );
        }

        $list['status'] = "1";

        return $list;
    }

    private function create($params)
    {
        $requeired_keys = [
            "mail_name",
            "mail_password",
            "quota",
            "status",
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
        $unlimited_quota = "";
        if($params["quota"] == null || $params['quota'] == "0") {
            $unlimited_quota = true;
        }

        $post_data = array(
            'domain_name'       => $domain_name,
            'domain_id'         => $domains->id,
            'mail_account'      => $params['mail_name'],
            'password'          => $params['mail_password'],
            'mailbox_quota'     => $params['quota'],
            'unlimited_quota'   => $unlimited_quota,
            'status'            => (!empty($params['status'] && $params["status"]) ? ON : OFF),
        );
        $mail_account = new Mails();
        $mail_account->setPosts($post_data);
        $mail_account_save = $mail_account->createMailAccount();

        if($mail_account_save) {
            return "Success";
        }
        else {
            foreach ($mail_account->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    private function update($params)
    {
        $requeired_keys = [
            "mail_name",
            "mail_password",
            "quota",
            "status",
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
        $mails = \FPanel\Models\Mails::findFirst("mail_name = '$params[mail_name]' AND domain_id = '$domains->id'");
        if(!$mails) {
            $this->error_messages['mail_name'] = "Mail not found";
            return $this->error_messages;
        }

        $unlimited_quota = OFF;
        if($params['quota'] == "0") {
            $unlimited_quota = ON;
        }

        $forward = OFF;
        $forward_address = "";
        if($params['forward']) {
            $forward = ON;
            $forward_address = $params['forward_address'];
        }
        $autoresponder = false;
        if($params['forward']) {
            $autoresponder = true;
        }

        $post_data = array(
            'mail_id'           => $mails->id,
            'domain_name'       => $domain_name,
            'domain_id'         => $domains->id,
            'mail_account'      => $params['mail_name'],
            'password'          => $params['mail_password'],
            'mailbox_quota'     => $params['quota'],
            'unlimited_quota'   => $unlimited_quota,
            'status'            => (!empty($params['status'] && $params["status"]) ? ON : OFF),

            'forward'           => $forward,
            'autoresponder'     => $autoresponder,
            'forward_addr'      => $forward_address,
            'old_address'       => $params['mail_name'], //@TODO: İleride burası old_name olacak yani mail account ismide değiştirilebilecek
        );
        $mail_account = new Mails();
        $mail_account->setPosts($post_data);
        $mail_account_update = $mail_account->updateMailAccount();

        if($mail_account_update) {
            return "Success";
        }
        else {
            foreach ($mail_account->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    private function delete($params)
    {
        $requeired_keys = [
            "mail_name",
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
        $mails = \FPanel\Models\Mails::findFirst("mail_name = '$params[mail_name]' AND domain_id = '$domains->id'");

        if(!$mails) {
            $this->error_messages['mail_name'] = "Mail not found";
            return $this->error_messages;
        }
        $mail = new Mails();
        $mail->setPosts(array(
            'domain_name' => $domain_name,
            'domain_id' => $domains->id,
            'mail_id' => $mails->id,
        ));
        $mail_status = $mail->deleteMailAccount();

        if(!$mail_status) {
            foreach ($mail->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
            return $this->error_messages;
        }
        return "Success";
    }

    private function createAlias($params)
    {
        $requeired_keys = [
            "mail_name",
            "alias_name",
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
        $mails = \FPanel\Models\Mails::findFirst("mail_name = '$params[mail_name]' AND domain_id = '$domains->id'");
        if(!$mails) {
            $this->error_messages['mail_name'] = "Mail not found";
            return $this->error_messages;
        }

        $post_data = array(
            'mail_account'    => $params['mail_name'],
            'domain_name'     => $domain_name,
            'domain_id'       => $domains->id,
            'mail_id'         => $mails->id,
            'alias_name'      => $params['alias_name']
        );
        $mail_alias = new Mails();
        $mail_alias->setPosts($post_data);
        $mail_alias_save = $mail_alias->createMailAlias();

        if($mail_alias_save) {
            return "Success";
        }
        else {
            foreach ($mail_alias->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    private function updateAlias($params)
    {
        $requeired_keys = [
            "mail_name",
            "alias_name",
            "old_alias",
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

        $mails = \FPanel\Models\Mails::findFirst("mail_name = '$params[mail_name]' AND domain_id = '$domains->id'");
        if(!$mails) {
            $this->error_messages['mail_name'] = "Mail not found";
            return $this->error_messages;
        }

        $aliases = MailAlias::findFirst("alias_name = '$params[old_alias]' AND mail_id = '$mails->id'");
        if(!$aliases) {
            $this->error_messages['old_alias'] = "Mail Alias not found";
            return $this->error_messages;
        }

        $post_data = array(
            'mail_account'      => $params['mail_name'],
            'domain_name'       => $domain_name,
            'domain_id'         => $domains->id,
            'mail_id'           => $mails->id,
            'alias_id'          => $aliases->id,
            'alias_name'        => $params['alias_name'],
            'old_aliasname'     => $params['old_alias']
        );
        $mail_alias = new Mails();
        $mail_alias->setPosts($post_data);
        $mail_alias_update = $mail_alias->updateMailAlias();
        if($mail_alias_update) {
            return "Success";
        }
        else {
            foreach ($mail_alias->getMessages() as $title => $message) {
                $this->error_messages[$title] = $message;
            }
        }
        return $this->error_messages;
    }

    private function deleteAlias($params)
    {
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            $this->error_messages['domain_name'] = "This domain doesn't exist on system.";
            return $this->error_messages;
        }

        $mails = \FPanel\Models\Mails::findFirst("mail_name = '$params[mail_name]' AND domain_id = '$domains->id'");
        if(!$mails) {
            $this->error_messages['mail_name'] = "Mail not found";
            return $this->error_messages;
        }

        $aliases = MailAlias::findFirst("alias_name = '$params[alias_name]' AND mail_id = '$mails->id'");
        if(!$aliases) {
            $this->error_messages['old_alias'] = "Mail Alias not found";
            return $this->error_messages;
        }

        $mail = new Mails();
        $mail->setPosts(array(
            'domain_name' => $domain_name,
            'domain_id' => $domains->id,
            'mail_id' => $mails->id,
            'alias_id' => $aliases->id,
        ));
        $mail_status = $mail->deleteMailAlias();
        if($mail_status) {
            return "Success";
        }
        else {
            foreach ($mail->getMessages() as $title => $message) {
                $mail_status[$title] = $message;
            }
        }
        return $this->error_messages;
    }
}