<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopRemove;

use App\Twig\Components\TwigComponentDtoInterface;

class ShopRemoveComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $csrfToken,
        public readonly string $formActionUrl,
        public readonly bool $removeMulti,
    ) {
    }
}
