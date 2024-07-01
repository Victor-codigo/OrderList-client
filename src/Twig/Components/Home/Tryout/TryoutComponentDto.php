<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Tryout;

use App\Twig\Components\TwigComponentDtoInterface;

class TryoutComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $domainName,
        public readonly string $domain,
        public readonly string $lang,
        public readonly string $csrfToken,
        public readonly string $loginUrl,
    ) {
    }
}
