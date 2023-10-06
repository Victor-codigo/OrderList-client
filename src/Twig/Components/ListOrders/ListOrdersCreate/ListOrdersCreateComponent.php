<?php

namespace App\Twig\Components\ListOrders\ListOrdersCreate;

use App\Form\ListOrders\ListOrdersCreate\LIST_ORDERS_CREATE_FORM_ERRORS;
use App\Form\ListOrders\ListOrdersCreate\LIST_ORDERS_CREATE_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersCreateComponent',
    template: 'Components/ListOrders/ListOrdersCreate/ListOrdersCreateComponent.html.twig'
)]
final class ListOrdersCreateComponent extends TwigComponent
{
    public ListOrdersCreateComponentLangDto $lang;
    public ListOrdersCreateComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $dateToBuyFieldName;
    public readonly string $userGroupsFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'ListOrdersCreateComponent';
    }

    public function mount(ListOrdersCreateComponentDto $data): void
    {
        $this->formName = LIST_ORDERS_CREATE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FORM_FIELDS::NAME);
        $this->descriptionFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FORM_FIELDS::DESCRIPTION);
        $this->dateToBuyFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FORM_FIELDS::DATE_TO_BUY);
        $this->userGroupsFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FORM_FIELDS::USER_GROUP);
        $this->submitFieldName = sprintf('%s[%s]', LIST_ORDERS_CREATE_FORM_FIELDS::FORM, LIST_ORDERS_CREATE_FORM_FIELDS::SUBMIT);

        $this->data = $data;
        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ListOrdersCreateComponentLangDto())
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
            ->dateToBuy(
                $this->translate('date_to_buy.label'),
                $this->translate('date_to_buy.placeholder'),
                $this->translate('date_to_buy.msg_invalid')
            )
            ->userGroups(
                $this->translate('userGroups.label'),
                $this->translate('userGroups.placeholder'),
                $this->translate('userGroups.msg_invalid')
            )
            ->submitButton(
                $this->translate('button_list_orders_create.label')
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
                LIST_ORDERS_CREATE_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                LIST_ORDERS_CREATE_FORM_ERRORS::NAME_EXISTS->value => $this->translate('validation.error.name_exists'),
                LIST_ORDERS_CREATE_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.group_id'),
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
