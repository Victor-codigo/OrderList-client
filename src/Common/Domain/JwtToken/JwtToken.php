<?php

declare(strict_types=1);

namespace Common\Domain\JwtToken;

use Common\Domain\JwtToken\Exception\JwtTokenGetPayLoadException;

class JwtToken
{
    /**
     * @throws JwtTokenGetPayLoadException
     */
    public static function getUserName(string $tokenSession): string
    {
        $jwtPayload = self::getTokenPayLoad($tokenSession);

        return $jwtPayload->username;
    }

    /**
     * @throws JwtTokenGetPayLoadException
     */
    public static function hasExpired(string $tokenSession): bool
    {
        $jwtPayLoad = self::getTokenPayLoad($tokenSession);

        if ($jwtPayLoad->exp <= time()) {
            return true;
        }

        return false;
    }

    /**
     * @throws JwtTokenGetPayLoadException
     */
    public static function hasSessionActive(?string $tokenSession): bool
    {
        if (null === $tokenSession || self::hasExpired($tokenSession)) {
            return false;
        }

        return true;
    }

    /**
     * @throws JwtTokenGetPayLoadException
     */
    private static function getTokenPayLoad(string $tokenSession): mixed
    {
        try {
            $tokenParts = explode('.', $tokenSession);
            $tokenPayload = base64_decode($tokenParts[1]);

            return json_decode($tokenPayload);
        } catch (\Throwable) {
            throw JwtTokenGetPayLoadException::fromMessage('Error getting token payload');
        }
    }
}
