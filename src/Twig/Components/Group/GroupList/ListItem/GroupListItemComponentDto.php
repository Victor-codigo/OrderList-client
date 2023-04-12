<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupList\ListItem;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupListItemComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $image,
        public readonly bool $admin,
    ) {
    }
}
