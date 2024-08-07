<?php

declare(strict_types=1);

namespace App\Controller\User\Login\Create;

use App\Controller\Request\RequestDto;
use App\Form\User\Login\LOGIN_FORM_FIELDS;
use App\Form\User\Login\LoginForm;
use App\Twig\Components\User\Login\LoginComponent;
use Common\Adapter\Captcha\Recaptcha3ValidatorAdapter;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/login/execute',
    name: 'user_login_execute',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserLoginController extends AbstractController
{
    use UrlEncoder;

    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $apiEndpoint,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private LoginComponent $loginComponent,
        private Recaptcha3ValidatorAdapter $recaptcha,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $loginForm = $this->formFactory->create(new LoginForm($this->recaptcha), $requestDto->request);

        $tokenSession = null;
        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $tokenSession = $this->validForm($loginForm);
        }

        if (null !== $tokenSession) {
            $redirectResponse = $this->redirectToRoute('home');
            $redirectResponse->headers->setCookie($this->getCookieSession($loginForm, $tokenSession));

            return $redirectResponse;
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [],
            $this->loginComponent->loadErrorsTranslation($loginForm->getErrors()),
            [
                'form' => $loginForm->getData(),
            ]
        );
    }

    /**
     * @return string|null token session
     */
    private function validForm(FormInterface $form): ?string
    {
        $responseData = $this->apiEndpoint->userLogin(
            $form->getFieldData(LOGIN_FORM_FIELDS::EMAIL),
            $form->getFieldData(LOGIN_FORM_FIELDS::PASSWORD),
        );

        foreach ($responseData['errors'] as $error) {
            $form->addError((string) $error);
        }

        if (!empty($responseData['errors'])) {
            return null;
        }

        return $responseData['data']['token_session'];
    }

    private function getCookieSession(FormInterface $form, string $tokenSession): Cookie
    {
        $formData = $form->getData();
        $sessionExpire = 0;
        if ($formData[LOGIN_FORM_FIELDS::REMEMBER_ME]) {
            $sessionExpire = time() + Config::SESSION_KEEP_ALIVE;
        }

        return Cookie::create(
            Config::COOKIE_TOKEN_SESSION_NAME,
            $tokenSession,
            $sessionExpire,
            '/',
            '',
            'https' === mb_strtolower(Config::CLIENT_PROTOCOL) ? true : false,
            false
        );
    }
}
