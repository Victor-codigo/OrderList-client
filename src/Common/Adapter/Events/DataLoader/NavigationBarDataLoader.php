<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\RequestDto;
use App\Twig\Components\NavigationBar\NavigationBarDto;
use Common\Adapter\Endpoints\Endpoints;
use Common\Domain\Config\Config;

class NavigationBarDataLoader
{
    public function __construct(
        private Endpoints $endpoints
    ) {
    }

    public function load(RequestDto $requestDto): NavigationBarDto
    {
        return new NavigationBarDto(
            Config::CLIENT_DOMAIN,
            Config::CLIENT_DOMAIN_NAME,
            'OrderListTile',
            $requestDto->getUserSessionData(),
            $requestDto->groupData?->type,
            $requestDto->groupNameUrlEncoded,
            $requestDto->listOrdersUrlEncoded,
            $requestDto->sectionActiveId,
            $requestDto->locale ?? 'en',
            $requestDto->request->attributes->get('_route') ?? '',
            $requestDto->request->attributes->get('_route_params') ?? [],
            $requestDto->getNotificationsData(),
            $requestDto->requestReferer
        );
    }
}
