<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList\List;

use App\Twig\Components\Alert\AlertComponentDto;

class ShopListComponentLangDto
{
    public function __construct(
        public readonly string $buttonAddOrderText,
        public readonly string $listEmptyMessage,
        public readonly string $listEmptyIconAlt,
        public readonly AlertComponentDto|null $validationErrors,
    ) {
    }
}
