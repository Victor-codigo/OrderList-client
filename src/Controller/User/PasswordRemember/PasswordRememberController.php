<?php

declare(strict_types=1);

namespace App\Controller\User\PasswordRemember;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordRememberController extends AbstractController
{
    #[Route('/password/remember', name: 'app_password_remember')]
    public function index(): Response
    {
        return $this->render('password_remember/index.html.twig', [
            'controller_name' => 'PasswordRememberController',
        ]);
    }
}
