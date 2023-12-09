<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopList;

use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use App\Twig\Components\Shop\ShopList\ListItem\ShopListItemComponent;
use App\Twig\Components\Shop\ShopList\ListItem\ShopListItemComponentDto;
use App\Twig\Components\Shop\ShopList\List\ShopListComponent;
use App\Twig\Components\Shop\ShopList\List\ShopListComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class ShopListComponentBuilder
{
    private DtoBuilder $builder;

    private readonly array $errors;
    /**
     * @param ShopDataResponse[] $shops
     */
    private readonly array $shops;
    private readonly int $page;
    private readonly int $pageItems;
    private readonly int $pagesTotal;
    private readonly bool $validation;
    public readonly string|null $shopModifyFormCsrfToken;
    public readonly string|null $shopRemoveFormCsrfToken;
    public readonly string $shopModifyFormActionUrlPlaceholder;
    public readonly string $shopRemoveFormActionUrl;
    public readonly string $shopsIdFieldName;
    public readonly string $shopNoImagePath;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'validation',
            'pagination',
            'shops',
            'shopModifyForm',
            'shopRemoveForm',
        ]);
    }

    public function validation(array $errors, bool $validation): self
    {
        $this->builder->setMethodStatus('validation', true);

        $this->errors = $errors;
        $this->validation = $validation;

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->page = $page;
        $this->pageItems = $pageItems;
        $this->pagesTotal = $pagesTotal;

        return $this;
    }

    /**
     * @param ShopDataResponse[] $shops
     */
    public function shops(array $shops, string $shopNoImagePath, string $shopsIdFiledName): self
    {
        $this->builder->setMethodStatus('shops', true);

        $this->shops = $shops;
        $this->shopNoImagePath = $shopNoImagePath;
        $this->shopsIdFieldName = $shopsIdFiledName;

        return $this;
    }

    public function shopModifyForm(string|null $shopModifyFormCsrfToken, string $shopModifyFormActionUrlPlaceholder): self
    {
        $this->builder->setMethodStatus('shopModifyForm', true);

        $this->shopModifyFormCsrfToken = $shopModifyFormCsrfToken;
        $this->shopModifyFormActionUrlPlaceholder = $shopModifyFormActionUrlPlaceholder;

        return $this;
    }

    public function shopRemoveForm(string|null $shopRemoveFormCsrfToken, string $shopRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('shopRemoveForm', true);

        $this->shopRemoveFormCsrfToken = $shopRemoveFormCsrfToken;
        $this->shopRemoveFormActionUrl = $shopRemoveFormActionUrl;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function build(): ShopListComponentDto
    {
        $this->builder->validate();

        $paginator = $this->createPaginatorComponentDto($this->page, $this->pageItems, $this->pagesTotal);
        $shopsListItems = $this->createShopListItemComponentDto($this->shops);

        return $this->createShopListComponentDto(
            $paginator,
            $shopsListItems,
        );
    }

    /**
     * @param ShopListItemComponentDto[] $shopsListsItems
     */
    private function createShopListComponentDto(PaginatorComponentDto $paginatorDto, array $shopsListsItems): ShopListComponentDto
    {
        return new ShopListComponentDto(
            $this->errors,
            $shopsListsItems,
            $paginatorDto,
            $this->shopModifyFormCsrfToken,
            $this->shopRemoveFormCsrfToken,
            $this->validation,
            $this->shopModifyFormActionUrlPlaceholder,
            $this->shopRemoveFormActionUrl,
            $this->shopsIdFieldName,
            $this->shopNoImagePath,
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
                $this->shopsIdFieldName,
                $shopData->id,
                $shopData->name,
                $shopData->description,
                null === $shopData->image ? $this->shopNoImagePath : $shopData->image,
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
