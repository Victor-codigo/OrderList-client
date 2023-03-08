<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\DropZone;

use App\Twig\Components\TwigComponentDtoInterface;

class DropZoneComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string|null $componentId = null,
        public readonly string|null $formName = null,
        public readonly string|null $labelField = null,
        public readonly string|null $nameField = null,
        public readonly string|null $placeholderField = null,
        ) {
    }
}
