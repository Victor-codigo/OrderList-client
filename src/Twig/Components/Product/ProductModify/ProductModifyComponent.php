<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductModify;

use App\Form\Product\ProductModify\PRODUCT_MODIFY_FORM_ERRORS;
use App\Form\Product\ProductModify\PRODUCT_MODIFY_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponent;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ImageAvatar\ImageAvatarComponentDto;
use App\Twig\Components\Controls\ItemPriceAdd\ItemPriceAddComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\Config\Config;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductModifyComponent',
    template: 'Components/Product/ProductModify/ProductModifyComponent.html.twig'
)]
final class ProductModifyComponent extends TwigComponent
{
    public ProductModifyComponentLangDto $lang;
    public ProductModifyComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $formActionUrl;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $imageFieldName;
    public readonly string $imageRemoveFieldName;
    public readonly string $submitFieldName;
    public readonly DropZoneComponentDto $imageDto;
    public readonly ImageAvatarComponentDto $imageAvatarDto;
    public readonly TitleComponentDto $titleDto;
    public readonly ItemPriceAddComponentDto $itemPriceAddDto;

    public static function getComponentName(): string
    {
        return 'ProductModifyComponent';
    }

    public function mount(ProductModifyComponentDto $data): void
    {
        $this->formName = PRODUCT_MODIFY_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::NAME);
        $this->descriptionFieldName = sprintf('%s[%s]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::DESCRIPTION);
        $this->imageFieldName = sprintf('%s[%s]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::IMAGE);
        $this->imageRemoveFieldName = sprintf('%s[%s]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::IMAGE_REMOVE);
        $this->submitFieldName = sprintf('%s[%s]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::SUBMIT);

        $this->data = $data;
        $this->loadTranslation();

        $this->titleDto = $this->createTitleComponentDto();
        $this->imageDto = $this->createImageDropZone();
        $this->imageAvatarDto = $this->createImageAvatar();
        $this->itemPriceAddDto = $this->createItemPriceAddComponentDto();
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP,null);
    }

    private function createImageDropZone(): DropZoneComponentDto
    {
        return new DropZoneComponentDto(
            DropZoneComponent::getComponentName(),
            PRODUCT_MODIFY_FORM_FIELDS::FORM,
            $this->lang->imageLabel,
            PRODUCT_MODIFY_FORM_FIELDS::IMAGE,
            $this->lang->imagePlaceholder
        );
    }

    private function createImageAvatar(): ImageAvatarComponentDto
    {
        return new ImageAvatarComponentDto(
            '',
            $this->data->imageNoImage,
            $this->translate('image_avatar.alt')
        );
    }

    private function createItemPriceAddComponentDto(): ItemPriceAddComponentDto
    {
        return (new ItemPriceAddComponentDto())
            ->itemId(
                sprintf('%s[%s][]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::SHOP_ID),
                null
            )
            ->itemName(
                sprintf('%s[%s][]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::SHOP_NAME),
                '',
                $this->translate('shop_name.label'),
                $this->translate('shop_name.placeholder'),
                $this->translate('shop_name.msg_invalid'),
            )
            ->price(
                sprintf('%s[%s][]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::SHOP_PRICE),
                null,
                $this->translate('product_price.label'),
                $this->translate('product_price.placeholder'),
                $this->translate('product_price.msg_invalid'),
                Config::CURRENCY,
                sprintf('%s[%s][]', PRODUCT_MODIFY_FORM_FIELDS::FORM, PRODUCT_MODIFY_FORM_FIELDS::SHOP_UNIT_MEASURE)
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
            ->build();
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        $errorsLang = $this->loadErrorsTranslation($this->data->errors);

        return new AlertValidationComponentDto(
            array_unique([$this->loadValidationOkTranslation()]),
            array_unique($errorsLang)
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ProductModifyComponentLangDto())
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
            ->buttons(
                $this->translate('product_modify_button.label'),
                $this->translate('close_button.label')
            )
            ->errors(
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
            )
            ->build();
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }

    /**
     * @param string[] $errors
     *
     * @return string[]
     */
    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                PRODUCT_MODIFY_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                PRODUCT_MODIFY_FORM_ERRORS::PRODUCT_NAME_REPEATED->value => $this->translate('validation.error.product_name_repeated'),
                PRODUCT_MODIFY_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
                PRODUCT_MODIFY_FORM_ERRORS::PERMISSIONS->value,
                PRODUCT_MODIFY_FORM_ERRORS::DESCRIPTION->value,
                PRODUCT_MODIFY_FORM_ERRORS::PRODUCTS_OR_SHOPS_PRICES_NOT_EQUALS->value,
                PRODUCT_MODIFY_FORM_ERRORS::PRODUCT_ID_AND_SHOP_ID->value,
                PRODUCT_MODIFY_FORM_ERRORS::PRODUCTS_OR_SHOPS_ID->value,
                PRODUCT_MODIFY_FORM_ERRORS::SHOP_ID->value,
                PRODUCT_MODIFY_FORM_ERRORS::PRICES->value,
                PRODUCT_MODIFY_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return $errorsLang;
    }
}
