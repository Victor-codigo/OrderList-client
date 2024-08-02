<?php

declare(strict_types=1);

namespace Common\Domain\JwtToken;

use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;

class JwtToken
{
    /**
     * @throws RequestUnauthorizedException
     */
    public static function getUserName(string $tokenSession): string
    {
        $jwtPayload = self::getTokenPayLoad($tokenSession);

        return $jwtPayload->username;
    }

    /**
     * @throws RequestUnauthorizedException
     */
    public static function hasExpired(string $tokenSession): bool
    {
        $jwtPayLoad = self::getTokenPayLoad($tokenSession);

        if ($jwtPayLoad->exp <= time()) {
            return true;
        }

        return false;
    }

    public static function hasSessionActive(?string $tokenSession): bool
    {
        if (null === $tokenSession || self::hasExpired($tokenSession)) {
            return false;
        }

        return true;
    }

    /**
     * @throws RequestUnauthorizedException
     */
    private static function getTokenPayLoad(string $tokenSession): mixed
    {
        try {
            $tokenParts = explode('.', $tokenSession);
            $tokenPayload = base64_decode($tokenParts[1]);

            return json_decode($tokenPayload);
        } catch (\Throwable) {
            throw RequestUnauthorizedException::fromMessage('Error getting token payload');
        }
    }
}
