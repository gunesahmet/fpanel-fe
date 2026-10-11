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

use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class LoginForm
 * @package FPanel\Forms
 */
class LoginForm extends Form
{
    /**
     * init
     */
    public function initialize()
    {

        $username = new Text('username', array(
            'placeholder' => __('Username')

        ));
        $username->setLabel(__('Username'));
        $username->addValidators(array(
            new PresenceOf(array(
                'message' => __('The username is required')
            ))
        ));

        $this->add($username);

        //Password
        $password = new Password('password', array(
            'placeholder' => __('Your Password'),
            'class' => '',
        ));
        $password->setLabel(__('Password'));

        $password->addValidator(
            new PresenceOf(array(
                'message' => __('The password is required')
            ))
        );

        $this->add($password);

        $lang_list = new Select(
            'lang_select',
            $this->translate->getLangList(), array(
                'id' => 'lang_select',
                'name' => 'lang_select',
            )
        );

        $templates = new Select(
            'templates',
            $this->getTemplates(), array(
                'id' => 'templates',
                'name' => 'templates',
            )
        );

        if($this->fcookies->has('language'))
            $language = $this->fcookies->get('language');
        else
            $language = "en_US";

//        if($this->fcookies->has('template'))
//            $template = $this->fcookies->get('template');
//        else
//            $template = "default";

        $lang_list->setDefault($this->crypto->staticEncode($language));
//        $templates->setDefault($this->crypto->staticEncode($template));

        $this->add($lang_list);
        $this->add($templates);

    }

    public function getTemplates(): array
    {
        $template_dir = ROOTPATH."/templates";
        $template_list = array_diff(scandir($template_dir), array('..', '.'));

        $result = array();
        foreach($template_list as $template) {
            $result[$this->crypto->staticEncode($template)] = ucfirst($template);
        }
        return $result;
    }
}
