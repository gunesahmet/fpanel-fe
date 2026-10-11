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
namespace FPanel\Jwt\Exception;

use Exception;
use FPanel\Jwt\JWT;

/**
 * An exception thrown if a JWT related error is detected.
 */
class JwtException extends Exception
{
    /**
     * The JWT for which the exception was thrown.
     *
     * @var JWT|null
     */
    protected $jwt;

    /**
     * Construct a JWT related exception.
     *
     * @param  string   $message
     * @param  JWT|null $jwt
     * @return void
     */
    public function __construct(string $message = '', JWT $jwt = null)
    {
        $this->jwt = $jwt;

        parent::__construct($message);
    }

    /**
     * Get the JWT for which the exception was thrown.
     *
     * @return JWT|null
     */
    public function getJwt(): ?JWT
    {
        return $this->jwt;
    }
}
