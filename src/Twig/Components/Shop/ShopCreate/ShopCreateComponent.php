<?php

namespace App\Twig\Components\Shop\ShopCreate;

use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_ERRORS;
use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponent;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\Config\Config;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopCreateComponent',
    template: 'Components/Shop/ShopCreate/ShopCreateComponent.html.twig'
)]
final class ShopCreateComponent extends TwigComponent
{
    private const CLIENT_ENDPOINT_SHOP_CREATE = Config::CLIENT_ENDPOINT_SHOP_CREATE;

    public ShopCreateComponentLangDto $lang;
    public ShopCreateComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $imageFieldName;
    public readonly string $submitFieldName;
    public readonly TitleComponentDto $titleDto;
    public readonly DropZoneComponentDto $imageDto;
    public readonly string $clientEndpointShopCreate;

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

        $this->titleDto = $this->createTitleComponentDto();
        $this->imageDto = $this->createImageDropZone();
        $this->clientEndpointShopCreate = str_replace('{group_name}', $data->groupNameUrlEncoded, self::CLIENT_ENDPOINT_SHOP_CREATE);
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
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
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
            )
            ->build();
    }

    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
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
