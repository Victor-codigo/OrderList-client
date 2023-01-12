<?php

declare(strict_types=1);

namespace App\Twig\Components\User\PasswordChange;

use App\Twig\Components\TwigComponentDtoInterface;

class PasswordChangeComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string|null $passwordOld,
        public readonly string|null $passwordNew,
        public readonly string|null $passwordNewRepeat,
        public readonly string|null $csrfToken,
        public readonly bool $passwordOldRequired = true
    ) {
    }
}
