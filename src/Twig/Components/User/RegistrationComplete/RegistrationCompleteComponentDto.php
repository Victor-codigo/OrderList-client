<?php

declare(strict_types=1);

namespace App\Twig\Components\User\RegistrationComplete;

use App\Twig\Components\TwigComponentDtoInterface;

class RegistrationCompleteComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $domainName
    ) {
    }
}
