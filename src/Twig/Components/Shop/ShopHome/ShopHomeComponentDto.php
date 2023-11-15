<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome;

use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\TwigComponentDtoInterface;

class ShopHomeComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param array<int, ShopDataResponse> $shopsData
     */
    public function __construct(
        public readonly array $errors,
        public readonly array $shopsData,
        public readonly int $page,
        public readonly int $pageItems,
        public readonly int $pagesTotal,
        public readonly string $shopNoImagePath,
        public readonly string|null $csrfToken,
        public readonly bool $validForm,
    ) {
    }
}
