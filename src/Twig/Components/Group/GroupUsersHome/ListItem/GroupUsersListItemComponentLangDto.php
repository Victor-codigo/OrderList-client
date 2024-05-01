<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;

class GroupUsersListItemComponentLangDto extends HomeListItemComponentLangDto
{
    public function __construct(
        public readonly string $removeItemButtonAlt,
        public readonly string $removeItemButtonTitle,
        public readonly string $infoItemButtonAlt,
        public readonly string $infoItemButtonTitle,

        public readonly string $imageItemAlt,
        public readonly string $imageItemTitle,

        public readonly string $adminLabel,
    ) {
    }
}
