<?php

declare(strict_types=1);

namespace App\Twig\Components\AlertValidation;

use App\Twig\Components\TwigComponentDtoInterface;

class AlertValidationComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $messageValidationOk = [],
        public readonly array $messageErrors = []
    ) {
    }
}
