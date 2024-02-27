<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome;

use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\Controls\ContentLoaderJs\ContentLoaderJsComponentDto;
use App\Twig\Components\Controls\PaginatorContentLoaderJs\PaginatorContentLoaderJsComponentDto;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\PaginatorJs\PaginatorJsComponentDto;
use App\Twig\Components\Product\ProductCreateAjax\ProductCreateAjaxComponent;
use App\Twig\Components\Product\ProductCreateAjax\ProductCreateAjaxComponentDto;
use App\Twig\Components\Product\ProductCreate\ProductCreateComponentDto;
use App\Twig\Components\Product\ProductsListAjax\ProductsListAjaxComponent;
use App\Twig\Components\Product\ProductsListAjax\ProductsListAjaxComponentDto;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use App\Twig\Components\Shop\ShopHome\Home\ShopHomeSectionComponentDto;
use App\Twig\Components\Shop\ShopHome\ListItem\ShopListItemComponent;
use App\Twig\Components\Shop\ShopHome\ListItem\ShopListItemComponentDto;
use App\Twig\Components\Shop\ShopInfo\ShopInfoComponent;
use App\Twig\Components\Shop\ShopInfo\ShopInfoComponentDto;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponent;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponentDto;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponent;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ShopHomeComponentBuilder implements DtoBuilderInterface
{
    private const SHOP_CREATE_MODAL_ID = 'shop_create_modal';
    private const SHOP_REMOVE_MULTI_MODAL_ID = 'shop_remove_multi_modal';
    public const SHOP_DELETE_MODAL_ID = 'shop_delete_modal';
    public const SHOP_MODIFY_MODAL_ID = 'shop_modify_modal';
    public const SHOP_INFO_MODAL_ID = 'shop_info_modal';
    private const PRODUCT_LIST_MODAL_ID = 'product_list_select_modal';
    private const PRODUCT_CREATE_MODAL_ID = 'product_create_modal';

    private const SHOP_HOME_COMPONENT_NAME = 'ShopHomeComponent';
    private const SHOP_HOME_LIST_COMPONENT_NAME = 'ShopHomeListComponent';
    private const SHOP_HOME_LIST_ITEM_COMPONENT_NAME = 'ShopHomeListItemComponent';

    private const PRODUCT_LIST_PAGE_NUM_ITEMS = Config::MODAL_LIST_ITEMS_MAX_NUMBER;
    private const PRODUCT_LIST_RESPONSE_INDEX_NAME = 'products';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $productsListAjaxModalDto;
    private readonly ModalComponentDto $productCreateModalDto;
    private readonly ModalComponentDto $shopInfoModalDto;

    /**
     * @var ProductDataResponse[]
     */
    private readonly array $listProductsData;
    /**
     * @var ShopDataResponse[]
     */
    private readonly array $listShopsData;
    /**
     * @var ProductShopPriceDataResponse[]
     */
    private readonly array $listProductsShopPricesData;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'shopCreateModal',
            'shopModifyFormModal',
            'shopRemoveMultiModal',
            'shopRemoveFormModal',
            'productsListModal',
            'productCreateModal',
            'errors',
            'pagination',
            'listItems',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = new HomeSectionComponentDto();
    }

    public function shopCreateFormModal(string $shopCreateFormCsrfToken, string $shopCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('shopCreateModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createShopCreateComponentDto($shopCreateFormCsrfToken, $shopCreateFormActionUrl)
        );

        return $this;
    }

    public function shopModifyFormModal(string $shopModifyFormCsrfToken, string $shopModifyFormActionUrl): self
    {
        $this->builder->setMethodStatus('shopModifyFormModal', true);

        $this->homeSectionComponentDto->modifyFormModal(
            $this->createShopModifyModalDto($shopModifyFormCsrfToken, $shopModifyFormActionUrl)
        );

        return $this;
    }

    public function shopRemoveMultiFormModal(string $shopRemoveMultiFormCsrfToken, string $shopRemoveMultiFormActionUrl): self
    {
        $this->builder->setMethodStatus('shopRemoveMultiModal', true);

        $this->homeSectionComponentDto->removeMultiFormModal(
            $this->createShopRemoveMultiComponentDto($shopRemoveMultiFormCsrfToken, $shopRemoveMultiFormActionUrl)
        );

        return $this;
    }

    public function shopRemoveFormModal(string $shopRemoveFormCsrfToken, string $shopRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('shopRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createShopRemoveModalDto($shopRemoveFormCsrfToken, $shopRemoveFormActionUrl)
        );

        return $this;
    }

    public function productsListModal(string $groupId, string $urlPathToProductImages, string $urlImageProductNoImage): self
    {
        $this->builder->setMethodStatus('productsListModal', true);

        $this->productsListAjaxModalDto = $this->createProductListItemsModalDto($groupId, $urlPathToProductImages, $urlImageProductNoImage);

        return $this;
    }

    public function productCreateModal(string $groupId, string $csrfToken, string $productCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('productCreateModal', true);

        $this->productCreateModalDto = $this->createProductModalDto($groupId, $csrfToken, $productCreateFormActionUrl);

        return $this;
    }

    public function errors(array $shopSectionValidationOk, array $shopValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($shopSectionValidationOk, $shopValidationErrorsMessage);

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    /**
     * @param ShopListItemComponentDto[] $listShopsData
     */
    public function listItems(array $listShopsData, array $listProductsData, array $listProductsShopsPriceData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listProductsData = $listProductsData;
        $this->listShopsData = $listShopsData;
        $this->listProductsShopPricesData = $listProductsShopsPriceData;

        return $this;
    }

    public function validation(bool $validForm): self
    {
        $this->builder->setMethodStatus('validation', true);

        $this->homeSectionComponentDto->validation(
            $validForm,
        );

        return $this;
    }

    public function searchBar(
        string $groupId,
        string|null $searchValue,
        string|null $nameFilterValue,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->homeSectionComponentDto->searchBar(new SearchBarComponentDto(
            $groupId,
            $searchValue,
            [SECTION_FILTERS::SHOP],
            null,
            $nameFilterValue,
            $searchBarCsrfToken,
            $searchBarFormActionUrl,
            $searchAutoCompleteUrl,
        ));

        return $this;
    }

    public function build(): ShopHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::SHOP_HOME_COMPONENT_NAME,
            self::SHOP_HOME_LIST_COMPONENT_NAME,
            self::SHOP_HOME_LIST_ITEM_COMPONENT_NAME
        );
        $this->homeSectionComponentDto->createRemoveMultiForm(
            $this->createRemoveMultiFormDto()
        );
        $this->homeSectionComponentDto->listItems(
            ShopListItemComponent::getComponentName(),
            $this->createShopListItemComponentDto(),
            Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
        );
        $this->shopInfoModalDto = $this->createShopInfoModalDto();

        return $this->createShopHomeSectionComponentDto($this->productsListAjaxModalDto, $this->productCreateModalDto, $this->shopInfoModalDto);
    }

    private function createShopCreateComponentDto(string $shopCreateFormCsrfToken, string $shopCreateFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new ShopCreateComponentDto(
            [],
            '',
            '',
            $shopCreateFormCsrfToken,
            false,
            mb_strtolower($shopCreateFormActionUrl)
        );

        return new ModalComponentDto(
            self::SHOP_CREATE_MODAL_ID,
            '',
            false,
            ShopCreateComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createShopRemoveMultiComponentDto(string $shopRemoveMultiFormCsrfToken, string $shopRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new ShopRemoveComponentDto(
            ShopRemoveComponent::getComponentName(),
            [],
            $shopRemoveMultiFormCsrfToken,
            mb_strtolower($shopRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::SHOP_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createShopRemoveModalDto(string $shopRemoveFormCsrfToken, string $shopRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new ShopRemoveComponentDto(
            ShopRemoveComponent::getComponentName(),
            [],
            $shopRemoveFormCsrfToken,
            mb_strtolower($shopRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::SHOP_DELETE_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
            $homeModalDelete,
            []
        );
    }

    private function createShopModifyModalDto(string $shopModifyFormCsrfToken, string $shopModifyFormActionUrlPlaceholder): ModalComponentDto
    {
        $homeModalModify = new ShopModifyComponentDto(
            [],
            '{name_placeholder}',
            '{description_placeholder}',
            '{image_placeholder}',
            Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
            $shopModifyFormCsrfToken,
            false,
            mb_strtolower($shopModifyFormActionUrlPlaceholder)
        );

        return new ModalComponentDto(
            self::SHOP_MODIFY_MODAL_ID,
            '',
            false,
            ShopModifyComponent::getComponentName(),
            $homeModalModify,
            []
        );
    }

    private function createRemoveMultiFormDto(): RemoveMultiFormDto
    {
        return new RemoveMultiFormDto(
            SHOP_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::TOKEN),
            sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SHOPS_ID),
            self::SHOP_REMOVE_MULTI_MODAL_ID
        );
    }

    private function createShopInfoModalDto(): ModalComponentDto
    {
        $productInfoComponentDto = new ShopInfoComponentDto(
            ShopInfoComponent::getComponentName()
        );

        return new ModalComponentDto(
            self::SHOP_INFO_MODAL_ID,
            '',
            false,
            ShopInfoComponent::getComponentName(),
            $productInfoComponentDto,
            []
        );
    }

    private function createShopListItemComponentDto(): array
    {
        $productsIdById = array_combine(
            array_map(
                fn (ProductDataResponse $productData) => $productData->id,
                $this->listProductsData
            ),
            $this->listProductsData
        );

        $thisArg = $this;
        $shopListItems = array_map(
            function (ShopDataResponse $shopData) use ($thisArg, $productsIdById) {
                $productShopsPriceData = array_filter(
                    $thisArg->listProductsShopPricesData,
                    fn (ProductShopPriceDataResponse $productsShopPrice) => $productsShopPrice->shopId === $shopData->id
                );

                $shopShopsData = array_map(
                    fn (ProductShopPriceDataResponse $productShopPrice) => $productsIdById[$productShopPrice->productId],
                    $productShopsPriceData,
                );

                return [
                    'shopData' => $shopData,
                    'productsData' => array_values($shopShopsData),
                    'productsPricesData' => array_values($productShopsPriceData),
                ];
            },
            $this->listShopsData
        );

        return array_map(
            fn (array $listItemData) => new ShopListItemComponentDto(
                ShopListItemComponent::getComponentName(),
                $listItemData['shopData']->id,
                $listItemData['shopData']->name,
                self::SHOP_MODIFY_MODAL_ID,
                self::SHOP_DELETE_MODAL_ID,
                self::SHOP_INFO_MODAL_ID,
                self::SHOP_HOME_LIST_ITEM_COMPONENT_NAME,
                $listItemData['shopData']->description,
                $listItemData['shopData']->image ?? Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                $listItemData['shopData']->createdOn,
                $listItemData['productsData'],
                $listItemData['productsPricesData']
            ),
            $shopListItems
        );
    }

    private function createProductListItemsModalDto(string $groupId, string $urlPathToShopImages, string $urlImageShopNoImage): ModalComponentDto
    {
        $pageCurrent = 1;
        $contentLoaderJsDto = new ContentLoaderJsComponentDto(
            'getProductsData',
            [
                'group_id' => $groupId,
                'page' => $pageCurrent,
                'page_items' => self::PRODUCT_LIST_PAGE_NUM_ITEMS,
            ],
            self::PRODUCT_LIST_RESPONSE_INDEX_NAME,
        );
        $paginatorJsDto = new PaginatorJsComponentDto($pageCurrent, 1);
        $paginatorContentLoaderJsDto = new PaginatorContentLoaderJsComponentDto($contentLoaderJsDto, $paginatorJsDto);

        $shopListAjaxComponentDto = new ProductsListAjaxComponentDto(
            ProductsListAjaxComponent::getComponentName(),
            $paginatorContentLoaderJsDto,
            $urlPathToShopImages,
            $urlImageShopNoImage,
        );

        return new ModalComponentDto(
            self::PRODUCT_LIST_MODAL_ID,
            '',
            false,
            ProductsListAjaxComponent::getComponentName(),
            $shopListAjaxComponentDto,
            []
        );
    }

    private function createProductModalDto(string $groupId, string $csrfToken, string $productCreateFormActionUrl): ModalComponentDto
    {
        $productCreateComponentDto = new ProductCreateComponentDto(
            [],
            null,
            null,
            $csrfToken,
            false,
            $productCreateFormActionUrl,
        );

        return new ModalComponentDto(
            self::PRODUCT_CREATE_MODAL_ID,
            '',
            false,
            ProductCreateAjaxComponent::getComponentName(),
            new ProductCreateAjaxComponentDto($groupId, $productCreateComponentDto),
            []
        );
    }

    private function createShopHomeSectionComponentDto(ModalComponentDto $productListItemsModalDto, ModalComponentDto $productCreateModalDto, ModalComponentDto $shopInfoModalDto): ShopHomeSectionComponentDto
    {
        return (new ShopHomeSectionComponentDto())
            ->homeSection(
                $this->homeSectionComponentDto
            )
            ->listItemsModal(
                $productListItemsModalDto
            )
            ->productCreateModal(
                $productCreateModalDto
            )
            ->shopInfoModal(
                $shopInfoModalDto
            )
            ->build();
    }
}
