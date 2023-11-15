<?php

declare(strict_types=1);

namespace Common\Domain\Config;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;

class Config
{
    private const CLIENT_DOMAIN = 'http://orderlist.client';

    public const SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_DOMAIN.'/assets/images/common/shop/shop-no-image-200x200.svg';

    public const API_IMAGES_SHOP_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/assets/img/shops';
}
