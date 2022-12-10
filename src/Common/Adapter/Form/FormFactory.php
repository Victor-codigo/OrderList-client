<?php

declare(strict_types=1);

namespace Common\Adapter\Form;

use Common\Domain\Form\FormType;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Validation\ValidationInterface;
use Symfony\Component\Form\FormFactoryInterface as SymfonyFormFactoryInterface;
use Symfony\Component\Form\FormInterface as SymfonyFormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class FormFactory implements FormFactoryInterface
{
    private SymfonyFormFactoryInterface $formFactory;
    private CsrfTokenManagerInterface $tokenManager;
    private ValidationInterface $validator;

    public function __construct(SymfonyFormFactoryInterface $formFactory, CsrfTokenManagerInterface $tokenManager, ValidationInterface $validator)
    {
        $this->formFactory = $formFactory;
        $this->tokenManager = $tokenManager;
        $this->validator = $validator;
    }

    public function create(FormType $formType, Request|null $request = null): FormInterface
    {
        $form = $this->formFactory->createNamed(
            $formType::getName(),
            FormTypeSymfony::class,
            [FormTypeSymfony::OPTION_FORM_TYPE => $formType]
        );

        $form->handleRequest($request);

        return $this->createForm($form, $formType::getCsrfTokenId());
    }

    private function createForm(SymfonyFormInterface $form, string|null $csrfTokenId): FormSymfonyAdapter
    {
        return new FormSymfonyAdapter($form, $this->tokenManager, $this->validator, $csrfTokenId);
    }
}
