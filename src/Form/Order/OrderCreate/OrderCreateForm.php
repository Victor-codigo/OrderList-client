<?php

declare(strict_types=1);

namespace App\Form\Order\OrderCreate;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class OrderCreateForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'OrderCreateCsrfTokenId';

    public static function getName(): string
    {
        return ORDER_CREATE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return ORDER_CREATE_FORM_FIELDS::TOKEN;
    }

    public function __construct()
    {
    }

    public function validate(ValidationInterface $validator, array $formData): array
    {
        return [];
    }

    public function formBuild(): void
    {
        $this
            ->addField(ORDER_CREATE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(ORDER_CREATE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT)
            ->addField(ORDER_CREATE_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(ORDER_CREATE_FORM_FIELDS::AMOUNT, FIELD_TYPE::NUMBER)
            ->addField(ORDER_CREATE_FORM_FIELDS::SHOP_ID, FIELD_TYPE::HIDDEN)
            ->addField(ORDER_CREATE_FORM_FIELDS::PRODUCT_ID, FIELD_TYPE::HIDDEN)
            ->addField(ORDER_CREATE_FORM_FIELDS::LIST_ORDERS_ID, FIELD_TYPE::HIDDEN);
    }
}
