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
use Phalcon\Forms\Form;

/**
 * Class AspNetSettingsForm
 * @package FPanel\Forms
 */
class AspNetSettingsForm extends Form
{
    public function initialize($entity = null, array $options)
    {
        $this->connectionString();
        $this->customError($options);
        $this->compilation($options);
        $this->globalization($options);
        $this->codeAccess($options);
        $this->sessionSettings($options);
    }

    private function connectionString()
    {
        $connStrName = new Text('connStrName', array(
            'id' => 'connStrName',
            'name' => 'connStrName',
        ));

        $connectionString = new Text('connectionString' , array(
            'id' => 'connectionString',
            'name' => 'connectionString',
        ));
        $connectionStringProvider = new Text('connectionStringProvider' , array(
            'id' => 'connectionStringProvider',
            'name' => 'connectionStringProvider',
        ));

        $this->add($connectionStringProvider);
        $this->add($connectionString);
        $this->add($connStrName);

    }

    private function customError(array $options)
    {
        $custom_error_mode_value = $options['aspnet_settings']['customErrors']['customErrorMode']['mode'];
        switch($custom_error_mode_value) {
            case "0":
                $custom_error_mode_value = "RemoteOnly";
                break;
            case "1":
                $custom_error_mode_value = "On";
                break;
            case "2":
                $custom_error_mode_value = "Off";
                break;
        }

        $custom_error_mode = new Select('customErrMode',array(
            'On' => 'On',
            'Off' => 'Off',
            'RemoteOnly' => 'RemoteOnly',
        ),array(
            'id' => 'customErrMode',
            'name' => 'customErrMode',
        ));
        $custom_error_mode->setDefault($custom_error_mode_value);
        $this->add($custom_error_mode);

        $this->add(new Text('customErrCode', array(
            'id' => 'customErrCode',
            'name' => 'customErrCode',
        )));
        $this->add(new Text('customErrUrl', array(
            'id' => 'customErrUrl',
            'name' => 'customErrUrl',
        )));
    }

    private function compilation($options)
    {
        $compilation = $options['aspnet_settings']['compilation']['compilation'];
        $default_language = new Select('defaultLang',
            array(
                'c#' => 'c#',
                'vb' => 'vb',
            ),
            array(
                'id' => 'defaultLang',
                'name' => 'defaultLang',
            ));
        $default_language->setDefault($compilation['defaultLanguage']);
        $this->add($default_language);

        $switch_debug = new Check('switchDebug', array(
            'id' => 'switchDebug',
            'name' => 'switchDebug',
        ));

        if($compilation['debug'] != "False") {
            $switch_debug->setAttribute('checked','checked');
        }
        $this->add($switch_debug);
    }

