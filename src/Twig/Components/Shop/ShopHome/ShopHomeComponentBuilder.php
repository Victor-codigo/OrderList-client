<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome;

use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\SearchBar\SEARCH_TYPE;
use App\Twig\Components\SearchBar\SearchBarComponentDto;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use App\Twig\Components\Shop\ShopHome\ListItem\ShopListItemComponent;
use App\Twig\Components\Shop\ShopHome\ListItem\ShopListItemComponentDto;
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

    private const SHOP_HOME_COMPONENT_NAME = 'ShopHomeComponent';
    private const SHOP_HOME_LIST_COMPONENT_NAME = 'ShopHomeListComponent';
    private const SHOP_HOME_LIST_ITEM_COMPONENT_NAME = 'ShopHomeListItemComponent';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;

    private readonly array $listShopsData;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'shopCreateModal',
            'shopModifyFormModal',
            'shopRemoveMultiModal',
            'shopRemoveFormModal',
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
    public function listItems(array $listShopsData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listShopsData = $listShopsData;

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
        string|null $searchBarFilterType,
        string|null $searchBarFilterValue,
        SEARCH_TYPE $searchType,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->homeSectionComponentDto->searchBar(new SearchBarComponentDto(
            $groupId,
            $searchBarFilterType,
            $searchBarFilterValue,
            $searchType,
            $searchBarCsrfToken,
            $searchAutoCompleteUrl,
            $searchBarFormActionUrl
        ));

        return $this;
    }

    public function build(): HomeSectionComponentDto
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

        return $this->homeSectionComponentDto;
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

    private function createShopListItemComponentDto(): array
    {
        return array_map(
            fn (ShopDataResponse $homeData) => new ShopListItemComponentDto(
                ShopListItemComponent::getComponentName(),
                $homeData->id,
                $homeData->name,
                self::SHOP_MODIFY_MODAL_ID,
                self::SHOP_DELETE_MODAL_ID,
                self::SHOP_HOME_LIST_ITEM_COMPONENT_NAME,
                $homeData->description,
                $homeData->image ?? Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                $homeData->createdOn,
            ),
            $this->listShopsData
        );
    }
}
