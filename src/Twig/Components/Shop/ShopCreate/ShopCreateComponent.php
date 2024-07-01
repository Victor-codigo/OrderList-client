<?php

namespace App\Twig\Components\Shop\ShopCreate;

use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_ERRORS;
use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponent;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ItemPriceAdd\ItemPriceAddComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\Config\Config;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopCreateComponent',
    template: 'Components/Shop/ShopCreate/ShopCreateComponent.html.twig'
)]
class ShopCreateComponent extends TwigComponent
{
    public ShopCreateComponentLangDto $lang;
    public ShopCreateComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $addressFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $imageFieldName;
    public readonly string $submitFieldName;
    public readonly TitleComponentDto $titleDto;
    public readonly DropZoneComponentDto $imageDto;
    public readonly ItemPriceAddComponentDto $itemPriceAddDto;

    public static function getComponentName(): string
    {
        return 'ShopCreateComponent';
    }

    public function mount(ShopCreateComponentDto $data): void
    {
        $this->formName = SHOP_CREATE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::NAME);
        $this->addressFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::ADDRESS);
        $this->descriptionFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::DESCRIPTION);
        $this->imageFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::IMAGE);
        $this->submitFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::SUBMIT);

        $this->data = $data;
        $this->loadTranslation();

        $this->titleDto = $this->createTitleComponentDto();
        $this->imageDto = $this->createImageDropZone();
        $this->itemPriceAddDto = $this->createItemPriceAddComponentDto();
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP);
    }

    private function createImageDropZone(): DropZoneComponentDto
    {
        return new DropZoneComponentDto(
            DropZoneComponent::getComponentName(),
            SHOP_CREATE_FORM_FIELDS::FORM,
            $this->lang->imageLabel,
            SHOP_CREATE_FORM_FIELDS::IMAGE,
            $this->lang->imagePlaceholder
        );
    }

    private function createItemPriceAddComponentDto(): ItemPriceAddComponentDto
    {
        return (new ItemPriceAddComponentDto())
            ->itemId(
                sprintf('%s[%s][]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::PRODUCT_ID),
                null
            )
            ->itemName(
                sprintf('%s[%s][]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::PRODUCT_NAME),
                '',
                $this->translate('product_name.label'),
                $this->translate('product_name.placeholder'),
                $this->translate('product_name.msg_invalid'),
            )
            ->price(
                sprintf('%s[%s][]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::PRODUCT_PRICE),
                null,
                $this->translate('shop_price.label'),
                $this->translate('shop_price.placeholder'),
                $this->translate('shop_price.msg_invalid'),
                Config::CURRENCY,
                sprintf('%s[%s][]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::PRODUCT_UNIT_MEASURE)
            )
            ->itemPriceAddButton(
                $this->translate('product_add_button.label'),
                $this->translate('product_add_button.title'),
                $this->translate('product_add_button.alt'),
            )
            ->itemRemoveButton(
                $this->translate('product_remove_button.title'),
                $this->translate('product_remove_button.alt'),
            )
            ->build();
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ShopCreateComponentLangDto())
            ->title(
                $this->translate('title.main')
            )
            ->name(
                $this->translate('name.label'),
                $this->translate('name.placeholder'),
                $this->translate('name.msg_invalid')
            )
            ->address(
                $this->translate('address.label'),
                $this->translate('address.placeholder'),
                $this->translate('address.msg_invalid')
            )
            ->productsTitle(
                $this->translate('title.products'),
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
            ->buttons(
                $this->translate('shop_create_button.label'),
                $this->translate('close_button.label'),
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
                SHOP_CREATE_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                SHOP_CREATE_FORM_ERRORS::ADDRESS->value => $this->translate('validation.error.address'),
                SHOP_CREATE_FORM_ERRORS::SHOP_NAME_REPEATED->value => $this->translate('validation.error.shop_name_repeated'),
                SHOP_CREATE_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
                SHOP_CREATE_FORM_ERRORS::DESCRIPTION->value,
                SHOP_CREATE_FORM_ERRORS::GROUP_ERROR->value,
                SHOP_CREATE_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
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
