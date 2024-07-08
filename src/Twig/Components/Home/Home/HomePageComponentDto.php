<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Home;

use App\Controller\Request\Response\UserDataResponse;
use App\Twig\Components\TwigComponentDtoInterface;

class HomePageComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $domainName,
        public readonly string $locale,
        public readonly string $languageUrl,
        public readonly bool $loggedIn,
        public readonly string $orderListHomeUrl,
        public readonly string $productsHomeUrl,
        public readonly string $shopsHomeUrl,
        public readonly ?UserDataResponse $userData,
    ) {
    }
}
