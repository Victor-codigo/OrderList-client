<?php

namespace App\Controller;

use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '{_locale}/user/signup/complete',
    name: 'user_register_complete',
    methods: ['GET'],
    requirements: [
        '_locale' => 'en|es'
    ]
)]
class UserRegistrationCompleteController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private string $domainName
    ) {
    }

    public function __invoke(): Response
    {
        $alertInfoData =new AlertComponentDto(
            ALERT_TYPE::INFO,
            '',
            $this->translator->trans('registration_complete.title', [], 'SignupComplete'),
            $this->translator->trans('registration_complete.msg', ['appName' => $this->domainName], 'SignupComplete'),
            false
        );

        return $this->render('user_registration_complete/index.html.twig', [
            'AlertComponent' => $alertInfoData,
        ]);
    }
}
