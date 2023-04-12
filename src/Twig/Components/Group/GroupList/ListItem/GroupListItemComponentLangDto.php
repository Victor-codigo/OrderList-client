<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupList\ListItem;

class GroupListItemComponentLangDto
{
    public function __construct(
        public readonly string $imageGroupAlt,
        public readonly string $removeGroupAlt
    ) {
    }
}
