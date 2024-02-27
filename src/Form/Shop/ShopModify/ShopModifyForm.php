<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopModify;

use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ShopModifyForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ShopModifyCsrfTokenId';

    public static function getName(): string
    {
        return SHOP_MODIFY_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return SHOP_MODIFY_FORM_FIELDS::TOKEN;
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
            ->addField(SHOP_MODIFY_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(SHOP_MODIFY_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT)
            ->addField(SHOP_MODIFY_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(SHOP_MODIFY_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(SHOP_MODIFY_FORM_FIELDS::IMAGE, FIELD_TYPE::FILE)
            ->addField(SHOP_MODIFY_FORM_FIELDS::IMAGE_REMOVE, FIELD_TYPE::HIDDEN)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_ID, FIELD_TYPE::COLLECTION)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_NAME, FIELD_TYPE::COLLECTION)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_PRICE, FIELD_TYPE::COLLECTION)
            ->addField(SHOP_CREATE_FORM_FIELDS::PRODUCT_UNIT_MEASURE, FIELD_TYPE::COLLECTION);
    }
}
