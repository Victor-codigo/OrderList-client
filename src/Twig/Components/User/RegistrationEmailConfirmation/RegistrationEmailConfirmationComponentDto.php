<?php

declare(strict_types=1);

namespace App\Twig\Components\User\RegistrationEmailConfirmation;

use App\Twig\Components\TwigComponentDtoInterface;

class RegistrationEmailConfirmationComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $title,
        public readonly string $message
    ) {
    }
}
