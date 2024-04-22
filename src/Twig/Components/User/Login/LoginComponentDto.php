<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Login;

use App\Twig\Components\TwigComponentDtoInterface;

class LoginComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $messagesErrors,
        public readonly ?string $email,
        public readonly ?string $password,
        public readonly ?bool $rememberMe,
        public readonly ?string $csrfToken,
        public readonly string $actionAttribute,
        public readonly bool $validForm,
    ) {
    }
}
