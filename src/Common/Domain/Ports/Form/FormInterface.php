<?php

declare(strict_types=1);

namespace Common\Domain\Ports\Form;

interface FormInterface
{
    /**
     * @throws LogicException
     */
    public function getCsrfToken(): string;

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function isValid(bool $csrfValidate = true): bool;

    public function isSubmitted(): bool;

    public function isButtonClicked(string $buttonName): bool;

    public function isCsrfValid(): bool;

    public function csrfTokenRefresh(): static;

    public function getData(): array;

    /**
     * @throws \InvalidArgumentException
     */
    public function getFieldData(string $fieldName, mixed $default = null): mixed;

    /**
     * @throws \InvalidArgumentException
     */
    public function setFieldData(string $fieldName, mixed $value): mixed;

    public function addError(string $name, mixed $value = null): static;

    public function getFormName(): string;
}
