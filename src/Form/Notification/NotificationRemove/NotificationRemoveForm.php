<?php

declare(strict_types=1);

namespace App\Form\Notification\NotificationRemove;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class NotificationRemoveForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'NotificationRemoveFormCsrfTokenId';

    public static function getName(): string
    {
        return NOTIFICATION_REMOVE_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return NOTIFICATION_REMOVE_FORM_FIELDS::TOKEN;
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
            ->addField(NOTIFICATION_REMOVE_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(NOTIFICATION_REMOVE_FORM_FIELDS::NOTIFICATIONS_ID, FIELD_TYPE::COLLECTION)
            ->addField(NOTIFICATION_REMOVE_FORM_FIELDS::SUBMIT, FIELD_TYPE::SUBMIT);
    }
}
