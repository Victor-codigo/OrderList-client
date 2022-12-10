<?php

declare(strict_types=1);

namespace App\Form\Login;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\EMAIL_TYPES;
use Common\Domain\Validation\ValidationInterface;

class LoginForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'loginFormCsrfTokenId';



    public static function getName(): string
    {
        return LOGIN_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return LOGIN_FORM_FIELDS::TOKEN;
    }

    public function __construct()
    {
        $this->fields = new LOGIN_FORM_FIELDS();
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
            ->addField(LOGIN_FORM_FIELDS::EMAIL, FIELD_TYPE::EMAIL)
            ->addField(LOGIN_FORM_FIELDS::PASSWORD, FIELD_TYPE::PASSWORD)
            ->addField(LOGIN_FORM_FIELDS::REMEMBER_ME, FIELD_TYPE::CHECKBOX)
            ->addField(LOGIN_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(LOGIN_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
