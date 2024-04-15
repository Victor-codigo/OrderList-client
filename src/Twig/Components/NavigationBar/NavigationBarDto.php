<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

use App\Twig\Components\TwigComponentDtoInterface;

class NavigationBarDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $groupNameEncoded,
        public readonly ?string $sectionActiveId,
        public readonly string $locale,
        public readonly string $routeName,
        public readonly array $routeParameters,
    ) {
    }
}
