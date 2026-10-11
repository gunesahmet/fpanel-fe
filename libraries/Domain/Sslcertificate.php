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
 */namespace FPanel\Domain;

use FPanel\Environments\Datatable;
use FPanel\Library\Library;
use FPanel\Models\Certificates;
use FPanel\Models\Domains;
use FPanel\Process\Ssl as SslMng;

/**
 * Class Sslcertificate
 * @package FPanel\Sslcertificate
 */
class Sslcertificate extends Library
{
    protected $created_ssl_id;

    public function requestCSR(): bool
    {
        if(!$this->validationRequest())
            return false;
        $ssl = new Certificates();

        $request = $this->requestCSRCurl();

        if(PANEL_OS_TYPE == WINDOWS) {
            $request = base64_decode($request);
        }
        $pri_key = str_replace("\r","", $this->parseRequest($request,'BEGIN CERT KEY','END CERT KEY'));
        $csr_key = str_replace("\r","", $this->parseRequest($request,'BEGIN CERT CSR','END CERT CSR'));

        $assign = [
            "id"            => $this->env->createUuid(),
            'domain_id'     => $this->getPost("domain_id"),
            'csr'           => $csr_key,
            'private_key'   => $pri_key,
            'crt'           => "NULL",
            'ca'            => "NULL",
            'name'          => $this->getPost("name"),
            'bit'           => $this->getPost("bit"),
            'country'       => $this->getPost("country"),
            'state'         => $this->getPost("state"),
            'city'          => $this->getPost("city"),
            'company'       => $this->getPost("company"),
            'department'    => $this->getPost("department"),
            'domain_name'   => $this->getPost("domain_name"),
            'email'         => $this->getPost("email"),
        ];
        $ssl->assign($assign);

        if(!$ssl->create()) {
            foreach ($ssl->getMessages() as $message) {
                $this->error_messages[$message->getField()] = $message->getMessage();
            }
            $this->log->error([
                "Module" => "Sslcertificate->requestCSR",
                "data" => $this->getPost(),
                "errors" => $this->error_messages,
            ]);
            return false;
        }
        else {
            return true;
        }
    }

