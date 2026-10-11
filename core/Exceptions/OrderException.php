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
 */namespace FPanel\Exceptions;

class OrderException extends Exception
{
	public const INVALIDKEYTYPEEXCEPTION 		= 0x31;
	public const INVALIDORDERSTATUSEXCEPTION 	= 0x32;
	public const CREATEFAILEDEXCEPTION 			= 0x33;
	public const INVALIDARGUMENTEXCEPTION		= 0x34;
	public const INVALIDCONFIGURATIONEXCEPTION	= 0x35;	
	
	public static function InvalidKeyTypeException(string $keyType): OrderException
    {
		return new static(sprintf('Key type \'%s\' not supported.', $keyType), self::INVALIDKEYTYPEEXCEPTION);
	}
	
	public static function InvalidOrderStatusException(): OrderException
    {
		return new static('Order status is invalid.', self::INVALIDORDERSTATUSEXCEPTION);
	}
	
	public static function CreateFailedException(string $message): OrderException
    {
		return new static($message, self::CREATEFAILEDEXCEPTION);
	}
	
	public static function InvalidArgumentException(string $message): OrderException
    {
		return new static($message, self::INVALIDARGUMENTEXCEPTION);
	}
	
	public static function InvalidConfigurationException(string $message): OrderException
    {
		return new static($message, self::INVALIDCONFIGURATIONEXCEPTION);
	}
}
