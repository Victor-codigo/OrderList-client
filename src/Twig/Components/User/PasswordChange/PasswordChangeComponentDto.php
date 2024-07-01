<?php

declare(strict_types=1);

namespace App\Twig\Components\User\PasswordChange;

use App\Twig\Components\TwigComponentDtoInterface;

class PasswordChangeComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $userId,
        public readonly ?string $passwordOld,
        public readonly ?string $passwordNew,
        public readonly ?string $passwordNewRepeat,
        public readonly ?string $csrfToken,
        public readonly bool $passwordOldRequired,
        public readonly string $actionAttribute,
        public readonly bool $validForm
    ) {
    }
}
