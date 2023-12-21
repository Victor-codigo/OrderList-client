<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductModify;

use App\Form\Product\ProductModify\PRODUCT_MODIFY_FORM_ERRORS;
use App\Form\Product\ProductModify\PRODUCT_MODIFY_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponent;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ImageAvatar\ImageAvatarComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
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
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
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
                $this->translate('title')
            )
            ->name(
                $this->translate('name.label'),
                $this->translate('name.placeholder'),
                $this->translate('name.msg_invalid')
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
                $this->translate('product_modify_button.label')
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

    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                PRODUCT_MODIFY_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                PRODUCT_MODIFY_FORM_ERRORS::DESCRIPTION->value => $this->translate('validation.error.description'),
                PRODUCT_MODIFY_FORM_ERRORS::PRODUCT_NAME_REPEATED->value => $this->translate('validation.error.product_name_repeated'),
                PRODUCT_MODIFY_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
                PRODUCT_MODIFY_FORM_ERRORS::GROUP_ID->value,
                PRODUCT_MODIFY_FORM_ERRORS::PRODUCT_ID->value,
                PRODUCT_MODIFY_FORM_ERRORS::SHOP_NOT_FOUND->value,
                PRODUCT_MODIFY_FORM_ERRORS::PRODUCT_NOT_FOUND->value,
                PRODUCT_MODIFY_FORM_ERRORS::PERMISSIONS->value,
                PRODUCT_MODIFY_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return $errorsLang;
    }
}
