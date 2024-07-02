<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Signup;

use App\Twig\Components\TwigComponentDtoInterface;

class SignupComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly ?array $messagesErrors,
        public readonly ?string $email,
        public readonly ?string $password,
        public readonly ?string $passwordRepeated,
        public readonly ?string $nick,
        public readonly ?string $csrfToken,
        public readonly string $formActionAttribute,
        public readonly bool $validForm
    ) {
    }
}
