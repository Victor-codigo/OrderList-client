<?php

declare(strict_types=1);

namespace App\Controller\User\Login;

use App\Form\User\Login\LOGIN_FORM_ERRORS;
use App\Form\User\Login\LOGIN_FORM_FIELDS;
use App\Form\User\Login\LoginForm;
use App\Twig\Components\User\Login\LoginComponentDto;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;
use Common\Adapter\Form\FormFactory;
use Common\Domain\Config\Config;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/login',
    name: 'user_login',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class UserLoginController extends AbstractController
{
    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoints,
        private int $cookieSessionKeepAlive,
        private string $domainName,
        private string $cookieSessionName
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(new LoginForm(), $request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formValid($form);
        }

        return $this->formNotValid($form);
    }

    private function formValid(FormInterface $form): Response
    {
        try {
            $tokenSession = $this->apiEndpoints->login(
                $form->getFieldData(LOGIN_FORM_FIELDS::EMAIL),
                $form->getFieldData(LOGIN_FORM_FIELDS::PASSWORD),
            );

            $responseHttp = $this->redirectToRoute('home');
            $responseHttp->headers->setCookie($this->getCookieSession($form, $tokenSession));

            return $responseHttp;
        } catch (RequestUnauthorizedException $e) {
            $form->addError(LOGIN_FORM_ERRORS::LOGIN->value);
        } catch (\Throwable $e) {
            $form->addError(LOGIN_FORM_ERRORS::INTERNAL_SERVER->value);
        }

        return $this->formNotValid($form);
    }

    private function getCookieSession(FormInterface $form, string $tokenSession): Cookie
    {
        $formData = $form->getData();
        $sessionExpire = 0;
        if ($formData[LOGIN_FORM_FIELDS::REMEMBER_ME]) {
            $sessionExpire = time() + $this->cookieSessionKeepAlive;
        }

        return Cookie::create(
            $this->cookieSessionName,
            $tokenSession,
            $sessionExpire,
            '/',
            $this->domainName,
            'https' === mb_strtolower(Config::CLIENT_PROTOCOL) ? true : false,
            true
        );
    }

    private function formNotValid(FormInterface $form): Response
    {
        $form->csrfTokenRefresh();

        return new Response($this->renderLoginComponent($form));
    }

    private function renderLoginComponent(FormInterface $form): string
    {
        $loginComponentData = new LoginComponentDto(
            $form->getErrors(),
            $form->getFieldData(LOGIN_FORM_FIELDS::EMAIL, ''),
            $form->getFieldData(LOGIN_FORM_FIELDS::PASSWORD, ''),
            $form->getFieldData(LOGIN_FORM_FIELDS::REMEMBER_ME, false),
            $form->getCsrfToken()
        );

        return $this->renderView('user_login/index.html.twig', [
            'LoginComponent' => $loginComponentData,
        ]);
    }
}
