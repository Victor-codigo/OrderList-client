<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome;

use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\Controls\ContentLoaderJs\ContentLoaderJsComponentDto;
use App\Twig\Components\Controls\PaginatorContentLoaderJs\PaginatorContentLoaderJsComponentDto;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Twig\Components\ListOrders\ListOrdersCreateFrom\ListOrdersCreateFromComponent;
use App\Twig\Components\ListOrders\ListOrdersCreateFrom\ListOrdersCreateFromComponentDto;
use App\Twig\Components\ListOrders\ListOrdersCreate\ListOrdersCreateComponent;
use App\Twig\Components\ListOrders\ListOrdersCreate\ListOrdersCreateComponentDto;
use App\Twig\Components\ListOrders\ListOrdersHome\Home\ListOrdersHomeSectionComponentDto;
use App\Twig\Components\ListOrders\ListOrdersHome\ListItem\ListOrdersListItemComponent;
use App\Twig\Components\ListOrders\ListOrdersHome\ListItem\ListOrdersListItemComponentDto;
use App\Twig\Components\ListOrders\ListOrdersInfo\ListOrdersInfoComponent;
use App\Twig\Components\ListOrders\ListOrdersInfo\ListOrdersInfoComponentDto;
use App\Twig\Components\ListOrders\ListOrdersListAjax\ListOrdersListAjaxComponent;
use App\Twig\Components\ListOrders\ListOrdersListAjax\ListOrdersListAjaxComponentDto;
use App\Twig\Components\ListOrders\ListOrdersModify\ListOrdersModifyComponent;
use App\Twig\Components\ListOrders\ListOrdersModify\ListOrdersModifyComponentDto;
use App\Twig\Components\ListOrders\ListOrdersRemove\ListOrdersRemoveComponent;
use App\Twig\Components\ListOrders\ListOrdersRemove\ListOrdersRemoveComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\PaginatorJs\PaginatorJsComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ListOrdersHomeComponentBuilder implements DtoBuilderInterface
{
    private const LIST_ORDERS_CREATE_MODAL_ID = 'list_orders_create_modal';
    private const LIST_ORDERS_CREATE_FROM_MODAL_ID = 'list_orders_create_from_modal';
    private const LIST_ORDERS_REMOVE_MULTI_MODAL_ID = 'list_orders_remove_multi_modal';
    public const LIST_ORDERS_DELETE_MODAL_ID = 'list_orders_delete_modal';
    public const LIST_ORDERS_MODIFY_MODAL_ID = 'list_orders_modify_modal';
    public const LIST_ORDERS_INFO_MODAL_ID = 'list_orders_info_modal';
    private const LIST_ORDERS_LIST_MODAL_ID = 'list_orders_list_select_modal';

    private const LIST_ORDERS_HOME_COMPONENT_NAME = 'ListOrdersHomeComponent';
    private const LIST_ORDERS_HOME_LIST_COMPONENT_NAME = 'ListOrdersHomeListComponent';
    private const LIST_ORDERS_HOME_LIST_ITEM_COMPONENT_NAME = 'ListOrdersHomeListItemComponent';

    private const LIST_ORDERS_LIST_PAGE_NUM_ITEMS = Config::MODAL_LIST_ITEMS_MAX_NUMBER;
    private const LIST_ORDERS_LIST_RESPONSE_INDEX_NAME = 'list_orders';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $listOrdersCreateFromModalDto;
    private readonly ModalComponentDto $listOrdersListAjaxModalDto;
    private readonly ModalComponentDto $listOrdersInfoModalDto;

    /**
     * @var ListOrdersDataResponse[]
     */
    private readonly array $listLitOrdersData;
    private readonly string $urlListOrdersPlaceholder;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'listOrdersCreateFormModal',
            'listOrdersCreateFromFormModal',
            'listOrdersModifyFormModal',
            'listOrdersRemoveMultiModal',
            'listOrdersRemoveFormModal',
            'listOrdersListAjaxModal',
            'errors',
            'pagination',
            'listItems',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = new HomeSectionComponentDto();
    }

    public function title(?string $title): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->homeSectionComponentDto->title($title);

        return $this;
    }

    public function listOrdersCreateFormModal(string $listOrdersCreateFormCsrfToken, string $listOrdersCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('listOrdersCreateFormModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createListOrdersCreateComponentDto($listOrdersCreateFormCsrfToken, $listOrdersCreateFormActionUrl)
        );

        return $this;
    }

    public function listOrdersCreateFromFormModal(string $listOrdersCreateFormCsrfToken, string $listOrdersCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('listOrdersCreateFromFormModal', true);

        $this->listOrdersCreateFromModalDto = $this->createListOrdersCreateFromComponentDto($listOrdersCreateFormCsrfToken, $listOrdersCreateFormActionUrl);

        return $this;
    }

    public function listOrdersListAjaxModal(string $groupId, string $urlPathToListOrdersImages, string $urlImageListOrdersNoImage): self
    {
        $this->builder->setMethodStatus('listOrdersListAjaxModal', true);

        $this->listOrdersListAjaxModalDto = $this->createListOrdersListItemsModalDto($groupId, $urlPathToListOrdersImages, $urlImageListOrdersNoImage);

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

    public function listItems(array $listListOrdersData, string $urlListOrdersPlaceholder): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listLitOrdersData = $listListOrdersData;
        $this->urlListOrdersPlaceholder = $urlListOrdersPlaceholder;

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
            [SECTION_FILTERS::LIST_ORDERS, SECTION_FILTERS::PRODUCT, SECTION_FILTERS::SHOP],
            $sectionFilterValue,
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
        $this->homeSectionComponentDto->display(
            false
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

    private function createListOrdersCreateFromComponentDto(string $listOrdersCreateFromFormCsrfToken, string $listOrdersCreateFromFormActionUrl): ModalComponentDto
    {
        $listOrdersCreateFromComponentDto = new ListOrdersCreateFromComponentDto(
            [],
            $listOrdersCreateFromFormCsrfToken,
            false,
            mb_strtolower($listOrdersCreateFromFormActionUrl)
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_CREATE_FROM_MODAL_ID,
            '',
            false,
            ListOrdersCreateFromComponent::getComponentName(),
            $listOrdersCreateFromComponentDto,
            []
        );
    }

    private function createListOrdersRemoveMultiComponentDto(string $listOrdersRemoveMultiFormCsrfToken, string $listOrdersRemoveFormActionUrl): ModalComponentDto
    {
        $homeSectionRemoveMultiComponentDto = new ListOrdersRemoveComponentDto(
            ListOrdersRemoveComponent::getComponentName(),
            [],
            $listOrdersRemoveMultiFormCsrfToken,
            mb_strtolower($listOrdersRemoveFormActionUrl),
            true,
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            ListOrdersRemoveComponent::getComponentName(),
            $homeSectionRemoveMultiComponentDto,
            []
        );
    }

    private function createListOrdersRemoveModalDto(string $listOrdersRemoveFormCsrfToken, string $listOrdersRemoveFormActionUrl): ModalComponentDto
    {
        $homeModalDelete = new ListOrdersRemoveComponentDto(
            ListOrdersRemoveComponent::getComponentName(),
            [],
            $listOrdersRemoveFormCsrfToken,
            mb_strtolower($listOrdersRemoveFormActionUrl),
            false
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_DELETE_MODAL_ID,
            '',
            false,
            ListOrdersRemoveComponent::getComponentName(),
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

    private function createListOrdersListItemsModalDto(string $groupId, string $urlPathToListOrdersImages, string $urlImageListOrdersNoImage): ModalComponentDto
    {
        $pageCurrent = 1;
        $contentLoaderJsDto = new ContentLoaderJsComponentDto(
            'getListOrdersData',
            [
                'group_id' => $groupId,
                'page' => $pageCurrent,
                'page_items' => self::LIST_ORDERS_LIST_PAGE_NUM_ITEMS,
            ],
            self::LIST_ORDERS_LIST_RESPONSE_INDEX_NAME,
        );
        $paginatorJsDto = new PaginatorJsComponentDto($pageCurrent, 1);
        $paginatorContentLoaderJsDto = new PaginatorContentLoaderJsComponentDto($contentLoaderJsDto, $paginatorJsDto);

        $shopListAjaxComponentDto = new ListOrdersListAjaxComponentDto(
            ListOrdersListAjaxComponent::getComponentName(),
            $paginatorContentLoaderJsDto,
            $urlPathToListOrdersImages,
            $urlImageListOrdersNoImage,
        );

        return new ModalComponentDto(
            self::LIST_ORDERS_LIST_MODAL_ID,
            '',
            false,
            ListOrdersListAjaxComponent::getComponentName(),
            $shopListAjaxComponentDto,
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
                $this->urlListOrdersPlaceholder,
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
            ->listOrdersCreateFromModal(
                $this->listOrdersCreateFromModalDto
            )
            ->listOrdersListAjaxModalDto(
                $this->listOrdersListAjaxModalDto
            )
            ->listOrdersInfoModal(
                $listOrdersInfoModalDto
            )
            ->build();
    }
}
