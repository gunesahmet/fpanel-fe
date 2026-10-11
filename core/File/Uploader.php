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

class Uploader
{
    /**
     * Delete chunks older than expiration time.
     *
     * @param string $chunksFolder
     * @param int $expirationTime seconds
     *
     * @throws FileOpenException
     */
    public static function pruneChunks(string $chunksFolder, int $expirationTime = 172800)
    {
        $handle = opendir($chunksFolder);

        if (!$handle) {
            throw new FileOpenException('failed to open folder: '.$chunksFolder);
        }

        while (false !== ($entry = readdir($handle))) {
            if ($entry == "." || $entry == ".." || $entry == ".gitignore") {
                continue;
            }

            $path = $chunksFolder.DIR_SEPARATOR.$entry;

            if (is_dir($path)) {
                continue;
            }

            if (time() - filemtime($path) > $expirationTime) {
                unlink($path);
            }
        }

        closedir($handle);
    }
}
