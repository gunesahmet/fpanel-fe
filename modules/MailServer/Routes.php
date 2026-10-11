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
 */
namespace MailServer;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Router\Group;

class Routes
{

    /**
     * @param Router $router
     * @return Router
     */
    public function add(Router $router): Router
    {
        $mailserver = new Group(array(
            "module"     => "MailServer",
            "controller" => "mailserver"
        ));

        $mailserver->setPrefix("/mailserver");

        $route_list = array(
            //array("link","method","name")
            array("","index","mailserver", "Mail Server Settings"),
            array("/generalsettings","generalsettings", "mailserver", "Mail Server Settings"),
            array("/multiple/{id_type}","multiple", "mailserver", "mailserver/multiple"), // @TODO Bak nerde kullaniliyor?

            array("/smtp","smtp", "mailserver","Mail Queue"),
            array("/removequeue/{id}","removeQueue", "mailserver","Mail Queue"),
            array("/flush","flush", "mailserver","Mail Queue"),
            array("/clear","clear", "mailserver","Mail Queue"),
            array("/multiplequeue","multiplequeue", "mailserver","Mail Queue"),
            array("/retrieve","retrieve", "mailserver","Mail Queue"),

            array("/spamsettings","spamsettings", "spamassassin","Mail Server Spam Settings"),

            array("/blacklist","blacklist", "spamassassin","Mail Server Blacklist"),
            array("/blacklistcreate","blacklistcreate","spamassassin","Mail Server Blacklist"),
            array("/editblack/{id}","editblack", "spamassassin","Mail Server Blacklist"),
            array("/deleteblack/{id}","deleteblack", "spamassassin","Mail Server Blacklist"),

            array("/whitelist","whitelist", "spamassassin","Mail Server Blacklist"),
            array("/whitelistcreate","whitelistcreate", "spamassassin","Mail Server Blacklist"),
            array("/editwhite/{id}","editwhite", "spamassassin","Mail Server Blacklist"),
            array("/deletewhite/{id}","deletewhite", "spamassassin","Mail Server Blacklist"),

            array("/policyd","policyd", "policy","Mail Server Policies"),
            array("/savepolicysettings","savepolicysettings", "policy","Mail Server Policies"),
            array("/addpolicy","addpolicy", "policy","Mail Server Policies"),
            array("/editpolicy/{id}","updatepolicy", "policy","Mail Server Policies"),
            array("/deletepolicy/{id}","deletepolicy", "policy","Mail Server Policies"),

            array("/policymember/{policy_id}","policymember", "policy","Mail Server Policies"),
            array("/addpolicymember/{policy_id}","addpolicymember", "policy","Mail Server Policies"),
            array("/editpolicymember/{policy_id}/{id}","updatepolicymember", "policy","Mail Server Policies"),
            array("/deletepolicymember/{policy_id}/{id}","deletepolicymember", "policy","Mail Server Policies"),

            array("/quotas/{policy_id}","quotas", "policy","Mail Server Policies"),
            array("/addquotas/{policy_id}","addquotas", "policy","Mail Server Policies"),
            array("/editquotas/{policy_id}/{id}","updatequotas", "policy","Mail Server Policies"),
            array("/deletequotas/{policy_id}/{id}","deletequotas", "policy","Mail Server Policies"),
        );

        foreach ($route_list as $url) {
            $mailserver->add($url[0],
                array(
                    "action" => $url[1],
                    "controller" => $url[2],
                )
            )->setName($url[3]);

            $mailserver->add($url[0] . "/{type}/{server_id}",
                array(
                    "action" => $url[1],
                    "controller" => $url[2],
                )
            )->setName($url[3]);
        }

        $router->mount($mailserver);
        return $router;
    }

}