<?php

declare(strict_types=1);

namespace App\Twig\Components\Alert;

use App\Twig\Components\TwigComponentDtoInterface;

class AlertComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly ALERT_TYPE $type,
        public readonly string|null $title,
        public readonly string|null $subtitle,
        public readonly array|string $messages,
        public readonly bool $escapeMesage = true
    ) {
    }
}
