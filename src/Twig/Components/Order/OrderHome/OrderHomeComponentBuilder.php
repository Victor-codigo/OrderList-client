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
use App\Twig\Components\Order\OrderHome\Home\OrderHomeSectionComponentDto;
use App\Twig\Components\Order\OrderHome\ListItem\OrderListItemComponent;
use App\Twig\Components\Order\OrderHome\ListItem\OrderListItemComponentDto;
use App\Twig\Components\PaginatorJs\PaginatorJsComponentDto;
use App\Twig\Components\Shop\ShopCreateAjax\ShopCreateAjaxComponent;
use App\Twig\Components\Shop\ShopCreateAjax\ShopCreateAjaxComponentDto;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use App\Twig\Components\Shop\ShopInfo\ShopInfoComponent;
use App\Twig\Components\Shop\ShopInfo\ShopInfoComponentDto;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponent;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponentDto;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponent;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponentDto;
use App\Twig\Components\Shop\ShopsListAjax\ShopsListAjaxComponent;
use App\Twig\Components\Shop\ShopsListAjax\ShopsListAjaxComponentDto;
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
    private const SHOP_LIST_MODAL_ID = 'shop_list_select_modal';
    private const SHOP_CREATE_MODAL_ID = 'shop_create_modal';

    private const ORDER_HOME_COMPONENT_NAME = 'OrderHomeComponent';
    private const ORDER_HOME_LIST_COMPONENT_NAME = 'OrderHomeListComponent';
    private const ORDER_HOME_LIST_ITEM_COMPONENT_NAME = 'OrderHomeListItemComponent';

    private const SHOP_LIST_PAGE_NUM_ITEMS = Config::MODAL_LIST_ITEMS_MAX_NUMBER;
    private const SHOP_LIST_RESPONSE_INDEX_NAME = 'shops';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $shopsListAjaxModalDto;
    private readonly ModalComponentDto $shopCreateModalDto;
    private readonly ModalComponentDto $orderInfoModalDto;

    /**
     * @var OrderDataResponse[]
     */
    private readonly array $listOrdersData;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'orderCreateModal',
            'orderModifyFormModal',
            'orderRemoveMultiModal',
            'orderRemoveFormModal',
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

    public function orderCreateFormModal(string $orderCreateFormCsrfToken, ?float $orderPrice, string $orderCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('orderCreateModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createOrderCreateComponentDto($orderCreateFormCsrfToken, $orderPrice, $orderCreateFormActionUrl)
        );

        return $this;
    }

    public function orderModifyFormModal(string $orderModifyFormCsrfToken, string $orderModifyFormActionUrl): self
    {
        $this->builder->setMethodStatus('orderModifyFormModal', true);

        $this->homeSectionComponentDto->modifyFormModal(
            $this->createOrderModifyModalDto($orderModifyFormCsrfToken, $orderModifyFormActionUrl)
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

    public function listItems(array $listOrdersData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listOrdersData = $listOrdersData;

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
            [SECTION_FILTERS::ORDER, SECTION_FILTERS::SHOP],
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

        $this->orderInfoModalDto = $this->createOrderInfoModalDto();

        return $this->createOrderHomeSectionComponentDto(/* $this->shopsListAjaxModalDto, */ $this->shopCreateModalDto/* , $this->orderInfoModalDto */);
    }

    private function createOrderCreateComponentDto(string $orderCreateFormCsrfToken, ?float $orderPrice, string $orderCreateFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new ShopCreateComponentDto(
            [],
            '',
            $orderPrice,
            $orderCreateFormCsrfToken,
            false,
            mb_strtolower($orderCreateFormActionUrl),
        );

        return new ModalComponentDto(
            self::ORDER_CREATE_MODAL_ID,
            '',
            false,
            ShopCreateComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createOrderRemoveMultiComponentDto(string $orderRemoveMultiFormCsrfToken, string $orderRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new ShopRemoveComponentDto(
            ShopRemoveComponent::getComponentName(),
            [],
            $orderRemoveMultiFormCsrfToken,
            mb_strtolower($orderRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::ORDER_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createOrderRemoveModalDto(string $orderRemoveFormCsrfToken, string $orderRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new ShopRemoveComponentDto(
            ShopRemoveComponent::getComponentName(),
            [],
            $orderRemoveFormCsrfToken,
            mb_strtolower($orderRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::ORDER_DELETE_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
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

    private function createOrderModifyModalDto(string $orderModifyFormCsrfToken, string $orderModifyFormActionUrlPlaceholder): ModalComponentDto
    {
        $homeModalModify = new ShopModifyComponentDto(
            [],
            '{name_placeholder}',
            '{description_placeholder}',
            '{image_placeholder}',
            Config::ORDER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
            $orderModifyFormCsrfToken,
            false,
            mb_strtolower($orderModifyFormActionUrlPlaceholder),
            self::SHOP_LIST_MODAL_ID,
            self::ORDER_MODIFY_MODAL_ID
        );

        return new ModalComponentDto(
            self::ORDER_MODIFY_MODAL_ID,
            '',
            false,
            ShopModifyComponent::getComponentName(),
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
                Config::ORDER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                $listItemData->createdOn,
                (array) $listItemData->product,
                null
            ),
            $this->listOrdersData
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

    private function createOrderInfoModalDto(): ModalComponentDto
    {
        $orderInfoComponentDto = new ShopInfoComponentDto(
            ShopInfoComponent::getComponentName()
        );

        return new ModalComponentDto(
            self::ORDER_INFO_MODAL_ID,
            '',
            false,
            ShopInfoComponent::getComponentName(),
            $orderInfoComponentDto,
            []
        );
    }

    private function createHomeSectionComponentDto(): HomeSectionComponentDto
    {
        return new HomeSectionComponentDto();
    }

    private function createOrderHomeSectionComponentDto(/* ModalComponentDto $orderListItemsModalDto,  ModalComponentDto $orderCreateModalDto , ModalComponentDto $orderInfoModalDto */): OrderHomeSectionComponentDto
    {
        return (new OrderHomeSectionComponentDto())
            ->homeSection(
                $this->homeSectionComponentDto
            )
            // ->listItemsModal(
            //     $orderListItemsModalDto
            // )
            // ->shopCreateModal(
            //     $orderCreateModalDto
            // )
            // ->orderInfoModal(
            //     $orderInfoModalDto
            // )
            ->build();
    }
}
