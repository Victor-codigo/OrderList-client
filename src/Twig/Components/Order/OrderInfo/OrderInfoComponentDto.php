<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentDto;

class OrderInfoComponentDto extends ItemInfoComponentDto
{
    public function __construct(
        public readonly string $componentName,
        public readonly string $iconBoughtPath,
        public readonly string $iconNotBoughtPath,
    ) {
    }
}
