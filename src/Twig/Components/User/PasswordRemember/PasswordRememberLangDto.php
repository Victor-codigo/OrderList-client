<?php

declare(strict_types=1);

namespace App\Twig\Components\User\PasswordRemember;

use App\Twig\Components\Alert\AlertComponentDto;

class PasswordRememberLangDto
{
    public function __construct(
        public string $title,
        public string $emailLabel,
        public string $emailPlaceholder,
        public string $emailMsgInvalid,
        public string $passwordRememberButton,
        public AlertComponentDto $validationErrors
    ) {
    }
}
