<?php

declare(strict_types=1);

namespace App\Controller\User\PasswordRememberChanged;

use App\Twig\Components\User\UserPasswordRememberChanged\UserPasswordRememberChangedComponentDto;
use Common\Domain\PageTitle\GetPageTitleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/remember/changed',
    name: 'user_password_remember_changed',
    methods: ['GET'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class UserPasswordRememberChangedController extends AbstractController
{
    public function __construct(
        private GetPageTitleService $getPageTitleService,
        private string $domainName
    ) {
    }

    public function __invoke(): Response
    {
        return $this->renderUserPasswordRememberChangedComponentDto();
    }

    private function renderUserPasswordRememberChangedComponentDto(): Response
    {
        $userPasswordRememberChangedComponentDto = new UserPasswordRememberChangedComponentDto(
            $this->generateUrl('user_login_home')
        );

        return $this->render('user/user_password_remember_changed/index.html.twig', [
            'userPasswordRememberChangedComponentDto' => $userPasswordRememberChangedComponentDto,
            'pageTitle' => $this->getPageTitleService->__invoke('UserPasswordRememberChangedComponent'),
        ]);
    }
}
