<?php

declare(strict_types=1);

namespace App\Form\Product\ProductCreate;

use Common\Domain\Form\FormErrorInterface;

enum PRODUCT_CREATE_FORM_ERRORS: string implements FormErrorInterface
{
    case DESCRIPTION = 'description';
    case GROUP_ID = 'group_id';
    case GROUP_ERROR = 'group_error';
    case NAME = 'name';
    case PRODUCT_NAME_REPEATED = 'product_name_repeated';
    case IMAGE = 'image';

    case PRODUCTS_OR_SHOPS_PRICES_NOT_EQUALS = 'products_or_shops_prices_not_equals';
    case PRODUCT_ID_AND_SHOP_ID = 'product_id_and_shop_id';
    case PRODUCTS_OR_SHOPS_ID = 'products_or_shops_id';
    case SHOP_ID = 'shop_id';
    case PRICES = 'prices';

    case INTERNAL_SERVER = 'error_internal_server';
}
