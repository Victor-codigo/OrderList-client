<?php

declare(strict_types=1);

namespace App\Form\GroupUsers\GroupUsersRemove;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class GroupUsersRemoveForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'groupUserRemoveCsrfTokenId';

    public static function getName(): string
    {
        return GROUP_USERS_REMOVE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): ?string
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return GROUP_USERS_REMOVE_FORM_FIELDS::TOKEN;
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
            ->addField(GROUP_USERS_REMOVE_FORM_FIELDS::GROUP_ID, FIELD_TYPE::HIDDEN)
            ->addField(GROUP_USERS_REMOVE_FORM_FIELDS::USERS_ID, FIELD_TYPE::COLLECTION)
            ->addField(GROUP_USERS_REMOVE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(GROUP_USERS_REMOVE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
