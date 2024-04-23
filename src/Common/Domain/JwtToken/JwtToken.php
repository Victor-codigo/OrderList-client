<?php

declare(strict_types=1);

namespace Common\Domain\JwtToken;

class JwtToken
{
    public static function getUserName(string $tokenSession): string
    {
        $tokenParts = explode('.', $tokenSession);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);

        return $jwtPayload->username;
    }
}
