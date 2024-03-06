<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome;

use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Twig\Components\ListOrders\ListOrdersCreate\ListOrdersCreateComponent;
use App\Twig\Components\ListOrders\ListOrdersCreate\ListOrdersCreateComponentDto;
use App\Twig\Components\ListOrders\ListOrdersHome\Home\ListOrdersHomeSectionComponentDto;
use App\Twig\Components\ListOrders\ListOrdersHome\ListItem\ListOrdersListItemComponent;
use App\Twig\Components\ListOrders\ListOrdersHome\ListItem\ListOrdersListItemComponentDto;
use App\Twig\Components\ListOrders\ListOrdersInfo\ListOrdersInfoComponent;
use App\Twig\Components\ListOrders\ListOrdersInfo\ListOrdersInfoComponentDto;
use App\Twig\Components\ListOrders\ListOrdersModify\ListOrdersModifyComponent;
use App\Twig\Components\ListOrders\ListOrdersModify\ListOrdersModifyComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponent;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ListOrdersHomeComponentBuilder implements DtoBuilderInterface
{
    private const LIST_ORDERS_CREATE_MODAL_ID = 'list_orders_create_modal';
    private const LIST_ORDERS_REMOVE_MULTI_MODAL_ID = 'list_orders_remove_multi_modal';
    public const LIST_ORDERS_DELETE_MODAL_ID = 'list_orders_delete_modal';
    public const LIST_ORDERS_MODIFY_MODAL_ID = 'list_orders_modify_modal';
    public const LIST_ORDERS_INFO_MODAL_ID = 'list_orders_info_modal';

    private const LIST_ORDERS_HOME_COMPONENT_NAME = 'ListOrdersHomeComponent';
    private const LIST_ORDERS_HOME_LIST_COMPONENT_NAME = 'ListOrdersHomeListComponent';
    private const LIST_ORDERS_HOME_LIST_ITEM_COMPONENT_NAME = 'ListOrdersHomeListItemComponent';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $listOrdersCreateModalDto;
    private readonly ModalComponentDto $listOrdersInfoModalDto;

    /**
     * @var ProductDataResponse[]
     */
    private readonly array $listListOrdersData;
    /**
     * @var ListOrdersDataResponse[]
     */
    private readonly array $listLitOrdersData;
    /**
     * @var ProductShopPriceDataResponse[]
     */
    private readonly array $listProductsShopPricesData;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'listOrdersCreateFormModal',
            'listOrdersModifyFormModal',
            'listOrdersRemoveMultiModal',
            'listOrdersRemoveFormModal',
            'errors',
            'pagination',
            'listItems',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = new HomeSectionComponentDto();
    }

    public function listOrdersCreateFormModal(string $listOrdersCreateFormCsrfToken, string $listOrdersCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('listOrdersCreateFormModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createListOrdersCreateComponentDto($listOrdersCreateFormCsrfToken, $listOrdersCreateFormActionUrl)
        );

        return $this;
    }

    public function listOrdersModifyFormModal(string $listOrdersModifyFormCsrfToken, string $listOrdersModifyFormActionUrl): self
    {
        $this->builder->setMethodStatus('listOrdersModifyFormModal', true);

        $this->homeSectionComponentDto->modifyFormModal(
            $this->createListOrdersModifyModalDto($listOrdersModifyFormCsrfToken, $listOrdersModifyFormActionUrl)
        );

        return $this;
    }

    public function listOrdersRemoveMultiFormModal(string $listOrdersRemoveMultiFormCsrfToken, string $listOrdersRemoveMultiFormActionUrl): self
    {
        $this->builder->setMethodStatus('listOrdersRemoveMultiModal', true);

        $this->homeSectionComponentDto->removeMultiFormModal(
            $this->createListOrdersRemoveMultiComponentDto($listOrdersRemoveMultiFormCsrfToken, $listOrdersRemoveMultiFormActionUrl)
        );

        return $this;
    }

    public function listOrdersRemoveFormModal(string $listOrdersRemoveFormCsrfToken, string $listOrdersRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('listOrdersRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createListOrdersRemoveModalDto($listOrdersRemoveFormCsrfToken, $listOrdersRemoveFormActionUrl)
        );

        return $this;
    }

    public function errors(array $listOrdersSectionValidationOk, array $listOrdersValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($listOrdersSectionValidationOk, $listOrdersValidationErrorsMessage);

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    public function listItems(array $listListOrdersData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listLitOrdersData = $listListOrdersData;

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
        ?string $nameFilterValue,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->homeSectionComponentDto->searchBar(new SearchBarComponentDto(
            $groupId,
            $searchValue,
            [SECTION_FILTERS::LIST_ORDERS],
            null,
            $nameFilterValue,
            $searchBarCsrfToken,
            $searchBarFormActionUrl,
            $searchAutoCompleteUrl,
        ));

        return $this;
    }

    public function build(): ListOrdersHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::LIST_ORDERS_HOME_COMPONENT_NAME,
            self::LIST_ORDERS_HOME_LIST_COMPONENT_NAME,
            self::LIST_ORDERS_HOME_LIST_ITEM_COMPONENT_NAME
        );
        $this->homeSectionComponentDto->createRemoveMultiForm(
            $this->createRemoveMultiFormDto()
        );
        $this->homeSectionComponentDto->listItems(
            ListOrdersListItemComponent::getComponentName(),
            $this->createListOrdersListItemComponentDto(),
            Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
        );
        $this->listOrdersInfoModalDto = $this->createListOrdersInfoModalDto();

        return $this->createListOrdersHomeSectionComponentDto($this->listOrdersInfoModalDto);
    }

    private function createListOrdersCreateComponentDto(string $listOrdersCreateFormCsrfToken, string $listOrdersCreateFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new ListOrdersCreateComponentDto(
            [],
            '',
            null,
            null,
            $listOrdersCreateFormCsrfToken,
            false,
            mb_strtolower($listOrdersCreateFormActionUrl)
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_CREATE_MODAL_ID,
            '',
            false,
            ListOrdersCreateComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createListOrdersRemoveMultiComponentDto(string $listOrdersRemoveMultiFormCsrfToken, string $listOrdersRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new ShopRemoveComponentDto(
            ShopRemoveComponent::getComponentName(),
            [],
            $listOrdersRemoveMultiFormCsrfToken,
            mb_strtolower($listOrdersRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createListOrdersRemoveModalDto(string $shopRemoveFormCsrfToken, string $shopRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new ShopRemoveComponentDto(
            ShopRemoveComponent::getComponentName(),
            [],
            $shopRemoveFormCsrfToken,
            mb_strtolower($shopRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_DELETE_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
            $homeModalDelete,
            []
        );
    }

    private function createListOrdersModifyModalDto(string $shopModifyFormCsrfToken, string $listOrdersModifyFormActionUrlPlaceholder): ModalComponentDto
    {
        $homeModalModify = new ListOrdersModifyComponentDto(
            [],
            '{name_placeholder}',
            '{description_placeholder}',
            null,
            $shopModifyFormCsrfToken,
            false,
            mb_strtolower($listOrdersModifyFormActionUrlPlaceholder)
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_MODIFY_MODAL_ID,
            '',
            false,
            ListOrdersModifyComponent::getComponentName(),
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
            self::LIST_ORDERS_REMOVE_MULTI_MODAL_ID
        );
    }

    private function createListOrdersInfoModalDto(): ModalComponentDto
    {
        $productInfoComponentDto = new ListOrdersInfoComponentDto(
            ListOrdersInfoComponent::getComponentName(),
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_INFO_MODAL_ID,
            '',
            false,
            ListOrdersInfoComponent::getComponentName(),
            $productInfoComponentDto,
            []
        );
    }

    private function createListOrdersListItemComponentDto(): array
    {
        return array_map(
            fn (ListOrdersDataResponse $listItemData) => new ListOrdersListItemComponentDto(
                ListOrdersListItemComponent::getComponentName(),
                $listItemData->id,
                $listItemData->name,
                self::LIST_ORDERS_MODIFY_MODAL_ID,
                self::LIST_ORDERS_DELETE_MODAL_ID,
                self::LIST_ORDERS_INFO_MODAL_ID,
                self::LIST_ORDERS_HOME_LIST_ITEM_COMPONENT_NAME,
                $listItemData->groupId,
                $listItemData->userId,
                $listItemData->description,
                $listItemData->dateToBuy,
                $listItemData->createdOn,
                Config::LIST_ORDERS_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
            ),
            $this->listLitOrdersData
        );
    }

    private function createListOrdersHomeSectionComponentDto(ModalComponentDto $listOrdersInfoModalDto): ListOrdersHomeSectionComponentDto
    {
        return (new ListOrdersHomeSectionComponentDto())
            ->homeSection(
                $this->homeSectionComponentDto
            )
            ->listOrdersInfoModal(
                $listOrdersInfoModalDto
            )
            ->build();
    }
}
