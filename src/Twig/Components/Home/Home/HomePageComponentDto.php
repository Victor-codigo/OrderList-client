<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Home;

use App\Twig\Components\TwigComponentDtoInterface;

class HomePageComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $domainName,
        public readonly string $locale,
        public readonly string $languageUrl,
    ) {
    }
}
