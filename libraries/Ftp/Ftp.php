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
 */namespace FPanel\Ftp;

use Exception;
use FPanel\File\Basic;
use FPanel\File\Config;
use FPanel\File\Request;
use FPanel\Library\Library;
use FPanel\Models\Domains;
use FPanel\Models\FtpUsers;
use FPanel\Models\IpAddress;
use FPanel\Models\Subdomains;
use FPanel\Process\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Ftp extends Library
{

    use Linux,
        Windows;

    private $domain_name;
    private $domain_id;
    public $nested = false;
    private $file_content = array();
    private $chunked_file_name = null;
    private $server_os;

    public function setOs($server_os)
    {
        $this->server_os = $server_os;
    }

    public function getChunkedFileName()
    {
        return $this->chunked_file_name;
    }

    private function generateFolderList($root_folder, $folders, $result = [])
    {
        $i = 0;
        foreach($folders as $value)  {

            $dir = $value["name"];
            $childs = $value["childs"];

            if($dir == "conf")
                continue;
            if($dir == "stats")
                continue;


            $dir_arr = [
                'id' => $this->crypto->encryptSession($root_folder.DIR_SEPARATOR.$dir),
                'text' => $dir,
                'icon' => "fa fa-folder text-primary",
                'li_attr' => array('data-url' => $this->crypto->encryptSession($root_folder.DIR_SEPARATOR.$dir), "data-name" => $this->env->replaceAll("//","/", $root_folder.DIR_SEPARATOR.$dir)),
                'state' => array('opened' => false),
                'children' => []
            ];
            $result[$i] = $dir_arr;
            if(!empty($childs)) {
                $rec = $this->generateFolderList($root_folder.DIR_SEPARATOR.$dir, $childs , $result[$i]['children']);
                if(!empty($rec))
                    $result[$i]['children'] = $rec;
            }
            $i++;
        }
        return $result;
    }

    private function folders($folder, Domains $domains)
    {
        if($folder == null) {
            $folder = "/";
        }

        $filemng = new File();
        $filemng->setServerId($domains->web_server_id);
        $params = [
            "domain" => $this->domain_name,
            'prefix' => $domains->prefix,
            "dir" => $folder
        ];

        $response = $filemng->folderList($params);

        $dirs = json_decode($response, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            return [];
        }

        return $this->generateFolderList($folder, $dirs["childs"], []);
    }

    public function files($folder, Domains $domains): array
    {
        if($folder == null) {
            $folder = "/";
        }
        $filemng = new File();
        $filemng->setServerId($domains->web_server_id);
        $params = [
            "domain" => $this->domain_name,
            'prefix' => $domains->prefix,
            "dir" => $folder
        ];

        $response = $filemng->fileList($params);


        $dirs = json_decode($response, true);
        if(json_last_error() != JSON_ERROR_NONE) {
            return array();
        }
        $childs = $dirs["childs"];

        $file_list = array();
        if(!empty($childs)) {
            foreach($childs as $value)  {

                $name = $value["name"];
                $type = $value["type"];
                $size = $value["size"];
                $mod_time = $value["mod_time"];

                if($folder == "/") {
                    if($name == "conf")
                        continue;
                    if($name == "stats")
                        continue;
                }

                if ($type == "file") {
                    $fileName = explode('.', $name);
                    if(count($fileName) < 2) {
                        $file_name = $name;
                        if(strlen($file_name) > 10)
                            $file_name = substr($file_name, 0, 10) . '...';
                    }
                    else {
                        $file_name_ext = end($fileName);
                        $file_name = str_replace("$file_name_ext.",'', $name);
                        if(strlen($file_name) > 10)
                            $file_name = substr($file_name, 0, 10) . '...'.$file_name_ext;
                    }

                    if(strlen($file_name) + 10 >= strlen($name)) {
                        $file_name = $name;
                    }

                    $mime = $this->getMime($name);
                    $file_list[] = array(
                        'file_name' => $file_name,
                        'modified_date' => $mod_time,
                        'size' => $this->env->convert($size,"0 B"),
                        'rsize' => $size,
                        'mime' => $mime,
                        'clear-name' => $name,
                        'id' => $name,
                        'path' => $this->crypto->encryptSession($folder.DIR_SEPARATOR.$name),
                        'type' => $this->getType($name),
                    );
                }
                else {
                    $file_name = $name;
                    if(strlen($file_name) > 10)
                        $file_name = substr($file_name, 0, 10) . '...';

                    $file_list[] = array(
                        'file_name' => $file_name,
                        'modified_date' => $mod_time,
                        'size' => "",
                        'rsize' => "0",
                        'mime' => "folder",
                        'id' => $name,
                        'clear-name' => $name,
                        'path' => $this->crypto->encryptSession($folder.DIR_SEPARATOR.$name),
                        "type" => __("File Folder"),
                    );
                }

            }
            return $file_list;
        }
        else {
            return array();
        }
    }

    private function getType($file_name): string
    {
        $file_types = [
            "txt" => "Plain text",
            "as,ms" => "Flash ActionScript file",
            "asm" => "Assmbly language source file",
            "mib" => "Abstrant syntax Natation One fiel",
            "asp,asp" => "Active Server Pages Script file",
            "au3" => "Autolt",
            "avs,avsi" => "AviSynth script file",
            "bc,ch" => "BaanC file",
            "bash,sh,bsh,csh,bash,_profile,bashac,profile" => "Unix script file",
            "bat,cmd,nt" => "Batch file",
            "bb" => "Blitz file",
            "c,lex" => "C source file",
            "ml,mli,sml,thy" => "Categorical Abstrant Machine Language",
            "cmake" => "CMake file",
            "cbl,cdb,cbd,cdc,cob" => "Common Business Oriented Language",
            "orc,sco,csd" => "Csound file",
            "coffee,litcoffee" => "CoffeeScript file",
            "h,hh,hxx,cpp,cxx,cc,ino" => "C++ source file",
            "cs" => "C# source file",
            "css" => "Cascade Style Sheets file",
            "d" => "D progarmming  language",
            "dff,patch" => "Diff file",
            "erl,hrl" => "Erlang file",
            "src,em" => "ESCRIPT file",
            "forth" => "Fortran file",
            "f,for,f90,f95,f2k,f23" => "Fortran free form source file",
            "f77" => "Fortran fixed form source file",
            "bas,bi" => "Free Basic file",
            "hs,lhs,las" => "Haskell",
            "html,htm,shtml,shtm,xhtml,xht,hta" => "Hyper Text Markup Language file",
            "ini,inf,url,wer" => "MS ini file",
            "lss" => "Inno setup script",
            "hex" => "Intel HEX binary date",
            "java" => "Java source file",
            "js,jsm,jsx,ts,tsx" => "JavaScript file",
            "json" => "JSON file",
            "jsp" => "Java Server Pages script file",
            "kix" => "KiXtart file",
            "lsp,lisp" => "List Processing Language file",
            "tex,sty" => "La Tex file",
            "lua" => "Lva source File",
            "mak" => "Make file",
            "m" => "MATrix LABoratoey",
            "mms" => "MAIXAL file",
            "nim" => "Nimrod file",
            "tab,spf" => "extended crontable file",
            "nfo" => "MSDOS style/ASCII Art",
            "nsi,nsh" => "Nullsoft Scriptable Install System Script file",
            "osx" => "DScript source file",
            "mn" => "Objective-C source file",
            "pas,pp,p,ins,lpr" => "Pascal source file ",
            "pl,pm,plx" => "Perl source file",
            "php,php3,php4,php5,phps,phpt,phtml" => "PHP Hyper Text Preprocessor file",
            "ps" => "Post Script file",
            "ps1,psm1" => "Windows power Shell",
            "properties" => "Properties file",
            "pb" => "Pure Basic file",
            "py,pyw" => "Python file",
            "r,s,splus" => "R programming language",
            "r2,r3,reb" => "REBOL file",
            "reg" => "registry file",
            "rc" => "Windows Resource file",
            "rb,rbw" => "Ruby file",
            "rs" => "Rust file",
            "scam,out" => "Scheme",
            "st" => "Smoltalk file",
            "scp,out" => "Spice file",
            "sql" => "Structured Query Language file",
            "mot,srec" => "Motorola S-Record binary data",
            "swift" => "Swift file",
            "tcl" => "Tool Command Language file",
            "tek" => "Tektronix extended HEX binary data",
            "tex" => "Tex file",
            "vb,vbs" => "Visual Basic file",
            "t2t" => "Txt2tags file",
            "v,sv,vh,svh" => "Verilog file",
            "vhd,vhdl" => "VHSIC Hardware Description Language file",
            "pro,cl,i,pack,ph" => "Visual Prolog file",
            "xml,xaml,xsl,xslt,xsd,xul,kml,svg,mxml,xsml,wsdl,xlf,xliff,xbl,sxbl,sitemap,gml,gpx,plist" => "eXtensible Markup Language file",
            "yml,yaml" => "YAML Aint Markup lang",
            "psd,pdd" => "Photoshop",
            "psb" => "Large Document Format",
            "bmp,rle,dib" => "BMP",
            "gif" => "Compu Server GIF",
            "eps" => "Photoshop DCS 1.0,2.0",
            "iff,tdi" => "IFF Format",
            "jpg,jpeg,jpe" => "JPEG",
            "jpg,jpx,jp2,j2c,j2k,jpc" => "JPEG 200",
            "jps" => "JPEG Stereo",
            "mpo" => "Multi-Picture Format",
            "pcx" => "PCX",
            "pdf,pdp" => "Photoshop PDF",
            "rew" => "Photoshop Raw",
            "pxr" => "Pixar",
            "png,pns" => "PNG",
            "pbm,pgm,ppm,pnm,pfm,pam" => "Portable Bit Map",
            "sct" => "ScitexCT",
            "tga,vda,icb,vst" => "Targa",
            "tif,tiff" => "TIFF",
            "m4v" => "Apple video files",
            "m4v,m4b,m4p,m4r" => "Apple Music files",
            "aa" => "Audible Audio",
            "aax" => "Audible Enhanced Format",
            "amr" => "Adaptive Multi-Rate file",
            "mod" => "MOD file",
            "tod" => "TOD file",
            "mts,ts,m2ts,m2t,tp,trp" => "High-definition files",
            "avi" => "AVI Video files",
            "mp4,m4v" => "MPEG4 file ",
            "mpg,mpeg"  => "MPEG files",
            "3gp,3g2" => "3GP files",
            "wmv,asf" => "Windows Media Video",
            "dat" => "Real Media files",
            "mov" => "Quick Time files",
            "flv,f4v" => "Flas video files",
            "mkv" => "MKV files",
            "dv" => "Digital video files",
            "dvr-ms" => "Microsoft Digital video Recorder",
            "h.264" => "Raw h.264 Video Files",
            "nsv" => "Nullsoft stream video",
            "vob" => "Unprotected VOB File",
            "mp3,m4a,mp2,mpa" => "MPEG Audio Files",
            "wma" => "Windows Media Audio  File",
            "ac3" => "Advanced Audio Coding File",
            "mka" => "Matroska Audio File",
            "ogg" => "OGG Vorbis Audio File",
            "aif,aiff" => "Audio Interleave File Formats",
            "ra,ram" => "Real Audio Files",
            "tivo" => "Tivo File",
            "wav,ape,flac" => "Lossless Audio Files",
            "webm" => "WEBM Files",
            "ogv" => "OGV Files",
            "mxf" => "MXF Files",
            "vro" => "VRO Files",
            "wtv" => "WTV Files",
            "cat" => "CAF Files",
            "csv" => "CSV Files",
            "docx" => "Word Document",
            "xlsx" => "Excel Document",
            "docm" => "Word Macro-Enabled Document",
            "doc" => "Word 97-2003 Document",
            "xls" => "Excel 97-2003 Document",
            "dotx" => "Word Template",
            "dotm" => "Word Macro-Enabled Template",
            "pdf" => "PDF",
            "xps" => "XPS Document",
            "mht,mhtml" => "Single File Web Page",
            "htm,html" => "Web Page",
            "rtf" => "Rich Text Format",
            "odt" => "Open Document Text",
            "wps" => "Words 6-9 Document",
            "pst,dbx,emlx" => "Email file",
            "zip,rar,sit" => "Archive file",
            "torrent" => "Torrent file",
            "webp" => "Raster image format file",
            "vpn,ovpn" => "VPN Config file",
        ];

        $file_name_exploded = explode(".", $file_name);

        $file_ext = array_pop($file_name_exploded);
        foreach ($file_types as $ext => $file_type) {
            $extensions = explode(",", $ext);
            if(in_array($file_ext, $extensions)) {
                return $file_type;
            }
        }
        return __("File");
    }

    private function getMime($file): string
    {
        $mime_types = array(
            'txt' => 'file-alt',
            'htm' => 'file-code',
            'html' => 'file-code',
            'php' => 'file-code',
            'pl' => 'file-code',
            'py' => 'file-code',
            'css' => 'file-code',
            'js' => 'file-code',
            'json' => 'file-code',
            'xml' => 'file-code',
            'swf' => 'file-video',
            'flv' => 'file-video',

            // images
            'png' => 'file-image',
            'jpe' => 'file-image',
            'jpeg' => 'file-image',
            'jpg' => 'file-image',
            'gif' => 'file-image',
            'bmp' => 'file-image',
            'ico' => 'file-image',
            'tiff' => 'file-image',
            'tif' => 'file-image',
            'svg' => 'file-image',
            'svgz' => 'file-image',

            // archives
            'zip' => 'file-archive',
            'rar' => 'file-archive',
            'exe' => 'file',
            'msi' => 'file-archive',
            'cab' => 'file-archive',

            // audio/video
            'mp3' => 'file-audio',
            'qt' => 'file-audio',
            'mov' => 'file-video',

            // adobe
            'pdf' => 'file-pdf',
            'psd' => 'file-image',
            'ai' => 'file-image',
            'eps' => 'file-image',
            'ps' => 'file-image',

            // ms office
            'doc' => 'file-word',
            'docx' => 'file-word',
            'rtf' => 'file-word',
            'xls' => 'file-excel',
            'xlsx' => 'file-excel',
            'ppt' => 'file-powerpoint',
            'pptx' => 'file-powerpoint',
            'csv' => 'file-csv',

            // open office
            'odt' => 'file-word',
            'ods' => 'file-excel',
        );
        $ext_arr = explode('.', $file);
        $ext = strtolower(end($ext_arr));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        else {
            return 'file';
        }

    }

    public function getFolders($domain_id, $domain_name): array
    {
        $domains = Domains::findFirstById($domain_id);
        if(!$domains) {
            return [
                'id' => 'root_folder',
                'text' => "/",
                'icon' => "fa fa-folder text-primary",
                'li_attr' => array('data-url' => ""),
                'state' => array('opened' => true),
                'children' => []
            ];
        }
        $this->domain_name = $domain_name;
        return array(
            'id' => 'root_folder',
            'text' => "/",
            'icon' => "fa fa-folder text-primary",
            'li_attr' => array('data-url' => ""),
            'state' => array('opened' => true),
            'children' => $this->folders("", $domains),
        );
    }

    public function getFiles($folder, $domain_name): array
    {
        $this->domain_name = $domain_name;

        $domains = Domains::findFirst("name = '$domain_name'");
        $this->domain_id = $domains->id;
        if($folder != null) {
            try {
                $folder = $this->crypto->decryptSession(trim($folder));
            }
            catch(Exception $e) {
                return array(__('Folder was not found'));
            }
        }

        $folder = str_replace("//", "/", $folder);
        $files = $this->files($folder, $domains);

        return array(
            'path' => $folder,
            'parent' => $this->crypto->encryptSession($this->getParent($folder)),
            'current_path' => $this->crypto->encryptSession($folder),
            'files' => $files
        );
    }

    private function getParent($folder): string
    {
        $parent = "";
        if($folder != null) {
            $folder_arr = explode('/', $folder);
            array_pop($folder_arr);
            foreach($folder_arr as $dir) {
                if($dir == null)
                    continue;

                if($parent == null)
                    $parent = '/'.$dir;
                else
                    $parent = $parent.'/'.$dir;

            }
        }
        else {
            $parent = '/';
        }
        return $parent;
    }

    public function renameFileOrFolder($name, $new_name, $domain_name, $type, Domains $domains): bool
    {
        $full_path = $this->crypto->decryptSession(trim($name));
        $current_folder = $this->env->replaceAll("//","/", $full_path);

        if($this->server_os == WINDOWS) {
            $full_current_folder = $this->env->replaceAll("\\","/", $current_folder);
            $full_current_folder = $this->env->replaceAll("\\","/", $full_current_folder);
            $full_current_folder = $this->env->replaceAll("//","/", $full_current_folder);
        }
        else {
            $full_current_folder = $this->env->replaceAll("//","/", $current_folder);
        }

        $new_name = trim($new_name);
        $name_array = explode('/', $full_current_folder);
        $name = end($name_array);
        $dir = str_replace($name, "", $full_current_folder);


        $result = "";
        $can_rename = false;
        if(isset($name_array[0])) {
            if((isset($name_array[1])) && ($name_array[1] != null)) {
                if(($name_array[1] == "vhost") && ((isset($name_array[2])) && ($name_array[2] != null))) {
                    $can_rename = true;
                }
                if(($name_array[1] == "cgi-bin") && ((isset($name_array[2])) && ($name_array[2] != null))) {
                    $can_rename = true;
                }
                if(($name_array[1] == "error-docs") && ((isset($name_array[2])) && ($name_array[2] != null))) {
                    $can_rename = true;
                }
                else if((isset($name_array[2])) && ($name_array[2] != null)) {
                    if((isset($name_array[3])) && ($name_array[3] != null)) {
                        if((isset($name_array[4])) && ($name_array[4] != null)) {
                            $can_rename = true;
                        }
                    }
                }
            }
            else if (($name_array[1] == "cgi-bin") || ($name_array[1] == "error-docs")) {
                if (isset($name_array[2])) {
                    $can_rename = true;
                }
            }
        }

        if(!$can_rename) {
            $this->error_messages[$name] = __('Can not rename this file or folder');
            return false;
        }

        if(($name == "/") || ($name == null)) {
            $this->error_messages['content'] = __('File or Directory name is requiered');
            return false;
        }
        if(($new_name == "/") || ($new_name == null)) {
            $this->error_messages['content'] = __('New File or New Directory name is requiered');
            return false;
        }

        if($name == $new_name) {
            $this->error_messages['content'] = __('New name and old name is not different');
            return false;
        }
        if(!$this->validateFile($new_name)) {
            return false;
        }
        $file = new File();
        $file->setServerId($domains->web_server_id);
        if($type == "file") {
            $params = [
                "domain" => $domain_name,
                'prefix' => $domains->prefix,
                "file-name" => $name,
                "new-file-name" => $new_name,
                "dir" => $dir,
            ];
            $result = $file->renameFile($params);
        }
        else if($type == "folder") {
            $params = [
                "domain" => $domain_name,
                'prefix' => $domains->prefix,
                "folder-name" => $name,
                "new-folder-name" => $new_name,
                "dir" => $dir,
            ];
            $result = $file->renameFolder($params);
        }
        if(trim($result) == null) {
            return true;
        }
        else {
            return false;
        }
    }

    public function createFileOrFolder($name, $domain_name, $type, $current_folder, Domains $domains): bool
    {
        $current_folder = $this->crypto->decryptSession(trim($current_folder));
        $current_folder = $this->env->replaceAll("//","/", $current_folder);
        if($this->server_os == WINDOWS) {
            $current_folder = $this->env->replaceAll("\\","/", $current_folder);
            $current_folder = $this->env->replaceAll("//","/", $current_folder);
            $full_current_folder = $this->env->replaceAll("\\","/", $current_folder . DIR_SEPARATOR . $name);
            $full_current_folder = $this->env->replaceAll("\\","/", $full_current_folder);
        }
        else {
            $full_current_folder = $current_folder . DIR_SEPARATOR . $name;
        }
        $full_current_folder = $this->env->replaceAll("//","/", $full_current_folder);

        $current_folder_array = explode('/', $full_current_folder);

        $domain_user = true;
        $can_create = false;
        if(isset($current_folder_array[0])) {
            if((isset($current_folder_array[1])) && ($current_folder_array[1] != null)) {
                if(($current_folder_array[1] == "vhost") && ((isset($current_folder_array[2])) && ($current_folder_array[2] != null))) {
                    $can_create = true;
                }
                if(($current_folder_array[1] == "cgi-bin") && ((isset($current_folder_array[2])) && ($current_folder_array[2] != null))) {
                    $can_create = true;
                }
                if(($current_folder_array[1] == "error-docs") && ((isset($current_folder_array[2])) && ($current_folder_array[2] != null))) {
                    $can_create = true;
                }
                else if((isset($current_folder_array[2])) && ($current_folder_array[2] != null)) {
                    if((isset($current_folder_array[3])) && ($current_folder_array[3] != null)) {
                        if((isset($current_folder_array[4])) && ($current_folder_array[4] != null)) {
                            $can_create = true;
                            if($current_folder_array[1] == "subdomains")
                                $domain_user = false;
                        }

                    }
                }
            }
            else if (($current_folder_array[1] == "cgi-bin") || ($current_folder_array[1] == "error-docs")) {
                if (isset($current_folder_array[2])) {
                    $can_create = true;
                }
            }
        }

        if(!$can_create) {
            $this->error_messages[$name] = __('Can not create this file or folder');
            return false;
        }

        $result = "";
        if(($type == "file") || ($type == "folder")) {
            if($domain_user) {
                $ftp_users = FtpUsers::findFirstById($domains->ftp_user_id);
                if($ftp_users) {
                    $username = $domains->getFtpUsers()->username;
                }
                else {
                    $this->error_messages[$name] = __('Ftpuser not found');
                    return false;
                }
            }
            else {
                $subdomainname = $current_folder_array[2];
                $subdomains = Subdomains::findFirst("subdomain_name = '$subdomainname' AND domain_id = '$domains->id'");
                if($subdomains) {
                    $ftp_users = FtpUsers::findFirstById($subdomains->ftpuser_id);
                    if($ftp_users) {
                        $username = $ftp_users->username;
                    }
                    else {
                        $this->error_messages[$name] = __('Ftpuser not found');
                        return false;
                    }
                }
                else {
                    $this->error_messages[$name] = __('Ftpuser not found');
                    return false;
                }
            }
            if($username == null) {
                $this->error_messages[$name] = __('Ftpuser not found');
                return false;
            }

            $file = new File();
            $file->setServerId($domains->web_server_id);
            if($type == "file") {
                if(!$this->validateFile($name)) {
                    return false;
                }
                if($this->server_os == WINDOWS) {
                    $current_folder = $this->env->replaceAll("\\","/", $current_folder);
                    $current_folder = $this->env->replaceAll("//","/", $current_folder);
                }

                $params = [
                    "domain" => $domain_name,
                    "prefix" => $domains->prefix,
                    "file-name" => $name,
                    "dir" => $current_folder,
                    "user" => $username,
                ];
                $result = $file->createFile($params);
            }
            else if($type == "folder") {
                if(!$this->validateFolder($name)) {
                    return false;
                }
                if($this->server_os == WINDOWS) {
                    $current_folder = $this->env->replaceAll("\\","/", $current_folder);
                    $current_folder = $this->env->replaceAll("//","/", $current_folder);
                }
                $params = [
                    "domain" => $domain_name,
                    "prefix" => $domains->prefix,
                    "folder-name" => $name,
                    "dir" => $current_folder,
                    "user" => $username,
                ];
                $result = $file->createFolder($params);
            }

            if(trim($result) == null) {
                return true;
            }
            else {
                $this->error_messages["content"] = $result;
                return false;
            }
        }
        else {
            $this->error_messages[$name] = __('Unknown type');
            return false;
        }
    }

    public function moveFileOrFolder($source, $target, $domain_name, Domains $domains): bool
    {
        $source = $this->crypto->decryptSession(trim($source));
        $target = $this->crypto->decryptSession(trim($target));
        $source = $this->env->replaceAll("//", "/", $source);
        $target = $this->env->replaceAll("//", "/", $target);

        if($this->server_os == WINDOWS) {
            $source = $this->env->replaceAll("\\","/", $source);
            $source = $this->env->replaceAll("\\","/", $source);
            $source = $this->env->replaceAll("//","/", $source);
            $target = $this->env->replaceAll("\\","/", $target);
            $target = $this->env->replaceAll("\\","/", $target);
        }
        else {
            $source = $this->env->replaceAll("//","/", $source);
        }
        $target = $this->env->replaceAll("//","/", $target);

        $source_array = explode('/', $source);
        $target_array = explode('/', $target);
        $source_name = end($source_array);
        $source_dir = str_replace($source_name, "", $source);


        $full_target = $target . DIR_SEPARATOR . $source_name;
        $full_target = $this->env->replaceAll("//", "/", $full_target);

        $domain_user = true;
        $can_move = false;
        if (isset($source_array[0])) {
            if ((isset($source_array[1])) && ($source_array[1] != null)) {
                if (($source_array[1] == "vhost") && ((isset($source_array[2])) && ($source_array[2] != null))) {
                    $can_move = true;
                } else if (($source_array[1] == "cgi-bin") && ((isset($source_array[2])) && ($source_array[2] != null))) {
                    $can_move = true;
                } else if (($source_array[1] == "error-docs") && ((isset($source_array[2])) && ($source_array[2] != null))) {
                    $can_move = true;
                } else if ((isset($source_array[2])) && ($source_array[2] != null)) {
                    if ((isset($source_array[3])) && ($source_array[3] != null)) {
                        if ((isset($source_array[4])) && ($source_array[4] != null)) {
                            $can_move = true;
                        }
                    }
                }
            }
        }

        if (!$can_move) {
            $this->error_messages[$source_name] = __('Can not move this file or folder');
            return false;
        }

        $can_move_target = false;
        if ((isset($target_array[1])) && ($target_array[1] != null)) {
            if ($target_array[1] == "vhost") {
                $can_move_target = true;
            } else if ($target_array[1] == "cgi-bin") {
                $can_move_target = true;
            } else if ($target_array[1] == "error-docs") {
                $can_move_target = true;
            } else if ((isset($target_array[2])) && ($target_array[2] != null)) {
                if ((isset($target_array[3])) && ($target_array[3] != null)) {
                    $can_move_target = true;
                    if($target_array[1] == "subdomains")
                        $domain_user = false;
                }
            }
        }

        if (!$can_move_target) {
            $this->error_messages[$source_name] = __('File or folder can not move this target');
            return false;
        }

        if ($source == $full_target) {
            $this->error_messages['content'] = __('Please select to different location');
            return false;
        }
        if (!$this->validateFile($source_name)) {
            return false;
        }

        if ($domain_user) {
            $ftp_users = FtpUsers::findFirstById($domains->ftp_user_id);
            if ($ftp_users) {
                $username = $ftp_users->username;
            }
            else {
                $this->error_messages[$source_name] = __('Ftpuser not found');
                return false;
            }
        }
        else {
            $subdomainname = $target_array[2];
            $subdomains = Subdomains::findFirst("subdomain_name = '$subdomainname' AND domain_id = '$domains->id'");
            if ($subdomains) {
                $ftp_users = FtpUsers::findFirstById($subdomains->ftpuser_id);
                if ($ftp_users) {
                    $username = $ftp_users->username;
                } else {
                    $this->error_messages[$source_name] = __('Ftpuser not found');
                    return false;
                }
            }
            else {
                $this->error_messages[$source_name] = __('Ftpuser not found');
                return false;
            }
        }

        if ($username == null) {
            $this->error_messages[$source_name] = __('Ftpuser not found');
            return false;
        }

        $file = new File();
        $file->setServerId($domains->web_server_id);
        $params = [
            "domain" => $domain_name,
            "source-file" => $source_name,
            'prefix' => $domains->prefix,
            "source-dir" => $source_dir,
            "target-dir" => $target,
            "user" => $username,
        ];

        $result = $file->moveFile($params);
        if(trim($result) == null) {
            return true;
        }
        else {
            return false;
        }
    }

    public function pasteFileOrFolder($source, $target, $domain_name): bool
    {
        //@TODO
        $source = $this->crypto->decryptSession(trim($source));
        $target = $this->crypto->decryptSession(trim($target));
        $default_path = VHOST_DIR."/$domain_name";
        $source = $default_path.$source;
        $target = $default_path.$target;

        $source_name = explode('/', $source);
        $full_target = $target.'/'.end($source_name);
        if(file_exists($full_target)) {
            $this->error_messages['content'] = __('File or folder already exists target location');
            return false;
        }

        if($source == $full_target) {
            $this->error_messages['content'] = __('Please select to different location');
            return false;
        }

        try {
            rename($source, $full_target);
        }
        catch(Exception $e) {
            $this->error_messages['content'] = __('File or folder can not move');
            return false;
        }
        return true;
    }

    public function deleteFileOrFolder($selecteds, $domain_name, Domains $domains): bool
    {
        if(empty($selecteds)) {
            $this->error_messages["content"] = __('Please select at least on file or directory');
            return false;
        }
        foreach ($selecteds as $selected) {
            $file_path = $selected["name"];
            $type = $selected["type"];
            if($this->server_os == WINDOWS) {
                $file_path = $this->crypto->decryptSession(trim($file_path));
                $file_path = $this->env->replaceAll("\\","/", $file_path);
            }
            else {
                $file_path = $this->crypto->decryptSession(trim($file_path));
            }

            $file_path = $this->env->replaceAll("//","/", $file_path);

            $file_path_array = explode('/', $file_path);
            $file_name = end($file_path_array);
            $file_path_dir = str_replace($file_name, "", $file_path);
            $result = "";
            if(($type == "file") || ($type == "folder")) {
                $can_remove = false;
                if(isset($file_path_array[0])) {
                    if((isset($file_path_array[1])) && ($file_path_array[1] != null)) {
                        if(($file_path_array[1] == "vhost") && ((isset($file_path_array[2])) && ($file_path_array[2] != null))) {
                            $can_remove = true;
                        }
                        else if(($file_path_array[1] == "cgi-bin") && ((isset($file_path_array[2])) && ($file_path_array[2] != null))) {
                            $can_remove = true;
                        }
                        else if(($file_path_array[1] == "error-docs") && ((isset($file_path_array[2])) && ($file_path_array[2] != null))) {
                            $can_remove = true;
                        }
                        else if((isset($file_path_array[2])) && ($file_path_array[2] != null)) {
                            if((isset($file_path_array[3])) && ($file_path_array[3] != null)) {
                                if((isset($file_path_array[4])) && ($file_path_array[4] != null)) {
                                    $can_remove = true;
                                }
                            }
                        }
                    }
                }

                if(!$can_remove) {
                    $this->error_messages[$file_name] = __('Can not remove this file or folder');
                    return false;
                }


                $file = new File();
                $file->setServerId($domains->web_server_id);
                if($type == "file") {
                    if(!$this->validateFile($file_name)) {
                        return false;
                    }

                    $params = [
                        "domain" => $domain_name,
                        "prefix" => $domains->prefix,
                        "file-name" => $file_name,
                        "dir" => $file_path_dir,
                    ];
                    $result = $file->removeFile($params);
                }
                else if($type == "folder") {
                    if(!$this->validateFolder($file_name)) {
                        return false;
                    }
                    $params = [
                        "domain" => $domain_name,
                        "prefix" => $domains->prefix,
                        "folder-name" => $file_name,
                        "dir" => $file_path_dir,
                    ];
                    $result = $file->removeFolder($params);
                }

                if(trim($result) != null) {
                    return false;
                }
            }
            else {
                $this->error_messages[$file_name] = __('Unknown type');
                return false;
            }
        }

        return true;
    }

    public function getFilePermission($file_name, $domain_name)
    {
        $file_name = $this->crypto->decryptSession(trim($file_name));

        if($this->server_os == LINUX) {
            return $this->getLinuxFilePermission($file_name, $domain_name);
        }
        else {
            return $this->getWindowsFilePermission($file_name, $domain_name);
        }
    }

    public function permissionFileOrFolderLinux($file_name, $domain_name, $chmod)
    {
        $file_name = $this->crypto->decryptSession(trim($file_name));
        return $this->setLinuxFilePermission($file_name, $domain_name, $chmod);
    }

    public function permissionFileOrFolderWindows($file_name, $domain_name, $permissions): bool
    {
        $file_name = $this->crypto->decryptSession(trim($file_name));
        return $this->setWindowsFilePermission($file_name, $domain_name, $permissions);
    }

    private function mimeContentType($filename): string
    {
        $mime_types = array(

            'txt' => 'text',
            'htm' => 'html',
            'html' => 'html',
            'php' => 'php',
            'py' => 'pl',
            'pl' => 'py',
            'css' => 'css',
            'js' => 'js',
            'json' => 'json',
            'xml' => 'xml',

            'doc' => 'word',
            'docx' => 'word',
            'rtf' => 'word',
            'xls' => 'excel',
            'xlsx' => 'excel',

            'odt' => 'word',
            'ods' => 'word',
        );

        $ext_arr = explode('.', $filename);
        $ext = strtolower(end($ext_arr));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        else {
            return 'file';
        }
    }

    public function editFile($file_path, $domain_name): bool
    {

        $file_path = $this->crypto->decryptSession(trim($file_path));
        $file_path = $this->env->replaceAll("//","/", $file_path);
        $file_name_array = explode('/', $file_path);
        $file_name = end($file_name_array);
        $dir = str_replace($file_name, "", $file_path);
        $file = new File();
        $domains = Domains::findFirst("name = '$domain_name'");
        $file->setServerId($domains->web_server_id);
        $params = [
            "domain" => $domain_name,
            'prefix' => $domains->prefix,
            "file-name" => $file_name,
            "dir" => $dir,
        ];
        if(!$this->validateFile($file_name)) {
            return false;
        }
        $result = $file->getFileContent($params);
        if($this->server_os == WINDOWS) {
            $content = base64_decode(trim($result));
        }
        else {
            $content = $this->crypto->staticDecode($result);
        }
        $content = $this->env->replaceAll(" ", "&nbsp;", $content);
        $this->file_content['content'] = trim($content);
        $this->file_content['type'] = $this->mimeContentType($file_name);
        return true;
    }

    public function saveFile($file_path, $domain_name, $clear_content): bool
    {
        $file_path = $this->crypto->decryptSession(trim($file_path));
        $file_path = $this->env->replaceAll("//","/", $file_path);
        if($this->server_os == WINDOWS) {
            $content = base64_encode($clear_content);
        }
        else {
            $content = $this->crypto->staticEncode($clear_content);
        }

        $file_name_array = explode('/', $file_path);
        $file_name = end($file_name_array);
        $dir = str_replace($file_name, "", $file_path);
        $domains = Domains::findFirst("name = '$domain_name'");
        $file = new File();
        $file->setServerId($domains->web_server_id);
        $params = [
            "domain" => $domain_name,
            'prefix' => $domains->prefix,
            "file-name" => $file_name,
            "file-content" => $content,
            "dir" => $dir,
        ];
        if(!$this->validateFile($file_name)) {
            return false;
        }

        $result = $file->saveFileContent($params);
        if(trim($result) == null) {
            return true;
        }
        else {
            return false;
        }
    }

    public function uploadFile($file_name, $folder, $domain_name): bool
    {
        if($folder == null) {
            $this->error_messages[$folder] = __('Can not found this file or folder');
            return false;
        }
        if($file_name == null) {
            $this->error_messages[$file_name] = __('Can not found this file or folder');
            return false;
        }

        $decrypted_folder = $this->crypto->decryptSession(trim($folder));
        $decrypted_folder = $this->env->replaceAll("//", "/", $decrypted_folder);
        $decrypted_folder = ltrim($decrypted_folder, "/");

        if(!$this->validateFile($file_name)) {
            return false;
        }

        if(!$this->validateFolder($decrypted_folder)) {
            return false;
        }

        $tmp_dir = ROOTPATH . DIR_SEPARATOR . 'tmp';
        $tmp_dir = $this->env->replaceAll("//", "/", $tmp_dir);
        $upload_dir = $tmp_dir . DIR_SEPARATOR . $domain_name . DIR_SEPARATOR . $folder . DIR_SEPARATOR;
        $upload_dir = $this->env->replaceAll("//", "/", $upload_dir);
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $config = new Config();
        $config->setTempDir($tmp_dir);
        $request = new Request();
        $basic = new Basic();
        if($basic->save($upload_dir .$file_name, $config, $request)) {
            $this->chunked_file_name = str_replace(ROOTPATH . "/tmp/", "", $basic->chunked_file_name);
            return true;
        }
        else {
            $this->error_messages[] = $basic->errors;
            return false;
        }
    }

    public function cancelUploadedFile($file_name, $folder, $domain_name): bool
    {
        $tmp_dir = ROOTPATH . DIR_SEPARATOR . 'tmp';
        $tmp_dir = $this->env->replaceAll("//", "/", $tmp_dir);
        $upload_dir = $tmp_dir . DIR_SEPARATOR . $domain_name . DIR_SEPARATOR . $folder . DIR_SEPARATOR;
        $upload_dir = $this->env->replaceAll("//", "/", $upload_dir);
        $exploded = explode("_", $file_name);
        unset($exploded[count($exploded)-1]);
        if(file_exists($upload_dir) && is_dir($upload_dir)) {
            rmdir($upload_dir);
        }
        $file_name_prefix = implode("_", $exploded);
        $files = preg_grep('~^' . $file_name_prefix ."~" , scandir($tmp_dir . "/"));
        foreach ($files as $file) {
            if(file_exists($tmp_dir . "/".$file)) {
                unlink($tmp_dir . "/".$file);
            }
        }
        return true;
    }

    public function uploadFtp($file_name, $folder, $domain_name): bool
    {
        $tmp_dir = ROOTPATH . DIR_SEPARATOR . 'tmp';
        $tmp_dir = $this->env->replaceAll("//", "/", $tmp_dir);
        $upload_dir = $tmp_dir . DIR_SEPARATOR . $domain_name . DIR_SEPARATOR . $folder . DIR_SEPARATOR;
        $upload_dir = $this->env->replaceAll("//", "/", $upload_dir);
        $source_file = $upload_dir.DIR_SEPARATOR.$file_name;
        $source_file = $this->env->replaceAll("//", "/", $source_file);

        if($folder == null) {
            $this->error_messages[$folder] = __('Can not found this folder');
            return false;
        }
        if($file_name == null) {
            $this->error_messages[$file_name] = __('Can not found this file');
            return false;
        }


        $decrypted_folder = $this->crypto->decryptSession(trim($folder));
        $decrypted_folder = $this->env->replaceAll("//", "/", $decrypted_folder);
        $current_folder_array = explode("/", $decrypted_folder);

        $domain_user = true;

        if(isset($current_folder_array[0])) {
            if((isset($current_folder_array[1])) && ($current_folder_array[1] != null)) {
                if((isset($current_folder_array[2])) && ($current_folder_array[2] != null)) {
                    if((isset($current_folder_array[3])) && ($current_folder_array[3] != null)) {
                        if((isset($current_folder_array[4])) && ($current_folder_array[4] != null)) {
                            if($current_folder_array[1] == "subdomains")
                                $domain_user = false;
                        }
                    }
                }
            }
        }
        $username = "";
        $password = "";
        $ip_address = "";
        $domains = Domains::findFirst("name = '$domain_name'");
        if($domains) {
            $sys_ip_address = IpAddress::findFirstById($domains->ipaddr_id);
            if ($sys_ip_address) {
                $ip_address = $sys_ip_address->ip_address;
            }
            else {
                $this->error_messages[$file_name] = __('Ip address not found');
                return false;
            }

            if ($domain_user) {
                $ftp_users = FtpUsers::findFirstById($domains->ftp_user_id);
                if ($ftp_users) {
                    $username = $ftp_users->username;
                    $password = $ftp_users->password;
                }
                else {
                    $this->error_messages[$file_name] = __('Ftpuser not found');
                    return false;
                }
            }
            else {
                $subdomainname = $current_folder_array[2];
                $subdomains = Subdomains::findFirst("subdomain_name = '$subdomainname' AND domain_id = '$domains->id'");
                if ($subdomains) {
                    $ftp_users = FtpUsers::findFirstById($subdomains->ftpuser_id);
                    if ($ftp_users) {
                        $username = $ftp_users->username;
                        $password = $ftp_users->password;
                    }
                    else {
                        $this->error_messages[$file_name] = __('Ftpuser not found');
                        return false;
                    }
                }
                else {
                    $this->error_messages[$file_name] = __('Ftpuser not found');
                    return false;
                }
            }
            if ($username == null) {
                $this->error_messages[$file_name] = __('Ftpuser not found');
                return false;
            }
        }

        $file = new File();
        // Ozel durum arka planda filemng upload isini halledecek
        $params = [
            "address" => $ip_address,
            "file-name" => $file_name,
            "dir" => $decrypted_folder,
            "user" => $username,
            "password" => $this->crypto->staticEncode($this->crypto->bdecode($password)),
            "source-file" => $source_file,
        ];

        $result = $file->uploadFtp($params);

        if(strpos($result, "[ERROR]") === false) {
            return true;
        }
        else {
            $results = explode("[ERROR]", $result);
            $this->error_messages["file"] = trim($results[1]);
            return false;
        }
    }

    public function getResult(): array
    {
        return $this->file_content;
    }

    private function validateFolder($name): bool
    {
        if (strpbrk($name, "\\/?%\$*:|\"<>^") === true) {
            $this->error_messages[$name] = __('Can not found this file or folder 1');
            header("HTTP/1.1 400 Bad Request");
            return false;
        }
        if(strpos($name, "..") !== false) {
            $this->error_messages[$name] = __('Can not found this file or folder 2');
            header("HTTP/1.1 400 Bad Request");
            return false;
        }
        if($name == "/") {
            $this->error_messages[$name] = __('Can not found this file or folder 3');
            header("HTTP/1.1 400 Bad Request");
            return false;
        }
        if(strpos($name, '$') !== false) {
            $this->error_messages[$name] = __('Can not found this file or folder 4');
            header("HTTP/1.1 400 Bad Request");
            return false;
        }
        return true;
    }

    private function validateFile($name): bool
    {
        if (strpbrk($name, "\\/?%*:|\"<>^") === true) {
            $this->error_messages[$name] = __('Can not found this file or folder');
            return false;
        }
        if(strpos($name, "..") !== false) {
            $this->error_messages[$name] = __('Can not found this file or folder');
            return false;
        }
        if(strpos($name, "/") !== false) {
            $this->error_messages[$name] = __('Can not found this file or folder');
            return false;
        }
        if(strpos($name, '$') !== false) {
            $this->error_messages[$name] = __('Can not found this file or folder');
            return false;
        }
        return true;
    }

    public function clearOldUploadedFiles(): bool
    {
        $tmp_dir = ROOTPATH . DIR_SEPARATOR . 'tmp';
        $tmp_dir = $this->env->replaceAll("//", "/", $tmp_dir);

        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmp_dir . "/"));

        foreach ($rii as $file) {
            if ($file->isDir()){
                continue;
            }
            else {
                if((time() - (int)filemtime($file->getPathname())) > 86400) {
                    unlink($file->getPathname());
                }
            }
        }
        return true;
    }
}