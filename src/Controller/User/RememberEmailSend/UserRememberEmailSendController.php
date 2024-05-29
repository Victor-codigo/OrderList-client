<?php

declare(strict_types=1);

namespace App\Controller\User\RememberEmailSend;

use App\Twig\Components\User\UserRememberEmailSend\UserRememberEmailSendComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\PageTitle\GetPageTitleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/remember/email/send',
    name: 'user_remember_email_send',
    methods: ['GET'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserRememberEmailSendController extends AbstractController
{
    public function __construct(
        private GetPageTitleService $getPageTitleService,
        private string $domainName
    ) {
    }

    public function __invoke(): Response
    {
        return $this->renderUserRememberEmailSendComponentDto();
    }

    private function renderUserRememberEmailSendComponentDto(): Response
    {
        $userRememberEmailSendComponentDto = new UserRememberEmailSendComponentDto(
            $this->generateUrl('user_remember')
        );

        return $this->render('user/user_remember_email_send/index.html.twig', [
            'userRememberEmailSendComponentDto' => $userRememberEmailSendComponentDto,
            'pageTitle' => $this->getPageTitleService->__invoke('UserRememberEmailSendComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
