<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemRemove;

use App\Twig\Components\TwigComponentDtoInterface;

class ItemRemoveComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $componentName,
        public readonly array $errors,
        public readonly string $csrfToken,
        public readonly string $formActionUrl,
        public readonly bool $removeMulti,
    ) {
    }
}
