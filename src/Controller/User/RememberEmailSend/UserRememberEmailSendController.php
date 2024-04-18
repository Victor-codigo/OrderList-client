<?php

declare(strict_types=1);

namespace App\Controller\User\RememberEmailSend;

use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '{_locale}/user/remember/email/send',
    name: 'user_remember_email_send',
    methods: ['GET'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class UserRememberEmailSendController extends AbstractController
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
            $this->translator->trans('email_send.title', [], 'UserrememberEmailSend'),
            $this->translator->trans(
                'email_send.message',
                ['urlRememberPasswordForm' => $this->generateUrl('user_remember')],
                'UserrememberEmailSend'
            ),
            false
        );

        return $this->render('user_remember_email_send/index.html.twig', [
            'AlertComponent' => $data,
        ]);
    }
}
