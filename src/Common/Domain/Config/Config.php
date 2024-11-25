<?php

declare(strict_types=1);

namespace Common\Domain\Config;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;

class Config
{
    public const CLIENT_PROTOCOL = 'http';
    public const string CLIENT_DOMAIN = '127.0.0.1';
    public const API_DOMAIN = 'http://nginx-api';
    public const CLIENT_DOMAIN_NAME = 'Order List';
    public const COOKIE_TOKEN_SESSION_NAME = 'TOKENSESSION';
    public const SESSION_KEEP_ALIVE = 5_184_000; // 60 days
    public const ADMIN_EMAIL = 'admin@orderlist.com';
    public const CLIENT_DOMAIN_LOCALE_VALID = 'es|en';

    public const USER_TRY_OUT_EMAIL = 'guest@email.com';
    public const USER_TRY_OUT_PASSWORD = '1597531564';

    public const SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = '/build/images/common/shop/shop-no-image.svg';
    public const PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = '/build/images/common/product/product-no-image.svg';
    public const LIST_ORDERS_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = '/build/images/common/list_orders/list-orders-no-image.svg';
    public const ORDER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = '/build/images/common/order/order-no-image.svg';
    public const USER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = '/build/images/common/user/user-avatar-no-image.svg';
    public const GROUP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = '/build/images/common/group/group-no-image.svg';
    public const NOTIFICATION_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = '/build/images/common/notification/notification-no-image.svg';

    public const LIST_EMPTY_IMAGE = '/build/images/common/list-icon.svg';
    public const ORDER_BOUGHT_ICON = '/build/images/common/order/bought-icon-green-24x24.svg';
    public const ORDER_BOUGHT_NOT_ICON = '/build/images/common/order/bought-not-icon-orange-24x24.svg';

    public const string LIST_ORDERS_NO_IMAGE = 'common/list_orders/list-orders-no-image.svg';
    public const string PRODUCT_NO_IMAGE = 'common/product/product-no-image.svg';
    public const string SHOP_NO_IMAGE = 'common/shop/shop-no-image.svg';

    public const API_IMAGES_SHOP_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/build/img/shops';
    public const API_IMAGES_PRODUCTS_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/build/img/products';
    public const API_IMAGES_USERS_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/build/img/users';

    // Maximum number of items in a modal list
    public const MODAL_LIST_ITEMS_MAX_NUMBER = 10;

    // Currency
    public const CURRENCY = '€';

    /**
     * Maximum number of users per group.
     *
     * @see This value, must be the same of AppConfig::GROUP_USERS_MAX in api
     */
    public const GROUP_USERS_MAX = 100;

    /**
     * Maximum number of item per page.
     */
    public const PAGINATION_ITEMS_MAX = 20;

    /**
     * Determines if between the client and the api, there is a proxy.
     */
    public const bool HAS_PROXY = true;

    /**
     * Gets the HTTP configuration for connections with the api.
     *
     * @return array{}|array{
     *  proxy: string,
     *  verify_peer: boolean,
     *  verify_host: boolean,
     * }
     */
    public static function getConfigurationHttp(): array
    {
        if (!self::HAS_PROXY) {
            return [];
        }

        if (self::CLIENT_PROTOCOL === 'http') {
            return [
                'proxy' => 'http://proxy:80',
                'verify_peer' => false,
                'verify_host' => false,
            ];
        }

        return [
            'proxy' => 'https://proxy:80',
            'verify_peer' => true,
            'verify_host' => true,
        ];
    }
}
