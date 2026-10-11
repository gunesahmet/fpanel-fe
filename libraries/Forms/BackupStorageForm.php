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
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Forms\Form;

/**
 * Class BackupStorageForm
 * @package FPanel\Forms
 */
class BackupStorageForm extends Form
{
    /**
     * @param null $entity
     * @param $options
     */
    public function initialize($entity = null, $options)
    {
        $this->formElements($options);
    }

    private function formElements($options)
    {
        $storages = new Select('storages',array(
            'dropbox' => 'Dropbox',
            'gdrive' => 'Google Drive',
        ),array(
            'id' => 'storages',
            'name' => 'storages'
        ));

        $storage_name = new Text('storage_name',array(
            'id' => 'storage_name',
            'name' => 'storage_name',
        ));

        $folder = new Text('folder',array(
            'id' => 'folder',
            'name' => 'folder',
        ));
        $status = new Check('status',array(
            'id' => 'status',
            'name' => 'status',
        ));

        if($options['action'] == "edit") {
            $data = $options['data'];
            $params = json_decode($options['data']['params'],true);
            $storage_name->setAttribute('value', $data['name']);
            $storages->setAttribute('value', $data['type']);
            $storages->setAttribute('disabled','disabled');
            $folder->setAttribute('value',ltrim($params['folder'],"/"));
            if($data['status'] == "on")
                $status->setAttribute('checked','checked');
        }
        $this->dropboxForms($options);
        $this->gDriveForms($options);

        $this->add($storage_name);
        $this->add($storages);
        $this->add($folder);
        $this->add($status);

    }

    private function dropboxForms($options)
    {
        $access_token = new Text('access_token',array(
            'id' => 'access_token',
            'name' => 'access_token',
        ));

        if($options['action'] == "edit") {
            $params = json_decode($options['data']['params'],true);
            $access_token->setAttribute('value', $params['access_token']);
        }
        $this->add($access_token);
    }

    private function gDriveForms($options)
    {
//        project_id
//        private_key
//        private_key_id
//        client_email
//        client_id
//        client_cert_url
//        ftp_folder

        $project_id = new Text('project_id',array(
            'id' => 'project_id',
            'name' => 'project_id',
        ));
        $private_key = new Text('private_key',array(
            'id' => 'private_key',
            'name' => 'private_key',
        ));
        $private_key_id = new Text('private_key_id',array(
            'id' => 'private_key_id',
            'name' => 'private_key_id',
        ));
        $client_email = new Text('client_email',array(
            'id' => 'client_email',
            'name' => 'client_email',
        ));
        $client_id = new Text('client_id',array(
            'id' => 'client_id',
            'name' => 'client_id',
        ));
        $client_cert_url = new Text('client_cert_url',array(
            'id' => 'client_cert_url',
            'name' => 'client_cert_url',
        ));

        $json_data = new TextArea('json_data',array(
            'id' => 'json_data',
            'name' => 'json_data',
        ));

        if($options['action'] == "edit") {
            $params = json_decode($options['data']['params'],true);

            $project_id->setAttribute('value', $params['project_id']);
            $private_key->setAttribute('value', $params['private_key']);
            $private_key_id->setAttribute('value', $params['private_key_id']);
            $client_email->setAttribute('value', $params['client_email']);
            $client_id->setAttribute('value', $params['client_id']);
            $client_cert_url->setAttribute('value', $params['client_cert_url']);
        }

        $this->add($project_id);
        $this->add($private_key);
        $this->add($private_key_id);
        $this->add($client_email);
        $this->add($client_id);
        $this->add($client_cert_url);
        $this->add($json_data);
    }
}