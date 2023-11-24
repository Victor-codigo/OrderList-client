<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopRemoveMulti;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ShopRemoveMultiForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ShopRemoveMultiFormCsrfTokenId';

    public static function getName(): string
    {
        return SHOP_REMOVE_MULTI_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return SHOP_REMOVE_MULTI_FORM_FIELDS::TOKEN;
    }

    public function validate(ValidationInterface $validator, array $formData): array
    {
        return [];
    }

    /**
     * @return FormField[]
     */
    public function formBuild(): void
    {
        $this
            ->addField(SHOP_REMOVE_MULTI_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(SHOP_REMOVE_MULTI_FORM_FIELDS::SHOPS_ID, FIELD_TYPE::COLLECTION)
            ->addField(SHOP_REMOVE_MULTI_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