    private function globalization($options)
    {
        $globalization = $options['aspnet_settings']['globalization']['globalization'];

        $requestEncoding = new Text('requestEncoding', array(
            'id' => 'requestEncoding',
            'name' => 'requestEncoding',
        ));

        $responseEncoding = new Text('responseEncoding', array(
            'id' => 'responseEncoding',
            'name' => 'responseEncoding',
        ));

        $file_encoding = new Text('fileEncoding', array(
            'id' => 'fileEncoding',
            'name' => 'fileEncoding',
        ));

        $culture_codes = array(
            'Invariant',
            'af-ZA' => 'Afrikaans (South Africa)',
            'sq-AL' => 'Albanian (Albania)',
            'gsw-FR' => 'Alsatian (France)',
            'am-ET' => 'Amharic (Ethiopia)',
            'ar-DZ' => 'Arabic (Algeria)',
            'ar-BH' => 'Arabic (Bahrain)',
            'ar-EG' => 'Arabic (Egypt)',
            'ar-IQ' => 'Arabic (Iraq)',
            'ar-JO' => 'Arabic (Jordan)',
            'ar-KW' => 'Arabic (Kuwait)',
            'ar-LB' => 'Arabic (Lebanon)',
            'ar-LY' => 'Arabic (Libya)',
            'ar-MA' => 'Arabic (Morocco)',
            'ar-OM' => 'Arabic (Oman)',
            'ar-QA' => 'Arabic (Qatar)',
            'ar-SA' => 'Arabic (Saudi Arabia)',
            'ar-SY' => 'Arabic (Syria)',
            'ar-TN' => 'Arabic (Tunisia)',
            'ar-AE' => 'Arabic (U.A.E.)',
            'ar-YE' => 'Arabic (Yemen)',
            'hy-AM' => 'Armenian (Armenia)',
            'as-IN' => 'Assamese (India)',
            'az-Cyrl-AZ' => 'Azeri (Cyrillic, Azerbaijan)',
            'az-Latn-AZ' => 'Azeri (Latin, Azerbaijan)',
            'ba-RU' => 'Bashkir (Russia)',
            'eu-ES' => 'Basque (Basque)',
            'be-BY' => 'Belarusian (Belarus)',
            'bn-BD' => 'Bengali (Bangladesh)',
            'bn-IN' => 'Bengali (India)',
            'bs-Cyrl-BA' => 'Bosnian (Cyrillic) (Bosnia and Herzegovina)',
            'bs-Latn-BA' => 'Bosnian (Latin) (Bosnia and Herzegovina)',
            'br-FR' => 'Breton (France)',
            'bg-BG' => 'Bulgarian (Bulgaria)',
            'ca-ES' => 'Catalan (Catalan)',
            'zh-HK' => 'Chinese (Hong Kong S.A.R.)',
            'zh-MO' => 'Chinese (Macao S.A.R.)',
            'zh-CN' => 'Chinese (People\'s Republic of China)',
            'zh-SG' => 'Chinese (Singapore)',
            'zh-TW' => 'Chinese (Taiwan)',
            'co-FR' => 'Corsican (France)',
            'hr-HR' => 'Croatian (Croatia)',
            'hr-BA' => 'Croatian (Latin) (Bosnia and Herzegovina)',
            'cs-CZ' => 'Czech (Czech Republic)',
            'da-DK' => 'Danish (Denmark)',
            'prs-AF' => 'Dari (Afghanistan)',
            'dv-MV' => 'Divehi (Maldives)',
            'nl-BE' => 'Dutch (Belgium)',
            'nl-NL' => 'Dutch (Netherlands)',
            'en-AU' => 'English (Australia)',
            'en-BZ' => 'English (Belize)',
            'en-CA' => 'English (Canada)',
            'en-029' => 'English (Caribbean)',
            'en-IN' => 'English (India)',
            'en-IE' => 'English (Ireland)',
            'en-JM' => 'English (Jamaica)',
            'en-MY' => 'English (Malaysia)',
            'en-NZ' => 'English (New Zealand)',
            'en-PH' => 'English (Republic of the Philippines)',
            'en-SG' => 'English (Singapore)',
            'en-ZA' => 'English (South Africa)',
            'en-TT' => 'English (Trinidad and Tobago)',
            'en-GB' => 'English (United Kingdom)',
            'en-US' => 'English (United States)',
            'en-ZW' => 'English (Zimbabwe)',
            'et-EE' => 'Estonian (Estonia)',
            'fo-FO' => 'Faroese (Faroe Islands)',
            'fil-PH' => 'Filipino (Philippines)',
            'fi-FI' => 'Finnish (Finland)',
            'fr-BE' => 'French (Belgium)',
            'fr-CA' => 'French (Canada)',
            'fr-FR' => 'French (France)',
            'fr-LU' => 'French (Luxembourg)',
            'fr-MC' => 'French (Principality of Monaco)',
            'fr-CH' => 'French (Switzerland)',
            'fy-NL' => 'Frisian (Netherlands)',
            'gl-ES' => 'Galician (Galician)',
            'ka-GE' => 'Georgian (Georgia)',
            'de-AT' => 'German (Austria)',
            'de-DE' => 'German (Germany)',
            'de-LI' => 'German (Liechtenstein)',
            'de-LU' => 'German (Luxembourg)',
            'de-CH' => 'German (Switzerland)',
            'el-GR' => 'Greek (Greece)',
            'kl-GL' => 'Greenlandic (Greenland)',
            'gu-IN' => 'Gujarati (India)',
            'ha-Latn-NG' => 'Hausa (Latin) (Nigeria)',
            'he-IL' => 'Hebrew (Israel)',
            'hi-IN' => 'Hindi (India)',
            'hu-HU' => 'Hungarian (Hungary)',
            'is-IS' => 'Icelandic (Iceland)',
            'ig-NG' => 'Igbo (Nigeria)',
            'id-ID' => 'Indonesian (Indonesia)',
            'iu-Cans-CA' => 'Inuktitut (Canada)',
            'iu-Latn-CA' => 'Inuktitut (Latin) (Canada)',
            'ga-IE' => 'Irish (Ireland)',
            'it-IT' => 'Italian (Italy)',
            'it-CH' => 'Italian (Switzerland)',
            'ja-JP' => 'Japanese (Japan)',
            'qut-GT' => 'K\'iche (Guatemala)',
            'kn-IN' => 'Kannada (India)',
            'kk-KZ' => 'Kazakh (Kazakhstan)',
            'km-KH' => 'Khmer (Cambodia)',
            'rw-RW' => 'Kinyarwanda (Rwanda)',
            'sw-KE' => 'Kiswahili (Kenya)',
            'kok-IN' => 'Konkani (India)',
            'ko-KR' => 'Korean (Korea)',
            'ky-KG' => 'Kyrgyz (Kyrgyzstan)',
            'lo-LA' => 'Lao (Lao P.D.R.)',
            'lv-LV' => 'Latvian (Latvia)',
            'lt-LT' => 'Lithuanian (Lithuania)',
            'dsb-DE' => 'Lower Sorbian (Germany)',
            'lb-LU' => 'Luxembourgish (Luxembourg)',
            'mk-MK' => 'Macedonian (Former Yugoslav Republic of Macedonia)',
            'ms-BN' => 'Malay (Brunei Darussalam)',
            'ms-MY' => 'Malay (Malaysia)',
            'ml-IN' => 'Malayalam (India)',
            'mt-MT' => 'Maltese (Malta)',
            'mi-NZ' => 'Maori (New Zealand)',
            'arn-CL' => 'Mapudungun (Chile)',
            'mr-IN' => 'Marathi (India)',
            'moh-CA' => 'Mohawk (Canada)',
            'mn-MN' => 'Mongolian (Cyrillic, Mongolia)',
            'mn-Mong-CN' => 'Mongolian (Traditional Mongolian) (People\'s Republic of China)',
            'ne-NP' => 'Nepali (Nepal)',
            'nb-NO' => 'Norwegian, Bokmål (Norway)',
            'nn-NO' => 'Norwegian, Nynorsk (Norway)',
            'oc-FR' => 'Occitan (France)',
            'or-IN' => 'Oriya (India)',
            'ps-AF' => 'Pashto (Afghanistan)',
            'fa-IR' => 'Persian (Iran)',
            'pl-PL' => 'Polish (Poland)',
            'pt-BR' => 'Portuguese (Brazil)',
            'pt-PT' => 'Portuguese (Portugal)',
            'pa-IN' => 'Punjabi (India)',
            'quz-BO' => 'Quechua (Bolivia)',
            'quz-EC' => 'Quechua (Ecuador)',
            'quz-PE' => 'Quechua (Peru)',
            'ro-RO' => 'Romanian (Romania)',
            'rm-CH' => 'Romansh (Switzerland)',
            'ru-RU' => 'Russian (Russia)',
            'smn-FI' => 'Sami (Inari) (Finland)',
            'smj-NO' => 'Sami (Lule) (Norway)',
            'smj-SE' => 'Sami (Lule) (Sweden)',
            'se-FI' => 'Sami (Northern) (Finland)',
            'se-NO' => 'Sami (Northern) (Norway)',
            'se-SE' => 'Sami (Northern) (Sweden)',
            'sms-FI' => 'Sami (Skolt) (Finland)',
            'sma-NO' => 'Sami (Southern) (Norway)',
            'sma-SE' => 'Sami (Southern) (Sweden)',
            'sa-IN' => 'Sanskrit (India)',
            'sr-Cyrl-BA' => 'Serbian (Cyrillic) (Bosnia and Herzegovina)',
            'sr-Cyrl-CS' => 'Serbian (Cyrillic, Serbia)',
            'sr-Latn-BA' => 'Serbian (Latin) (Bosnia and Herzegovina)',
            'sr-Latn-CS' => 'Serbian (Latin, Serbia)',
            'nso-ZA' => 'Sesotho sa Leboa (South Africa)',
            'tn-ZA' => 'Setswana (South Africa)',
            'si-LK' => 'Sinhala (Sri Lanka)',
            'sk-SK' => 'Slovak (Slovakia)',
            'sl-SI' => 'Slovenian (Slovenia)',
            'es-AR' => 'Spanish (Argentina)',
            'es-BO' => 'Spanish (Bolivia)',
            'es-CL' => 'Spanish (Chile)',
            'es-CO' => 'Spanish (Colombia)',
            'es-CR' => 'Spanish (Costa Rica)',
            'es-DO' => 'Spanish (Dominican Republic)',
            'es-EC' => 'Spanish (Ecuador)',
            'es-SV' => 'Spanish (El Salvador)',
            'es-GT' => 'Spanish (Guatemala)',
            'es-HN' => 'Spanish (Honduras)',
            'es-MX' => 'Spanish (Mexico)',
            'es-NI' => 'Spanish (Nicaragua)',
            'es-PA' => 'Spanish (Panama)',
            'es-PY' => 'Spanish (Paraguay)',
            'es-PE' => 'Spanish (Peru)',
            'es-PR' => 'Spanish (Puerto Rico)',
            'es-ES' => 'Spanish (Spain)',
            'es-US' => 'Spanish (United States)',
            'es-UY' => 'Spanish (Uruguay)',
            'es-VE' => 'Spanish (Venezuela)',
            'sv-FI' => 'Swedish (Finland)',
            'sv-SE' => 'Swedish (Sweden)',
            'syr-SY' => 'Syriac (Syria)',
            'tg-Cyrl-TJ' => 'Tajik (Cyrillic) (Tajikistan)',
            'tzm-Latn-DZ' => 'Tamazight (Latin) (Algeria)',
            'ta-IN' => 'Tamil (India)',
            'tt-RU' => 'Tatar (Russia)',
            'te-IN' => 'Telugu (India)',
            'th-TH' => 'Thai (Thailand)',
            'bo-CN' => 'Tibetan (People\'s Republic of China)',
            'tr-TR' => 'Turkish (Turkey)',
            'tk-TM' => 'Turkmen (Turkmenistan)',
            'ug-CN' => 'Uighur (People\'s Republic of China)',
            'uk-UA' => 'Ukrainian (Ukraine)',
            'hsb-DE' => 'Upper Sorbian (Germany)',
            'ur-PK' => 'Urdu (Islamic Republic of Pakistan)',
            'uz-Cyrl-UZ' => 'Uzbek (Cyrillic, Uzbekistan)',
            'uz-Latn-UZ' => 'Uzbek (Latin, Uzbekistan)',
            'vi-VN' => 'Vietnamese (Vietnam)',
            'cy-GB' => 'Welsh (United Kingdom)',
            'wo-SN' => 'Wolof (Senegal)',
            'sah-RU' => 'Yakut (Russia)',
            'ii-CN' => 'Yi (People\'s Republic of China)',
            'yo-NG' => 'Yoruba (Nigeria)',
            'xh-ZA' => 'isiXhosa (South Africa)',
            'zu-ZA' => 'isiZulu (South Africa)',
        );
        $culture = new Select('culture',
            $culture_codes,
            array(
                'id' => 'culture',
                'name' => 'culture',
            ));

        $ui_culture = new Select('ui_culture',
            $culture_codes,
            array(
                'id' => 'ui_culture',
                'name' => 'ui_culture',
            ));

        if(!empty($globalization['culture']))
            $culture->setDefault($globalization['culture']);
        if(!empty($globalization['uiCulture'])) {
            $ui_culture->setDefault($globalization['uiCulture']);
        }
        if(!empty($globalization['fileEncoding'])) {
            $file_encoding->setAttribute("value", $globalization['fileEncoding']);
        }
        if(!empty($globalization['responseEncoding'])) {
            $responseEncoding->setAttribute("value", $globalization['responseEncoding']);
        }
        if(!empty($globalization['requestEncoding'])) {
            $requestEncoding->setAttribute("value", $globalization['requestEncoding']);
        }

        $this->add($responseEncoding);
        $this->add($culture);
        $this->add($ui_culture);
        $this->add($file_encoding);
        $this->add($requestEncoding);
    }

