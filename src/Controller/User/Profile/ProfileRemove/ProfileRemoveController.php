<?php

declare(strict_types=1);

namespace App\Controller\User\Profile\ProfileRemove;

use App\Controller\Request\RequestDto;
use App\Form\User\UserRemove\UserRemoveForm;
use App\Twig\Components\User\UserRemove\UserRemoveComponent;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/profile/remove',
    name: 'profile_remove',
    methods: ['POST'],
    priority: 1,
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class ProfileRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $apiEndpoint,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private UserRemoveComponent $userRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $userRemoveComponent = $this->formFactory->create(new UserRemoveForm(), $requestDto->request);

        if ($userRemoveComponent->isSubmitted() && $userRemoveComponent->isValid()) {
            $this->validForm($userRemoveComponent, $requestDto->getTokenSessionOrFail());
        }

        $redirect = $this->redirectToRoute('home');
        $redirect->headers->clearCookie(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);

        return $redirect;
    }

    private function validForm(FormInterface $form, string $tokenSession): void
    {
        $responseData = $this->apiEndpoint->userRemove($tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, $errorDescription);
        }
    }
}
