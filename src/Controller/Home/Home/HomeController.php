<?php

declare(strict_types=1);

namespace App\Controller\Home\Home;

use App\Controller\Request\RequestDto;
use App\Twig\Components\Home\Home\HomePageComponentDto;
use Common\Adapter\Router\RouterSelector;
use Common\Domain\Config\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/home',
    name: 'home',
    methods: ['GET'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class HomeController extends AbstractController
{
    public function __construct(
        private RouterSelector $routerSelector
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $urlParams = [
            'page' => 1,
            'page_items' => 100,
        ];

        $groupNameEncodedSuffix = '_no_group';
        if (array_key_exists('group_name', $requestDto->requestReferer->params)) {
            $urlParams['group_name'] = $requestDto->requestReferer->params['group_name'];
            $groupNameEncodedSuffix = '_group';
        }

        $homePageComponentDto = new HomePageComponentDto(
            Config::CLIENT_DOMAIN_NAME,
            $requestDto->locale,
            $this->generateUrl('home', [
                '_locale' => 'en' === $requestDto->locale ? 'es' : 'en',
            ]),
            null === $requestDto->getTokenSessionOrFail() ? false : true,
            $this->generateUrl('list_orders_home'.$groupNameEncodedSuffix, [
                'section' => 'list-orders',
                ...$urlParams,
            ]),
            $this->generateUrl('product_home'.$groupNameEncodedSuffix, [
                'section' => 'product',
                ...$urlParams,
            ]),
            $this->generateUrl('shop_home'.$groupNameEncodedSuffix, [
                'section' => 'shop',
                ...$urlParams,
            ])
        );

        return $this->render('home/index.html.twig', [
            'homePageComponentDto' => $homePageComponentDto,
        ]);
    }
}
