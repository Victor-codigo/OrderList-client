<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList;

use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\Shop\ShopList\ListItem\ShopListItemComponent;
use App\Twig\Components\Shop\ShopList\ListItem\ShopListItemComponentDto;
use App\Twig\Components\Shop\ShopList\List\ShopListComponent;
use App\Twig\Components\Shop\ShopList\List\ShopListComponentDto;

class ShopListComponentBuilder
{
    /**
     * @param arrayShopDataResponse[] $shops
     */
    public function __construct(
        private readonly array $errors,
        private readonly array $shops,
        private readonly string $imageNoShopImage,
        private readonly int $page,
        private readonly int $pageItems,
        private readonly int $pagesTotal,
        private readonly bool $validation,
        public readonly string|null $shopModifyCsrfToken,
        public readonly string|null $shopRemoveFormCsrfToken,
        public readonly string $shopModifyFormActionUrlPlaceholder,
        public readonly string $shopRemoveFormActionUrl,
        public readonly string $listItemShopIdFieldName,
        public readonly string $shopNoImagePath,
    ) {
    }

    public function __invoke(): ShopListComponentDto
    {
        $paginator = $this->createPaginatorComponentDto($this->page, $this->pageItems, $this->pagesTotal);
        $shops = $this->createShopListItemComponentDto($this->shops);

        return $this->createShopListComponentDto(
            $this->errors,
            $paginator,
            $shops,
            $this->validation,
            $this->shopModifyCsrfToken,
            $this->shopRemoveFormCsrfToken,
            $this->shopModifyFormActionUrlPlaceholder,
            $this->shopRemoveFormActionUrl,
            $this->shopNoImagePath
        );
    }

    private function createShopListComponentDto(
        array $errors,
        PaginatorComponentDto $paginatorDto,
        array $shops,
        bool $validation,
        string $shopModifyCsrfToken,
        string $shopDeleteCsrfToken,
        string $shopModifyFormActionUrlPlaceholder,
        string $shopRemoveFormActionUrl,
        string $shopNoImagePath
    ): ShopListComponentDto {
        return new ShopListComponentDto(
            $errors,
            $shops,
            $paginatorDto,
            $shopModifyCsrfToken,
            $shopDeleteCsrfToken,
            $validation,
            $shopModifyFormActionUrlPlaceholder,
            $shopRemoveFormActionUrl,
            $this->listItemShopIdFieldName,
            $shopNoImagePath,
        );
    }

    /**
     * @param ShopDataResponse[] $shops
     */
    private function createShopListItemComponentDto(array $shops): array
    {
        return array_map(
            fn (ShopDataResponse $shopData) => new ShopListItemComponentDto(
                ShopListItemComponent::getComponentName(),
                $this->listItemShopIdFieldName,
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
}
