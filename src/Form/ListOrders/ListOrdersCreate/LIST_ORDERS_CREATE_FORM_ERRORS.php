<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersCreate;

use Common\Domain\Form\FormErrorInterface;

enum LIST_ORDERS_CREATE_FORM_ERRORS: string implements FormErrorInterface
{
    case NAME = 'name';
    case GROUP_ID = 'group_id';
    case NAME_EXISTS = 'name_exists';
    case PERMISSIONS = 'permissions';
    case INTERNAL_SERVER = 'error_internal_server';
}
