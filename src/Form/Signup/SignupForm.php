<?php

declare(strict_types=1);

namespace App\Form\Signup;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class SignupForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'SignupFormCsrfTokenId';



    public static function getName(): string
    {
        return SIGNUP_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return SIGNUP_FORM_FIELDS::TOKEN;
    }

    public function __construct()
    {
        $this->fields = new SIGNUP_FORM_FIELDS();
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
            ->addField(SIGNUP_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(SIGNUP_FORM_FIELDS::EMAIL, FIELD_TYPE::EMAIL)
            ->addField(SIGNUP_FORM_FIELDS::PASSWORD, FIELD_TYPE::PASSWORD)
            ->addField(SIGNUP_FORM_FIELDS::PASSWORD_REPEATED, FIELD_TYPE::PASSWORD)
            ->addField(SIGNUP_FORM_FIELDS::NICK, FIELD_TYPE::TEXT);
    }
}
