<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductCreateAjax;

use App\Twig\Components\Product\ProductCreate\ProductCreateComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class ProductCreateAjaxComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $groupId,
        public readonly ProductCreateComponentDto $productCreateComponentDto
    ) {
    }
}
