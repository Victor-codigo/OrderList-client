<?php

declare(strict_types=1);

namespace App\Form\Order\OrderRemove;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class OrderRemoveForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'OrderRemoveFormCsrfTokenId';

    public static function getName(): string
    {
        return ORDER_REMOVE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return ORDER_REMOVE_FORM_FIELDS::TOKEN;
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
            ->addField(ORDER_REMOVE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(ORDER_REMOVE_FORM_FIELDS::ORDERS_ID, FIELD_TYPE::COLLECTION)
            ->addField(ORDER_REMOVE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
