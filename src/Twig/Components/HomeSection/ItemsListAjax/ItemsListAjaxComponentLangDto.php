<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemsListAjax;

class ItemsListAjaxComponentLangDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $itemImageTitle,
        public readonly string $buttonBackLabel,
        public readonly string $buttonCreateItemLabel
    ) {
    }
}
