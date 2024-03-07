<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductHome;

use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Product\ProductRemoveMulti\PRODUCT_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\Controls\ContentLoaderJs\ContentLoaderJsComponentDto;
use App\Twig\Components\Controls\PaginatorContentLoaderJs\PaginatorContentLoaderJsComponentDto;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\PaginatorJs\PaginatorJsComponentDto;
use App\Twig\Components\Product\ProductCreate\ProductCreateComponent;
use App\Twig\Components\Product\ProductCreate\ProductCreateComponentDto;
use App\Twig\Components\Product\ProductHome\Home\ProductHomeSectionComponentDto;
use App\Twig\Components\Product\ProductHome\ListItem\ProductListItemComponent;
use App\Twig\Components\Product\ProductHome\ListItem\ProductListItemComponentDto;
use App\Twig\Components\Product\ProductInfo\ProductInfoComponent;
use App\Twig\Components\Product\ProductInfo\ProductInfoComponentDto;
use App\Twig\Components\Product\ProductModify\ProductModifyComponent;
use App\Twig\Components\Product\ProductModify\ProductModifyComponentDto;
use App\Twig\Components\Product\ProductRemove\ProductRemoveComponent;
use App\Twig\Components\Product\ProductRemove\ProductRemoveComponentDto;
use App\Twig\Components\Shop\ShopCreateAjax\ShopCreateAjaxComponent;
use App\Twig\Components\Shop\ShopCreateAjax\ShopCreateAjaxComponentDto;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use App\Twig\Components\Shop\ShopsListAjax\ShopsListAjaxComponent;
use App\Twig\Components\Shop\ShopsListAjax\ShopsListAjaxComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ProductHomeComponentBuilder implements DtoBuilderInterface
{
    private const PRODUCT_CREATE_MODAL_ID = 'product_create_modal';
    private const PRODUCT_REMOVE_MULTI_MODAL_ID = 'product_remove_multi_modal';
    private const PRODUCT_DELETE_MODAL_ID = 'product_delete_modal';
    private const PRODUCT_MODIFY_MODAL_ID = 'product_modify_modal';
    private const PRODUCT_INFO_MODAL_ID = 'product_info_modal';
    private const SHOP_LIST_MODAL_ID = 'shop_list_select_modal';
    private const SHOP_CREATE_MODAL_ID = 'shop_create_modal';

    private const PRODUCT_HOME_COMPONENT_NAME = 'ProductHomeComponent';
    private const PRODUCT_HOME_LIST_COMPONENT_NAME = 'ProductHomeListComponent';
    private const PRODUCT_HOME_LIST_ITEM_COMPONENT_NAME = 'ProductHomeListItemComponent';

    private const SHOP_LIST_PAGE_NUM_ITEMS = Config::MODAL_LIST_ITEMS_MAX_NUMBER;
    private const SHOP_LIST_RESPONSE_INDEX_NAME = 'shops';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $shopsListAjaxModalDto;
    private readonly ModalComponentDto $shopCreateModalDto;
    private readonly ModalComponentDto $productInfoModalDto;

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
            'productCreateModal',
            'productModifyFormModal',
            'productRemoveMultiModal',
            'productRemoveFormModal',
            'shopsListModal',
            'shopCreateModal',
            'errors',
            'pagination',
            'listItems',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = $this->createHomeSectionComponentDto();
    }

    public function productCreateFormModal(string $productCreateFormCsrfToken, ?float $productPrice, string $productCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('productCreateModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createProductCreateComponentDto($productCreateFormCsrfToken, $productPrice, $productCreateFormActionUrl)
        );

        return $this;
    }

    public function productModifyFormModal(string $productModifyFormCsrfToken, string $productModifyFormActionUrl): self
    {
        $this->builder->setMethodStatus('productModifyFormModal', true);

        $this->homeSectionComponentDto->modifyFormModal(
            $this->createProductModifyModalDto($productModifyFormCsrfToken, $productModifyFormActionUrl)
        );

        return $this;
    }

    public function productRemoveMultiFormModal(string $productRemoveMultiFormCsrfToken, string $productRemoveMultiFormActionUrl): self
    {
        $this->builder->setMethodStatus('productRemoveMultiModal', true);

        $this->homeSectionComponentDto->removeMultiFormModal(
            $this->createProductRemoveMultiComponentDto($productRemoveMultiFormCsrfToken, $productRemoveMultiFormActionUrl)
        );

        return $this;
    }

    public function productRemoveFormModal(string $productRemoveFormCsrfToken, string $productRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('productRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createProductRemoveModalDto($productRemoveFormCsrfToken, $productRemoveFormActionUrl)
        );

        return $this;
    }

    public function shopsListModal(string $groupId, string $urlPathToShopImages, string $urlImageShopNoImage): self
    {
        $this->builder->setMethodStatus('shopsListModal', true);

        $this->shopsListAjaxModalDto = $this->createShopListItemsModalDto($groupId, $urlPathToShopImages, $urlImageShopNoImage);

        return $this;
    }

    public function shopCreateModal(string $groupId, string $csrfToken, string $shopCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('shopCreateModal', true);

        $this->shopCreateModalDto = $this->createShopModalDto($groupId, $csrfToken, $shopCreateFormActionUrl);

        return $this;
    }

    /**
     * @param string[] $productSectionValidationOk
     * @param string[] $productValidationErrorsMessage
     */
    public function errors(array $productSectionValidationOk, array $productValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($productSectionValidationOk, $productValidationErrorsMessage);

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    public function listItems(array $listProductsData, array $listShopsData, array $listProductsShopsPriceData): self
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
        ?string $searchValue,
        ?string $sectionFilterValue,
        ?string $nameFilterValue,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->homeSectionComponentDto->searchBar(new SearchBarComponentDto(
            $groupId,
            $searchValue,
            [SECTION_FILTERS::PRODUCT, SECTION_FILTERS::SHOP],
            $sectionFilterValue,
            $nameFilterValue,
            $searchBarCsrfToken,
            $searchBarFormActionUrl,
            $searchAutoCompleteUrl,
        ));

        return $this;
    }

    public function build(): ProductHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::PRODUCT_HOME_COMPONENT_NAME,
            self::PRODUCT_HOME_LIST_COMPONENT_NAME,
            self::PRODUCT_HOME_LIST_ITEM_COMPONENT_NAME,
        );
        $this->homeSectionComponentDto->createRemoveMultiForm(
            $this->createRemoveMultiFormDto()
        );
        $this->homeSectionComponentDto->listItems(
            ProductListItemComponent::getComponentName(),
            $this->createProductListItemsComponentsDto(),
            Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
        );

        $this->productInfoModalDto = $this->createProductInfoModalDto();

        return $this->createProductHomeSectionComponentDto($this->shopsListAjaxModalDto, $this->shopCreateModalDto, $this->productInfoModalDto);
    }

    private function createProductCreateComponentDto(string $productCreateFormCsrfToken, ?float $productPrice, string $productCreateFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new ProductCreateComponentDto(
            [],
            '',
            $productPrice,
            $productCreateFormCsrfToken,
            false,
            mb_strtolower($productCreateFormActionUrl),
        );

        return new ModalComponentDto(
            self::PRODUCT_CREATE_MODAL_ID,
            '',
            false,
            ProductCreateComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createProductRemoveMultiComponentDto(string $productRemoveMultiFormCsrfToken, string $productRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new ProductRemoveComponentDto(
            ProductRemoveComponent::getComponentName(),
            [],
            $productRemoveMultiFormCsrfToken,
            mb_strtolower($productRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::PRODUCT_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            ProductRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createProductRemoveModalDto(string $productRemoveFormCsrfToken, string $productRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new ProductRemoveComponentDto(
            ProductRemoveComponent::getComponentName(),
            [],
            $productRemoveFormCsrfToken,
            mb_strtolower($productRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::PRODUCT_DELETE_MODAL_ID,
            '',
            false,
            ProductRemoveComponent::getComponentName(),
            $homeModalDelete,
            []
        );
    }

    private function createRemoveMultiFormDto(): RemoveMultiFormDto
    {
        return new RemoveMultiFormDto(
            PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::TOKEN),
            sprintf('%s[%s]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::PRODUCTS_ID),
            self::PRODUCT_REMOVE_MULTI_MODAL_ID
        );
    }

    private function createProductModifyModalDto(string $productModifyFormCsrfToken, string $productModifyFormActionUrlPlaceholder): ModalComponentDto
    {
        $homeModalModify = new ProductModifyComponentDto(
            [],
            '{name_placeholder}',
            '{description_placeholder}',
            '{image_placeholder}',
            Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
            $productModifyFormCsrfToken,
            false,
            mb_strtolower($productModifyFormActionUrlPlaceholder),
            self::SHOP_LIST_MODAL_ID,
            self::PRODUCT_MODIFY_MODAL_ID
        );

        return new ModalComponentDto(
            self::PRODUCT_MODIFY_MODAL_ID,
            '',
            false,
            ProductModifyComponent::getComponentName(),
            $homeModalModify,
            [],
        );
    }

    private function createProductListItemsComponentsDto(): array
    {
        $shopsIdById = array_combine(
            array_map(
                fn (ShopDataResponse $shopData) => $shopData->id,
                $this->listShopsData
            ),
            $this->listShopsData
        );

        $thisArg = $this;
        $productListItems = array_map(
            function (ProductDataResponse $productData) use ($thisArg, $shopsIdById) {
                $productShopsPriceData = array_filter(
                    $thisArg->listProductsShopPricesData,
                    fn (ProductShopPriceDataResponse $productsShopPrice) => $productsShopPrice->productId === $productData->id
                );

                $productShopsData = array_map(
                    fn (ProductShopPriceDataResponse $productShopPrice) => $shopsIdById[$productShopPrice->shopId],
                    $productShopsPriceData,
                );

                return [
                    'productData' => $productData,
                    'shopsData' => array_values($productShopsData),
                    'shopsPricesData' => array_values($productShopsPriceData),
                ];
            },
            $this->listProductsData
        );

        return array_map(
            fn (array $listItemData) => new ProductListItemComponentDto(
                ProductListItemComponent::getComponentName(),
                $listItemData['productData']->id,
                $listItemData['productData']->name,
                self::PRODUCT_MODIFY_MODAL_ID,
                self::PRODUCT_DELETE_MODAL_ID,
                self::PRODUCT_INFO_MODAL_ID,
                self::PRODUCT_HOME_LIST_ITEM_COMPONENT_NAME,
                $listItemData['productData']->description,
                $listItemData['productData']->image ?? Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                $listItemData['productData']->createdOn,
                $listItemData['shopsData'],
                $listItemData['shopsPricesData']
            ),
            $productListItems
        );
    }

    private function createShopListItemsModalDto(string $groupId, string $urlPathToShopImages, string $urlImageShopNoImage): ModalComponentDto
    {
        $pageCurrent = 1;
        $contentLoaderJsDto = new ContentLoaderJsComponentDto(
            'getShopsData',
            [
                'group_id' => $groupId,
                'page' => $pageCurrent,
                'page_items' => self::SHOP_LIST_PAGE_NUM_ITEMS,
            ],
            self::SHOP_LIST_RESPONSE_INDEX_NAME,
        );
        $paginatorJsDto = new PaginatorJsComponentDto($pageCurrent, 1);
        $paginatorContentLoaderJsDto = new PaginatorContentLoaderJsComponentDto($contentLoaderJsDto, $paginatorJsDto);

        $shopListAjaxComponentDto = new ShopsListAjaxComponentDto(
            ShopsListAjaxComponent::getComponentName(),
            $paginatorContentLoaderJsDto,
            $urlPathToShopImages,
            $urlImageShopNoImage,
        );

        return new ModalComponentDto(
            self::SHOP_LIST_MODAL_ID,
            '',
            false,
            ShopsListAjaxComponent::getComponentName(),
            $shopListAjaxComponentDto,
            []
        );
    }

    private function createShopModalDto(string $groupId, string $csrfToken, string $shopCreateFormActionUrl): ModalComponentDto
    {
        $shopCreateComponentDto = new ShopCreateComponentDto(
            [],
            null,
            null,
            $csrfToken,
            false,
            $shopCreateFormActionUrl,
        );

        return new ModalComponentDto(
            self::SHOP_CREATE_MODAL_ID,
            '',
            false,
            ShopCreateAjaxComponent::getComponentName(),
            new ShopCreateAjaxComponentDto($groupId, $shopCreateComponentDto),
            []
        );
    }

    private function createProductInfoModalDto(): ModalComponentDto
    {
        $productInfoComponentDto = new ProductInfoComponentDto(
            ProductInfoComponent::getComponentName()
        );

        return new ModalComponentDto(
            self::PRODUCT_INFO_MODAL_ID,
            '',
            false,
            ProductInfoComponent::getComponentName(),
            $productInfoComponentDto,
            []
        );
    }

    private function createHomeSectionComponentDto(): HomeSectionComponentDto
    {
        return new HomeSectionComponentDto();
    }

    private function createProductHomeSectionComponentDto(ModalComponentDto $productListItemsModalDto, ModalComponentDto $productCreateModalDto, ModalComponentDto $productInfoModalDto): ProductHomeSectionComponentDto
    {
        return (new ProductHomeSectionComponentDto())
            ->homeSection(
                $this->homeSectionComponentDto
            )
            ->listItemsModal(
                $productListItemsModalDto
            )
            ->shopCreateModal(
                $productCreateModalDto
            )
            ->productInfoModal(
                $productInfoModalDto
            )
            ->build();
    }
}
