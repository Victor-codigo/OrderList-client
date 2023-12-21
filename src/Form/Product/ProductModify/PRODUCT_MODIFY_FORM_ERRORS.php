<?php

declare(strict_types=1);

namespace App\Form\Product\ProductModify;

use Common\Domain\Form\FormErrorInterface;

enum PRODUCT_MODIFY_FORM_ERRORS: string implements FormErrorInterface
{
    case GROUP_ID = 'group_id';
    case PRODUCT_ID = 'product_id';
    case SHOP_NOT_FOUND = 'shop_not_found';
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case PRODUCT_NOT_FOUND = 'product_not_found';
    case PRODUCT_NAME_REPEATED = 'product_name_repeated';
    case IMAGE = 'image';
    case PERMISSIONS = 'permissions';
    case INTERNAL_SERVER = 'error_internal_server';
}
