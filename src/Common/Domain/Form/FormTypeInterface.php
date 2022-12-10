<?php

declare(strict_types=1);

namespace Common\Domain\Form;

use Common\Domain\Validation\ValidationInterface;

interface FormTypeInterface
{
    public static function getName(): string;

    public static function getCsrfTokenId(): string|null;

    public static function getCsrfTokenFieldName(): string;

    public function validate(ValidationInterface $validatior, array $formData): array;

    /**
     * @return FormField[]
     */
    public function formBuild(): void;

    /**
     * @var FormField[]
     */
    public function getFields(): array;

    public function getFieldsValueDefaults(): array;
}
