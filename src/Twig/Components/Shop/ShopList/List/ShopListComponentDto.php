<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList\List;

use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class ShopListComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param ListShopListItemComponentDto[] $shops
     */
    public function __construct(
        public readonly array $errors,
        public readonly array $shops,
        public readonly PaginatorComponentDto $paginatorDto,
        public readonly string|null $shopModifyCsrfToken,
        public readonly string|null $shopRemoveFormCsrfToken,
        public readonly bool $validForm,
        public readonly string $shopModifyFormActionUrlPlaceholder,
        public readonly string $shopRemoveFormActionUrl,
        public readonly string $shopNoImagePath,
    ) {
    }
}
