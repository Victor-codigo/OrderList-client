<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ButtonLoading;

use App\Twig\Components\TwigComponentDtoInterface;

class ButtonLoadingComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly ?string $selector,
        public readonly string $type,
        public readonly string $label,
        public readonly string $loadingLabel,
        public readonly ?string $title,
        public readonly ?string $icon,
    ) {
    }
}
