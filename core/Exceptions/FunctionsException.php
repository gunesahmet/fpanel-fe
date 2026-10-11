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
namespace FPanel\Exceptions;

class FunctionsException extends Exception
{
	public const INVALIDARGUMENTEXCEPTION 	= 0x51;
	public const GENERATEKEYPAIREXCEPTION 	= 0x52;
	public const PHPVERSIONEXCEPTION 		= 0x53;
	
	public static function InvalidArgumentException(string $message): FunctionsException
    {
		return new static($message, self::INVALIDARGUMENTEXCEPTION);
	}
	
	public static function GenerateKeypairException(string $message): FunctionsException
    {
		return new static($message, self::GENERATEKEYPAIREXCEPTION);
	}
	
	public static function PHPVersionException(): FunctionsException
    {
		return new static('PHP 7.1+ required for EC keys.', self::PHPVERSIONEXCEPTION);
	}
}
