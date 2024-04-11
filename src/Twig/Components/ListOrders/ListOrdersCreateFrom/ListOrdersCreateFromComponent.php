<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersCreateFrom;

use App\Form\ListOrders\ListOrdersCreateFrom\LIST_ORDERS_CREATE_FROM_FORM_ERRORS;
use App\Form\ListOrders\ListOrdersCreateFrom\LIST_ORDERS_CREATE_FROM_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersCreateFromComponent',
    template: 'Components/ListOrders/ListOrdersCreateFrom/ListOrdersCreateFromComponent.html.twig'
)]
final class ListOrdersCreateFromComponent extends TwigComponent
{
    public ListOrdersCreateFromComponentLangDto $lang;
    public ListOrdersCreateFromComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $listOrdersCreateFromFieldName;
    public readonly string $submitFieldName;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'ListOrdersCreateFromComponent';
    }

    public function mount(ListOrdersCreateFromComponentDto $data): void
    {
        $this->formName = LIST_ORDERS_CREATE_FROM_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FROM_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FROM_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FROM_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FROM_FORM_FIELDS::NAME);
        $this->listOrdersCreateFromFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FROM_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FROM_FORM_FIELDS::LIST_ORDERS_CREATE_FROM_ID);
        $this->submitFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FROM_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FROM_FORM_FIELDS::SUBMIT);

        $this->data = $data;
        $this->loadTranslation();

        $this->titleDto = $this->createTitleComponentDto();
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ListOrdersCreateFromComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->name(
                $this->translate('name.label'),
                $this->translate('name.placeholder'),
                $this->translate('name.msg_invalid')
            )
            ->listOrders(
                $this->translate('list_orders.label'),
                $this->translate('list_orders.title'),
                $this->translate('list_orders.msg_invalid'),
                $this->translate('list_orders_button.label'),
                $this->translate('list_orders_button.title'),
            )
            ->submitButton(
                $this->translate('button_list_orders_create_from.label')
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
                LIST_ORDERS_CREATE_FROM_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                LIST_ORDERS_CREATE_FROM_FORM_ERRORS::NAME_EXISTS->value => $this->translate('validation.error.name_exists'),
                LIST_ORDERS_CREATE_FROM_FORM_ERRORS::GROUP_ID->value,
                LIST_ORDERS_CREATE_FROM_FORM_ERRORS::LIST_ORDERS_CREATE_FROM_NOT_FOUND->value,
                LIST_ORDERS_CREATE_FROM_FORM_ERRORS::LIST_ORDERS_ID_CREATE_FROM->value,
                LIST_ORDERS_CREATE_FROM_FORM_ERRORS::PERMISSIONS->value,
                LIST_ORDERS_CREATE_FROM_FORM_ERRORS::INTERNAL_SERVER->value => $this->translate('validation.error.internal_server'),
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
