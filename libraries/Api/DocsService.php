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
 */namespace FPanel\Api;

use Phalcon\Config;
use Phalcon\Mvc\User\Component;

class DocsService extends Component
{
    /**
     * Get options for scan
     *
     * @param array $config
     * @return array
     */
    public function getOptions(array $config)
    {
        $options = [];
        if (array_key_exists('exclude', $config)) {
            $options['exclude'] = $this->getExclude($config['exclude']);
        }
        return $options;
    }

    /**
     * Get exclude options property
     *
     * @param mixed $exclude
     * @return string|array|null
     */
    protected function getExclude($exclude)
    {
        if (is_string($exclude)) {
            return $exclude;
        }
        if ($exclude instanceof Config) {
            return $exclude->toArray();
        }
        return null;
    }
}