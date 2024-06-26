<?php

declare(strict_types=1);

namespace Common\Domain\Config;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;

class Config
{
    public const CLIENT_PROTOCOL = 'http';
    public const CLIENT_DOMAIN = 'orderlist.client';
    public const CLIENT_DOMAIN_NAME = 'Order List';
    public const CLIENT_DOMAIN_LOCALE_VALID = 'es|en';
    public const CLIENT_PROTOCOL_AND_DOMAIN = self::CLIENT_PROTOCOL.'://'.self::CLIENT_DOMAIN;
    public const CLIENT_ENDPOINT_SHOP_CREATE = '/{_locale}/shop/{group_name}/create';
    public const CLIENT_ENDPOINT_SHOP_MODIFY = '/{_locale}/shop/{group_name}/modify/{shop_name}';
    public const CLIENT_ENDPOINT_SHOP_REMOVE = '/{_locale}/shop/{group_name}/remove';
    public const CLIENT_LISTS_NUM_MAX_PARAMS = 100;

    public const USER_TRY_OUT_EMAIL = 'guest@email.com';
    public const USER_TRY_OUT_PASSWORD = '1597531564';

    public const SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/shop/shop-no-image.svg';
    public const PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/product/product-no-image.svg';
    public const LIST_ORDERS_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/list_orders/list-orders-no-image.svg';
    public const ORDER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/order/order-no-image.svg';
    public const USER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/user/user-avatar-no-image.svg';
    public const GROUP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/group/group-no-image.svg';
    public const NOTIFICATION_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200 = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/notification/notification-no-image.svg';
    public const LIST_EMPTY_IMAGE = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/list-icon.svg';

    public const ORDER_BOUGHT_ICON = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/order/bought-icon-green-24x24.svg';
    public const ORDER_BOUGHT_NOT_ICON = self::CLIENT_PROTOCOL_AND_DOMAIN.'/assets/images/common/order/bought-not-icon-orange-24x24.svg';

    public const API_IMAGES_SHOP_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/assets/img/shops';
    public const API_IMAGES_PRODUCTS_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/assets/img/products';
    public const API_IMAGES_USERS_PATH = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/assets/img/users';

    // Maximum number of items in a modal list
    public const MODAL_LIST_ITEMS_MAX_NUMBER = 10;

    // Currency
    public const CURRENCY = '€';

    /**
     * Maximum number of users per group.
     */
    public const GROUP_USERS_MAX = 100;
}
