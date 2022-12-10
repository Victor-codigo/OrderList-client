<?php

declare(strict_types=1);

namespace Common\Adapter\Form;

use Common\Domain\Form\FormErrorInterface;
use Common\Domain\Form\FormTypeInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Validation\ValidationInterface;
use LogicException;
use Symfony\Component\Form\FormInterface as SymfonyFormInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @depends symfony/form
 * @depends symfony/security-csrf
 */
class FormSymfonyAdapter implements FormInterface
{
    private SymfonyFormInterface $form;
    private CsrfTokenManagerInterface $tokenManager;
    private ValidationInterface $validator;

    private string|null $csrfTokenId;
    private string $csrfTokenValue;
    private array $validationErrors;




    /**
     * @throws LogicException
     */
    public function getCsrfToken(): string
    {
        if (!isset($this->csrfTokenValue)) {
            $this->csrfTokenValue = $this->csrfTokenGenerate(false);
        }

        return $this->csrfTokenValue;
    }

    public function getErrors(): array
    {
        if (!isset($this->validationErrors)) {
            return [];
        }

        return  $this->validationErrors;
    }

    public function __construct(SymfonyFormInterface $form, CsrfTokenManagerInterface $tokenManager, ValidationInterface $validator, string|null $csrfTokenId = null)
    {
        $this->form = $form;
        $this->tokenManager = $tokenManager;
        $this->validator = $validator;
        $this->csrfTokenId = $csrfTokenId;
    }

    public function isValid(bool $csrfValidation = true): bool
    {
        if (!isset($this->validationErrors)) {
            $this->validationErrors = $this->getFormType()->validate($this->validator, $this->getData());
        }

        return (!$csrfValidation || $this->isCsrfValid()) && empty($this->validationErrors);
    }

    public function isSubmitted(): bool
    {
        return $this->form->isSubmitted();
    }


    public function isCsrfValid(): bool
    {
        $data = $this->form->getData();
        $tokenFieldName = $this->getFormType()::getCsrfTokenFieldName();

        if (!isset($data[$tokenFieldName])) {
            return false;
        }

        if (!isset($this->csrfTokenId)) {
            return false;
        }

        if (!isset($this->csrfTokenValue)) {
            $this->csrfTokenValue = $this->csrfTokenGenerate(false);
        }

        return $this->tokenManager->isTokenValid(
            new CsrfToken($this->csrfTokenId, $data[$tokenFieldName])
        );
    }

    private function csrfTokenGenerate(bool $refresh = false): string
    {
        return match ($refresh) {
            true => $this->tokenManager->refreshToken($this->csrfTokenId)->getValue(),
            false => $this->tokenManager->getToken($this->csrfTokenId)->getValue()
        };
    }

    public function csrfTokenRefresh(): static
    {
        $this->csrfTokenValue = $this->csrfTokenGenerate(true);

        return $this;
    }

    public function addError(string $name, mixed $value = null): static
    {
        $this->validationErrors[$name] = $value;

        return $this;
    }

    public function getData(): array
    {
        $dataDefault = $this->getFieldsValueDefaults();
        $dataSetted = $this->form->getData();
        unset($dataSetted[FormTypeSymfony::OPTION_FORM_TYPE]);

        return array_merge($dataDefault, $dataSetted);
    }

    private function getFieldsValueDefaults(): array
    {
        $formType = $this->getFormType();

        return $formType->getFieldsValueDefaults();
    }


    private function getFormType(): FormTypeInterface
    {
        return $this->form->getConfig()->getType()->getInnerType()->getFormType();
    }
}
