<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\Home;

class OrderHomeSectionComponentLangDto
{
    public function __construct(
        public readonly string $currentBought,
        public readonly string $totalBought,
        public readonly string $buttonBackLabel,
        public readonly string $buttonBackTitle,
    ) {
    }
}
