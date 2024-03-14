<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\OrderProductAndShop;

use App\Twig\Components\TwigComponentDtoInterface;

class OrderProductAndShopComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $productIdFieldName,
        public readonly bool $productIdFieldRequired,
        public readonly string $shopIdFieldName,
        public readonly bool $shopIdFieldRequired,
    ) {
    }
}
