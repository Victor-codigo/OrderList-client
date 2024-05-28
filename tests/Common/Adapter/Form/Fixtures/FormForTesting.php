<?php

declare(strict_types=1);

namespace App\Tests\Common\Adapter\Form\Fixtures;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormType;
use Common\Domain\Validation\ValidationInterface;

class FormForTesting extends FormType
{
    public static function getName(): string
    {
        return 'formName';
    }

    public static function getCsrfTokenId(): string
    {
        return 'csrfTokenId';
    }

    public static function getCsrfTokenFieldName(): string
    {
        return 'csrfTokenName';
    }

    public function __construct()
    {
        $this->addField('field1', FIELD_TYPE::TEXT);
    }

    public function formBuild(): void
    {
    }

    public function validate(ValidationInterface $validator, array $formData): array
    {
        return [];
    }
}
