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
 */namespace FPanel\Forms;

use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;

/**
 * Class BackupRepoForm
 * @package FPanel\Forms
 */
class BackupRepoForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
    }

    /**
     * @param $options
     */
    private function formElements($options)
    {
        $ftp_server = new Text('ftp_server',array(
            'id' => 'ftp_server',
            'name' => 'ftp_server',
        ));
        $ftp_user = new Text('ftp_user',array(
            'id' => 'ftp_user',
            'name' => 'ftp_user',
        ));
        $ftp_folder = new Text('ftp_folder',array(
            'id' => 'ftp_folder',
            'name' => 'ftp_folder',
        ));
        $ftp_pass = new Password('ftp_pass',array(
            'id' => 'ftp_pass',
            'name' => 'ftp_pass',
        ));
        $status = new Check('status',array(
            'id' => 'status',
            'name' => 'status',
        ));
        if($options['action'] == "edit") {
            $data = $options['data'];
            $ftp_server->setAttribute('value', $data['ftp_server']);
            $ftp_user->setAttribute('value', $data['ftp_user']);
            $ftp_folder->setAttribute('value', $data['ftp_folder']);
            if($data['status'] == "on")
                $status->setAttribute('checked','checked');
        }

        $this->add($ftp_server);
        $this->add($ftp_user);
        $this->add($ftp_folder);
        $this->add($ftp_pass);
        $this->add($status);
    }
}