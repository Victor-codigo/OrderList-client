<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersModify;

use Common\Domain\Form\FormErrorInterface;

enum LIST_ORDERS_MODIFY_FORM_ERRORS: string implements FormErrorInterface
{
    case NAME = 'name';
    case GROUP_ID = 'group_id';
    case LIST_ORDERS_ID = 'list_orders_id';
    case LIST_ORDERS_NOT_FOUND = 'list_orders_not_found';
    case LIST_ORDERS_NAME_EXISTS = 'list_orders_name_exists';
    case PERMISSIONS = 'permissions';
    case INTERNAL_SERVER = 'error_internal_server';
}
