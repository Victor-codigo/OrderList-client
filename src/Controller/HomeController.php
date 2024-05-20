<?php

namespace App\Controller;

use Common\Domain\Config\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(
        path: '{_locale}/home',
        name: 'home',
        methods: ['GET'],
        requirements: [
            '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        ]
    )]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
