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

use Exception;

class File
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * File hashed unique identifier
     *
     * @var string
     */
    private $identifier;

    private $error_messages = [];

    private $chunked_file_name;

    public function __construct(ConfigInterface $config, RequestInterface $request = null)
    {
        $this->config = $config;

        if ($request === null) {
            $request = new Request();
        }

        $this->request = $request;
        $this->identifier = call_user_func($this->config->getHashNameCallback(), $request);
    }

    public function getChunkedFileName()
    {
        return $this->chunked_file_name;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getChunkPath($index): string
    {
        return $this->config->getTempDir().DIR_SEPARATOR.basename($this->identifier).'_'. (int) $index;
    }

    public function checkChunk(): bool
    {
        return file_exists($this->getChunkPath($this->request->getCurrentChunkNumber()));
    }

    public function validateChunk(): bool
    {
        $file = $this->request->getFile();

        if (!$file) {
            return false;
        }

        if(!isset($file['tmp_name'])) {
            $this->error_messages["size"] = __("Tmp name is not set");
            return false;
        }

        if(!isset($file['size'])) {
            $this->error_messages["size"] = __("File size is not set");
            return false;
        }

        if((isset($file['error']) && ($file['error'] != "0"))) {
            $this->error_messages["error"] = $file['error'];
            return false;
        }


        if ($this->request->getCurrentChunkSize() != $file['size']) {
            $this->error_messages["size"] = __("Incorrect chunked upload size");
            return false;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        return true;
    }

    public function getErrors(): array
    {
        return $this->error_messages;
    }

    public function saveChunk(): bool
    {
        $file = $this->request->getFile();
        $this->chunked_file_name = $this->getChunkPath($this->request->getCurrentChunkNumber());
        return $this->_move_uploaded_file($file['tmp_name'], $this->getChunkPath($this->request->getCurrentChunkNumber()));
    }

    public function validateFile(): bool
    {
        $totalChunks = $this->request->getTotalChunks();
        $totalChunksSize = 0;

        for ($i = $totalChunks; $i >= 1; $i--) {
            $file = $this->getChunkPath($i);
            if (!file_exists($file)) {
                return false;
            }
            $totalChunksSize += filesize($file);
        }

        return $this->request->getTotalSize() == $totalChunksSize;
    }

    /**
     * @throws FileLockException
     * @throws FileOpenException
     */
    public function save($destination): bool
    {
        $fh = fopen($destination, 'wb');
        if (!$fh) {
            throw new FileOpenException('failed to open destination file: '.$destination);
        }

        if (!flock($fh, LOCK_EX | LOCK_NB, $blocked)) {
            // @codeCoverageIgnoreStart
            if ($blocked) {
                // Concurrent request has requested a lock.
                // File is being processed at the moment.
                // Warning: lock is not checked in windows.
                return false;
            }
            // @codeCoverageIgnoreEnd

            throw new FileLockException('failed to lock file: '.$destination);
        }

        $totalChunks = $this->request->getTotalChunks();

        try {
            $preProcessChunk = $this->config->getPreprocessCallback();

            for ($i = 1; $i <= $totalChunks; $i++) {
                $file = $this->getChunkPath($i);
                $chunk = fopen($file, "rb");

                if (!$chunk) {
                    throw new FileOpenException('failed to open chunk: '.$file);
                }

                if ($preProcessChunk !== null) {
                    call_user_func($preProcessChunk, $chunk);
                }

                stream_copy_to_stream($chunk, $fh);
                fclose($chunk);
            }
        } catch (Exception $e) {
            flock($fh, LOCK_UN);
            fclose($fh);
            throw $e;
        }

        if ($this->config->getDeleteChunksOnSave()) {
            $this->deleteChunks();
            if(is_dir($destination)) {
                rmdir($destination, true);
            }
        }

        flock($fh, LOCK_UN);
        fclose($fh);

        return true;
    }

    public function deleteChunks()
    {
        $totalChunks = $this->request->getTotalChunks();

        for ($i = 1; $i <= $totalChunks; $i++) {
            $path = $this->getChunkPath($i);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    public function _move_uploaded_file($filePath, $destinationPath): bool
    {
        return move_uploaded_file($filePath, $destinationPath);
    }
}
