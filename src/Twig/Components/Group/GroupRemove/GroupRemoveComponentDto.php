<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupRemove;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupRemoveComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $groupId,
        public readonly string $csrfToken
    ) {
    }
}
