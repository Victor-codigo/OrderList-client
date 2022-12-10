<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Signup;

use App\Twig\Components\Alert\AlertComponentDto;

class SignupComponentLangDto
{
    public function __construct(
        public readonly string $title,
        // --
        public readonly string $emailLabel,
        public readonly string $emailMsgInvalid,
        public readonly string $emailPlaceholder,
        // --
        public readonly string $passwordLabel,
        public readonly string $passwordPlaceholder,
        public readonly string $passwordMsgInvalid,
        // --
        public readonly string $passwordRepeatedLabel,
        public readonly string $passwordRepeatedPlaceholder,
        public readonly string $passwordRepeatedMsgInvalid,
        // --
        public readonly string $nickLabel,
        public readonly string $nickPlaceholder,
        public readonly string $nickMsgInvalid,
        // --
        public readonly string $loginButton,
        public readonly string $loginLink,
        public readonly AlertComponentDto $validationErrors
    ) {
    }
}
