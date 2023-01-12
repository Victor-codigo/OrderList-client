<?php

declare(strict_types=1);

namespace App\Form\EmailChange;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class EmailChangeForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'EmailChangeFormCsrfTokenId';



    public static function getName(): string
    {
        return EMAIL_CHANGE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return EMAIL_CHANGE_FORM_FIELDS::TOKEN;
    }

    public function __construct()
    {
    }

    public function validate(ValidationInterface $validatior, array $formData): array
    {
        return [];
    }

    /**
     * @return FormField[]
     */
    public function formBuild(): void
    {
        $this
            ->addField(EMAIL_CHANGE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(EMAIL_CHANGE_FORM_FIELDS::EMAIL, FIELD_TYPE::TEXT)
            ->addField(EMAIL_CHANGE_FORM_FIELDS::PASSWORD, FIELD_TYPE::PASSWORD)
            ->addField(EMAIL_CHANGE_FORM_FIELDS::SUBMIT, FIELD_TYPE::BUTTON);
    }
}
