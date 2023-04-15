<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersList\ListItem;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupUsersListItemComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $groupId,
        public readonly string $userId,
        public readonly string $userName,
        public readonly string $userImage,
        public readonly bool $admin,
        public readonly bool $userSessionIsAdmin,
    ) {
    }
}
