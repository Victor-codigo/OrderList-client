<?php

namespace App\Controller;

use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '{_locale}/user/remember/changed',
    name: 'user_password_remember_changed',
    methods: ['GET'],
    requirements:[
        '_locale' => 'en|es'
    ]
)]
class UserPasswordRememberChangedController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(): Response
    {
        $data = new AlertComponentDto(
            ALERT_TYPE::INFO,
            '',
            $this->translator->trans('title', [], 'PasswordRememberChanged'),
            $this->translator->trans('message', ['urlLoginForm' => $this->generateUrl('user_login')], 'PasswordRememberChanged'),
            false
        );

        return $this->render('user_password_remember_changed/index.html.twig', [
            'AlertComponent' => $data,
        ]);
    }
}
