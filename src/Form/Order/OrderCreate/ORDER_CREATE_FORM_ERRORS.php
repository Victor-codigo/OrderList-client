<?php

declare(strict_types=1);

namespace App\Form\Order\OrderCreate;

use Common\Domain\Form\FormErrorInterface;

enum ORDER_CREATE_FORM_ERRORS: string implements FormErrorInterface
{
    case GROUP_ID = 'group_id';
    case LIST_ORDERS_ID = 'list_orders_id';
    case PRODUCT_ID = 'product_id';
    case ORDERS_EMPTY = 'orders_empty';
    case LIST_ORDERS_NOT_FOUND = 'list_orders_not_found';
    case PRODUCT_NOT_FOUND = 'product_not_found';
    case SHOP_NOT_FOUND = 'shop_not_found';
    case GROUP_ERROR = 'group_error';
    case ORDER_PRODUCT_AND_SHOP_REPEATED = 'order_product_and_shop_repeated';

    case INTERNAL_SERVER = 'error_internal_server';
}
