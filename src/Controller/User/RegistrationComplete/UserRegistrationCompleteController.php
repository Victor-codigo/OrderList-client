<?php

declare(strict_types=1);

namespace App\Controller\User\RegistrationComplete;

use App\Twig\Components\User\RegistrationComplete\RegistrationCompleteComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\PageTitle\GetPageTitleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/signup/complete',
    name: 'user_register_complete',
    methods: ['GET'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserRegistrationCompleteController extends AbstractController
{
    public function __construct(
        private GetPageTitleService $getPageTitleService,
        private string $domainName
    ) {
    }

    public function __invoke(): Response
    {
        return $this->renderRegistrationCompleteComponent();
    }

    private function renderRegistrationCompleteComponent(): Response
    {
        $registrationCompleteComponentDto = new RegistrationCompleteComponentDto(
            $this->domainName
        );

        return $this->render('user/user_registration_complete/index.html.twig', [
            'RegistrationCompleteComponentDto' => $registrationCompleteComponentDto,
            'pageTitle' => $this->getPageTitleService->__invoke('RegistrationCompleteComponent'),
        ]);
    }
}
