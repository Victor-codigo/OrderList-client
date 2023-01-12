<?php

declare(strict_types=1);

namespace App\Form\PasswordRemember;

use App\Form\PasswordRemember\PASSWORD_REMEMBER_FORM_FIELDS;
use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class PasswordRememberForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'PasswordRememberFormCsrfTokenId';



    public static function getName(): string
    {
        return PASSWORD_REMEMBER_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return PASSWORD_REMEMBER_FORM_FIELDS::TOKEN;
    }

    public function __construct()
    {
        $this->formFields = new PASSWORD_REMEMBER_FORM_FIELDS();
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
            ->addField(PASSWORD_REMEMBER_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(PASSWORD_REMEMBER_FORM_FIELDS::EMAIL, FIELD_TYPE::TEXT)
            ->addField(PASSWORD_REMEMBER_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
