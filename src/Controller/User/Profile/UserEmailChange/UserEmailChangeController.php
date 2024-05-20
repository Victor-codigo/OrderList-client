<?php

declare(strict_types=1);

namespace App\Controller\User\Profile\UserEmailChange;

use App\Controller\Request\RequestDto;
use App\Form\EmailChange\EMAIL_CHANGE_FORM_FIELDS;
use App\Form\EmailChange\EmailChangeForm;
use App\Twig\Components\User\EmailChange\EmailChangeComponent;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/profile/email-change',
    name: 'profile_email_change',
    methods: ['POST'],
    priority: 1,
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserEmailChangeController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $apiEndpoint,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private EmailChangeComponent $emailChangeComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $emailChangeForm = $this->formFactory->create(new EmailChangeForm(), $requestDto->request);

        if ($emailChangeForm->isSubmitted() && $emailChangeForm->isValid()) {
            $this->validForm($emailChangeForm, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->emailChangeComponent->loadValidationOkTranslation()],
            $this->emailChangeComponent->loadErrorsTranslation($emailChangeForm->getErrors()),
            []
        );
    }

    private function validForm(FormInterface $form, string $tokenSession): void
    {
        $responseData = $this->apiEndpoint->userEmailChange(
            $form->getFieldData(EMAIL_CHANGE_FORM_FIELDS::EMAIL, ''),
            $form->getFieldData(EMAIL_CHANGE_FORM_FIELDS::PASSWORD, ''),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, $errorDescription);
        }
    }
}
