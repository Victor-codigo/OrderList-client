<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersCreate;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ListOrdersCreateForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ListOrdersCreateCsrfTokenId';

    public static function getName(): string
    {
        return LIST_ORDERS_CREATE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return LIST_ORDERS_CREATE_FORM_FIELDS::TOKEN;
    }

    public function __construct(
        private array $selectUserGroups
    ) {
    }

    public function validate(ValidationInterface $validator, array $formData): array
    {
        return [];
    }

    public function formBuild(): void
    {
        $this
            ->addField(LIST_ORDERS_CREATE_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(LIST_ORDERS_CREATE_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(LIST_ORDERS_CREATE_FORM_FIELDS::DATE_TO_BUY, FIELD_TYPE::DATETIME)
            ->addField(LIST_ORDERS_CREATE_FORM_FIELDS::USER_GROUP, FIELD_TYPE::CHOICE, null, $this->selectUserGroups)
            ->addField(LIST_ORDERS_CREATE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(LIST_ORDERS_CREATE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
