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
 * Class MessageForm
 * @package FPanel\Forms
 */
class MessageForm extends Form
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
        if($options["action"] == "edit") {
            $status = new Check("status", array(
                "id" => "status",
                "name" => "status",
            ));
            if($options["status"] == ACTIVE) {
                $status->setAttribute("checked","checked");
            }
            $this->add($status);

            $this->add(new Text("title", array(
                "id" => "title",
                "name" => "title",
                "value" => $options["title"],
            )));
            $this->add(new Text("description", array(
                "id" => "description",
                "name" => "description",
                "value" => $options["description"],
            )));
            $this->add(new Text("expire_date", array(
                "id" => "expire_date",
                "name" => "expire_date",
                "value" => $options["expire_date"],
            )));
            $type = new Select("type",
                array(
                    "announcement"  => __("Announcement"),
                    "important"  => __("Important"),
                    "welcome"  => __("Welcome"),
                ),
                array(
                    "id" => "type",
                    "name" => "type",
                ));
            $type->setDefault($options["type"]);
            $this->add($type);
            $this->add(new TextArea("message_content", array(
                "id" => "message_content",
                "name" => "message_content",
                "rows"   => "10",
                "class" => "wysihtml5 span10",
                "value" => $options["message"],
            )));
        }
        else {
            $this->add(new Check("status", array(
                "id" => "status",
                "name" => "status",
            )));
            $this->add(new Text("title", array(
                "id" => "title",
                "name" => "title",
            )));
            $this->add(new Text("description", array(
                "id" => "description",
                "name" => "description",
            )));
            $this->add(new Text("expire_date", array(
                "id" => "expire_date",
                "name" => "expire_date",
            )));
            $type = new Select("type",
                array(
                    "announcement"  => __("Announcement"),
                    "important"  => __("Important"),
                ),
                array(
                    "id" => "type",
                    "name" => "type",
                ));
            $this->add($type);
            $this->add(new TextArea("message_content", array(
                "id" => "message_content",
                "name" => "message_content",
                "rows"   => "10",
                "class" => "wysihtml5 span10",
            )));
        }
    }
}