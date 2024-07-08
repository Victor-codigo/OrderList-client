<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Login;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;

class LoginComponentLangDto
{
    public function __construct(
        public readonly string $title,
        // --
        public readonly string $emailLabel,
        public readonly string $emailPlaceholder,
        public readonly string $emailMsgInvalid,
        // --
        public readonly string $passwordLabel,
        public readonly string $passwordPlaceholder,
        public readonly string $passwordMsgInvalid,
        // --
        public readonly string $loginButton,
        public readonly string $rememberLogin,
        // --
        public readonly string $passwordForget,
        public readonly string $registerPreText,
        public readonly string $register,
        // --
        public readonly ?AlertValidationComponentDto $validationErrors
    ) {
    }
}
