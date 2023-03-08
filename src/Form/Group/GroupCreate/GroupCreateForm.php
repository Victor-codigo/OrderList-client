<?php

declare(strict_types=1);

namespace App\Form\Group\GroupCreate;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class GroupCreateForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'groupCreateCsrfTokenId';

    public static function getName(): string
    {
        return GROUP_CREATE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return GROUP_CREATE_FORM_FIELDS::TOKEN;
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
            ->addField(GROUP_CREATE_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(GROUP_CREATE_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(GROUP_CREATE_FORM_FIELDS::IMAGE, FIELD_TYPE::DROPDOWN)
            ->addField(GROUP_CREATE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(GROUP_CREATE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
