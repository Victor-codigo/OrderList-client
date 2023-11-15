<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopHome;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ShopHomeForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ShopHomeCsrfTokenId';

    public static function getName(): string
    {
        return SHOP_HOME_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return SHOP_HOME_FORM_FIELDS::TOKEN;
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
            ->addField(SHOP_HOME_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(SHOP_HOME_FORM_FIELDS::SHOP_REMOVE_MULTIPLE, FIELD_TYPE::SUBMIT);
    }
}
