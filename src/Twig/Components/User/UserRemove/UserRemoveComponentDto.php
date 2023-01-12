<?php

declare(strict_types=1);

namespace App\Twig\Components\User\UserRemove;

use App\Twig\Components\TwigComponentDtoInterface;

class UserRemoveComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $userId,
        public readonly string $csrfToken
    ) {
    }
}
