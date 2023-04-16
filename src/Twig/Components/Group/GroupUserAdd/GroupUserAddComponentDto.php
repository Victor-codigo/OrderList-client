<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUserAdd;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupUserAddComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string|null $name,
        public readonly string|null $groupId,
        public readonly string|null $groupName,
        public readonly string|null $csrfToken,
        public readonly bool $validForm,
    ) {
    }
}
