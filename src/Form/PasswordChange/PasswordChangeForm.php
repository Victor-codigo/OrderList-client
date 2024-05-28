<?php

declare(strict_types=1);

namespace App\Form\PasswordChange;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class PasswordChangeForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'PasswordChangeFormCsrfTokenId';

    public static function getName(): string
    {
        return PASSWORD_CHANGE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return PASSWORD_CHANGE_FORM_FIELDS::TOKEN;
    }

    public function __construct()
    {
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
            ->addField(PASSWORD_CHANGE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(PASSWORD_CHANGE_FORM_FIELDS::USER_ID, FIELD_TYPE::TEXT)
            ->addField(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_OLD, FIELD_TYPE::PASSWORD)
            ->addField(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW, FIELD_TYPE::PASSWORD)
            ->addField(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT, FIELD_TYPE::PASSWORD)
            ->addField(PASSWORD_CHANGE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
