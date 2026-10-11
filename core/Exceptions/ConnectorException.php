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

class ConnectorException extends Exception
{
	public const NONEWNONCEEXCEPTION 			= 0x11;
	public const ACCOUNTDEACTIVATEDEXCEPTION 	= 0x12;
	public const METHODNOTSUPPORTEDEXCEPTION 	= 0x13;
	public const CURLERROREXCEPTION 			= 0x14;
	public const INVALIDRESPONSEEXCEPTION 		= 0x15;
	
	public static function NoNewNonceException(): ConnectorException
    {
		return new static('No new nonce.', self::NONEWNONCEEXCEPTION);
	}
	
	public static function AccountDeactivatedException(): ConnectorException
    {
		return new static('The account was deactivated. No further requests can be made.', self::ACCOUNTDEACTIVATEDEXCEPTION);
	}
	
	public static function MethodNotSupportedException(string $method): ConnectorException
    {
		return new static(sprintf('HTTP request %s not supported.', $method), self::METHODNOTSUPPORTEDEXCEPTION);
	}
	
	public static function CurlErrorException(string $error): ConnectorException
    {
		return new static(sprintf('Curl error: %s', $error), self::CURLERROREXCEPTION);
	}
	
	public static function InvalidResponseException(array $response): ConnectorException
    {
		$statusCode = array_key_exists('status', $response) ? $response['status'] : 'unknown';
		return new static(sprintf('Invalid response: %s', $statusCode), self::INVALIDRESPONSEEXCEPTION, null, $response);
	}
}
