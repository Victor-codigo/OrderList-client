<?php

declare(strict_types=1);

namespace Common\Domain\Cookie;

trait Cookie
{
    private function getCookieValue(string $cookie): ?string
    {
        $cookieParams = explode(';', $cookie);

        if (count($cookieParams) < 1) {
            return null;
        }

        $cookieKeyValue = explode('=', $cookieParams[0]);

        if (2 !== count($cookieKeyValue)) {
            return null;
        }

        return $cookieKeyValue[1];
    }
}
