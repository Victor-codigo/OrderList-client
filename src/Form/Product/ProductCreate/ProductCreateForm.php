<?php

declare(strict_types=1);

namespace App\Form\Product\ProductCreate;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ProductCreateForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ProductCreateCsrfTokenId';

    public static function getName(): string
    {
        return PRODUCT_CREATE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return PRODUCT_CREATE_FORM_FIELDS::TOKEN;
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
            ->addField(PRODUCT_CREATE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(PRODUCT_CREATE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT)
            ->addField(PRODUCT_CREATE_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(PRODUCT_CREATE_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(PRODUCT_CREATE_FORM_FIELDS::IMAGE, FIELD_TYPE::FILE);
    }
}
