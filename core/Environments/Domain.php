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
 */namespace FPanel\Environments;
use FPanel\Models\Domains;
use Phalcon\Text;

/**
 * Class Domain
 * @package FPanel\Environments
 */
trait Domain
{
    /**
     * @param $domain_name
     */
    public function checkDomain($domain_name)
    {
        $domains = Domains::findFirst("name = '$domain_name'");
        if($domains) {
            echo '<span data-toggle="tooltip" title="'. __('This domain already exists on system.').'"><i class="fa fa-exclamation-circle text-danger"></i></span>';
        }
    }

    /**
     * @param $dir
     * @return bool
     */
    public function isDirectoryEmpty($dir): bool
    {
        if (!is_readable($dir))
            return false;
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return false;
            }
        }
        return true;
    }

    public function isDomain($domain_name): bool
    {
        $status = preg_match('/^ (?: [a-z0-9] (?:[a-z0-9\-]* [a-z0-9])? \. )*  #Subdomains
   							[a-z0-9] (?:[a-z0-9\-]* [a-z0-9])?            #Domain
   							\. [a-z]{2,6} $                               #Top-level domain
							/ix', $domain_name);
        if(!$status) {
            return false;
        }
        return true;
    }

    public function createPrefix(): string
    {
        $prefix = strtolower(Text::random(Text::RANDOM_ALNUM));
        if (is_numeric($prefix[0])) {
            return $this->createPrefix();
        }
        return $prefix;
    }
}