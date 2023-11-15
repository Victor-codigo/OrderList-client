<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList\ListItem;

class ShopListItemComponentLangDto
{
    public function __construct(
        public readonly string $imageShopAlt,
        public readonly string $imageShopTitle,
        public readonly string $modifyShopButtonAlt,
        public readonly string $modifyShopButtonTitle,
        public readonly string $removeShopButtonTitle,
        public readonly string $removeShopButtonAlt
    ) {
    }
}
