<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\Title;

use App\Twig\Components\TwigComponentDtoInterface;

class TitleComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $titleLabel
    ) {
    }
}
