<?php

declare(strict_types=1);

namespace App\Controller\User\Profile\ProfilePasswordChange;

use App\Controller\Request\RequestDto;
use App\Form\PasswordChange\PASSWORD_CHANGE_FORM_FIELDS;
use App\Form\PasswordChange\PasswordChangeForm;
use App\Twig\Components\User\PasswordChange\PasswordChangeComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/profile/password-change',
    name: 'profile_password_change',
    methods: ['POST'],
    priority: 1,
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ProfilePasswordChangeController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $apiEndpoint,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private PasswordChangeComponent $passwordChangeComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $profilePasswordChangeForm = $this->formFactory->create(new PasswordChangeForm(), $requestDto->request);

        if ($profilePasswordChangeForm->isSubmitted() && $profilePasswordChangeForm->isValid()) {
            $this->validForm($profilePasswordChangeForm, $requestDto->tokenSession);
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->passwordChangeComponent->loadValidationOkTranslation()],
            $this->passwordChangeComponent->loadErrorsTranslation($profilePasswordChangeForm->getErrors()),
            []
        );
    }

    private function validForm(FormInterface $form, string $tokenSession): void
    {
        $responseData = $this->apiEndpoint->userPasswordChange(
            $form->getFieldData(PASSWORD_CHANGE_FORM_FIELDS::USER_ID, ''),
            $form->getFieldData(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_OLD, ''),
            $form->getFieldData(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW, ''),
            $form->getFieldData(PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT, ''),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, $errorDescription);
        }
    }
}
