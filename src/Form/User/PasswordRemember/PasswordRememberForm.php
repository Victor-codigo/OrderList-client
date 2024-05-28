<?php

declare(strict_types=1);

namespace App\Form\User\PasswordRemember;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Ports\Captcha\CaptchaInterface;
use Common\Domain\Validation\ValidationInterface;

class PasswordRememberForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'PasswordRememberFormCsrfTokenId';

    public function __construct(
        private CaptchaInterface $captcha
    ) {
    }

    public static function getName(): string
    {
        return PASSWORD_REMEMBER_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return PASSWORD_REMEMBER_FORM_FIELDS::TOKEN;
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
            ->addField(PASSWORD_REMEMBER_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(PASSWORD_REMEMBER_FORM_FIELDS::EMAIL, FIELD_TYPE::TEXT)
            ->addField(PASSWORD_REMEMBER_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT)
            ->addField(PASSWORD_REMEMBER_FORM_FIELDS::CAPTCHA, FIELD_TYPE::CAPTCHA, null, [
                'action_name' => 'login',
                'locale' => 'en',
            ]);
    }
}
