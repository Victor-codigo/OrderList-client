<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersCreateFrom;

use Common\Domain\Form\FormErrorInterface;

enum LIST_ORDERS_CREATE_FROM_FORM_ERRORS: string implements FormErrorInterface
{
    case LIST_ORDERS_ID_CREATE_FROM = 'list_orders_id_create_from';
    case GROUP_ID = 'group_id';
    case NAME = 'name';
    case LIST_ORDERS_CREATE_FROM_NOT_FOUND = 'list_orders_create_from_not_found';
    case NAME_EXISTS = 'name_exists';
    case PERMISSIONS = 'permissions';
    case INTERNAL_SERVER = 'error_internal_server';
}
