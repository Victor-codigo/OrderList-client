<?php

declare(strict_types=1);

namespace App\Controller\Home\Tryout;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/home/try-out',
    name: 'home_tryout',
    methods: ['GET'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class TryoutController extends AbstractController
{
    public function __construct()
    {
    }

    public function __invoke(): Response
    {
        return $this->render('this is burgos');
    }
}
