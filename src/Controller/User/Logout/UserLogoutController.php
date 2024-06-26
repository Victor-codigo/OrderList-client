<?php

declare(strict_types=1);

namespace App\Controller\User\Logout;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Config\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/user/logout',
    name: 'user_logout',
    methods: ['GET'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class UserLogoutController extends AbstractController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $redirect = $this->redirectToRoute('home');
        $redirect->headers->clearCookie($request->getSession()->getName());
        $redirect->headers->clearCookie(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);

        return $redirect;
    }
}
