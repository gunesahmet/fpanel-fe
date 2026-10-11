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
use Phalcon\Loader;

class Namespaces
{
    public function load(Loader &$loader)
    {
        $namespaces = [
            'Phalcon\Http'              => ROOTPATH . '/core/Http',
            'FPanel\Auth'               => ROOTPATH . '/core/Auth/',
            'FPanel\Session'            => ROOTPATH . '/core/Session/',
            'FPanel\Log'                => ROOTPATH . '/core/Log/',
            'FPanel\FirePHP'            => ROOTPATH . '/core/FirePHP/',
            'FPanel\Api'                => ROOTPATH . '/core/Api/',
            'FPanel\Environments'       => ROOTPATH . '/core/Environments/',
            'FPanel\Error'              => ROOTPATH . '/core/Error',
            'FPanel\Menu'               => ROOTPATH . '/core/Menu',
            'FPanel\Acl'                => ROOTPATH . '/core/Acl/',
            'FPanel\Process'            => ROOTPATH . '/core/Process/',
            'FPanel\Socket'             => ROOTPATH . '/core/Socket/',
            'FPanel\Translate'          => ROOTPATH . '/core/Translate/',
            'FPanel\Validations'        => ROOTPATH . '/core/Validations',
            'FPanel\Upload'             => ROOTPATH . '/core/Upload/',
            'FPanel\Security'           => ROOTPATH . '/core/Security/',
            'FPanel\Components'         => ROOTPATH . '/components/',
            'FPanel\Breadcrumbs'        => ROOTPATH . '/core/Breadcrumbs/',
            'FPanel\Globals'            => ROOTPATH . '/core/Globals/',
            'FPanel\File'               => ROOTPATH . '/core/File/',
            'FPanel\Mailer'             => ROOTPATH . '/core/Mailer/',
            'FPanel\Telegram'           => ROOTPATH . '/core/Telegram',
            'FPanel\Datatable'          => ROOTPATH . '/core/Datatable',
            'FPanel\Exceptions'         => ROOTPATH . '/core/Exceptions',
            'FPanel\Jwt'                => ROOTPATH . '/core/Jwt',
            'FPanel\Jwt\Exception'      => ROOTPATH . '/core/Jwt/Exception',
            'FPanel\Update'             => ROOTPATH . '/core/Update',

            'FPanel\Dropbox'            => ROOTPATH . '/libraries/Backup/Dropbox',

            'FPanel\Common\Client'            => ROOTPATH . '/libraries/Common/Client',
            'FPanel\Common\Domain'            => ROOTPATH . '/libraries/Common/Domain',
            'FPanel\Common\Migration'         => ROOTPATH . '/libraries/Common/Migration',
            'FPanel\Common\Server'            => ROOTPATH . '/libraries/Common/Server',
            'FPanel\Common\Statistics'        => ROOTPATH . '/libraries/Common/Statistics',

            'FPanel\Library'                => ROOTPATH . '/libraries',
            'Api\Controller\System'         => ROOTPATH . '/modules/Api/Controller/System',

        ];

        foreach(array_diff(scandir(ROOTPATH."/libraries"), array('..', '.')) as $library) {
            $namespaces["FPanel\\$library"] = ROOTPATH."/libraries/$library";
        }

        foreach(array_diff(scandir(ROOTPATH."/modules"), array('..', '.')) as $module) {
            $namespaces[$module] = ROOTPATH."/modules/$module";
        }

        $loader->registerNamespaces($namespaces)->register();

    }
}