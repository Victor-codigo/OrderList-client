<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductHome;

use App\Controller\Request\Response\ProductDataResponse;
use App\Form\Product\ProductRemoveMulti\PRODUCT_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Product\ProductCreate\ProductCreateComponent;
use App\Twig\Components\Product\ProductCreate\ProductCreateComponentDto;
use App\Twig\Components\Product\ProductHome\ListItem\ProductListItemComponent;
use App\Twig\Components\Product\ProductHome\ListItem\ProductListItemComponentDto;
use App\Twig\Components\Product\ProductModify\ProductModifyComponent;
use App\Twig\Components\Product\ProductModify\ProductModifyComponentDto;
use App\Twig\Components\Product\ProductRemove\ProductRemoveComponent;
use App\Twig\Components\Product\ProductRemove\ProductRemoveComponentDto;
use App\Twig\Components\SearchBar\SEARCH_TYPE;
use App\Twig\Components\SearchBar\SearchBarComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\DtoBuilder\DtoBuilder;
use Common\Domain\DtoBuilder\DtoBuilderInterface;

class ProductHomeComponentBuilder implements DtoBuilderInterface
{
    private const PRODUCT_CREATE_MODAL_ID = 'product_create_modal';
    private const PRODUCT_REMOVE_MULTI_MODAL_ID = 'product_remove_multi_modal';
    public const PRODUCT_DELETE_MODAL_ID = 'product_delete_modal';
    public const PRODUCT_MODIFY_MODAL_ID = 'product_modify_modal';

    private const PRODUCT_HOME_COMPONENT_NAME = 'ProductHomeComponent';
    private const PRODUCT_HOME_LIST_COMPONENT_NAME = 'ProductHomeListComponent';
    private const PRODUCT_HOME_LIST_ITEM_COMPONENT_NAME = 'ProductHomeListItemComponent';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;

    private readonly array $listProductsData;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'productCreateModal',
            'productModifyFormModal',
            'productRemoveMultiModal',
            'productRemoveFormModal',
            'errors',
            'pagination',
            'listItems',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = new HomeSectionComponentDto();
    }

    public function productCreateFormModal(string $productCreateFormCsrfToken, string $productCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('productCreateModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createProductCreateComponentDto($productCreateFormCsrfToken, $productCreateFormActionUrl)
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

    public function listItems(array $listProductsData): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->listProductsData = $listProductsData;

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

        return $this->homeSectionComponentDto;
    }

    private function createProductCreateComponentDto(string $productCreateFormCsrfToken, string $productCreateFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new ProductCreateComponentDto(
            [],
            '',
            '',
            $productCreateFormCsrfToken,
            false,
            mb_strtolower($productCreateFormActionUrl)
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
            mb_strtolower($productModifyFormActionUrlPlaceholder)
        );

        return new ModalComponentDto(
            self::PRODUCT_MODIFY_MODAL_ID,
            '',
            false,
            ProductModifyComponent::getComponentName(),
            $homeModalModify,
            []
        );
    }

    private function createProductListItemsComponentsDto(): array
    {
        return array_map(
            fn (ProductDataResponse $homeData) => new ProductListItemComponentDto(
                ProductListItemComponent::getComponentName(),
                $homeData->id,
                $homeData->name,
                self::PRODUCT_MODIFY_MODAL_ID,
                self::PRODUCT_DELETE_MODAL_ID,
                self::PRODUCT_HOME_LIST_ITEM_COMPONENT_NAME,
                $homeData->description,
                $homeData->image ?? Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
                $homeData->createdOn,
            ),
            $this->listProductsData
        );
    }
}
