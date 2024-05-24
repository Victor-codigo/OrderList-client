<?php

declare(strict_types=1);

namespace App\Controller\Home\Home;

use App\Controller\Request\RequestDto;
use App\Twig\Components\Home\Home\HomePageComponentDto;
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
    public function index(RequestDto $requestDto): Response
    {
        $homePageComponentDto = new HomePageComponentDto(
            Config::CLIENT_DOMAIN_NAME,
            $requestDto->locale,
            $this->generateUrl('home', [
                '_locale' => 'en' === $requestDto->locale ? 'es' : 'en',
            ]),
        );

        return $this->render('home/index.html.twig', [
            'homePageComponentDto' => $homePageComponentDto,
        ]);
    }
}
