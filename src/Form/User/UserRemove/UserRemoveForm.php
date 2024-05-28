<?php

declare(strict_types=1);

namespace App\Form\User\UserRemove;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class UserRemoveForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'UserRemoveFormCsrfTokenId';

    public static function getName(): string
    {
        return USER_REMOVE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return USER_REMOVE_FORM_FIELDS::TOKEN;
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
            ->addField(USER_REMOVE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(USER_REMOVE_FORM_FIELDS::USER_ID, FIELD_TYPE::HIDDEN)
            ->addField(USER_REMOVE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
