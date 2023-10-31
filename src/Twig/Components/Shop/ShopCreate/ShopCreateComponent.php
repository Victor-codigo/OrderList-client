<?php

namespace App\Twig\Components\Shop\ShopCreate;

use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_ERRORS;
use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponent;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopCreateComponent',
    template: 'Components/Shop/ShopCreate/ShopCreateComponent.html.twig'
)]
final class ShopCreateComponent extends TwigComponent
{
    public ShopCreateComponentLangDto $lang;
    public ShopCreateComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $imageFieldName;
    public readonly string $submitFieldName;
    public readonly DropZoneComponentDto $imageDto;

    public static function getComponentName(): string
    {
        return 'ShopCreateComponent';
    }

    public function mount(ShopCreateComponentDto $data): void
    {
        $this->formName = SHOP_CREATE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::NAME);
        $this->descriptionFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::DESCRIPTION);
        $this->imageFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::IMAGE);
        $this->submitFieldName = sprintf('%s[%s]', SHOP_CREATE_FORM_FIELDS::FORM, SHOP_CREATE_FORM_FIELDS::SUBMIT);

        $this->data = $data;
        $this->loadTranslation();

        $this->imageDto = $this->createImageDropZone();
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

    private function loadTranslation(): void
    {
        $this->lang = (new ShopCreateComponentLangDto())
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
                $this->translate('shop_create_button.label')
            )
            ->errors(
                $this->data->validForm ? $this->loadErrorsTranslation() : null
            )
            ->build();
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $field => $error) {
            $errorsLang[] = match ($field) {
                SHOP_CREATE_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                SHOP_CREATE_FORM_ERRORS::SHOP_NAME_REPEATED->value => $this->translate('validation.error.shop_name_repeated'),
                SHOP_CREATE_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
                SHOP_CREATE_FORM_ERRORS::DESCRIPTION->value,
                SHOP_CREATE_FORM_ERRORS::GROUP_ERROR->value,
                SHOP_CREATE_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        if (!empty($errorsLang)) {
            return new AlertComponentDto(
                ALERT_TYPE::DANGER,
                '',
                '',
                array_unique($errorsLang)
            );
        }

        return new AlertComponentDto(
            ALERT_TYPE::SUCCESS,
            '',
            '',
            $this->translate('validation.ok')
        );
    }
}
