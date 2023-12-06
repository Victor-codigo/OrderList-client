<?php

declare(strict_types=1);

namespace App\Form\SearchBar;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class SearchBarForm extends FormType
{
    private const FORM_CSRF_TOKEN_ID = 'SearchBarFormCsrfTokenId';

    public static function getName(): string
    {
        return SEARCHBAR_FORM_FIELDS::FORM;
    }

    public static function getCsrfTokenId(): string|null
    {
        return static::FORM_CSRF_TOKEN_ID;
    }

    public static function getCsrfTokenFieldName(): string
    {
        return SEARCHBAR_FORM_FIELDS::TOKEN;
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
            ->addField(SEARCHBAR_FORM_FIELDS::SEARCH_FILTER, FIELD_TYPE::TEXT)
            ->addField(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE, FIELD_TYPE::TEXT)
            ->addField(SEARCHBAR_FORM_FIELDS::TOKEN, FIELD_TYPE::HIDDEN)
            ->addField(SEARCHBAR_FORM_FIELDS::BUTTON, FIELD_TYPE::BUTTON);
    }
}
