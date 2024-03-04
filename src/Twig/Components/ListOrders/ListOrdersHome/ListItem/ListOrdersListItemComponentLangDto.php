<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;

class ListOrdersListItemComponentLangDto extends HomeListItemComponentLangDto
{
    public function __construct(
        public readonly string $modifyItemButtonAlt,
        public readonly string $modifyItemButtonTitle,
        public readonly string $removeItemButtonAlt,
        public readonly string $removeItemButtonTitle,
        public readonly string $infoItemButtonAlt,
        public readonly string $infoItemButtonTitle,

        public readonly string $imageItemAlt,
        public readonly string $imageItemTitle,
    ) {
    }
}
