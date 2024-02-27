<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopCreate;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ShopCreateForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ShopCreateCsrfTokenId';

    public static function getName(): string
    {
        return SHOP_CREATE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return SHOP_CREATE_FORM_FIELDS::TOKEN;
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
            ->addField(SHOP_CREATE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(SHOP_CREATE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT)
            ->addField(SHOP_CREATE_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(SHOP_CREATE_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(SHOP_CREATE_FORM_FIELDS::IMAGE, FIELD_TYPE::FILE)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_ID, FIELD_TYPE::COLLECTION)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_NAME, FIELD_TYPE::COLLECTION)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_PRICE, FIELD_TYPE::COLLECTION)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_UNIT_MEASURE, FIELD_TYPE::COLLECTION);
    }
}
