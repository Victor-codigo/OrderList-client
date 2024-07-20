<?php

declare(strict_types=1);

namespace App\Controller\User\SignUp\Signup;

use App\Controller\Request\RequestDto;
use App\Form\User\Signup\SIGNUP_FORM_FIELDS;
use App\Form\User\Signup\SignupForm;
use App\Twig\Components\User\Signup\SignupComponent;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Captcha\CaptchaInterface;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/signup/execute',
    name: 'user_register_execute',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserSignupController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $apiEndpoint,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private SignupComponent $signupComponent,
        private CaptchaInterface $captcha
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $signupForm = $this->formFactory->create(new SignupForm($this->captcha), $requestDto->request);

        $signedUp = false;
        if ($signupForm->isSubmitted() && $signupForm->isValid()) {
            $signedUp = $this->validForm($signupForm, $requestDto->locale);
        }

        if ($signedUp) {
            return $this->redirectToRoute('user_register_complete');
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [],
            $this->signupComponent->loadErrorsTranslation($signupForm->getErrors()),
            [
                'form' => $signupForm->getData(),
            ]
        );
    }

    private function validForm(FormInterface $form, string $locale): bool
    {
        $emailConfirmationUrl = $this->generateUrl('user_signup_email_confirm', [
            '_locale' => $locale,
        ]);

        $responseData = $this->apiEndpoint->userSignUp(
            $form->getFieldData(SIGNUP_FORM_FIELDS::NICK),
            $form->getFieldData(SIGNUP_FORM_FIELDS::EMAIL),
            $form->getFieldData(SIGNUP_FORM_FIELDS::PASSWORD),
            $emailConfirmationUrl,
            $locale
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, $errorDescription);
        }

        return empty($form->getErrors());
    }
}
