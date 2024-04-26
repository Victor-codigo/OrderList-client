<?php

declare(strict_types=1);

namespace App\Form\Group\GroupModify;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class GroupModifyForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'groupModifyCsrfTokenId';

    public static function getName(): string
    {
        return GROUP_MODIFY_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return GROUP_MODIFY_FORM_FIELDS::TOKEN;
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
            ->addField(GROUP_MODIFY_FORM_FIELDS::GROUP_ID, FIELD_TYPE::HIDDEN)
            ->addField(GROUP_MODIFY_FORM_FIELDS::IMAGE_REMOVE, FIELD_TYPE::HIDDEN)
            ->addField(GROUP_MODIFY_FORM_FIELDS::NAME, FIELD_TYPE::TEXT)
            ->addField(GROUP_MODIFY_FORM_FIELDS::DESCRIPTION, FIELD_TYPE::TEXTAREA)
            ->addField(GROUP_MODIFY_FORM_FIELDS::IMAGE, FIELD_TYPE::DROPDOWN)
            ->addField(GROUP_MODIFY_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(GROUP_MODIFY_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
