<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersList\List;

use App\Twig\Components\Alert\AlertComponentDto;

class GroupUsersListComponentLangDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $listEmptyMessage,
        public readonly string $listEmptyIconAlt,
        public readonly AlertComponentDto|null $validationErrors,
    ) {
    }
}
