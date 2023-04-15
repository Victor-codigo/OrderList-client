<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUserRemove;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupUserRemoveComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $groupId,
        public readonly string $userId,
        public readonly string $csrfToken
    ) {
    }
}
