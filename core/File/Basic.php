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
namespace FPanel\File;

use Exception;
use Phalcon\Mvc\User\Component;

/**
 * Class Basic
 * @package FPanel\File
 */
class Basic extends Component
{

    public $errors;

    public $chunked_file_name;

    public function save($destination, $config, RequestInterface $request = null): bool
    {
        if (!$config instanceof ConfigInterface) {
            $config = new Config(array(
                'tempDir' => $config,
            ));
        }
        $file = new File($config, $request);

        if ($file->validateChunk()) {
            $status = $file->saveChunk();
            $this->chunked_file_name = $file->getChunkedFileName();
        }
        else {
            // error, invalid chunk upload request, retry
            $this->errors = $file->getErrors();
            header("HTTP/1.1 400 Bad Request");
            return false;
        }

        try {
            if ($file->validateFile() && $file->save($destination)) {
                return true;
            }
            else {
                return $status;
            }
        }
        catch (Exception $e) {
            return false;
        }
    }
}
