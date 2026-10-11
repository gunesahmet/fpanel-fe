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
 */namespace FPanel\Menu;

use Phalcon\Mvc\User\Component;

/**
 * Class Breadcrumbs
 * @package FPanel\Menu
 */
class Breadcrumbs extends Component
{
    protected $_elements = array();

    /**
     * @param $text
     * @param $link
     * @param $class
     * @return $this
     */
    public function add($text, $link = null, $class = null): Breadcrumbs
    {
        if($link == null) {
            $this->_elements[] = array(
                'active' => false,
                'link' => "",
                'text' => $text,
                'class' => $class,
            );
        }
        else {
            $this->_elements[] = array(
                'active' => false,
                'link' => $this->uri->url_encrypt($link),
                'text' => $text,
                'class' => $class,
            );
        }
        return $this;
    }

    /**
     * Resets the internal element array
     */
    public function reset()
    {
        $this->_elements = array();
    }

    /**
     *
     */
    public function generate(): array
    {
        $lastKey = key(array_slice($this->_elements, -1, 1, true));
        $this->_elements[$lastKey]['active'] = true;
        if(!$this->request->isAjax()) {
            $this->view->setVar("breadcrumbs", $this->_elements);
        }
        return $this->_elements;
    }
}