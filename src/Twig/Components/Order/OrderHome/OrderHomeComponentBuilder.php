<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome;

use App\Controller\Request\Response\OrderDataResponse;
use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\Controls\ContentLoaderJs\ContentLoaderJsComponentDto;
use App\Twig\Components\Controls\PaginatorContentLoaderJs\PaginatorContentLoaderJsComponentDto;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Order\OrderCreate\OrderCreateComponent;
use App\Twig\Components\Order\OrderCreate\OrderCreateComponentDto;
use App\Twig\Components\Order\OrderHome\Home\OrderHomeSectionComponentDto;
use App\Twig\Components\Order\OrderHome\ListItem\OrderListItemComponent;
use App\Twig\Components\Order\OrderHome\ListItem\OrderListItemComponentDto;
use App\Twig\Components\Order\OrderInfo\OrderInfoComponent;
use App\Twig\Components\Order\OrderInfo\OrderInfoComponentDto;
use App\Twig\Components\Order\OrderModify\OrderModifyComponent;
use App\Twig\Components\Order\OrderModify\OrderModifyComponentDto;
use App\Twig\Components\Order\OrderProductsListAjax\OrderProductsListAjaxComponent;
use App\Twig\Components\Order\OrderProductsListAjax\OrderProductsListAjaxComponentDto;
use App\Twig\Components\Order\OrderRemove\OrderRemoveComponent;
use App\Twig\Components\Order\OrderRemove\OrderRemoveComponentDto;
use App\Twig\Components\PaginatorJs\PaginatorJsComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class OrderHomeComponentBuilder implements DtoBuilderInterface
{
    private const ORDER_CREATE_MODAL_ID = 'order_create_modal';
    private const ORDER_REMOVE_MULTI_MODAL_ID = 'order_remove_multi_modal';
    private const ORDER_DELETE_MODAL_ID = 'order_delete_modal';
    private const ORDER_MODIFY_MODAL_ID = 'order_modify_modal';
    private const ORDER_INFO_MODAL_ID = 'order_info_modal';
    private const ORDER_PRODUCT_LIST_MODAL_ID = 'order_product_list_select_modal';

    private const ORDER_HOME_COMPONENT_NAME = 'OrderHomeSectionComponent';
    private const ORDER_HOME_LIST_COMPONENT_NAME = 'OrderHomeListComponent';
    private const ORDER_HOME_LIST_ITEM_COMPONENT_NAME = 'OrderHomeListItemComponent';

    private const ORDER_PRODUCT_LIST_PAGE_NUM_ITEMS = Config::MODAL_LIST_ITEMS_MAX_NUMBER;
    private const ORDER_PRODUCT_LIST_RESPONSE_INDEX_NAME = 'products';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $productsListAjaxModalDto;
    private readonly ModalComponentDto $orderInfoModalDto;

    /**
     * @var OrderDataResponse[]
     */
    private readonly array $listOrdersData;
    private readonly string $listOrdersId;
    private readonly string $groupId;
    private readonly bool $interactive;
    private readonly bool $headerButtonsHide;
    private readonly bool $hideInteraction;
    private readonly string $shareButtonUrl;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'listOrders',
            'homeSection',
            'orderCreateModal',
            'orderModifyFormModal',
            'orderRemoveMultiModal',
            'orderRemoveFormModal',
            'productsListModal',
            'errors',
            'pagination',
            'listItems',
            'shareButton',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = $this->createHomeSectionComponentDto();
    }

    public function title(?string $title, ?string $titlePath): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->homeSectionComponentDto->title($title, $titlePath);

        return $this;
    }

    public function listOrders(string $listOrdersId, string $groupId): self
    {
        $this->builder->setMethodStatus('listOrders', true);

        $this->listOrdersId = $listOrdersId;
        $this->groupId = $groupId;

        return $this;
    }

    public function homeSection(bool $interactive, bool $headerButtonsHide, bool $hideInteraction): self
    {
        $this->builder->setMethodStatus('homeSection', true);

        $this->interactive = $interactive;
        $this->headerButtonsHide = $headerButtonsHide;
        $this->hideInteraction = $hideInteraction;

        return $this;
    }

    public function orderCreateFormModal(string $orderCreateFormCsrfToken, ?float $orderPrice, string $orderCreateFormActionUrl, string $groupId, string $listOrdersId): self
    {
        $this->builder->setMethodStatus('orderCreateModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createOrderCreateComponentDto($orderCreateFormCsrfToken, $orderPrice, $orderCreateFormActionUrl, $groupId, $listOrdersId)
        );

        return $this;
    }

    public function orderModifyFormModal(string $orderModifyFormCsrfToken, string $orderModifyFormActionUrl, string $groupId, string $listOrdersId): self
    {
        $this->builder->setMethodStatus('orderModifyFormModal', true);

        $this->homeSectionComponentDto->modifyFormModal(
            $this->createOrderModifyModalDto($orderModifyFormCsrfToken, $orderModifyFormActionUrl, $groupId, $listOrdersId)
        );

        return $this;
    }

    public function orderRemoveMultiFormModal(string $orderRemoveMultiFormCsrfToken, string $orderRemoveMultiFormActionUrl): self
    {
        $this->builder->setMethodStatus('orderRemoveMultiModal', true);

        $this->homeSectionComponentDto->removeMultiFormModal(
            $this->createOrderRemoveMultiComponentDto($orderRemoveMultiFormCsrfToken, $orderRemoveMultiFormActionUrl)
        );

        return $this;
    }

    public function orderRemoveFormModal(string $orderRemoveFormCsrfToken, string $orderRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('orderRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createOrderRemoveModalDto($orderRemoveFormCsrfToken, $orderRemoveFormActionUrl)
        );

        return $this;
    }

    public function productsListModal(string $groupId, string $urlPathToProductImages, string $urlImageProductNoImage): self
    {
        $this->builder->setMethodStatus('productsListModal', true);

        $this->productsListAjaxModalDto = $this->createProductListItemsModalDto($groupId, $urlPathToProductImages, $urlImageProductNoImage);

        return $this;
    }

    /**
     * @param string[] $orderSectionValidationOk
     * @param string[] $orderValidationErrorsMessage
     */
    public function errors(array $orderSectionValidationOk, array $orderValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($orderSectionValidationOk, $orderValidationErrorsMessage);

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    /**
     * @param OrderDataResponse[] $listOrdersData
     */
    public function listItems(array $listOrdersData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listOrdersData = $listOrdersData;

        return $this;
    }

    public function shareButton(string $url): self
    {
        $this->builder->setMethodStatus('shareButton', true);

        $this->shareButtonUrl = $url;

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

    /**
     * @param SECTION_FILTERS[] $sectionFilters
     */
    public function searchBar(
        string $groupId,
        array $sectionFilters,
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
            $sectionFilters,
            $sectionFilterValue,
            $nameFilterValue,
            $searchBarCsrfToken,
            $searchBarFormActionUrl,
            $searchAutoCompleteUrl,
        ));

        return $this;
    }

    public function build(): OrderHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::ORDER_HOME_COMPONENT_NAME,
            self::ORDER_HOME_LIST_COMPONENT_NAME,
            self::ORDER_HOME_LIST_ITEM_COMPONENT_NAME,
        );
        $this->homeSectionComponentDto->createRemoveMultiForm(
            $this->createRemoveMultiFormDto()
        );
        $this->homeSectionComponentDto->listItems(
            OrderListItemComponent::getComponentName(),
            $this->createOrderListItemsComponentsDto(),
            Config::ORDER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
        );
        $this->homeSectionComponentDto->display(
            $this->interactive,
            $this->headerButtonsHide,
        );

        $this->orderInfoModalDto = $this->createOrderInfoModalDto();

        return $this->createOrderHomeSectionComponentDto($this->listOrdersId, $this->groupId, $this->productsListAjaxModalDto, $this->orderInfoModalDto);
    }

    private function createOrderCreateComponentDto(string $orderCreateFormCsrfToken, ?float $orderPrice, string $orderCreateFormActionUrl, string $groupId, string $listOrdersId): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new OrderCreateComponentDto(
            [],
            '',
            $orderPrice,
            $orderCreateFormCsrfToken,
            false,
            mb_strtolower($orderCreateFormActionUrl),
            $groupId,
            $listOrdersId
        );

        return new ModalComponentDto(
            self::ORDER_CREATE_MODAL_ID,
            '',
            false,
            OrderCreateComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createOrderRemoveMultiComponentDto(string $orderRemoveMultiFormCsrfToken, string $orderRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new OrderRemoveComponentDto(
            OrderRemoveComponent::getComponentName(),
            [],
            $orderRemoveMultiFormCsrfToken,
            mb_strtolower($orderRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::ORDER_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            OrderRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createOrderRemoveModalDto(string $orderRemoveFormCsrfToken, string $orderRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new OrderRemoveComponentDto(
            OrderRemoveComponent::getComponentName(),
            [],
            $orderRemoveFormCsrfToken,
            mb_strtolower($orderRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::ORDER_DELETE_MODAL_ID,
            '',
            false,
            OrderRemoveComponent::getComponentName(),
            $homeModalDelete,
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
            self::ORDER_REMOVE_MULTI_MODAL_ID
        );
    }

    private function createOrderModifyModalDto(string $orderModifyFormCsrfToken, string $orderModifyFormActionUrlPlaceholder, string $groupId, string $listOrdersId): ModalComponentDto
    {
        $homeModalModify = new OrderModifyComponentDto(
            [],
            $groupId,
            $listOrdersId,
            '{name_placeholder}',
            '{description_placeholder}',
            $orderModifyFormCsrfToken,
            false,
            mb_strtolower($orderModifyFormActionUrlPlaceholder),
        );

        return new ModalComponentDto(
            self::ORDER_MODIFY_MODAL_ID,
            '',
            false,
            OrderModifyComponent::getComponentName(),
            $homeModalModify,
            [],
        );
    }

    private function createOrderListItemsComponentsDto(): array
    {
        return array_map(
            fn (OrderDataResponse $listItemData) => new OrderListItemComponentDto(
                OrderListItemComponent::getComponentName(),
                $listItemData->id,
                $listItemData->groupId,
                $listItemData->product->name,
                self::ORDER_MODIFY_MODAL_ID,
                self::ORDER_DELETE_MODAL_ID,
                self::ORDER_INFO_MODAL_ID,
                self::ORDER_HOME_LIST_ITEM_COMPONENT_NAME,
                $listItemData->description,
                $listItemData->amount,
                $listItemData->bought,
                $listItemData->product->image ?? Config::ORDER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                null === $listItemData->product->image ? true : false,
                $listItemData->createdOn,
                $listItemData->product,
                $listItemData->shop,
                $listItemData->productShop,
                $this->hideInteraction
            ),
            $this->listOrdersData
        );
    }

    private function createProductListItemsModalDto(string $groupId, string $urlPathToProductImages, string $urlImageProductNoImage): ModalComponentDto
    {
        $pageCurrent = 1;
        $contentLoaderJsDto = new ContentLoaderJsComponentDto(
            'getProductsData',
            [
                'group_id' => $groupId,
                'page' => $pageCurrent,
                'page_items' => self::ORDER_PRODUCT_LIST_PAGE_NUM_ITEMS,
            ],
            self::ORDER_PRODUCT_LIST_RESPONSE_INDEX_NAME,
        );
        $paginatorJsDto = new PaginatorJsComponentDto($pageCurrent, 1);
        $paginatorContentLoaderJsDto = new PaginatorContentLoaderJsComponentDto($contentLoaderJsDto, $paginatorJsDto);

        $orderProductListAjaxComponentDto = new OrderProductsListAjaxComponentDto(
            OrderProductsListAjaxComponent::getComponentName(),
            $groupId,
            SECTION_FILTERS::PRODUCT,
            $paginatorContentLoaderJsDto,
            $urlPathToProductImages,
            $urlImageProductNoImage,
            Config::LIST_EMPTY_IMAGE
        );

        return new ModalComponentDto(
            self::ORDER_PRODUCT_LIST_MODAL_ID,
            '',
            false,
            OrderProductsListAjaxComponent::getComponentName(),
            $orderProductListAjaxComponentDto,
            []
        );
    }

    private function createOrderInfoModalDto(): ModalComponentDto
    {
        $orderInfoComponentDto = new OrderInfoComponentDto(
            OrderInfoComponent::getComponentName(),
            Config::ORDER_BOUGHT_ICON,
            Config::ORDER_BOUGHT_NOT_ICON,
        );

        return new ModalComponentDto(
            self::ORDER_INFO_MODAL_ID,
            '',
            false,
            OrderInfoComponent::getComponentName(),
            $orderInfoComponentDto,
            []
        );
    }

    private function createHomeSectionComponentDto(): HomeSectionComponentDto
    {
        return new HomeSectionComponentDto();
    }

    private function createOrderHomeSectionComponentDto(string $listOrdersId, string $groupId, ModalComponentDto $productListItemsModalDto, ModalComponentDto $orderInfoModalDto): OrderHomeSectionComponentDto
    {
        return (new OrderHomeSectionComponentDto())
            ->listOrders(
                $listOrdersId,
                $groupId
            )
            ->homeSection(
                $this->homeSectionComponentDto,
            )
            ->listItemsModal(
                $productListItemsModalDto
            )
            ->orderInfoModal(
                $orderInfoModalDto
            )
            ->share(
                $this->shareButtonUrl
            )
            ->build();
    }
}
