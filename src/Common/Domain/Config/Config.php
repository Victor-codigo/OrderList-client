<?php

declare(strict_types=1);

namespace Common\Domain\Config;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;

class Config
{
    public const CLIENT_PROTOCOL = 'http';
    public const CLIENT_DOMAIN = 'orderlist.client';
    public const CLIENT_PROTOCOL_AND_DOMAIN = self::CLIENT_PROTOCOL.'://'.self::CLIENT_DOMAIN;
    public const CLIENT_ENDPOINT_SHOP_CREATE = '/{_locale}/shop/{group_name}/create';
    public const CLIENT_ENDPOINT_SHOP_MODIFY = '/{_locale}/shop/{group_name}/modify/{shop_name}';
    public const CLIENT_ENDPOINT_SHOP_REMOVE = '/{_locale}/shop/{group_name}/remove';

    public const SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/shop/shop-no-image-200x200.svg';

    public const API_IMAGES_SHOP_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/assets/img/shops';
}
