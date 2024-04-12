<?php

declare(strict_types=1);

namespace Common\Domain\CodedUrlParameter;

trait UrlEncoder
{
    public function decodeUrlName(?string $name): ?string
    {
        if (null === $name) {
            return null;
        }

        return str_replace('-', ' ', $name);
    }

    public function encodeUrl(string $url): string
    {
        return str_replace(' ', '-', $url);
    }
}
