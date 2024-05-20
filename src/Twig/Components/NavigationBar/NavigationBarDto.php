<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

use App\Controller\Request\RequestRefererDto;
use App\Controller\Request\Response\NotificationDataResponse;
use App\Controller\Request\Response\UserDataResponse;
use App\Twig\Components\TwigComponentDtoInterface;

class NavigationBarDto implements TwigComponentDtoInterface
{
    /**
     * @param NotificationDataResponse[] $notificationsData
     */
    public function __construct(
        public readonly string $title,
        public readonly ?UserDataResponse $userData,
        public readonly ?string $groupType,
        public readonly ?string $groupNameEncoded,
        public readonly ?string $sectionActiveId,
        public readonly string $locale,
        public readonly string $routeName,
        public readonly array $routeParameters,
        public readonly array $notificationsData,
        public readonly ?RequestRefererDto $refererRoute,
    ) {
    }
}
