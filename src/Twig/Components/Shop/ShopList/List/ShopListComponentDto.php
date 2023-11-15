<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList\List;

use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class ShopListComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param array<int, ListShopListItemComponentDto> $shops
     */
    public function __construct(
        public readonly array $errors,
        public readonly array $shops,
        public readonly PaginatorComponentDto $paginatorDto,
        public readonly string $csrfToken,
        // public readonly ModalComponentDto $listShopAddModalDto,
        public readonly bool $validForm,
    ) {
    }
}
