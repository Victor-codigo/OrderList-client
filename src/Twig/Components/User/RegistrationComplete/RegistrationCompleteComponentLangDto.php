<?php

declare(strict_types=1);

namespace App\Twig\Components\User\RegistrationComplete;

class RegistrationCompleteComponentLangDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $message,
    ) {
    }
}
