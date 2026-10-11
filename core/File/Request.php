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
 * Class Request
 * @package FPanel\File
 */
class Request implements RequestInterface
{
    /**
     * Request parameters
     *
     * @var array
     */
    protected $params;

    /**
     * File
     *
     * @var array
     */
    protected $file;

    public function __construct($params = null, $file = null)
    {
        if ($params === null) {
            $params = $_REQUEST;
        }

        if ($file === null && isset($_FILES['file'])) {
            $file = $_FILES['file'];
        }

        $this->params = $params;
        $this->file = $file;
    }

    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }

    public function getFileName()
    {
        return $this->getParam('flowFilename');
    }

    public function getTotalSize()
    {
        return $this->getParam('flowTotalSize');
    }

    public function getIdentifier()
    {
        return $this->getParam('flowIdentifier');
    }

    public function getRelativePath()
    {
        return $this->getParam('flowRelativePath');
    }

    public function getTotalChunks()
    {
        return $this->getParam('flowTotalChunks');
    }

    public function getDefaultChunkSize()
    {
        return $this->getParam('flowChunkSize');
    }

    public function getCurrentChunkNumber()
    {
        return $this->getParam('flowChunkNumber');
    }

    public function getCurrentChunkSize()
    {
        return $this->getParam('flowCurrentChunkSize');
    }

    public function getFile()
    {
        return $this->file;
    }

    public function isFPUploadRequest(): bool
    {
        return false;
    }
}
