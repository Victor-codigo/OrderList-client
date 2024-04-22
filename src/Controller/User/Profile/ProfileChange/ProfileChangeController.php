<?php

declare(strict_types=1);

namespace App\Controller\User\Profile\ProfileChange;

use App\Controller\Request\RequestDto;
use App\Form\User\Profile\PROFILE_FORM_FIELDS;
use App\Form\User\Profile\ProfileForm;
use App\Twig\Components\User\Profile\ProfileComponent;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/profile/change',
    name: 'profile_change',
    methods: ['POST'],
    priority: 1,
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ProfileChangeController extends AbstractController
{
    use UrlEncoder;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $apiEndpoint,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ProfileComponent $profileComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $profileForm = $this->formFactory->create(new ProfileForm(), $requestDto->request);

        $success = false;
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $success = $this->validForm($profileForm, $requestDto->tokenSession);
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $this->getRouteParams($profileForm, $success, $requestDto->requestReferer->params),
            [$this->profileComponent->loadValidationOkTranslation()],
            $this->profileComponent->loadErrorsTranslation($profileForm->getErrors()),
            []
        );
    }

    private function validForm(FormInterface $form, string $tokenSession): bool
    {
        $responseData = $this->apiEndpoint->userModify(
            $form->getFieldData(PROFILE_FORM_FIELDS::NICK, ''),
            $form->getFieldData(PROFILE_FORM_FIELDS::IMAGE, null),
            $form->getFieldData(PROFILE_FORM_FIELDS::IMAGE_REMOVE, false),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, $errorDescription);
        }

        return empty($responseData['errors']) ? true : false;
    }

    private function getRouteParams(FormInterface $form, bool $formSuccess, array $routePrams): array
    {
        if ($formSuccess) {
            return [
                '_locale' => $routePrams['_locale'],
                'user_name' => $this->encodeUrl($form->getFieldData(PROFILE_FORM_FIELDS::NICK)),
            ];
        }

        return $routePrams;
    }
}
