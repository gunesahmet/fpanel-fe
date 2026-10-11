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
 */namespace FPanel\Translate;

use Exception;
use Phalcon\Mvc\User\Component;
use Phalcon\Translate\Adapter\NativeArray;

/**
 * Class Translate
 * @package SBYS\Translate
 */
class Translate extends Component
{
    private $lang_data;

    public function _getTransPath($language)
    {
        $translation_path = ROOTPATH . "/languages/$language.json";

        if (!file_exists($translation_path)) {
            return false;
        }
        return $translation_path;
    }

    public function initPath()
    {
        if ($this->fcookies->has("language")) {
            $language = $this->fcookies->get("language");
        }
        else {
            $this->fcookies->set("language", 'en_US', time() + 30 * 86400);
            $language = "en_US";
        }

        $translation_path = $this->_getTransPath($language);
        if ($translation_path != false) {
            $translate_data = file_get_contents($translation_path);
            $this->lang_data = trim($translate_data);
            if ($this->lang_data != null) {
                $this->lang_data = json_decode($this->lang_data, true);
            }
        }
    }

    public function loadCustomTranslate($key, $bind = null): string
    {
        try {
            if ($this->lang_data != null) {
                $translator = new NativeArray(array(
                    "content" => $this->lang_data
                ));
                if (!empty($bind)) {
                    return $translator->_($key, $bind);
                }
                else {
                    $translated = $translator->_($key);
                    if ($translated != null) {
                        return $translated;
                    }
                    else
                        return $key;
                }
            }
            return $key;
        }
        catch (Exception $e) {
            return $key;
        }
    }

    public function getLangList(): array
    {
        if ($handle = opendir(ROOTPATH . '/languages')) {
            $country_list = $this->codeList();

            $installed_lang_list = array();
            while (false !== ($lang = readdir($handle))) {
                $lang = str_replace(".json","", $lang);
                if ($lang != "." && $lang != "..") {
                    if (array_key_exists($lang, $country_list)) {
                        $installed_lang_list[$this->crypto->staticEncode($lang)] = $country_list[$lang];
                    }
                }
            }
            closedir($handle);
            if (empty($installed_lang_list)) {
                $installed_lang_list = array(
                    $this->crypto->staticEncode("en_US") => 'English',
                );
            }
        } else {
            $installed_lang_list = array(
                $this->crypto->staticEncode("en_US") => 'English',
            );
        }
        return $installed_lang_list;
    }

    public function codeList(): array
    {
        return array(
            'zh_CN' => 'Chinese',
            'cs_CZ' => 'Czech',
            'nl_NL' => 'Dutch',
            'en_US' => 'English',
            'et_EE' => 'Estonian',
            'fr_FR' => 'French',
            'de_DE' => 'German',
            'el_GR' => 'Greek',
            'hu_HU' => 'Hungarian',
            'id_ID' => 'Indonesian',
            'it_IT' => 'Italian',
            'ja_JP' => 'Japanese',
            'ko_KR' => 'Korean',
            'pt_PT' => 'Portuguese',
            'ro_RO' => 'Romanian',
            'ru_RU' => 'Russian',
            'es_ES' => 'Spanish',
            'tr_TR' => 'Turkish',
        );
    }

    public function codeListTranslate($language_code): string
    {
        $lang_list = array(
            'zh_CN' => 'Chinese',
            'cs_CZ' => 'Czech',
            'nl_NL' => 'Dutch',
            'en_US' => 'English',
            'et_EE' => 'Estonian',
            'fr_FR' => 'French',
            'de_DE' => 'German',
            'el_GR' => 'Greek',
            'hu_HU' => 'Hungarian',
            'id_ID' => 'Indonesian',
            'it_IT' => 'Italian',
            'ja_JP' => 'Japanese',
            'ko_KR' => 'Korean',
            'pt_PT' => 'Portuguese',
            'ro_RO' => 'Romanian',
            'ru_RU' => 'Russian',
            'es_ES' => 'Spanish',
            'tr_TR' => 'Turkish',
        );

        return $lang_list[$language_code];

    }
}
