<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList;

use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\Shop\ShopList\ListItem\ShopListItemComponent;
use App\Twig\Components\Shop\ShopList\ListItem\ShopListItemComponentDto;
use App\Twig\Components\Shop\ShopList\List\ShopListComponent;
use App\Twig\Components\Shop\ShopList\List\ShopListComponentDto;

class ShopListComponentBuilder
{
    /**
     * @param array<int, ShopDataResponse> $shops
     */
    public function __construct(
        private readonly array $errors,
        private readonly array $shops,
        private readonly string $imageNoShopImage,
        private readonly int $page,
        private readonly int $pageItems,
        private readonly int $pagesTotal,
        private readonly bool $validation,
        private readonly string $csrfToken,
    ) {
    }

    public function __invoke(): ShopListComponentDto
    {
        $paginator = $this->createPaginatorComponentDto($this->page, $this->pageItems, $this->pagesTotal);
        $shops = $this->createShopListItemComponentDto($this->shops);

        return $this->createShopListComponentDto($this->errors, $paginator, $shops, $this->validation, $this->csrfToken);
    }

    private function createShopListComponentDto(array $errors, PaginatorComponentDto $paginatorDto, array $shops, bool $validation, string $csrfToken): ShopListComponentDto
    {
        return new ShopListComponentDto(
            $errors,
            $shops,
            $paginatorDto,
            $csrfToken,
            $validation
        );
    }

    /**
     * @param array<int, ShopDataResponse> $shops
     */
    private function createShopListItemComponentDto(array $shops): array
    {
        return array_map(
            fn (ShopDataResponse $shopData) => new ShopListItemComponentDto(
                ShopListItemComponent::getComponentName(),
                $shopData->id,
                $shopData->name,
                $shopData->description,
                null === $shopData->image ? $this->imageNoShopImage : $shopData->image,
                $shopData->createdOn,
                ShopListComponent::SHOP_MODIFY_MODAL_ID,
                ShopListComponent::SHOP_DELETE_MODAL_ID
            ),
            $shops
        );
    }

    private function createPaginatorComponentDto(int $page, int $pageItems, int $pagesTotal): PaginatorComponentDto
    {
        return new PaginatorComponentDto($page, $pagesTotal, "page-{pageNum}-{$pageItems}");
    }

    private function createModal(): ModalComponentDto
    {
        return new ModalComponentDto(
            'shop_remove_modal',
            'Esto es una prueba',
            false,
            '',
            '',
            []
        );
    }
}
