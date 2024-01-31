<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopsListAjax;

use App\Twig\Components\Controls\PaginatorContentLoaderJs\PaginatorContentLoaderJsComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class ShopsListAjaxComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly PaginatorContentLoaderJsComponentDto $paginatorContentLoaderJsDto,
        public readonly string $shopCreateModalAttributeId,
        public readonly string|null $modalBeforeAttributeId,
        public readonly string $urlPathShopsImages,
        public readonly string $urlNoShopsImage
    ) {
    }
}
