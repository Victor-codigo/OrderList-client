<?php

declare(strict_types=1);

namespace App\Controller\User\Login;

use App\Form\User\Login\LOGIN_FORM_ERRORS;
use App\Form\User\Login\LOGIN_FORM_FIELDS;
use App\Form\User\Login\LoginForm;
use App\Twig\Components\User\Login\LoginComponentDto;
use Common\Adapter\Form\FormFactory;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
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
    private const LOGIN_ENDPOINT = '/api/v1/users/login';

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient,
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

    /**
     * @throws UnsupportedOptionException
     */
    private function requestLogin(array $formData): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::LOGIN_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                'username' => $formData[LOGIN_FORM_FIELDS::EMAIL],
                'password' => $formData[LOGIN_FORM_FIELDS::PASSWORD],
            ])
        );
    }

    private function formValid(FormInterface $form): Response
    {
        try {
            $formData = $form->getData();
            $response = $this->requestLogin($formData);
            $headers = $response->getHeaders();
            $responseHttp = $this->redirectToRoute('home');
            $responseHttp->headers->setCookie($this->getCookieSession($form, $headers));
        } catch (Error400Exception $e) {
            $form->addError(LOGIN_FORM_ERRORS::LOGIN->value);
            $responseHttp = $this->formNotValid($form);
        } catch (Error500Exception|NetworkException $e) {
            $form->addError(LOGIN_FORM_ERRORS::INTERNAL_SERVER->value);
            $responseHttp = $this->formNotValid($form);
        } finally {
            return $responseHttp;
        }
    }

    private function getCookieSession(FormInterface $form, array $headers): Cookie
    {
        $formData = $form->getData();
        $sessionExpire = 0;
        if ($formData[LOGIN_FORM_FIELDS::REMEMBER_ME]) {
            $sessionExpire = time() + $this->cookieSessionKeepAlive;
        }

        return Cookie::create(
            $this->cookieSessionName,
            $headers['set-cookie'][0],
            $sessionExpire,
            '/',
            $this->domainName,
            false,      // TODO if https, change to true
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
        $formData = $form->getData();
        $loginComponentData = new LoginComponentDto(
            $form->getErrors(),
            $formData[LOGIN_FORM_FIELDS::EMAIL],
            $formData[LOGIN_FORM_FIELDS::PASSWORD],
            $formData[LOGIN_FORM_FIELDS::REMEMBER_ME],
            $form->getCsrfToken()
        );

        return $this->renderView('user_login/index.html.twig', [
            'LoginComponent' => $loginComponentData,
        ]);
    }
}
