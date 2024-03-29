<?php

declare(strict_types=1);

namespace App\Form\Product\ProductModify;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ProductModifyForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ProductModifyCsrfTokenId';

    public static function getName(): string
    {
        return PRODUCT_MODIFY_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return PRODUCT_MODIFY_FORM_FIELDS::TOKEN;
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
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::IMAGE, FIELD_TYPE::FILE)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::IMAGE_REMOVE, FIELD_TYPE::TEXT)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::SHOP_ID, FIELD_TYPE::COLLECTION)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::SHOP_NAME, FIELD_TYPE::COLLECTION)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::SHOP_PRICE, FIELD_TYPE::COLLECTION)
            ->addField(PRODUCT_MODIFY_FORM_FIELDS::SHOP_UNIT_MEASURE, FIELD_TYPE::COLLECTION);
    }
}
