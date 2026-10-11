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

use FPanel\Environments\QueryBuilder;
use FPanel\Models\Domains;
use Phalcon\Mvc\User\Component;

class DomainAlias extends Component
{
    public function alias($process, $params, $client_id)
    {
        $requeired_keys = [
            "domain_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        if(!$this->env->checkDomainOwner($params["domain_name"], $client_id)) {
            return $error_messages['domain_name'] = "You dont have access for this domain";
        }
        switch($process) {
            case "list":
                return $this->aliasList($params);
            case "create":
                return $this->create($params);
            case "update":
                return $this->update($params);
            case "delete":
                return $this->delete($params);
            default:
                return $error_messages['Error'] = __('Incorrect process');
        }
    }

    public function aliasList($params): array
    {
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        $qbuilder = new QueryBuilder();
        $domain_aliases = $qbuilder->select([
            "domain_alias.alias_name",
            "domain_alias.web",
            "domain_alias.mail",
        ])
            ->from('domain_alias')
            ->where([
                'conditions' => "domain_alias.domain_id = ?1",
                'bind' => [1 => $domains->id]
            ])
            ->find();
        foreach ($domain_aliases as $k => $v) {
            $domain_aliases[$k]["web"] = $v["web"] == "1";
            $domain_aliases[$k]["mail"] = $v["mail"] == "1";
        }
        $domain_aliases['status'] = "1";

        return $domain_aliases;
    }

    public function create($params)
    {
        $requeired_keys = [
            "mail_service",
            "web_service",
            "alias_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $error_messages = array();
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages['domain_name'] = "This domain doesn't exist on system.";
        }

        $post_data = array(
            'update'            => false,
            'domain_name'       => $domain_name,
            'domain_id'         => $domains->id,
            'alias_name'        => $params['alias_name'],
            'syn_pri_dom'       => "1",
            'dns'               => "1",
            'status'            => "1",
            'mail'              => $params['mail_service'],
            'web'               => $params['web_service'],
            'old_alias'         => "",
        );
        $domain_alias = new \FPanel\Domain\DomainAlias();
        $domain_alias->setPosts($post_data);
        $domain_alias_status = $domain_alias->createAlias();
        if($domain_alias_status) {
            return "Success";
        }
        else {
            foreach ($domain_alias->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
        }
        return $error_messages;
    }

    public function update($params)
    {
        $requeired_keys = [
            "mail_service",
            "web_service",
            "alias_name",
            "old_alias",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $error_messages = array();
        try {
            $domain_name = $params['domain_name'];
            $domains = Domains::findFirst("name = '$domain_name'");
            if(!$domains) {
                return $error_messages['domain_name'] = "This domain doesn't exist on system.";
            }
            $domain_aliases = \FPanel\Models\DomainAlias::findFirst("domain_id = '$domains->id' AND alias_name = '$params[old_alias]'");

            if(!$domain_aliases) {
                return $error_messages['old_alias'] = "Alias not found";
            }

            $post_data = array(
                'update'            => true,
                'id'                => $domain_aliases->id,
                'domain_id'         => $domain_aliases->domain_id,
                'domain_name'       => $domain_name,
                'alias_name'        => $params['alias_name'],
                'syn_pri_dom'       => "1",
                'dns'               => "1",
                'status'            => "1",
                'mail'              => !empty($params['mail_service']) && $params["mail_service"]? ON : OFF,
                'web'              => !empty($params['web_service']) && $params["web_service"]? ON : OFF,
                'old_alias'         => $params['old_alias'],
            );
            $domain_alias = new \FPanel\Domain\DomainAlias();
            $domain_alias->setPosts($post_data);
            $domain_alias_status = $domain_alias->updateAlias($domain_aliases->id, $domains->id);
            if ($domain_alias_status) {
                return "Success";
            }
            else {
                foreach ($domain_alias->getMessages() as $title => $message) {
                    $error_messages[$title] = $message;
                }
            }
        }
        catch (\Exception $e) {
            $error_messages["exception"] = $e->getMessage();
        }

        return $error_messages;
    }

    public function delete($params)
    {
        $requeired_keys = [
            "alias_name",
        ];
        foreach ($requeired_keys as $requeired_key) {
            if(!array_key_exists($requeired_key, $params)) {
                return [$requeired_key => "$requeired_key is required"];
            }
        }
        $error_messages = array();
        $domain_name = $params['domain_name'];
        $domains = Domains::findFirst("name = '$domain_name'");
        if(!$domains) {
            return $error_messages['domain_name'] = "This domain doesn't exist on system.";
        }
        $domain_aliases = \FPanel\Models\DomainAlias::findFirst("domain_id = '$domains->id' AND alias_name = '$params[alias_name]'");

        if(!$domain_aliases) {
            return $error_messages['alias_name'] = "Alias not found";
        }

        $domain_alias = new \FPanel\Domain\DomainAlias();
        $domain_alias_status = $domain_alias->deleteAlias($domain_aliases->id, $domain_name, $domains->id);
        if(!$domain_alias_status) {
            foreach ($domain_alias->getMessages() as $title => $message) {
                $error_messages[$title] = $message;
            }
            return $error_messages;
        }
        return "Success";

    }
}