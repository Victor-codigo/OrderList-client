<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList\List;

class ShopListComponentLangDto
{
    public function __construct(
        public readonly string $buttonAddOrderText,
        public readonly string $listEmptyMessage,
        public readonly string $listEmptyIconAlt,
    ) {
    }
}
