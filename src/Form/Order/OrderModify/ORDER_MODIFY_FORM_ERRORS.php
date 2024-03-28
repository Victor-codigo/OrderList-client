<?php

declare(strict_types=1);

namespace App\Form\Order\OrderModify;

use Common\Domain\Form\FormErrorInterface;

enum ORDER_MODIFY_FORM_ERRORS: string implements FormErrorInterface
{
    case ORDERS_ID = 'orders_id';
    case GROUP_ID = 'group_id';
    case LIST_ORDERS_ID = 'list_orders_id';
    case PRODUCT_ID = 'product_id';
    case SHOP_ID = 'shop_id';
    case DESCRIPTION = 'description';
    case AMOUNT = 'amount';
    case LIST_ORDERS_NOT_FOUND = 'list_orders_not_found';
    case ORDER_NOT_FOUND = 'order_not_found';
    case PRODUCT_NOT_FOUND = 'product_not_found';
    case SHOP_NOT_FOUND = 'shop_not_found';
    case ORDER_PRODUCT_AND_SHOP_REPEATED = 'order_product_and_shop_repeated';
    case GROUP_ERROR = 'group_error';

    case INTERNAL_SERVER = 'error_internal_server';
}
