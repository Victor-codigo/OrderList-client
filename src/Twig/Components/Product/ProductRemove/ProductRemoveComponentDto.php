<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductRemove;

use App\Twig\Components\TwigComponentDtoInterface;

class ProductRemoveComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $csrfToken,
        public readonly string $formActionUrl,
        public readonly bool $removeMulti,
    ) {
    }
}
