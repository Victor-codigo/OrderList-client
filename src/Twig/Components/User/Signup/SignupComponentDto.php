<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Signup;

use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class SignupComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array|null $errors,
        public readonly string|null $email,
        public readonly string|null $password,
        public readonly string|null $passwordRepeated,
        public readonly string|null $nick,
        public readonly string|null $csrfToken
    ) {
    }
}
