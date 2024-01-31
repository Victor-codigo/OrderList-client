<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductCreate;

use App\Form\Product\ProductCreate\PRODUCT_CREATE_FORM_ERRORS;
use App\Form\Product\ProductCreate\PRODUCT_CREATE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponent;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ItemPriceAdd\ItemPriceAddComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductCreateComponent',
    template: 'Components/Product/ProductCreate/ProductCreateComponent.html.twig'
)]
final class ProductCreateComponent extends TwigComponent
{
    public ProductCreateComponentLangDto $lang;
    public ProductCreateComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $imageFieldName;
    public readonly string $submitFieldName;
    public readonly TitleComponentDto $titleDto;
    public readonly DropZoneComponentDto $imageDto;
    public readonly ItemPriceAddComponentDto $itemPriceAddDto;

    public static function getComponentName(): string
    {
        return 'ProductCreateComponent';
    }

    public function mount(ProductCreateComponentDto $data): void
    {
        $this->formName = PRODUCT_CREATE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::NAME);
        $this->descriptionFieldName = sprintf('%s[%s]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::DESCRIPTION);
        $this->imageFieldName = sprintf('%s[%s]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::IMAGE);
        $this->submitFieldName = sprintf('%s[%s]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::SUBMIT);

        $this->data = $data;
        $this->loadTranslation();

        $this->titleDto = $this->createTitleComponentDto();
        $this->imageDto = $this->createImageDropZone();
        $this->itemPriceAddDto = $this->createItemPriceAddComponentDto();
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function createImageDropZone(): DropZoneComponentDto
    {
        return new DropZoneComponentDto(
            DropZoneComponent::getComponentName(),
            PRODUCT_CREATE_FORM_FIELDS::FORM,
            $this->lang->imageLabel,
            PRODUCT_CREATE_FORM_FIELDS::IMAGE,
            $this->lang->imagePlaceholder
        );
    }

    private function createItemPriceAddComponentDto(): ItemPriceAddComponentDto
    {
        return (new ItemPriceAddComponentDto())
            ->itemId(
                sprintf('%s[%s][]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::SHOP_ID),
                null
            )
            ->itemName(
                sprintf('%s[%s][]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::SHOP_NAME),
                '',
                $this->translate('shop_name.label'),
                $this->translate('shop_name.placeholder'),
                $this->translate('shop_name.msg_invalid'),
            )
            ->price(
                sprintf('%s[%s][]', PRODUCT_CREATE_FORM_FIELDS::FORM, PRODUCT_CREATE_FORM_FIELDS::SHOP_PRICE),
                null,
                $this->translate('product_price.label'),
                $this->translate('product_price.placeholder'),
                $this->translate('product_price.msg_invalid'),
                '€'
            )
            ->itemPriceAddButton(
                $this->translate('shop_add_button.label'),
                $this->translate('shop_add_button.title'),
                $this->translate('shop_add_button.alt'),
            )
            ->itemRemoveButton(
                $this->translate('shop_remove_button.title'),
                $this->translate('shop_remove_button.alt'),
            )
            ->itemSelectModal(
                $this->data->shopListSelectModalIdAttribute
            )
            ->build();
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ProductCreateComponentLangDto())
            ->title(
                $this->translate('title.main')
            )
            ->shopsTitle(
                $this->translate('title.shops')
            )
            ->name(
                $this->translate('name.label'),
                $this->translate('name.placeholder'),
                $this->translate('name.msg_invalid')
            )
            ->price(
                $this->translate('price.label'),
                $this->translate('price.placeholder'),
                $this->translate('price.msg_invalid')
            )
            ->description(
                $this->translate('description.label'),
                $this->translate('description.placeholder'),
                $this->translate('description.msg_invalid')
            )
            ->image(
                $this->translate('image.label'),
                $this->translate('image.placeholder'),
                $this->translate('image.msg_invalid')
            )
            ->submitButton(
                $this->translate('product_create_button.label')
            )
            ->errors(
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
            )
            ->build();
    }

    /**
     * @return string[]
     */
    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                PRODUCT_CREATE_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                PRODUCT_CREATE_FORM_ERRORS::PRODUCT_NAME_REPEATED->value => $this->translate('validation.error.product_name_repeated'),
                PRODUCT_CREATE_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
                PRODUCT_CREATE_FORM_ERRORS::DESCRIPTION->value,
                PRODUCT_CREATE_FORM_ERRORS::GROUP_ERROR->value,
                PRODUCT_CREATE_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return $errorsLang;
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        $errorsLang = $this->loadErrorsTranslation($this->data->errors);

        return new AlertValidationComponentDto(
            array_unique([$this->loadValidationOkTranslation()]),
            array_unique($errorsLang)
        );
    }
}