    public function installSSLFiles($id): bool
    {
        $domain_id = $this->getPost("domain_id");
        $ssl = Certificates::findFirst("id = '$id' AND domain_id = '$domain_id'");
        $assign = array();
        if(!empty($this->getPost("crt"))) {
            $assign['crt'] = $this->getPost("crt");
        }
        else {
            $this->error_messages['content'] = __('Certificate is required');
            return false;
        }

        if(!empty($this->getPost("ca_crt"))) {
            $assign['ca'] = $this->getPost("ca_crt");
        }

        if(openssl_x509_check_private_key($this->getPost("crt"), $ssl->private_key)) {
            $newsource = openssl_x509_parse($this->getPost("crt"),true);
            if($newsource['subject']['CN'] != $this->getPost("domain_name")) {
                $this->error_messages['content'] = __('This SSL Certificate not for this domain');
                return false;
            }

            $assign["common_name"] = $newsource['subject']['CN'];
            $assign["valid_from"] = $newsource["validFrom_time_t"];
            $assign["valid_to"] = $newsource["validTo_time_t"];
            $assign["signature_type"] = $newsource["signatureTypeSN"];

            $ssl->assign($assign);

            if(!$ssl->update()) {
                foreach ($ssl->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Sslcertificate->installSSLFiles",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                return true;
            }
        }
        else {
            $this->error_messages['content'] = __('Certificate can not corresponds with private key');
            return false;
        }
    }

    public function deleteSSLCert($id): bool
    {
        $ssl = Certificates::findFirstById($id);
        if($ssl) {
            if(!$ssl->delete()) {
                foreach($ssl->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Sslcertificate->deleteSSLCert",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
        }
        return true;
    }

    public function deleteAllSSLDomain($domain_id): bool
    {
        $ssl = Certificates::find("domain_id = '$domain_id'");
        if($ssl) {
            if(!$ssl->delete()) {
                foreach($ssl->getMessages() as $title => $message) {
                    $this->error_messages[$title] = $message;
                }
                $this->log->error([
                    "Module" => "Sslcertificate->deleteAllSSLDomain",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            return true;
        }
        else {
            return false;
        }
    }

    public function uploadCertificateFile(): bool
    {
        $ssl = new Certificates();
        $newsource = openssl_x509_parse($this->getPost("crt"),true);

        if($newsource['subject']['CN'] == $this->getPost("domain_name") || strpos($newsource['subject']['CN'], ".".$this->getPost("domain_name")) !== false) {
            $ssl->assign(array(
                "id"            => $this->env->createUuid(),
                'domain_id'     =>  $this->getPost("domain_id"),
                'private_key'   =>  $this->getPost("private_key"),
                'crt'           =>  $this->getPost("crt"),
                'ca'            =>  $this->getPost("ca_crt"),
                'name'          =>  $this->getPost("name"),
                'domain_name'   =>  $this->getPost("domain_name"),
                'common_name'   =>  $newsource['subject']['CN'],
                'valid_from'    =>  $newsource["validFrom_time_t"],
                'valid_to'      =>  $newsource["validTo_time_t"],
                'signature_type'=>  $newsource["signatureTypeSN"],
            ));

            if(!$ssl->create()) {
                foreach ($ssl->getMessages() as $message) {
                    $this->error_messages[$message->getField()] = $message->getMessage();
                }
                $this->log->error([
                    "Module" => "Sslcertificate->uploadCertificateFile",
                    "data" => $this->getPost(),
                    "errors" => $this->error_messages,
                ]);
                return false;
            }
            else {
                $this->created_ssl_id = $ssl->id;
                return true;
            }
        }
        else {
            $this->error_messages["ssl_cn"] = $newsource['subject']['CN'];
            $this->error_messages['content'] = __('This SSL Certificate not for this domain');
            return false;
        }

    }

    public function listSSL($domain_id = null, $nested = null): array
    {
        $datatable = new Datatable();
        if($nested) {
            $delete_button = $datatable->buttons("certificate/delete/$1/domain/$domain_id",'ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons("certificate/install/$1/domain/$domain_id",'normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }
        else {
            $delete_button = $datatable->buttons('certificate/delete/$1','ajax',array('i' => 'fa fa-times','a' => 'text-danger'),__('Delete'));
            $edit_button = $datatable->buttons('certificate/install/$1','normal',array('i' => 'fa fa-edit','a' => 'text-success'),__('Edit'),null,'data-id="nested"');
        }

        $datatable->select([
            "certificates.id",
            "certificates.name",
            "certificates.common_name",
            "certificates.valid_from",
            "certificates.valid_to",
        ])
            ->from("certificates")
            ->where("certificates.domain_id = '$domain_id'")
            ->searchColumn(['certificates.name','certificates.common_name'])
            ->addColumn('actions', $edit_button. $delete_button,'certificates.id');

        if($nested) {
            $datatable->editColumn('certificates.name','<a href="'. "certificate/install/$1/domain/$domain_id\">$2</a>", 'certificates.id, certificates.name');
        }
        else {
            $datatable->editColumn('certificates.name','<a href="'. 'certificate/install/$1">$2</a>', 'certificates.id, certificates.name');
        }

        $data = $datatable->generate()
            ->execute()
            ->result();

        foreach($data['aaData'] as $key => $value){
            $data['aaData'][$key][0] = '<input type="checkbox" value="' . $value[0] . '" name="certificates[]" class="checkbox-blue"/>';
            $data['aaData'][$key][3] = $this->date->tsToDate($value[3]);
            $data['aaData'][$key][4] = $this->date->tsToDate($value[4]);

        }
        return $data;
    }

    public function parseRequest($str, $from, $to)
    {
        $sub = substr($str, strpos($str, $from)+strlen($from),strlen($str));
        return substr($sub,0,strpos($sub, $to));
    }

    public function requestCSRCurl()
    {
        $sslmng = new SslMng();
        $domain = Domains::findFirstById($this->getPost("domain_id"));
        $sslmng->setServerId($domain->web_server_id);
        $params = array(
            'domain' => $this->getPost("domain_name"),
            'country' => $this->getPost("country"),
            'state' => $this->getPost("state"),
            'city' => $this->getPost("city"),
            'companyname' => $this->getPost("company"),
            'department' => $this->getPost("department"),
            'crypto' => $this->getPost("bit"),
            'ssl-name' => $this->getPost("name"),
            'email' => $this->getPost("email")
        );

        return $sslmng->createCSR($params);
    }

    private function validationRequest(): bool
    {
        $required = array(
            array(
                'item' => $this->getPost("domain_name"),
                'field' => 'certificate_domain_name',
                'message' => __('Domain name is required')
            ),
            array(
                'item' => $this->getPost("name"),
                'field' => 'certificate_name',
                'message' => __('Certificate name is required')
            ),
            array(
                'item' => $this->getPost("bit"),
                'field' => 'certificate_keysize',
                'message' => __('Key size is required')
            ),
            array(
                'item' => $this->getPost("country"),
                'field' => 'certificate_country',
                'message' => __('Country is required')
            ),


            array(
                'item' => $this->getPost("state"),
                'field' => 'certificate_state',
                'message' => __('State is required')
            ),
            array(
                'item' => $this->getPost("city"),
                'field' => 'certificate_city',
                'message' => __('City is required')
            ),
            array(
                'item' => $this->getPost("company"),
                'field' => 'certificate_company',
                'message' => __('Company name is required')
            ),
            array(
                'item' => $this->getPost("department"),
                'field' => 'certificate_department',
                'message' => __('Department is required')
            ),
        );
        $this->validation->Required($required);
        $domain_name = $this->getPost("domain_name");

        if(strpos($this->getPost("domain_name"),'*.') !== false) {
            $domain_name = str_replace('*.','', $this->getPost("domain_name"));
        }
        $this->validation->Domain($domain_name,'certificate_domain_name')
            ->Username($this->getPost("name"),'certificate_domain_name',__("Please enter valid certificate name. Don't use space and special characters."));
        if(!$this->validation->Validate()) {
            $this->error_messages = $this->validation->getMessage();
            return false;
        }
        return true;
    }
}