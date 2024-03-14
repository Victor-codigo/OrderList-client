<?php

declare(strict_types=1);

namespace App\Form\Order\OrderCreate;

use Common\Domain\Form\FormErrorInterface;

enum ORDER_CREATE_FORM_ERRORS: string implements FormErrorInterface
{
    case GROUP_ID = 'group_id';
    case ORDERS_EMPTY = 'orders_empty';
    case PRODUCT_NOT_FOUND = 'product_not_found';
    case GROUP_ERROR = 'group_error';

    case INTERNAL_SERVER = 'error_internal_server';
}
