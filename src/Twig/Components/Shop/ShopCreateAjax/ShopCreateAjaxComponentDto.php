<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopCreateAjax;

use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class ShopCreateAjaxComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $groupId,
        public readonly ShopCreateComponentDto $shopCreateComponentDto,
        public readonly string $modalBeforeAttributeId
    ) {
    }
}
