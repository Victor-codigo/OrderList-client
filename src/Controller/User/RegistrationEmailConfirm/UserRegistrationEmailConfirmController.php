<?php

declare(strict_types=1);

namespace App\Controller\User\RegistrationEmailConfirm;

use App\Twig\Components\User\RegistrationEmailConfirmation\RegistrationEmailConfirmationComponentDto;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Config\Config;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
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
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserRegistrationEmailConfirmController extends AbstractController
{
    private const SIGNUP_CONFIRM_ENDPOINT = '/api/v1/users/confirm';

    public function __construct(
        private HttpClientInterface $httpClient,
        private TranslatorInterface $translator,
        private GetPageTitleService $getPageTitleService,
        private string $domainName
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $token = $request->attributes->get('token');

        try {
            $response = $this->requestSignupConfirm($token);
            $responseData = $response->toArray();

            return $this->renderRegistrationEmailConfirmationComponentOk();
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            $responseData = (object) $e->getResponse()->toArray(false);

            if (isset($responseData->errors['email_verified'])) {
                return $this->renderRegistrationEmailConfirmationComponentAlreadyVerified();
            } else {
                return $this->renderRegistrationEmailConfirmationComponentFail();
            }
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestSignupConfirm(string $token): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'PATCH',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::SIGNUP_CONFIRM_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                'token' => $token,
            ])
        );
    }

    private function renderRegistrationEmailConfirmationComponentOk(): Response
    {
        $params = [
            'loginLink' => $this->generateUrl('user_login_home'),
            'appName' => $this->domainName,
        ];

        return $this->renderRegistrationEmailConfirmationComponent(
            $this->translator->trans('signup_email_confirmation.title', [], 'RegistrationEmailConfirmationComponent'),
            $this->translator->trans('signup_email_confirmation.message', $params, 'RegistrationEmailConfirmationComponent')
        );
    }

    private function renderRegistrationEmailConfirmationComponentAlreadyVerified(): Response
    {
        $params = [
            'loginLink' => $this->generateUrl('user_login_home'),
            'appName' => $this->domainName,
        ];

        return $this->renderRegistrationEmailConfirmationComponent(
            $this->translator->trans('signup_email_confirmation_already_verified.title', [], 'RegistrationEmailConfirmationComponent'),
            $this->translator->trans('signup_email_confirmation_already_verified.message', $params, 'RegistrationEmailConfirmationComponent')
        );
    }

    private function renderRegistrationEmailConfirmationComponentFail(): Response
    {
        return $this->renderRegistrationEmailConfirmationComponent(
            $this->translator->trans('signup_email_confirmation_fail.title', [], 'RegistrationEmailConfirmationComponent'),
            $this->translator->trans('signup_email_confirmation_fail.message', [], 'RegistrationEmailConfirmationComponent')
        );
    }

    private function renderRegistrationEmailConfirmationComponent(string $title, string $message): Response
    {
        $registrationEmailConfirmationComponent = new RegistrationEmailConfirmationComponentDto(
            $title,
            $message,
        );

        return $this->render('user/user_signup_email_confirm/index.html.twig', [
            'registrationEmailConfirmationComponentDto' => $registrationEmailConfirmationComponent,
            'pageTitle' => $this->getPageTitleService->__invoke('RegistrationEmailConfirmationComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
