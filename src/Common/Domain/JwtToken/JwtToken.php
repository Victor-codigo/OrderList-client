<?php

declare(strict_types=1);

namespace Common\Domain\JwtToken;

class JwtToken
{
    public static function getUserName(string $tokenSession): string
    {
        $jwtPayload = self::getTokenPayLoad($tokenSession);

        return $jwtPayload->username;
    }

    public static function hasExpired(string $tokenSession): bool
    {
        $jwtPayLoad = self::getTokenPayLoad($tokenSession);

        if ($jwtPayLoad->exp <= time()) {
            return true;
        }

        return false;
    }

    private static function getTokenPayLoad(string $tokenSession): mixed
    {
        $tokenParts = explode('.', $tokenSession);
        $tokenPayload = base64_decode($tokenParts[1]);

        return json_decode($tokenPayload);
    }
}
