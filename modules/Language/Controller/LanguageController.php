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
 */
namespace Language\Controller;

use Main\Controller\ControllerBase;

/**
 * Class LanguageController
 * @package Language\Controller
 */
class LanguageController extends ControllerBase
{
    protected $user_data;

    /**
     * @return bool|void
     */
    public function initialize()
    {
        parent::initialize();
        if(!$this->session_status)
            return;

        return true;
    }

    public function indexAction()
    {
        $params = $this->dispatcher->getParams();
        $referrer = $this->request->getHTTPReferer();
        $lang_code = $params['lang_code'];

        if($lang_code != null) {
            $this->fcookies->set("language", $lang_code, time() + (86400 * 365));
        }
        if($referrer != null)
            $this->response->redirect($referrer,true);
        else
            $this->response->redirect($this->uri->url_encrypt("index.php"),true);
    }


}