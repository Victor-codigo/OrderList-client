<?php

declare(strict_types=1);

namespace App\Controller\User\RegistrationEmailConfirm;

use App\Twig\Components\User\RegistrationEmailConfirmation\RegistrationEmailConfirmationComponentDto;
use Common\Adapter\Endpoints\UsersEndpoint;
use Common\Domain\Config\Config;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
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
    private const SIGNUP_CONFIRM_ENDPOINT = UsersEndpoint::PATCH_SIGNUP_CONFIRM_ENDPOINT;

    public function __construct(
        private HttpClientInterface $httpClient,
        private TranslatorInterface $translator,
        private GetPageTitleService $getPageTitleService,
        private EndpointsInterface $apiEndpoints
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $token = $request->attributes->get('token');
        $responseData = $this->apiEndpoints->userRegistrationEmailConfirmation(self::SIGNUP_CONFIRM_ENDPOINT, $token);

        if (empty($responseData['errors'])) {
            return $this->renderRegistrationEmailConfirmationComponentOk();
        }

        if (isset($responseData['errors']['email_verified'])) {
            return $this->renderRegistrationEmailConfirmationComponentAlreadyVerified();
        } else {
            return $this->renderRegistrationEmailConfirmationComponentFail();
        }
    }

    private function renderRegistrationEmailConfirmationComponentOk(): Response
    {
        $params = [
            'loginLink' => $this->generateUrl('user_login_home'),
            'appName' => Config::CLIENT_DOMAIN_NAME,
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
            'appName' => Config::CLIENT_DOMAIN_NAME,
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
