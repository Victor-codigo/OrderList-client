<?php

declare(strict_types=1);

namespace Common\Domain\Ports\Form;

use Common\Domain\Form\FormErrorInterface;

interface FormInterface
{
    /**
     * @throws LogicException
     */
    public function getCsrfToken(): string;

    public function getErrors(): array;

    public function isValid(bool $csrfValidate = true): bool;

    public function isSubmitted(): bool;

    public function isCsrfValid(): bool;

    public function csrfTokenRefresh(): static;

    public function getData(): array;

    public function addError(string $name, mixed $value = null): static;
}
