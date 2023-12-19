<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\HomeList\ListItem;

class HomeListItemComponentLangDto
{
    public function __construct(
        public readonly string $modifyItemButtonAlt,
        public readonly string $modifyItemButtonTitle,
        public readonly string $removeItemButtonAlt,
        public readonly string $removeItemButtonTitle,
    ) {
    }
}
