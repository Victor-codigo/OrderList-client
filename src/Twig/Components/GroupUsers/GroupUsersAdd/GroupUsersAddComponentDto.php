<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersAdd;

use App\Twig\Components\TwigComponentDtoInterface;

class GroupUsersAddComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly ?string $name,
        public readonly ?string $groupId,
        public readonly ?string $groupName,
        public readonly ?string $csrfToken,
        public readonly bool $validForm,
        public readonly string $formActionAttribute,
    ) {
    }
}
