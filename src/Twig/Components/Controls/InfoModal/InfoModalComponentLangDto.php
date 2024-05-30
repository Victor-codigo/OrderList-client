<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\InfoModal;

class InfoModalComponentLangDto
{
    public function __construct(
        public readonly string $closeButtonLabel,
        public readonly string $closeButtonTitle,
    ) {
    }
}
