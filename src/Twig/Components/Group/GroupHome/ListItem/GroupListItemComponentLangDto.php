<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;

class GroupListItemComponentLangDto extends HomeListItemComponentLangDto
{
    public function __construct(
        public readonly string $userGroupLabel,
        public readonly string $userGroupDescription,
        public readonly string $modifyItemButtonLabel,
        public readonly string $modifyItemButtonTitle,
        public readonly string $removeItemButtonLabel,
        public readonly string $removeItemButtonTitle,
        public readonly string $infoItemButtonLabel,
        public readonly string $infoItemButtonTitle,
        public readonly string $groupUsersLinkItemButtonTitle,
        public readonly string $groupSelectLinkItemButtonTitle,

        public readonly string $imageItemAlt,
        public readonly string $imageItemTitle,

        public readonly string $adminLabel,
    ) {
    }
}
