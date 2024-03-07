<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersRemove;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ListOrdersRemoveForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ListOrdersRemoveFormCsrfTokenId';

    public static function getName(): string
    {
        return LIST_ORDERS_REMOVE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return LIST_ORDERS_REMOVE_FORM_FIELDS::TOKEN;
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
            ->addField(LIST_ORDERS_REMOVE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(LIST_ORDERS_REMOVE_FORM_FIELDS::LIST_ORDERS_ID, FIELD_TYPE::COLLECTION)
            ->addField(LIST_ORDERS_REMOVE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
