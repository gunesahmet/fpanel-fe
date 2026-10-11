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
 */namespace FPanel\File;

/**
 * Class FPUploadRequest
 * @package FPanel\File
 */
class FPUploadRequest extends Request
{
    private $isFusty = false;

    public function __construct($params = null, $file = null)
    {
        parent::__construct($params, $file);

        $this->isFusty = $this->getTotalSize() === null && $this->getFileName() && $this->getFile();

        if ($this->isFusty) {
            $this->params['flowTotalSize'] = $this->file['size'] ?? 0;
            $this->params['flowTotalChunks'] = 1;
            $this->params['flowChunkNumber'] = 1;
            $this->params['flowChunkSize'] = $this->params['flowTotalSize'];
            $this->params['flowCurrentChunkSize'] = $this->params['flowTotalSize'];
        }
    }

    public function isFPUploadRequest(): bool
    {
        return $this->isFusty;
    }
}
