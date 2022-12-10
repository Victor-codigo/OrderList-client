<?php

namespace App\Controller;

use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponent;
use App\Twig\Components\Alert\AlertComponentDto;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\HttpCllent\HttpClientInterface;
use Common\Domain\Ports\HttpCllent\HttpClientResponseInteface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '{_locale}/user/signup/confirm/{token}',
    name: 'user_signup_email_confirm',
    methods: ['GET'],
    requirements: [
        '_locale' => 'en|es'
    ]
)]
class UserSignupEmailConfirmController extends AbstractController
{
    private const SIGNUP_CONFIRM_ENDPOINT = '/api/v1/users/confirm';

    public function __construct(
        private HttpClientInterface $httpClient,
        private TranslatorInterface $translator,
        private string $domainName
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $token = $request->attributes->get('token');

        try {
            $response = $this->requestSignupConfirm($token);
            $responseData = $response->toArray();
            $responseHttp = $this->renderSignupEmailConfirmationOk();
        } catch(Error400Exception|Error500Exception|NetworkException $e) {
            $responseData = (object) $e->getResponse()->toArray(false);

            if (isset($responseData->errors['email_verified'])) {
                $responseHttp = $this->renderSignupEmailConfirmationAlreadyVerified();
            } else {
                $responseHttp = $this->renderSignupEmailConfirmationFail();
            }
        } finally {
            return $responseHttp;
        }
    }

    /**
     * @throws UnsuportedOptionException
     */
    private function requestSignupConfirm(string $token): HttpClientResponseInteface
    {
        return $this->httpClient->request(
            'PATCH',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN . self::SIGNUP_CONFIRM_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                'token' => $token
            ])
        );
    }

    private function renderSignupEmailConfirmationOk(): Response
    {
        $params = [
            'loginLink' => $this->generateUrl('user_login'),
            'appName' => $this->domainName
        ];

        return $this->renderSignupEmailConfirmation(
            $this->translator->trans('signup_email_confirmation.title', [], 'SignupEmailConfirmation'),
            $this->translator->trans('signup_email_confirmation.message', $params, 'SignupEmailConfirmation')
        );
    }

    private function renderSignupEmailConfirmationAlreadyVerified(): Response
    {
        $params = [
            'loginLink' => $this->generateUrl('user_login'),
            'appName' => $this->domainName
        ];

        return $this->renderSignupEmailConfirmation(
            $this->translator->trans('signup_email_confirmation_already_verified.title', [], 'SignupEmailConfirmation'),
            $this->translator->trans('signup_email_confirmation_already_verified.message', $params, 'SignupEmailConfirmation')
        );
    }

    private function renderSignupEmailConfirmationFail(): Response
    {
        return $this->renderSignupEmailConfirmation(
            $this->translator->trans('signup_email_confirmation_fail.title', [], 'SignupEmailConfirmation'),
            $this->translator->trans('signup_email_confirmation_fail.message', [], 'SignupEmailConfirmation')
        );
    }

    private function renderSignupEmailConfirmation(string $title, string $message): Response
    {
        $data = new AlertComponentDto(
            ALERT_TYPE::INFO,
            '',
            $title,
            $message,
            false
        );

        return $this->render('user_signup_email_confirm/index.html.twig', [
            'SignupEmailConfirmation' => $data,
        ]);
    }
}
