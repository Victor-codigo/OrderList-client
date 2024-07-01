<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\InfoModal;

use App\Twig\Components\TwigComponentDtoInterface;

class InfoModalComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $idAttribute,
        public readonly string $title,
        public readonly string $content,
        public readonly INFO_MODAL_TYPE $type
    ) {
    }
}
