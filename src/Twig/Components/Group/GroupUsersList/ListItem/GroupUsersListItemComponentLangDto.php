<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersList\ListItem;

class GroupUsersListItemComponentLangDto
{
    public function __construct(
        public readonly string $imageGroupUserAlt,
        public readonly string $removeGroupUserAlt
    ) {
    }
}