    private function codeAccess($options)
    {
        $trust_level = new Select('trust_level',
            array(
                'Full' => 'Full',
                'High' => 'High',
                'Medium' => 'Medium',
                'Low' => 'Low',
                'Minimal' => 'Minimal'
            ),
            array(
                'id' => 'trust_level',
                'name' => 'trust_level',
            ));
        $trust_level->setDefault("Medium");

        if($options["is_admin"]) {
            $trust_level->setAttribute("disabled", "disabled");
        }

        $this->add($trust_level);
    }

    private function sessionSettings($options)
    {
        $auth_mode = new Select('auth_mode',
            array(
                '0' => 'None',
                '1' => 'Windows',
                '2' => 'Passport',
                '3' => 'Forms',
            ),
            array(
                'id' => 'auth_mode',
                'name' => 'auth_mode',
            ));
        $auth_mode->setDefault($options['aspnet_settings']['sessionSettings']['sessionSettings']['mode']);
        $this->add($auth_mode);

        $session_time_out = explode(':', $options['aspnet_settings']['sessionSettings']['sessionState']['timeout']);
        $this->add(new Text('sessionTimeOut', array(
            'id' => 'sessionTimeOut',
            'name' => 'sessionTimeOut',
            'value' => abs(($session_time_out['0']*60) + ($session_time_out['1']))
        )));
    }
}