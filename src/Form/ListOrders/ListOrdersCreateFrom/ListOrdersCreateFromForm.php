<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersCreateFrom;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ListOrdersCreateFromForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ListOrdersCreateFromCsrfTokenId';

    public static function getName(): string
    {
        return LIST_ORDERS_CREATE_FROM_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return LIST_ORDERS_CREATE_FROM_FORM_FIELDS::TOKEN;
    }

    public function validate(ValidationInterface $validator, array $formData): array
    {
        return [];
    }

    public function formBuild(): void
    {
        $this
            ->addField(LIST_ORDERS_CREATE_FROM_FORM_FIELDS::LIST_ORDERS_CREATE_FROM_ID, FIELD_TYPE::TEXT)
            ->addField(LIST_ORDERS_CREATE_FROM_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(LIST_ORDERS_CREATE_FROM_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(LIST_ORDERS_CREATE_FROM_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
