<?php

declare(strict_types=1);

namespace App\Twig\Components\User\PasswordRemember;

use App\Twig\Components\TwigComponentDtoInterface;

class PasswordRememberDto implements TwigComponentDtoInterface
{
    public function __construct(
        public array $errors,
        public string|null $email,
        public string|null $csrfToken
    ) {
    }
}
