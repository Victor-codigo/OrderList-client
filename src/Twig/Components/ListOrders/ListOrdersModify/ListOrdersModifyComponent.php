<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersModify;

use App\Form\ListOrders\ListOrdersModify\LIST_ORDERS_MODIFY_FORM_ERRORS;
use App\Form\ListOrders\ListOrdersModify\LIST_ORDERS_MODIFY_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersModifyComponent',
    template: 'Components/ListOrders/ListOrdersModify/ListOrdersModifyComponent.html.twig'
)]
final class ListOrdersModifyComponent extends TwigComponent
{
    public ListOrdersModifyComponentLangDto $lang;
    public ListOrdersModifyComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $dateToBuyFieldName;
    public readonly string $userGroupsFieldName;
    public readonly string $submitFieldName;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'ListOrdersModifyComponent';
    }

    public function mount(ListOrdersModifyComponentDto $data): void
    {
        $this->formName = LIST_ORDERS_MODIFY_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', LIST_ORDERS_MODIFY_FORM_FIELDS::FORM, LIST_ORDERS_MODIFY_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', LIST_ORDERS_MODIFY_FORM_FIELDS::FORM, LIST_ORDERS_MODIFY_FORM_FIELDS::NAME);
        $this->descriptionFieldName = sprintf('%s[%s]', LIST_ORDERS_MODIFY_FORM_FIELDS::FORM, LIST_ORDERS_MODIFY_FORM_FIELDS::DESCRIPTION);
        $this->dateToBuyFieldName = sprintf('%s[%s]', LIST_ORDERS_MODIFY_FORM_FIELDS::FORM, LIST_ORDERS_MODIFY_FORM_FIELDS::DATE_TO_BUY);
        $this->submitFieldName = sprintf('%s[%s]', LIST_ORDERS_MODIFY_FORM_FIELDS::FORM, LIST_ORDERS_MODIFY_FORM_FIELDS::SUBMIT);

        $this->data = $data;
        $this->loadTranslation();

        $this->titleDto = $this->createTitleComponentDto();
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP,null);
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ListOrdersModifyComponentLangDto())
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
            ->submitButton(
                $this->translate('button_list_orders_modify.label')
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
                LIST_ORDERS_MODIFY_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                LIST_ORDERS_MODIFY_FORM_ERRORS::LIST_ORDERS_NAME_EXISTS->value => $this->translate('validation.error.name_exists'),
                LIST_ORDERS_MODIFY_FORM_ERRORS::LIST_ORDERS_ID->value ,
                LIST_ORDERS_MODIFY_FORM_ERRORS::LIST_ORDERS_NOT_FOUND->value ,
                LIST_ORDERS_MODIFY_FORM_ERRORS::PERMISSIONS->value ,
                LIST_ORDERS_MODIFY_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
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
