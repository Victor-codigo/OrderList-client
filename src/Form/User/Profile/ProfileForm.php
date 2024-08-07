<?php

declare(strict_types=1);

namespace App\Form\User\Profile;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class ProfileForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'ProfileFormCsrfTokenId';

    public static function getName(): string
    {
        return PROFILE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return PROFILE_FORM_FIELDS::TOKEN;
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
            ->addField(PROFILE_FORM_FIELDS::NICK, FIELD_TYPE::TEXT)
            ->addField(PROFILE_FORM_FIELDS::IMAGE, FIELD_TYPE::FILE)
            ->addField(PROFILE_FORM_FIELDS::IMAGE_REMOVE, FIELD_TYPE::HIDDEN)
            ->addField(PROFILE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(PROFILE_FORM_FIELDS::SUBMIT, FIELD_TYPE::BUTTON);
    }
}
