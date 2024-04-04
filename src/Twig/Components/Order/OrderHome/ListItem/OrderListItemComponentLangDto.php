<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;

class OrderListItemComponentLangDto extends HomeListItemComponentLangDto
{
    public function __construct(
        public readonly string $modifyItemButtonAlt,
        public readonly string $modifyItemButtonTitle,
        public readonly string $removeItemButtonAlt,
        public readonly string $removeItemButtonTitle,
        public readonly string $infoItemButtonAlt,
        public readonly string $infoItemButtonTitle,
        public readonly string $boughtItemButtonTitle,
        public readonly string $notBoughtItemButtonTitle,

        public readonly string $imageItemAlt,
        public readonly string $imageItemTitle,
    ) {
    }
}
