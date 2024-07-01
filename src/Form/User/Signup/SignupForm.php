<?php

declare(strict_types=1);

namespace App\Form\User\Signup;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Ports\Captcha\CaptchaInterface;
use Common\Domain\Validation\ValidationInterface;

class SignupForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'SignupFormCsrfTokenId';

    public function __construct(
        private CaptchaInterface $captcha
    ) {
    }

    public static function getName(): string
    {
        return SIGNUP_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return SIGNUP_FORM_FIELDS::TOKEN;
    }

    public function validate(ValidationInterface $validator, array $formData): array
    {
        if ($this->captcha->valid()) {
            return [];
        }

        return $this->captcha->getErrors();
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
            ->addField(SIGNUP_FORM_FIELDS::NICK, FIELD_TYPE::TEXT)
            ->addField(SIGNUP_FORM_FIELDS::CAPTCHA, FIELD_TYPE::CAPTCHA, null, [
                'action_name' => 'signup',
                'locale' => 'en',
            ]);
    }
}
