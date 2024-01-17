<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopsListAjax;

class ShopsListAjaxComponentLangDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $shopImageTitle,
        public readonly string $buttonBackLabel,
        public readonly string $buttonCreateShopLabel
    ) {
    }
}
