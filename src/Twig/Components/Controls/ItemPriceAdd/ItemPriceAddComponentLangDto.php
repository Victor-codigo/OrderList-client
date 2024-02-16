<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ItemPriceAdd;

class ItemPriceAddComponentLangDto
{
    /**
     * @param string[] $unitsMeasure
     */
    public function __construct(
        public readonly array $unitsMeasure
    ) {
    }
}
