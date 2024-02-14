<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemInfo;

use App\Twig\Components\TwigComponentDtoInterface;

class ItemInfoComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $componentName,
    ) {
    }
}
