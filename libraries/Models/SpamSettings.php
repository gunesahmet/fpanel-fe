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
 */namespace FPanel\Models;

use Phalcon\Mvc\Model;

/**
 * Class SpamSettings
 * @package FPanel\Models
 * @method static SpamSettings|Model\ResultsetInterface findFirst($params = null)
 * @method static SpamSettings|Model\ResultsetInterface findFirstById($params = null)
 * @method static SpamSettings[]|Model\ResultsetInterface find($params = null)
 */
class SpamSettings extends FModel
{
    /**
     * @Primary
     * @var int
     */
    public $id;

    public $status;

    public $scores;

    public $pyzor;

    public $razor;

    public $skip_rbl;

    public $bayes;

    public $spam_report;

    public $use_bayes_rules;

    public $bayes_auto_learn_on_error;

    public $bayes_auto_learn;

    public $bayes_auto_learn_threshold_nonspam;

    public $bayes_auto_learn_threshold_spam;

    public $rewrite_header;

    public $server_id;

    public $installed;

    public $created_date;

    public $updated_date;

    public function initialize()
    {
        $this->setSource('spam_settings');
    }
}
